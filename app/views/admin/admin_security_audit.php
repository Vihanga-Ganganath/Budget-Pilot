
<?php require APPROOT . '/Views/shared/header.php'; ?>
<header class="content-header">
                <div class="header-left">
                    <h2 class="page-title">Security &amp; Audit Management</h2>
                    <p class="page-subtitle">Monitor system integrity, unauthorized access attempts, and administrative actions.</p>
                </div>
                <div class="header-right">
                    <button class="btn btn-outline-navy btn-lg" id="export-logs-btn" type="button">
                        
                        Export Logs
                    </button>
                    <button class="btn btn-navy btn-lg" id="lockdown-btn" type="button">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        Lockdown Mode
                    </button>
                </div>
            </header>

            <!-- Critical Alert -->
            <div class="critical-alert-banner">
                <div class="critical-alert-left">
                    <svg class="critical-alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    <div>
                        <div class="critical-alert-title">Critical Security Alert</div>
                        <div class="critical-alert-desc">Multiple failed login attempts detected from unrecognized IP addresses targeting Admin accounts in the last 15 minutes.</div>
                    </div>
                </div>
                <button class="btn btn-danger-solid btn-sm" id="view-alert-details-btn" type="button">View Details</button>
            </div>

            <section class="admin-grid-2 even">
                <!-- Suspicious Logins -->
                <div class="dashboard-card">
                    <div class="card-heading-row">
                        <h3>Suspicious Logins</h3>
                        <span style="font-size:0.78rem;color:var(--text-light);">Last 24h</span>
                    </div>
                    <div id="suspicious-logins-container">
                        <!-- rendered by js/admin.js -->
                    </div>
                </div>

                <!-- Audit Log -->
                <div class="dashboard-card" style="padding:0;">
                    <div class="card-heading-row" style="padding:24px 24px 0;">
                        <h3>System Audit Log</h3>
                        <select class="filter-select" id="audit-action-filter">
                            <option value="all">All Actions</option>
                            <option value="policy">Policy Changes</option>
                            <option value="login">Login Attempts</option>
                            <option value="data">Data Changes</option>
                            <option value="system">System</option>
                            <option value="security">Security</option>
                        </select>
                    </div>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Admin</th>
                                <th>Action</th>
                                <th>Target</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody id="audit-table-body">
                            <!-- rendered by js/admin.js -->
                        </tbody>
                    </table>
                    <div class="table-footer">
                        <span class="table-footer-info" id="audit-footer-info">Showing entries</span>
                        <div class="pagination">
                            <button class="page-btn active">1</button>
                        </div>
                    </div>
                </div>
            </section>
        <script src="<?php echo URLROOT; ?>/public/js/admin.js?v=<?php echo filemtime(APPROOT . '/../public/js/admin.js'); ?>"></script>

<?php require APPROOT . '/Views/shared/footer.php'; ?>






