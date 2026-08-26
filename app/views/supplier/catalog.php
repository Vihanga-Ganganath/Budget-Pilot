<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Budget Pilot - Product Catalog</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/supplier/supplier.css">
</head>
<body>

<header class="topbar">
  <div class="logo">Budget Pilot Supplier</div>
  <a class="toplink" href="<?php echo URLROOT; ?>/supplier/overview">Back to Home</a>
  <div class="topicons">
    <span>♧</span>
    <span>⚙</span>
    <span title="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>">◉</span>
  </div>
</header>

<div class="layout">

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="side-title">Budget Pilot</div>
    <div class="side-sub">Supplier Portal</div>
    <nav class="nav">
      <a href="<?php echo URLROOT; ?>/supplier/overview">▦ &nbsp; Brand Overview</a>
      <a class="active" href="<?php echo URLROOT; ?>/supplier/catalog">▤ &nbsp; Product Catalog</a>
      <a href="<?php echo URLROOT; ?>/supplier/analytics">↗ &nbsp; Analytics &amp; Trends</a>
      <a href="<?php echo URLROOT; ?>/supplier/verification">♢ &nbsp; Verification Status</a>
    </nav>
    <div class="bottom">
      <button class="add" id="addProduct">＋ Add New Product</button>
      <div class="bottom-links">
        <a href="#" id="help">ⓘ &nbsp; Help Center</a>
        <a href="<?php echo URLROOT; ?>/supplier/logout" id="logout">⇥ &nbsp; Logout</a>
      </div>
    </div>
  </aside>

  <!-- Main Content -->
  <main>
    <h1 class="page-title">Product Catalog</h1>
    <p class="subtitle">Manage your inventory, pricing, and product visibility across the platform.</p>

    <!-- Stats -->
    <div class="grid3">
      <div class="card stat">
        <div class="iconbox">◉</div>
        <div>
          <div class="stat-label">Total Brand Reach</div>
          <div class="stat-value">128.4K</div>
          <div class="trend">↗ +12% this month</div>
        </div>
      </div>
      <div class="card stat">
        <div class="iconbox green">▤</div>
        <div>
          <div class="stat-label">Active Listings</div>
          <div class="stat-value" id="activeCount">1,402</div>
          <div class="trend">◉ Stable performance</div>
        </div>
      </div>
      <div class="card stat">
        <div class="iconbox blue">☑</div>
        <div>
          <div class="stat-label">Pending Verifications</div>
          <div class="stat-value">24</div>
          <div class="trend" style="color:#e53935">! 5 urgent reviews</div>
        </div>
      </div>
    </div>

    <!-- Inventory Table + Sidebar Cards -->
    <div class="grid2" style="margin-top:22px">
      <div class="card table-card">
        <div class="toolbar">
          <div>
            <h2 class="section-title">
              Inventory Overview
              <span class="pill blue" id="countPill">6 Products</span>
            </h2>
          </div>
          <div class="toolbar-right">
            <input class="input search" id="search" placeholder="Search products...">
            <button class="btn" id="filter">☷ Filters</button>
            <button class="btn" id="export">⇩ Export CSV</button>
          </div>
        </div>
        <table>
          <thead>
            <tr>
              <th>PRODUCT &amp; SKU</th>
              <th>BRAND</th>
              <th>CATEGORY</th>
              <th>PRICE</th>
              <th>STATUS</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="rows"></tbody>
        </table>
      </div>

      <div>
        <div class="card">
          <h2 class="section-title">Top Sellers</h2>
          <p><b>Organic Whole Milk</b><br><span class="small">420 units sold</span><b style="float:right;color:#18752b">+15%</b></p>
          <hr>
          <p><b>Hass Avocados</b><br><span class="small">385 units sold</span><b style="float:right;color:#18752b">+8%</b></p>
        </div>
        <div class="card" style="margin-top:22px;background:var(--navy2);color:#fff">
          <h2 style="margin-top:0">Catalog Health</h2>
          <p>98% of your listings meet the 'Premium' quality standard. 2 items need image updates.</p>
          <button class="btn green" id="fix">Fix Issues</button>
        </div>
        <div class="card" style="margin-top:22px">
          <h2 class="section-title">Market Trends</h2>
          <div style="border:1px solid #ccd1dd;border-radius:9px;padding:14px;margin-bottom:12px">
            <b>Organic Demand</b><b style="float:right;color:#18752b">+24%</b>
            <hr>
            <div style="height:6px;background:#dfe3e8">
              <div style="width:82%;height:100%;background:#18752b"></div>
            </div>
          </div>
          <div style="border:1px solid #ccd1dd;border-radius:9px;padding:14px">
            <b>Bulk Pricing Index</b><b style="float:right;color:var(--navy)">Stable</b>
          </div>
          <button class="btn" id="report" style="width:100%;margin-top:14px">View Full Report →</button>
        </div>
      </div>
    </div>
  </main>
</div>

<div id="toast" class="toast"></div>
<div id="modalRoot"></div>
<script>
  const URLROOT = '<?php echo URLROOT; ?>';
</script>
<script src="<?php echo URLROOT; ?>/js/supplier/catalog.js"></script>
</body>
</html>
