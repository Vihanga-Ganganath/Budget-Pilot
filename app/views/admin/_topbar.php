<div class="admin-topbar">
    <!-- 1. Search Section -->
    <div class="admin-search-wrapper">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        <input type="text" id="global-search-input" placeholder="Search">
    </div>

    <!-- 2. Right Side Icons & Avatar -->
    <div class="admin-topbar-right" style="position:relative;">
        
        <!-- Help Button & Dropdown -->
        <button class="icon-btn-dash" id="help-btn" title="Help">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
        </button>
        <div id="help-dropdown" class="dropdown-panel" style="display:none;">
            <div class="dropdown-header">Help &amp; Support</div>
            <div class="dropdown-item"><a href="#" id="help-docs">📘 Admin Documentation</a></div>
            <div class="dropdown-item"><a href="#" id="help-contact">✉️ Contact Support</a></div>
            <div class="dropdown-item"><a href="#" id="help-shortcuts">⌨️ Keyboard Shortcuts</a></div>
        </div>

        <!-- Notifications Button & Dropdown -->
        <button class="icon-btn-dash" id="notif-bell-btn" title="Notifications">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
            <span class="alert-dot-badge" id="notif-dot"></span>
        </button>
        <div id="notif-dropdown" class="dropdown-panel" style="display:none;">
            <div class="dropdown-header">Notifications</div>
            <!-- JS එකේ තිබ්බ CSS Classes පාවිච්චි කරලා -->
            <div class="dropdown-item notif-item">
                <div class="notif-item-title">New Supplier Registered</div>
                <div class="notif-item-msg">'MegaMart Co.' is awaiting your approval.</div>
                <div class="notif-item-time">10 mins ago</div>
            </div>
            <div class="dropdown-item notif-item">
                <div class="notif-item-title">Security Alert</div>
                <div class="notif-item-msg">Multiple failed login attempts detected.</div>
                <div class="notif-item-time">1 hour ago</div>
            </div>
            <div class="dropdown-footer"><a href="#" id="mark-all-read">Mark all as read</a></div>
        </div>

        <!-- Quick Settings Button & Dropdown -->
        <button class="icon-btn-dash" id="quick-settings-btn" title="Quick settings">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
        </button>
        <div id="quick-settings-dropdown" class="dropdown-panel" style="display:none;">
            <div class="dropdown-header">Quick Settings</div>
            <!-- JS එකේ තිබ්බ Dark Mode Toggle එක -->
            <div class="dropdown-item dropdown-toggle-item">
                <span>Dark Mode</span>
                <label class="switch switch-sm">
                    <input type="checkbox" id="quick-dark-toggle">
                    <span class="switch-slider"></span>
                </label>
            </div>
            <div class="dropdown-item"><a href="<?php echo URLROOT; ?>/admin/settings">Full Settings →</a></div>
        </div>

        <!-- Avatar Image & Dropdown -->
        <img class="admin-avatar-photo" id="avatar-btn" src="https://i.pravatar.cc/150?img=47" alt="Admin">
        <div id="avatar-dropdown" class="dropdown-panel" style="display:none;">
            <div class="dropdown-header"><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin User'; ?></div>
            <!-- JS එකේ තිබ්බ විදිහටම Emoji සහ ලින්ක් -->
            <div class="dropdown-item"><a href="<?php echo URLROOT; ?>/admin/settings">👤 View Profile</a></div>
            <div class="dropdown-item"><a href="<?php echo URLROOT; ?>/admin/settings">⚙️ Settings</a></div>
            <div class="dropdown-item"><a href="<?php echo URLROOT; ?>/admin/logout" id="dropdown-logout">🚪 Logout</a></div>
        </div>

    </div>
</div>