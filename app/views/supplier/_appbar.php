<?php
/* Supplier top bar — same look as the customer app bar. */
$spName = trim($_SESSION['user_name'] ?? '');
$spInitials = '';
foreach (preg_split('/\s+/', $spName) as $part) {
  if ($part !== '' && strlen($spInitials) < 2) $spInitials .= strtoupper(substr($part, 0, 1));
}
?>
    <header class="appbar">
      <button class="appbar__menu" type="button" data-nav-toggle aria-controls="sidebar" aria-expanded="false" aria-label="Open menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
      </button>
      <a class="appbar__brand" href="<?php echo URLROOT; ?>/supplier/overview">
        <img src="<?php echo URLROOT; ?>/public/assets/logos/budget-pilot-mark.png" alt="">
        <span>Budget Pilot</span>
      </a>
      <div class="appbar__actions">
        <a class="icon-btn" href="<?php echo URLROOT; ?>/supplier/overview#supplierNoticesTitle" aria-label="Notices">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>
        </a>
        <a class="icon-btn" href="<?php echo URLROOT; ?>/supplier/catalog" aria-label="Product catalog">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8l-9-5-9 5v8l9 5 9-5z"/><path d="M3 8l9 5 9-5M12 13v8"/></svg>
        </a>
        <span class="appbar__avatar" title="<?php echo htmlspecialchars($spName); ?>" aria-hidden="true"><?php echo htmlspecialchars($spInitials ?: 'S'); ?></span>
      </div>
    </header>
