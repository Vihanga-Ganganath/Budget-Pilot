<?php 
$currentURL = $_SERVER['REQUEST_URI'];
$userRole = strtolower($_SESSION['user_role'] ?? 'customer');

if ($userRole === 'admin') {
    $subtitle = 'Admin Console';
    $homeLink = URLROOT . '/admin/dashboard';
} elseif ($userRole === 'supplier') {
    $subtitle = 'Supplier Portal';
    $homeLink = URLROOT . '/supplier/overview';
} else {
    $subtitle = 'Smart Finance Copilot';
    $homeLink = URLROOT . '/customer/index';
}
?>

<aside class="sidebar" id="sidebar" aria-label="Main menu">
    <div class="sidebar__head">
        <a class="sidebar__brand" href="<?php echo $homeLink; ?>">
            <span class="sidebar__mark" aria-hidden="true">
                <img src="<?php echo URLROOT; ?>/public/assets/logos/budget-pilot-mark.png" alt="">
            </span>
            <span class="sidebar__brandtext">
                <span class="sidebar__name">Budget Pilot</span>
                <span class="sidebar__tag"><?php echo $subtitle; ?></span>
            </span>
        </a>
        <button class="sidebar__close" type="button" data-nav-close aria-label="Close menu">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                <path d="M6 6l12 12M18 6L6 18"/>
            </svg>
        </button>
    </div>

    <nav class="sidebar__nav" aria-label="Sections">
        
        <?php if ($userRole === 'admin'): ?>
            <a href="<?php echo URLROOT; ?>/admin/dashboard" class="navlink <?php echo strpos($currentURL, 'dashboard') !== false ? 'active' : ''; ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                Dashboard
            </a>
            <a href="<?php echo URLROOT; ?>/admin/users" class="navlink <?php echo strpos($currentURL, 'users') !== false ? 'active' : ''; ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                User Management
            </a>
            <a href="<?php echo URLROOT; ?>/admin/suppliers" class="navlink <?php echo strpos($currentURL, 'suppliers') !== false ? 'active' : ''; ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                Supplier Approvals
            </a>
            <a href="<?php echo URLROOT; ?>/admin/products" class="navlink <?php echo strpos($currentURL, 'products') !== false ? 'active' : ''; ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                Product Moderation
            </a>
            <a href="<?php echo URLROOT; ?>/admin/notices" class="navlink <?php echo strpos($currentURL, 'notices') !== false ? 'active' : ''; ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path><path d="M10 21h4"></path></svg>
                Notice Management
            </a>
            <a href="<?php echo URLROOT; ?>/admin/security" class="navlink <?php echo strpos($currentURL, 'security') !== false ? 'active' : ''; ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                Security &amp; Audit
            </a>
            <a href="<?php echo URLROOT; ?>/admin/reports" class="navlink <?php echo strpos($currentURL, 'reports') !== false ? 'active' : ''; ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                Reports
            </a>
        
        <?php elseif ($userRole === 'supplier'): ?>
            <a href="<?php echo URLROOT; ?>/supplier/overview" class="navlink <?php echo strpos($currentURL, 'overview') !== false ? 'active' : ''; ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.6"/><rect x="14" y="3" width="7" height="7" rx="1.6"/><rect x="3" y="14" width="7" height="7" rx="1.6"/><rect x="14" y="14" width="7" height="7" rx="1.6"/></svg>
                Brand Overview
            </a>
            <a href="<?php echo URLROOT; ?>/supplier/catalog" class="navlink <?php echo strpos($currentURL, 'catalog') !== false || strpos($currentURL, 'edit-product') !== false ? 'active' : ''; ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18l-1.5 11H4.5z"/><path d="M3 9l1.5-4h15L21 9"/></svg>
                Product Catalog
            </a>
            <a href="<?php echo URLROOT; ?>/supplier/addProduct" class="navlink <?php echo strpos($currentURL, 'addProduct') !== false ? 'active' : ''; ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 8.5v7M8.5 12h7"/></svg>
                Add Product
            </a>
            <a href="<?php echo URLROOT; ?>/supplier/analytics" class="navlink <?php echo strpos($currentURL, 'analytics') !== false ? 'active' : ''; ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l5-6 4 3 5-7 4 4"/></svg>
                Analytics &amp; Trends
            </a>
            <a href="<?php echo URLROOT; ?>/supplier/verification" class="navlink <?php echo strpos($currentURL, 'verification') !== false ? 'active' : ''; ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7.5 3v5.5c0 4.6-3.2 8.3-7.5 9.5-4.3-1.2-7.5-4.9-7.5-9.5V6z"/><path d="M8.8 12.2l2.2 2.2 4.3-4.4"/></svg>
                Verification Status
            </a>
            <a href="<?php echo URLROOT; ?>/supplier/promotions" class="navlink <?php echo strpos($currentURL, 'promotions') !== false ? 'active' : ''; ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                Promotions
            </a>
        
        <?php else: ?>
            <a href="<?php echo URLROOT; ?>/customer/index" class="navlink <?php echo strpos($currentURL, 'index') !== false ? 'active' : ''; ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5L12 3l9 7.5"/><path d="M5.5 9.5V20h13V9.5"/><path d="M9.5 20v-6h5v6"/></svg>
                Home
            </a>
            <a href="<?php echo URLROOT; ?>/customer/dashboard" class="navlink <?php echo strpos($currentURL, 'dashboard') !== false ? 'active' : ''; ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.6"/><rect x="14" y="3" width="7" height="7" rx="1.6"/><rect x="3" y="14" width="7" height="7" rx="1.6"/><rect x="14" y="14" width="7" height="7" rx="1.6"/></svg>
                Dashboard
            </a>
                        <a href="<?php echo URLROOT; ?>/customer/addExpense" class="navlink <?php echo strpos($currentURL, 'addExpense') !== false ? 'active' : ''; ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 8.5v7M8.5 12h7"/></svg>
                Add Expense
            </a>
            <a href="<?php echo URLROOT; ?>/customer/budgets" class="navlink <?php echo strpos($currentURL, 'budgets') !== false ? 'active' : ''; ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="5" width="19" height="14" rx="3"/><path d="M2.5 10h19"/></svg>
                Budgets
            </a>
            <a href="<?php echo URLROOT; ?>/customer/grocery" class="navlink <?php echo strpos($currentURL, 'grocery') !== false ? 'active' : ''; ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18l-1.5 11H4.5z"/><path d="M3 9l1.5-4h15L21 9"/></svg>
                Grocery Catalog
            </a>
            <a href="<?php echo URLROOT; ?>/customer/cart" class="navlink <?php echo strpos($currentURL, 'cart') !== false ? 'active' : ''; ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1.5"/><circle cx="18" cy="21" r="1.5"/><path d="M2.5 3h2.2l2.6 12.4a1.6 1.6 0 0 0 1.6 1.3h8.9a1.6 1.6 0 0 0 1.6-1.3L21 7H6"/></svg>
                Shopping Cart
            </a>
            <a href="<?php echo URLROOT; ?>/customer/analytics" class="navlink <?php echo strpos($currentURL, 'analytics') !== false ? 'active' : ''; ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l5-6 4 3 5-7 4 4"/></svg>
                Analytics
            </a>
            <a href="<?php echo URLROOT; ?>/customer/household" class="navlink <?php echo strpos($currentURL, 'household') !== false ? 'active' : ''; ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.2"/><path d="M2.5 20c.6-3.6 3.2-5.5 6.5-5.5s5.9 1.9 6.5 5.5"/><path d="M16.5 5.2a3.2 3.2 0 0 1 0 6"/><path d="M18 14.8c2 .7 3.2 2.4 3.5 5.2"/></svg>
                Household
            </a>
        <?php endif; ?>

    </nav>

    <div class="sidebar__foot">
        <?php if ($userRole === 'supplier'): ?>
            <a href="<?php echo URLROOT; ?>/supplier/logout" class="navlink logout-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/></svg>
                Logout
            </a>
        <?php else: ?>
            <a href="<?php echo URLROOT; ?>/<?php echo $userRole === 'admin' ? 'admin' : 'customer'; ?>/settings" class="navlink <?php echo strpos($currentURL, 'settings') !== false ? 'active' : ''; ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3.2"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-2.9 1.2v.2a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-3-1.2l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0-1.2-2.9H3a2 2 0 1 1 0-4h.1A1.7 1.7 0 0 0 4.3 6l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 2.9-1.2V2a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 3 1.2l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0 1.2 2.9H22a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/></svg>
                Settings
            </a>
            <a href="<?php echo URLROOT; ?>/<?php echo $userRole === 'admin' ? 'admin' : 'customer'; ?>/logout" class="navlink logout-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/></svg>
                Logout
            </a>
        <?php endif; ?>
    </div>
</aside>



