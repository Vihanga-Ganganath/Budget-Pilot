<?php $pageCss = ['admin-css/base', 'admin-css/users']; require APPROOT . '/Views/shared/header.php'; ?>
<header class="content-header">
                <div class="header-left">
                    <h2 class="page-title">User Management</h2>
                    <p class="page-subtitle">Manage and monitor all platform users, roles, and access status.</p>
                </div>
                <div class="header-right">
                    <button class="btn btn-navy btn-lg" onclick="document.getElementById('add-admin-modal').style.display='flex'" type="button">
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
                <?php echo htmlspecialchars($data['flash'], ENT_QUOTES, 'UTF-8'); ?>
                <button onclick="this.parentElement.remove()" style="float:right;background:none;border:0;cursor:pointer;font-size:1rem;color:<?php echo $ftx;?>;line-height:1;"></button>
            </div>
            <?php endif; ?>

            <!-- Stat Cards -->
            <section class="stat-grid">
                <div class="stat-card">
                    <div class="stat-card-label">
                        Total Users
                    </div>
                    <div class="stat-card-value" id="stat-total-users"><?php echo $data['stats']->total_users; ?></div>
                    <div class="stat-card-sub">All registered accounts</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-label">
                        Active
                    </div>
                    <div class="stat-card-value" id="stat-active-users"><?php echo $data['stats']->active_users ?: 0; ?></div>
                    <div class="stat-card-sub">Current active accounts</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-label">
                        Suspended
                    </div>
                    <div class="stat-card-value" id="stat-suspended-users"><?php echo $data['stats']->suspended_users ?: 0; ?></div>
                    <div class="stat-card-sub">Blocked from platform</div>
                </div>

                <div class="stat-card accent-navy">
                    <div class="stat-card-label">
                        Locked Accounts
                    </div>
                    <div class="stat-card-value" id="stat-locked-users"><?php echo $data['stats']->locked_users ?: 0; ?></div>
                    <div class="stat-card-sub">Requires immediate action</div>
                </div>
            </section>

            <form class="filter-bar filter-bar-wrap" method="GET" action="<?php echo URLROOT; ?>/admin/users">
                <input type="search" name="q" class="filter-select" placeholder="Search name or email" value="<?php echo htmlspecialchars($data['search'], ENT_QUOTES, 'UTF-8'); ?>">
                <select class="filter-select" name="role"><option value="all">All Roles</option>
                    <?php foreach (['customer'=>'Customer','supplier'=>'Supplier','admin'=>'Admin'] as $key=>$label): ?><option value="<?php echo $key; ?>" <?php echo $data['role_filter']===$key?'selected':''; ?>><?php echo $label; ?></option><?php endforeach; ?></select>
                <select class="filter-select" name="status"><option value="all">All Statuses</option>
                    <?php foreach (['active'=>'Active','suspended'=>'Suspended','locked'=>'Locked','pending'=>'Pending'] as $key=>$label): ?><option value="<?php echo $key; ?>" <?php echo $data['status_filter']===$key?'selected':''; ?>><?php echo $label; ?></option><?php endforeach; ?></select>
                <button class="btn btn-navy btn-sm" type="submit">Apply Filters</button>
                <a class="btn btn-outline-navy btn-sm" href="<?php echo URLROOT; ?>/admin/users">Clear</a>
            </form>

            <!-- User Table -->
            <div class="admin-table-card">
                <table class="admin-table">
                    <thead>
                        <tr>
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
                                    $initial = strtoupper(substr($user->name, 0, 1));
                                    
                                    $roleClass = 'pill-gray';
                                    if($user->role == 'admin') { $roleClass = 'pill-navy'; }
                                    elseif($user->role == 'supplier') { $roleClass = 'pill-blue'; }
                                    
                                    $status = strtoupper($user->account_status);
                                    $statusClass = 'pill-green'; // Default is active
                                    
                                    if($user->account_status == 'locked') {
                                        $statusClass = 'pill-red';
                                    } elseif($user->account_status == 'suspended') {
                                        $statusClass = 'pill-amber';
                                    }
                                ?>
                                
                                <tr class="<?php echo $user->account_status != 'active' ? 'flagged-row' : ''; ?>" data-user-id="<?php echo $user->id; ?>">
                                    <td>
                                        <div class="cell-user">
                                            <div class="cell-avatar user-avatar-indigo"><?php echo $initial; ?></div>
                                            <div>
                                                <div class="cell-name"><?php echo htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8'); ?></div>
                                                <div class="cell-sub"><?php echo htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8'); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="pill <?php echo $roleClass; ?>"><?php echo strtoupper($user->role); ?></span></td>
                                    <td><span class="pill <?php echo $statusClass; ?>"><span class="pill-dot"></span><?php echo $status; ?></span></td>
                                    <td><?php echo $user->last_login_at ? date('Y-m-d H:i', strtotime($user->last_login_at)) : 'Never'; ?></td>
                                    <td><button type="button" class="btn btn-outline-navy btn-sm edit-managed-user" data-id="<?php echo (int)$user->id; ?>" data-role="<?php echo htmlspecialchars($user->role, ENT_QUOTES, 'UTF-8'); ?>" data-status="<?php echo htmlspecialchars($user->account_status, ENT_QUOTES, 'UTF-8'); ?>" data-name="<?php echo htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8'); ?>">Edit</button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td colspan="5" class="users-empty-cell">No users found in database.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="table-footer">
                    <span class="table-footer-info" id="table-footer-info">
                        Showing <?php echo ($data['total_users'] > 0) ? $data['offset'] + 1 : 0; ?> to 
                        <?php echo min($data['offset'] + $data['limit'], $data['total_users']); ?> 
                        of <?php echo $data['total_users']; ?> entries
                    </span>
                    
                    <div class="pagination" id="pagination-container">
                        <?php if($data['current_page'] > 1) : ?>
                            <a href="?<?php echo htmlspecialchars(http_build_query(['q'=>$data['search'],'role'=>$data['role_filter'],'status'=>$data['status_filter'],'page'=>$data['current_page'] - 1]), ENT_QUOTES, 'UTF-8'); ?>" class="users-pagination-link">&lsaquo;</a>
                        <?php endif; ?>

                        <?php for($i = 1; $i <= $data['total_pages']; $i++) : ?>
                            <a href="?<?php echo htmlspecialchars(http_build_query(['q'=>$data['search'],'role'=>$data['role_filter'],'status'=>$data['status_filter'],'page'=>$i]), ENT_QUOTES, 'UTF-8'); ?>" class="users-page-number <?php echo ($i == $data['current_page']) ? 'active' : 'inactive'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if($data['current_page'] < $data['total_pages']) : ?>
                            <a href="?<?php echo htmlspecialchars(http_build_query(['q'=>$data['search'],'role'=>$data['role_filter'],'status'=>$data['status_filter'],'page'=>$data['current_page'] + 1]), ENT_QUOTES, 'UTF-8'); ?>" class="users-pagination-link">&rsaquo;</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div class="modal-overlay users-modal-overlay" id="manage-user-modal">
      <div class="modal-card users-modal-edit">
        <h3 id="managed-user-heading">Edit User</h3>
        <form method="POST" action="<?php echo URLROOT; ?>/admin/manageUser" id="manage-user-form">
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['user_manage_csrf'],ENT_QUOTES,'UTF-8'); ?>">
          <input type="hidden" name="id" id="manage-user-id"><input type="hidden" name="action" id="manage-user-action" value="update">
          <div class="form-field-group"><label>Role</label><select name="role" id="manage-user-role" required><option value="customer">Customer</option><option value="supplier">Supplier</option><option value="admin">Admin</option></select></div>
          <div class="form-field-group"><label>Status</label><select name="status" id="manage-user-status" required><option value="active">Active</option><option value="suspended">Suspended</option><option value="locked">Locked</option><option value="pending">Pending</option></select></div>
          <p id="manage-user-note" class="users-manage-note"></p>
          <div class="modal-actions"><button type="button" class="btn btn-outline-navy btn-sm" id="close-manage-user">Cancel</button><button type="submit" class="btn btn-navy btn-sm">Save Changes</button><button type="button" class="btn btn-reject btn-sm" id="delete-managed-user">Delete User</button></div>
        </form>
      </div>
    </div>
    <script>
    (function(){
      const modal=document.getElementById('manage-user-modal'),form=document.getElementById('manage-user-form');
      document.querySelectorAll('.edit-managed-user').forEach(btn=>btn.addEventListener('click',()=>{
        document.getElementById('manage-user-id').value=btn.dataset.id;
        document.getElementById('manage-user-role').value=btn.dataset.role;
        document.getElementById('manage-user-status').value=btn.dataset.status;
        document.getElementById('managed-user-heading').textContent='Edit: '+btn.dataset.name;
        const isAdmin=btn.dataset.role==='admin';
        document.getElementById('manage-user-role').disabled=false;
        document.getElementById('delete-managed-user').hidden=isAdmin;
        document.getElementById('manage-user-note').textContent=isAdmin?'Admin accounts can change status, but cannot change role or be deleted.':'Deleting a user is permanent and may be blocked if related records exist.';
        document.getElementById('manage-user-action').value='update';modal.style.display='flex';
      }));
      document.getElementById('close-manage-user').onclick=()=>modal.style.display='none';
      modal.addEventListener('click',e=>{if(e.target===modal)modal.style.display='none'});
      document.getElementById('delete-managed-user').onclick=()=>{if(confirm('Permanently delete this account? This cannot be undone.')){document.getElementById('manage-user-action').value='delete';form.submit()}};
    })();
    </script>
    <!-- Add Admin User Modal -->
    <div class="modal-overlay users-modal-overlay" id="add-admin-modal">
        <div class="modal-card users-modal-admin">
            <div class="modal-header">
                <h3>Add Admin User</h3>
                <button class="modal-close-btn" type="button" id="closeAdminModal">&#x2715;</button>
            </div>

            <?php if (!empty($data['modal_error'])): ?>
            <div class="users-modal-error">
                <?php echo htmlspecialchars($data['modal_error']); ?>
            </div>
            <?php endif; ?>

            <p class="users-modal-desc">
                This creates a new admin row in the users table.
                If the email already exists as a customer or supplier, a separate admin account will be added for it.
            </p>

            <form method="POST" action="<?php echo URLROOT; ?>/admin/addAdmin" id="add-admin-form">

                <div class="form-field-group">
                    <label>Full Name <span class="users-req-star">*</span></label>
                    <input type="text" name="full_name"
                           placeholder="Jane Doe"
                           value="<?php echo htmlspecialchars($_SESSION['admin_form_name'] ?? ''); ?>"
                           required autocomplete="name" />
                </div>

                <div class="form-field-group">
                    <label>Email Address <span class="users-req-star">*</span></label>
                    <input type="email" name="email"
                           placeholder="jane@budgetpilot.com"
                           value="<?php echo htmlspecialchars($_SESSION['admin_form_email'] ?? ''); ?>"
                           required autocomplete="email" />
                    <p class="users-help-text">
                        If this email already has a customer or supplier account, a <em>new admin row</em> is created alongside it.
                    </p>
                </div>

                <div class="form-field-group">
                    <label>Password <span class="users-req-star">*</span></label>
                    <div class="users-pw-wrapper">
                        <input type="password" name="password" id="adminPw"
                               placeholder="Min. 8 characters"
                               required autocomplete="new-password"
                               class="users-pw-input" />
                        <button type="button" id="toggleAdminPw"
                                class="users-pw-toggle"
                                aria-label="Show password">
                        </button>
                    </div>
                </div>

                <div class="form-field-group">
                    <label>Confirm Password <span class="users-req-star">*</span></label>
                    <div class="users-pw-wrapper">
                        <input type="password" name="password_confirm" id="adminPwConfirm"
                               placeholder="Re-enter password"
                               required autocomplete="new-password"
                               class="users-pw-input" />
                        <button type="button" id="toggleAdminPwConfirm"
                                class="users-pw-toggle"
                                aria-label="Show confirm password">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3.8-6.5 10-6.5S22 12 22 12s-3.8 6.5-10 6.5S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    <p id="pwMatchMsg" class="users-match-msg"></p>
                </div>

                <div class="modal-actions users-modal-actions">
                    <button type="button" class="btn btn-reject btn-sm" id="cancelAdminModal">Cancel</button>
                    <button type="submit" class="btn btn-navy btn-sm">Create Admin Account</button>
                </div>
            </form>
        <script src="<?php echo URLROOT; ?>/public/js/admin.js?v=<?php echo filemtime(APPROOT . '/../public/js/admin.js'); ?>"></script>
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
            matchMsg.textContent   = ok ? ' Passwords match' : ' Passwords do not match';
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

<?php require APPROOT . '/Views/shared/footer.php'; ?>
