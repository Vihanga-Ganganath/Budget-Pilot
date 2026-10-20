<?php require APPROOT . '/Views/shared/header.php'; ?>
<?php
// one colour per category for the pie chart
$colours = ['#16A34A', '#2563EB', '#DB2777', '#F59E0B', '#101C56', '#DC2626', '#0D9488', '#9CA3AF'];
$card    = "background: #fff; padding: 24px; border-radius: 14px; border: 1px solid #CBD5E1;";
function money($n) { return ($n < 0 ? '-' : '') . '$' . number_format(abs($n), 2); }

// build the pie: each category gets a slice from $start% to $end%
$slices = [];
$gradient = [];
$start  = 0;
foreach ($data['spending'] as $i => $c) {
    if ($c['total'] <= 0) continue;
    $percent = $c['total'] / $data['spent'] * 100;
    $colour  = $colours[$i % count($colours)];
    $slices[] = ['name' => $c['name'], 'total' => $c['total'], 'percent' => $percent, 'colour' => $colour];
    $gradient[] = $colour . ' ' . $start . '% ' . ($start + $percent) . '%';
    $start += $percent;
}
?>
<style>
    .dash-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 22px; }
    .dash-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    @media (max-width: 1080px) { .dash-3, .dash-2 { grid-template-columns: 1fr; } }
</style>

      <div class="dashboard-header">
        <div>
          <h1 class="page__title" id="greeting">Welcome back, <strong><?php echo htmlspecialchars($userName); ?></strong></h1>
          <p class="page__sub" id="greetingSub">Here is where your money stands this month.</p>
        </div>
      </div>

      <?php if (!empty($data['flash'])): ?>
        <p style="background: #DCFCE7; color: #166534; padding: 12px 16px; border-radius: 8px; margin: 0 0 20px;"><?php echo htmlspecialchars($data['flash']); ?></p>
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
          <strong class="kpi__value"><?php echo money($data['income']); ?></strong>
        </section>
        <section class="kpi">
          <h2 class="kpi__label">Total expenses</h2>
          <strong class="kpi__value"><?php echo money($data['spent']); ?></strong>
        </section>
        <section class="kpi">
          <h2 class="kpi__label">Remaining budget</h2>
          <strong class="kpi__value" style="color: <?php echo $data['remaining'] < 0 ? '#DC2626' : 'var(--color-success)'; ?>;"><?php echo money($data['remaining']); ?></strong>
        </section>
      </div>

      <div class="dash-2">
        <!-- ===== Spending pie chart (this month) ===== -->
        <section style="<?php echo $card; ?>">
          <h2 class="card__title">Spending by category</h2>
          <?php if (empty($slices)): ?>
            <p style="color: var(--color-text-muted);">No expenses this month yet. <a href="<?php echo URLROOT; ?>/customer/addExpense">Add an expense</a></p>
          <?php else: ?>
            <div style="display: flex; align-items: center; gap: 28px; flex-wrap: wrap; margin-top: 16px;">
              <div style="width: 200px; height: 200px; border-radius: 50%; flex: none; background: conic-gradient(<?php echo implode(', ', $gradient); ?>);"></div>
              <ul style="list-style: none; margin: 0; padding: 0; flex: 1; min-width: 200px;">
                <?php foreach ($slices as $sl): ?>
                  <li style="display: flex; align-items: center; gap: 10px; padding: 6px 0;">
                    <span style="width: 12px; height: 12px; border-radius: 3px; background: <?php echo $sl['colour']; ?>;"></span>
                    <span style="flex: 1;"><?php echo htmlspecialchars($sl['name']); ?></span>
                    <strong><?php echo money($sl['total']); ?></strong>
                    <small style="color: var(--color-text-muted); width: 44px; text-align: right;"><?php echo round($sl['percent']); ?>%</small>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>
        </section>

        <!-- ===== All transactions ===== -->
        <section style="<?php echo $card; ?>">
          <h2 class="card__title">Recent transactions</h2>
          <?php if (empty($data['transactions'])): ?>
            <p style="color: var(--color-text-muted);">Nothing recorded yet.</p>
          <?php else: ?>
            <div style="max-height: 360px; overflow-y: auto; margin-top: 8px;">
              <?php foreach ($data['transactions'] as $t): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                  <div>
                    <strong style="display: block;"><?php echo htmlspecialchars($t['description'] ?: $t['category']); ?></strong>
                    <small style="color: var(--color-text-muted);"><?php echo htmlspecialchars($t['category']); ?> &bull; <?php echo htmlspecialchars($t['person']); ?> &bull; <?php echo date('M j, Y', strtotime($t['transaction_date'])); ?></small>
                  </div>
                  <strong style="color: <?php echo $t['type'] == 'income' ? 'var(--color-success)' : '#DC2626'; ?>; white-space: nowrap;">
                    <?php echo ($t['type'] == 'income' ? '+' : '-') . money($t['amount']); ?>
                  </strong>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </section>
      </div>

<?php require APPROOT . '/Views/shared/footer.php'; ?>
