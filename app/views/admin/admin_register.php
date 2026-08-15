<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration - Budget Pilot</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/common.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin_auth.css">
</head>
<body data-page="register">

    <!-- Brand -->
    <div class="admin-brand-header">
        <div class="admin-brand-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 5H5C3.9 5 3 5.9 3 7V17C3 18.1 3.9 19 5 19H19C20.1 19 21 18.1 21 17V7C21 5.9 20.1 5 19 5ZM19 17H5V11H19V17ZM19 9H5V7H19V9Z" fill="white"/>
            </svg>
        </div>
        <div class="admin-brand-title">Budget Pilot</div>
        <div class="admin-brand-subtitle">Create Admin Account</div>
    </div>

    <!-- Registration Card -->
    <div class="admin-auth-card">
        <form id="admin-register-form" action="process_admin_register.php" method="POST">

            <div class="admin-form-group">
                <div class="admin-form-header">
                    <label>Full Name</label>
                </div>
                <div class="admin-input-wrapper">
                    <span class="admin-input-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </span>
                    <input type="text" name="full_name" placeholder="Jane Doe" required>
                </div>
            </div>

            <div class="admin-form-group">
                <div class="admin-form-header">
                    <label>Work Email</label>
                </div>
                <div class="admin-input-wrapper">
                    <span class="admin-input-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </span>
                    <input type="email" name="email" placeholder="jane@company.com" required>
                </div>
            </div>

            <div class="admin-form-group">
                <div class="admin-form-header">
                    <label>Password</label>
                </div>
                <div class="admin-input-wrapper">
                    <span class="admin-input-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </span>
                    <input type="password" name="password" id="reg-password" placeholder="••••••••" required
                           oninput="checkPasswordStrength(this.value)">
                    <span class="admin-action-icon" onclick="const p=document.getElementById('reg-password'); p.type = p.type === 'password' ? 'text' : 'password';">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </span>
                </div>
            </div>

            <!-- Password Requirements -->
            <div class="admin-password-requirements">
                <div class="admin-password-requirements-title">Password Requirements</div>
                <div class="admin-requirement-item" id="req-length"><span class="req-dot"></span> At least 8 characters long</div>
                <div class="admin-requirement-item" id="req-special"><span class="req-dot"></span> Contains a special character (!@#$%^&amp;*)</div>
                <div class="admin-requirement-item" id="req-number"><span class="req-dot"></span> Contains a number</div>
            </div>

            <button type="submit" class="admin-submit-btn">Register Account</button>
        </form>

        <div class="admin-auth-footer">
            Already have an account? <a href="admin_login.html">Sign In</a>
        </div>
    </div>

    <a href="index.php" class="admin-back-link">← Back to Home</a>

    <script>
        function checkPasswordStrength(value) {
            const lengthOk = value.length >= 8;
            const specialOk = /[!@#$%^&*]/.test(value);
            const numberOk = /[0-9]/.test(value);

            document.getElementById('req-length').classList.toggle('met', lengthOk);
            document.getElementById('req-special').classList.toggle('met', specialOk);
            document.getElementById('req-number').classList.toggle('met', numberOk);
        }
    </script>
    <script src="<?php echo URLROOT; ?>/public/js/admin.js"></script>
</body>
</html>
