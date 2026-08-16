<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Profile &amp; Settings — Budget Pilot</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/settings.css" />
</head>
<body class="settings">

<div class="shell">

  <!-- ============ SIDEBAR ============ -->
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
      <a class="navlink" href="<?php echo URLROOT; ?>/customer/household" data-main-only>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3.2"/><path d="M2.5 20c.6-3.6 3.2-5.5 6.5-5.5s5.9 1.9 6.5 5.5"/><path d="M16.5 5.2a3.2 3.2 0 0 1 0 6"/><path d="M18 14.8c2 .7 3.2 2.4 3.5 5.2"/></svg>
        Household
      </a>
    </nav>

    <div class="sidebar__foot">
      <span class="navlink navlink--active" aria-current="page">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3.2"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-2.9 1.2v.2a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-3-1.2l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0-1.2-2.9H3a2 2 0 1 1 0-4h.1A1.7 1.7 0 0 0 4.3 6l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 2.9-1.2V2a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 3 1.2l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0 1.2 2.9H22a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/></svg>
        Settings
      </span>
      <a class="navlink" href="<?php echo URLROOT; ?>/customer/login" id="loginLink">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="3"/><path d="M8 9.5h8M8 14h5"/></svg>
        Sign-in page
      </a>
      <button class="navlink" type="button" id="logoutBtn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/></svg>
        Logout
      </button>
    </div>
  </aside>

  <!-- ============ MAIN ============ -->
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
      <h1 class="page__title">Profile &amp; Settings</h1>
      <p class="page__sub">Manage your personal information and application preferences.</p>

      <div class="layout">
        <!-- ---------- Left column ---------- -->
        <div class="col">

          <!-- Identity -->
          <section class="card identity">
            <div class="identity__photo">
              <span class="identity__avatar" id="profileAvatar" aria-hidden="true"></span>
              <label class="identity__camera" for="avatarFile" title="Change photo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 8h3.5L8 6h8l1.5 2H21v11H3z"/><circle cx="12" cy="13" r="3.4"/></svg>
                <span class="sr-only">Change profile photo</span>
              </label>
              <input class="sr-only" id="avatarFile" type="file" accept="image/*" />
            </div>
            <div class="identity__text">
              <h2 class="identity__name" id="displayName">—</h2>
              <p class="identity__role" id="displayRole">—</p>
            </div>
            <button class="btn btn--outline" type="button" id="editBtn">Edit Profile</button>
          </section>

          <!-- Account details -->
          <section class="card">
            <h2 class="card__title">Account Details</h2>
            <p class="card__sub">Fill in anything you skipped at sign-up. Changes apply when you save.</p>
            <div class="fields">
              <div class="field">
                <label class="label" for="fName">Full name</label>
                <input class="control" id="fName" type="text" placeholder="Your full name" />
                <p class="err" id="fNameErr" role="alert"></p>
              </div>
              <div class="field">
                <label class="label" for="fEmail">Email address</label>
                <input class="control" id="fEmail" type="email" placeholder="name@example.com" />
                <p class="err" id="fEmailErr" role="alert"></p>
              </div>
              <div class="field">
                <label class="label" for="fIncome">Monthly income</label>
                <div class="control control--money">
                  <span class="control__prefix" id="incomeSymbol">$</span>
                  <input id="fIncome" type="number" min="0" step="0.01" placeholder="Add your monthly income" data-member-lock />
                </div>
                <p class="hint" id="incomeDerivedHint" hidden>Your sign-up income plus everything recorded under Financial Data.</p>
                <p class="hint" id="incomeOwnerHint" hidden>Your salary is set by the account holder.</p>
                <p class="err" id="fIncomeErr" role="alert"></p>
              </div>
              <div class="field">
                <label class="label" for="fSavings">Savings goal</label>
                <div class="control control--money">
                  <span class="control__prefix" id="savingsSymbol">$</span>
                  <input id="fSavings" type="number" min="0" step="0.01" placeholder="Add a savings goal" />
                </div>
                <p class="err" id="fSavingsErr" role="alert"></p>
              </div>
              <div class="field">
                <label class="label" for="fNic">National ID card (NIC)</label>
                <input class="control" id="fNic" type="text" placeholder="Add your NIC number" />
                <p class="err" id="fNicErr" role="alert"></p>
              </div>
              <div class="field field--split">
                <div>
                  <label class="label" for="fGender">Gender</label>
                  <div class="control control--select">
                    <select id="fGender">
                      <option value="">Not set</option>
                      <option value="female">Female</option>
                      <option value="male">Male</option>
                      <option value="other">Other</option>
                      <option value="undisclosed">Prefer not to say</option>
                    </select>
                  </div>
                </div>
                <div>
                  <label class="label" for="fAge">Age</label>
                  <input class="control" id="fAge" type="number" min="13" max="120" placeholder="Add your age" />
                </div>
                <p class="err" id="fAgeErr" role="alert"></p>
              </div>
            </div>
          </section>

          <!-- Financial preferences -->
          <section class="card">
            <h2 class="card__title">Financial Preferences</h2>
            <p class="card__sub">Sets the currency and reporting year used across your budgets.</p>
            <div class="fields">
              <div class="field">
                <label class="label" for="fCurrency">Preferred currency</label>
                <div class="control control--select">
                  <select id="fCurrency">
                    <option value="USD">USD - US Dollar ($)</option>
                    <option value="EUR">EUR - Euro (€)</option>
                    <option value="GBP">GBP - British Pound (£)</option>
                    <option value="LKR">LKR - Sri Lankan Rupee (Rs)</option>
                    <option value="INR">INR - Indian Rupee (₹)</option>
                    <option value="AUD">AUD - Australian Dollar ($)</option>
                  </select>
                </div>
              </div>
              <div class="field">
                <label class="label" for="fFiscal">Fiscal year start</label>
                <div class="control control--select">
                  <select id="fFiscal">
                    <option>January</option><option>February</option><option>March</option>
                    <option>April</option><option>May</option><option>June</option>
                    <option>July</option><option>August</option><option>September</option>
                    <option>October</option><option>November</option><option>December</option>
                  </select>
                </div>
              </div>
            </div>
          </section>

          <!-- Financial data -->
          <section class="card" id="financeCard">
            <h2 class="card__title">Financial Data</h2>
            <p class="card__sub">Add where your money comes from, and keep your pay sheets in one place.</p>

            <div class="field finperson" id="finPersonField" hidden>
              <label class="label" for="finPerson">Whose financial data</label>
              <div class="control control--select">
                <select id="finPerson"></select>
              </div>
              <p class="hint">You record this for everyone on the account. Saving updates that person's monthly income.</p>
            </div>

            <p class="locknote" id="financeLocked" hidden></p>

            <div class="field">
              <label class="label" for="finBase">Income at sign-up</label>
              <div class="control control--money">
                <span class="control__prefix" id="finBaseSymbol">$</span>
                <input id="finBase" type="number" min="0" step="0.01" placeholder="0" data-member-lock />
              </div>
              <p class="hint">The figure entered when the account was created. Income sources are added on top of it.</p>
              <p class="err" id="finBaseErr" role="alert"></p>
            </div>

            <h3 class="subhead">Income sources</h3>
            <ul class="sources" id="sourceList"></ul>
            <p class="empty" id="sourceEmpty">No income sources yet. Add your salary or any other income below.</p>

            <div class="addsource" id="addSourceBlock">
              <div class="addsource__grid">
                <div class="field">
                  <label class="label" for="srcName">Source</label>
                  <input class="control" id="srcName" type="text" placeholder="Salary — Acme Ltd" data-member-lock />
                </div>
                <div class="field">
                  <label class="label" for="srcAmount">Amount</label>
                  <div class="control control--money">
                    <span class="control__prefix" id="srcSymbol">$</span>
                    <input id="srcAmount" type="number" min="0" step="0.01" placeholder="4,000" data-member-lock />
                  </div>
                </div>
                <div class="field">
                  <label class="label" for="srcFreq">Frequency</label>
                  <div class="control control--select">
                    <select id="srcFreq" data-member-lock>
                      <option value="monthly">Monthly</option>
                      <option value="weekly">Weekly</option>
                      <option value="annual">Annual</option>
                      <option value="once">One-off</option>
                    </select>
                  </div>
                </div>
              </div>
              <p class="err" id="srcErr" role="alert"></p>
              <button class="btn btn--soft" type="button" id="addSourceBtn" data-member-lock>Add income source</button>
            </div>

            <ul class="breakdown" id="incomeBreakdown">
              <li><span>Income at sign-up</span><strong id="bdBase">$0</strong></li>
              <li><span>Income sources</span><strong id="bdSources">$0</strong></li>
              <li class="breakdown__total"><span id="bdWho">Monthly income</span><strong id="bdTotal">$0</strong></li>
            </ul>

            <h3 class="subhead">Pay sheets and statements</h3>
            <ul class="docs" id="docList"></ul>
            <p class="empty" id="docEmpty">No documents yet. Upload a pay sheet to keep it with your account.</p>
            <p class="err" id="docErr" role="alert"></p>
            <div class="docs__actions" id="docActions">
              <label class="btn btn--soft" for="docFile">Upload pay sheet</label>
              <input class="sr-only" id="docFile" type="file" accept=".pdf,.png,.jpg,.jpeg,.csv,.xlsx,.doc,.docx" data-member-lock />
              <span class="docs__hint">PDF, image, or spreadsheet up to 1MB.</span>
            </div>
          </section>
        </div>

        <!-- ---------- Right column ---------- -->
        <div class="col col--side">

          <section class="card">
            <h2 class="card__title">App Settings</h2>

            <div class="setting">
              <span class="setting__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
              </span>
              <span class="setting__text">
                <span class="setting__name">Dark Mode</span>
                <span class="setting__desc">Switch theme</span>
              </span>
              <button class="toggle" type="button" id="darkToggle" role="switch" aria-checked="false" aria-label="Dark mode"><span class="toggle__knob"></span></button>
            </div>

            <div class="setting">
              <span class="setting__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>
              </span>
              <span class="setting__text">
                <span class="setting__name">Smart Alerts</span>
                <span class="setting__desc">Budget &amp; spend alerts</span>
              </span>
              <button class="toggle" type="button" id="alertToggle" role="switch" aria-checked="true" aria-label="Smart alerts"><span class="toggle__knob"></span></button>
            </div>
          </section>

          <section class="card card--navy">
            <h2 class="card__title card__title--light">Security Checkup</h2>
            <p class="checkup__text" id="checkupText">Checking your account…</p>
            <div class="checkup__bar"><span class="checkup__fill" id="checkupFill"></span></div>
            <button class="btn btn--white" type="button" id="checkupBtn">View Details</button>
          </section>

          <section class="card">
            <h2 class="card__title">Data Management</h2>
            <button class="rowbtn" type="button" id="exportBtn">
              <svg class="rowbtn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12M7 11l5 5 5-5M4 20h16"/></svg>
              <span>Export Financial Data</span>
              <svg class="rowbtn__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
            </button>
            <button class="rowbtn" type="button" id="cacheBtn">
              <svg class="rowbtn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3.5" y="4" width="17" height="16" rx="3"/><path d="M9 9l6 6M15 9l-6 6"/></svg>
              <span>Clear Cache</span>
              <svg class="rowbtn__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
            </button>
            <button class="rowbtn rowbtn--danger" type="button" id="deactivateBtn">
              <svg class="rowbtn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3.5L21.5 20h-19z"/><path d="M12 10v4M12 17.2v.1"/></svg>
              <span>Deactivate Account</span>
              <svg class="rowbtn__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
            </button>
          </section>
          <section class="card">
            <h2 class="card__title">Household</h2>
            <p class="card__sub">Everyone who can sign in to this account.</p>
            <ul class="people" id="peopleList"></ul>
            <a class="btn btn--soft btn--block" href="create-account.html#members">Add family member</a>
          </section>
        </div>
      </div>

      <!-- ---------- Save bar ---------- -->
      <footer class="actionbar">
        <p class="actionbar__state" id="dirtyState"></p>
        <button class="btn btn--ghost" type="button" id="discardBtn" disabled>Discard Changes</button>
        <button class="btn btn--primary" type="button" id="saveBtn" disabled>Save All Changes</button>
      </footer>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="toast" id="toast" role="status" hidden></div>

<!-- Modal -->
<div class="modal" id="modal" hidden>
  <div class="modal__backdrop" id="modalBackdrop"></div>
  <div class="modal__box" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <h2 class="modal__title" id="modalTitle"></h2>
    <div class="modal__body" id="modalBody"></div>
    <div class="modal__actions" id="modalActions"></div>
  </div>
</div>

<script>window.URLROOT = "<?php echo URLROOT; ?>";</script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/store.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/notify.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/household-ui.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/settings.js"></script>
</body>
</html>
