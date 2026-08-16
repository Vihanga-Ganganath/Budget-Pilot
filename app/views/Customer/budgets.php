<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Budget Setup — Budget Pilot</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/settings.css" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/budgets.css" />
</head>
<body class="settings">

<div class="shell">

  <aside class="sidebar">
    <a class="sidebar__brand" href="<?php echo URLROOT; ?>/customer/index">
      <span class="sidebar__mark" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none"><rect x="2.5" y="4.5" width="19" height="15" rx="3" stroke="#fff" stroke-width="2"/><rect x="6" y="8" width="6" height="8" rx="1.5" fill="#fff"/></svg>
      </span>
      <span class="sidebar__brandtext">
        <span class="sidebar__name">Budget Pilot</span>
        <span class="sidebar__tag">Smart Finance Copilot</span>
      </span>
    </a>

    <nav class="sidebar__nav" aria-label="Sections">
      <a class="navlink" href="<?php echo URLROOT; ?>/customer/index" id="homeLink">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10.5L12 3l9 7.5"/><path d="M5.5 9.5V20h13V9.5"/><path d="M9.5 20v-6h5v6"/></svg>
        Home
      </a>
      <a class="navlink" href="<?php echo URLROOT; ?>/customer/dashboard">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1.6"/><rect x="14" y="3" width="7" height="7" rx="1.6"/><rect x="3" y="14" width="7" height="7" rx="1.6"/><rect x="14" y="14" width="7" height="7" rx="1.6"/></svg>
        Dashboard
      </a>
      <span class="navlink navlink--active" aria-current="page">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2.5" y="5" width="19" height="14" rx="3"/><path d="M2.5 10h19"/></svg>
        Budgets
      </span>
      <a class="navlink" href="<?php echo URLROOT; ?>/customer/grocery">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9h18l-1.5 11H4.5z"/><path d="M3 9l1.5-4h15L21 9"/></svg>
        Grocery Catalog
      </a>
      <a class="navlink" href="<?php echo URLROOT; ?>/customer/cart">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="21" r="1.5"/><circle cx="18" cy="21" r="1.5"/><path d="M2.5 3h2.2l2.6 12.4a1.6 1.6 0 0 0 1.6 1.3h8.9a1.6 1.6 0 0 0 1.6-1.3L21 7H6"/></svg>
        Shopping Cart
      </a>
      <a class="navlink" href="<?php echo URLROOT; ?>/customer/analytics">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 17l5-6 4 3 5-7 4 4"/></svg>
        Analytics
      </a>
      <a class="navlink" href="<?php echo URLROOT; ?>/customer/household" data-main-only>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3.2"/><path d="M2.5 20c.6-3.6 3.2-5.5 6.5-5.5s5.9 1.9 6.5 5.5"/><path d="M16.5 5.2a3.2 3.2 0 0 1 0 6"/><path d="M18 14.8c2 .7 3.2 2.4 3.5 5.2"/></svg>
        Household
      </a>
    </nav>

    <div class="sidebar__foot">
      <a class="navlink" href="<?php echo URLROOT; ?>/customer/login" id="loginLink">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="3"/><path d="M8 9.5h8M8 14h5"/></svg>
        Sign-in page
      </a>
      <a class="navlink" href="<?php echo URLROOT; ?>/customer/settings">
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
        <a class="icon-btn" href="<?php echo URLROOT; ?>/customer/notifications" data-bell aria-label="Notifications">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>
        </a>
        <a class="icon-btn" href="<?php echo URLROOT; ?>/customer/cart" aria-label="Shopping cart">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1.6"/><circle cx="19" cy="21" r="1.6"/><path d="M2.5 3h2.2l2.6 12.4a1.6 1.6 0 0 0 1.6 1.3h8.9a1.6 1.6 0 0 0 1.6-1.3L21 7H6"/></svg>
        </a>
        <span class="appbar__avatar" id="appbarAvatar" aria-hidden="true"></span>
      </div>
    </header>

    <div class="content">
      <h1 class="page__title">Budget Setup</h1>
      <p class="page__sub">Configure your monthly spending limits and let Budget Pilot optimize your finances.</p>

      <p class="hhnote" id="householdNote" hidden></p>

      <div class="setup">
        <section class="card">
          <label class="label" for="income" id="incomeLabel">Estimated monthly income</label>
          <div class="control control--money control--lg">
            <span class="control__prefix" id="incomeSymbol">$</span>
            <input id="income" type="number" min="0" step="1" placeholder="5000" data-member-lock />
          </div>
          <p class="err" id="incomeErr" role="alert"></p>

          <!-- shown once there is more than one person on the account -->
          <div class="salaries" id="salaries" hidden>
            <h3 class="salaries__title">Who earns what</h3>
            <p class="salaries__hint" id="salariesHint">
              Enter each person's monthly salary. Together they make the income the plan is built on.
            </p>
            <ul class="salaries__list" id="salaryList"></ul>
            <p class="salaries__total">
              <span>Family income</span>
              <strong id="familyIncome">$0</strong>
            </p>
          </div>

          <div class="setup__actions">
            <button class="btn btn--primary" type="button" id="suggestBtn" data-member-lock>
              Auto-Suggest Budget
            </button>
            <button class="btn btn--soft" type="button" id="saveBtn" data-member-lock>Save Configuration</button>
          </div>
          <p class="setup__note" id="savedNote"></p>
        </section>

        <section class="card card--navy total">
          <span class="total__label">Total planned</span>
          <strong class="total__value" id="totalPlanned">$0</strong>
          <p class="total__hint" id="totalHint">Set your income, then auto-suggest a plan.</p>
          <div class="total__bar"><span class="total__fill" id="totalFill"></span></div>
          <p class="total__chip" id="unallocated" hidden>
            <span class="total__chiplabel" id="unallocatedLabel">Unallocated</span>
            <strong class="total__chipvalue" id="unallocatedValue">$0</strong>
          </p>
        </section>
      </div>

      <ul class="grid" id="categoryGrid"></ul>

      <footer class="planbar">
        <p class="planbar__note">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l7 3v6c0 4.6-3 8-7 9-4-1-7-4.4-7-9V6z"/><path d="M9.5 12l1.8 1.8L15 10"/></svg>
          All entries are encrypted. Budget Pilot never shares your personal financial data with third-party advertisers.
        </p>
        <button class="btn btn--primary btn--lg" type="button" id="finalizeBtn" data-member-lock>Finalize Monthly Plan</button>
      </footer>
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

<template id="categoryTemplate">
  <li class="cat">
    <div class="cat__art">
      <img class="cat__photo" src="" alt="" />
    </div>
    <div class="cat__body">
      <div class="cat__head">
        <h3 class="cat__name"></h3>
        <div class="control control--money cat__input">
          <span class="control__prefix cat__symbol">$</span>
          <input class="cat__amount" type="number" min="0" step="1" />
        </div>
      </div>
      <div class="cat__meta">
        <span class="cat__metacol"><span class="cat__metalabel">Suggested</span><strong class="cat__suggested">—</strong></span>
        <span class="cat__metacol cat__metacol--right"><span class="cat__metalabel">Spent</span><strong class="cat__spent">$0</strong></span>
      </div>
      <div class="cat__bar"><span class="cat__fill"></span></div>
      <div class="cat__foot">
        <span class="cat__used">0% used</span>
        <span class="cat__left">$0 left</span>
      </div>
    </div>
  </li>
</template>

<script>window.URLROOT = "<?php echo URLROOT; ?>";</script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/store.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/notify.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/household-ui.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/budgets.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/budget-hooks.js"></script>
</body>
</html>
