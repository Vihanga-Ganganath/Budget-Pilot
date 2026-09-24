<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Household — Budget Pilot</title>
<link rel="icon" type="image/png" href="<?php echo URLROOT; ?>/public/assets/logos/favicon.png?v=2">
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/settings.css?v=<?php echo @filemtime(APPROOT . '/../public/css/customer-css/settings.css'); ?>" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/household.css?v=<?php echo @filemtime(APPROOT . '/../public/css/customer-css/household.css'); ?>" />
</head>
<body class="settings">

<div class="shell">

  <aside class="sidebar" id="sidebar" aria-label="Main menu">
    <div class="sidebar__head">
      <a class="sidebar__brand" href="<?php echo URLROOT; ?>/customer/index">
        <span class="sidebar__mark" aria-hidden="true">
          <img src="<?php echo URLROOT; ?>/public/assets/logos/budget-pilot-mark.png" alt="">
        </span>
        <span class="sidebar__brandtext">
          <span class="sidebar__name">Budget Pilot</span>
          <span class="sidebar__tag">Smart Finance Copilot</span>
        </span>
      </a>
      <button class="sidebar__close" type="button" data-nav-close aria-label="Close menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
      </button>
    </div>

    <nav class="sidebar__nav" aria-label="Sections">
      <a class="navlink" href="<?php echo URLROOT; ?>/customer/index">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10.5L12 3l9 7.5"/><path d="M5.5 9.5V20h13V9.5"/><path d="M9.5 20v-6h5v6"/></svg>
        Home
      </a>
      <a class="navlink" href="<?php echo URLROOT; ?>/customer/dashboard">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1.6"/><rect x="14" y="3" width="7" height="7" rx="1.6"/><rect x="3" y="14" width="7" height="7" rx="1.6"/><rect x="14" y="14" width="7" height="7" rx="1.6"/></svg>
        Dashboard
      </a>
      <a class="navlink" href="<?php echo URLROOT; ?>/customer/budgets">
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
      <span class="navlink navlink--active" aria-current="page" data-main-only>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3.2"/><path d="M2.5 20c.6-3.6 3.2-5.5 6.5-5.5s5.9 1.9 6.5 5.5"/><path d="M16.5 5.2a3.2 3.2 0 0 1 0 6"/><path d="M18 14.8c2 .7 3.2 2.4 3.5 5.2"/></svg>
        Household
      </span>
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
      <button class="appbar__menu" type="button" data-nav-toggle aria-controls="sidebar" aria-expanded="false" aria-label="Open menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
      </button>
      <a class="appbar__brand" href="<?php echo URLROOT; ?>/customer/index">
        <img src="<?php echo URLROOT; ?>/public/assets/logos/budget-pilot-mark.png" alt="">
        <span>Budget Pilot</span>
      </a>
      <div class="appbar__actions">
        <a class="icon-btn" href="<?php echo URLROOT; ?>/customer/notifications" data-bell aria-label="Notifications">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>
        </a>
        <a class="icon-btn" href="<?php echo URLROOT; ?>/customer/cart" aria-label="Shopping cart">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1.6"/><circle cx="19" cy="21" r="1.6"/><path d="M2.5 3h2.2l2.6 12.4a1.6 1.6 0 0 0 1.6 1.3h8.9a1.6 1.6 0 0 0 1.6-1.3L21 7H6"/></svg>
        </a>
        <a class="appbar__avatar" href="<?php echo URLROOT; ?>/customer/settings" id="appbarAvatar" aria-label="Your profile"></a>
      </div>
    </header>

    <div class="content" id="content">

      <!-- shown to anyone who is not the account holder -->
      <section class="denied" id="denied" hidden>
        <h1 class="page__title">Household</h1>
        <p class="page__sub">
          Only the account holder can see everyone's transactions. Your own
          spending is on your dashboard, along with what's left of the shared budget.
        </p>
        <a class="btn btn--primary" href="<?php echo URLROOT; ?>/customer/dashboard">Back to your dashboard</a>
      </section>

      <div id="holderView" hidden>
        <div class="hhead">
          <div>
            <h1 class="page__title">Household</h1>
            <p class="page__sub" id="pageSub">Everyone on this account, and what they have spent.</p>
          </div>
          <button class="btn btn--primary" type="button" id="addMemberBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8.5v7M8.5 12h7"/></svg>
            Add family member
          </button>
        </div>

        <section class="hhsum">
          <div class="hhsum__box">
            <h2 class="hhsum__label">Household budget</h2>
            <strong class="hhsum__value" id="sumPlanned">—</strong>
          </div>
          <div class="hhsum__box">
            <h2 class="hhsum__label">Spent by everyone</h2>
            <strong class="hhsum__value" id="sumSpent">—</strong>
          </div>
          <div class="hhsum__box">
            <h2 class="hhsum__label">Left this month</h2>
            <strong class="hhsum__value" id="sumLeft">—</strong>
          </div>
        </section>

        <section class="card">
          <h2 class="card__title">People on this account</h2>
          <p class="card__hint" id="peopleStatus" role="status">Loading members…</p>
          <ul class="people" id="people"></ul>
          <p class="empty" id="peopleEmpty" hidden>
            It's just you so far. Add a family member and their spending joins this budget.
          </p>
        </section>

        <section class="card">
          <div class="card__head">
            <h2 class="card__title">All transactions</h2>
            <p class="card__hint">Everything logged on this account, whoever logged it.</p>
          </div>

          <div class="chips" id="whoChips" role="group" aria-label="Filter by person"></div>

          <ul class="feed" id="feed"></ul>
          <p class="empty" id="feedEmpty" hidden>
            Nothing logged yet. Expenses and grocery checkouts land here as soon as anyone
            records one.
          </p>
        </section>
      </div>
    </div>
  </div>
</div>

<div class="toast" id="toast" role="status" hidden></div>

<!-- Add / Edit member (one form for both Create and Update) -->
<div class="modal" id="memberModal" hidden>
  <div class="modal__backdrop" data-close></div>
  <form class="modal__box" id="memberForm" role="dialog" aria-modal="true" aria-labelledby="memberModalTitle" novalidate>
    <h2 class="modal__title" id="memberModalTitle">Add family member</h2>
    <p class="err" id="mFormErr" role="alert"></p>

    <div class="mfield">
      <label class="label" for="mName">Full name</label>
      <input class="control" id="mName" type="text" autocomplete="off" placeholder="Member’s full name" />
      <p class="err" id="mNameErr" role="alert"></p>
    </div>
    <div class="mfield">
      <label class="label" for="mEmail">Email address</label>
      <input class="control" id="mEmail" type="email" autocomplete="off" placeholder="name@example.com" />
      <p class="err" id="mEmailErr" role="alert"></p>
    </div>
    <div class="mfield" id="mGenderField">
      <label class="label" for="mGender">Gender</label>
      <div class="control control--select">
        <select id="mGender">
          <option value="">Not set</option>
          <option value="male">Male</option>
          <option value="female">Female</option>
          <option value="other">Other</option>
          <option value="prefer_not_to_say">Prefer not to say</option>
        </select>
      </div>
      <p class="err" id="mGenderErr" role="alert"></p>
    </div>
    <div class="mfield">
      <label class="label" for="mPassword" id="mPasswordLabel">Password</label>
      <input class="control" id="mPassword" type="password" autocomplete="new-password" placeholder="8+ characters" />
      <p class="hint" id="mPasswordHint">Upper and lower case, a number and a symbol.</p>
      <p class="err" id="mPasswordErr" role="alert"></p>
    </div>

    <div class="modal__actions">
      <button class="btn btn--ghost" type="button" data-close>Cancel</button>
      <button class="btn btn--primary" type="submit" id="memberSaveBtn">Add member</button>
    </div>
  </form>
</div>

<!-- Remove member confirmation -->
<div class="modal" id="deleteModal" hidden>
  <div class="modal__backdrop" data-close></div>
  <div class="modal__box" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
    <h2 class="modal__title" id="deleteModalTitle">Remove member</h2>
    <div class="modal__body">
      <p id="deleteText"></p>
      <p>They will no longer be able to sign in. This cannot be undone.</p>
    </div>
    <p class="err" id="deleteErr" role="alert"></p>
    <div class="modal__actions">
      <button class="btn btn--ghost" type="button" data-close>Keep member</button>
      <button class="btn btn--danger" type="button" id="deleteConfirmBtn">Remove</button>
    </div>
  </div>
</div>

<script>window.URLROOT = "<?php echo URLROOT; ?>";</script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/nav.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/nav.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/store.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/store.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/orders.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/orders.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/notify.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/notify.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/household-ui.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/household-ui.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/household.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/household.js'); ?>"></script>
</body>
</html>
