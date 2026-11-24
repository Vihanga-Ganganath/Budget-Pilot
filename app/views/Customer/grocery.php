<?php $pageCss = 'customer-css/grocery'; require APPROOT . '/Views/shared/header.php'; ?>
<main class="main-content">
<header class="content-header">
    <div class="header-left">
        <h2 class="page-title text-navy">Grocery Catalog</h2>
        <p class="page-subtitle">Comparing prices across verified brands</p>
    </div>
</header>

<?php if (!empty($_SESSION['cart_flash'])): 
    $flashMsg = $_SESSION['cart_flash'];
    $flashType = $_SESSION['cart_flash_type'] ?? 'success';
    unset($_SESSION['cart_flash'], $_SESSION['cart_flash_type']);
?>
    <div class="cart-flash <?= $flashType === 'error' ? 'error' : 'success' ?>">
        <?= htmlspecialchars($flashMsg) ?>
    </div>
<?php endif; ?>

<section class="dashboard-card catalog-filters-card">
    <div class="catalog-filters-row">
        <select class="catalog-select">
            <option>All Categories</option>
            <option>Fresh Produce</option>
            <option>Dairy</option>
            <option>Bakery</option>
            <option>Pantry</option>
        </select>
        <select class="catalog-select">
            <option>Brand: All</option>
        </select>
        <select class="catalog-select">
            <option>Price Range</option>
        </select>
        <button class="btn catalog-more-btn">More Filters</button>
    </div>
</section>

<!-- Product Grid -->
<div class="catalog-product-grid">

    <?php 
        $products = $data['products'] ?? [];
        if (!empty($products)): 
            foreach ($products as $p):
                $pId = (int)$p['id'];
                $pName = htmlspecialchars($p['name']);
                $pBrand = htmlspecialchars($p['brand_name'] ?? 'Verified Brand');
                $pPrice = number_format((float)$p['price'], 2);
                $pUnit = htmlspecialchars($p['variant_name'] ?? 'unit');
                $pImg = !empty($p['image_url']) ? URLROOT . '/' . htmlspecialchars($p['image_url']) : '';
    ?>
        <div class="catalog-product-card">
            <div class="catalog-img-box">
                <?php if ($pImg): ?>
                    <img src="<?= $pImg ?>" alt="<?= $pName ?>">
                <?php else: ?>
                    <span class="catalog-img-placeholder">[Product Image]</span>
                <?php endif; ?>
            </div>
            <div class="catalog-card-body">
                <span class="catalog-brand-label"><?= $pBrand ?></span>
                <h3 class="catalog-product-title"><?= $pName ?></h3>
                <div class="catalog-price-row">
                    <strong class="catalog-price-val">$<?= $pPrice ?></strong>
                    <span class="catalog-unit-label">/ <?= $pUnit ?></span>
                </div>
                <div>
                    <a href="<?= URLROOT; ?>/customer/product_detail/<?= $pId ?>" class="catalog-details-link">View Details</a>
                    <form method="POST" action="<?= URLROOT; ?>/cart/add" class="m-0">
                        <input type="hidden" name="product_id" value="<?= $pId ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn catalog-add-cart-btn">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
    <?php 
            endforeach;
        else: 
            // Fallback default catalog items with working Add to Cart forms
            $defaultItems = [
                ['id' => 1, 'brand' => 'GreenHarvest', 'name' => 'Organic Bananas', 'price' => '2.99', 'unit' => 'bunch'],
                ['id' => 2, 'brand' => 'DairyPure', 'name' => 'Whole Milk (1 Gallon)', 'price' => '3.49', 'unit' => 'gal'],
                ['id' => 3, 'brand' => 'Crunchies', 'name' => 'Oat Cereal', 'price' => '4.15', 'unit' => 'box'],
                ['id' => 4, 'brand' => 'GoldenHarvest', 'name' => 'Whole Wheat Bread', 'price' => '3.00', 'unit' => 'loaf']
            ];
            foreach ($defaultItems as $item):
    ?>
        <div class="catalog-product-card">
            <div class="catalog-img-box">
                <span class="catalog-img-placeholder">[Product Image]</span>
            </div>
            <div class="catalog-card-body">
                <span class="catalog-brand-label"><?= $item['brand'] ?></span>
                <h3 class="catalog-product-title"><?= $item['name'] ?></h3>
                <div class="catalog-price-row">
                    <strong class="catalog-price-val">$<?= $item['price'] ?></strong>
                    <span class="catalog-unit-label">/ <?= $item['unit'] ?></span>
                </div>
                <div>
                    <a href="<?= URLROOT; ?>/customer/product_detail/<?= $item['id'] ?>" class="catalog-details-link">View Details</a>
                    <form method="POST" action="<?= URLROOT; ?>/cart/add" class="m-0">
                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn catalog-add-cart-btn">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
    <?php 
            endforeach;
        endif; 
    ?>

</div>

</main>
<?php require APPROOT . '/Views/shared/footer.php'; ?>
