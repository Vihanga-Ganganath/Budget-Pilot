
<?php require APPROOT . '/Views/shared/header.php'; ?>
<header class="content-header">
                <div class="header-left">
                    <h2 class="page-title">Reports &amp; Analytics</h2>
                    <p class="page-subtitle">Overview of financial performance and platform activity.</p>
                </div>
                <div class="header-right">
                    <select class="filter-select" id="period-select" style="font-weight:600;">
                        <option value="7">Last 7 Days</option>
                        <option value="30" selected>Last 30 Days</option>
                        <option value="90">Last 90 Days</option>
                    </select>
                    <button class="btn btn-navy btn-lg" id="export-pdf-btn" type="button">
                        
                        Export PDF
                    </button>
                </div>
            </header>

            <!-- Stat Cards -->
            <section class="stat-grid">
                <div class="stat-card">
                    <div class="stat-card-label">Total Transactions</div>
                    <div class="stat-card-value" id="stat-total-transactions">124,592</div>
                    <div class="stat-card-sub"><span class="stat-trend up" id="trend-transactions"> 12.5%</span> vs last month</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-label">Total Budget Tracked</div>
                    <div class="stat-card-value" id="stat-total-budget">$4.2M</div>
                    <div class="stat-card-sub"><span class="stat-trend up" id="trend-budget"> 8.2%</span> vs last month</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-label">Avg Transaction Value</div>
                    <div class="stat-card-value" id="stat-avg-transaction">$34.50</div>
                    <div class="stat-card-sub"><span class="stat-trend down" id="trend-avg"> 1.4%</span> vs last month</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-label">Active Suppliers</div>
                    <div class="stat-card-value" id="stat-active-suppliers-report">842</div>
                    <div class="stat-card-sub"><span class="stat-trend up" id="trend-suppliers"> 5.1%</span> vs last month</div>
                </div>
            </section>

            <section class="admin-grid-2 even">
                <!-- Category Performance -->
                <div class="dashboard-card">
                    <div class="card-heading-row">
                        <h3>Category Performance</h3>
                        <button class="activity-menu-btn" type="button"></button>
                    </div>
                    <div id="category-performance-container">
                        <!-- rendered by js/admin.js -->
                    </div>
                </div>

                <!-- Top Suppliers -->
                <div class="dashboard-card">
                    <div class="card-heading-row">
                        <h3>Top Suppliers</h3>
                        <span style="font-size:0.75rem;color:var(--text-light);font-weight:700;">BY VIEWS</span>
                    </div>
                    <div class="top-supplier-row">
                        <div class="top-supplier-icon">
                            
                        </div>
                        <div style="flex:1;">
                            <div class="top-supplier-name">MegaMart Co.</div>
                            <div class="top-supplier-cat">Grocery</div>
                        </div>
                        <div>
                            <div class="top-supplier-value">45.2k</div>
                            <div class="top-supplier-trend up"> 12%</div>
                        </div>
                    </div>
                    <div class="top-supplier-row">
                        <div class="top-supplier-icon">
                            
                        </div>
                        <div style="flex:1;">
                            <div class="top-supplier-name">Swift Transit</div>
                            <div class="top-supplier-cat">Transport</div>
                        </div>
                        <div>
                            <div class="top-supplier-value">38.9k</div>
                            <div class="top-supplier-trend up"> 8%</div>
                        </div>
                    </div>
                    <div class="top-supplier-row">
                        <div class="top-supplier-icon">
                            
                        </div>
                        <div style="flex:1;">
                            <div class="top-supplier-name">City Utilities</div>
                            <div class="top-supplier-cat">Utilities</div>
                        </div>
                        <div>
                            <div class="top-supplier-value">29.1k</div>
                            <div class="top-supplier-trend down"> 3%</div>
                        </div>
                    </div>
                    <div class="top-supplier-row">
                        <div class="top-supplier-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="1" x2="6" y2="4"></line><line x1="10" y1="1" x2="10" y2="4"></line><line x1="14" y1="1" x2="14" y2="4"></line></svg>
                        </div>
                        <div style="flex:1;">
                            <div class="top-supplier-name">Bistro Group</div>
                            <div class="top-supplier-cat">Dining Out</div>
                        </div>
                        <div>
                            <div class="top-supplier-value">22.4k</div>
                            <div class="top-supplier-trend up"> 5%</div>
                        </div>
                    </div>
                    <button class="btn btn-outline-navy btn-sm" id="view-full-suppliers-btn" type="button" style="width:100%; margin-top:16px;">View Full List</button>
                </div>
            </section>
        <script src="<?php echo URLROOT; ?>/public/js/admin.js?v=<?php echo filemtime(APPROOT . '/../public/js/admin.js'); ?>"></script>

<?php require APPROOT . '/Views/shared/footer.php'; ?>






