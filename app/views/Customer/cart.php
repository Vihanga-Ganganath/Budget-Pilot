<?php require APPROOT . '/Views/shared/header.php'; ?>
<main class="main-content">
<header class="content-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 24px;">
    <div class="header-left" style="flex: 1;">
        <h2 class="page-title" style="color: var(--color-primary);">Shopping Cart</h2>
        <p class="page-subtitle" style="color: var(--color-text);">Review your groceries and their impact on your budget.</p>
    </div>
    
    <!-- Upgraded Budget Progress (Top Right) -->
    <div class="dashboard-card" style="background: #fff; padding: 16px 24px; border-radius: var(--radius-main); border: 1px solid #CBD5E1; min-width: 320px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 12px;">
            <div>
                <span style="display: block; color: var(--color-text-muted); font-size: 13px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Grocery Budget Status</span>
                <strong style="color: var(--color-primary); font-size: 20px;">$45.50</strong> <span style="color: var(--color-text-muted); font-size: 14px;">/ $150.00</span>
            </div>
            <strong style="color: var(--color-success); font-size: 14px;">Healthy (30%)</strong>
        </div>
        <div style="background: #f1f5f9; height: 10px; border-radius: 5px; overflow: hidden; width: 100%;">
            <div style="background: var(--color-success); width: 30%; height: 100%; border-radius: 5px;"></div>
        </div>
    </div>
</header>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 40px;">
    <!-- LEFT: Itemized List -->
    <div>
        <section class="dashboard-card" style="background: #fff; border-radius: var(--radius-main); border: 1px solid #CBD5E1; overflow: hidden;">
            <div style="padding: 24px; border-bottom: 1px solid #CBD5E1; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: var(--color-primary); font-size: 18px;">Itemized List (3 items)</h3>
                <button class="btn" style="background: transparent; border: none; color: var(--color-primary); cursor: pointer; font-size: 13px; font-weight: 600; text-decoration: underline;">Clear All</button>
            </div>
            
            <div style="display: flex; flex-direction: column;">
                
                <!-- Dummy Item 1 -->
                <div style="display: flex; align-items: center; padding: 24px; border-bottom: 1px solid #CBD5E1; gap: 24px;">
                    <div style="width: 80px; height: 80px; background: #f8f9fa; border: 1px solid #CBD5E1; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <span style="color: var(--color-text-muted); font-size: 10px;">[Image]</span>
                    </div>
                    <div style="flex-grow: 1;">
                        <span style="color: var(--color-text-muted); font-size: 12px; font-weight: 600; text-transform: uppercase;">GreenHarvest</span>
                        <h4 style="margin: 4px 0 8px 0; color: var(--color-primary); font-size: 16px;">Organic Bananas</h4>
                        <strong style="color: var(--color-primary); font-size: 16px;">$2.99</strong>
                    </div>
                    <div style="display: flex; align-items: center; border: 1px solid #CBD5E1; border-radius: 8px; overflow: hidden;">
                        <button style="background: #f8f9fa; border: none; padding: 8px 12px; cursor: pointer; color: var(--color-primary); font-weight: 600;">-</button>
                        <input type="text" value="2" style="width: 40px; text-align: center; border: none; border-left: 1px solid #CBD5E1; border-right: 1px solid #CBD5E1; padding: 8px 0; font-size: 14px; font-weight: 600; color: var(--color-primary);">
                        <button style="background: #f8f9fa; border: none; padding: 8px 12px; cursor: pointer; color: var(--color-primary); font-weight: 600;">+</button>
                    </div>
                    <div style="text-align: right; min-width: 80px;">
                        <strong style="display: block; color: var(--color-primary); font-size: 18px; margin-bottom: 8px;">$5.98</strong>
                        <button style="background: transparent; border: none; color: #DC2626; cursor: pointer; font-size: 13px; font-weight: 600; text-decoration: underline;">Remove</button>
                    </div>
                </div>

                <!-- Dummy Item 2 -->
                <div style="display: flex; align-items: center; padding: 24px; border-bottom: 1px solid #CBD5E1; gap: 24px;">
                    <div style="width: 80px; height: 80px; background: #f8f9fa; border: 1px solid #CBD5E1; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <span style="color: var(--color-text-muted); font-size: 10px;">[Image]</span>
                    </div>
                    <div style="flex-grow: 1;">
                        <span style="color: var(--color-text-muted); font-size: 12px; font-weight: 600; text-transform: uppercase;">DairyPure</span>
                        <h4 style="margin: 4px 0 8px 0; color: var(--color-primary); font-size: 16px;">Whole Milk (1 Gallon)</h4>
                        <strong style="color: var(--color-primary); font-size: 16px;">$3.49</strong>
                    </div>
                    <div style="display: flex; align-items: center; border: 1px solid #CBD5E1; border-radius: 8px; overflow: hidden;">
                        <button style="background: #f8f9fa; border: none; padding: 8px 12px; cursor: pointer; color: var(--color-primary); font-weight: 600;">-</button>
                        <input type="text" value="1" style="width: 40px; text-align: center; border: none; border-left: 1px solid #CBD5E1; border-right: 1px solid #CBD5E1; padding: 8px 0; font-size: 14px; font-weight: 600; color: var(--color-primary);">
                        <button style="background: #f8f9fa; border: none; padding: 8px 12px; cursor: pointer; color: var(--color-primary); font-weight: 600;">+</button>
                    </div>
                    <div style="text-align: right; min-width: 80px;">
                        <strong style="display: block; color: var(--color-primary); font-size: 18px; margin-bottom: 8px;">$3.49</strong>
                        <button style="background: transparent; border: none; color: #DC2626; cursor: pointer; font-size: 13px; font-weight: 600; text-decoration: underline;">Remove</button>
                    </div>
                </div>
                
                <!-- Dummy Item 3 -->
                <div style="display: flex; align-items: center; padding: 24px; gap: 24px;">
                    <div style="width: 80px; height: 80px; background: #f8f9fa; border: 1px solid #CBD5E1; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <span style="color: var(--color-text-muted); font-size: 10px;">[Image]</span>
                    </div>
                    <div style="flex-grow: 1;">
                        <span style="color: var(--color-text-muted); font-size: 12px; font-weight: 600; text-transform: uppercase;">EssenceCo</span>
                        <h4 style="margin: 4px 0 8px 0; color: var(--color-primary); font-size: 16px;">Laundry Detergent</h4>
                        <strong style="color: var(--color-primary); font-size: 16px;">$18.00</strong>
                    </div>
                    <div style="display: flex; align-items: center; border: 1px solid #CBD5E1; border-radius: 8px; overflow: hidden;">
                        <button style="background: #f8f9fa; border: none; padding: 8px 12px; cursor: pointer; color: var(--color-primary); font-weight: 600;">-</button>
                        <input type="text" value="2" style="width: 40px; text-align: center; border: none; border-left: 1px solid #CBD5E1; border-right: 1px solid #CBD5E1; padding: 8px 0; font-size: 14px; font-weight: 600; color: var(--color-primary);">
                        <button style="background: #f8f9fa; border: none; padding: 8px 12px; cursor: pointer; color: var(--color-primary); font-weight: 600;">+</button>
                    </div>
                    <div style="text-align: right; min-width: 80px;">
                        <strong style="display: block; color: var(--color-primary); font-size: 18px; margin-bottom: 8px;">$36.03</strong>
                        <button style="background: transparent; border: none; color: #DC2626; cursor: pointer; font-size: 13px; font-weight: 600; text-decoration: underline;">Remove</button>
                    </div>
                </div>

            </div>
        </section>
    </div>

    <!-- RIGHT: Summary Panel -->
    <div>
        <section class="dashboard-card" style="background: #fff; padding: 24px; border-radius: var(--radius-main); border: 1px solid #CBD5E1; position: sticky; top: 100px;">
            <h3 style="margin: 0 0 24px 0; color: var(--color-primary); font-size: 18px;">Order Summary</h3>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; color: var(--color-text); font-size: 15px;">
                <span>Subtotal (5 items)</span>
                <strong>$45.50</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; color: var(--color-text); font-size: 15px;">
                <span>Estimated Tax</span>
                <strong>$3.87</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 24px; color: var(--color-text); font-size: 15px;">
                <span>Delivery Fee</span>
                <strong>$0.00</strong>
            </div>
            
            <div style="border-top: 1px solid #CBD5E1; padding-top: 16px; display: flex; justify-content: space-between; margin-bottom: 24px;">
                <strong style="color: var(--color-primary); font-size: 18px;">Total</strong>
                <strong style="color: var(--color-primary); font-size: 24px;">$49.37</strong>
            </div>
            
            <button class="btn" style="width: 100%; background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 16px; border-radius: 8px; font-weight: 600; font-size: 16px; cursor: pointer; margin-bottom: 12px;">Checkout Now</button>
            <p style="text-align: center; color: var(--color-text-muted); font-size: 12px; margin: 0;">Secure encrypted payment</p>
        </section>
    </div>
</div>
</main>
<?php require APPROOT . '/Views/shared/footer.php'; ?>
