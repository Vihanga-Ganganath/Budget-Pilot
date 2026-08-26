<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Budget Pilot - Brand Overview</title>
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
      <a class="active" href="<?php echo URLROOT; ?>/supplier/overview">▦ &nbsp; Brand Overview</a>
      <a href="<?php echo URLROOT; ?>/supplier/catalog">▤ &nbsp; Product Catalog</a>
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
    <h1 class="page-title">Brand Overview</h1>
    <p class="subtitle">Manage your market presence and catalog health.</p>

    <!-- Registered Brands -->
    <div class="card" style="margin-bottom:22px">
      <div class="toolbar">
        <div>
          <h2 class="section-title">Registered Brands</h2>
          <div class="section-sub">22 brands available in the product system</div>
        </div>
        <span class="pill green">● ACTIVE</span>
      </div>
      <div id="brandList" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px;margin-top:18px"></div>
    </div>

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
          <div class="stat-value">1,402</div>
          <div class="trend">◉ Stable performance</div>
        </div>
      </div>
      <div class="card stat">
        <div class="iconbox blue">▣</div>
        <div>
          <div class="stat-label">Pending Verifications</div>
          <div class="stat-value">24</div>
          <div class="trend" style="color:#e53935">! 5 urgent reviews</div>
        </div>
      </div>
    </div>

    <!-- Charts & Activities -->
    <div class="grid2" style="margin-top:22px">
      <div class="card">
        <div class="toolbar">
          <div>
            <h2 class="section-title">Brand Popularity</h2>
            <div class="section-sub">Performance vs. Market Benchmark (Last 6 Months)</div>
          </div>
          <div class="legend">
            <span><i class="dot"></i>Your Brand</span>
            <span><i class="dot" style="background:#c6c7d3"></i>Market Avg</span>
          </div>
        </div>
        <div class="chart" id="chart"></div>
      </div>
      <div class="card">
        <h2 class="section-title">Recent Activities</h2>
        <div style="margin-top:22px">
          <b style="color:#17752b">● &nbsp; Organic Produce Updated</b>
          <p class="small">12 new SKUs added to 'Seasonal Fruits'</p>
          <hr>
          <b style="color:var(--navy)">● &nbsp; Freshness Certification</b>
          <p class="small">'Dairy Essentials' line received USDA Organic Badge</p>
          <hr>
          <b style="color:#0b3154">● &nbsp; Inventory Restock</b>
          <p class="small">Inventory restocked for 'Premium Whole Milk'</p>
          <button class="btn" id="activities" style="width:100%;margin-top:10px">View All Activities</button>
        </div>
      </div>
    </div>

    <!-- AI Insights -->
    <div class="grid2" style="margin-top:22px">
      <div class="card" style="background:var(--navy2);color:#fff">
        <h2 style="margin-top:0">AI Insights</h2>
        <p>Your brand reach is 2.4x higher than competitors in the 'Sustainable' niche. Consider expanding your eco-friendly catalog.</p>
        <button class="btn green" id="insights">Explore Opportunities</button>
      </div>
      <button class="card insight-widget" id="addInsight" type="button">
        <span class="insight-plus">⊕</span>
        <strong>Add Insight Widget</strong>
        <span class="small">Customize your dashboard by adding custom data streams or market alerts.</span>
      </button>
    </div>
  </main>
</div>

<div id="toast" class="toast"></div>
<div id="modalRoot"></div>
<script>
  // Pass URLROOT to JS
  const URLROOT = '<?php echo URLROOT; ?>';
</script>
<script src="<?php echo URLROOT; ?>/js/supplier/overview.js"></script>
</body>
</html>
