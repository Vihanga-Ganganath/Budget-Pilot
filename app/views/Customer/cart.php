<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Shopping Cart — Budget Pilot</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/settings.css" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/grocery.css" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/cart.css" />
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
      <span class="navlink navlink--active" aria-current="page">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="21" r="1.5"/><circle cx="18" cy="21" r="1.5"/><path d="M2.5 3h2.2l2.6 12.4a1.6 1.6 0 0 0 1.6 1.3h8.9a1.6 1.6 0 0 0 1.6-1.3L21 7H6"/></svg>
        Shopping Cart
        <span class="navlink__count" id="navCount" hidden>0</span>
      </span>
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
        <span class="icon-btn icon-btn--badged" aria-label="Shopping cart">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1.6"/><circle cx="19" cy="21" r="1.6"/><path d="M2.5 3h2.2l2.6 12.4a1.6 1.6 0 0 0 1.6 1.3h8.9a1.6 1.6 0 0 0 1.6-1.3L21 7H6"/></svg>
          <span class="icon-btn__badge" id="cartBadge" hidden>0</span>
        </span>
        <span class="appbar__avatar" id="appbarAvatar" aria-hidden="true"></span>
      </div>
    </header>

    <div class="content">
      <div class="carthead">
        <div>
          <h1 class="page__title">Shopping Cart</h1>
          <p class="page__sub">Review your groceries and their impact on your budget.</p>
        </div>
        <div class="progress" id="progressCard">
          <div class="progress__left">
            <span class="progress__label">Budget progress <strong id="progressPercent">0%</strong></span>
            <div class="progress__bar"><span class="progress__fill" id="progressFill"></span></div>
          </div>
          <div class="progress__right">
            <span class="progress__label">Projected spend</span>
            <strong class="progress__value" id="projectedSpend">—</strong>
          </div>
        </div>
      </div>

      <section class="alert" id="overAlert" hidden>
        <span class="alert__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3.5L21.5 20h-19z"/><path d="M12 10v4M12 17.2v.1"/></svg>
        </span>
        <div class="alert__text">
          <h2 class="alert__title">You are exceeding your grocery budget</h2>
          <p class="alert__body" id="alertBody"></p>
        </div>
        <button class="btn btn--danger" type="button" id="optimizeBtn">Optimization Tools</button>
      </section>

      <section class="notice notice--plain" id="noBudget" hidden>
        No grocery budget set yet. <a class="link" href="budgets.php">Set one on the Budgets page</a> to track this cart against it.
      </section>

      <div class="cartlayout">
        <div class="cartlayout__main">
          <section class="card items">
            <div class="items__head">
              <h2 class="card__title" id="itemsTitle">Itemized list</h2>
              <button class="items__clear" type="button" id="clearBtn">Clear all</button>
            </div>
            <ul class="lines" id="lineList"></ul>
            <p class="empty" id="emptyCart" hidden>
              Your cart is empty. <a class="link" href="grocery.php">Browse the catalog</a> to add something.
            </p>
          </section>

          <section id="swapSection" hidden>
            <h2 class="swaps__title">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a9 9 0 0 1 0 18 9 9 0 0 1 0-18z"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/></svg>
              Recommended cheaper brands
            </h2>
            <div class="swaps" id="swapList"></div>
          </section>
        </div>

        <aside class="cartlayout__side">
          <section class="card summary">
            <h2 class="card__title">Order summary</h2>
            <div class="summary__row">
              <span id="subtotalLabel">Subtotal</span>
              <span id="subtotal">—</span>
            </div>
            <div class="summary__row summary__row--total">
              <span>Total</span>
              <strong id="total">—</strong>
            </div>

            <div class="budgetstate" id="budgetState">
              <p class="budgetstate__head" id="budgetHead"></p>
              <p class="budgetstate__body" id="budgetBody"></p>
              <div class="progress__bar"><span class="progress__fill" id="stateFill"></span></div>
            </div>

            <div class="summary__foot">
              <button class="btn btn--primary summary__checkout" type="button" id="checkoutBtn">
                Checkout
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
              </button>
              <p class="summary__fine">Checking out records this spend against your grocery budget.</p>
            </div>
          </section>

          <section class="savings" id="savingsCard" hidden>
            <h2 class="savings__title">Savings potential</h2>
            <p class="savings__text">Switch to the cheaper brands below and you would save:</p>
            <p class="savings__value" id="savingsValue">—</p>
            <p class="savings__pill" id="savingsPill"></p>
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

<template id="lineTemplate">
  <li class="line">
    <a class="line__art" href="#"><img class="line__photo" src="" alt="" /></a>
    <div class="line__text">
      <a class="line__name" href="#"></a>
      <p class="line__meta"></p>
      <div class="qty">
        <button class="qty__btn line__minus" type="button" aria-label="One less">&minus;</button>
        <span class="line__qty">1</span>
        <button class="qty__btn line__plus" type="button" aria-label="One more">+</button>
      </div>
    </div>
    <div class="line__right">
      <strong class="line__price"></strong>
      <span class="line__unit"></span>
      <button class="line__remove" type="button">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M9 7V5h6v2M6 7l1 13h10l1-13"/></svg>
        Remove
      </button>
    </div>
  </li>
</template>

<template id="swapTemplate">
  <article class="swap">
    <span class="swap__art"><img class="swap__photo" src="" alt="" /></span>
    <div class="swap__text">
      <p class="swap__from"></p>
      <h3 class="swap__to"></h3>
      <p class="swap__price"><strong class="swap__amount"></strong> <span class="swap__save"></span></p>
      <button class="btn btn--primary swap__btn" type="button">Swap &amp; save</button>
    </div>
  </article>
</template>

<script src="<?php echo URLROOT; ?>/public/js/customer-js/store.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/orders.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/catalog-data.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/notify.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/household-ui.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/cart.js"></script>
</body>
</html>
