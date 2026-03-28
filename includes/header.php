<?php
/**
 * Header Template
 * Azaxm Portfolio CMS
 *
 * Variabel yang harus di-set sebelum include:
 * $pageTitle   - Judul halaman
 * $pageDesc    - Deskripsi halaman (opsional)
 * $activePage  - Nama halaman aktif untuk navbar highlight
 */
if (!defined('BASE_URL')) {
    require_once dirname(__DIR__) . '/config/config.php';
}

$siteName  = e(getSetting('site_title', 'Azaxm | Portfolio'));
$siteDesc  = e($pageDesc ?? getSetting('site_description', 'Portfolio Azaxm'));
$pageTitle = isset($pageTitle) ? e($pageTitle) . ' | Azaxm' : $siteName;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $siteDesc ?>">
    <meta name="author" content="Azaxm">
    <title><?= $pageTitle ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'az-bg':      '#0a0a0a',
                        'az-card':    '#111111',
                        'az-border':  '#1e1e1e',
                        'az-primary': '#6366f1',
                        'az-secondary':'#8b5cf6',
                        'az-accent':  '#a78bfa',
                        'az-text':    '#e2e8f0',
                        'az-muted':   '#64748b',
                    }
                }
            }
        }
    </script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="bg-az-bg text-az-text" data-page="<?= e($activePage ?? '') ?>">

<!-- Overlay untuk hamburger menu -->
<div id="menuOverlay" class="menu-overlay" aria-hidden="true"></div>
