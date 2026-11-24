<?php $pageCss = 'customer-css/product_detail'; require APPROOT . '/Views/shared/header.php'; ?>
<main class="main-content">
<div class="content-header mb-24">
    <a href="<?php echo URLROOT; ?>/customer/grocery" class="pd-back-link">&larr; Back to Catalog</a>
</div>

<!-- Product Top Section -->
<div class="pd-hero-grid">
    <?php
        $p = $data['product'] ?? null;
        $prodId = (int)($p['id'] ?? 1);
        $prodName = htmlspecialchars($p['name'] ?? 'Organic Bananas');
        $prodBrand = htmlspecialchars($p['brand_name'] ?? 'GreenHarvest');
        $prodPrice = number_format((float)($p['price'] ?? 2.99), 2);
        $prodUnit = htmlspecialchars($p['variant_name'] ?? 'bunch (approx 5-6 bananas)');
        $prodDesc = htmlspecialchars($p['description'] ?? 'Fresh, sweet, and ethically sourced. These organic bananas are perfect for snacking, baking, or blending into your morning smoothie.');
        $prodImg = !empty($p['image_url']) ? URLROOT . '/' . htmlspecialchars($p['image_url']) : '';
    ?>
    <!-- Left: Image -->
    <div class="pd-image-box">
        <?php if ($prodImg): ?>
            <img src="<?= $prodImg ?>" alt="<?= $prodName ?>">
        <?php else: ?>
            <span class="pd-image-placeholder">[High-Res Product Image]</span>
        <?php endif; ?>
    </div>
    
    <!-- Right: Info -->
    <div class="pd-info-col">
        <span class="pd-brand-tag"><?= $prodBrand ?></span>
        <h1 class="pd-title"><?= $prodName ?></h1>
        
        <div class="pd-price-row">
            <strong class="pd-price-val">$<?= $prodPrice ?></strong>
            <span class="pd-price-unit">/ <?= $prodUnit ?></span>
        </div>
        
        <p class="pd-desc">
            <?= $prodDesc ?>
        </p>

        <form method="POST" action="<?= URLROOT; ?>/cart/add" class="pd-form-action">
            <input type="hidden" name="product_id" value="<?= $prodId ?>">
            <div class="pd-stepper">
                <button type="button" onclick="let inp = this.nextElementSibling; inp.value = Math.max(1, parseInt(inp.value || 1) - 1);" class="pd-stepper-btn">-</button>
                <input type="number" name="quantity" value="1" min="1" class="pd-stepper-input">
                <button type="button" onclick="let inp = this.previousElementSibling; inp.value = parseInt(inp.value || 1) + 1;" class="pd-stepper-btn">+</button>
            </div>
            <button type="submit" class="btn pd-add-cart-btn">Add to Cart</button>
        </form>
    </div>
</div>

<!-- Tabs Section -->
<div class="dashboard-card pd-tabs-card">
    <div class="pd-tabs-nav">
        <button class="pd-tab-btn active">Description</button>
        <button class="pd-tab-btn">Nutritional Information</button>
        <button class="pd-tab-btn">Ingredients</button>
    </div>
    <div class="pd-tab-body">
        <p>Sourced directly from certified organic farms, our bananas are grown without the use of synthetic pesticides or fertilizers. They are rich in potassium, vitamin B6, and provide an excellent source of sustained energy.</p>
        <ul>
            <li>USDA Certified Organic</li>
            <li>Fair Trade Certified</li>
            <li>Stored and shipped at optimal temperatures for freshness</li>
        </ul>
    </div>
</div>

<!-- Brand Comparison Table -->
<div class="dashboard-card pd-compare-card">
    <h3 class="pd-compare-title">Compare Brands</h3>
    <p class="pd-compare-subtitle">See how this product compares to alternatives in the catalog to ensure you are getting the best value for your budget.</p>
    
    <div class="overflow-x-auto">
        <table class="pd-compare-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Brand</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Unit Size</th>
                    <th class="text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr class="is-active-item">
                    <td class="cell-primary">Organic Bananas</td>
                    <td>GreenHarvest</td>
                    <td class="text-right cell-primary">$2.99</td>
                    <td class="text-right">1 bunch</td>
                    <td class="text-right">
                        <span class="pd-badge-viewing">Currently Viewing</span>
                    </td>
                </tr>
                <tr>
                    <td class="cell-primary">Conventional Bananas</td>
                    <td>DailyChoice</td>
                    <td class="text-right cell-primary">$1.49</td>
                    <td class="text-right">1 bunch</td>
                    <td class="text-right">
                        <button class="btn pd-btn-view-details">View Details</button>
                    </td>
                </tr>
                <tr>
                    <td class="cell-primary">Premium Fair Trade Bananas</td>
                    <td>EcoClean</td>
                    <td class="text-right cell-primary">$3.50</td>
                    <td class="text-right">1 bunch</td>
                    <td class="text-right">
                        <button class="btn pd-btn-view-details">View Details</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

</main>
<?php require APPROOT . '/Views/shared/footer.php'; ?>
