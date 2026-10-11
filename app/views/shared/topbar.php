<?php
// Budget Pilot - Unified Topbar
$userRole = strtolower($_SESSION['user_role'] ?? 'customer');
$userName = trim($_SESSION['user_name'] ?? 'User');

// Determine home link
$homeLink = URLROOT;
if ($userRole === 'admin') {
    $homeLink .= '/admin/dashboard';
} elseif ($userRole === 'supplier') {
    $homeLink .= '/supplier/overview';
} else {
    $homeLink .= '/customer/index';
}

// Generate initials for avatar
$initials = '';
foreach (preg_split('/\s+/', $userName) as $part) {
    if ($part !== '' && strlen($initials) < 2) {
        $initials .= strtoupper(substr($part, 0, 1));
    }
}
if (!$initials) $initials = strtoupper(substr($userRole, 0, 1));
?>
<header class="appbar">
  <!-- Mobile Menu Toggle -->
  <button class="appbar__menu" type="button" data-nav-toggle aria-controls="sidebar" aria-expanded="false" aria-label="Open menu">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
  </button>

  <!-- Mobile Brand Logo -->
  <a class="appbar__brand" href="<?php echo $homeLink; ?>">
    <img src="<?php echo URLROOT; ?>/public/assets/logos/budget-pilot-mark.png" alt="">
    <span>Budget Pilot</span>
  </a>

 

  <!-- Right Actions -->
  <div class="appbar__actions">
    
    <?php if ($userRole === 'admin'): ?>
      <a class="icon-btn" href="#" aria-label="Information">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
      </a>
      <div style="position: relative;">
          <button class="icon-btn" id="topbarNotifBtn" aria-label="Notifications">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>
          </button>
          <div id="topbarNotifPanel" class="dropdown-panel">
              <div class="dropdown-header">Notifications</div>
              <div class="dropdown-item">No new notifications.</div>
          </div>
      </div>
      <a class="icon-btn" href="<?php echo URLROOT; ?>/admin/settings" aria-label="Settings">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
      </a>
    <?php elseif ($userRole === 'supplier'): ?>
      <a class="icon-btn" href="<?php echo URLROOT; ?>/supplier/overview#supplierNoticesTitle" aria-label="Notices">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>
      </a>
      <a class="icon-btn" href="<?php echo URLROOT; ?>/supplier/catalog" aria-label="Product catalog">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8l-9-5-9 5v8l9 5 9-5z"/><path d="M3 8l9 5 9-5M12 13v8"/></svg>
      </a>
    <?php else: ?>
      <a class="icon-btn" href="<?php echo URLROOT; ?>/customer/notifications" aria-label="Notifications">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>
      </a>
      <a class="icon-btn" href="<?php echo URLROOT; ?>/customer/cart" aria-label="Shopping cart">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1.6"/><circle cx="19" cy="21" r="1.6"/><path d="M2.5 3h2.2l2.6 12.4a1.6 1.6 0 0 0 1.6 1.3h8.9a1.6 1.6 0 0 0 1.6-1.3L21 7H6"/></svg>
      </a>
    <?php endif; ?>

    <!-- Profile Circle & Dropdown -->
    <div class="appbar__profile" id="topbarProfileDropdownBtn">
      <span class="appbar__avatar has-bg" title="<?php echo htmlspecialchars($userName); ?>" aria-hidden="true">
        <?php echo htmlspecialchars($initials); ?>
      </span>
      <div id="topbarProfilePanel" class="dropdown-panel">
          <div class="dropdown-header"><?php echo htmlspecialchars($userName); ?></div>
          <?php if ($userRole === 'admin'): ?>
            <a href="<?php echo URLROOT; ?>/admin/settings" class="dropdown-item">Settings</a>
            <a href="<?php echo URLROOT; ?>/admin/logout" class="dropdown-item" style="color: #C2373C;">Logout</a>
          <?php elseif ($userRole === 'supplier'): ?>
            <a href="<?php echo URLROOT; ?>/supplier/logout" class="dropdown-item" style="color: #C2373C;">Logout</a>
          <?php else: ?>
            <a href="<?php echo URLROOT; ?>/customer/settings" class="dropdown-item">Settings</a>
            <a href="<?php echo URLROOT; ?>/customer/logout" class="dropdown-item" style="color: #C2373C;">Logout</a>
          <?php endif; ?>
      </div>
    </div>
  </div>
</header>

