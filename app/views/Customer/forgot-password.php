<?php $step = $data['step']; // email -> pin (only if two-factor is on) -> password ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Budget Pilot</title>
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
            <h1 class="login-card__title">Forgot Password</h1>
            <p class="login-card__sub">
                <?php if ($step == 'email'): ?>Enter the email you use to log in.
                <?php elseif ($step == 'pin'): ?>Two-factor is on for this account. Enter your 6-digit security PIN.
                <?php else: ?>Choose a new password for your account.
                <?php endif; ?>
            </p>
        </div>

        <?php if (!empty($data['error'])): ?>
            <div class="login-alert login-alert--error"><?php echo htmlspecialchars($data['error']); ?></div>
        <?php endif; ?>

        <form action="<?php echo URLROOT; ?>/customer/forgot" method="post">
            <input type="hidden" name="step" value="<?php echo $step; ?>">

            <?php if ($step == 'email' || $step == 'pin'): ?>
                <div class="form-group">
                    <div class="form-header"><label for="email">Email Address</label></div>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" required style="padding-left:14px;" <?php echo $step == 'pin' ? 'readonly' : ''; ?>>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($step == 'pin'): ?>
                <div class="form-group">
                    <div class="form-header"><label for="pin">Security PIN</label></div>
                    <div class="input-wrapper">
                        <input type="password" id="pin" name="pin" inputmode="numeric" maxlength="6" placeholder="6 digits" required autofocus style="padding-left:14px; letter-spacing:.3em;">
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($step == 'password'): ?>
                <div class="form-group">
                    <div class="form-header"><label for="new">New Password</label></div>
                    <div class="input-wrapper">
                        <input type="password" id="new" name="new" placeholder="At least 8 characters" required style="padding-left:14px;">
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-header"><label for="confirm">Confirm New Password</label></div>
                    <div class="input-wrapper">
                        <input type="password" id="confirm" name="confirm" required style="padding-left:14px;">
                    </div>
                </div>
            <?php endif; ?>

            <button type="submit" class="login-submit-btn"><?php echo $step == 'password' ? 'Change Password' : 'Continue'; ?></button>
        </form>

        <div class="login-footer">
            Remembered it? <a href="<?php echo URLROOT; ?>/customer/login">Back to login</a>
        </div>
    </div>

</body>
</html>
