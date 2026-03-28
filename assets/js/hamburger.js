/**
 * Hamburger Menu - Side Navigation
 * Azaxm Portfolio CMS
 * Vanilla JS - no dependencies
 */

(function () {
    'use strict';

    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const closeNavBtn  = document.getElementById('closeNavBtn');
    const sideNav      = document.getElementById('sideNav');
    const overlay      = document.getElementById('menuOverlay');

    if (!hamburgerBtn || !sideNav) return;

    function openMenu() {
        sideNav.classList.add('is-open');
        overlay.classList.add('is-visible');
        hamburgerBtn.classList.add('is-open');
        hamburgerBtn.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
        // Fokus ke tombol close untuk aksesibilitas
        setTimeout(() => closeNavBtn && closeNavBtn.focus(), 300);
    }

    function closeMenu() {
        sideNav.classList.remove('is-open');
        overlay.classList.remove('is-visible');
        hamburgerBtn.classList.remove('is-open');
        hamburgerBtn.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
        hamburgerBtn.focus();
    }

    // Toggle menu
    hamburgerBtn.addEventListener('click', function () {
        if (sideNav.classList.contains('is-open')) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    // Tombol close di dalam menu
    if (closeNavBtn) {
        closeNavBtn.addEventListener('click', closeMenu);
    }

    // Klik overlay untuk menutup
    overlay.addEventListener('click', closeMenu);

    // Escape key untuk menutup
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && sideNav.classList.contains('is-open')) {
            closeMenu();
        }
    });

    // Tutup menu saat resize ke layar besar (opsional)
    window.addEventListener('resize', function () {
        if (window.innerWidth > 1200 && sideNav.classList.contains('is-open')) {
            // Opsional: biarkan menu tetap terbuka di desktop
        }
    });

}());
