<?php
/**
 * CartModel
 * Manages Module 3: Smart Purchase & Live Cart.
 * Connects cart_items with the products table to calculate real-time live cart value,
 * subtotals, quantities, and item data.
 * Tables used: cart_items, products
 */
class CartModel {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    /**
     * Fetch brand alternatives for a product (delegates to RecommendationModel).
     *
     * @param int $product_id
     * @return array
     */
    public function getCartBrandAlternatives($product_id) {
        if (!class_exists('RecommendationModel')) {
            require_once dirname(__FILE__) . '/RecommendationModel.php';
        }
        return (new RecommendationModel())->getCartBrandAlternatives($product_id);
    }

    /**
     * Add a product to the user's cart.
     * Uses ON DUPLICATE KEY UPDATE so adding an existing product increments quantity.
     *
     * @param int $user_id
     * @param int $product_id
     * @param int $qty
     * @return bool
     */
    public function addItem($user_id, $product_id, $qty = 1) {
        $user_id = (int)$user_id;
        $product_id = (int)$product_id;
        $qty = max(1, (int)$qty);

        if ($user_id <= 0 || $product_id <= 0) {
            return false;
        }

        // Verify product exists to prevent foreign key errors
        try {
            $checkStmt = $this->db->prepare("SELECT id FROM products WHERE id = ?");
            $checkStmt->execute([$product_id]);
            if (!$checkStmt->fetchColumn()) {
                return false;
            }
        } catch (Throwable $e) {
            error_log('CartModel product check notice: ' . $e->getMessage());
        }

        $query = "
            INSERT INTO cart_items (user_id, product_id, quantity, added_at)
            VALUES (:user_id, :product_id, :quantity, NOW())
            ON DUPLICATE KEY UPDATE 
                quantity = quantity + VALUES(quantity),
                added_at = NOW()
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $qty, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Remove an item from the cart by its cart item ID.
     *
     * @param int $cart_id
     * @return bool
     */
    public function removeItem($cart_id) {
        $query = "DELETE FROM cart_items WHERE id = :cart_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Update the quantity of a specific cart item.
     * If quantity is <= 0, the item is removed from the cart.
     *
     * @param int $cart_id
     * @param int $qty
     * @return bool
     */
    public function updateQuantity($cart_id, $qty) {
        $cart_id = (int)$cart_id;
        $qty = (int)$qty;

        if ($qty <= 0) {
            return $this->removeItem($cart_id);
        }

        $query = "UPDATE cart_items SET quantity = :quantity WHERE id = :cart_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':quantity', $qty, PDO::PARAM_INT);
        $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Swap an existing cart item with a new product.
     * Updates product_id for that specific cart_items row.
     * If the new product is already in the user's cart, merges quantities and removes the old row.
     *
     * @param int $cart_item_id
     * @param int $user_id
     * @param int $new_product_id
     * @return bool
     */
    public function swapCartItem($cart_item_id, $user_id, $new_product_id) {
        $cart_item_id = (int)$cart_item_id;
        $user_id = (int)$user_id;
        $new_product_id = (int)$new_product_id;

        if ($cart_item_id <= 0 || $user_id <= 0 || $new_product_id <= 0) {
            return false;
        }

        // 1. Fetch current cart item and verify ownership
        $stmt = $this->db->prepare("SELECT id, user_id, product_id, quantity FROM cart_items WHERE id = ? AND user_id = ?");
        $stmt->execute([$cart_item_id, $user_id]);
        $currentItem = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$currentItem) {
            return false;
        }

        // If swapping to the same product, nothing needs to change
        if ((int)$currentItem['product_id'] === $new_product_id) {
            return true;
        }

        // 2. Verify new product exists
        $stmtProd = $this->db->prepare("SELECT id FROM products WHERE id = ?");
        $stmtProd->execute([$new_product_id]);
        if (!$stmtProd->fetchColumn()) {
            return false;
        }

        $currentQty = (int)$currentItem['quantity'];

        // 3. Check if new product is already present in user's cart
        $stmtExisting = $this->db->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ? AND id != ?");
        $stmtExisting->execute([$user_id, $new_product_id, $cart_item_id]);
        $existing = $stmtExisting->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Merge quantities into existing row and delete old row
            $mergedQty = (int)$existing['quantity'] + $currentQty;
            $updateStmt = $this->db->prepare("UPDATE cart_items SET quantity = ?, added_at = NOW() WHERE id = ?");
            $updateStmt->execute([$mergedQty, $existing['id']]);

            $delStmt = $this->db->prepare("DELETE FROM cart_items WHERE id = ?");
            return $delStmt->execute([$cart_item_id]);
        } else {
            // Update product_id of the current cart item
            $updateStmt = $this->db->prepare("UPDATE cart_items SET product_id = ?, added_at = NOW() WHERE id = ?");
            return $updateStmt->execute([$new_product_id, $cart_item_id]);
        }
    }

    /**
     * Clear all cart items for a given user.
     *
     * @param int $user_id
     * @return bool
     */
    public function clearCart($user_id) {
        $query = "DELETE FROM cart_items WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Calculate the total live cart value directly by joining cart_items with the products table.
     * Real-time calculation reflecting current product catalog prices.
     *
     * @param int $user_id
     * @return float Total cart value
     */
    public function getTotalCartValue($user_id) {
        $user_id = (int)$user_id;
        if ($user_id <= 0) {
            return 0.00;
        }

        $query = "
            SELECT COALESCE(SUM(
                CASE 
                    WHEN p.price IS NOT NULL AND p.price > 0 THEN (p.price * c.quantity)
                    ELSE 0.00
                END
            ), 0.00) AS total_value
            FROM cart_items c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = :user_id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $total = (float)$stmt->fetchColumn();
        return round($total, 2);
    }

    /**
     * Fetch complete user cart details with product metadata and line-item subtotals.
     * Joins cart_items with products table.
     *
     * @param int $user_id
     * @return array Contains 'items', 'subtotal', and 'total_items'
     */
    public function getUserCartDetails($user_id) {
        $user_id = (int)$user_id;
        if ($user_id <= 0) {
            return [
                'items'       => [],
                'subtotal'    => 0.00,
                'total_items' => 0
            ];
        }

        $query = "
            SELECT 
                c.id AS cart_id,
                c.id,
                c.user_id,
                c.product_id,
                c.quantity,
                c.added_at,
                p.sku,
                p.name AS product_name,
                p.name,
                COALESCE(p.brand, 'BudgetPilot Brand') AS brand,
                COALESCE(p.brand, 'BudgetPilot Brand') AS brand_name,
                COALESCE(p.category, 'Groceries') AS category,
                COALESCE(p.size, '1 unit') AS size,
                COALESCE(p.size, '1 unit') AS variant_name,
                COALESCE(p.price, 0.00) AS price,
                COALESCE(p.unit_price, p.price, 0.00) AS unit_price,
                p.status
            FROM cart_items c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = :user_id
            ORDER BY c.added_at DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $subtotal = 0.00;
        $items = [];

        foreach ($rows as $row) {
            $price = (float)$row['price'];
            $unitPrice = (float)$row['unit_price'];
            $quantity = (int)$row['quantity'];
            $itemSubtotal = round($price * $quantity, 2);

            $row['price'] = $price;
            $row['unit_price'] = $unitPrice;
            $row['quantity'] = $quantity;
            $row['item_subtotal'] = $itemSubtotal;

            $subtotal += $itemSubtotal;
            $items[] = $row;
        }

        return [
            'items'       => $items,
            'subtotal'    => round($subtotal, 2),
            'total_items' => count($items)
        ];
    }
}
?>
