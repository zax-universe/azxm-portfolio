<?php
/**
 * Navbar Template dengan Hamburger Menu
 * Azaxm Portfolio CMS
 */
$loggedIn   = isAdminLoggedIn();
$activePage = $activePage ?? '';

$navLinks = [
    ['href' => BASE_URL . '/public/index.php',   'label' => 'Home',     'icon' => 'fa-home',          'page' => 'home'],
    ['href' => BASE_URL . '/public/about.php',   'label' => 'About Me', 'icon' => 'fa-user',          'page' => 'about'],
    ['href' => BASE_URL . '/public/skills.php',  'label' => 'Skills',   'icon' => 'fa-code',          'page' => 'skills'],
    ['href' => BASE_URL . '/public/contact.php', 'label' => 'Contact',  'icon' => 'fa-envelope',      'page' => 'contact'],
];
?>

<!-- ── Hamburger Button ───────────────────────────────── -->
<button id="hamburgerBtn"
        class="hamburger-btn"
        aria-label="Toggle navigation menu"
        aria-expanded="false"
        aria-controls="sideNav">
    <span class="hamburger-line"></span>
    <span class="hamburger-line"></span>
    <span class="hamburger-line"></span>
</button>

<!-- ── Side Navigation ───────────────────────────────── -->
<nav id="sideNav" class="side-nav" role="navigation" aria-label="Main navigation">

    <!-- Logo / Brand -->
    <div class="side-nav__brand">
        <a href="<?= BASE_URL ?>/public/index.php" class="brand-logo">
            <span class="brand-logo__bracket">&lt;</span>
            <span class="brand-logo__name">Azaxm</span>
            <span class="brand-logo__bracket">/&gt;</span>
        </a>
        <button id="closeNavBtn" class="close-nav-btn" aria-label="Close menu">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Nav Links -->
    <ul class="side-nav__links" role="list">
        <?php foreach ($navLinks as $link): ?>
        <li>
            <a href="<?= $link['href'] ?>"
               class="nav-link <?= $activePage === $link['page'] ? 'nav-link--active' : '' ?>">
                <i class="fas <?= $link['icon'] ?> nav-link__icon"></i>
                <span><?= $link['label'] ?></span>
                <?php if ($activePage === $link['page']): ?>
                    <span class="nav-link__indicator"></span>
                <?php endif; ?>
            </a>
        </li>
        <?php endforeach; ?>

        <!-- Divider -->
        <li class="nav-divider" role="separator"></li>

        <?php if ($loggedIn): ?>
        <li>
            <a href="<?= BASE_URL ?>/admin/dashboard.php"
               class="nav-link <?= $activePage === 'dashboard' ? 'nav-link--active' : '' ?>">
                <i class="fas fa-tachometer-alt nav-link__icon"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/admin/logout.php" class="nav-link nav-link--logout">
                <i class="fas fa-sign-out-alt nav-link__icon"></i>
                <span>Logout</span>
            </a>
        </li>
        <?php else: ?>
        <li>
            <a href="<?= BASE_URL ?>/admin/login.php"
               class="nav-link <?= $activePage === 'login' ? 'nav-link--active' : '' ?>">
                <i class="fas fa-sign-in-alt nav-link__icon"></i>
                <span>Login</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>

    <!-- Social Links di sidebar -->
    <div class="side-nav__social">
        <?php foreach (getSocialLinks() as $sl): ?>
        <a href="<?= e(sanitizeUrl($sl['url'])) ?>"
           class="social-icon-sm"
           target="<?= str_starts_with($sl['url'], 'mailto:') ? '_self' : '_blank' ?>"
           rel="noopener noreferrer"
           title="<?= e($sl['platform']) ?>">
            <i class="<?= e($sl['icon']) ?>"></i>
        </a>
        <?php endforeach; ?>
    </div>
</nav>

<!-- JS untuk hamburger -->
<script src="<?= BASE_URL ?>/assets/js/hamburger.js" defer></script>
