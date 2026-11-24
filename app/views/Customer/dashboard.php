<?php $pageCss = 'customer-css/dashboard'; require APPROOT . '/Views/shared/header.php'; ?>
<?php
// one colour per category for the pie chart
$colours = ['#16A34A', '#2563EB', '#DB2777', '#F59E0B', '#101C56', '#DC2626', '#0D9488', '#9CA3AF'];
function money($n) { return ($n < 0 ? '-' : '') . '$' . number_format(abs($n), 2); }

// build the pie: each category gets a slice from $start% to $end%
$slices = [];
$gradient = [];
$start  = 0;
$totalExpenses = (float)($data['total_expenses'] ?? 0);
foreach ($data['spending'] as $i => $c) {
    if ($c['total'] <= 0) continue;
    $percent = $totalExpenses > 0 ? ($c['total'] / $totalExpenses * 100) : 0;
    $colour  = $colours[$i % count($colours)];
    $slices[] = ['name' => $c['name'], 'total' => $c['total'], 'percent' => $percent, 'colour' => $colour];
    $gradient[] = $colour . ' ' . $start . '% ' . ($start + $percent) . '%';
    $start += $percent;
}
?>

      <div class="dashboard-header">
        <div>
          <h1 class="page__title" id="greeting">Welcome back, <strong><?php echo htmlspecialchars($userName); ?></strong></h1>
          <p class="page__sub" id="greetingSub">Here is where your money stands this month.</p>
        </div>
      </div>

      <?php if (!empty($data['flash'])): ?>
        <p class="dashboard-flash"><?php echo htmlspecialchars($data['flash']); ?></p>
      <?php endif; ?>

        <section class="card notice-board" aria-labelledby="noticeBoardTitle">
          <div class="card__head">
            <div>
              <h2 class="card__title" id="noticeBoardTitle"> Notices &amp; Announcements</h2>
              <p class="notice-board__sub">Important updates shared by the Budget Pilot admin team.</p>
            </div>
          <?php if (!empty($data['notices'])): ?>
            <span class="notice-count"><?php echo count($data['notices']); ?> active</span>
          <?php endif; ?>
          </div>

          <?php if (!empty($data['notices'])): ?>
            <div class="notice-list">
              <?php foreach ($data['notices'] as $notice): ?>
                <article class="notice-item">
                  <div class="notice-item__icon" aria-hidden="true">!</div>
                  <div class="notice-item__body">
                    <div class="notice-item__top">
                      <h3><?php echo htmlspecialchars($notice->title); ?></h3>
                      <span class="notice-audience"><?php echo $notice->audience === 'all' ? 'Everyone' : 'Customers'; ?></span>
                    </div>
                    <p><?php echo nl2br(htmlspecialchars($notice->message)); ?></p>
                    <div class="notice-item__meta">
                      Posted <?php echo date('M j, Y', strtotime($notice->created_at)); ?>
                      <?php if (!empty($notice->expires_at)): ?>
                        <span></span> Available until <?php echo date('M j, Y', strtotime($notice->expires_at)); ?>
                      <?php endif; ?>
                    </div>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="notice-empty">No active notices for customers right now.</div>
          <?php endif; ?>
        </section>

      <!-- ===== 3 numbers ===== -->
      <div class="dash-3">
        <section class="kpi">
          <h2 class="kpi__label">Monthly income</h2>
          <strong class="kpi__value">$<?php echo number_format($data['total_income'] ?? 0, 2); ?></strong>
        </section>
        <section class="kpi">
          <h2 class="kpi__label">Total expenses</h2>
          <strong class="kpi__value">$<?php echo number_format($data['total_expenses'] ?? 0, 2); ?></strong>
        </section>
        <section class="kpi">
          <h2 class="kpi__label">Remaining budget</h2>
          <?php $remVal = $data['remaining_budget'] ?? 0; ?>
          <strong class="kpi__value <?php echo $remVal < 0 ? 'dashboard-rem-val is-over' : 'dashboard-rem-val is-ok'; ?>">$<?php echo number_format($remVal, 2); ?></strong>
        </section>
      </div>

      <div class="dash-2">
        <!-- ===== Spending pie chart (this month) ===== -->
        <section class="dashboard-pie-card">
          <h2 class="card__title">Spending by category</h2>
          <?php if (empty($slices)): ?>
            <p class="dashboard-empty-msg">No expenses this month yet. <a href="<?php echo URLROOT; ?>/customer/addExpense">Add an expense</a></p>
          <?php else: ?>
            <div class="dashboard-pie-chart-wrap">
              <div class="dashboard-pie-circle" style="background: conic-gradient(<?php echo implode(', ', $gradient); ?>);"></div>
              <ul class="dashboard-pie-list">
                <?php foreach ($slices as $sl): ?>
                  <li class="dashboard-pie-row">
                    <span class="dashboard-legend-dot" style="background: <?php echo $sl['colour']; ?>;"></span>
                    <span class="item-name"><?php echo htmlspecialchars($sl['name']); ?></span>
                    <strong><?php echo money($sl['total']); ?></strong>
                    <small><?php echo round($sl['percent']); ?>%</small>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>
        </section>

        <!-- ===== All transactions ===== -->
        <section class="dashboard-pie-card">
          <h2 class="card__title">Recent transactions</h2>
          <?php if (empty($data['transactions'])): ?>
            <p class="dashboard-empty-msg">Nothing recorded yet.</p>
          <?php else: ?>
            <div class="dashboard-tx-list">
              <?php foreach ($data['transactions'] as $t): ?>
                <div class="dashboard-tx-row">
                  <div>
                    <strong class="dashboard-tx-desc"><?php echo htmlspecialchars($t['description'] ?: $t['category']); ?></strong>
                    <small class="dashboard-tx-sub"><?php echo htmlspecialchars($t['category']); ?> &bull; <?php echo htmlspecialchars($t['person']); ?> &bull; <?php echo date('M j, Y', strtotime($t['transaction_date'])); ?></small>
                  </div>
                  <strong class="dashboard-tx-amount <?php echo $t['type'] == 'income' ? 'is-income' : 'is-expense'; ?>">
                    <?php echo ($t['type'] == 'income' ? '+' : '-') . money($t['amount']); ?>
                  </strong>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </section>
      </div>

<?php require APPROOT . '/Views/shared/footer.php'; ?>
