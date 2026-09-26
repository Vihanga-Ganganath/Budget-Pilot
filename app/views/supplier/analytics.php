<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Budget Pilot - Analytics &amp; Market Trends</title>
<link rel="icon" type="image/png" href="<?php echo URLROOT; ?>/public/assets/logos/favicon.png?v=2">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/supplier/supplier.css?v=<?php echo @filemtime(APPROOT . '/../public/css/supplier/supplier.css'); ?>">
</head>
<body class="sp">

<div class="shell">

<?php $spActive = 'analytics'; $spActiveLink = false; require APPROOT . '/views/supplier/_sidebar.php'; ?>

  <div class="main">
<?php require APPROOT . '/views/supplier/_appbar.php'; ?>

  <!-- Main Content -->
  <main>
    <div class="toolbar">
      <div>
        <h1 class="page-title">Analytics &amp; Market Trends</h1>
      <p class="subtitle">Analytics dashboard preview. Live market and sales analytics integration is planned for the next development phase.</p>      </div>
      <button class="btn primary" id="exportReport">Export Quarterly Report</button>
    </div>

    <!-- Stats -->
    <div class="grid3">
      <div class="card stat">
        <div>
          <div class="stat-label">Revenue Analytics</div>
          <div class="stat-value">Preview</div>
          <div class="trend">Live sales integration pending</div>
        </div>
      </div>
      <div class="card stat">
        <div>
          <div class="stat-label">Customer Interactions</div>
          <div class="stat-value">Preview</div>
          <div class="trend">Interaction tracking planned</div>
        </div>
      </div>
      <div class="card stat">
        <div>
          <div class="stat-label">Market Insights</div>
          <div class="stat-value">Preview</div>
          <div class="trend">Market-data integration pending</div>
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
            <td>Aggressive Capture</td>
          </tr>
          <tr>
            <td><b>Artisan Sourdough Loaf</b></td>
            <td>$6.50</td>
            <td>$5.90</td>
            <td><span class="pill red">+10.1%</span></td>
            <td>Review for Retention</td>
          </tr>
          <tr>
            <td><b>Premium Avocado Oil</b></td>
            <td>$14.99</td>
            <td>$14.25</td>
            <td><span class="pill green">+5.2%</span></td>
            <td>Premium Positioning</td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>
  </div>
</div>

<div id="toast" class="toast"></div>
<div id="modalRoot"></div>
<script>
  const URLROOT = '<?php echo URLROOT; ?>';
</script>
<script src="<?php echo URLROOT; ?>/js/supplier/analytics.js"></script>
<script src="<?php echo URLROOT; ?>/js/customer-js/nav.js"></script>
</body>
</html>
