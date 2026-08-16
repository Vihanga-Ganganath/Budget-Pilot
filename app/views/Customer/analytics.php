<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Analytics — Budget Pilot</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/settings.css" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/analytics.css" />
</head>
<body class="settings">

<div class="shell">

  <aside class="sidebar">
    <a class="sidebar__brand" href="index.php">
      <span class="sidebar__mark" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none"><rect x="2.5" y="4.5" width="19" height="15" rx="3" stroke="#fff" stroke-width="2"/><rect x="6" y="8" width="6" height="8" rx="1.5" fill="#fff"/></svg>
      </span>
      <span class="sidebar__brandtext">
        <span class="sidebar__name">Budget Pilot</span>
        <span class="sidebar__tag">Smart Finance Copilot</span>
      </span>
    </a>

    <nav class="sidebar__nav" aria-label="Sections">
      <a class="navlink" href="index.php" id="homeLink">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10.5L12 3l9 7.5"/><path d="M5.5 9.5V20h13V9.5"/><path d="M9.5 20v-6h5v6"/></svg>
        Home
      </a>
      <a class="navlink" href="dashboard.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1.6"/><rect x="14" y="3" width="7" height="7" rx="1.6"/><rect x="3" y="14" width="7" height="7" rx="1.6"/><rect x="14" y="14" width="7" height="7" rx="1.6"/></svg>
        Dashboard
      </a>
      <a class="navlink" href="budgets.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2.5" y="5" width="19" height="14" rx="3"/><path d="M2.5 10h19"/></svg>
        Budgets
      </a>
      <a class="navlink" href="grocery.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9h18l-1.5 11H4.5z"/><path d="M3 9l1.5-4h15L21 9"/></svg>
        Grocery Catalog
      </a>
      <a class="navlink" href="cart.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="21" r="1.5"/><circle cx="18" cy="21" r="1.5"/><path d="M2.5 3h2.2l2.6 12.4a1.6 1.6 0 0 0 1.6 1.3h8.9a1.6 1.6 0 0 0 1.6-1.3L21 7H6"/></svg>
        Shopping Cart
      </a>
      <span class="navlink navlink--active" aria-current="page">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 17l5-6 4 3 5-7 4 4"/></svg>
        Analytics
      </span>
      <a class="navlink" href="household.php" data-main-only>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3.2"/><path d="M2.5 20c.6-3.6 3.2-5.5 6.5-5.5s5.9 1.9 6.5 5.5"/><path d="M16.5 5.2a3.2 3.2 0 0 1 0 6"/><path d="M18 14.8c2 .7 3.2 2.4 3.5 5.2"/></svg>
        Household
      </a>
    </nav>

    <div class="sidebar__foot">
      <a class="navlink" href="customer-login.php" id="loginLink">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="3"/><path d="M8 9.5h8M8 14h5"/></svg>
        Sign-in page
      </a>
      <a class="navlink" href="settings.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3.2"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-2.9 1.2v.2a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-3-1.2l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0-1.2-2.9H3a2 2 0 1 1 0-4h.1A1.7 1.7 0 0 0 4.3 6l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 2.9-1.2V2a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 3 1.2l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0 1.2 2.9H22a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/></svg>
        Settings
      </a>
      <button class="navlink" type="button" id="logoutBtn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/></svg>
        Logout
      </button>
    </div>
  </aside>

  <div class="main">
    <header class="appbar">
      <div class="appbar__actions">
        <a class="icon-btn" href="notifications.php" data-bell aria-label="Notifications">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>
        </a>
        <a class="icon-btn" href="cart.php" aria-label="Shopping cart">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1.6"/><circle cx="19" cy="21" r="1.6"/><path d="M2.5 3h2.2l2.6 12.4a1.6 1.6 0 0 0 1.6 1.3h8.9a1.6 1.6 0 0 0 1.6-1.3L21 7H6"/></svg>
        </a>
        <span class="appbar__avatar" id="appbarAvatar" aria-hidden="true"></span>
      </div>
    </header>

    <div class="content an">

      <header class="an-head">
        <div>
          <h1 class="an-head__title">Monthly Summary Report</h1>
          <p class="an-head__range" id="reportRange">&nbsp;</p>
        </div>
        <div class="an-head__actions">
          <button class="an-btn" type="button" id="pdfBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12"/><path d="M8 11l4 4 4-4"/><path d="M4 20h16"/></svg>
            Download PDF
          </button>
          <button class="an-btn an-btn--navy" type="button" id="csvBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M7 10h4M7 14h7"/></svg>
            Export CSV
          </button>
        </div>
      </header>

      <div class="an-row an-row--top">

        <section class="an-card an-progress" aria-labelledby="progressTitle">
          <div class="an-card__head">
            <h2 class="an-card__title" id="progressTitle">Total spent vs budget</h2>
            <span class="an-chip" id="paceChip">—</span>
          </div>

          <div class="an-progress__grid">
            <div class="an-progress__main">
              <div class="an-progress__line">
                <span class="an-progress__label">Monthly progress</span>
                <span class="an-progress__figures" id="progressFigures">—</span>
              </div>
              <div class="an-track"><span class="an-track__fill" id="progressFill"></span></div>
              <p class="an-progress__copy" id="progressCopy"></p>
            </div>

            <div class="an-progress__side">
              <div class="an-stat">
                <span class="an-stat__label">Remaining</span>
                <strong class="an-stat__value" id="remainingValue">—</strong>
              </div>
              <div class="an-stat an-stat--good" id="projectionStat">
                <span class="an-stat__label" id="projectionLabel">Projected surplus</span>
                <strong class="an-stat__value" id="projectionValue">—</strong>
              </div>
            </div>
          </div>
        </section>

        <section class="an-card an-card--navy an-savings" aria-labelledby="savingsTitle">
          <h2 class="an-card__title an-card__title--light" id="savingsTitle">Savings achieved</h2>
          <p class="an-savings__sub" id="savingsSub">This month</p>
          <strong class="an-savings__value" id="savingsValue">—</strong>
          <p class="an-savings__delta" id="savingsDelta"></p>
          <p class="an-savings__note" id="savingsNote"></p>
        </section>

      </div>

      <div class="an-row an-row--mid">

        <section class="an-card an-cats" aria-labelledby="catsTitle">
          <h2 class="an-card__title" id="catsTitle">Top spending categories</h2>
          <ul class="an-cats__list" id="topCategories"></ul>
          <p class="an-empty" id="catsEmpty" hidden>Nothing logged yet this month. Record an expense on the Budgets page and your categories will rank here.</p>
        </section>

        <section class="an-card an-card--dashed an-insights" aria-labelledby="insightsTitle">
          <h2 class="an-card__title an-insights__title" id="insightsTitle">
            <span class="an-dot" aria-hidden="true"></span>Smart insights
          </h2>
          <ul class="an-insights__list" id="insightsList"></ul>
        </section>

      </div>

      <h2 class="an-section">Brand popularity comparison</h2>
      <section class="an-card an-brands">
        <div class="an-brands__grid">
          <div class="an-brands__main">
            <p class="an-brands__lead" id="brandsLead">&nbsp;</p>
            <ul class="an-brands__list" id="brandList"></ul>
            <p class="an-empty" id="brandsEmpty" hidden>No shopping lists saved yet. Check out a basket from the cart and the brands in it show up here.</p>
          </div>
          <aside class="an-market">
            <h3 class="an-market__title">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg>
              Market insight
            </h3>
            <p class="an-market__copy" id="marketInsight"></p>
          </aside>
        </div>
      </section>

      <h2 class="an-section">Significant transactions</h2>
      <p class="an-scope" id="txScope" hidden></p>
      <section class="an-card an-tx">
        <table class="an-table">
          <thead>
            <tr>
              <th scope="col">Date</th>
              <th scope="col">Merchant / service</th>
              <th scope="col">Category</th>
              <th scope="col" class="an-table__num">Amount</th>
            </tr>
          </thead>
          <tbody id="txBody"></tbody>
        </table>
        <p class="an-empty" id="txEmpty" hidden>No transactions recorded this month. Log one from a category on the Budgets page.</p>
      </section>

    </div>
  </div>
</div>

<div class="toast" id="toast" role="status" hidden></div>

<script src="<?php echo URLROOT; ?>/public/js/customer-js/store.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/orders.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/notify.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/household-ui.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/analytics.js"></script>
</body>
</html>
