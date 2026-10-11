
<?php require APPROOT . '/Views/shared/header.php'; ?>

      <div class="dashboard-header">
        <div>
          <h1 class="page__title" id="greeting">Welcome back, <strong><?php echo htmlspecialchars($userName); ?></strong> </h1>
          <p class="page__sub" id="greetingSub">Here is where your money stands.</p>
        </div>
        <div class="dashboard-header__actions">
          <a class="btn btn--primary dashboard-btn" href="<?php echo URLROOT; ?>/customer/addExpense">
            
            Add expense
          </a>
          <a class="btn btn--soft dashboard-btn" href="<?php echo URLROOT; ?>/customer/grocery">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9h18l-1.5 11H4.5z"/><path d="M3 9l1.5-4h15L21 9"/></svg>
            Smart grocery
          </a>
        </div>
      </div>

      <p class="hhnote" id="householdNote" hidden></p>
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
      <div class="kpis">
        <section class="kpi">
          <div class="kpi__top">
            
            <span class="pill" id="incomePill" hidden></span>
          </div>
          <h2 class="kpi__label">Monthly income</h2>
          <strong class="kpi__value" id="incomeValue"></strong>
        </section>

        <section class="kpi">
          <div class="kpi__top">
            
            <span class="pill" id="expensePill" hidden></span>
          </div>
          <h2 class="kpi__label" data-member-label="Household expenses">Total expenses</h2>
          <strong class="kpi__value" id="expenseValue"></strong>
        </section>

        <section class="kpi">
          <div class="kpi__top">
            
            <span class="pill" id="remainingPill" hidden></span>
          </div>
          <h2 class="kpi__label">Remaining budget</h2>
          <strong class="kpi__value" id="remainingValue"></strong>
        </section>

        <section class="kpi kpi--score" id="scoreCard">
          <div class="kpi__top">
            
            <span class="pill pill--onnavy" id="scoreTier"></span>
          </div>
          <h2 class="kpi__label">Budget health score</h2>
          <strong class="kpi__value" id="scoreValue"><span class="kpi__of">/100</span></strong>
          <button class="kpi__why" type="button" id="scoreWhy">How is this worked out?</button>
        </section>
      </div>

      <div class="panels">
        <section class="card trend">
          <div class="card__head">
            <h2 class="card__title" data-member-label="Household spending trend">Spending trend</h2>
            <div class="rangetoggle" role="group" aria-label="Trend range">
              <button class="rangetoggle__btn" type="button" data-range="weekly">Weekly</button>
              <button class="rangetoggle__btn is-on" type="button" data-range="monthly">Monthly</button>
            </div>
          </div>
          <div class="trend__plot" id="trendPlot"></div>
          <p class="empty" id="trendEmpty" hidden>
            No spending recorded yet. <a class="link" href="<?php echo URLROOT; ?>/customer/addExpense">Log an expense</a> and the trend starts here.
          </p>
        </section>

        <section class="card bycat">
          <h2 class="card__title" data-member-label="Household by category">By category</h2>
          <div class="donutwrap" id="donutWrap"></div>
          <ul class="legend" id="legend"></ul>
          <p class="empty" id="catEmpty" hidden>Nothing spent against your categories yet.</p>
        </section>
      </div>

      <section class="card tx">
        <div class="card__head">
          <h2 class="card__title" data-member-label="Your recent transactions">Recent transactions</h2>
          <button class="tx__all" type="button" id="viewAllBtn">View all</button>
        </div>
        <ul class="txlist" id="txList"></ul>
        <p class="empty" id="txEmpty" hidden>
          Nothing recorded yet. Spending shows up here as soon as you
          <a class="link" href="<?php echo URLROOT; ?>/customer/addExpense">log an expense</a> or check out a grocery cart.
        </p>
      </section>
    <div class="toast" id="toast" role="status" hidden></div>

<div class="modal" id="modal" hidden>
  <div class="modal__backdrop" id="modalBackdrop"></div>
  <div class="modal__box" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <h2 class="modal__title" id="modalTitle"></h2>
    <div class="modal__body" id="modalBody"></div>
    <div class="modal__actions" id="modalActions">
      </div>
    </div>
  </div>

  <template id="txTemplate">
  <li class="txrow">
    <span class="txrow__icon" aria-hidden="true"></span>
    <div class="txrow__text">
      <h3 class="txrow__name"></h3>
      <p class="txrow__meta"></p>
    </div>
    <strong class="txrow__amount"></strong>
  </li>
</template>

<script>window.URLROOT = "<?php echo URLROOT; ?>";</script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/store.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/store.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/notify.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/notify.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/household-ui.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/household-ui.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/dashboard.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/dashboard.js'); ?>"></script>

<?php require APPROOT . '/Views/shared/footer.php'; ?>










