<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Approvals - Budget Pilot Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../public/css/common.css">
    <link rel="stylesheet" href="../../../public/css/admin.css">
</head>
<body data-page="suppliers">

    <div class="dashboard-layout">
        <div id="sidebar-mount" data-active="suppliers"></div>

        <main class="main-content">
            <div id="topbar-mount" data-variant="default"></div>

            <header class="content-header">
                <div class="header-left">
                    <h2 class="page-title">Supplier Approvals</h2>
                    <p class="page-subtitle">Review and verify pending supplier registrations.</p>
                </div>
            </header>

            <!-- Stat Cards -->
            <section class="stat-grid cols-3">
                <div class="stat-card">
                    <div class="stat-card-label">Pending Verification</div>
                    <div class="stat-card-value" id="stat-pending-verification">24</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-label">Verified This Week</div>
                    <div class="stat-card-value"><span id="stat-verified-week">156</span> <span class="stat-trend up">+12%</span></div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-label">Rejected</div>
                    <div class="stat-card-value"><span id="stat-rejected">8</span> <span class="stat-trend" style="color:var(--text-light); background:var(--input-bg);">vs 12 last wk</span></div>
                </div>
            </section>

            <div class="card-heading-row">
                <h3>Awaiting Verification</h3>
                <a href="#" class="view-all-link" id="view-all-suppliers-link">View All →</a>
            </div>

            <!-- Approval Cards -->
            <div class="approval-grid" id="pending-suppliers-container">
                <!-- rendered by js/admin.js -->
            </div>

            <!-- Recent Decisions -->
            <div class="dashboard-card" style="padding:0;">
                <div style="padding: 24px 24px 0;">
                    <h3 style="margin-bottom:16px;">Recent Decisions</h3>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Supplier Name</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Reviewed By</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody id="decisions-table-body">
                        <!-- rendered by js/admin.js -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="../../../public/js/admin.js"></script>
</body>
</html>
