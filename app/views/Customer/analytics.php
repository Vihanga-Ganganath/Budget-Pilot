
<?php require APPROOT . '/Views/shared/header.php'; ?>
<div class="content analytics-page">

      <header class="analytics-head">
        <div>
          <h1 class="analytics-head__title">Monthly Summary Report</h1>
          <p class="analytics-head__range" id="reportRange">&nbsp;</p>
        </div>
        <div class="analytics-head__actions">
          <button class="analytics-btn" type="button" id="pdfBtn">
            
            Download PDF
          </button>
          <button class="analytics-btn analytics-btn--navy" type="button" id="csvBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M7 10h4M7 14h7"/></svg>
            Export CSV
          </button>
        </div>
      </header>

      <div class="analytics-row analytics-row--top">

        <section class="analytics-card analytics-progress" aria-labelledby="progressTitle">
          <div class="analytics-card__head">
            <h2 class="analytics-card__title" id="progressTitle">Total spent vs budget</h2>
            <span class="analytics-chip" id="paceChip"></span>
          </div>

          <div class="analytics-progress__grid">
            <div class="analytics-progress__main">
              <div class="analytics-progress__line">
                <span class="analytics-progress__label">Monthly progress</span>
                <span class="analytics-progress__figures" id="progressFigures"></span>
              </div>
              <div class="analytics-track"><span class="analytics-track__fill" id="progressFill"></span></div>
              <p class="analytics-progress__copy" id="progressCopy"></p>
            </div>

            <div class="analytics-progress__side">
              <div class="analytics-stat">
                <span class="analytics-stat__label">Remaining</span>
                <strong class="analytics-stat__value" id="remainingValue"></strong>
              </div>
              <div class="analytics-stat analytics-stat--good" id="projectionStat">
                <span class="analytics-stat__label" id="projectionLabel">Projected surplus</span>
                <strong class="analytics-stat__value" id="projectionValue"></strong>
              </div>
            </div>
          </div>
        </section>

        <section class="analytics-card analytics-card--navy analytics-savings" aria-labelledby="savingsTitle">
          <h2 class="analytics-card__title analytics-card__title--light" id="savingsTitle">Savings achieved</h2>
          <p class="analytics-savings__sub" id="savingsSub">This month</p>
          <strong class="analytics-savings__value" id="savingsValue"></strong>
          <p class="analytics-savings__delta" id="savingsDelta"></p>
          <p class="analytics-savings__note" id="savingsNote"></p>
        </section>

      </div>

      <div class="analytics-row analytics-row--mid">

        <section class="analytics-card analytics-cats" aria-labelledby="catsTitle">
          <h2 class="analytics-card__title" id="catsTitle">Top spending categories</h2>
          <ul class="analytics-cats__list" id="topCategories"></ul>
          <p class="analytics-empty" id="catsEmpty" hidden>Nothing logged yet this month. Record an expense on the Budgets page and your categories will rank here.</p>
        </section>

        <section class="analytics-card analytics-card--dashed analytics-insights" aria-labelledby="insightsTitle">
          <h2 class="analytics-card__title analytics-insights__title" id="insightsTitle">
            <span class="analytics-dot" aria-hidden="true"></span>Smart insights
          </h2>
          <ul class="analytics-insights__list" id="insightsList"></ul>
        </section>

      </div>

      <h2 class="analytics-section">Brand popularity comparison</h2>
      <section class="analytics-card analytics-brands">
        <div class="analytics-brands__grid">
          <div class="analytics-brands__main">
            <p class="analytics-brands__lead" id="brandsLead">&nbsp;</p>
            <ul class="analytics-brands__list" id="brandList"></ul>
            <p class="analytics-empty" id="brandsEmpty" hidden>No shopping lists saved yet. Check out a basket from the cart and the brands in it show up here.</p>
          </div>
          <aside class="analytics-market">
            <h3 class="analytics-market__title">
              
              Market insight
            </h3>
            <p class="analytics-market__copy" id="marketInsight"></p>
          </aside>
        </div>
      </section>

      <h2 class="analytics-section">Significant transactions</h2>
      <p class="analytics-scope" id="txScope" hidden></p>
      <section class="analytics-card analytics-tx">
        <table class="analytics-table">
          <thead>
            <tr>
              <th scope="col">Date</th>
              <th scope="col">Merchant / service</th>
              <th scope="col">Category</th>
              <th scope="col" class="analytics-table__num">Amount</th>
            </tr>
          </thead>
          <tbody id="txBody"></tbody>
        </table>
        <p class="analytics-empty" id="txEmpty" hidden>No transactions recorded this month. Log one from a category on the Budgets page.</p>
      </section>

    <div class="toast" id="toast" role="status" hidden></div>

<script>window.URLROOT = "<?php echo URLROOT; ?>";</script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/store.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/store.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/orders.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/orders.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/notify.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/notify.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/household-ui.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/household-ui.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/analytics.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/analytics.js'); ?>"></script>

<?php require APPROOT . '/Views/shared/footer.php'; ?>







