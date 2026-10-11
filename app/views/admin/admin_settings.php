
<?php require APPROOT . '/Views/shared/header.php'; ?>
<header class="content-header">
                <div class="header-left">
                    <h2 class="page-title">Settings &amp; Configuration</h2>
                    <p class="page-subtitle">Manage your account settings, security preferences, and administrative configurations.</p>
                </div>
            </header>

            <?php if (!empty($data['flash'])): ?>
            <?php
                $ft  = $data['flash_type'] ?? 'success';
                $fbg = $ft === 'success' ? '#E8F6EE' : ($ft === 'warning' ? '#FEF9E8' : '#FDF6F6');
                $fbd = $ft === 'success' ? '#BDE6CE' : ($ft === 'warning' ? '#F5D87A' : '#F0BCBC');
                $ftx = $ft === 'success' ? '#1B6B42' : ($ft === 'warning' ? '#7A5C00' : '#C2373C');
            ?>
            <div style="background:<?php echo $fbg;?>;border:1px solid <?php echo $fbd;?>;color:<?php echo $ftx;?>;border-radius:9px;padding:12px 16px;margin-bottom:20px;font-size:.9rem;font-weight:500;">
                <?php echo htmlspecialchars($data['flash']); ?>
                <button onclick="this.parentElement.remove()" style="float:right;background:none;border:0;cursor:pointer;font-size:1.1rem;color:<?php echo $ftx;?>;line-height:1;"></button>
            </div>
            <?php endif; ?>

            <?php
                // Resolve avatar URL
                $avatarFile = $data['avatar'] ?? null;
                $avatarUrl  = $avatarFile
                    ? URLROOT . '/public/assets/avatars/admin/' . htmlspecialchars($avatarFile)
                    : null;
                $adminName  = $data['profile']->name ?? ($_SESSION['user_name'] ?? 'Admin');
                $adminEmail = $data['profile']->email ?? ($_SESSION['user_email'] ?? '');
                $initials   = mb_strtoupper(mb_substr($adminName, 0, 1));
            ?>

            <div class="settings-columns">
                <!-- Left column -->
                <div class="settings-stack">
                    <div class="dashboard-card">
                        <h3 style="margin-bottom:4px;">Profile Information</h3>
                        <p class="page-subtitle" style="margin-bottom:20px;font-size:.85rem;">Update your basic profile details and profile picture.</p>

                        <!--  Avatar zone  -->
                        <div class="avatar-zone">
                            <!-- Current avatar or initials fallback -->
                            <div class="avatar-wrap" id="currentAvatarWrap">
                                <?php if ($avatarUrl): ?>
                                    <img src="<?php echo $avatarUrl; ?>?v=<?php echo time(); ?>"
                                         alt="Profile photo" id="currentAvatarImg">
                                <?php else: ?>
                                    <div class="avatar-initials" id="currentAvatarInitials"><?php echo $initials; ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Drop zone + buttons -->
                            <div style="flex:1;">
                                
                                <div class="avatar-actions">
                                    <button type="button" class="btn btn-outline-navy btn-sm" id="changePhotoBtn">
                                        
                                        Upload Photo
                                    </button>
                                    <?php if ($avatarUrl): ?>
                                    <form method="POST" action="<?php echo URLROOT; ?>/admin/deleteAvatar"
                                          id="deleteAvatarForm"
                                          onsubmit="return confirm('Remove your profile picture?')">
                                        <button type="submit" class="btn btn-reject btn-sm">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:5px;"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                            Remove Photo
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden real file input -->
                        <input style="display:none;" type="file" id="avatarFileInput" name="avatar" accept="image/jpeg,image/png,image/gif,image/webp">

                        <!-- Profile fields (display only  real edit TBD) -->
                        <div class="form-row-2">
                            <div class="form-field-group">
                                <label>Full Name</label>
                                <input type="text" id="profile-name-input"
                                       value="<?php echo htmlspecialchars($adminName); ?>" disabled>
                            </div>
                            <div class="form-field-group">
                                <label>Role / Title</label>
                                <input type="text" value="System Administrator" disabled>
                            </div>
                        </div>
                        <div class="form-field-group">
                            <label>Email Address</label>
                            <input type="email" id="profile-email-input"
                                   value="<?php echo htmlspecialchars($adminEmail); ?>" disabled>
                        </div>

                        <button class="btn btn-navy btn-sm" id="save-profile-btn" type="button">Save Profile</button>
                    </div>

                    <div class="dashboard-card">
                        <h3 style="margin-bottom:4px;">Security Settings</h3>
                        <p class="page-subtitle" style="margin-bottom:20px;font-size:.85rem;">Manage your password and authentication methods.</p>

                        <div class="form-field-group">
                            <label>Current Password</label>
                            <input type="password" id="current-password-input" placeholder="">
                        </div>
                        <div class="form-row-2">
                            <div class="form-field-group">
                                <label>New Password</label>
                                <input type="password" id="new-password-input" placeholder="New Password">
                            </div>
                            <div class="form-field-group">
                                <label>Confirm New Password</label>
                                <input type="password" id="confirm-password-input" placeholder="Confirm Password">
                            </div>
                        </div>

                        <div class="twofa-box">
                            <div class="twofa-box-title">
                                
                                Two-Factor Authentication
                            </div>
                            <div class="twofa-box-desc">Add an extra layer of security to your account by requiring a verification code upon login.</div>
                            <div class="twofa-status-row">
                                <span class="twofa-status-label">Status: Enabled</span>
                                <label class="switch">
                                    <input type="checkbox" id="twofa-toggle" checked>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <button class="btn btn-navy btn-sm" id="update-security-btn" type="button">Update Security</button>
                    </div>
                </div>

                <!-- Right column -->
                <div class="settings-stack">
                    <div class="dashboard-card">
                        <h3 style="margin-bottom:16px;">Preferences</h3>
                        <div class="toggle-row">
                            <div class="toggle-row-text">
                                <div class="toggle-title">Email Notifications</div>
                                <div class="toggle-sub">Receive daily summary reports.</div>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="email-notif-toggle">
                                <span class="switch-slider"></span>
                            </label>
                        </div>
                        <div class="toggle-row">
                            <div class="toggle-row-text">
                                <div class="toggle-title">System Alerts</div>
                                <div class="toggle-sub">Critical system warnings.</div>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="system-alerts-toggle">
                                <span class="switch-slider"></span>
                            </label>
                        </div>
                        
                    </div>

                    <div class="dashboard-card">
                        <h3 style="margin-bottom:16px;color:#DC2626;">Data &amp; Privacy</h3>
                        <button class="danger-zone-btn export" id="export-data-btn" type="button"> Export Personal Data</button>
                        <p class="danger-zone-note">Once deleted, account data cannot be recovered.</p>
                        <button class="danger-zone-btn delete" id="delete-account-btn" type="button"> Delete Account</button>
                    </div>
                </div>
            </div>

            <div class="admin-footer-note"> <?php echo date('Y'); ?> Budget Pilot. All Systems Operational.</div>
        </main>
    </div>

    <!--  Avatar Preview / Confirm overlay  -->
    <div id="avatarPreviewOverlay">
        <div class="preview-card">
            <img src="" id="previewImg" alt="Preview">
            <h4>Looks good?</h4>
            <p>Upload this as your new profile picture.</p>
            <div class="preview-actions">
                <button type="button" class="btn btn-cancel btn-sm" id="previewCancel">Cancel</button>
                <button type="button" class="btn btn-navy btn-sm" id="previewConfirm">
                    
                    Save Picture
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden upload form (submitted programmatically) -->
    <form method="POST" action="<?php echo URLROOT; ?>/admin/uploadAvatar"
          enctype="multipart/form-data" id="avatarUploadForm" style="display:none;">
        <input type="file" name="avatar" id="hiddenAvatarInput">
    </form>

    <script src="<?php echo URLROOT; ?>/public/js/admin.js?v=<?php echo filemtime(APPROOT . '/../public/js/admin.js'); ?>"></script>
    <script>
    (function () {
        var changeBtn      = document.getElementById('changePhotoBtn');
        var fileInput      = document.getElementById('avatarFileInput');
        var hiddenInput    = document.getElementById('hiddenAvatarInput');
        var uploadForm     = document.getElementById('avatarUploadForm');
        var overlay        = document.getElementById('avatarPreviewOverlay');
        var previewImg     = document.getElementById('previewImg');
        var previewConfirm = document.getElementById('previewConfirm');
        var previewCancel  = document.getElementById('previewCancel');

        var selectedFile = null;

        /*  Trigger file picker  */
        function openPicker() { fileInput.click(); }
        if (changeBtn)   changeBtn.addEventListener('click',   openPicker);
        /*  File input change  */
        fileInput.addEventListener('change', function () {
            if (fileInput.files && fileInput.files[0]) {
                handleFile(fileInput.files[0]);
            }
        });

        /*  Validate & preview  */
        function handleFile(file) {
            var allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowed.includes(file.type)) {
                alert('Only JPEG, PNG, GIF, and WebP images are allowed.');
                return;
            }
            if (file.size > 3 * 1024 * 1024) {
                alert('File is too large. Maximum size is 3 MB.');
                return;
            }
            selectedFile = file;
            var reader   = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                overlay.classList.add('show');
            };
            reader.readAsDataURL(file);
        }

        /*  Confirm upload  */
        previewConfirm.addEventListener('click', function () {
            if (!selectedFile) return;
            // Transfer file to hidden input via DataTransfer
            var dt = new DataTransfer();
            dt.items.add(selectedFile);
            hiddenInput.files = dt.files;
            overlay.classList.remove('show');
            uploadForm.submit();
        });

        /*  Cancel preview  */
        previewCancel.addEventListener('click', function () {
            overlay.classList.remove('show');
            selectedFile = null;
            fileInput.value = '';
        });

        /* Close overlay on backdrop click */
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) previewCancel.click();
        });
    })();
    </script>

<?php require APPROOT . '/Views/shared/footer.php'; ?>










