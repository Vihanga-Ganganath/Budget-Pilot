<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Budget Pilot - Product Catalog</title>
<link rel="icon" type="image/png" href="<?php echo URLROOT; ?>/public/assets/logos/favicon.png?v=2">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/supplier/supplier.css?v=<?php echo @filemtime(APPROOT . '/../public/css/supplier/supplier.css'); ?>">
</head>
<body class="sp">

<div class="shell">

<?php $spActive = 'catalog'; $spActiveLink = false; require APPROOT . '/views/supplier/_sidebar.php'; ?>

  <div class="main">
<?php require APPROOT . '/views/supplier/_appbar.php'; ?>

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
          <div class="stat-value" id="activeCount">
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
        <div class="iconbox blue">☑</div>
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

    <!-- Inventory Table + Sidebar Cards -->
    <div class="grid2" style="margin-top:22px">
      <div class="card table-card">
        <div class="toolbar">
          <div>
            <h2 class="section-title">
              Inventory Overview
              <span class="pill blue" id="countPill">
                    <?= count($data['products'] ?? []) ?> Products
              </span>
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
              <th>PRODUCT</th>
              <th>VARIANT</th>
              <th>SKU</th>
              <th>CATEGORY</th>
              <th>PRICE (USD)</th>
              <th>STATUS</th>
              <th>VERIFICATION</th>
              <th>ACTIONS</th>
            </tr>
          </thead>
          <tbody id="rows">

          <?php if (!empty($data['products'])): ?>

            <?php foreach ($data['products'] as $product): ?>

              <tr>
    <td>
    <div style="display:flex; align-items:center; gap:10px;">

        <?php if (!empty($product['image_url'])): ?>

            <img
                src="<?= URLROOT . '/' . htmlspecialchars($product['image_url']) ?>"
                alt="<?= htmlspecialchars($product['name']) ?>"
                style="
                    width:50px;
                    height:50px;
                    object-fit:cover;
                    border-radius:8px;
                "
            >

        <?php else: ?>

            <div style="
                width:50px;
                height:50px;
                border-radius:8px;
                background:#eee;
                display:flex;
                align-items:center;
                justify-content:center;
                font-size:11px;
                color:#777;
            ">
                No Image
            </div>

        <?php endif; ?>

        <span>
            <?= htmlspecialchars($product['name']) ?>
        </span>

    </div>
</td>

    <td>
        <?= htmlspecialchars($product['variant_name'] ?? 'N/A') ?>
    </td>

    <td>
        <?= htmlspecialchars($product['sku'] ?? 'N/A') ?>
    </td>

    <td>
        <?= htmlspecialchars($product['category_name'] ?? 'N/A') ?>
    </td>

    <td>
        $<?= number_format((float)($product['price'] ?? 0), 2) ?>
    </td>

    <td>
        <?= htmlspecialchars($product['status']) ?>
    </td>

    <td>
        <?= htmlspecialchars($product['admin_verification']) ?>
    </td>

    <td>
      <div class="product-actions">
        <a class="btn"
           href="<?= URLROOT ?>/supplier/editProduct/<?= $product['id'] ?>">
            Edit
        </a>

        <a class="btn"
           href="<?= URLROOT ?>/supplier/deleteProduct/<?= $product['id'] ?>"
           onclick="return confirm('Are you sure you want to delete this product?');">
            Delete
        </a>
      </div>
    </td>
</tr>
            <?php endforeach; ?>

          <?php else: ?>

            <tr>
              <td colspan="8" style="text-align:center;">
                No products found.
              </td>
            </tr>

          <?php endif; ?>

          </tbody>
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
    <!-- Add Product Form -->
    <div id="addProductForm" style="display:none; margin-top:25px;" class="card">

    <h2 class="section-title">Add New Product</h2>

   <form method="POST"
      enctype="multipart/form-data"
      action="<?php echo URLROOT; ?>/supplier/addProduct">

      <div style="margin-bottom:12px;">
        <label>Product Name *</label><br>
        <input
          type="text"
          name="name"
          class="input"
          required
          style="width:100%;"
        >
      </div>

      <div style="margin-bottom:12px;">
        <label>Category *</label><br>

        <select
          name="category_id"
          class="input"
          required
          style="width:100%;"
        >
          <option value="">Select Category</option>

          <option value="1">Detergents &amp; Cleaning</option>
          <option value="2">Personal Care</option>
          <option value="3">Packaged Food</option>
          <option value="4">Cooking Essentials</option>
          <option value="5">Beverages</option>
          <option value="6">Snacks &amp; Spreads</option>
          <option value="7">Household Consumables</option>

        </select>
      </div>

      <div style="margin-bottom:12px;">
        <label>Description</label><br>
        <textarea
          name="description"
          class="input"
          style="width:100%;"
        ></textarea>
      </div>

      <div style="margin-bottom:12px;">
        <label>Ingredients</label><br>
        <textarea
          name="ingredients"
          class="input"
          style="width:100%;"
        ></textarea>
      </div>

      <div style="margin-bottom:12px;">
        <label>Nutritional Value</label><br>
        <textarea
          name="nutritional_value"
          class="input"
          style="width:100%;"
        ></textarea>
      </div>

      <div style="margin-bottom:12px;">
        <label>Document URL</label><br>
        <input
          type="text"
          name="document_url"
          class="input"
          style="width:100%;"
        >
      </div>
      <!-- Product Variant Details -->

<div style="margin-bottom:12px;">
    <label>Unit Size / Variant *</label><br>
    <input
        type="text"
        name="variant_name"
        class="input"
        placeholder="e.g. 1L, 500g, 250ml"
        required
        style="width:100%;"
    >
</div>

<div style="margin-bottom:12px;">
    <label>Price (USD $) *</label><br>
    <input
        type="number"
        name="price"
        class="input"
        placeholder="e.g. 4.50"
        min="0"
        step="0.01"
        required
        style="width:100%;"
    >
</div>

<div style="margin-bottom:12px;">
    <label>Product Image</label><br>

    <input
        type="file"
        name="product_image"
        class="input"
        accept="image/jpeg,image/png,image/webp"
        style="width:100%;"
    >

    <small style="color:#777;">
        JPG, PNG or WEBP
    </small>
</div>

      <div style="margin-bottom:15px;">
        <label>Status</label><br>

        <select
          name="status"
          class="input"
          style="width:100%;"
        >
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
          <option value="Discontinued">Discontinued</option>
        </select>
      </div>

      <button type="submit" class="btn">
        Save Product
      </button>

     <button
      type="button"
      class="btn"
      id="cancelAddProduct"
      onclick="hideAddProductForm()">
      Cancel
    </button>

    </form>

  </div>
  </main>
  </div>
</div>

<div id="toast" class="toast"></div>
<div id="modalRoot"></div>
<script>
    const URLROOT = '<?php echo URLROOT; ?>';

    function showAddProductForm() {
        const form = document.getElementById('addProductForm');

        if (form) {
            form.style.display = 'block';

            form.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }

    // Sidebar "Add New Product" on other pages links here as catalog#add
    if (location.hash === '#add') {
        window.addEventListener('DOMContentLoaded', showAddProductForm);
    }

    function hideAddProductForm() {
        const form = document.getElementById('addProductForm');

        if (form) {
            form.style.display = 'none';
        }
    }
</script>
<!--<script src="<?php echo URLROOT; ?>/js/supplier/catalog.js"></script> -->
<script src="<?php echo URLROOT; ?>/js/customer-js/nav.js"></script>
</body>
</html>
