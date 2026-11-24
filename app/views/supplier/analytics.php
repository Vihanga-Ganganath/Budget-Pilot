<?php $spActive = 'analytics'; $spActiveLink = false; $pageCss = 'supplier/supplier'; ?>
<?php require APPROOT . '/Views/shared/header.php'; ?>

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
    <div class="grid2 grid-mt-22">
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
        <div class="heatmap-grid">
          <div class="heatmap-cell-1"><b class="heatmap-val">42%</b><br>Fresh Produce</div>
          <div class="heatmap-cell-2"><b class="heatmap-val">18%</b><br>Dairy &amp; Eggs</div>
          <div class="heatmap-cell-3"><b class="heatmap-val">25%</b><br>Organic Pantry</div>
          <div class="heatmap-cell-4"><b class="heatmap-val">10%</b><br>Frozen Goods</div>
          <div class="heatmap-cell-5"><b class="heatmap-val">5%</b><br>Other</div>
        </div>
      </div>
    </div>

    <!-- Pricing Comparison Table -->
    <div class="card card-mt-22">
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
  <div id="toast" class="toast"></div>
<div id="modalRoot"></div>
<script>
  const URLROOT = '<?php echo URLROOT; ?>';
</script>
<script src="<?php echo URLROOT; ?>/js/supplier/analytics.js"></script>

<?php require APPROOT . '/Views/shared/footer.php'; ?>
