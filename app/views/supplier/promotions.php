<?php $pageCss = ['supplier/supplier', 'supplier/promotions']; require APPROOT . '/Views/shared/header.php'; ?>
<main class="main-content">
<header class="content-header mb-24">
    <div class="header-left">
        <h2 class="page-title text-navy">Promotions &amp; Marketing</h2>
        <p class="page-subtitle">Manage active campaigns and launch new promotions for your brand.</p>
    </div>
</header>

<div class="promotions-split-layout">
    
    <!-- LEFT COLUMN: Active Campaigns -->
    <div>
        <section class="dashboard-card promotions-table-card">
            <div class="promotions-table-head">
                <h3 class="promotions-table-title">Active &amp; Past Campaigns</h3>
                <span class="promotions-count-pill">2 Active</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="promotions-table">
                    <thead>
                        <tr>
                            <th>Campaign Name</th>
                            <th>Discount</th>
                            <th>Valid Dates</th>
                            <th class="text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Campaign 1 -->
                        <tr>
                            <td class="cell-name">Summer Organic Sale</td>
                            <td class="cell-discount">15% Off</td>
                            <td class="cell-dates">Oct 01 - Oct 31, 2026</td>
                            <td class="text-right">
                                <span class="promotions-badge-active">Active</span>
                            </td>
                        </tr>
                        <!-- Campaign 2 -->
                        <tr>
                            <td class="cell-name">Bulk Buy Discount</td>
                            <td class="cell-discount">$5.00 Fixed</td>
                            <td class="cell-dates">Oct 10 - Nov 10, 2026</td>
                            <td class="text-right">
                                <span class="promotions-badge-active">Active</span>
                            </td>
                        </tr>
                        <!-- Campaign 3 -->
                        <tr>
                            <td class="cell-name">Back to School Promo</td>
                            <td class="cell-discount">10% Off</td>
                            <td class="cell-dates">Aug 15 - Sep 15, 2026</td>
                            <td class="text-right">
                                <span class="promotions-badge-expired">Expired</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <!-- RIGHT COLUMN: Create Form -->
    <div>
        <section class="dashboard-card promotions-form-card">
            <h3 class="promotions-form-title">Create Promotion</h3>
            <form>
                <div class="promotions-field-group">
                    <label class="promotions-label">Campaign Name</label>
                    <input type="text" placeholder="e.g., Holiday Special" class="promotions-input">
                </div>
                
                <div class="promotions-form-row">
                    <div>
                        <label class="promotions-label">Discount Type</label>
                        <select class="promotions-select">
                            <option>Percentage (%)</option>
                            <option>Fixed Amount ($)</option>
                        </select>
                    </div>
                    <div>
                        <label class="promotions-label">Value</label>
                        <input type="number" step="0.01" placeholder="15" class="promotions-input">
                    </div>
                </div>

                <div class="promotions-form-row">
                    <div>
                        <label class="promotions-label">Start Date</label>
                        <input type="date" class="promotions-input">
                    </div>
                    <div>
                        <label class="promotions-label">End Date</label>
                        <input type="date" class="promotions-input">
                    </div>
                </div>

                <div class="promotions-field-group mb-24">
                    <label class="promotions-label">Applicable Products</label>
                    <select multiple class="promotions-select-multi">
                        <option>All Products</option>
                        <option>Organic Bananas</option>
                        <option>Whole Wheat Bread</option>
                        <option>Almond Milk 1L</option>
                    </select>
                    <span class="promotions-hint">Hold Ctrl (Windows) or Cmd (Mac) to select multiple.</span>
                </div>
                
                <button type="button" class="btn promotions-btn-submit">Launch Campaign</button>
            </form>
        </section>
    </div>

</div>

</main>
<?php require APPROOT . '/Views/shared/footer.php'; ?>
