<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Dashboard — Budget Pilot</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/settings.css" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/dashboard.css" />
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
      <a class="navlink" href="index.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10.5L12 3l9 7.5"/><path d="M5.5 9.5V20h13V9.5"/><path d="M9.5 20v-6h5v6"/></svg>
        Home
      </a>
      <span class="navlink navlink--active" aria-current="page">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1.6"/><rect x="14" y="3" width="7" height="7" rx="1.6"/><rect x="3" y="14" width="7" height="7" rx="1.6"/><rect x="14" y="14" width="7" height="7" rx="1.6"/></svg>
        Dashboard
      </span>
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
      <a class="navlink" href="analytics.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 17l5-6 4 3 5-7 4 4"/></svg>
        Analytics
      </a>
      <a class="navlink" href="household.php" data-main-only>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3.2"/><path d="M2.5 20c.6-3.6 3.2-5.5 6.5-5.5s5.9 1.9 6.5 5.5"/><path d="M16.5 5.2a3.2 3.2 0 0 1 0 6"/><path d="M18 14.8c2 .7 3.2 2.4 3.5 5.2"/></svg>
        Household
      </a>
    </nav>

    <div class="sidebar__foot">
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

    <div class="content">
      <div class="dashhead">
        <div>
          <h1 class="page__title" id="greeting">Welcome back</h1>
          <p class="page__sub" id="greetingSub">Here is where your money stands.</p>
        </div>
        <div class="dashhead__actions">
          <a class="btn btn--primary dashbtn" href="expense.php">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8.5v7M8.5 12h7"/></svg>
            Add expense
          </a>
          <a class="btn btn--soft dashbtn" href="grocery.php">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9h18l-1.5 11H4.5z"/><path d="M3 9l1.5-4h15L21 9"/></svg>
            Smart grocery
          </a>
        </div>
      </div>

      <p class="hhnote" id="householdNote" hidden></p>

      <div class="kpis">
        <section class="kpi">
          <div class="kpi__top">
            <span class="kpi__icon kpi__icon--green" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="6" width="19" height="12" rx="2.5"/><circle cx="12" cy="12" r="2.6"/></svg>
            </span>
            <span class="pill" id="incomePill" hidden></span>
          </div>
          <h2 class="kpi__label">Monthly income</h2>
          <strong class="kpi__value" id="incomeValue">—</strong>
        </section>

        <section class="kpi">
          <div class="kpi__top">
            <span class="kpi__icon kpi__icon--red" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2z"/><path d="M9 8h6M9 12h6"/></svg>
            </span>
            <span class="pill" id="expensePill" hidden></span>
          </div>
          <h2 class="kpi__label" data-member-label="Household expenses">Total expenses</h2>
          <strong class="kpi__value" id="expenseValue">—</strong>
        </section>

        <section class="kpi">
          <div class="kpi__top">
            <span class="kpi__icon kpi__icon--blue" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v5h-4l-1 2H9l-1-2H4z"/><circle cx="16" cy="11" r="1"/></svg>
            </span>
            <span class="pill" id="remainingPill" hidden></span>
          </div>
          <h2 class="kpi__label">Remaining budget</h2>
          <strong class="kpi__value" id="remainingValue">—</strong>
        </section>

        <section class="kpi kpi--score" id="scoreCard">
          <div class="kpi__top">
            <span class="kpi__icon kpi__icon--onnavy" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2.5L4.5 13.5H11l-1 8 8.5-11H12z"/></svg>
            </span>
            <span class="pill pill--onnavy" id="scoreTier">—</span>
          </div>
          <h2 class="kpi__label">Budget health score</h2>
          <strong class="kpi__value" id="scoreValue">—<span class="kpi__of">/100</span></strong>
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
            No spending recorded yet. <a class="link" href="expense.php">Log an expense</a> and the trend starts here.
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
          <a class="link" href="expense.php">log an expense</a> or check out a grocery cart.
        </p>
      </section>
    </div>
  </div>
</div>

<div class="toast" id="toast" role="status" hidden></div>

<div class="modal" id="modal" hidden>
  <div class="modal__backdrop" id="modalBackdrop"></div>
  <div class="modal__box" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <h2 class="modal__title" id="modalTitle"></h2>
    <div class="modal__body" id="modalBody"></div>
    <div class="modal__actions" id="modalActions"></div>
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

<script src="<?php echo URLROOT; ?>/public/js/customer-js/store.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/notify.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/household-ui.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/dashboard.js"></script>
</body>
</html>
