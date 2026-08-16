<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Create Account — Budget Pilot</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/auth.css" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/create-account.css" />
</head>
<body class="signup">

<header class="page-head">
  <a class="page-head__back" href="index.php">← Back to Home</a>

  <a class="brand" href="index.php" aria-label="Budget Pilot home">
    <span class="brand__mark" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none"><rect x="2.5" y="4.5" width="19" height="15" rx="3" stroke="#101C56" stroke-width="2"/><rect x="6" y="8" width="6" height="8" rx="1.5" fill="#101C56"/></svg>
    </span>
    <span class="brand__name">Budget Pilot</span>
  </a>

  <p class="page-head__alt"><a class="link" href="customer-login.php">Back to Login</a>
</header>

<main class="signup__wrap">
  <section class="signup-card">

    <p class="notice" id="modeNotice" hidden></p>

    <h1 class="signup__title" id="pageTitle">Let's get started</h1>
    <p class="signup__sub" id="pageSub">Begin your journey to financial freedom with a secure account.</p>

    <div id="ownerSection">
    <!-- ============ Profile photo ============ -->
    <div class="field">
      <span class="label">Profile photo</span>
      <div class="photo">
        <span class="photo__preview" id="ownerPreview" data-empty="true" aria-hidden="true"></span>
        <div class="photo__actions">
          <label class="btn btn--soft" for="ownerPhoto">Choose image</label>
          <input class="sr-only" id="ownerPhoto" type="file" accept="image/*" />
          <button class="btn btn--text" type="button" id="ownerPhotoClear" hidden>Remove</button>
          <p class="photo__hint">Shown on the sign-in screen. Optional.</p>
        </div>
      </div>
      <p class="field__error" id="ownerPhotoError" role="alert"></p>
    </div>

    <!-- ============ Account details ============ -->
    <div class="field">
      <label class="label" for="fullName">Full name</label>
      <div class="input"><input id="fullName" type="text" placeholder="John Doe" autocomplete="name" /></div>
      <p class="field__error" id="fullNameError" role="alert"></p>
    </div>

    <div class="field">
      <label class="label" for="email">Email address</label>
      <div class="input"><input id="email" type="email" placeholder="name@company.com" autocomplete="email" /></div>
      <p class="field__error" id="emailError" role="alert"></p>
    </div>

    <div class="field">
      <label class="label" for="password">Password</label>
      <div class="input">
        <input id="password" type="password" placeholder="••••••••" autocomplete="new-password" />
        <button class="input__toggle" type="button" data-toggle="password" aria-label="Show password">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 12s3.8-6.5 10-6.5S22 12 22 12s-3.8 6.5-10 6.5S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>
          </svg>
        </button>
      </div>

      <!-- Password security level -->
      <div class="strength" id="strength" hidden>
        <div class="strength__head">
          <span class="strength__caption">Security level</span>
          <span class="strength__value" id="strengthValue" aria-live="polite"></span>
        </div>
        <div class="strength__meter">
          <span class="strength__seg"></span><span class="strength__seg"></span>
          <span class="strength__seg"></span><span class="strength__seg"></span>
        </div>
        <ul class="rules" id="rules">
          <li class="rule" data-rule="length">
            <svg class="rule__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
            <span>At least 8 characters</span>
          </li>
          <li class="rule" data-rule="upper">
            <svg class="rule__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
            <span>An uppercase letter</span>
          </li>
          <li class="rule" data-rule="lower">
            <svg class="rule__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
            <span>A lowercase letter</span>
          </li>
          <li class="rule" data-rule="number">
            <svg class="rule__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
            <span>A number</span>
          </li>
          <li class="rule" data-rule="symbol">
            <svg class="rule__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
            <span>A symbol, such as ! ? @ #</span>
          </li>
        </ul>
      </div>

      <p class="field__error" id="passwordError" role="alert"></p>
    </div>

    <div class="field">
      <label class="label" for="nic">National ID card (NIC)</label>
      <div class="input"><input id="nic" type="text" placeholder="Enter your NIC number" /></div>
      <p class="field__error" id="nicError" role="alert"></p>
    </div>

    <div class="grid-2">
      <div class="field">
        <label class="label" for="gender">Gender</label>
        <div class="input input--select">
          <select id="gender">
            <option value="">Select Gender</option>
            <option value="female">Female</option>
            <option value="male">Male</option>
          </select>
          <span class="input__chevron" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
          </span>
        </div>
        <p class="field__error" id="genderError" role="alert"></p>
      </div>

      <div class="field">
        <label class="label" for="age">Age</label>
        <div class="input"><input id="age" type="number" min="13" max="120" placeholder="25" /></div>
        <p class="field__error" id="ageError" role="alert"></p>
      </div>
    </div>

    <hr class="rule-line" />
    </div><!-- /#ownerSection -->

    <!-- ============ Family members ============ -->
    <section id="members">
      <h2 class="signup__section" id="memberHeading">Add Family Members</h2>
      <p class="signup__sub" id="memberSub">Invite family members to manage your household budget together.</p>

      <ul class="members" id="memberList"></ul>

      <button class="add-member" type="button" id="addMemberBtn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
        Add more
      </button>
    </section>

    <div id="financeSection">
    <hr class="rule-line" />

    <!-- ============ Financial profile ============ -->
    <h2 class="signup__section">Setup your financial profile</h2>
    <p class="signup__sub">We use this to calibrate your personalized copilot insights.</p>

    <div class="field">
      <label class="label" for="income">Monthly after-tax income</label>
      <div class="input"><span class="input__prefix">$</span><input id="income" type="number" min="0" placeholder="5,000" /></div>
      <p class="field__error" id="incomeError" role="alert"></p>
    </div>

    <div class="field">
      <label class="label" for="savings">Target monthly savings</label>
      <div class="input"><span class="input__prefix">$</span><input id="savings" type="number" min="0" placeholder="1,000" /></div>
      <p class="field__error" id="savingsError" role="alert"></p>
    </div>

    <label class="check">
      <input type="checkbox" id="terms" />
      <span>I agree to the <a class="link" href="terms.php" target="_blank" rel="noopener">Terms of Service</a> and <a class="link" href="privacy.php" target="_blank" rel="noopener">Privacy Policy</a></span>
    </label>
    <p class="field__error" id="termsError" role="alert"></p>
    </div><!-- /#financeSection -->

    <p class="form__error" id="formError" role="alert"></p>

    <button class="btn btn--primary btn--block" type="button" id="createBtn">
      <span id="createBtnLabel">Create Account</span>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h15M13 6l6 6-6 6"/></svg>
    </button>

    <p class="signup__fine" id="finePrint">By creating an account, you agree to our processing of personal data.</p>
  </section>
</main>

<!-- Template for a family member card -->
<template id="memberTemplate">
  <li class="member">
    <div class="member__bar">
      <p class="member__caption">Family member</p>
      <button class="member__remove" type="button" aria-label="Remove this member">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
      </button>
    </div>

    <div class="field">
      <label class="label member__name-label">Full name</label>
      <div class="input"><input class="member__name" type="text" placeholder="Jane Doe" /></div>
    </div>

    <div class="field">
      <label class="label member__email-label">Email</label>
      <div class="input"><input class="member__email" type="email" placeholder="family@example.com" /></div>
    </div>

    <div class="field">
      <span class="label">Profile photo</span>
      <div class="photo">
        <span class="photo__preview member__preview" data-empty="true" aria-hidden="true"></span>
        <div class="photo__actions">
          <label class="btn btn--soft member__choose">Choose image</label>
          <input class="sr-only member__file" type="file" accept="image/*" />
          <button class="btn btn--text member__clear" type="button" hidden>Remove</button>
          <p class="photo__hint">Shown on the sign-in screen. Optional.</p>
        </div>
      </div>
    </div>

    <div class="field">
      <label class="label member__password-label">Password</label>
      <div class="input">
        <input class="member__password" type="password" placeholder="••••••••" />
        <button class="input__toggle member__reveal" type="button" aria-label="Show password">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 12s3.8-6.5 10-6.5S22 12 22 12s-3.8 6.5-10 6.5S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>
          </svg>
        </button>
      </div>
      <p class="member__level"></p>
    </div>

    <p class="field__error member__error" role="alert"></p>
  </li>
</template>

<script src="<?php echo URLROOT; ?>/public/js/customer-js/store.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/create-account.js"></script>
</body>
</html>
