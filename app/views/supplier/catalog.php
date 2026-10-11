<?php $spActive = 'catalog'; $spActiveLink = false; ?>
<?php require APPROOT . '/Views/shared/header.php'; ?>

    <h1 class="page-title">Product Catalog</h1>
    <p class="subtitle">Manage your inventory, pricing, and product visibility across the platform.</p>

    <!-- Stats -->
    <div class="grid3">
      <div class="card stat">
        <div>
          <div class="stat-label">Total Brand Reach</div>
          <div class="stat-value">128.4K</div>
          <div class="trend"> +12% this month</div>
        </div>
      </div>
      <div class="card stat">
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
          <div class="trend"> Stable performance</div>
        </div>
      </div>
      <div class="card stat">
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
            <button class="btn" id="filter"> Filters</button>
            <button class="btn" id="export"> Export CSV</button>
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
          <a class="btn" style="background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary);" href="<?= URLROOT ?>/supplier/manage_pricing">Pricing</a>

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
          <button class="btn" id="report" style="width:100%;margin-top:14px">View Full Report </button>
        </div>
      </div>
    </div>
    
  <div id="toast" class="toast"></div>
<div id="modalRoot"></div>
<script>
    const URLROOT = '<?php echo URLROOT; ?>';

    );
        }
    }

    }
</script>
<!--<script src="<?php echo URLROOT; ?>/js/supplier/catalog.js"></script> -->

<?php require APPROOT . '/Views/shared/footer.php'; ?>








