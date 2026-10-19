<?php
// පද්ධතියේ Sessions ආරම්භ කිරීම
session_start();
// 1. Load the configuration file (This is where URLROOT is!)
require_once '../app/Config/config.php';

// 2. Load the database configuration
require_once '../app/Config/Database.php';

// Enforce admin-triggered session revocation on the next request.
if (!empty($_SESSION['user_id'])) {
    require_once '../app/Models/SecurityAudit.php';
    try {
        $security = new SecurityAudit();
        if ($security->isRevoked()) {
            $_SESSION = [];
            session_regenerate_id(true);
            header('Location: '.URLROOT.'/admin/login'); exit();
        }
        $security->touchSession((int)$_SESSION['user_id']);
    } catch (Throwable $e) { error_log('Session audit: '.$e->getMessage()); }
}
// 3. Load the core MVC libraries
require_once '../app/Libraries/Core.php';
require_once '../app/Libraries/Controller.php';

// 4. Initialize the Router (This starts the whole system)
$init = new Core();
?>