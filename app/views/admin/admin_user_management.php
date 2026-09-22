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

            <?php if (!empty($data['flash'])): ?>
            <?php
                $ft = $data['flash_type'] ?? 'success';
                $fbg  = $ft === 'success' ? '#E8F6EE' : ($ft === 'warning' ? '#FEF9E8' : '#FDF6F6');
                $fbd  = $ft === 'success' ? '#BDE6CE' : ($ft === 'warning' ? '#F5D87A' : '#F0BCBC');
                $ftx  = $ft === 'success' ? '#1B6B42' : ($ft === 'warning' ? '#7A5C00' : '#C2373C');
            ?>
            <div style="background:<?php echo $fbg;?>;border:1px solid <?php echo $fbd;?>;color:<?php echo $ftx;?>;border-radius:9px;padding:12px 16px;margin-bottom:20px;font-size:.9rem;font-weight:500;">
                <?php echo $data['flash']; ?>
                <button onclick="this.parentElement.remove()" style="float:right;background:none;border:0;cursor:pointer;font-size:1rem;color:<?php echo $ftx;?>;line-height:1;">×</button>
            </div>
            <?php endif; ?>

            <!-- Stat Cards -->
            <section class="stat-grid">
                <div class="stat-card">
                    <div class="stat-card-label">
                        Total Users
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                    </div>
                    <!-- Total ගාණ -->
                    <div class="stat-card-value" id="stat-total-users"><?php echo $data['stats']->total_users; ?></div>
                    <div class="stat-card-sub"><span class="stat-trend up">↑ 12%</span> vs last month</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-label">
                        Active
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    </div>
                    <!-- Active ගාණ -->
                    <div class="stat-card-value" id="stat-active-users"><?php echo $data['stats']->active_users ?: 0; ?></div>
                    <div class="stat-card-sub">Current active accounts</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-label">
                        Suspended
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                    </div>
                    <!-- Suspended ගාණ -->
                    <div class="stat-card-value" id="stat-suspended-users"><?php echo $data['stats']->suspended_users ?: 0; ?></div>
                    <div class="stat-card-sub">Blocked from platform</div>
                </div>

                <div class="stat-card accent-navy">
                    <div class="stat-card-label">
                        Locked Accounts
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </div>
                    <!-- Locked ගාණ -->
                    <div class="stat-card-value" id="stat-locked-users"><?php echo $data['stats']->locked_users ?: 0; ?></div>
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
                        <?php if(!empty($data['users'])) : ?>
                            <?php foreach($data['users'] as $user) : ?>
                                <?php 
                                    // 1. නමේ මුල් අකුර
                                    $initial = strtoupper(substr($user->name, 0, 1));
                                    
                                    // 2. Role එකට අදාළ පාට
                                    $roleClass = 'pill-gray';
                                    if($user->role == 'admin') { $roleClass = 'pill-navy'; }
                                    elseif($user->role == 'supplier') { $roleClass = 'pill-blue'; }
                                    
                                    // 3. අලුත් Status එකට අදාළ පාට තෝරාගැනීම
                                    $status = strtoupper($user->account_status);
                                    $statusClass = 'pill-green'; // Default is active
                                    
                                    if($user->account_status == 'locked') {
                                        $statusClass = 'pill-red';
                                    } elseif($user->account_status == 'suspended') {
                                        $statusClass = 'pill-amber';
                                    }
                                ?>
                                
                                <!-- එකවුන්ට් එක active නැත්නම් රතු පාට Background එකක් (flagged-row) වැටෙන්න -->
                                <tr class="<?php echo $user->account_status != 'active' ? 'flagged-row' : ''; ?>" data-user-id="<?php echo $user->id; ?>">
                                    <td><input type="checkbox" class="admin-checkbox row-checkbox"></td>
                                    <td>
                                        <div class="cell-user">
                                            <div class="cell-avatar" style="background:#E0E7FF;color:#3730A3;"><?php echo $initial; ?></div>
                                            <div>
                                                <div class="cell-name"><?php echo $user->name; ?></div>
                                                <div class="cell-sub"><?php echo $user->email; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="pill <?php echo $roleClass; ?>"><?php echo strtoupper($user->role); ?></span></td>
                                    <td><span class="pill <?php echo $statusClass; ?>"><span class="pill-dot"></span><?php echo $status; ?></span></td>
                                    <td><?php echo $user->last_login_at ? date('Y-m-d H:i', strtotime($user->last_login_at)) : 'Never'; ?></td>
                                    <td style="position:relative;"><button class="row-actions-btn" data-user-id="<?php echo $user->id; ?>">⋮</button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td colspan="6" style="text-align:center;color:var(--text-light);padding:32px;">No users found in database.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="table-footer">
                    <span class="table-footer-info" id="table-footer-info">
                        Showing <?php echo ($data['totalUsers'] > 0) ? $data['offset'] + 1 : 0; ?> to 
                        <?php echo min($data['offset'] + $data['limit'], $data['totalUsers']); ?> 
                        of <?php echo $data['totalUsers']; ?> entries
                    </span>
                    
                    <div class="pagination" id="pagination-container">
                        <!-- කලින් පිටුවට යන බොත්තම -->
                        <?php if($data['currentPage'] > 1) : ?>
                            <a href="?page=<?php echo $data['currentPage'] - 1; ?>" style="padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 6px; color: #334155; text-decoration: none; background: white;">&lsaquo;</a>
                        <?php endif; ?>

                        <!-- පිටු අංක ටික -->
                        <?php for($i = 1; $i <= $data['totalPages']; $i++) : ?>
                            <a href="?page=<?php echo $i; ?>" style="padding: 6px 14px; margin: 0 4px; border-radius: 6px; text-decoration: none; display: inline-block; font-weight: 600; <?php echo ($i == $data['currentPage']) ? 'background: #1e293b; color: #ffffff;' : 'background: #ffffff; color: #334155; border: 1px solid #cbd5e1;'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <!-- ඊළඟ පිටුවට යන බොත්තම -->
                        <?php if($data['currentPage'] < $data['totalPages']) : ?>
                            <a href="?page=<?php echo $data['currentPage'] + 1; ?>" style="padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 6px; color: #334155; text-decoration: none; background: white;">&rsaquo;</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Admin User Modal -->
    <div class="modal-overlay" id="add-admin-modal" style="display:none;align-items:center;justify-content:center;">
        <div class="modal-card" style="max-width:460px;width:100%;margin:auto;">
            <div class="modal-header">
                <h3>Add Admin User</h3>
                <button class="modal-close-btn" type="button" id="closeAdminModal">&#x2715;</button>
            </div>

            <?php if (!empty($data['modal_error'])): ?>
            <div style="background:#FDF6F6;border:1px solid #F0BCBC;color:#C2373C;border-radius:8px;padding:11px 14px;margin:0 0 16px;font-size:.88rem;font-weight:500;">
                <?php echo htmlspecialchars($data['modal_error']); ?>
            </div>
            <?php endif; ?>

            <p style="font-size:.88rem;color:#6B7280;margin:0 0 18px;">
                This creates a new admin row in the users table.
                If the email already exists as a customer or supplier, a separate admin account will be added for it.
            </p>

            <form method="POST" action="<?php echo URLROOT; ?>/admin/addAdmin" id="add-admin-form">

                <div class="form-field-group">
                    <label>Full Name <span style="color:#C2373C">*</span></label>
                    <input type="text" name="full_name"
                           placeholder="Jane Doe"
                           value="<?php echo htmlspecialchars($_SESSION['admin_form_name'] ?? ''); ?>"
                           required autocomplete="name" />
                </div>

                <div class="form-field-group">
                    <label>Email Address <span style="color:#C2373C">*</span></label>
                    <input type="email" name="email"
                           placeholder="jane@budgetpilot.com"
                           value="<?php echo htmlspecialchars($_SESSION['admin_form_email'] ?? ''); ?>"
                           required autocomplete="email" />
                    <p style="font-size:.8rem;color:#9CA3AF;margin:4px 0 0;">
                        If this email already has a customer or supplier account, a <em>new admin row</em> is created alongside it.
                    </p>
                </div>

                <div class="form-field-group">
                    <label>Password <span style="color:#C2373C">*</span></label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="adminPw"
                               placeholder="Min. 8 characters"
                               required autocomplete="new-password"
                               style="width:100%;padding-right:42px;" />
                        <button type="button" id="toggleAdminPw"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:0;cursor:pointer;color:#9CA3AF;padding:4px;"
                                aria-label="Show password">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3.8-6.5 10-6.5S22 12 22 12s-3.8 6.5-10 6.5S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-field-group">
                    <label>Confirm Password <span style="color:#C2373C">*</span></label>
                    <div style="position:relative;">
                        <input type="password" name="password_confirm" id="adminPwConfirm"
                               placeholder="Re-enter password"
                               required autocomplete="new-password"
                               style="width:100%;padding-right:42px;" />
                        <button type="button" id="toggleAdminPwConfirm"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:0;cursor:pointer;color:#9CA3AF;padding:4px;"
                                aria-label="Show confirm password">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3.8-6.5 10-6.5S22 12 22 12s-3.8 6.5-10 6.5S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    <p id="pwMatchMsg" style="font-size:.8rem;margin:4px 0 0;display:none;"></p>
                </div>

                <div class="modal-actions" style="margin-top:22px;">
                    <button type="button" class="btn btn-reject btn-sm" id="cancelAdminModal">Cancel</button>
                    <button type="submit" class="btn btn-navy btn-sm">Create Admin Account</button>
                </div>
            </form>
        </div>
    </div>

    <script src="<?php echo URLROOT; ?>/public/js/admin.js"></script>
    <script>
    (function () {
        var modal      = document.getElementById('add-admin-modal');
        var openBtn    = document.querySelector('[onclick*="add-admin-modal"]');
        var closeBtn   = document.getElementById('closeAdminModal');
        var cancelBtn  = document.getElementById('cancelAdminModal');

        function openModal()  { modal.style.display = 'flex'; }
        function closeModal() { modal.style.display = 'none'; }

        if (openBtn)   openBtn.onclick  = openModal;
        if (closeBtn)  closeBtn.onclick = closeModal;
        if (cancelBtn) cancelBtn.onclick = closeModal;

        // Close on backdrop click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });

        // Auto-open if validation failed on previous submit
        <?php if (!empty($_GET['open_modal']) || !empty($data['modal_error'])): ?>
        openModal();
        <?php endif; ?>

        // Password show/hide toggles
        function makeToggle(inputId, btnId) {
            var inp = document.getElementById(inputId);
            var btn = document.getElementById(btnId);
            if (!inp || !btn) return;
            btn.addEventListener('click', function () {
                inp.type = inp.type === 'password' ? 'text' : 'password';
                btn.setAttribute('aria-label', inp.type === 'password' ? 'Show password' : 'Hide password');
            });
        }
        makeToggle('adminPw', 'toggleAdminPw');
        makeToggle('adminPwConfirm', 'toggleAdminPwConfirm');

        // Live password match indicator
        var pwInput   = document.getElementById('adminPw');
        var pwConfirm = document.getElementById('adminPwConfirm');
        var matchMsg  = document.getElementById('pwMatchMsg');

        function checkMatch() {
            if (!pwConfirm.value) { matchMsg.style.display = 'none'; return; }
            var ok = pwInput.value === pwConfirm.value;
            matchMsg.style.display = 'block';
            matchMsg.textContent   = ok ? '✓ Passwords match' : '✗ Passwords do not match';
            matchMsg.style.color   = ok ? '#1B6B42' : '#C2373C';
        }
        pwInput.addEventListener('input', checkMatch);
        pwConfirm.addEventListener('input', checkMatch);

        // Client-side guard before submit
        document.getElementById('add-admin-form').addEventListener('submit', function (e) {
            if (pwInput.value !== pwConfirm.value) {
                e.preventDefault();
                checkMatch();
                pwConfirm.focus();
            }
        });
    })();
    </script>
</body>
</html>
