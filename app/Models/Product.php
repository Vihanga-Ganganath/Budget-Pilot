<?php

class Product {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }
    
    public function getProductsBySupplier($supplierId) {

    $query = "
        SELECT
            p.*,
            pc.name AS category_name,
            pv.id AS variant_id,
            pv.variant_name,
            pv.sku,
            pv.price,
            pv.stock_quantity,
            pi.image_url
        FROM products p

        LEFT JOIN product_categories pc
            ON p.category_id = pc.id

        LEFT JOIN product_variants pv
            ON p.id = pv.product_id
        
        LEFT JOIN product_images pi
            ON pv.id = pi.variant_id
            AND pi.image_type = 'Front'

        WHERE p.supplier_id = :supplier_id

        ORDER BY p.created_at DESC
    ";

    $stmt = $this->db->prepare($query);

    $stmt->bindParam(
        ':supplier_id',
        $supplierId,
        PDO::PARAM_INT
    );

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function createProduct($data) {
    try {
        // Both inserts must succeed together
        $this->db->beginTransaction();

        // 1. Create the main product
        $query = "
            INSERT INTO products
            (
                supplier_id,
                category_id,
                name,
                description,
                ingredients,
                nutritional_value,
                document_url,
                status,
                admin_verification
            )
            VALUES
            (
                :supplier_id,
                :category_id,
                :name,
                :description,
                :ingredients,
                :nutritional_value,
                :document_url,
                :status,
                'Pending'
            )
        ";

        $stmt = $this->db->prepare($query);

        $stmt->execute([
            ':supplier_id'       => $data['supplier_id'],
            ':category_id'       => $data['category_id'],
            ':name'              => $data['name'],
            ':description'       => $data['description'],
            ':ingredients'       => $data['ingredients'],
            ':nutritional_value' => $data['nutritional_value'],
            ':document_url'      => $data['document_url'],
            ':status'            => $data['status']
        ]);

        // Get the ID of the product we just created
        $productId = $this->db->lastInsertId();

        // Automatically generate a unique SKU
        $sku = 'BP-' . $productId . '-' . strtoupper(substr(uniqid(), -6));

        // 2. Create its first product variant
        $variantQuery = "
            INSERT INTO product_variants
            (
                product_id,
                variant_name,
                sku,
                price,
                stock_quantity
            )
            VALUES
            (
                :product_id,
                :variant_name,
                :sku,
                :price,
                :stock_quantity
            )
        ";

        $variantStmt = $this->db->prepare($variantQuery);

        $variantStmt->execute([
            ':product_id'     => $productId,
            ':variant_name'   => $data['variant_name'],
            ':sku'            => $sku,
            ':price'          => $data['price'],
            ':stock_quantity' => $data['stock_quantity']
        ]);

        // Get the ID of the variant we just created
        $variantId = $this->db->lastInsertId();

        // Everything worked
        $this->db->commit();

        // Return the new variant ID
        return $variantId;

    } catch (Exception $e) {

        // If either INSERT fails, undo everything
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }

        return false;
    }
}
public function saveProductImage($variantId, $imageUrl, $imageType = 'Front') {

    $query = "
        INSERT INTO product_images
        (
            variant_id,
            image_url,
            image_type
        )
        VALUES
        (
            :variant_id,
            :image_url,
            :image_type
        )
    ";

    $stmt = $this->db->prepare($query);

    return $stmt->execute([
        ':variant_id' => $variantId,
        ':image_url'  => $imageUrl,
        ':image_type' => $imageType
    ]);
}

public function updateProductImage($variantId, $imageUrl) {

    // Check whether this variant already has a Front image
    $checkQuery = "
        SELECT id
        FROM product_images
        WHERE variant_id = :variant_id
        AND image_type = 'Front'
        LIMIT 1
    ";

    $checkStmt = $this->db->prepare($checkQuery);
    $checkStmt->execute([
        ':variant_id' => $variantId
    ]);

    $existingImage = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existingImage) {

        // Replace existing Front image path
        $query = "
            UPDATE product_images
            SET image_url = :image_url
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($query);

        return $stmt->execute([
            ':image_url' => $imageUrl,
            ':id' => $existingImage['id']
        ]);

    } else {

        // Product did not previously have an image
        return $this->saveProductImage(
            $variantId,
            $imageUrl,
            'Front'
        );
    }
}

public function getProductById($productId, $supplierId) {

    $query = "
        SELECT
            p.*,
            pv.id AS variant_id,
            pv.variant_name,
            pv.sku,
            pv.price,
            pv.stock_quantity,
            pi.image_url

        FROM products p

        LEFT JOIN product_variants pv
            ON p.id = pv.product_id

        LEFT JOIN product_images pi
            ON pv.id = pi.variant_id
            AND pi.image_type = 'Front'

        WHERE p.id = :id
        AND p.supplier_id = :supplier_id

        LIMIT 1
    ";

    $stmt = $this->db->prepare($query);

    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->bindParam(':supplier_id', $supplierId, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


public function updateProduct($data) {
    try {
        $this->db->beginTransaction();

        // Update main product information
        $query = "
            UPDATE products
            SET
                category_id = :category_id,
                name = :name,
                description = :description,
                ingredients = :ingredients,
                nutritional_value = :nutritional_value,
                document_url = :document_url,
                status = :status,
                admin_verification = 'Pending'
            WHERE id = :id
            AND supplier_id = :supplier_id
        ";

        $stmt = $this->db->prepare($query);

        $stmt->execute([
            ':category_id' => $data['category_id'],
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':ingredients' => $data['ingredients'],
            ':nutritional_value' => $data['nutritional_value'],
            ':document_url' => $data['document_url'],
            ':status' => $data['status'],
            ':id' => $data['id'],
            ':supplier_id' => $data['supplier_id']
        ]);

        // Update variant information
        $variantQuery = "
            UPDATE product_variants
            SET
                variant_name = :variant_name,
                price = :price,
                stock_quantity = :stock_quantity
            WHERE product_id = :product_id
        ";

        $variantStmt = $this->db->prepare($variantQuery);

        $variantStmt->execute([
            ':variant_name' => $data['variant_name'],
            ':price' => $data['price'],
            ':stock_quantity' => $data['stock_quantity'],
            ':product_id' => $data['id']
        ]);

        $this->db->commit();

        return true;

    } catch (Exception $e) {

        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }

        return false;
    }
}
public function deleteProduct($id, $supplierId) {

    $query = "
        DELETE FROM products
        WHERE id = :id
        AND supplier_id = :supplier_id
    ";

    $stmt = $this->db->prepare($query);

    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':supplier_id', $supplierId, PDO::PARAM_INT);

    return $stmt->execute();
}
}