/**
 * Main JavaScript - Frontend Animations & Interactions
 * Azaxm Portfolio CMS
 * Vanilla JS - slowmo animations
 */

(function () {
    'use strict';

    // ── Scroll Reveal ────────────────────────────────────────
    function initScrollReveal() {
        const elements = document.querySelectorAll('.reveal');
        if (!elements.length) return;

        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.12,
            rootMargin: '0px 0px -40px 0px'
        });

        elements.forEach(function (el) {
            observer.observe(el);
        });
    }

    // ── Progress Bars Animation ───────────────────────────────
    function initProgressBars() {
        const bars = document.querySelectorAll('.progress-fill[data-level]');
        if (!bars.length) return;

        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    const fill  = entry.target;
                    const level = parseInt(fill.dataset.level, 10) || 0;
                    // Slowmo delay sesuai index
                    const delay = parseInt(fill.dataset.delay || '0', 10);
                    setTimeout(function () {
                        fill.style.width = level + '%';
                    }, delay);
                    observer.unobserve(fill);
                }
            });
        }, { threshold: 0.3 });

        bars.forEach(function (bar, idx) {
            bar.dataset.delay = idx * 80; // stagger 80ms
            observer.observe(bar);
        });
    }

    // ── Counter Animation (stat numbers) ─────────────────────
    function animateCounter(el) {
        const target   = parseInt(el.dataset.count, 10) || 0;
        const duration = 1800; // ms (slowmo)
        const start    = performance.now();

        function update(now) {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            // ease-out cubic
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.round(eased * target) + (el.dataset.suffix || '');
            if (progress < 1) {
                requestAnimationFrame(update);
            }
        }
        requestAnimationFrame(update);
    }

    function initCounters() {
        const counters = document.querySelectorAll('[data-count]');
        if (!counters.length) return;

        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(function (el) { observer.observe(el); });
    }

    // ── Typed Text Effect ─────────────────────────────────────
    function initTyped() {
        const el = document.getElementById('typedText');
        if (!el) return;

        const texts   = JSON.parse(el.dataset.texts || '[]');
        if (!texts.length) return;

        let textIdx  = 0;
        let charIdx  = 0;
        let deleting = false;
        let pausing  = false;

        function tick() {
            const current = texts[textIdx];

            if (pausing) return;

            if (!deleting && charIdx <= current.length) {
                el.textContent = current.substring(0, charIdx++);
                if (charIdx > current.length) {
                    pausing = true;
                    setTimeout(function () { pausing = false; deleting = true; }, 2200);
                }
                setTimeout(tick, 65); // typing speed (slowmo)
            } else if (deleting && charIdx >= 0) {
                el.textContent = current.substring(0, charIdx--);
                if (charIdx < 0) {
                    deleting = false;
                    textIdx  = (textIdx + 1) % texts.length;
                }
                setTimeout(tick, 38); // deleting speed
            }
        }
        tick();
    }

    // ── Flash message auto-dismiss ────────────────────────────
    function initFlashDismiss() {
        const flashes = document.querySelectorAll('.flash');
        flashes.forEach(function (flash) {
            setTimeout(function () {
                flash.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                flash.style.opacity    = '0';
                flash.style.transform  = 'translateY(-10px)';
                setTimeout(function () { flash.remove(); }, 500);
            }, 4500);
        });
    }

    // ── Smooth hover glow on cards ────────────────────────────
    function initCardGlow() {
        const cards = document.querySelectorAll('.card');
        cards.forEach(function (card) {
            card.addEventListener('mousemove', function (e) {
                const rect = card.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                card.style.setProperty('--mouse-x', x + '%');
                card.style.setProperty('--mouse-y', y + '%');
            });
        });
    }

    // ── Contact form AJAX-like submission ─────────────────────
    function initContactForm() {
        const form = document.getElementById('contactForm');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            const btn = form.querySelector('[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
            }
        });
    }

    // ── Init on DOM ready ─────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        initScrollReveal();
        initProgressBars();
        initCounters();
        initTyped();
        initFlashDismiss();
        initCardGlow();
        initContactForm();
    });

}());
