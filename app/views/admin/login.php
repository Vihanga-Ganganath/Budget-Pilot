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
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/common.css?v=<?php echo filemtime(APPROOT . '/../public/css/common.css'); ?>">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin_auth.css">
</head>
<body data-page="login">

    <!-- Brand — matches customer login brand--center style -->
    <style>
        .auth-brand-center {
            display: flex;
            align-items: center;
            gap: 11px;
            justify-content: center;
            text-decoration: none;
            margin-bottom: 28px;
        }
        .auth-brand-center__mark {
            width: 40px; height: 40px;
            min-width: 40px; min-height: 40px;
            max-width: 40px; max-height: 40px;
            border-radius: 10px;
            background: #fff;
            border: 1px solid #E6E9F0;
            overflow: hidden;
            display: grid;
            place-items: center;
            flex: none;
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
        }
        .auth-brand-center__mark img {
            width: 100%; height: 100%;
            object-fit: contain;
            padding: 4px;
            display: block;
        }
        .auth-brand-center__name {
            font-family: 'Outfit', 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.18rem;
            color: #101C56;
            letter-spacing: -.01em;
            line-height: 1;
        }
    </style>

    <a href="<?php echo URLROOT; ?>" class="auth-brand-center" aria-label="Budget Pilot home">
        <span class="auth-brand-center__mark" aria-hidden="true">
            <img src="<?php echo URLROOT; ?>/public/assets/logos/budget-pilot-mark.png" alt="">
        </span>
        <span class="auth-brand-center__name">Budget Pilot</span>
    </a>



    <!-- Login Card -->
    <div class="admin-auth-card">
        <form action="<?php echo URLROOT; ?>/admin/login" method="POST">

            <div class="admin-form-group">
                <div class="admin-form-header">
                    <label>Email Address</label>
                </div>
                <div class="admin-input-wrapper">
                    <span class="admin-input-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </span>
                    <input type="email" name="email" placeholder="admin@budgetpilot.com" required>
                </div>
            </div>

            <div class="admin-form-group">
                <div class="admin-form-header">
                    <label>Password</label>
                    <a href="password_reset_sent.html" class="admin-forgot-link">Forgot Password?</a>
                </div>
                <div class="admin-input-wrapper">
                    <span class="admin-input-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </span>
                    <input type="password" name="password" id="admin-password" placeholder="••••••••" required>
                    <span class="admin-action-icon" onclick="const p=document.getElementById('admin-password'); p.type = p.type === 'password' ? 'text' : 'password';">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </span>
                </div>
            </div>

            <div class="admin-remember-row">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="admin-submit-btn">Sign In</button>
        </form>

        <div class="admin-auth-footer">
            Need an admin account? <a href="admin_register.html">Register</a>
        </div>
    </div>

    <a href="<?php echo URLROOT; ?>" class="admin-back-link">← Back to Home</a>

    <script src="<?php echo URLROOT; ?>/public/js/admin.js"></script>

    <!-- ... ඔයාගේ Login Form එක සහ අනිත් HTML කේත ... -->

    <script src="<?php echo URLROOT; ?>/public/js/admin.js"></script>

    <!-- PHP වලින් Error එකක් ඇවිත් තියෙනවා නම් Toast එක පෙන්වන්න -->
    <?php if(!empty($data['error'])) : ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                toast('<?php echo $data['error']; ?>', 'error');
            });
        </script>
    <?php endif; ?>

    <!-- PHP වලින් Success එකක් ඇවිත් තියෙනවා නම් Toast එක පෙන්වලා Dashboard එකට යවන්න -->
    <?php if(!empty($data['success'])) : ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                toast('<?php echo $data['success']; ?>', 'success');
                
                // තත්පරයකට පස්සේ ඇත්තටම Dashboard එකට Redirect කරනවා!
                setTimeout(() => { 
                    window.location.href = '<?php echo URLROOT; ?>/admin/dashboard'; 
                }, 1000);
            });
        </script>
    <?php endif; ?>

</body>
</html>
