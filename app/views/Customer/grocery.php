<?php require APPROOT . '/Views/shared/header.php'; ?>
<main class="main-content">
<header class="content-header">
    <div class="header-left">
        <h2 class="page-title" style="color: var(--color-primary);">Grocery Catalog</h2>
        <p class="page-subtitle" style="color: var(--color-text);">Comparing prices across verified brands</p>
    </div>
</header>

<section class="dashboard-card" style="background: #fff; padding: 24px; border-radius: var(--radius-main); border: 1px solid #CBD5E1; margin-bottom: 24px;">
    <div style="display: flex; gap: 16px; flex-wrap: wrap;">
        <select style="padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text); min-width: 200px; background: #fff;">
            <option>All Categories</option>
            <option>Fresh Produce</option>
            <option>Dairy</option>
        </select>
        <select style="padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text); min-width: 200px; background: #fff;">
            <option>Brand: All</option>
        </select>
        <select style="padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text); min-width: 200px; background: #fff;">
            <option>Price Range</option>
        </select>
        <button class="btn" style="background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">More Filters</button>
    </div>
</section>

<!-- Product Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 24px; padding-bottom: 40px;">
    
    <!-- Dummy Product Card 1 -->
    <div style="background: #fff; border: 1px solid #CBD5E1; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column;">
        <div style="height: 180px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-bottom: 1px solid #CBD5E1;">
            <span style="color: var(--color-text-muted); font-size: 14px;">[Product Image]</span>
        </div>
        <div style="padding: 16px; display: flex; flex-direction: column; flex-grow: 1;">
            <span style="color: var(--color-text-muted); font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">GreenHarvest</span>
            <h3 style="margin: 0 0 12px 0; color: var(--color-primary); font-size: 16px; line-height: 1.4;">Organic Bananas</h3>
            <div style="margin-top: auto; display: flex; align-items: baseline; gap: 8px; margin-bottom: 16px;">
                <strong style="color: var(--color-primary); font-size: 20px;">$2.99</strong>
                <span style="color: var(--color-text-muted); font-size: 13px;">/ bunch</span>
            </div>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <a href="<?php echo URLROOT; ?>/customer/product_detail" style="text-align: center; color: var(--color-primary); font-size: 13px; font-weight: 600; text-decoration: underline;">View Details</a>
                <button class="btn" style="background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 10px; border-radius: 6px; font-weight: 600; cursor: pointer;">Add to Cart</button>
            </div>
        </div>
    </div>

    <!-- Dummy Product Card 2 -->
    <div style="background: #fff; border: 1px solid #CBD5E1; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column;">
        <div style="height: 180px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-bottom: 1px solid #CBD5E1;">
            <span style="color: var(--color-text-muted); font-size: 14px;">[Product Image]</span>
        </div>
        <div style="padding: 16px; display: flex; flex-direction: column; flex-grow: 1;">
            <span style="color: var(--color-text-muted); font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">DairyPure</span>
            <h3 style="margin: 0 0 12px 0; color: var(--color-primary); font-size: 16px; line-height: 1.4;">Whole Milk (1 Gallon)</h3>
            <div style="margin-top: auto; display: flex; align-items: baseline; gap: 8px; margin-bottom: 16px;">
                <strong style="color: var(--color-primary); font-size: 20px;">$3.49</strong>
                <span style="color: var(--color-text-muted); font-size: 13px;">/ gal</span>
            </div>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <a href="<?php echo URLROOT; ?>/customer/product_detail" style="text-align: center; color: var(--color-primary); font-size: 13px; font-weight: 600; text-decoration: underline;">View Details</a>
                <button class="btn" style="background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 10px; border-radius: 6px; font-weight: 600; cursor: pointer;">Add to Cart</button>
            </div>
        </div>
    </div>

    <!-- Dummy Product Card 3 -->
    <div style="background: #fff; border: 1px solid #CBD5E1; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column;">
        <div style="height: 180px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-bottom: 1px solid #CBD5E1;">
            <span style="color: var(--color-text-muted); font-size: 14px;">[Product Image]</span>
        </div>
        <div style="padding: 16px; display: flex; flex-direction: column; flex-grow: 1;">
            <span style="color: var(--color-text-muted); font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Crunchies</span>
            <h3 style="margin: 0 0 12px 0; color: var(--color-primary); font-size: 16px; line-height: 1.4;">Oat Cereal</h3>
            <div style="margin-top: auto; display: flex; align-items: baseline; gap: 8px; margin-bottom: 16px;">
                <strong style="color: var(--color-primary); font-size: 20px;">$4.15</strong>
                <span style="color: var(--color-text-muted); font-size: 13px;">/ box</span>
            </div>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <a href="<?php echo URLROOT; ?>/customer/product_detail" style="text-align: center; color: var(--color-primary); font-size: 13px; font-weight: 600; text-decoration: underline;">View Details</a>
                <button class="btn" style="background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 10px; border-radius: 6px; font-weight: 600; cursor: pointer;">Add to Cart</button>
            </div>
        </div>
    </div>
    
    <!-- Dummy Product Card 4 -->
    <div style="background: #fff; border: 1px solid #CBD5E1; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column;">
        <div style="height: 180px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-bottom: 1px solid #CBD5E1;">
            <span style="color: var(--color-text-muted); font-size: 14px;">[Product Image]</span>
        </div>
        <div style="padding: 16px; display: flex; flex-direction: column; flex-grow: 1;">
            <span style="color: var(--color-text-muted); font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">GoldenHarvest</span>
            <h3 style="margin: 0 0 12px 0; color: var(--color-primary); font-size: 16px; line-height: 1.4;">Whole Wheat Bread</h3>
            <div style="margin-top: auto; display: flex; align-items: baseline; gap: 8px; margin-bottom: 16px;">
                <strong style="color: var(--color-primary); font-size: 20px;">$3.00</strong>
                <span style="color: var(--color-text-muted); font-size: 13px;">/ loaf</span>
            </div>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <a href="<?php echo URLROOT; ?>/customer/product_detail" style="text-align: center; color: var(--color-primary); font-size: 13px; font-weight: 600; text-decoration: underline;">View Details</a>
                <button class="btn" style="background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 10px; border-radius: 6px; font-weight: 600; cursor: pointer;">Add to Cart</button>
            </div>
        </div>
    </div>

</div>

</main>
<?php require APPROOT . '/Views/shared/footer.php'; ?>
