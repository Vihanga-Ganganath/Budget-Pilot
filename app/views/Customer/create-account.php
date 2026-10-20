<?php
// Values typed before a failed submit, so the user does not lose them
$old = $data['old'] ?? [];
function oldValue($old, $key) { return htmlspecialchars($old[$key] ?? ''); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Create Account  Budget Pilot</title>
<link rel="icon" type="image/png" href="<?php echo URLROOT; ?>/public/assets/logos/favicon.png?v=2">
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/auth.css?v=<?php echo @filemtime(APPROOT . '/../public/css/customer-css/auth.css'); ?>" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/assets/css/login.css?v=<?php echo @filemtime(APPROOT . '/../public/assets/css/login.css'); ?>" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/create-account.css?v=<?php echo @filemtime(APPROOT . '/../public/css/customer-css/create-account.css'); ?>" />
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/assets/css/sidebar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/assets/css/topbar.css?v=<?php echo time(); ?>">
</head>
<body class="signup">

<header class="page-head">
  <a class="page-head__back" href="<?php echo URLROOT; ?>/customer/index"> Back to Home</a>

  <a href="<?php echo URLROOT; ?>/" class="login-brand" aria-label="Budget Pilot home">
    <div class="login-brand__mark">
      <img src="<?php echo URLROOT; ?>/public/assets/logos/budget-pilot-mark.png" alt="">
    </div>
    <span class="login-brand__title">Budget Pilot</span>
  </a>

  <p class="page-head__alt"><a class="link" href="<?php echo URLROOT; ?>/customer/login">Back to Login</a>
</header>

<main class="signup__wrap">
  <section class="signup-card">

    <form action="<?php echo URLROOT; ?>/customer/register" method="post" enctype="multipart/form-data" novalidate>

    <h1 class="signup__title" id="pageTitle">Let's get started</h1>
    <p class="signup__sub" id="pageSub">Begin your journey to financial freedom with a secure account.</p>

    <div id="ownerSection">
    <!-- ============ Profile photo ============ -->
    <div class="field">
      <span class="label">Profile photo</span>
      <input id="ownerPhoto" name="photo" type="file" accept="image/*" />
      <p class="photo__hint" style="margin-top:6px">JPG, PNG or WEBP, up to 2 MB. Optional.</p>
    </div>

    <!-- ============ Account details ============ -->
    <div class="field">
      <label class="label" for="fullName">Full name</label>
      <div class="input"><input id="fullName" name="name" type="text" placeholder="John Doe" autocomplete="name" value="<?php echo oldValue($old, 'name'); ?>" /></div>
    </div>

    <div class="field">
      <label class="label" for="email">Email address</label>
      <div class="input"><input id="email" name="email" type="email" placeholder="name@company.com" autocomplete="email" value="<?php echo oldValue($old, 'email'); ?>" /></div>
    </div>

    <div class="field">
      <label class="label" for="password">Password</label>
      <div class="input">
        <input id="password" name="password" type="password" placeholder="" autocomplete="new-password" />
      </div>
      <p class="photo__hint" style="margin-top:6px">At least 8 characters.</p>
    </div>

    <div class="field">
      <label class="label" for="nic">National ID card (NIC)</label>
      <div class="input"><input id="nic" name="nic" type="text" placeholder="Enter your NIC number" value="<?php echo oldValue($old, 'nic'); ?>" /></div>
    </div>

    <div class="grid-2">
      <div class="field">
        <label class="label" for="gender">Gender</label>
        <div class="input input--select">
          <select id="gender" name="gender">
            <option value="">Select Gender</option>
            <option value="female" <?php echo ($old['gender'] ?? '') == 'female' ? 'selected' : ''; ?>>Female</option>
            <option value="male" <?php echo ($old['gender'] ?? '') == 'male' ? 'selected' : ''; ?>>Male</option>
          </select>
          <span class="input__chevron" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
          </span>
        </div>
      </div>

      <div class="field">
        <label class="label" for="age">Age</label>
        <div class="input"><input id="age" name="age" type="number" min="13" max="120" placeholder="25" value="<?php echo oldValue($old, 'age'); ?>" /></div>
      </div>
    </div>

    <hr class="rule-line" />
    </div><!-- /#ownerSection -->


    <div id="financeSection">
    <hr class="rule-line" />

    <!-- ============ Financial profile ============ -->
    <h2 class="signup__section">Setup your financial profile</h2>
    <p class="signup__sub">We use this to calibrate your personalized copilot insights.</p>

    <div class="field">
      <label class="label" for="income">Monthly after-tax income</label>
      <div class="input"><span class="input__prefix">$</span><input id="income" name="income" type="number" min="0" step="0.01" placeholder="5,000" value="<?php echo oldValue($old, 'income'); ?>" /></div>
    </div>

    <div class="field">
      <label class="label" for="savings">Target monthly savings</label>
      <div class="input"><span class="input__prefix">$</span><input id="savings" name="savings" type="number" min="0" step="0.01" placeholder="1,000" value="<?php echo oldValue($old, 'savings'); ?>" /></div>
    </div>

    <label class="check">
      <input type="checkbox" id="terms" name="terms" value="1" <?php echo !empty($old['terms']) ? 'checked' : ''; ?> />
      <span>I agree to the <a class="link" href="<?php echo URLROOT; ?>/customer/terms" target="_blank" rel="noopener">Terms of Service</a> and <a class="link" href="<?php echo URLROOT; ?>/customer/privacy" target="_blank" rel="noopener">Privacy Policy</a></span>
    </label>
    </div><!-- /#financeSection -->

    <p class="form__error" id="formError" role="alert"><?php echo htmlspecialchars($data['error'] ?? ''); ?></p>

    <button class="btn btn--primary btn--block" type="submit" id="createBtn">
      <span id="createBtnLabel">Create Account</span>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h15M13 6l6 6-6 6"/></svg>
    </button>

    <p class="signup__fine" id="finePrint">By creating an account, you agree to our processing of personal data.</p>
    </form>
  </section>
</main>


</body>
</html>
