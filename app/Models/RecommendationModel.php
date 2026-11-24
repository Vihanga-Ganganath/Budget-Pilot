<?php
/**
 * RecommendationModel
 * Core model for Module: Recommendation System (Brand Comparison).
 * Finds budget-friendly substitute products across different brands in the same category
 * with lower unit prices.
 * Table used: products
 */
class RecommendationModel {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    /**
     * Fetch all approved brand alternatives belonging to the same product_group
     * (fallback to category if product_group is null), excluding the current $product_id,
     * ordered by effective unit/product price ASC so the cheapest brand comes first.
     *
     * @param int $product_id
     * @return array List of alternative products with savings and cheapest flag
     */
    public function getCartBrandAlternatives($product_id) {
        $product_id = (int)$product_id;
        if ($product_id <= 0) {
            return [];
        }

        // 1. Fetch current product details
        $stmt = $this->db->prepare("
            SELECT id, name, brand, category, product_group, price, size, unit_price 
            FROM products 
            WHERE id = ? 
            LIMIT 1
        ");
        $stmt->execute([$product_id]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$current) {
            return [];
        }

        $currentGroup = !empty($current['product_group']) ? trim($current['product_group']) : null;
        $currentCategory = trim($current['category'] ?? '');
        $currentPrice = (float)$current['price'];
        $currentUnitPrice = (float)$current['unit_price'];
        $currentEffectivePrice = ($currentUnitPrice > 0) ? $currentUnitPrice : $currentPrice;

        // 2. Query valid products: status = 'Active' AND admin_verification = 'Approved'
        // Match by product_group (or fallback to category), exclude current product, sort by effective price ASC
        if (!empty($currentGroup)) {
            $query = "
                SELECT 
                    p.id, p.supplier_id, p.sku, p.name, p.brand, p.category, 
                    p.product_group, p.price, p.size, p.unit_price,
                    (CASE WHEN p.unit_price > 0 THEN p.unit_price ELSE p.price END) AS effective_price
                FROM products p
                WHERE p.product_group = :group
                  AND p.id != :product_id
                  AND p.status = 'Active'
                  AND p.admin_verification = 'Approved'
                ORDER BY (CASE WHEN p.unit_price > 0 THEN p.unit_price ELSE p.price END) ASC, p.price ASC
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':group', $currentGroup, PDO::PARAM_STR);
            $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        } elseif (!empty($currentCategory)) {
            $query = "
                SELECT 
                    p.id, p.supplier_id, p.sku, p.name, p.brand, p.category, 
                    p.product_group, p.price, p.size, p.unit_price,
                    (CASE WHEN p.unit_price > 0 THEN p.unit_price ELSE p.price END) AS effective_price
                FROM products p
                WHERE p.category = :category
                  AND p.id != :product_id
                  AND p.status = 'Active'
                  AND p.admin_verification = 'Approved'
                ORDER BY (CASE WHEN p.unit_price > 0 THEN p.unit_price ELSE p.price END) ASC, p.price ASC
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':category', $currentCategory, PDO::PARAM_STR);
            $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        } else {
            return [];
        }

        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            return [];
        }

        // The first row is the cheapest alternative because of ORDER BY effective_price ASC
        $cheapestEffective = (float)$rows[0]['effective_price'];
        $cheapestAltId = (int)$rows[0]['id'];

        $alternatives = [];
        foreach ($rows as $row) {
            $altPrice = (float)$row['price'];
            $altUnitPrice = (float)$row['unit_price'];
            $altEffectivePrice = (float)$row['effective_price'];

            // Price difference / savings without item quantity
            $priceSavings = round($currentPrice - $altPrice, 2);
            $effectiveSavings = round($currentEffectivePrice - $altEffectivePrice, 2);
            $saving = ($priceSavings > 0) ? $priceSavings : max(0.00, $effectiveSavings);

            $row['original_product_id'] = $product_id;
            $row['original_price']      = $currentPrice;
            $row['original_unit_price'] = $currentUnitPrice;
            $row['effective_price']     = $altEffectivePrice;
            $row['saving_per_unit']     = $saving;
            $row['total_saving']        = $saving; // Strictly no quantity tracking
            $row['price_difference']    = $priceSavings;
            $row['unit_savings']        = $effectiveSavings;
            $row['is_cheapest']         = ((int)$row['id'] === $cheapestAltId && $altEffectivePrice < $currentEffectivePrice);
            $row['is_cheaper']          = ($saving > 0);

            $alternatives[] = $row;
        }

        return $alternatives;
    }

    /**
     * Finds approved products in the same category with a lower unit_price across different brands.
     * Calculates unit savings and percentage cheaper for each alternative.
     *
     * @param int $product_id The reference product ID to compare against
     * @param string|null $category Optional category name (if null, fetched from database)
     * @return array List of cheaper alternative products with calculated savings
     */
    public function getCheaperAlternatives($product_id, $category = null) {
        $product_id = (int)$product_id;
        if ($product_id <= 0) {
            return [];
        }

        // 1. Fetch current product's details (unit_price, brand, category, price)
        $stmt = $this->db->prepare("
            SELECT id, name, brand, category, price, size, unit_price, status 
            FROM products 
            WHERE id = ? 
            LIMIT 1
        ");
        $stmt->execute([$product_id]);
        $currentProduct = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$currentProduct) {
            return [];
        }

        $targetCategory = !empty($category) ? trim($category) : trim($currentProduct['category'] ?? '');
        $currentBrand = trim($currentProduct['brand'] ?? '');
        $currentUnitPrice = (float)($currentProduct['unit_price'] > 0 ? $currentProduct['unit_price'] : $currentProduct['price']);
        $currentPrice = (float)$currentProduct['price'];

        if (empty($targetCategory) || $currentUnitPrice <= 0) {
            return [];
        }

        // 2. Query approved alternatives in the same category, lower unit_price, and differing brand
        $query = "
            SELECT 
                p.id,
                p.supplier_id,
                p.sku,
                p.name,
                p.brand,
                p.category,
                p.price,
                p.size,
                p.unit_price,
                p.status
            FROM products p
            WHERE p.category = :category
              AND (p.status = 'approved' OR p.status = 'Active')
              AND p.id != :product_id
              AND (p.brand IS NULL OR p.brand != :brand)
              AND p.unit_price < :current_unit_price
            ORDER BY p.unit_price ASC, p.price ASC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':category', $targetCategory, PDO::PARAM_STR);
        $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindValue(':brand', $currentBrand, PDO::PARAM_STR);
        $stmt->bindValue(':current_unit_price', $currentUnitPrice);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $recommendations = [];

        foreach ($rows as $row) {
            $altUnitPrice = (float)$row['unit_price'];
            $altPrice = (float)$row['price'];

            $unitSavings = round($currentUnitPrice - $altUnitPrice, 2);
            $percentCheaper = $currentUnitPrice > 0 
                ? round(($unitSavings / $currentUnitPrice) * 100, 1) 
                : 0.0;
            $priceDifference = round($currentPrice - $altPrice, 2);

            $row['original_product_id']   = $product_id;
            $row['original_product_name'] = $currentProduct['name'];
            $row['original_brand']        = $currentBrand;
            $row['original_unit_price']   = $currentUnitPrice;
            $row['unit_savings']          = $unitSavings;
            $row['percent_cheaper']       = $percentCheaper;
            $row['price_difference']      = $priceDifference;

            $recommendations[] = $row;
        }

        return $recommendations;
    }

    /**
     * Get the single best (cheapest unit_price) approved alternative for a product.
     *
     * @param int $product_id
     * @param string|null $category
     * @return array|null
     */
    public function getTopCheaperAlternative($product_id, $category = null) {
        $alternatives = $this->getCheaperAlternatives($product_id, $category);
        return !empty($alternatives) ? $alternatives[0] : null;
    }

    /**
     * Analyze all items currently in a user's cart and map recommended alternatives.
     *
     * @param array $cartItems Array of cart rows (each containing 'product_id' and optional 'category')
     * @return array Keyed by product_id with list of cheaper substitutes
     */
    public function getRecommendationsForCart(array $cartItems) {
        $cartRecommendations = [];

        foreach ($cartItems as $item) {
            $productId = (int)($item['product_id'] ?? 0);
            $category = $item['category'] ?? null;

            if ($productId <= 0) {
                continue;
            }

            $alts = $this->getCheaperAlternatives($productId, $category);
            if (!empty($alts)) {
                $cartRecommendations[$productId] = [
                    'original_item' => $item,
                    'alternatives'  => $alts,
                    'best_deal'     => $alts[0] // Lowest unit price option
                ];
            }
        }

        return $cartRecommendations;
    }

    /**
     * Calculate potential overall savings if customer replaces cart items with the cheapest alternatives.
     *
     * @param array $cartItems
     * @return float
     */
    public function calculatePotentialCartSavings(array $cartItems) {
        $totalSavings = 0.00;
        $recommendations = $this->getRecommendationsForCart($cartItems);

        foreach ($recommendations as $productId => $rec) {
            $qty = (int)($rec['original_item']['quantity'] ?? 1);
            $priceDiff = (float)($rec['best_deal']['price_difference'] ?? 0.00);
            if ($priceDiff > 0) {
                $totalSavings += ($priceDiff * $qty);
            }
        }

        return round($totalSavings, 2);
    }
}
?>
