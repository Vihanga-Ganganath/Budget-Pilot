<?php
$product = $data['product'];
$spActive = 'catalog';
$spActiveLink = true;
$pageCss = ['supplier/supplier', 'supplier/edit-product'];
require APPROOT . '/Views/shared/header.php';
?>

        <div class="card edit-product-card">

            <h2 class="section-title">Edit Product</h2>

            <p class="edit-product-desc-lead">
                Update the product information below.
            </p>

            <form method="POST"
                enctype="multipart/form-data"
                action="<?php echo URLROOT; ?>/supplier/editProduct/<?php echo $product['id']; ?>">

                <!-- Product Name -->
                <div class="edit-product-group">
                    <label>Product Name *</label>
                    <input
                        class="input w-100"
                        type="text"
                        name="name"
                        required
                        value="<?php echo htmlspecialchars($product['name']); ?>">
                </div>

                <!-- Variant Details -->
                <div class="edit-product-row-3">
                    <div>
                        <label>Variant *</label>
                        <input
                            class="input w-100"
                            type="text"
                            name="variant_name"
                            required
                            value="<?= htmlspecialchars($product['variant_name'] ?? '') ?>">
                    </div>

                    <div>
                        <label>Price (USD $) *</label>
                        <input
                            class="input w-100"
                            type="number"
                            name="price"
                            step="0.01"
                            min="0"
                            required
                            value="<?= htmlspecialchars($product['price'] ?? '') ?>">
                    </div>

                    <div>
                        <label>Stock Quantity *</label>
                        <input
                            class="input w-100"
                            type="number"
                            name="stock_quantity"
                            min="0"
                            required
                            value="<?= htmlspecialchars($product['stock_quantity'] ?? '') ?>">
                    </div>
                </div>

                <!-- Category -->
                <div class="edit-product-group">
                    <label>Category *</label>
                    <select
                        class="input w-100"
                        name="category_id"
                        required>

                        <option value="">Select Category</option>
                        <option value="1" <?php echo $product['category_id'] == 1 ? 'selected' : ''; ?>>Detergents &amp; Cleaning</option>
                        <option value="2" <?php echo $product['category_id'] == 2 ? 'selected' : ''; ?>>Personal Care</option>
                        <option value="3" <?php echo $product['category_id'] == 3 ? 'selected' : ''; ?>>Packaged Food</option>
                        <option value="4" <?php echo $product['category_id'] == 4 ? 'selected' : ''; ?>>Cooking Essentials</option>
                        <option value="5" <?php echo $product['category_id'] == 5 ? 'selected' : ''; ?>>Beverages</option>
                        <option value="6" <?php echo $product['category_id'] == 6 ? 'selected' : ''; ?>>Snacks &amp; Spreads</option>
                        <option value="7" <?php echo $product['category_id'] == 7 ? 'selected' : ''; ?>>Household Consumables</option>
                    </select>
                </div>

                <div class="edit-product-group">
                    <label>Product Image</label><br>

                    <?php if (!empty($product['image_url'])): ?>
                        <div class="edit-product-img-preview">
                            <p class="edit-product-img-label">
                                Current Image
                            </p>
                            <img
                                src="<?= URLROOT . '/' . htmlspecialchars($product['image_url']) ?>"
                                alt="<?= htmlspecialchars($product['name']) ?>"
                                class="edit-product-thumb">
                        </div>
                    <?php endif; ?>

                    <input
                        class="input w-100"
                        type="file"
                        name="product_image"
                        accept="image/jpeg,image/png,image/webp">

                    <small class="edit-product-hint">
                        Leave this empty to keep the current image. JPG, PNG or WEBP.
                    </small>
                </div>

                <!-- Description -->
                <div class="edit-product-group">
                    <label>Description</label>
                    <textarea
                        class="input w-100"
                        name="description"
                        rows="3"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                </div>

                <!-- Ingredients -->
                <div class="edit-product-group">
                    <label>Ingredients</label>
                    <textarea
                        class="input w-100"
                        name="ingredients"
                        rows="3"><?php echo htmlspecialchars($product['ingredients'] ?? ''); ?></textarea>
                </div>

                <!-- Nutritional Value -->
                <div class="edit-product-group">
                    <label>Nutritional Value</label>
                    <textarea
                        class="input w-100"
                        name="nutritional_value"
                        rows="3"><?php echo htmlspecialchars($product['nutritional_value'] ?? ''); ?></textarea>
                </div>

                <!-- Document URL -->
                <div class="edit-product-group">
                    <label>Document URL</label>
                    <input
                        class="input w-100"
                        type="text"
                        name="document_url"
                        value="<?php echo htmlspecialchars($product['document_url'] ?? ''); ?>">
                </div>

                <!-- Status -->
                <div class="edit-product-group mb-25">
                    <label>Status</label>
                    <select
                        class="input w-100"
                        name="status">
                        <option value="Active" <?php echo $product['status'] === 'Active' ? 'selected' : ''; ?>>Active</option>
                        <option value="Inactive" <?php echo $product['status'] === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                        <option value="Discontinued" <?php echo $product['status'] === 'Discontinued' ? 'selected' : ''; ?>>Discontinued</option>
                        <option value="Coming Soon" <?php echo $product['status'] === 'Coming Soon' ? 'selected' : ''; ?>>Coming Soon</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="edit-product-btn-row">
                    <button class="btn" type="submit">
                        Save Changes
                    </button>
                    <a class="btn" href="<?php echo URLROOT; ?>/supplier/catalog">
                        Cancel
                    </a>
                </div>

            </form>

        </div>

<?php require APPROOT . '/Views/shared/footer.php'; ?>
