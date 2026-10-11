<?php require APPROOT . '/Views/shared/header.php'; ?>

<header class="content-header">
    <div class="header-left">
        <h2 class="page-title">Add New Product</h2>
        <p class="page-subtitle">Publish a new item to your supplier catalog.</p>
    </div>
</header>

<section class="dashboard-card" style="max-width: 800px; margin: 0 auto; background: #fff; padding: 24px; border-radius: var(--radius-main); box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
    <form method="POST" action="<?php echo URLROOT; ?>/supplier/saveProduct" enctype="multipart/form-data">
        
        <div class="form-group" style="margin-bottom: 20px;">
            <label style="display:block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Product Image</label>
            <input type="file" name="product_image" class="form-control" accept="image/jpeg,image/png,image/webp" required style="width: 100%; padding: 10px 14px; border: 1px dashed #CBD5E1; border-radius: 8px; color: var(--color-text); background: #F8FAFC; cursor: pointer;">
        </div>
        
        <div class="form-row-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label style="display:block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Product Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g., Organic Apples" required style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text);">
            </div>
            <div class="form-group">
                <label style="display:block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Category</label>
                <select name="category_id" class="form-control" required style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text); background: #fff; appearance: auto;">
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
        
        <div class="form-group" style="margin-bottom: 20px;">
            <label style="display:block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Brand</label>
            <input type="text" name="brand" class="form-control" placeholder="e.g., GreenFarm" required style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text);">
        </div>
        
        <div class="form-row-3" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label style="display:block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Variant / Size</label>
                <input type="text" name="variant_name" class="form-control" placeholder="e.g., 1L, 500g" required style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text);">
            </div>
            <div class="form-group">
                <label style="display:block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">SKU</label>
                <input type="text" name="sku" class="form-control" placeholder="e.g., BP-2-595599" required style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text);">
            </div>
            <div class="form-group">
                <label style="display:block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Price ($)</label>
                <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text);">
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 24px;">
            <label style="display:block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Product Description</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Enter detailed product description..." required style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; resize: vertical; color: var(--color-text);"></textarea>
        </div>
        
        <div class="form-row-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label style="display:block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Ingredients</label>
                <textarea name="ingredients" class="form-control" rows="3" placeholder="List of ingredients..." style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; resize: vertical; color: var(--color-text);"></textarea>
            </div>
            <div class="form-group">
                <label style="display:block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Nutritional Value</label>
                <textarea name="nutritional_value" class="form-control" rows="3" placeholder="Calories, Macros, etc." style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; resize: vertical; color: var(--color-text);"></textarea>
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 24px;">
            <label style="display:block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Supporting Document (Compliance/Certificates)</label>
            <input type="file" name="product_document" class="form-control" accept=".pdf,image/jpeg,image/png" style="width: 100%; padding: 10px 14px; border: 1px dashed #CBD5E1; border-radius: 8px; color: var(--color-text); background: #F8FAFC; cursor: pointer;">
        </div>
                <div class="form-actions" style="text-align: right;">
            <a href="<?php echo URLROOT; ?>/supplier/catalog" class="btn btn-cancel" style="margin-right: 12px; background: #fff; border: 1px solid #CBD5E1; color: var(--color-text-muted); padding: 10px 18px; border-radius: 8px; text-decoration: none;">Cancel</a>
            <button type="submit" class="btn" style="background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 10px 24px; border-radius: 8px; font-weight: 600; cursor: pointer;">Save Product</button>
        </div>
    </form>
</section>

<?php require APPROOT . '/Views/shared/footer.php'; ?>
