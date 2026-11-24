<?php $pageCss = ['supplier/supplier', 'supplier/addProduct']; require APPROOT . '/Views/shared/header.php'; ?>

<header class="content-header">
    <div class="header-left">
        <h2 class="page-title">Add New Product</h2>
        <p class="page-subtitle">Publish a new item to your supplier catalog.</p>
    </div>
</header>

<section class="dashboard-card add-product-card">
    <form method="POST" action="<?php echo URLROOT; ?>/supplier/saveProduct" enctype="multipart/form-data">
        
        <div class="form-group add-product-group">
            <label class="add-product-label">Product Image</label>
            <input type="file" name="product_image" class="form-control add-product-file" accept="image/jpeg,image/png,image/webp" required>
        </div>
        
        <div class="form-row-2 add-product-row-2">
            <div class="form-group">
                <label class="add-product-label">Product Name</label>
                <input type="text" name="name" class="form-control add-product-input" placeholder="e.g., Organic Apples" required>
            </div>
            <div class="form-group">
                <label class="add-product-label">Category</label>
                <select name="category_id" class="form-control add-product-select" required>
                    <option value="">Select a category...</option>
                    <option value="1">Packaged Food</option>
                    <option value="2">Dairy</option>
                    <option value="3">Fresh Produce</option>
                    <option value="4">Bakery</option>
                    <option value="5">Seafood</option>
                    <option value="6">Pantry</option>
                </select>
            </div>
        </div>
        
        <div class="form-group add-product-group">
            <label class="add-product-label">Brand</label>
            <input type="text" name="brand" class="form-control add-product-input" placeholder="e.g., GreenFarm" required>
        </div>
        
        <div class="form-row-3 add-product-row-3">
            <div class="form-group">
                <label class="add-product-label">Variant / Size</label>
                <input type="text" name="variant_name" class="form-control add-product-input" placeholder="e.g., 1L, 500g" required>
            </div>
            <div class="form-group">
                <label class="add-product-label">SKU</label>
                <input type="text" name="sku" class="form-control add-product-input" placeholder="e.g., BP-2-595599" required>
            </div>
            <div class="form-group">
                <label class="add-product-label">Price ($)</label>
                <input type="number" step="0.01" name="price" class="form-control add-product-input" placeholder="0.00" required>
            </div>
        </div>
        
        <div class="form-group add-product-group mb-24">
            <label class="add-product-label">Product Description</label>
            <textarea name="description" class="form-control add-product-textarea" rows="4" placeholder="Enter detailed product description..." required></textarea>
        </div>
        
        <div class="form-row-2 add-product-row-2">
            <div class="form-group">
                <label class="add-product-label">Ingredients</label>
                <textarea name="ingredients" class="form-control add-product-textarea" rows="3" placeholder="List of ingredients..."></textarea>
            </div>
            <div class="form-group">
                <label class="add-product-label">Nutritional Value</label>
                <textarea name="nutritional_value" class="form-control add-product-textarea" rows="3" placeholder="Calories, Macros, etc."></textarea>
            </div>
        </div>
        
        <div class="form-group add-product-group mb-24">
            <label class="add-product-label">Supporting Document (Compliance/Certificates)</label>
            <input type="file" name="product_document" class="form-control add-product-file" accept=".pdf,image/jpeg,image/png">
        </div>
        
        <div class="form-actions add-product-actions">
            <a href="<?php echo URLROOT; ?>/supplier/catalog" class="btn btn-cancel add-product-btn-cancel">Cancel</a>
            <button type="submit" class="btn add-product-btn-submit">Save Product</button>
        </div>
    </form>
</section>

<?php require APPROOT . '/Views/shared/footer.php'; ?>
