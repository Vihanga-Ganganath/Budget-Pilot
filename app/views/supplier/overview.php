<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Budget Pilot - Brand Overview</title>
<link rel="icon" type="image/png" href="<?php echo URLROOT; ?>/public/assets/logos/favicon.png?v=2">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/supplier/supplier.css?v=<?php echo @filemtime(APPROOT . '/../public/css/supplier/supplier.css'); ?>">
</head>
<body class="sp">

<div class="shell">

<?php $spActive = 'overview'; $spActiveLink = false; require APPROOT . '/views/supplier/_sidebar.php'; ?>

  <div class="main">
<?php require APPROOT . '/views/supplier/_appbar.php'; ?>

  <!-- Main Content -->
  <main>
    <h1 class="page-title">Brand Overview</h1>
    <p class="subtitle">Manage your market presence and catalog health.</p>

    <!-- Admin Notices (read-only for suppliers) -->
    <section class="card supplier-notices" aria-labelledby="supplierNoticesTitle">
      <div class="toolbar">
        <div>
          <h2 class="section-title" id="supplierNoticesTitle">📢 Notices &amp; Announcements</h2>
          <div class="section-sub">Important updates shared by the Budget Pilot admin team</div>
        </div>
        <?php if (!empty($data['notices'])): ?>
          <span class="pill blue"><?php echo count($data['notices']); ?> ACTIVE</span>
        <?php endif; ?>
      </div>

      <?php if (!empty($data['notices'])): ?>
        <div class="supplier-notice-list">
          <?php foreach ($data['notices'] as $notice): ?>
            <article class="supplier-notice-item">
              <div class="supplier-notice-icon">!</div>
              <div class="supplier-notice-body">
                <div class="supplier-notice-head">
                  <strong><?php echo htmlspecialchars($notice->title); ?></strong>
                  <span class="pill green"><?php echo $notice->audience === 'all' ? 'EVERYONE' : 'SUPPLIERS'; ?></span>
                </div>
                <p><?php echo nl2br(htmlspecialchars($notice->message)); ?></p>
                <div class="supplier-notice-meta">
                  Posted <?php echo date('M j, Y', strtotime($notice->created_at)); ?>
                  <?php if (!empty($notice->expires_at)): ?>
                    &nbsp;•&nbsp; Available until <?php echo date('M j, Y', strtotime($notice->expires_at)); ?>
                  <?php endif; ?>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="supplier-notice-empty">No active notices for suppliers right now.</div>
      <?php endif; ?>
    </section>

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
          <div class="stat-value">
  <?php
    $activeCount = 0;

    foreach (($data['products'] ?? []) as $product) {
      if (($product['status'] ?? '') === 'Active') {
        $activeCount++;
      }
    }

    echo $activeCount;
  ?>
</div>
          <div class="trend">◉ Stable performance</div>
        </div>
      </div>
      <div class="card stat">
        <div class="iconbox blue">▣</div>
        <div>
          <div class="stat-label">Pending Verifications</div>
          <div class="stat-value">
  <?php
    $pendingCount = 0;

    foreach (($data['products'] ?? []) as $product) {
      if (($product['admin_verification'] ?? '') === 'Pending') {
        $pendingCount++;
      }
    }

    echo $pendingCount;
  ?>
</div>
          <div class="trend">
  <?= $pendingCount > 0 ? $pendingCount . ' awaiting review' : 'No pending reviews' ?>
</div>
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

    
    <div style="margin-top:22px">
      <button class="card insight-widget" id="addInsight" type="button"
        onclick="alert('Custom insight widgets will be available in the next development phase.')">
        <span class="insight-plus">⊕</span>
        <strong>Add Insight Widget</strong>
        <span class="small">Customize your dashboard by adding custom data streams or market alerts.</span>
      </button>
    </div>
  </main>
  </div>
</div>

<div id="toast" class="toast"></div>
<div id="modalRoot"></div>
<script>
  // Pass URLROOT to JS
  const URLROOT = '<?php echo URLROOT; ?>';
</script>
<script src="<?php echo URLROOT; ?>/js/supplier/overview.js"></script>
<script src="<?php echo URLROOT; ?>/js/customer-js/nav.js"></script>
</body>
</html>
