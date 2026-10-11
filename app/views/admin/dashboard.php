
<?php require APPROOT . '/Views/shared/header.php'; ?>
<header class="content-header">
                <div class="header-left">
                    <h2 class="page-title">Welcome back, <strong><?php echo htmlspecialchars($userName); ?></strong> </h2>
                    <p class="page-subtitle">Platform-wide overview and pending actions across Budget Pilot.</p>
                </div>
                <div class="header-right">
                    <button class="btn btn-navy btn-lg" id="export-report-btn">
                        
                        Export System Report
                    </button>
                </div>
            </header>

            <!-- Stat Cards -->
            <section class="stat-grid">
                <div class="stat-card">
                    <div class="stat-card-label">
                        Total Users
                        
                    </div>
                    <div class="stat-card-value">2,846 <span class="stat-trend">+8.2%</span></div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-label">
                        Active Suppliers
                        
                    </div>
                    <div class="stat-card-value">132 <span class="stat-trend">+3.1%</span></div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-label">
                        Pending Approvals
                        
                    </div>
                    <div class="stat-card-value">23 <span class="stat-trend">Review</span></div>
                </div>
                <div class="stat-card ">
                    <div class="stat-card-label">
                        Security Alerts
                        
                    </div>
                    <div class="stat-card-value">3 <span class="stat-trend">Critical</span></div>
                </div>
            </section>

            <!-- Charts -->
            <section class="admin-grid-2">
                <div class="dashboard-card">
                    <div class="card-heading-row">
                        <h3>Platform Growth</h3>
                        <div class="chip-toggle-group">
                            <button class="chip-toggle" id="chip-1m" type="button">1M</button>
                            <button class="chip-toggle active" id="chip-1y" type="button">1Y</button>
                        </div>
                    </div>
                    <div class="line-chart-wrap">
    <svg viewBox="0 0 600 220" preserveAspectRatio="none">
        <g id="chart-line-1y" style="display:block;">
            <polyline points="0,190 50,175 100,160 150,168 200,120 250,110 300,95 350,80 400,55 450,45 500,30 550,20 600,15" fill="none" stroke="var(--color-primary)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            <polyline points="0,205 50,200 100,198 150,195 200,180 250,175 300,165 350,155 400,145 450,130 500,120 550,112 600,105" fill="none" stroke="var(--color-success)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        </g>
    </svg>
</div>
                    <div class="chart-legend">
                        <div class="legend-item"><span class="legend-dot" style="background:#00106B;"></span>Users</div>
                        <div class="legend-item"><span class="legend-dot" style="background:#10B981;"></span>Suppliers</div>
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="card-heading-row">
                        <h3>User Distribution</h3>
                    </div>
                    <div class="donut-chart" style="background: conic-gradient(#00106B 0% 78%, #10B981 78% 96%, #CBD5E1 96% 100%);">
                        <div class="donut-center">
                            <span class="donut-value">2.8k</span>
                            <span class="donut-label">Total</span>
                        </div>
                    </div>
                    <div class="donut-legend">
                        <div class="donut-legend-row">
                            <span class="legend-name"><span class="legend-dot" style="background:#00106B;"></span>Customers</span>
                            <span class="legend-pct">78%</span>
                        </div>
                        <div class="donut-legend-row">
                            <span class="legend-name"><span class="legend-dot" style="background:#10B981;"></span>Suppliers</span>
                            <span class="legend-pct">18%</span>
                        </div>
                        <div class="donut-legend-row">
                            <span class="legend-name"><span class="legend-dot" style="background:#CBD5E1;"></span>Admins</span>
                            <span class="legend-pct">4%</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Recent Activity -->
            <section class="dashboard-card">
                <div class="card-heading-row">
                    <h3>Recent Admin Activity</h3>
                    <a href="admin_security_audit.html" class="view-all-link">View All</a>
                </div>
                <div class="activity-list">
                    <div class="activity-row">
                        
                        <div class="activity-text">
                            <div class="activity-title">Approved supplier  GreenLeaf Grocers</div>
                            <div class="activity-sub">10 min ago by You</div>
                        </div>
                        <button class="activity-menu-btn" type="button"></button>
                    </div>
                    <div class="activity-row">
                        
                        <div class="activity-text">
                            <div class="activity-title">Suspended account  user_2291</div>
                            <div class="activity-sub">1 hr ago by System Admin</div>
                        </div>
                        <button class="activity-menu-btn" type="button"></button>
                    </div>
                    <div class="activity-row">
                        
                        <div class="activity-text">
                            <div class="activity-title">Rejected product  Choco Deluxe 250g</div>
                            <div class="activity-sub">2 hr ago by Moderation Bot</div>
                        </div>
                        <button class="activity-menu-btn" type="button"></button>
                    </div>
                    <div class="activity-row">
                        
                        <div class="activity-text">
                            <div class="activity-title">Unlocked account  sarah.perera@mail.com</div>
                            <div class="activity-sub">5 hr ago by You</div>
                        </div>
                        <button class="activity-menu-btn" type="button"></button>
                    </div>
                </div>
            </section>
        <script src="<?php echo URLROOT; ?>/public/js/admin.js?v=<?php echo filemtime(APPROOT . '/../public/js/admin.js'); ?>"></script>

<?php require APPROOT . '/Views/shared/footer.php'; ?>









