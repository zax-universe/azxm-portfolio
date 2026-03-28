<?php
/**
 * Admin Header Template
 * Variabel yang dibutuhkan: $adminPageTitle
 */
if (!defined('BASE_URL')) {
    require_once dirname(__DIR__, 2) . '/config/config.php';
}
$adminUser = $_SESSION['admin_user'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($adminPageTitle ?? 'Dashboard') ?> | Admin Azaxm</title>
    <meta name="robots" content="noindex, nofollow">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body class="admin-body">

<!-- Overlay mobile -->
<div id="adminOverlay" class="menu-overlay" aria-hidden="true"></div>

<div class="admin-layout">

<!-- ── Sidebar ─────────────────────────────────────────── -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-sidebar__brand">
        <a href="<?= BASE_URL ?>/admin/dashboard.php" class="brand-logo">
            <span class="brand-logo__bracket">&lt;</span>
            <span class="brand-logo__name">Azaxm</span>
            <span class="brand-logo__bracket">/&gt;</span>
        </a>
        <span style="font-size:0.7rem;color:var(--text-muted);font-family:var(--font-mono)">CMS</span>
    </div>

    <nav class="admin-sidebar__nav" role="navigation">
        <?php
        $navItems = [
            ['href' => 'dashboard.php', 'icon' => 'fa-tachometer-alt', 'label' => 'Dashboard',    'page' => 'dashboard'],
            ['href' => 'profile.php',   'icon' => 'fa-user-edit',      'label' => 'Edit Profile',  'page' => 'profile'],
            ['href' => 'skills.php',    'icon' => 'fa-code',           'label' => 'Manage Skills', 'page' => 'skills'],
            ['href' => 'social.php',    'icon' => 'fa-share-alt',      'label' => 'Social Links',  'page' => 'social'],
            ['href' => 'messages.php',  'icon' => 'fa-envelope',       'label' => 'Messages',      'page' => 'messages'],
            ['href' => 'settings.php',  'icon' => 'fa-cog',            'label' => 'Settings',      'page' => 'settings'],
        ];
        $currentPage = basename($_SERVER['PHP_SELF'], '.php');
        foreach ($navItems as $item):
        $isActive = $currentPage === basename($item['href'], '.php');
        ?>
        <a href="<?= BASE_URL ?>/admin/<?= $item['href'] ?>"
           class="admin-nav-link <?= $isActive ? 'active' : '' ?>">
            <i class="fas <?= $item['icon'] ?>"></i>
            <?= $item['label'] ?>
            <?php if ($item['page'] === 'messages'): ?>
            <?php $unread = getUnreadMessages(); if ($unread > 0): ?>
            <span style="margin-left:auto;background:var(--primary);color:white;font-size:0.7rem;padding:0.1rem 0.45rem;border-radius:999px"><?= $unread ?></span>
            <?php endif; endif; ?>
        </a>
        <?php endforeach; ?>

        <div class="admin-nav-divider"></div>

        <a href="<?= BASE_URL ?>/public/index.php" class="admin-nav-link" target="_blank">
            <i class="fas fa-external-link-alt"></i> View Site
        </a>
        <a href="<?= BASE_URL ?>/admin/logout.php" class="admin-nav-link" style="color:var(--error)">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>

    <div class="admin-sidebar__footer">
        <div class="admin-user-info">
            <div class="admin-user-avatar"><?= strtoupper(substr($adminUser, 0, 1)) ?></div>
            <div>
                <div class="admin-user-name"><?= e($adminUser) ?></div>
                <div class="admin-user-role">Administrator</div>
            </div>
        </div>
    </div>
</aside>

<!-- ── Main Content ────────────────────────────────────── -->
<main class="admin-main">
    <!-- Topbar -->
    <div class="admin-topbar">
        <div style="display:flex;align-items:center;gap:0.875rem">
            <button class="admin-mobile-toggle" id="adminMobileToggle" aria-label="Toggle sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <span class="admin-page-title"><?= e($adminPageTitle ?? 'Dashboard') ?></span>
        </div>
        <div class="admin-topbar-actions">
            <span style="font-size:0.78rem;color:var(--text-muted);font-family:var(--font-mono)">
                <?= date('d M Y, H:i') ?>
            </span>
        </div>
    </div>

    <!-- Content -->
    <div class="admin-content">
        <!-- Flash messages -->
        <?= displayFlash() ?>
