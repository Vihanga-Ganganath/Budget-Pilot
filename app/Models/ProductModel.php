<?php
/**
 * ProductModel
 * Handles Product & Inventory Management for Suppliers and Admin.
 * Interconnects with the Recommendation System and Live Cart.
 * Table used: products
 */
class ProductModel {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    /**
     * Generates a collision-free unique SKU for a product.
     * Format: BP-{CAT}-{6_HEX_CHARS} (e.g., BP-DAI-A4F92C)
     *
     * @param string $category
     * @return string
     */
    public function generateUniqueSku($category = 'GEN') {
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $category), 0, 3));
        if (empty($prefix)) {
            $prefix = 'GEN';
        }

        do {
            $sku = 'BP-' . $prefix . '-' . strtoupper(bin2hex(random_bytes(3)));
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM products WHERE sku = ?");
            $stmt->execute([$sku]);
            $exists = (int)$stmt->fetchColumn();
        } while ($exists > 0);

        return $sku;
    }

    /**
     * Inserts a new product into the database.
     * Automatically generates a unique SKU and calculates unit_price if needed.
     * Default status is 'pending' awaiting Admin moderation.
     * Note: Duplicate checking logic is omitted as requested.
     *
     * @param array $data Contains supplier_id, name, brand, category, price, size, and optional unit_price
     * @return int|bool Returns the inserted product ID, or false on failure
     */
    public function insertProduct(array $data) {
        $category = trim($data['category'] ?? 'General');
        $sku = !empty($data['sku']) ? trim($data['sku']) : $this->generateUniqueSku($category);

        $price = (float)($data['price'] ?? 0.00);
        $size = trim($data['size'] ?? '1 unit');

        // Calculate unit_price if not explicitly provided
        if (isset($data['unit_price']) && is_numeric($data['unit_price'])) {
            $unitPrice = (float)$data['unit_price'];
        } else {
            // Extract numeric amount from size (e.g., "500g" -> 500, "1L" -> 1)
            preg_match('/([\d\.]+)/', $size, $matches);
            $sizeNumeric = !empty($matches[1]) ? (float)$matches[1] : 1;
            $unitPrice = $sizeNumeric > 0 ? round($price / $sizeNumeric, 4) : $price;
        }

        $status = in_array(strtolower($data['status'] ?? ''), ['pending', 'approved', 'rejected']) 
            ? strtolower($data['status']) 
            : 'pending';

        // Detect if category_id column exists (for backward compatibility with legacy schema)
        $hasCategoryId = false;
        try {
            $colCheck = $this->db->query("SHOW COLUMNS FROM products LIKE 'category_id'");
            $hasCategoryId = (bool)$colCheck->fetch();
        } catch (Throwable $e) {}

        if ($hasCategoryId) {
            $categoryId = !empty($data['category_id']) ? (int)$data['category_id'] : 1;
            $query = "
                INSERT INTO products (
                    supplier_id, category_id, sku, name, brand, category, price, size, unit_price, status, created_at
                ) VALUES (
                    :supplier_id, :category_id, :sku, :name, :brand, :category, :price, :size, :unit_price, :status, NOW()
                )
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        } else {
            $query = "
                INSERT INTO products (
                    supplier_id, sku, name, brand, category, price, size, unit_price, status, created_at
                ) VALUES (
                    :supplier_id, :sku, :name, :brand, :category, :price, :size, :unit_price, :status, NOW()
                )
            ";
            $stmt = $this->db->prepare($query);
        }

        $stmt->bindValue(':supplier_id', (int)($data['supplier_id'] ?? 0), PDO::PARAM_INT);
        $stmt->bindValue(':sku', $sku, PDO::PARAM_STR);
        $stmt->bindValue(':name', trim($data['name'] ?? ''), PDO::PARAM_STR);
        $stmt->bindValue(':brand', trim($data['brand'] ?? ''), PDO::PARAM_STR);
        $stmt->bindValue(':category', $category, PDO::PARAM_STR);
        $stmt->bindValue(':price', $price);
        $stmt->bindValue(':size', $size, PDO::PARAM_STR);
        $stmt->bindValue(':unit_price', $unitPrice);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return (int)$this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Fetch products filtered by their approval status (e.g., 'pending', 'approved', 'rejected').
     *
     * @param string $status
     * @return array
     */
    public function getProductsByStatus($status) {
        $status = strtolower(trim($status));
        $query = "
            SELECT * FROM products 
            WHERE status = :status 
            ORDER BY created_at DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Admin Logic: Updates product moderation status to 'approved' or 'rejected'.
     *
     * @param int $productId
     * @param string $status 'approved' or 'rejected'
     * @return bool
     */
    public function updateProductStatus($productId, $status) {
        $status = strtolower(trim($status));
        if (!in_array($status, ['approved', 'rejected', 'pending'])) {
            return false;
        }

        $query = "
            UPDATE products 
            SET status = :status, updated_at = NOW() 
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':id', $productId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Fetch single product by ID.
     *
     * @param int $id
     * @return array|bool
     */
    public function getProductById($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch all approved products for customer browsing.
     *
     * @return array
     */
    public function getApprovedProducts() {
        return $this->getProductsByStatus('approved');
    }

    /**
     * Fetch all products submitted by a specific supplier.
     *
     * @param int $supplierId
     * @return array
     */
    public function getProductsBySupplier($supplierId) {
        $stmt = $this->db->prepare("
            SELECT * FROM products 
            WHERE supplier_id = :supplier_id 
            ORDER BY created_at DESC
        ");
        $stmt->bindParam(':supplier_id', $supplierId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
