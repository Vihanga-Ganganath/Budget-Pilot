<?php $pageCss = ['supplier/supplier', 'supplier/manage_pricing']; require APPROOT . '/Views/shared/header.php'; ?>
<main class="main-content">
<header class="content-header pricing-header">
    <div class="header-left">
        <a href="<?php echo URLROOT; ?>/supplier/catalog" class="pricing-back-link">&larr; Back to Catalog</a>
        <h2 class="page-title pricing-title">Manage Pricing: Organic Whole Milk</h2>
        <p class="page-subtitle pricing-subtitle">Schedule future price changes and view pricing history.</p>
    </div>
</header>

<!-- Top Section: Current Price -->
<section class="dashboard-card pricing-hero">
    <span class="pricing-hero-label">Current Active Price</span>
    <strong class="pricing-hero-value">$4.99</strong>
    <span class="pricing-hero-sub">Effective since Sep 01, 2026</span>
</section>

<div class="pricing-grid">
    <!-- Left: Scheduling Form -->
    <div>
        <section class="dashboard-card pricing-card">
            <h3 class="pricing-card-title">Schedule Price Change</h3>
            <form>
                <div class="pricing-group">
                    <label class="pricing-label">New Price ($)</label>
                    <input type="number" step="0.01" placeholder="5.49" class="pricing-input">
                </div>
                
                <div class="pricing-group">
                    <label class="pricing-label">Effective Start Date</label>
                    <input type="date" class="pricing-input">
                </div>

                <div class="pricing-group mb-24">
                    <label class="pricing-label">End Date (Optional)</label>
                    <input type="date" class="pricing-input">
                    <span class="pricing-hint">Leave blank for a permanent price change.</span>
                </div>
                
                <button type="button" class="btn pricing-btn-submit">Schedule Price</button>
            </form>
        </section>
    </div>

    <!-- Right: Table -->
    <div>
        <section class="dashboard-card pricing-card-flush">
            <div class="pricing-card-header">
                <h3 class="pricing-card-title m-0">Pricing History &amp; Scheduled</h3>
            </div>
            
            <div class="pricing-table-wrap">
                <table class="pricing-table">
                    <thead>
                        <tr>
                            <th class="text-left">Status</th>
                            <th class="text-right">Price</th>
                            <th class="text-left">Start Date</th>
                            <th class="text-left">End Date</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Scheduled -->
                        <tr>
                            <td>
                                <span class="pricing-badge-scheduled">Scheduled</span>
                            </td>
                            <td class="text-right text-primary font-bold">$5.49</td>
                            <td class="font-date">Nov 01, 2026</td>
                            <td class="font-date text-muted">--</td>
                            <td class="text-right">
                                <button class="btn pricing-btn-cancel">Cancel</button>
                            </td>
                        </tr>
                        <!-- Active -->
                        <tr class="pricing-row-active">
                            <td>
                                <span class="pricing-badge-active">Active</span>
                            </td>
                            <td class="text-right text-primary font-bold">$4.99</td>
                            <td class="font-date">Sep 01, 2026</td>
                            <td class="font-date">Oct 31, 2026</td>
                            <td class="text-right">
                                <span class="text-muted fs-13">--</span>
                            </td>
                        </tr>
                        <!-- Past -->
                        <tr>
                            <td>
                                <span class="pricing-badge-past">Past</span>
                            </td>
                            <td class="text-right text-muted font-bold">$5.25</td>
                            <td class="font-date text-muted">Jan 15, 2026</td>
                            <td class="font-date text-muted">Aug 31, 2026</td>
                            <td class="text-right">
                                <span class="text-muted fs-13">--</span>
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
