<?php
/**
 * Footer Template
 * Azaxm Portfolio CMS
 */
$footerText  = getSetting('footer_text', '© 2024 Azaxm. All rights reserved.');
$socialLinks = getSocialLinks();
?>

<footer class="site-footer">
    <div class="footer-inner">

        <!-- Brand -->
        <div class="footer-brand">
            <span class="brand-logo">
                <span class="brand-logo__bracket">&lt;</span>
                <span class="brand-logo__name">Azaxm</span>
                <span class="brand-logo__bracket">/&gt;</span>
            </span>
            <p class="footer-tagline">Full-stack Developer · Bot Developer · Mobile Developer</p>
        </div>

        <!-- Social Links -->
        <?php if ($socialLinks): ?>
        <div class="footer-social">
            <?php foreach ($socialLinks as $sl): ?>
            <a href="<?= e(sanitizeUrl($sl['url'])) ?>"
               class="footer-social__link"
               target="<?= str_starts_with($sl['url'], 'mailto:') ? '_self' : '_blank' ?>"
               rel="noopener noreferrer"
               title="<?= e($sl['platform']) ?>">
                <i class="<?= e($sl['icon']) ?>"></i>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Copyright -->
        <p class="footer-copy"><?= e($footerText) ?></p>
    </div>
</footer>

<!-- Main JS -->
<script src="<?= BASE_URL ?>/assets/js/main.js" defer></script>
</body>
</html>
