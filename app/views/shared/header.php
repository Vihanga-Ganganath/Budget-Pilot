<?php
$userRole = strtolower($_SESSION['user_role'] ?? '');
$currentUrl = $_SERVER['REQUEST_URI'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Pilot</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo URLROOT; ?>/public/assets/logos/favicon.png?v=2">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Global CSS -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/common.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/assets/css/layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/assets/css/components.css?v=<?php echo time(); ?>">
    
    <!-- Layout Components -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/assets/css/sidebar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/assets/css/topbar.css?v=<?php echo time(); ?>">
    
    <!-- Dynamic Portal CSS -->
    <?php if ($userRole === 'admin' || strpos($currentUrl, '/admin') !== false): ?>
        <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin-css/base.css?v=<?php echo time(); ?>">
        <?php
            $adminPages = ['dashboard', 'users', 'suppliers', 'products', 'notices', 'security', 'reports', 'auth'];
            foreach ($adminPages as $p) {
                if (strpos($currentUrl, '/' . $p) !== false && file_exists(APPROOT . '/../public/css/admin-css/' . $p . '.css')) {
                    echo '<link rel="stylesheet" href="' . URLROOT . '/public/css/admin-css/' . $p . '.css?v=' . time() . '">';
                    break;
                }
            }
        ?>
    <?php elseif ($userRole === 'supplier' || strpos($currentUrl, '/supplier') !== false): ?>
        <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/supplier/supplier.css?v=<?php echo time(); ?>">
    <?php else: ?>
        <!-- Customer Portal Base CSS -->
        <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/settings.css?v=<?php echo time(); ?>">
        <!-- Dynamic Customer Page CSS based on URL -->
        <?php
            $pages = ['dashboard', 'analytics', 'budgets', 'cart', 'expense', 'grocery', 'household', 'notifications', 'product', 'settings', 'catalog', 'overview'];
            foreach ($pages as $p) {
                if (strpos($currentUrl, '/' . $p) !== false && file_exists(APPROOT . '/../public/css/customer-css/' . $p . '.css')) {
                    echo '<link rel="stylesheet" href="' . URLROOT . '/public/css/customer-css/' . $p . '.css?v=' . time() . '">';
                    break;
                }
            }
        ?>
    <?php endif; ?></head>
<?php $isCustomer = ($userRole !== 'admin' && $userRole !== 'supplier' && strpos($currentUrl, '/admin') === false && strpos($currentUrl, '/supplier') === false); ?>
<body class="<?php echo $isCustomer ? 'settings' : ''; ?>" data-page="<?php echo htmlspecialchars($pageId ?? ''); ?>">

<div class="shell">
    <!-- Unified Sidebar -->
    <?php require APPROOT . '/Views/shared/sidebar.php'; ?>

    <div class="main">
        <!-- Unified Topbar -->
        <?php require_once APPROOT . '/Views/shared/topbar.php'; ?>

        <!-- Main Content Area -->
        <main class="main-content">



