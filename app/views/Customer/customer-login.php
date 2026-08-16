<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Customer Login — Budget Pilot</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/auth.css" />
</head>
<body class="auth">

<main class="auth__wrap">

  <a class="brand brand--center" href="<?php echo URLROOT; ?>/customer/index" aria-label="Budget Pilot home">
    <span class="brand__mark" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none"><rect x="2.5" y="4.5" width="19" height="15" rx="3" stroke="#101C56" stroke-width="2"/><rect x="6" y="8" width="6" height="8" rx="1.5" fill="#101C56"/></svg>
    </span>
    <span class="brand__name">Budget Pilot</span>
  </a>

  <section class="auth-card">
    <!-- Tabs -->
    <div class="tabs" role="tablist" aria-label="Account type">
      <a class="tab is-active" href="<?php echo URLROOT; ?>/customer/login" role="tab" aria-selected="true">Customer Account</a>
      <a class="tab" href="<?php echo URLROOT; ?>/customer/supplierLogin" role="tab" aria-selected="false">Supplier Portal</a>
    </div>

    <div class="auth-card__body">
      <p class="notice" id="notice" hidden></p>

      <h1 class="auth__title">Welcome Back</h1>
      <p class="auth__sub">Manage your personal or family finances in one place.</p>

      <!-- Profile picker — filled in from saved accounts -->
      <div class="profiles">
        <div class="profiles__head">
          <span class="field__label">Select Profile</span>
          <button class="profiles__manage" type="button" id="manageBtn" hidden>Manage</button>
        </div>
        <div class="profiles__row" id="profileRow" role="radiogroup" aria-label="Select profile"></div>
        <p class="profiles__empty" id="profilesEmpty" hidden>
          No profiles yet. <a class="link" href="<?php echo URLROOT; ?>/customer/register">Create an account</a> to get started.
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

      <div class="auth__foot">
        <a class="auth__back" href="<?php echo URLROOT; ?>/customer/index">← Back to Home</a>
      </div>
    </div>
  </section>
</main>

<script>window.URLROOT = "<?php echo URLROOT; ?>";</script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/store.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/auth.js"></script>
</body>
</html>
