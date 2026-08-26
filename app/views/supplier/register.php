<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Supplier Registration — Budget Pilot</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/auth.css" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/create-account.css" />
</head>
<body class="signup">

<!-- ── Page header ──────────────────────────────────────────────────────── -->
<header class="page-head">
  <a class="page-head__back" href="<?php echo URLROOT; ?>/supplier/login">← Back to Login</a>

  <a class="brand" href="<?php echo URLROOT; ?>/customer/index" aria-label="Budget Pilot home">
    <span class="brand__mark" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none"><rect x="2.5" y="4.5" width="19" height="15" rx="3" stroke="#101C56" stroke-width="2"/><rect x="6" y="8" width="6" height="8" rx="1.5" fill="#101C56"/></svg>
    </span>
    <span class="brand__name">Budget Pilot</span>
  </a>

  <p class="page-head__alt"><a class="link" href="<?php echo URLROOT; ?>/supplier/login">Already have an account?</a></p>
</header>

<!-- ── Main form card ────────────────────────────────────────────────────── -->
<main class="signup__wrap">
  <section class="signup-card">

    <h1 class="signup__title">Supplier Registration</h1>
    <p class="signup__sub">Apply to list your grocery brands on Budget Pilot. Your account will be reviewed and activated by an admin.</p>

    <?php if (!empty($data['errors']['general'])): ?>
      <div style="background:#FDF6F6;border:1px solid #F0BCBC;color:#C2373C;border-radius:9px;padding:12px 14px;margin-bottom:18px;font-size:.9rem;font-weight:500;">
        <?php echo htmlspecialchars($data['errors']['general']); ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo URLROOT; ?>/supplier/register" novalidate>

      <!-- ═══ Contact Person ═══════════════════════════════════════════════ -->
      <h2 class="signup__section">Contact Person</h2>
      <p class="signup__sub" style="margin-bottom:20px">The person responsible for managing this supplier account.</p>

      <div class="field">
        <label class="label" for="name">Full Name</label>
        <div class="input <?php echo !empty($data['errors']['name']) ? 'is-invalid' : ''; ?>">
          <input id="name" name="name" type="text" placeholder="Jane Doe"
                 autocomplete="name" value="<?php echo htmlspecialchars($data['name'] ?? ''); ?>" />
        </div>
        <?php if (!empty($data['errors']['name'])): ?>
          <p class="field__error" role="alert"><?php echo htmlspecialchars($data['errors']['name']); ?></p>
        <?php endif; ?>
      </div>

      <div class="field">
        <label class="label" for="email">Email Address</label>
        <div class="input <?php echo !empty($data['errors']['email']) ? 'is-invalid' : ''; ?>">
          <input id="email" name="email" type="email" placeholder="name@company.com"
                 autocomplete="email" value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>" />
        </div>
        <?php if (!empty($data['errors']['email'])): ?>
          <p class="field__error" role="alert"><?php echo htmlspecialchars($data['errors']['email']); ?></p>
        <?php endif; ?>
      </div>

      <div class="field">
        <label class="label" for="phone">Phone Number <span style="font-weight:400;text-transform:none;letter-spacing:0;color:#8A92A0">(optional)</span></label>
        <div class="input">
          <input id="phone" name="phone" type="tel" placeholder="+94 77 123 4567"
                 autocomplete="tel" value="<?php echo htmlspecialchars($data['phone'] ?? ''); ?>" />
        </div>
      </div>

      <div class="field">
        <label class="label" for="password">Password</label>
        <div class="input <?php echo !empty($data['errors']['password']) ? 'is-invalid' : ''; ?>">
          <input id="password" name="password" type="password"
                 placeholder="••••••••" autocomplete="new-password" />
          <button class="input__toggle" type="button" data-toggle="password" aria-label="Show password">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
              <path d="M2 12s3.8-6.5 10-6.5S22 12 22 12s-3.8 6.5-10 6.5S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>

        <!-- Password strength meter -->
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
          </ul>
        </div>
        <?php if (!empty($data['errors']['password'])): ?>
          <p class="field__error" role="alert"><?php echo htmlspecialchars($data['errors']['password']); ?></p>
        <?php endif; ?>
      </div>

      <div class="field">
        <label class="label" for="password_confirm">Confirm Password</label>
        <div class="input" id="confirmWrap">
          <input id="password_confirm" name="password_confirm" type="password"
                 placeholder="••••••••" autocomplete="new-password" />
          <button class="input__toggle" type="button" data-toggle="password_confirm" aria-label="Show password">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
              <path d="M2 12s3.8-6.5 10-6.5S22 12 22 12s-3.8 6.5-10 6.5S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>
        <p class="field__error" id="confirmError" role="alert"></p>
      </div>

      <hr class="rule-line" />

      <!-- ═══ Company / Brand Details ═══════════════════════════════════════ -->
      <h2 class="signup__section">Company &amp; Brand Details</h2>
      <p class="signup__sub" style="margin-bottom:20px">Tell us about the brand you will be listing products for.</p>

      <div class="field">
        <label class="label" for="company_name">Company / Brand Name</label>
        <div class="input <?php echo !empty($data['errors']['company_name']) ? 'is-invalid' : ''; ?>">
          <input id="company_name" name="company_name" type="text" placeholder="e.g. FreshHarvest Co."
                 value="<?php echo htmlspecialchars($data['company_name'] ?? ''); ?>" />
        </div>
        <?php if (!empty($data['errors']['company_name'])): ?>
          <p class="field__error" role="alert"><?php echo htmlspecialchars($data['errors']['company_name']); ?></p>
        <?php endif; ?>
      </div>

      <div class="field">
        <label class="label" for="category">Business Category</label>
        <div class="input input--select <?php echo !empty($data['errors']['category']) ? 'is-invalid' : ''; ?>">
          <select id="category" name="category">
            <option value="">Select a category</option>
            <option value="Fresh Produce"   <?php echo (($data['category'] ?? '') === 'Fresh Produce')   ? 'selected' : ''; ?>>Fresh Produce</option>
            <option value="Dairy & Eggs"    <?php echo (($data['category'] ?? '') === 'Dairy & Eggs')    ? 'selected' : ''; ?>>Dairy &amp; Eggs</option>
            <option value="Bakery"          <?php echo (($data['category'] ?? '') === 'Bakery')          ? 'selected' : ''; ?>>Bakery</option>
            <option value="Meat & Seafood"  <?php echo (($data['category'] ?? '') === 'Meat & Seafood')  ? 'selected' : ''; ?>>Meat &amp; Seafood</option>
            <option value="Organic & Health"<?php echo (($data['category'] ?? '') === 'Organic & Health')? 'selected' : ''; ?>>Organic &amp; Health</option>
            <option value="Pantry & Dry Goods"<?php echo (($data['category'] ?? '') === 'Pantry & Dry Goods') ? 'selected' : ''; ?>>Pantry &amp; Dry Goods</option>
            <option value="Beverages"       <?php echo (($data['category'] ?? '') === 'Beverages')       ? 'selected' : ''; ?>>Beverages</option>
            <option value="Frozen Foods"    <?php echo (($data['category'] ?? '') === 'Frozen Foods')    ? 'selected' : ''; ?>>Frozen Foods</option>
            <option value="Snacks"          <?php echo (($data['category'] ?? '') === 'Snacks')          ? 'selected' : ''; ?>>Snacks</option>
            <option value="Other"           <?php echo (($data['category'] ?? '') === 'Other')           ? 'selected' : ''; ?>>Other</option>
          </select>
          <span class="input__chevron" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
          </span>
        </div>
        <?php if (!empty($data['errors']['category'])): ?>
          <p class="field__error" role="alert"><?php echo htmlspecialchars($data['errors']['category']); ?></p>
        <?php endif; ?>
      </div>

      <div class="field">
        <label class="label" for="description">Brief Description <span style="font-weight:400;text-transform:none;letter-spacing:0;color:#8A92A0">(optional)</span></label>
        <div class="input" style="align-items:flex-start;padding-top:12px;padding-bottom:12px;">
          <textarea id="description" name="description" rows="3"
                    placeholder="Tell us about your products and what makes your brand unique..."
                    style="flex:1;border:0;background:none;outline:none;font-family:inherit;font-size:.97rem;color:#1B2333;resize:vertical;min-height:70px;"><?php echo htmlspecialchars($data['description'] ?? ''); ?></textarea>
        </div>
      </div>

      <hr class="rule-line" />

      <!-- ═══ Terms ════════════════════════════════════════════════════════ -->
      <label class="check">
        <input type="checkbox" id="terms" name="terms" <?php echo !empty($_POST['terms']) ? 'checked' : ''; ?> />
        <span>I agree to the <a class="link" href="<?php echo URLROOT; ?>/customer/terms" target="_blank" rel="noopener">Terms of Service</a> and <a class="link" href="<?php echo URLROOT; ?>/customer/privacy" target="_blank" rel="noopener">Privacy Policy</a></span>
      </label>
      <?php if (!empty($data['errors']['terms'])): ?>
        <p class="field__error" role="alert"><?php echo htmlspecialchars($data['errors']['terms']); ?></p>
      <?php endif; ?>

      <p class="form__error" id="formError" role="alert"></p>

      <button class="btn btn--primary btn--block" type="submit" style="margin-top:22px">
        Submit Application
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h15M13 6l6 6-6 6"/></svg>
      </button>

      <p class="signup__fine">Your application will be reviewed by our admin team. You will be notified once your account is approved.</p>
    </form>
  </section>
</main>

<script>
/* ── Password show/hide ─────────────────────────────────────────────────── */
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

/* ── Password strength meter ────────────────────────────────────────────── */
var pwInput     = document.getElementById('password');
var strengthBox = document.getElementById('strength');
var strengthVal = document.getElementById('strengthValue');
var levels      = ['', 'Weak', 'Fair', 'Good', 'Strong'];

function checkRule(id, pass) {
  var rules = { length:/^.{8,}$/, upper:/[A-Z]/, lower:/[a-z]/, number:/[0-9]/ };
  return rules[id].test(pass);
}

function updateStrength(pw) {
  var ruleEls = document.querySelectorAll('.rule[data-rule]');
  var score   = 0;
  ruleEls.forEach(function (li) {
    var met = checkRule(li.dataset.rule, pw);
    li.classList.toggle('is-met', met);
    if (met) score++;
  });
  strengthBox.dataset.level = score;
  strengthVal.textContent   = levels[score] || '';
}

pwInput.addEventListener('input', function () {
  strengthBox.hidden = pwInput.value.length === 0;
  if (!strengthBox.hidden) updateStrength(pwInput.value);
});

/* ── Confirm password match ─────────────────────────────────────────────── */
var confirmInput = document.getElementById('password_confirm');
var confirmError = document.getElementById('confirmError');
var confirmWrap  = document.getElementById('confirmWrap');

confirmInput.addEventListener('blur', function () {
  if (!confirmInput.value) return;
  var match = pwInput.value === confirmInput.value;
  confirmWrap.classList.toggle('is-invalid', !match);
  confirmWrap.classList.toggle('is-valid',   match);
  confirmError.textContent = match ? '' : 'Passwords do not match.';
});
</script>
</body>
</html>
