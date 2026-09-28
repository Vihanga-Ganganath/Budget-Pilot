<?php
// Base URL of your website
define('URLROOT', 'http://localhost/BudgetPilot');

// (Optional) App Root for easily requiring files later
define('APPROOT', dirname(dirname(__FILE__)));

// Show real database errors in API replies. Set to false before going live.
define('APP_DEBUG', true);

// ---------------- EMAIL (Gmail SMTP) ----------------
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 465);
define('MAIL_USER', 'ovindu04naleesha@gmail.com');
define('MAIL_PASS', 'vftuncvdicjuzwif');
define('MAIL_FROM_NAME', 'Budget Pilot');

// ---------------- VERIFICATION RULES ----------------
define('CODE_MINUTES', 15);   // how long a verification code stays valid
define('MAX_ATTEMPTS', 5);    // wrong guesses allowed per code
define('RESEND_SECONDS', 60); // wait time between "resend" requests
?>