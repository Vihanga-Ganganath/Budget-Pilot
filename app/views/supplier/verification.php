<?php $spActive = 'verification'; $spActiveLink = false; $pageCss = 'supplier/supplier'; ?>
<?php require APPROOT . '/Views/shared/header.php'; ?>

    <h1 class="page-title">Verification Status</h1>
    <p class="subtitle">Manage your grocery brand credentials and legal compliance.</p>

    <!-- Trust Stats -->
    <div class="grid3">
      <div class="card card-border-navy">
        <div class="stat-label">VERIFICATION PROGRESS</div>
        <div class="stat-value stat-value-lg">In Progress</div>
        <div class="v-progress-track">
          <div class="v-progress-fill"></div>
        </div>
        <div class="trend trend-mt-14">Supplier verification workflow</div>
      </div>
      <div class="card card-border-dark">
        <div class="stat-label">DOCUMENT REVIEW</div>
        <div class="stat-value stat-value-lg">Pending</div>
        <p class="small">Compliance document integration in progress</p>
      </div>
      <div class="card card-border-green">
        <div class="stat-label">VERIFICATION STATUS</div>
        <div class="stat-value stat-value-lg-dark">Pending</div>
        <span class="pill blue">AWAITING REVIEW</span>
      </div>
    </div>

    <!-- Verification Journey -->
    <div class="card card-mt-22">
      <h2 class="section-title">Verification Journey</h2>
      <div class="journey-grid">
        <div>
          <div class="iconbox iconbox-step-active">1</div>
          <b class="step-title-navy">Identity</b>
          <span class="small">Brand Entity Validation</span>
        </div>
        <div>
          <div class="iconbox iconbox-step-active">2</div>
          <b class="step-title-navy">Credentials</b>
          <span class="small">Legal Documentation</span>
        </div>
        <div>
          <div class="iconbox iconbox-step-current">3</div>
          <b class="step-title-navy">Auth Tier</b>
          <span class="small">Brand Rights Review</span>
        </div>
        <div>
          <div class="iconbox iconbox-step-pending">4</div>
          <b class="step-title-gray">Market Ready</b>
          <span class="small">Final Global Approval</span>
        </div>
      </div>
    </div>

    <!-- Compliance Documents Table -->
    <div class="card table-card card-mt-22">
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
  <div id="toast" class="toast"></div>
<div id="modalRoot"></div>
<script>
  const URLROOT = '<?php echo URLROOT; ?>';
</script>
<script src="<?php echo URLROOT; ?>/js/supplier/verification.js"></script>

<?php require APPROOT . '/Views/shared/footer.php'; ?>
