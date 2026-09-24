<?php 
  $currentURL = $_SERVER['REQUEST_URI']; 
?>
<style>
/* Admin sidebar brand — inline to guarantee no caching issues */
.sidebar__brand {
    display: flex !important;
    align-items: center !important;
    gap: 11px !important;
    padding: 4px 8px 26px !important;
    text-decoration: none !important;
    margin-bottom: 0 !important;
}
.sidebar__mark {
    width: 38px !important;
    height: 38px !important;
    min-width: 38px !important;
    min-height: 38px !important;
    max-width: 38px !important;
    max-height: 38px !important;
    flex: none !important;
    border-radius: 9px !important;
    background: #ffffff !important;
    border: 1px solid #E6E9F0 !important;
    overflow: hidden !important;
    display: grid !important;
    place-items: center !important;
}
.sidebar__mark img {
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
    padding: 3px !important;
    display: block !important;
    max-width: 38px !important;
    max-height: 38px !important;
}
.sidebar__brandtext {
    display: flex !important;
    flex-direction: column !important;
    line-height: 1.25 !important;
}
.sidebar__name {
    font-family: 'Outfit', 'Poppins', sans-serif !important;
    font-weight: 700 !important;
    font-size: 1.05rem !important;
    color: #101C56 !important;
    letter-spacing: -.01em !important;
}
.sidebar__tag {
    font-size: .6rem !important;
    letter-spacing: 1.1px !important;
    text-transform: uppercase !important;
    color: #8A92A0 !important;
    font-weight: 600 !important;
}
/* Also remove any leftover sidebar-logo margin that could push things around */
.sidebar-logo { display: none !important; }
</style>

<aside class="sidebar">
    <!-- 1. Logo Section — matches customer sidebar__brand exactly -->
    <a href="<?php echo URLROOT; ?>/admin/dashboard" class="sidebar__brand">
        <span class="sidebar__mark" aria-hidden="true">
            <img src="<?php echo URLROOT; ?>/public/assets/logos/budget-pilot-mark.png" alt="">
        </span>
        <span class="sidebar__brandtext">
            <span class="sidebar__name">Budget Pilot</span>
            <span class="sidebar__tag">Admin Console</span>
        </span>
    </a>

    <!-- 2. Main Navigation Menu -->
    <nav class="sidebar-menu">
        
        <!-- Dashboard -->
        <a href="<?php echo URLROOT; ?>/admin/dashboard" class="menu-item <?php echo strpos($currentURL, 'dashboard') !== false ? 'active' : ''; ?>">
            <svg class="menu-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            Dashboard
        </a>

        <!-- User Management -->
        <a href="<?php echo URLROOT; ?>/admin/users" class="menu-item <?php echo strpos($currentURL, 'users') !== false ? 'active' : ''; ?>">
            <svg class="menu-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            User Management
        </a>

        <!-- Supplier Approvals -->
        <a href="<?php echo URLROOT; ?>/admin/suppliers" class="menu-item <?php echo strpos($currentURL, 'suppliers') !== false ? 'active' : ''; ?>">
            <svg class="menu-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            Supplier Approvals
        </a>

        <!-- Product Moderation -->
        <a href="<?php echo URLROOT; ?>/admin/products" class="menu-item <?php echo strpos($currentURL, 'products') !== false ? 'active' : ''; ?>">
            <svg class="menu-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
            Product Moderation
        </a>

        <!-- Notice Management -->
        <a href="<?php echo URLROOT; ?>/admin/notices" class="menu-item <?php echo strpos($currentURL, 'notices') !== false ? 'active' : ''; ?>">
            <svg class="menu-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path><path d="M10 21h4"></path></svg>
            Notice Management
        </a>

        <!-- Security & Audit -->
        <a href="<?php echo URLROOT; ?>/admin/security" class="menu-item <?php echo strpos($currentURL, 'security') !== false ? 'active' : ''; ?>">
            <svg class="menu-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            Security &amp; Audit
        </a>

        <!-- Reports -->
        <a href="<?php echo URLROOT; ?>/admin/reports" class="menu-item <?php echo strpos($currentURL, 'reports') !== false ? 'active' : ''; ?>">
            <svg class="menu-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
            Reports
        </a>

    </nav>

    <!-- 3. Bottom Section (Settings & Logout) -->
    <div class="sidebar-bottom">
        <a href="<?php echo URLROOT; ?>/admin/settings" class="menu-item <?php echo strpos($currentURL, 'settings') !== false ? 'active' : ''; ?>">
            <svg class="menu-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            Settings
        </a>
        
        <a href="<?php echo URLROOT; ?>/admin/logout" class="menu-item logout-item" id="admin-logout-link">
            <svg class="menu-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            Logout
        </a>
    </div>
</aside>