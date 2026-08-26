<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Budget Pilot - Analytics &amp; Market Trends</title>
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
      <a href="<?php echo URLROOT; ?>/supplier/catalog">▤ &nbsp; Product Catalog</a>
      <a class="active" href="<?php echo URLROOT; ?>/supplier/analytics">↗ &nbsp; Analytics &amp; Trends</a>
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
    <div class="toolbar">
      <div>
        <h1 class="page-title">Analytics &amp; Market Trends</h1>
        <p class="subtitle">Visualizing performance data and competitor pricing strategies for FreshHarvest, PureDairy, and GreenLeaf Grocers.</p>
      </div>
      <button class="btn primary" id="exportReport">▣ Export Quarterly Report</button>
    </div>

    <!-- Stats -->
    <div class="grid3">
      <div class="card stat">
        <div class="iconbox green">▣</div>
        <div>
          <div class="stat-label">Projected Revenue (Sep)</div>
          <div class="stat-value">$42,910</div>
          <div class="trend">75% of Target Achieved</div>
        </div>
      </div>
      <div class="card stat">
        <div class="iconbox">♧</div>
        <div>
          <div class="stat-label">New Supplier Interactions</div>
          <div class="stat-value">2.4k</div>
          <div class="trend">↗ +12.4% vs last year</div>
        </div>
      </div>
      <div class="card stat">
        <div class="iconbox blue">▥</div>
        <div>
          <div class="stat-label">Market Confidence</div>
          <div class="stat-value">High</div>
          <div class="trend">✥ Category Leader</div>
        </div>
      </div>
    </div>

    <!-- Charts -->
    <div class="grid2" style="margin-top:22px">
      <div class="card">
        <div class="toolbar">
          <div>
            <h2 class="section-title">Brand Popularity</h2>
            <div class="section-sub">Last 6 Months Grocery Engagement Index</div>
          </div>
          <div>
            <button class="btn primary period" data-period="monthly">Monthly</button>
            <button class="btn period" data-period="quarterly">Quarterly</button>
          </div>
        </div>
        <div id="lineChart" class="line-chart"></div>
      </div>
      <div class="card">
        <h2 class="section-title">Market Share Heatmap</h2>
        <div class="section-sub">Category Distribution</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:7px">
          <div style="background:#19742b;color:#fff;border-radius:8px;padding:18px;height:90px"><b style="font-size:25px">42%</b><br>Fresh Produce</div>
          <div style="background:#9bf18f;border-radius:8px;padding:18px;height:90px"><b style="font-size:25px">18%</b><br>Dairy &amp; Eggs</div>
          <div style="background:#062443;color:#fff;border-radius:8px;padding:18px;height:120px"><b style="font-size:25px">25%</b><br>Organic Pantry</div>
          <div style="background:#dddfe3;border-radius:8px;padding:18px;height:90px"><b style="font-size:25px">10%</b><br>Frozen Goods</div>
          <div style="background:#c7c5d6;border-radius:8px;padding:18px;height:75px"><b style="font-size:25px">5%</b><br>Other</div>
        </div>
      </div>
    </div>

    <!-- Pricing Comparison Table -->
    <div class="card" style="margin-top:22px">
      <h2 class="section-title">Pricing Strategy Comparison</h2>
      <div class="section-sub">Your Pricing vs. Market Top 3 Competitors</div>
      <table>
        <thead>
          <tr>
            <th>PRODUCT SEGMENT</th>
            <th>YOUR PRICE</th>
            <th>MARKET AVG</th>
            <th>VARIANCE</th>
            <th>STRATEGY SUGGESTION</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><b>Organic Whole Milk (1gal)</b></td>
            <td>$5.49</td>
            <td>$5.65</td>
            <td><span class="pill green">-2.8%</span></td>
            <td>🚀 Aggressive Capture</td>
          </tr>
          <tr>
            <td><b>Artisan Sourdough Loaf</b></td>
            <td>$6.50</td>
            <td>$5.90</td>
            <td><span class="pill red">+10.1%</span></td>
            <td>⚖ Review for Retention</td>
          </tr>
          <tr>
            <td><b>Premium Avocado Oil</b></td>
            <td>$14.99</td>
            <td>$14.25</td>
            <td><span class="pill green">+5.2%</span></td>
            <td>✥ Premium Positioning</td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>
</div>

<div id="toast" class="toast"></div>
<div id="modalRoot"></div>
<script>
  const URLROOT = '<?php echo URLROOT; ?>';
</script>
<script src="<?php echo URLROOT; ?>/js/supplier/analytics.js"></script>
</body>
</html>
