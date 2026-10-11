<?php require APPROOT . '/Views/shared/header.php'; ?>
<main class="main-content">
<header class="content-header" style="margin-bottom: 24px;">
    <div class="header-left">
        <a href="<?php echo URLROOT; ?>/supplier/catalog" style="color: var(--color-primary); text-decoration: none; font-weight: 600; font-size: 14px; margin-bottom: 8px; display: inline-block;">&larr; Back to Catalog</a>
        <h2 class="page-title" style="color: var(--color-primary);">Manage Pricing: Organic Whole Milk</h2>
        <p class="page-subtitle" style="color: var(--color-text);">Schedule future price changes and view pricing history.</p>
    </div>
</header>

<!-- Top Section: Current Price -->
<section class="dashboard-card" style="background: var(--color-primary); color: #fff; padding: 24px; border-radius: var(--radius-main); margin-bottom: 24px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    <span style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; opacity: 0.9;">Current Active Price</span>
    <strong style="display: block; font-size: 36px; margin-bottom: 4px;">$4.99</strong>
    <span style="font-size: 14px; opacity: 0.8;">Effective since Sep 01, 2026</span>
</section>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
    <!-- Left: Scheduling Form -->
    <div>
        <section class="dashboard-card" style="background: #fff; padding: 24px; border-radius: var(--radius-main); border: 1px solid #CBD5E1;">
            <h3 style="margin: 0 0 24px 0; color: var(--color-primary); font-size: 18px;">Schedule Price Change</h3>
            <form>
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">New Price ($)</label>
                    <input type="number" step="0.01" placeholder="5.49" style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text);">
                </div>
                
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Effective Start Date</label>
                    <input type="date" style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text);">
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">End Date (Optional)</label>
                    <input type="date" style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text);">
                    <span style="display: block; font-size: 12px; color: var(--color-text-muted); margin-top: 6px; line-height: 1.4;">Leave blank for a permanent price change.</span>
                </div>
                
                <button type="button" class="btn" style="width: 100%; background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 14px; border-radius: 8px; font-weight: 600; font-size: 16px; cursor: pointer;">Schedule Price</button>
            </form>
        </section>
    </div>

    <!-- Right: Table -->
    <div>
        <section class="dashboard-card" style="background: #fff; border-radius: var(--radius-main); border: 1px solid #CBD5E1; overflow: hidden;">
            <div style="padding: 24px; border-bottom: 1px solid #CBD5E1;">
                <h3 style="margin: 0; color: var(--color-primary); font-size: 18px;">Pricing History &amp; Scheduled</h3>
            </div>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #CBD5E1; background: #f8f9fa;">
                            <th style="text-align: left; padding: 16px; color: var(--color-primary); font-weight: 600;">Status</th>
                            <th style="text-align: right; padding: 16px; color: var(--color-primary); font-weight: 600;">Price</th>
                            <th style="text-align: left; padding: 16px; color: var(--color-primary); font-weight: 600;">Start Date</th>
                            <th style="text-align: left; padding: 16px; color: var(--color-primary); font-weight: 600;">End Date</th>
                            <th style="text-align: right; padding: 16px; color: var(--color-primary); font-weight: 600;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Scheduled -->
                        <tr style="border-bottom: 1px solid #CBD5E1;">
                            <td style="padding: 16px;">
                                <span style="background: rgba(30, 41, 59, 0.1); color: var(--color-primary); font-weight: 600; font-size: 12px; padding: 6px 10px; border-radius: 4px; border: 1px solid var(--color-primary);">Scheduled</span>
                            </td>
                            <td style="padding: 16px; color: var(--color-primary); font-weight: 600; text-align: right;">$5.49</td>
                            <td style="padding: 16px; color: var(--color-text); font-size: 14px;">Nov 01, 2026</td>
                            <td style="padding: 16px; color: var(--color-text); font-size: 14px; color: var(--color-text-muted);">--</td>
                            <td style="padding: 16px; text-align: right;">
                                <button class="btn" style="background: transparent; border: none; color: var(--color-primary); font-weight: 600; cursor: pointer; text-decoration: underline; font-size: 13px;">Cancel</button>
                            </td>
                        </tr>
                        <!-- Active -->
                        <tr style="border-bottom: 1px solid #CBD5E1; background: rgba(16, 185, 129, 0.05);">
                            <td style="padding: 16px;">
                                <span style="background: rgba(16, 185, 129, 0.1); color: var(--color-success); font-weight: 600; font-size: 12px; padding: 6px 10px; border-radius: 4px; border: 1px solid var(--color-success);">Active</span>
                            </td>
                            <td style="padding: 16px; color: var(--color-primary); font-weight: 600; text-align: right;">$4.99</td>
                            <td style="padding: 16px; color: var(--color-text); font-size: 14px;">Sep 01, 2026</td>
                            <td style="padding: 16px; color: var(--color-text); font-size: 14px;">Oct 31, 2026</td>
                            <td style="padding: 16px; text-align: right;">
                                <span style="color: var(--color-text-muted); font-size: 13px;">--</span>
                            </td>
                        </tr>
                        <!-- Past -->
                        <tr>
                            <td style="padding: 16px;">
                                <span style="background: #f1f5f9; color: var(--color-text-muted); font-weight: 600; font-size: 12px; padding: 6px 10px; border-radius: 4px; border: 1px solid #CBD5E1;">Past</span>
                            </td>
                            <td style="padding: 16px; color: var(--color-text-muted); font-weight: 600; text-align: right;">$5.25</td>
                            <td style="padding: 16px; color: var(--color-text-muted); font-size: 14px;">Jan 15, 2026</td>
                            <td style="padding: 16px; color: var(--color-text-muted); font-size: 14px;">Aug 31, 2026</td>
                            <td style="padding: 16px; text-align: right;">
                                <span style="color: var(--color-text-muted); font-size: 13px;">--</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
</main>
<?php require APPROOT . '/Views/shared/footer.php'; ?>
