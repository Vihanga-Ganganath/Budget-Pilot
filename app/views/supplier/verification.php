<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Budget Pilot - Verification Status</title>
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
      <a href="<?php echo URLROOT; ?>/supplier/analytics">↗ &nbsp; Analytics &amp; Trends</a>
      <a class="active" href="<?php echo URLROOT; ?>/supplier/verification">♢ &nbsp; Verification Status</a>
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
    <h1 class="page-title">Verification Status</h1>
    <p class="subtitle">Manage your grocery brand credentials and legal compliance.</p>

    <!-- Trust Stats -->
    <div class="grid3">
      <div class="card" style="border-left:4px solid var(--navy)">
        <div class="stat-label">TRUST SCORE</div>
        <div class="stat-value" style="font-size:48px">98 <span style="font-size:18px">/ 100</span></div>
        <div style="height:7px;background:#e2e4f0;border-radius:8px">
          <div style="height:100%;width:98%;background:var(--navy);border-radius:8px"></div>
        </div>
        <div class="trend" style="margin-top:14px">↗ Top 2% of Grocery Brands</div>
      </div>
      <div class="card" style="border-left:4px solid #062443">
        <div class="stat-label">BRAND IMPRESSIONS</div>
        <div class="stat-value" style="font-size:45px">42.8K <span style="font-size:15px">Last 30 days</span></div>
        <p class="small">◉ ◉ +12 &nbsp; Growth in premium segment</p>
      </div>
      <div class="card" style="border-left:4px solid #18802b">
        <div class="stat-label">NEXT REVIEW DATE</div>
        <div class="stat-value" style="font-size:42px;color:#222">Oct 15 <span style="font-size:18px">2024</span></div>
        <span class="pill green">◉ STATUS: COMPLIANT</span>
      </div>
    </div>

    <!-- Verification Journey -->
    <div class="card" style="margin-top:22px">
      <h2 class="section-title">Verification Journey</h2>
      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;text-align:center;margin-top:30px">
        <div>
          <div class="iconbox" style="margin:auto;background:var(--navy);color:#fff">▦</div>
          <b style="display:block;margin-top:10px;color:var(--navy)">Identity</b>
          <span class="small">Brand Entity Validation</span>
        </div>
        <div>
          <div class="iconbox" style="margin:auto;background:var(--navy);color:#fff">☑</div>
          <b style="display:block;margin-top:10px;color:var(--navy)">Credentials</b>
          <span class="small">Legal Documentation</span>
        </div>
        <div>
          <div class="iconbox" style="margin:auto;border:2px solid var(--navy)">♢</div>
          <b style="display:block;margin-top:10px;color:var(--navy)">Auth Tier</b>
          <span class="small">Brand Rights Review</span>
        </div>
        <div>
          <div class="iconbox" style="margin:auto;background:#eee;color:#777">♙</div>
          <b style="display:block;margin-top:10px;color:#777">Market Ready</b>
          <span class="small">Final Global Approval</span>
        </div>
      </div>
    </div>

    <!-- Compliance Documents Table -->
    <div class="card table-card" style="margin-top:22px">
      <div class="toolbar">
        <h2 class="section-title">Compliance Documents</h2>
        <button class="btn" id="updateAll">⇧ Update All</button>
      </div>
      <table>
        <thead>
          <tr>
            <th>DOCUMENT NAME</th>
            <th>REFERENCE ID</th>
            <th>ISSUING AUTHORITY</th>
            <th>STATUS</th>
            <th>ACTIONS</th>
          </tr>
        </thead>
        <tbody id="docs">
          <tr>
            <td>▣ &nbsp; <b>Business Registration</b></td>
            <td>#BR-990-221</td>
            <td>Global Trade Registry</td>
            <td><span class="pill green">✓ VERIFIED</span></td>
            <td><button class="btn doc" data-name="Business Registration">⇩</button></td>
          </tr>
          <tr>
            <td>▤ &nbsp; <b>Tax Clearance</b></td>
            <td>#TX-112-900</td>
            <td>Federal Revenue Dept</td>
            <td><span class="pill green">✓ VERIFIED</span></td>
            <td><button class="btn doc" data-name="Tax Clearance">⇩</button></td>
          </tr>
          <tr>
            <td>⚑ &nbsp; <b>Grocery Brand Authorization</b></td>
            <td>#GBA-554-12</td>
            <td>Brand Integrity Board</td>
            <td><span class="pill blue pending">↻ PENDING REVIEW</span></td>
            <td><button class="btn doc" data-name="Grocery Brand Authorization">◉</button></td>
          </tr>
          <tr>
            <td>◉ &nbsp; <b>Digital Rights Charter</b></td>
            <td>#DRC-001-9X</td>
            <td>Global Commerce Authority</td>
            <td><span class="pill red">◉ EXPIRED</span></td>
            <td><button class="btn doc" data-name="Digital Rights Charter">!</button></td>
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
<script src="<?php echo URLROOT; ?>/js/supplier/verification.js"></script>
</body>
</html>
