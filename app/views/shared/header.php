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
    
    <!-- Global Base & Layout Shell -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/common.css?v=<?php echo time(); ?>">
    
    <!-- Dedicated Layout Components -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/assets/css/sidebar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/assets/css/topbar.css?v=<?php echo time(); ?>">
    
    <!-- Optional Per-Page Stylesheet Hook -->
    <?php if (!empty($pageCss)): ?>
        <?php foreach ((array)$pageCss as $cssFile): ?>
            <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/<?php echo htmlspecialchars($cssFile); ?>.css?v=<?php echo time(); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
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
