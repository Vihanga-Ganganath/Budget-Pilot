<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Manual Expense Entry — Budget Pilot</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/settings.css" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/budgets.css" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/expense.css" />
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
      <a class="navlink" href="<?php echo URLROOT; ?>/customer/index">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10.5L12 3l9 7.5"/><path d="M5.5 9.5V20h13V9.5"/><path d="M9.5 20v-6h5v6"/></svg>
        Home
      </a>
      <a class="navlink" href="<?php echo URLROOT; ?>/customer/dashboard">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1.6"/><rect x="14" y="3" width="7" height="7" rx="1.6"/><rect x="3" y="14" width="7" height="7" rx="1.6"/><rect x="14" y="14" width="7" height="7" rx="1.6"/></svg>
        Dashboard
      </a>
      <a class="navlink navlink--active" href="<?php echo URLROOT; ?>/customer/budgets" aria-current="page">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2.5" y="5" width="19" height="14" rx="3"/><path d="M2.5 10h19"/></svg>
        Budgets
      </a>
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
      <a class="backlink" href="<?php echo URLROOT; ?>/customer/budgets">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
        Back to Budget Setup
      </a>

      <h1 class="page__title">Manual Expense Entry</h1>
      <p class="page__sub">Update your copilot with recent transaction data.</p>

      <div class="entrylayout">
        <div class="entrylayout__main">
          <div id="entryList"></div>

          <button class="addmore" type="button" id="addEntryBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8.5v7M8.5 12h7"/></svg>
            Add another expense
          </button>
        </div>

        <aside class="entrylayout__side">
          <section class="card pickcard">
            <h2 class="eyebrow" id="pickHeading">Category selection</h2>
            <p class="pickcard__for" id="pickFor"></p>
            <div class="picks" id="pickGrid" role="group" aria-labelledby="pickHeading"></div>
            <p class="err" id="pickErr" role="alert"></p>
          </section>

          <section class="review">
            <span class="eyebrow eyebrow--onnavy">Final review</span>

            <div class="review__top">
              <span class="review__label">Total for all entries:</span>
              <strong class="review__total" id="reviewTotal">$0.00</strong>
            </div>

            <ul class="impact" id="impactList"></ul>
            <p class="review__empty" id="reviewEmpty">Enter an amount and pick a category to see the effect on your budget.</p>

            <button class="btn btn--primary review__save" type="button" id="saveExpenseBtn">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 3.5h11L20.5 8v12.5h-15z"/><path d="M8.5 3.5v5h7v-5M8.5 20.5V14h7v6.5"/></svg>
              Save expense
            </button>
          </section>
        </aside>
      </div>
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

<template id="entryTemplate">
  <div class="entry">
    <section class="card entry__card">
      <div class="entry__head">
        <h2 class="entry__title">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2.5" y="5" width="19" height="14" rx="3"/><path d="M2.5 10h19"/></svg>
          Transaction details
        </h2>
        <div class="entry__headright">
          <span class="chip entry__chip">No category</span>
          <button class="entry__drop" type="button">Remove</button>
        </div>
      </div>

      <div class="fieldrow">
        <div class="field">
          <label class="label entry__amountlabel">Amount</label>
          <div class="control control--money control--lg">
            <span class="control__prefix entry__symbol">$</span>
            <input class="entry__amount" type="number" min="0" step="0.01" placeholder="0.00" />
          </div>
        </div>
        <div class="field">
          <label class="label entry__datelabel">Date</label>
          <div class="control">
            <input class="entry__date" type="date" />
          </div>
        </div>
      </div>

      <label class="label entry__desclabel">Description</label>
      <div class="control">
        <input class="entry__desc" type="text" placeholder="e.g. Weekly grocery shopping" />
      </div>
    </section>

    <section class="card entry__card">
      <h2 class="entry__title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 6h11M4 11h11M4 16h7"/><path d="M17.5 14.5l3 3-3.5 1.2z"/></svg>
        Quick notes
      </h2>
      <div class="control control--area">
        <textarea class="entry__notes" rows="4" placeholder="Any additional context for your financial records..."></textarea>
      </div>
      <div class="tags entry__tags"></div>
    </section>
  </div>
</template>

<template id="pickTemplate">
  <button class="pick" type="button" aria-pressed="false">
    <span class="pick__art"><img class="pick__photo" src="" alt="" /></span>
    <span class="pick__name"></span>
    <span class="pick__tick" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7"/></svg>
    </span>
  </button>
</template>

<script>window.URLROOT = "<?php echo URLROOT; ?>";</script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/store.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/notify.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/household-ui.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/expense.js"></script>
</body>
</html>
