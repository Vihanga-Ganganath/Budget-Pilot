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
</head>
<body data-page="settings">

    <div class="dashboard-layout">
        <div id="sidebar-mount" data-active="settings"></div>

        <main class="main-content">
            <div id="topbar-mount" data-variant="settings"></div>

            <header class="content-header">
                <div class="header-left">
                    <h2 class="page-title">Settings &amp; Configuration</h2>
                    <p class="page-subtitle">Manage your account settings, security preferences, and administrative configurations.</p>
                </div>
            </header>

            <div class="settings-columns">
                <!-- Left column -->
                <div class="settings-stack">
                    <div class="dashboard-card">
                        <h3 style="margin-bottom:4px;">Profile Information</h3>
                        <p class="page-subtitle" style="margin-bottom:20px; font-size:0.85rem;">Update your basic profile details and administrative role.</p>

                        <div class="profile-photo-row">
                            <img class="profile-photo-lg" src="https://i.pravatar.cc/150?img=47" alt="Profile photo">
                            <button class="btn btn-outline-navy btn-sm" type="button" onclick="toast('Photo upload is a demo action.')">Change Photo</button>
                        </div>

                        <div class="form-row-2">
                            <div class="form-field-group">
                                <label>Full Name</label>
                                <input type="text" id="profile-name-input" value="Sarah Jenkins">
                            </div>
                            <div class="form-field-group">
                                <label>Role / Title</label>
                                <input type="text" value="System Administrator" disabled>
                            </div>
                        </div>
                        <div class="form-field-group">
                            <label>Email Address</label>
                            <input type="email" id="profile-email-input" value="sarah.jenkins@budgetpilot.com">
                        </div>

                        <button class="btn btn-navy btn-sm" id="save-profile-btn" type="button">Save Profile</button>
                    </div>

                    <div class="dashboard-card">
                        <h3 style="margin-bottom:4px;">Security Settings</h3>
                        <p class="page-subtitle" style="margin-bottom:20px; font-size:0.85rem;">Manage your password and authentication methods.</p>

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
                        <h3 style="margin-bottom:16px; color:#DC2626;">Data &amp; Privacy</h3>
                        <button class="danger-zone-btn export" id="export-data-btn" type="button">⬇ Export Personal Data</button>
                        <p class="danger-zone-note">Once deleted, account data cannot be recovered.</p>
                        <button class="danger-zone-btn delete" id="delete-account-btn" type="button">🗑 Delete Account</button>
                    </div>
                </div>
            </div>

            <div class="admin-footer-note">© 2026 Budget Pilot. All Systems Operational.</div>
        </main>
    </div>

    <script src="<?php echo URLROOT; ?>/public/js/admin.js"></script>
</body>
</html>
