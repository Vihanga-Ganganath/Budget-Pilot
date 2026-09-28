<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Verify Email — Budget Pilot</title>
<link rel="icon" type="image/png" href="<?php echo URLROOT; ?>/public/assets/logos/favicon.png?v=2">
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/auth.css?v=<?php echo @filemtime(APPROOT . '/../public/css/customer-css/auth.css'); ?>" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/verify.css?v=<?php echo @filemtime(APPROOT . '/../public/css/customer-css/verify.css'); ?>" />
</head>
<body class="auth verify-page">

<div class="auth__wrap">

  <header class="brand brand--center">
    <a class="brand" href="<?php echo URLROOT; ?>/customer/index" aria-label="Budget Pilot home">
      <span class="brand__mark" aria-hidden="true">
        <img src="<?php echo URLROOT; ?>/public/assets/logos/budget-pilot-mark.png" alt="Budget Pilot Logo">
      </span>
      <span class="brand__name">Budget Pilot</span>
    </a>
  </header>

  <main class="auth-card verify-card">
    <div class="auth-card__body">
      
      <div class="verify-icon" aria-hidden="true">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="1.8"
             stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="5" width="18" height="14" rx="2"></rect>
          <path d="m3 7 9 6 9-6"></path>
        </svg>
      </div>

      <h1 class="auth__title verify-title">Verify Your Email</h1>
      <p class="verify-sub">
        We sent a 6-digit verification code to<br>
        <strong id="emailShown">Loading...</strong><br>
        <small style="color:#6B7280; display:inline-block; margin-top:6px;">(Please check your <strong>Spam / Junk folder</strong> if you don't see it in your Inbox)</small>
      </p>

      <form id="verifyForm" novalidate>
        <div class="field">
          <label class="label" for="code">Verification Code</label>
          <div class="input input--code">
            <input type="text"
                   id="code"
                   class="code-input"
                   inputmode="numeric"
                   autocomplete="one-time-code"
                   maxlength="6"
                   placeholder="000000"
                   required />
          </div>
          <p class="field__hint">Enter the 6 digits sent to your email address.</p>
        </div>

        <div class="msg msg--error" id="formError" role="alert" hidden></div>
        <div class="msg msg--success" id="formSuccess" role="status" hidden></div>

        <button class="btn btn--primary btn--block verify-btn" type="submit" id="verifyBtn">
          <span>Verify Email</span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h15M13 6l6 6-6 6"/></svg>
        </button>

        <div class="verify-actions">
          <p class="verify-resend">
            Didn't receive the code?
            <button type="button" class="btn-link" id="resendBtn">Resend code</button>
          </p>

          <p class="verify-back">
            Wrong email address?
            <a class="link" href="<?php echo URLROOT; ?>/customer/register">Register again</a>
          </p>
        </div>
      </form>

    </div>
  </main>

  <footer class="auth-foot">
    <p>© <?php echo date('Y'); ?> Budget Pilot. All rights reserved.</p>
  </footer>

</div>

<script>window.URLROOT = "<?php echo URLROOT; ?>";</script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/store.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/store.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/verify.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/verify.js'); ?>"></script>
</body>
</html>
