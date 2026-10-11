<?php require APPROOT . '/Views/shared/header.php'; ?>
<main class="main-content">
<div class="content-header" style="margin-bottom: 24px;">
    <a href="<?php echo URLROOT; ?>/customer/grocery" style="color: var(--color-primary); text-decoration: none; font-weight: 600; font-size: 14px;">&larr; Back to Catalog</a>
</div>

<!-- Product Top Section -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px; background: #fff; padding: 32px; border-radius: var(--radius-main); border: 1px solid #CBD5E1;">
    <!-- Left: Image -->
    <div style="background: #f8f9fa; border: 1px solid #CBD5E1; border-radius: 12px; height: 400px; display: flex; align-items: center; justify-content: center;">
        <span style="color: var(--color-text-muted); font-size: 16px;">[High-Res Product Image]</span>
    </div>
    
    <!-- Right: Info -->
    <div style="display: flex; flex-direction: column; justify-content: center;">
        <span style="color: var(--color-text-muted); font-size: 14px; font-weight: 600; text-transform: uppercase; margin-bottom: 8px;">GreenHarvest</span>
        <h1 style="margin: 0 0 16px 0; color: var(--color-primary); font-size: 32px; line-height: 1.2;">Organic Bananas</h1>
        
        <div style="display: flex; align-items: baseline; gap: 12px; margin-bottom: 24px;">
            <strong style="color: var(--color-primary); font-size: 36px;">$2.99</strong>
            <span style="color: var(--color-text-muted); font-size: 16px;">/ bunch (approx 5-6 bananas)</span>
        </div>
        
        <p style="color: var(--color-text); font-size: 15px; line-height: 1.6; margin-bottom: 32px;">
            Fresh, sweet, and ethically sourced. These organic bananas are perfect for snacking, baking, or blending into your morning smoothie.
        </p>

        <div style="display: flex; gap: 16px; align-items: center;">
            <div style="display: flex; align-items: center; border: 1px solid #CBD5E1; border-radius: 8px; overflow: hidden;">
                <button style="background: #f8f9fa; border: none; padding: 12px 16px; cursor: pointer; color: var(--color-primary); font-weight: 600;">-</button>
                <input type="text" value="1" style="width: 50px; text-align: center; border: none; border-left: 1px solid #CBD5E1; border-right: 1px solid #CBD5E1; padding: 12px 0; font-size: 16px; font-weight: 600; color: var(--color-primary);">
                <button style="background: #f8f9fa; border: none; padding: 12px 16px; cursor: pointer; color: var(--color-primary); font-weight: 600;">+</button>
            </div>
            <button class="btn" style="background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 14px 32px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; flex-grow: 1;">Add to Cart</button>
        </div>
    </div>
</div>

<!-- Tabs Section -->
<div class="dashboard-card" style="background: #fff; border-radius: var(--radius-main); border: 1px solid #CBD5E1; margin-bottom: 40px;">
    <div style="display: flex; border-bottom: 1px solid #CBD5E1;">
        <button style="background: transparent; border: none; border-bottom: 3px solid var(--color-primary); color: var(--color-primary); font-weight: 600; padding: 16px 24px; font-size: 15px; cursor: pointer;">Description</button>
        <button style="background: transparent; border: none; color: var(--color-text-muted); font-weight: 600; padding: 16px 24px; font-size: 15px; cursor: pointer;">Nutritional Information</button>
        <button style="background: transparent; border: none; color: var(--color-text-muted); font-weight: 600; padding: 16px 24px; font-size: 15px; cursor: pointer;">Ingredients</button>
    </div>
    <div style="padding: 24px; color: var(--color-text); line-height: 1.6; font-size: 15px;">
        <p style="margin: 0 0 16px 0;">Sourced directly from certified organic farms, our bananas are grown without the use of synthetic pesticides or fertilizers. They are rich in potassium, vitamin B6, and provide an excellent source of sustained energy.</p>
        <ul style="margin: 0; padding-left: 20px;">
            <li>USDA Certified Organic</li>
            <li>Fair Trade Certified</li>
            <li>Stored and shipped at optimal temperatures for freshness</li>
        </ul>
    </div>
</div>

<!-- Brand Comparison Table -->
<div class="dashboard-card" style="background: #fff; border-radius: var(--radius-main); border: 1px solid #CBD5E1; padding: 24px; margin-bottom: 40px;">
    <h3 style="margin: 0 0 16px 0; color: var(--color-primary); font-size: 18px;">Compare Brands</h3>
    <p style="color: var(--color-text-muted); font-size: 14px; margin-bottom: 24px;">See how this product compares to alternatives in the catalog to ensure you are getting the best value for your budget.</p>
    
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #CBD5E1; background: #f8f9fa;">
                    <th style="text-align: left; padding: 16px; color: var(--color-primary); font-weight: 600;">Product</th>
                    <th style="text-align: left; padding: 16px; color: var(--color-primary); font-weight: 600;">Brand</th>
                    <th style="text-align: right; padding: 16px; color: var(--color-primary); font-weight: 600;">Price</th>
                    <th style="text-align: right; padding: 16px; color: var(--color-primary); font-weight: 600;">Unit Size</th>
                    <th style="text-align: right; padding: 16px; color: var(--color-primary); font-weight: 600;">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #CBD5E1; background: rgba(16, 185, 129, 0.05);">
                    <td style="padding: 16px; color: var(--color-primary); font-weight: 600;">Organic Bananas</td>
                    <td style="padding: 16px; color: var(--color-text);">GreenHarvest</td>
                    <td style="padding: 16px; color: var(--color-primary); font-weight: 600; text-align: right;">$2.99</td>
                    <td style="padding: 16px; color: var(--color-text); text-align: right;">1 bunch</td>
                    <td style="padding: 16px; text-align: right;">
                        <span style="color: var(--color-success); font-weight: 600; font-size: 13px;">Currently Viewing</span>
                    </td>
                </tr>
                <tr style="border-bottom: 1px solid #CBD5E1;">
                    <td style="padding: 16px; color: var(--color-primary); font-weight: 600;">Conventional Bananas</td>
                    <td style="padding: 16px; color: var(--color-text);">DailyChoice</td>
                    <td style="padding: 16px; color: var(--color-primary); font-weight: 600; text-align: right;">$1.49</td>
                    <td style="padding: 16px; color: var(--color-text); text-align: right;">1 bunch</td>
                    <td style="padding: 16px; text-align: right;">
                        <button class="btn" style="background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600;">View Details</button>
                    </td>
                </tr>
                <tr style="border-bottom: 1px solid #CBD5E1;">
                    <td style="padding: 16px; color: var(--color-primary); font-weight: 600;">Premium Fair Trade Bananas</td>
                    <td style="padding: 16px; color: var(--color-text);">EcoClean</td>
                    <td style="padding: 16px; color: var(--color-primary); font-weight: 600; text-align: right;">$3.50</td>
                    <td style="padding: 16px; color: var(--color-text); text-align: right;">1 bunch</td>
                    <td style="padding: 16px; text-align: right;">
                        <button class="btn" style="background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600;">View Details</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

</main>
<?php require APPROOT . '/Views/shared/footer.php'; ?>
