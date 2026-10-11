
<?php require APPROOT . '/Views/shared/header.php'; ?>

      <div class="content">
    <div class="dashboard-header">
        <div>
            <h1 class="page__title">Profile &amp; Settings</h1>
            <p class="page__sub">Manage your personal information and application preferences.</p>
        </div>
    </div>

      <div class="layout">
        <!-- ---------- Left column ---------- -->
        <div class="col">

          <!-- Identity -->
          <section class="card identity">
            <div class="identity__photo" style="display:flex; align-items:center; gap:20px;">
              <span class="identity__avatar" id="profileAvatar" aria-hidden="true"></span>
              <div class="identity__upload">
                <label class="btn" style="background:#fff; color:var(--color-primary); border:1px solid var(--color-primary); cursor:pointer;" for="avatarFile">Change Photo</label>
                <input style="display:none;" id="avatarFile" type="file" accept="image/*" />
              </div>
            </div>
            <div class="identity__text">
              <h2 class="identity__name" id="displayName"></h2>
              <p class="identity__role" id="displayRole"></p>
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

          <!-- Change password -->
          <section class="card" id="passwordCard">
            <h2 class="card__title">Change Password</h2>
            <p class="card__sub">Enter your current password, then choose a new one. This is saved straight away.</p>
            <div class="fields">
              <div class="field">
                <label class="label" for="pwCurrent">Current password</label>
                <input class="control" id="pwCurrent" type="password" autocomplete="current-password" />
                <p class="err" id="pwCurrentErr" role="alert"></p>
              </div>
              <div class="field">
                <label class="label" for="pwNew">New password</label>
                <input class="control" id="pwNew" type="password" autocomplete="new-password" />
                <p class="hint">8+ characters with upper and lower case, a number and a symbol.</p>
                <p class="err" id="pwNewErr" role="alert"></p>
              </div>
              <div class="field">
                <label class="label" for="pwConfirm">Confirm new password</label>
                <input class="control" id="pwConfirm" type="password" autocomplete="new-password" />
                <p class="err" id="pwConfirmErr" role="alert"></p>
              </div>
              <div class="field">
                <button class="btn btn--primary" type="button" id="pwBtn">Update password</button>
              </div>
            </div>
          </section>

          <!-- Two-factor authentication (users.two_factor_enabled + security PIN)  two-factor.js -->
          <section class="card" id="tfaCard">
            <div class="tfa__head">
              <h2 class="card__title">Two-Factor Authentication</h2>
              <span class="tfa__badge" id="tfaBadge">Checking</span>
            </div>
            <p class="card__sub">Resetting a forgotten password always asks for your NIC. When two-factor is on, it also asks for a 6-digit security PIN that only you know.</p>
            <p class="hint" id="tfaNoNic" hidden>Add your NIC under Account Details and save before turning this on.</p>
            <div class="fields">
              <div class="field">
                <label class="label" for="tfaPin" id="tfaPinLabel">Security PIN</label>
                <input class="control" id="tfaPin" type="password" inputmode="numeric" maxlength="6" placeholder="6 digits" autocomplete="off" />
                <p class="hint" id="tfaPinHint">6 digits. Avoid easy ones like 123456 or 111111.</p>
                <p class="err" id="tfaPinErr" role="alert"></p>
              </div>
              <div class="field">
                <label class="label" for="tfaPinConfirm">Confirm PIN</label>
                <input class="control" id="tfaPinConfirm" type="password" inputmode="numeric" maxlength="6" placeholder="6 digits" autocomplete="off" />
                <p class="err" id="tfaPinConfirmErr" role="alert"></p>
              </div>
              <div class="field">
                <label class="label" for="tfaPassword">Current password to confirm</label>
                <input class="control" id="tfaPassword" type="password" autocomplete="current-password" />
                <p class="err" id="tfaPasswordErr" role="alert"></p>
              </div>
              <div class="field tfa__actions">
                <button class="btn btn--primary" type="button" id="tfaBtn" disabled>Turn on</button>
                <button class="btn btn--danger" type="button" id="tfaOffBtn" hidden>Turn off</button>
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
                    <option value="EUR">EUR - Euro ()</option>
                    <option value="GBP">GBP - British Pound ()</option>
                    <option value="LKR">LKR - Sri Lankan Rupee (Rs)</option>
                    <option value="INR">INR - Indian Rupee ()</option>
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
                  <input class="control" id="srcName" type="text" placeholder="Salary  Acme Ltd" data-member-lock />
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
            <p class="checkup__text" id="checkupText">Checking your account</p>
            <div class="checkup__bar"><span class="checkup__fill" id="checkupFill"></span></div>
            <button class="btn btn--white" type="button" id="checkupBtn">View Details</button>
          </section>

          <section class="card">
            <h2 class="card__title">Data Management</h2>
            <button class="rowbtn" type="button" id="exportBtn">
              
              <span>Export Financial Data</span>
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
            <a class="btn btn--soft btn--block" href="<?php echo URLROOT; ?>/customer/register#members">Add family member</a>
          </section>
        </div>
      </div>

      <!-- ---------- Save bar ---------- -->
      <footer class="actionbar">
        <p class="actionbar__state" id="dirtyState"></p>
        <button class="btn btn--ghost" type="button" id="discardBtn" disabled>Discard Changes</button>
        <button class="btn btn--primary" type="button" id="saveBtn" disabled>Save All Changes</button>
      </footer>
    <!-- Toast -->
<div class="toast" id="toast" role="status" hidden></div>

<!-- Modal -->
<div class="modal" id="modal" hidden>
  <div class="modal__backdrop" id="modalBackdrop"></div>
  <div class="modal__box" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <h2 class="modal__title" id="modalTitle"></h2>
    <div class="modal__body" id="modalBody"></div>
    <div class="modal__actions" id="modalActions">
      </div>
    </div>
  </div>

  <script>window.URLROOT = "<?php echo URLROOT; ?>";</script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/store.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/store.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/notify.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/notify.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/household-ui.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/household-ui.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/deactivate.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/deactivate.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/settings.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/settings.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/two-factor.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/two-factor.js'); ?>" defer></script>

</div>

<?php require APPROOT . '/Views/shared/footer.php'; ?>










