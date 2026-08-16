<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Budget Pilot Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/common.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin.css">
</head>
<body data-page="users">

    <div class="dashboard-layout">
        <?php require '../app/Views/admin/_sidebar.php'; ?>

        <main class="main-content">
            <?php require '../app/Views/admin/_topbar.php'; ?>

            <header class="content-header">
                <div class="header-left">
                    <h2 class="page-title">User Management</h2>
                    <p class="page-subtitle">Manage and monitor all platform users, roles, and access status.</p>
                </div>
                <div class="header-right">
                    <button class="btn btn-navy btn-lg" onclick="document.getElementById('add-admin-modal').style.display='flex'" type="button">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px;"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                        Add Admin User
                    </button>
                </div>
            </header>

            <!-- Stat Cards -->
            <section class="stat-grid">
                <div class="stat-card">
                    <div class="stat-card-label">
                        Total Users
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                    </div>
                    <div class="stat-card-value" id="stat-total-users">12,458</div>
                    <div class="stat-card-sub"><span class="stat-trend up">↑ 12%</span> vs last month</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-label">
                        Active
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    </div>
                    <div class="stat-card-value" id="stat-active-users">11,204</div>
                    <div class="stat-card-sub">90% of total users</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-label">
                        Suspended
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                    </div>
                    <div class="stat-card-value" id="stat-suspended-users">843</div>
                    <div class="stat-card-sub"><span class="stat-trend down">↑ 5%</span> vs last month</div>
                </div>
                <div class="stat-card accent-navy">
                    <div class="stat-card-label">
                        Locked Accounts
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </div>
                    <div class="stat-card-value" id="stat-locked-users">411</div>
                    <div class="stat-card-sub">Requires immediate action</div>
                </div>
            </section>

            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="admin-search-wrapper">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" id="user-search-input" placeholder="Filter users...">
                </div>
                <select class="filter-select" id="role-filter">
                    <option value="all">All Roles</option>
                    <option value="customer">Customer</option>
                    <option value="supplier">Supplier</option>
                    <option value="admin">Admin</option>
                </select>
                <select class="filter-select" id="status-filter">
                    <option value="all">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="suspended">Suspended</option>
                    <option value="locked">Locked</option>
                </select>
                <div class="filter-spacer"></div>
                <button class="btn btn-outline-navy btn-sm" id="more-filters-btn" type="button">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    More Filters
                </button>
            </div>

            <!-- User Table -->
            <div class="admin-table-card">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" class="admin-checkbox" id="select-all-checkbox"></th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="user-table-body">
                        <!-- rendered by js/admin.js -->
                    </tbody>
                </table>
                <div class="table-footer">
                    <span class="table-footer-info" id="table-footer-info">Showing entries…</span>
                    <div class="pagination" id="pagination-container"></div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Admin User Modal -->
    <div class="modal-overlay" id="add-admin-modal">
        <div class="modal-card">
            <div class="modal-header">
                <h3>Add Admin User</h3>
                <button class="modal-close-btn" type="button" onclick="document.getElementById('add-admin-modal').style.display='none'">&times;</button>
            </div>
            <form id="add-admin-form">
                <div class="form-field-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" placeholder="Jane Doe" required>
                </div>
                <div class="form-field-group">
                    <label>Work Email</label>
                    <input type="email" name="email" placeholder="jane@budgetpilot.com" required>
                </div>
                <div class="form-field-group">
                    <label>Role</label>
                    <select class="filter-select" name="role" style="width:100%;">
                        <option value="admin">Admin</option>
                        <option value="moderator">Moderator (Restricted)</option>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-reject btn-sm" onclick="document.getElementById('add-admin-modal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn btn-navy btn-sm">Send Invite</button>
                </div>
            </form>
        </div>
    </div>

    <script src="<?php echo URLROOT; ?>/public/js/admin.js"></script>
</body>
</html>
