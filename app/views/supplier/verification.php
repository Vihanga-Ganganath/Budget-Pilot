<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Budget Pilot - Verification Status</title>
<link rel="icon" type="image/png" href="<?php echo URLROOT; ?>/public/assets/logos/favicon.png?v=2">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/supplier/supplier.css?v=<?php echo @filemtime(APPROOT . '/../public/css/supplier/supplier.css'); ?>">
</head>
<body class="sp">

<div class="shell">

<?php $spActive = 'verification'; $spActiveLink = false; require APPROOT . '/views/supplier/_sidebar.php'; ?>

  <div class="main">
<?php require APPROOT . '/views/supplier/_appbar.php'; ?>

  <!-- Main Content -->
  <main>
    <h1 class="page-title">Verification Status</h1>
    <p class="subtitle">Manage your grocery brand credentials and legal compliance.</p>

    <!-- Trust Stats -->
    <div class="grid3">
      <div class="card" style="border-left:4px solid var(--navy)">
        <div class="stat-label">VERIFICATION PROGRESS</div>
        <div class="stat-value" style="font-size:38px">In Progress</div>
        <div style="height:7px;background:#e2e4f0;border-radius:8px">
        <div style="height:100%;width:50%;background:var(--navy);border-radius:8px"></div>
        </div>
        <div class="trend" style="margin-top:14px">Supplier verification workflow</div>
      </div>
      <div class="card" style="border-left:4px solid #062443">
        <div class="stat-label">DOCUMENT REVIEW</div>
        <div class="stat-value" style="font-size:38px">Pending</div>
        <p class="small">Compliance document integration in progress</p>
      </div>
      <div class="card" style="border-left:4px solid #18802b">
        <div class="stat-label">VERIFICATION STATUS</div>
        <div class="stat-value" style="font-size:38px;color:#222">Pending</div>
        <span class="pill blue">AWAITING REVIEW</span>
      </div>
    </div>

    <!-- Verification Journey -->
    <div class="card" style="margin-top:22px">
      <h2 class="section-title">Verification Journey</h2>
      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;text-align:center;margin-top:30px">
        <div>
          <div class="iconbox" style="margin:auto;border-radius:50%;font-weight:800;font-size:18px;background:var(--navy);color:#fff">1</div>
          <b style="display:block;margin-top:10px;color:var(--navy)">Identity</b>
          <span class="small">Brand Entity Validation</span>
        </div>
        <div>
          <div class="iconbox" style="margin:auto;border-radius:50%;font-weight:800;font-size:18px;background:var(--navy);color:#fff">2</div>
          <b style="display:block;margin-top:10px;color:var(--navy)">Credentials</b>
          <span class="small">Legal Documentation</span>
        </div>
        <div>
          <div class="iconbox" style="margin:auto;border-radius:50%;font-weight:800;font-size:18px;border:2px solid var(--navy)">3</div>
          <b style="display:block;margin-top:10px;color:var(--navy)">Auth Tier</b>
          <span class="small">Brand Rights Review</span>
        </div>
        <div>
          <div class="iconbox" style="margin:auto;border-radius:50%;font-weight:800;font-size:18px;background:#eee;color:#777">4</div>
          <b style="display:block;margin-top:10px;color:#777">Market Ready</b>
          <span class="small">Final Global Approval</span>
        </div>
      </div>
    </div>

    <!-- Compliance Documents Table -->
    <div class="card table-card" style="margin-top:22px">
      <div class="toolbar">
        <h2 class="section-title">Compliance Documents <span class="small">(Prototype)</span></h2>
        <button class="btn" id="updateAll">Update All</button>
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
            <td><b>Business Registration</b></td>
            <td>#BR-990-221</td>
            <td>Global Trade Registry</td>
            <td><span class="pill green">VERIFIED</span></td>
            <td><button class="btn doc" data-name="Business Registration">Download</button></td>
          </tr>
          <tr>
            <td><b>Tax Clearance</b></td>
            <td>#TX-112-900</td>
            <td>Federal Revenue Dept</td>
            <td><span class="pill green">VERIFIED</span></td>
            <td><button class="btn doc" data-name="Tax Clearance">Download</button></td>
          </tr>
          <tr>
            <td><b>Grocery Brand Authorization</b></td>
            <td>#GBA-554-12</td>
            <td>Brand Integrity Board</td>
            <td><span class="pill blue pending">PENDING REVIEW</span></td>
            <td><button class="btn doc" data-name="Grocery Brand Authorization">Download</button></td>
          </tr>
          <tr>
            <td><b>Digital Rights Charter</b></td>
            <td>#DRC-001-9X</td>
            <td>Global Commerce Authority</td>
            <td><span class="pill red">EXPIRED</span></td>
            <td><button class="btn doc" data-name="Digital Rights Charter">!</button></td>
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
<script src="<?php echo URLROOT; ?>/js/supplier/verification.js"></script>
<script src="<?php echo URLROOT; ?>/js/customer-js/nav.js"></script>
</body>
</html>
