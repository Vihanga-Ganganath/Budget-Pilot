<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings &amp; Configuration - Budget Pilot Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/common.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin.css">
    <style>
        /* ── Avatar upload UI ───────────────────────────────────────── */
        .avatar-zone {
            display: flex;
            align-items: center;
            gap: 22px;
            margin-bottom: 24px;
        }
        .avatar-wrap {
            position: relative;
            width: 88px;
            height: 88px;
            flex: none;
        }
        .avatar-wrap img,
        .avatar-wrap .avatar-initials {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #E8EAFF;
        }
        .avatar-wrap .avatar-initials {
            display: grid;
            place-items: center;
            background: #E8EAFF;
            color: #101C56;
            font-size: 2rem;
            font-weight: 700;
            border: 3px solid #E8EAFF;
        }
        /* Hidden real file input */
        #avatarFileInput { display: none; }

        /* Drop zone for drag-and-drop */
        .avatar-drop-zone {
            border: 2px dashed #CBD5E1;
            border-radius: 10px;
            padding: 14px 20px;
            text-align: center;
            cursor: pointer;
            transition: border-color .18s, background .18s;
            font-size: .88rem;
            color: #6B7280;
        }
        .avatar-drop-zone:hover,
        .avatar-drop-zone.drag-over { border-color: #101C56; background: #F0F2FF; }
        .avatar-drop-zone strong { display: block; font-size: .95rem; color: #111827; margin-bottom: 3px; }

        .avatar-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 12px; }

        /* Preview overlay */
        #avatarPreviewOverlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.55);
            z-index: 9000;
            align-items: center;
            justify-content: center;
        }
        #avatarPreviewOverlay.show { display: flex; }
        .preview-card {
            background: #fff;
            border-radius: 14px;
            padding: 28px;
            max-width: 380px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,.2);
        }
        .preview-card img {
            width: 140px; height: 140px;
            border-radius: 50%; object-fit: cover;
            border: 4px solid #E8EAFF;
            margin-bottom: 18px;
        }
        .preview-card h4 { font-size: 1.05rem; margin-bottom: 6px; }
        .preview-card p  { font-size: .85rem; color: #6B7280; margin-bottom: 22px; }
        .preview-actions { display: flex; gap: 12px; justify-content: center; }
    </style>
</head>
<body data-page="settings">

    <div class="dashboard-layout">
        <?php require '../app/Views/admin/_sidebar.php'; ?>

        <main class="main-content">
            <?php require '../app/Views/admin/_topbar.php'; ?>

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
                <button onclick="this.parentElement.remove()" style="float:right;background:none;border:0;cursor:pointer;font-size:1.1rem;color:<?php echo $ftx;?>;line-height:1;">×</button>
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

                        <!-- ── Avatar zone ─────────────────────────────────── -->
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
                                <div class="avatar-drop-zone" id="avatarDropZone">
                                    <strong>Drag &amp; drop a photo here</strong>
                                    or <a href="#" id="avatarBrowseLink" style="color:#101C56;font-weight:600;">browse files</a>
                                    <br><span style="font-size:.78rem;">JPEG, PNG, GIF, WebP — max 3 MB</span>
                                </div>
                                <div class="avatar-actions">
                                    <button type="button" class="btn btn-outline-navy btn-sm" id="changePhotoBtn">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:5px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
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
                        <input type="file" id="avatarFileInput" name="avatar"
                               accept="image/jpeg,image/png,image/gif,image/webp">

                        <!-- Profile fields (display only — real edit TBD) -->
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
                            <input type="password" id="current-password-input" placeholder="••••••••">
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
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
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
                        <div class="toggle-row">
                            <div class="toggle-row-text">
                                <div class="toggle-title">Dark Mode</div>
                                <div class="toggle-sub">Applies across the whole admin console.</div>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="dark-mode-toggle">
                                <span class="switch-slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <h3 style="margin-bottom:16px;color:#DC2626;">Data &amp; Privacy</h3>
                        <button class="danger-zone-btn export" id="export-data-btn" type="button">⬇ Export Personal Data</button>
                        <p class="danger-zone-note">Once deleted, account data cannot be recovered.</p>
                        <button class="danger-zone-btn delete" id="delete-account-btn" type="button">🗑 Delete Account</button>
                    </div>
                </div>
            </div>

            <div class="admin-footer-note">© <?php echo date('Y'); ?> Budget Pilot. All Systems Operational.</div>
        </main>
    </div>

    <!-- ── Avatar Preview / Confirm overlay ─────────────────────────── -->
    <div id="avatarPreviewOverlay">
        <div class="preview-card">
            <img src="" id="previewImg" alt="Preview">
            <h4>Looks good?</h4>
            <p>Upload this as your new profile picture.</p>
            <div class="preview-actions">
                <button type="button" class="btn btn-reject btn-sm" id="previewCancel">Cancel</button>
                <button type="button" class="btn btn-navy btn-sm" id="previewConfirm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right:5px;"><polyline points="20 6 9 17 4 12"/></svg>
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

    <script src="<?php echo URLROOT; ?>/public/js/admin.js"></script>
    <script>
    (function () {
        var dropZone       = document.getElementById('avatarDropZone');
        var browseLink     = document.getElementById('avatarBrowseLink');
        var changeBtn      = document.getElementById('changePhotoBtn');
        var fileInput      = document.getElementById('avatarFileInput');
        var hiddenInput    = document.getElementById('hiddenAvatarInput');
        var uploadForm     = document.getElementById('avatarUploadForm');
        var overlay        = document.getElementById('avatarPreviewOverlay');
        var previewImg     = document.getElementById('previewImg');
        var previewConfirm = document.getElementById('previewConfirm');
        var previewCancel  = document.getElementById('previewCancel');

        var selectedFile = null;

        /* ── Trigger file picker ───────────────────────────────────── */
        function openPicker() { fileInput.click(); }
        if (changeBtn)   changeBtn.addEventListener('click',   openPicker);
        if (browseLink)  browseLink.addEventListener('click',  function(e){ e.preventDefault(); openPicker(); });
        if (dropZone)    dropZone.addEventListener('click',    openPicker);

        /* ── Drag-and-drop ─────────────────────────────────────────── */
        dropZone.addEventListener('dragover', function (e) {
            e.preventDefault();
            dropZone.classList.add('drag-over');
        });
        dropZone.addEventListener('dragleave', function () {
            dropZone.classList.remove('drag-over');
        });
        dropZone.addEventListener('drop', function (e) {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
            var files = e.dataTransfer.files;
            if (files && files[0]) handleFile(files[0]);
        });

        /* ── File input change ─────────────────────────────────────── */
        fileInput.addEventListener('change', function () {
            if (fileInput.files && fileInput.files[0]) {
                handleFile(fileInput.files[0]);
            }
        });

        /* ── Validate & preview ────────────────────────────────────── */
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

        /* ── Confirm upload ────────────────────────────────────────── */
        previewConfirm.addEventListener('click', function () {
            if (!selectedFile) return;
            // Transfer file to hidden input via DataTransfer
            var dt = new DataTransfer();
            dt.items.add(selectedFile);
            hiddenInput.files = dt.files;
            overlay.classList.remove('show');
            uploadForm.submit();
        });

        /* ── Cancel preview ────────────────────────────────────────── */
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
</body>
</html>
