<?php
/* Supplier side panel — same look as the customer sidebar (fonts, sizes, icons).
   Set before including:
     $spActive     'overview' | 'catalog' | 'analytics' | 'verification'
     $spActiveLink true on a sub-page (e.g. Edit Product) so the active item stays clickable */
$spActive     = $spActive ?? '';
$spActiveLink = $spActiveLink ?? false;

$spIcons = [
  'overview'     => '<rect x="3" y="3" width="7" height="7" rx="1.6"/><rect x="14" y="3" width="7" height="7" rx="1.6"/><rect x="3" y="14" width="7" height="7" rx="1.6"/><rect x="14" y="14" width="7" height="7" rx="1.6"/>',
  'catalog'      => '<path d="M3 9h18l-1.5 11H4.5z"/><path d="M3 9l1.5-4h15L21 9"/>',
  'analytics'    => '<path d="M3 17l5-6 4 3 5-7 4 4"/>',
  'verification' => '<path d="M12 3l7.5 3v5.5c0 4.6-3.2 8.3-7.5 9.5-4.3-1.2-7.5-4.9-7.5-9.5V6z"/><path d="M8.8 12.2l2.2 2.2 4.3-4.4"/>',
];
$spItems = [
  'overview'     => 'Brand Overview',
  'catalog'      => 'Product Catalog',
  'analytics'    => 'Analytics &amp; Trends',
  'verification' => 'Verification Status',
];
$spSvg = function ($paths) {
  return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
};
?>
  <aside class="sidebar" id="sidebar" aria-label="Main menu">
    <div class="sidebar__head">
      <a class="sidebar__brand" href="<?php echo URLROOT; ?>/home">
        <span class="sidebar__mark" aria-hidden="true">
          <img src="<?php echo URLROOT; ?>/public/assets/logos/budget-pilot-mark.png" alt="">
        </span>
        <span class="sidebar__brandtext">
          <span class="sidebar__name">Budget Pilot</span>
          <span class="sidebar__tag">Supplier Portal</span>
        </span>
      </a>
      <button class="sidebar__close" type="button" data-nav-close aria-label="Close menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
      </button>
    </div>

    <nav class="sidebar__nav" aria-label="Sections">
<?php foreach ($spItems as $key => $label): ?>
<?php   if ($key === $spActive && !$spActiveLink): ?>
      <span class="navlink navlink--active" aria-current="page">
        <?php echo $spSvg($spIcons[$key]); ?>
        <?php echo $label; ?>
      </span>
<?php   else: ?>
      <a class="navlink<?php echo $key === $spActive ? ' navlink--active' : ''; ?>" href="<?php echo URLROOT; ?>/supplier/<?php echo $key; ?>">
        <?php echo $spSvg($spIcons[$key]); ?>
        <?php echo $label; ?>
      </a>
<?php   endif; ?>
<?php endforeach; ?>
    </nav>

    <div class="sidebar__foot">
<?php $spAddIcon = $spSvg('<circle cx="12" cy="12" r="9"/><path d="M12 8.5v7M8.5 12h7"/>'); ?>
<?php if ($spActive === 'catalog' && !$spActiveLink): ?>
      <button class="sidebar__add" type="button" id="addProduct" onclick="showAddProductForm()">
        <?php echo $spAddIcon; ?>
        Add New Product
      </button>
<?php else: ?>
      <a class="sidebar__add" id="addProduct" href="<?php echo URLROOT; ?>/supplier/catalog#add">
        <?php echo $spAddIcon; ?>
        Add New Product
      </a>
<?php endif; ?>

      <a class="navlink" href="#" id="help">
        <?php echo $spSvg('<circle cx="12" cy="12" r="9"/><path d="M9.6 9.3a2.5 2.5 0 0 1 4.8.9c0 1.7-2.4 2.3-2.4 3.8"/><path d="M12 17.2h.01"/>'); ?>
        Help Center
      </a>
      <a class="navlink" href="<?php echo URLROOT; ?>/supplier/logout" id="logout">
        <?php echo $spSvg('<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/>'); ?>
        Logout
      </a>
    </div>
  </aside>
