<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Budget Pilot</title>
    <link rel="icon" type="image/png" href="<?php echo URLROOT; ?>/public/assets/logos/favicon.png?v=2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/common.css?v=<?php echo @filemtime(APPROOT . '/../public/css/common.css'); ?>">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/assets/css/login.css?v=<?php echo @filemtime(APPROOT . '/../public/assets/css/login.css'); ?>">
</head>
<body class="login-page">

    <a href="<?php echo URLROOT; ?>/" class="login-brand" aria-label="Budget Pilot home">
        <div class="login-brand__mark">
            <img src="<?php echo URLROOT; ?>/public/assets/logos/budget-pilot-mark.png" alt="">
        </div>
        <span class="login-brand__title">Budget Pilot</span>
    </a>

    <div class="login-card">
        <div class="login-card__header">
            <h1 class="login-card__title">Admin Portal</h1>
            <p class="login-card__sub">Sign in to manage the platform.</p>
        </div>

        <?php if (!empty($data['error']) || !empty($data['email_err']) || !empty($data['password_err'])): ?>
            <div class="login-alert login-alert--error" style="background: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 500; border: 1px solid #f87171; text-align: center;">
                <?php 
                    if (!empty($data['error'])) echo $data['error']; 
                    else echo !empty($data['email_err']) ? $data['email_err'] : $data['password_err']; 
                ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo URLROOT; ?>/admin/login" method="post">
            <div class="form-group">
                <div class="form-header">
                    <label for="email">Email Address</label>
                </div>
                <div class="input-wrapper">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    <input type="email" id="email" name="email" value="<?php echo isset($data['email']) ? htmlspecialchars($data['email']) : ''; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <div class="form-header">
                    <label for="password">Password</label>
                    <a href="#">Forgot?</a>
                </div>
                <div class="input-wrapper">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    <input type="password" id="password" name="password" required>
                    <button type="button" class="input-action js-toggle-password" data-target="password" aria-label="Toggle password visibility">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </button>
                </div>
            </div>

            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember" <?php echo (isset($_COOKIE['admin_remember']) || isset($data['remember'])) ? 'checked' : ''; ?>>
                <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="login-submit-btn">Sign In</button>
        </form>
    </div>

    <a href="<?php echo URLROOT; ?>/" class="login-back-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Back to Home
    </a>

    <script src="<?php echo URLROOT; ?>/public/assets/js/login.js?v=<?php echo @filemtime(APPROOT . '/../public/assets/js/login.js'); ?>"></script>
</body>
</html>

