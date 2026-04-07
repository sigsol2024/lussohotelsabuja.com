<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Dashboard';
}

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
$isPagesEditor = (strpos($currentPath, '/pages/') !== false && strpos($currentPath, 'pages/rooms/') === false && strpos($currentPath, 'pages/media') === false && strpos($currentPath, 'pages/settings') === false && strpos($currentPath, 'pages/profile') === false);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= generateCSRFToken() ?>">
  <script>window.ADMIN_URL = <?= json_encode(ADMIN_URL) ?>;</script>
  <title><?= sanitize($pageTitle) ?> - Lusso CMS Admin</title>
  <link rel="stylesheet" href="<?= ADMIN_URL ?>assets/css/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="admin-wrapper">
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <h2>Lusso CMS</h2>
        <button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="Toggle sidebar">
          <i class="fas fa-bars"></i>
        </button>
      </div>
      <nav class="sidebar-nav">
        <ul>
          <li><a href="<?= ADMIN_URL ?>dashboard.php" class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
          <li>
            <a href="<?= ADMIN_URL ?>pages/pages-list.php" class="<?= ($currentPage === 'pages-list' || $currentPage === 'homepage' || $isPagesEditor) ? 'active' : '' ?>">
              <i class="fas fa-file-alt"></i><span>Pages</span>
            </a>
          </li>
          <li><a href="<?= ADMIN_URL ?>pages/rooms/list.php" class="<?= strpos($currentPath, 'rooms') !== false ? 'active' : '' ?>"><i class="fas fa-bed"></i><span>Rooms</span></a></li>
          <li><a href="<?= ADMIN_URL ?>pages/media.php" class="<?= strpos($currentPath, 'media') !== false ? 'active' : '' ?>"><i class="fas fa-folder"></i><span>Media</span></a></li>
          <li><a href="<?= ADMIN_URL ?>pages/settings.php" class="<?= $currentPage === 'settings' ? 'active' : '' ?>"><i class="fas fa-cog"></i><span>Settings</span></a></li>
          <li class="sidebar-divider"></li>
          <li><a href="<?= ADMIN_URL ?>pages/profile.php" class="<?= strpos($currentPath, 'profile') !== false ? 'active' : '' ?>"><i class="fas fa-user"></i><span>My Profile</span></a></li>
          <li><a href="<?= ADMIN_URL ?>api/auth/logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
        </ul>
      </nav>
    </aside>

    <div class="main-content">
      <header class="top-header">
        <div class="header-left">
          <button class="mobile-menu-toggle" id="mobileMenuToggle" type="button" aria-label="Toggle mobile menu">
            <i class="fas fa-bars"></i>
          </button>
          <h1><?= sanitize($pageTitle) ?></h1>
        </div>
        <div class="header-right">
          <span class="user-info">Welcome, <strong><?= sanitize(getCurrentUsername()) ?></strong></span>
          <a href="<?= SITE_URL ?>" target="_blank" class="btn btn-sm btn-outline"><i class="fas fa-external-link-alt"></i> View Site</a>
        </div>
      </header>

      <div class="content-area">

