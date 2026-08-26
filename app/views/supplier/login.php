<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Supplier Login — Budget Pilot</title>
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
    <div class="tabs" role="tablist" aria-label="Account type">
      <a class="tab" href="<?php echo URLROOT; ?>/customer/login" role="tab" aria-selected="false">Customer Account</a>
      <a class="tab is-active" href="<?php echo URLROOT; ?>/supplier/login" role="tab" aria-selected="true">Supplier Portal</a>
    </div>

    <div class="auth-card__body">
      <h1 class="auth__title">Welcome Back</h1>
      <p class="auth__sub">Manage all your grocery item brands in one place.</p>

      <?php if (!empty($data['registered'])): ?>
        <!-- Success notice after registration -->
        <div style="background:#E8F6EE;border:1px solid #BDE6CE;color:#1B6B42;border-radius:9px;padding:12px 14px;margin-bottom:18px;font-size:.9rem;font-weight:500;">
          <strong>Application submitted!</strong> Your supplier account is awaiting admin approval. You will receive access once reviewed.
        </div>
      <?php endif; ?>

      <?php if (!empty($data['error'])): ?>
        <?php
          // Choose alert style based on error type
          $isPending = strpos($data['error'], 'awaiting admin') !== false;
          $bgColor   = $isPending ? '#FEF9E8' : '#FDF6F6';
          $bdColor   = $isPending ? '#F5D87A' : '#F0BCBC';
          $txColor   = $isPending ? '#7A5C00' : '#C2373C';
        ?>
        <div style="background:<?php echo $bgColor; ?>;border:1px solid <?php echo $bdColor; ?>;color:<?php echo $txColor; ?>;border-radius:9px;padding:12px 14px;margin-bottom:18px;font-size:.9rem;font-weight:500;">
          <?php echo htmlspecialchars($data['error']); ?>
        </div>
      <?php endif; ?>

      <form id="loginForm" method="POST" action="<?php echo URLROOT; ?>/supplier/login">

        <div class="field">
          <label class="field__label" for="email">Email</label>
          <div class="input">
            <span class="input__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="M3 6.5l9 6.5 9-6.5"/>
              </svg>
            </span>
            <input id="email" name="email" type="email" placeholder="name@example.com"
                   autocomplete="email" aria-describedby="emailError"
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required />
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
            <input id="password" name="password" type="password" placeholder="••••••••"
                   autocomplete="current-password" required />
            <button class="input__toggle" type="button" data-toggle="password" aria-label="Show password">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 12s3.8-6.5 10-6.5S22 12 22 12s-3.8 6.5-10 6.5S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
          <p class="field__error" id="passwordError" role="alert"></p>
        </div>

        <p class="form__error" id="formError" role="alert"></p>

        <button class="btn btn--primary btn--block" type="submit">
          Sign In
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h15M13 6l6 6-6 6"/></svg>
        </button>

        <p class="auth__switch">New supplier? <a class="link" href="<?php echo URLROOT; ?>/supplier/register">Apply for an account</a></p>
      </form>

      <div class="auth__foot">
        <a class="auth__back" href="<?php echo URLROOT; ?>/customer/index">← Back to Home</a>
      </div>
    </div>
  </section>
</main>

<script>
  document.querySelectorAll('[data-toggle]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input  = document.getElementById(btn.getAttribute('data-toggle'));
      if (!input) return;
      var hidden = input.type === 'password';
      input.type = hidden ? 'text' : 'password';
      btn.setAttribute('aria-label', hidden ? 'Hide password' : 'Show password');
      input.focus();
    });
  });

  var forgotBtn = document.getElementById('forgotBtn');
  var formError = document.getElementById('formError');
  if (forgotBtn && formError) {
    forgotBtn.addEventListener('click', function () {
      formError.textContent = 'To reset your password, contact your system administrator.';
      formError.style.display = 'block';
    });
  }
</script>
</body>
</html>
