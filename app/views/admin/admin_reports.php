<?php $pageCss = ['admin-css/base', 'admin-css/reports']; require APPROOT . '/Views/shared/header.php';
$esc = static function ($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
};
$money = static function ($v) {
    return 'LKR ' . number_format((float)$v, 2);
};
$change = static function ($now, $before) {
    $before = (float)$before;
    return $before > 0 ? number_format(((float)$now - $before) / $before * 100, 1) . '% vs previous period' : 'Previous period: ' . ($before == 0 ? 'no data' : number_format($before));
};
?>
<header class="content-header">
    <div class="header-left">
        <h2 class="page-title">Reports &amp; Analytics</h2>
        <p class="page-subtitle">Real platform transactions, budgets and supplier activity</p>
    </div>
    <div class="header-right report-controls">
        <form method="get" action="<?php echo $esc(URLROOT); ?>/admin/reports"><label for="period-select">Period </label><select class="filter-select" id="period-select" name="days" onchange="this.form.submit()">
                <option value="7" <?= $data['days'] == 7 ? 'selected' : '' ?>>Last 7 days</option>
                <option value="30" <?= $data['days'] == 30 ? 'selected' : '' ?>>Last 30 days</option>
                <option value="90" <?= $data['days'] == 90 ? 'selected' : '' ?>>Last 90 days</option>
            </select></form><button class="btn btn-navy" type="button" onclick="window.print()">Print / Save PDF</button>
    </div>
</header>
<p class="report-note">Reporting period: <?= $esc($data['start']) ?> to <?= $esc(date('Y-m-d')) ?>. Transaction values are based on recorded transactions; budgets are separately recorded planned amounts.</p>
<section class="report-cards">
    <div class="report-card">
        <div class="report-muted">Total Transactions</div>
        <div class="report-number"><?= number_format($data['stats']['transactions']) ?></div>
        <div class="report-muted"><?= $esc($change($data['stats']['transactions'], $data['prior']['transactions'])) ?></div>
    </div>
    <div class="report-card">
        <div class="report-muted">Recorded Transaction Volume</div>
        <div class="report-number"><?= $esc($money($data['stats']['volume'])) ?></div>
        <div class="report-muted"><?= $esc($change($data['stats']['volume'], $data['prior']['volume'])) ?></div>
    </div>
    <div class="report-card">
        <div class="report-muted">Average Transaction Value</div>
        <div class="report-number"><?= $esc($money($data['stats']['average'])) ?></div>
        <div class="report-muted"><?= $esc($change($data['stats']['average'], $data['prior']['average'])) ?></div>
    </div>
    <div class="report-card">
        <div class="report-muted">Active Suppliers (current)</div>
        <div class="report-number"><?= number_format($data['active']) ?></div>
        <div class="report-muted">Not filtered by transaction period</div>
    </div>
    <div class="report-card">
        <div class="report-muted">Recorded Income</div>
        <div class="report-number"><?= $esc($money($data['stats']['income'])) ?></div>
    </div>
    <div class="report-card">
        <div class="report-muted">Recorded Expenses</div>
        <div class="report-number"><?= $esc($money($data['stats']['expenses'])) ?></div>
    </div>
    <div class="report-card">
        <div class="report-muted">Budgets Recorded for Period</div>
        <div class="report-number"><?= $esc($money($data['budget'])) ?></div>
        <div class="report-muted">Monthly budgets with month dates in selected range</div>
    </div>
</section>
<section class="report-panels">
    <div class="report-card">
        <h3>Expense Category Performance</h3>
        <p class="report-muted">Based on recorded expense transactions (not product views)</p><?php $max = 0;
                                                                                                foreach ($data['categories'] as $c) {
                                                                                                    $max = max($max, (float)$c['total']);
                                                                                                }
                                                                                                if (!$data['categories']): ?><p>No expense transactions for this period.</p><?php else: ?><div class="report-table-wrap">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Transactions</th>
                            <th>Total spent</th>
                        </tr>
                    </thead>
                    <tbody><?php foreach ($data['categories'] as $c): ?><tr>
                                <td><?= $esc($c['name']) ?><div class="report-bar"><span style="width:<?= $max > 0 ? round((float)$c['total'] / $max * 100) : 0 ?>%"></span></div>
                                </td>
                                <td><?= number_format($c['transaction_count']) ?></td>
                                <td><?= $esc($money($c['total'])) ?></td>
                            </tr><?php endforeach ?></tbody>
                </table>
            </div><?php endif ?>
    </div>
    <div class="report-card">
        <h3>Suppliers by Product Count</h3>
        <p class="report-muted">Product count is not a measure of sales or page views.</p><?php if (!$data['suppliers']): ?><p>No suppliers found.</p><?php else: ?><div class="report-table-wrap">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Status</th>
                            <th>Products</th>
                            <th>Approved</th>
                        </tr>
                    </thead>
                    <tbody><?php foreach (array_slice($data['suppliers'], 0, 5) as $s): ?><tr>
                                <td><?= $esc($s['name']) ?></td>
                                <td><?= $esc($s['account_status']) ?></td>
                                <td><?= number_format($s['product_count']) ?></td>
                                <td><?= number_format($s['approved_products']) ?></td>
                            </tr><?php endforeach ?></tbody>
                </table>
            </div><button class="btn btn-outline-navy btn-sm report-no-print report-btn-spacing" type="button" id="show-suppliers">View Full List</button>
            <div id="all-suppliers" class="report-hidden report-table-wrap">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Products</th>
                            <th>Approved</th>
                        </tr>
                    </thead>
                    <tbody><?php foreach ($data['suppliers'] as $s): ?><tr>
                                <td><?= $esc($s['name']) ?></td>
                                <td><?= $esc($s['email']) ?></td>
                                <td><?= $esc($s['account_status']) ?></td>
                                <td><?= number_format($s['product_count']) ?></td>
                                <td><?= number_format($s['approved_products']) ?></td>
                            </tr><?php endforeach ?></tbody>
                </table>
            </div><?php endif ?>
    </div>
</section>
<section class="report-card report-card-mt">
    <h3>Daily Income and Expenses</h3><?php if (!$data['daily']): ?><p>No transactions recorded during this period.</p><?php else: ?><div class="report-table-wrap">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Income</th>
                        <th>Expenses</th>
                    </tr>
                </thead>
                <tbody><?php foreach ($data['daily'] as $day): ?><tr>
                            <td><?= $esc($day['day']) ?></td>
                            <td><?= $esc($money($day['income'])) ?></td>
                            <td><?= $esc($money($day['expenses'])) ?></td>
                        </tr><?php endforeach ?></tbody>
            </table>
        </div><?php endif ?>
</section>
<script>
    document.getElementById('show-suppliers')?.addEventListener('click', function() {
        const el = document.getElementById('all-suppliers');
        el.classList.toggle('report-hidden');
        this.textContent = el.classList.contains('report-hidden') ? 'View Full List' : 'Hide Full List';
    });
</script>
<?php require APPROOT . '/Views/shared/footer.php'; ?>
