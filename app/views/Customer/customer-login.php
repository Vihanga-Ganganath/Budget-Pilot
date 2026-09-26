<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Customer Login — Budget Pilot</title>
<link rel="icon" type="image/png" href="<?php echo URLROOT; ?>/public/assets/logos/favicon.png?v=2">
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/auth.css?v=<?php echo @filemtime(APPROOT . '/../public/css/customer-css/auth.css'); ?>" />
</head>
<body class="auth">

<main class="auth__wrap">

  <a class="brand brand--center" href="<?php echo URLROOT; ?>/customer/index" aria-label="Budget Pilot home">
    <span class="brand__mark" aria-hidden="true">
      <img src="<?php echo URLROOT; ?>/public/assets/logos/budget-pilot-mark.png" alt="">
    </span>
    <span class="brand__name">Budget Pilot</span>
  </a>

  <section class="auth-card">
    <div class="auth-card__body">
      <p class="notice" id="notice" hidden></p>

      <div id="signInView">
      <h1 class="auth__title">Welcome Back</h1>
      <p class="auth__sub">Manage your personal or family finances in one place.</p>

      <!-- Profile picker — every household in the database, grouped (auth.js) -->
      <div class="profiles">
        <div class="profiles__head">
          <span class="field__label">Select Profile</span>
          <button class="profiles__manage" type="button" id="manageBtn" hidden>Manage</button>
        </div>
        <div class="profiles__row" id="profileRow" role="radiogroup" aria-label="Select profile"></div>
        <p class="profiles__empty" id="profilesEmpty" hidden>
          No accounts yet. New here? <a class="link" href="<?php echo URLROOT; ?>/customer/register">Create an account</a>.
        </p>
        <div class="confirm" id="profileConfirm" hidden>
          <p class="confirm__text" id="confirmText"></p>
          <div class="confirm__actions">
            <button class="confirm__btn confirm__btn--danger" type="button" id="confirmRemove">Remove</button>
            <button class="confirm__btn" type="button" id="confirmCancel">Cancel</button>
          </div>
        </div>
        <p class="profiles__selected" id="profileSelected"></p>
      </div>

      <!-- Form -->
      <div class="form">
        <div class="field">
          <label class="field__label" for="email">Email</label>
          <div class="input">
            <span class="input__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="M3 6.5l9 6.5 9-6.5"/>
              </svg>
            </span>
            <input id="email" type="email" placeholder="name@example.com" autocomplete="email" aria-describedby="emailError" />
          </div>
          <p class="field__error" id="emailError" role="alert"></p>
        </div>

        <div class="field">
          <div class="field__row">
            <label class="field__label" for="password">Password</label>
            <button class="link link--plain" type="button" id="forgotBtn">Forgot password?</button>
          </div>
          <div class="input">
            <span class="input__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <rect x="4" y="10.5" width="16" height="10" rx="2.5"/><path d="M8 10.5V7.8a4 4 0 0 1 8 0v2.7"/>
              </svg>
            </span>
            <input id="password" type="password" placeholder="••••••••" autocomplete="current-password" />
            <button class="input__toggle" type="button" data-toggle="password" aria-label="Show password">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 12s3.8-6.5 10-6.5S22 12 22 12s-3.8 6.5-10 6.5S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
          <p class="field__error" id="passwordError" role="alert"></p>
        </div>

        <p class="form__error" id="formError" role="alert"></p>

        <button class="btn btn--primary btn--block" id="signInBtn" type="button">
          Sign In
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h15M13 6l6 6-6 6"/></svg>
        </button>

        <p class="auth__switch" id="createSwitch" hidden>New to Budget Pilot? <a class="link" href="<?php echo URLROOT; ?>/customer/register">Create Account</a></p>
      </div>
      </div><!-- /signInView -->

      <!-- ============ Forgot password (forgot-password.js) ============ -->
      <div id="forgotView" hidden>
        <p class="steps" id="fpSteps" aria-hidden="true"></p>
        <h1 class="auth__title" id="fpTitle">Reset your password</h1>
        <p class="auth__sub" id="fpSub">Enter the email you sign in with.</p>

        <!-- Step 1: email -->
        <div class="form fp-step" data-step="email">
          <div class="field">
            <label class="field__label" for="fpEmail">Email</label>
            <div class="input">
              <span class="input__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="M3 6.5l9 6.5 9-6.5"/></svg></span>
              <input id="fpEmail" type="email" placeholder="name@example.com" autocomplete="email" />
            </div>
            <p class="field__error" id="fpEmailError" role="alert"></p>
          </div>
          <button class="btn btn--primary btn--block" type="button" id="fpEmailBtn">Continue</button>
        </div>

        <!-- Step 2: NIC (every customer) -->
        <div class="form fp-step" data-step="nic" hidden>
          <div class="field">
            <label class="field__label" for="fpNic">National ID card (NIC) number</label>
            <div class="input">
              <span class="input__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="5" width="19" height="14" rx="2.5"/><circle cx="8.5" cy="11" r="2"/><path d="M5.5 16c.6-1.6 1.7-2.4 3-2.4s2.4.8 3 2.4M14 10h4M14 13.5h3"/></svg></span>
              <input id="fpNic" type="text" maxlength="12" placeholder="200012345678 or 991234567V" autocomplete="off" />
            </div>
            <p class="field__error" id="fpNicError" role="alert"></p>
          </div>
          <button class="btn btn--primary btn--block" type="button" id="fpNicBtn">Verify</button>
        </div>

        <!-- Step 3: security PIN (only when users.two_factor_enabled is on) -->
        <div class="form fp-step" data-step="pin" hidden>
          <div class="field">
            <label class="field__label" for="fpPin">Security PIN</label>
            <div class="input">
              <span class="input__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="10" rx="2.5"/><path d="M7 12h.01M11 12h.01M15 12h.01"/></svg></span>
              <input id="fpPin" class="input--code" type="password" inputmode="numeric" maxlength="6" placeholder="••••••" autocomplete="off" />
            </div>
            <p class="field__error" id="fpPinError" role="alert"></p>
          </div>
          <button class="btn btn--primary btn--block" type="button" id="fpPinBtn">Verify PIN</button>
        </div>

        <!-- Last step: new password -->
        <div class="form fp-step" data-step="password" hidden>
          <div class="field">
            <label class="field__label" for="fpNew">New password</label>
            <div class="input">
              <span class="input__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10.5" width="16" height="10" rx="2.5"/><path d="M8 10.5V7.8a4 4 0 0 1 8 0v2.7"/></svg></span>
              <input id="fpNew" type="password" autocomplete="new-password" />
              <button class="input__toggle" type="button" data-toggle="fpNew" aria-label="Show password"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.8-6.5 10-6.5S22 12 22 12s-3.8 6.5-10 6.5S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg></button>
            </div>
            <p class="fp-hint">8+ characters with upper and lower case, a number and a symbol.</p>
            <p class="field__error" id="fpNewError" role="alert"></p>
          </div>
          <div class="field">
            <label class="field__label" for="fpConfirm">Confirm new password</label>
            <div class="input">
              <span class="input__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10.5" width="16" height="10" rx="2.5"/><path d="M8 10.5V7.8a4 4 0 0 1 8 0v2.7"/></svg></span>
              <input id="fpConfirm" type="password" autocomplete="new-password" />
            </div>
            <p class="field__error" id="fpConfirmError" role="alert"></p>
          </div>
          <button class="btn btn--primary btn--block" type="button" id="fpSaveBtn">Save new password</button>
        </div>

        <p class="form__error" id="fpError" role="alert"></p>
        <p class="auth__switch"><button class="link link--plain" type="button" id="fpBackBtn">← Back to sign in</button></p>
      </div><!-- /forgotView -->

      <div class="auth__foot">
        <a class="auth__back" href="<?php echo URLROOT; ?>/customer/index">← Back to Home</a>
      </div>
    </div>
  </section>
</main>

<script>window.URLROOT = "<?php echo URLROOT; ?>";</script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/store.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/store.js'); ?>"></script>
<script>window.BP_HOUSEHOLDS = <?php echo json_encode($data['households'] ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;</script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/auth.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/auth.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/forgot-password.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/forgot-password.js'); ?>"></script>
</body>
</html>
