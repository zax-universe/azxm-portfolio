/**
 * Admin Panel JavaScript
 * Azaxm Portfolio CMS
 */

(function () {
    'use strict';

    // ── Mobile Sidebar Toggle ─────────────────────────────────
    function initMobileSidebar() {
        const toggleBtn = document.getElementById('adminMobileToggle');
        const sidebar   = document.getElementById('adminSidebar');
        const overlay   = document.getElementById('adminOverlay');
        if (!toggleBtn || !sidebar) return;

        function openSidebar() {
            sidebar.classList.add('is-open');
            if (overlay) overlay.classList.add('is-visible');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            sidebar.classList.remove('is-open');
            if (overlay) overlay.classList.remove('is-visible');
            document.body.style.overflow = '';
        }

        toggleBtn.addEventListener('click', function () {
            sidebar.classList.contains('is-open') ? closeSidebar() : openSidebar();
        });
        if (overlay) overlay.addEventListener('click', closeSidebar);
    }

    // ── Confirm Delete Dialogs ────────────────────────────────
    function initConfirmDelete() {
        document.querySelectorAll('[data-confirm]').forEach(function (el) {
            el.addEventListener('click', function (e) {
                const msg = el.dataset.confirm || 'Apakah Anda yakin ingin menghapus ini?';
                if (!confirm(msg)) {
                    e.preventDefault();
                }
            });
        });
    }

    // ── Live skill level preview ──────────────────────────────
    function initSkillLevelPreview() {
        const input = document.getElementById('skillLevel');
        const preview = document.getElementById('skillLevelPreview');
        if (!input || !preview) return;

        function update() {
            preview.textContent = input.value + '%';
        }
        input.addEventListener('input', update);
        update();
    }

    // ── Flash auto-dismiss ────────────────────────────────────
    function initFlashDismiss() {
        document.querySelectorAll('.flash').forEach(function (flash) {
            setTimeout(function () {
                flash.style.transition = 'opacity 0.4s ease';
                flash.style.opacity    = '0';
                setTimeout(function () { flash.remove(); }, 400);
            }, 4000);
        });
    }

    // ── Sort order inputs helper ──────────────────────────────
    function initSortableHint() {
        // Tampilkan placeholder di tabel order
        document.querySelectorAll('.order-input').forEach(function (input, idx) {
            input.placeholder = String(idx + 1);
        });
    }

    // ── Init ──────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        initMobileSidebar();
        initConfirmDelete();
        initSkillLevelPreview();
        initFlashDismiss();
        initSortableHint();
    });

}());
