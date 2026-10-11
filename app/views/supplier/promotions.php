<?php require APPROOT . '/Views/shared/header.php'; ?>
<main class="main-content">
<header class="content-header" style="margin-bottom: 24px;">
    <div class="header-left">
        <h2 class="page-title" style="color: var(--color-primary);">Promotions &amp; Marketing</h2>
        <p class="page-subtitle" style="color: var(--color-text);">Manage active campaigns and launch new promotions for your brand.</p>
    </div>
</header>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    
    <!-- LEFT COLUMN: Active Campaigns -->
    <div>
        <section class="dashboard-card" style="background: #fff; border-radius: var(--radius-main); border: 1px solid #CBD5E1; overflow: hidden; margin-bottom: 24px;">
            <div style="padding: 24px; border-bottom: 1px solid #CBD5E1; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: var(--color-primary); font-size: 18px;">Active &amp; Past Campaigns</h3>
                <span style="background: #f1f5f9; color: var(--color-primary); font-size: 12px; padding: 6px 12px; border-radius: 12px; font-weight: 600; border: 1px solid #e2e8f0;">2 Active</span>
            </div>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #CBD5E1; background: #f8f9fa;">
                            <th style="text-align: left; padding: 16px; color: var(--color-primary); font-weight: 600;">Campaign Name</th>
                            <th style="text-align: left; padding: 16px; color: var(--color-primary); font-weight: 600;">Discount</th>
                            <th style="text-align: left; padding: 16px; color: var(--color-primary); font-weight: 600;">Valid Dates</th>
                            <th style="text-align: right; padding: 16px; color: var(--color-primary); font-weight: 600;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Campaign 1 -->
                        <tr style="border-bottom: 1px solid #CBD5E1;">
                            <td style="padding: 16px; color: var(--color-primary); font-weight: 600;">Summer Organic Sale</td>
                            <td style="padding: 16px; color: var(--color-text); font-weight: 500;">15% Off</td>
                            <td style="padding: 16px; color: var(--color-text); font-size: 14px;">Oct 01 - Oct 31, 2026</td>
                            <td style="padding: 16px; text-align: right;">
                                <span style="background: rgba(16, 185, 129, 0.1); color: var(--color-success); font-weight: 600; font-size: 12px; padding: 6px 10px; border-radius: 4px; border: 1px solid var(--color-success);">Active</span>
                            </td>
                        </tr>
                        <!-- Campaign 2 -->
                        <tr style="border-bottom: 1px solid #CBD5E1;">
                            <td style="padding: 16px; color: var(--color-primary); font-weight: 600;">Bulk Buy Discount</td>
                            <td style="padding: 16px; color: var(--color-text); font-weight: 500;">$5.00 Fixed</td>
                            <td style="padding: 16px; color: var(--color-text); font-size: 14px;">Oct 10 - Nov 10, 2026</td>
                            <td style="padding: 16px; text-align: right;">
                                <span style="background: rgba(16, 185, 129, 0.1); color: var(--color-success); font-weight: 600; font-size: 12px; padding: 6px 10px; border-radius: 4px; border: 1px solid var(--color-success);">Active</span>
                            </td>
                        </tr>
                        <!-- Campaign 3 -->
                        <tr>
                            <td style="padding: 16px; color: var(--color-primary); font-weight: 600;">Back to School Promo</td>
                            <td style="padding: 16px; color: var(--color-text); font-weight: 500;">10% Off</td>
                            <td style="padding: 16px; color: var(--color-text); font-size: 14px;">Aug 15 - Sep 15, 2026</td>
                            <td style="padding: 16px; text-align: right;">
                                <span style="background: #f1f5f9; color: var(--color-text-muted); font-weight: 600; font-size: 12px; padding: 6px 10px; border-radius: 4px; border: 1px solid #CBD5E1;">Expired</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <!-- RIGHT COLUMN: Create Form -->
    <div>
        <section class="dashboard-card" style="background: #fff; padding: 24px; border-radius: var(--radius-main); border: 1px solid #CBD5E1;">
            <h3 style="margin: 0 0 24px 0; color: var(--color-primary); font-size: 18px;">Create Promotion</h3>
            <form>
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Campaign Name</label>
                    <input type="text" placeholder="e.g., Holiday Special" style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text);">
                </div>
                
                <div style="display: flex; gap: 16px; margin-bottom: 16px;">
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Discount Type</label>
                        <select style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text); background: #fff; appearance: auto;">
                            <option>Percentage (%)</option>
                            <option>Fixed Amount ($)</option>
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Value</label>
                        <input type="number" step="0.01" placeholder="15" style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text);">
                    </div>
                </div>

                <div style="display: flex; gap: 16px; margin-bottom: 16px;">
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Start Date</label>
                        <input type="date" style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text);">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">End Date</label>
                        <input type="date" style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text);">
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Applicable Products</label>
                    <select multiple style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text); background: #fff; height: 100px;">
                        <option>All Products</option>
                        <option>Organic Bananas</option>
                        <option>Whole Wheat Bread</option>
                        <option>Almond Milk 1L</option>
                    </select>
                    <span style="display: block; font-size: 12px; color: var(--color-text-muted); margin-top: 4px;">Hold Ctrl (Windows) or Cmd (Mac) to select multiple.</span>
                </div>
                
                <button type="button" class="btn" style="width: 100%; background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 14px; border-radius: 8px; font-weight: 600; font-size: 16px; cursor: pointer;">Launch Campaign</button>
            </form>
        </section>
    </div>

</div>

</main>
<?php require APPROOT . '/Views/shared/footer.php'; ?>
