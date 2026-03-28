<?php
/**
 * Halaman Home
 * Azaxm Portfolio CMS
 */
require_once dirname(__DIR__) . '/config/config.php';

$activePage = 'home';
$pageTitle  = 'Home';
$pageDesc   = getSetting('site_description');

logPageVisit('home');

$profile = getProfile();
?>
<?php require_once BASE_PATH . '/includes/header.php'; ?>
<?php require_once BASE_PATH . '/includes/navbar.php'; ?>

<!-- ── Hero Section ──────────────────────────────────────── -->
<section class="hero">
    <div class="hero-grid-bg" aria-hidden="true"></div>
    <div class="container">
        <div class="hero-content">

            <p class="hero-greeting">
                <span style="color:var(--text-muted)">~/</span>
                <span id="typedText"
                      data-texts='["Full-stack Developer","Bot Developer","Mobile Developer","Problem Solver"]'
                      class="mono"></span>
                <span style="color:var(--primary);animation:pulse 1s infinite">_</span>
            </p>

            <h1 class="hero-name gradient-text">
                <?= e($profile['full_name'] ?? 'Azaxm') ?>
            </h1>

            <p class="hero-tagline">
                <?= e($profile['bio'] ? (strlen($profile['bio']) > 160 ? substr($profile['bio'], 0, 160) . '…' : $profile['bio']) : 'Membangun solusi digital yang inovatif, aman, dan efisien.') ?>
            </p>

            <div class="hero-cta">
                <a href="<?= BASE_URL ?>/public/contact.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-envelope"></i> Contact Me
                </a>
                <a href="<?= BASE_URL ?>/public/skills.php" class="btn btn-outline btn-lg">
                    <i class="fas fa-code"></i> View Skills
                </a>
            </div>
        </div>
    </div>

    <!-- Scroll indicator -->
    <div style="position:absolute;bottom:2rem;left:50%;transform:translateX(-50%);color:var(--text-muted);font-size:0.75rem;text-align:center;animation:fadeSlideUp 0.6s ease 0.8s both">
        <i class="fas fa-chevron-down" style="animation:pulse 2s infinite;display:block;margin-bottom:0.25rem"></i>
        scroll
    </div>
</section>

<!-- ── Stats Section ─────────────────────────────────────── -->
<section class="section" style="padding-top:3rem;padding-bottom:3rem">
    <div class="container">
        <div class="grid-4" style="gap:1rem">

            <div class="card stat-card reveal">
                <span class="stat-card__number" data-count="<?= (int)($profile['experience_years'] ?? 5) ?>" data-suffix="+">0</span>
                <span class="stat-card__label">Tahun Pengalaman</span>
            </div>

            <div class="card stat-card reveal reveal-delay-1">
                <span class="stat-card__number" data-count="<?= (int)($profile['projects_completed'] ?? 50) ?>" data-suffix="+">0</span>
                <span class="stat-card__label">Proyek Selesai</span>
            </div>

            <div class="card stat-card reveal reveal-delay-2">
                <span class="stat-card__number" data-count="3" data-suffix="">0</span>
                <span class="stat-card__label">Platform Bot</span>
            </div>

            <div class="card stat-card reveal reveal-delay-3">
                <span class="stat-card__number" data-count="<?= getTotalVisits() ?>">0</span>
                <span class="stat-card__label">Total Kunjungan</span>
            </div>

        </div>
    </div>
</section>

<!-- ── What I Do ─────────────────────────────────────────── -->
<section class="section">
    <div class="container">
        <div class="section-heading reveal">
            <h2 class="section-title">What I Do</h2>
        </div>
        <p class="section-subtitle reveal">Bidang spesialisasi utama saya</p>

        <div class="grid-3" style="gap:1.5rem">

            <div class="card reveal" style="padding:2rem">
                <div style="font-size:2rem;margin-bottom:1rem">
                    <i class="fas fa-globe" style="background:var(--gradient-text);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text"></i>
                </div>
                <h3 style="margin-bottom:0.75rem;font-size:1.1rem">Website Development</h3>
                <p style="color:var(--text-muted);font-size:0.9rem;line-height:1.7">
                    Pengembangan website full-stack dari frontend yang menarik hingga backend yang aman dan skalabel.
                </p>
                <div style="margin-top:1rem;display:flex;gap:0.5rem;flex-wrap:wrap">
                    <span class="badge">PHP</span>
                    <span class="badge">Node.js</span>
                    <span class="badge">React</span>
                </div>
            </div>

            <div class="card reveal reveal-delay-1" style="padding:2rem">
                <div style="font-size:2rem;margin-bottom:1rem">
                    <i class="fas fa-robot" style="background:var(--gradient-text);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text"></i>
                </div>
                <h3 style="margin-bottom:0.75rem;font-size:1.1rem">Bot Development</h3>
                <p style="color:var(--text-muted);font-size:0.9rem;line-height:1.7">
                    Pengembangan bot pintar untuk WhatsApp, Telegram, dan Discord dengan fitur otomatisasi canggih.
                </p>
                <div style="margin-top:1rem;display:flex;gap:0.5rem;flex-wrap:wrap">
                    <span class="badge"><i class="fab fa-whatsapp"></i> WA</span>
                    <span class="badge"><i class="fab fa-telegram"></i> TG</span>
                    <span class="badge"><i class="fab fa-discord"></i> DC</span>
                </div>
            </div>

            <div class="card reveal reveal-delay-2" style="padding:2rem">
                <div style="font-size:2rem;margin-bottom:1rem">
                    <i class="fas fa-mobile-alt" style="background:var(--gradient-text);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text"></i>
                </div>
                <h3 style="margin-bottom:0.75rem;font-size:1.1rem">Mobile Development</h3>
                <p style="color:var(--text-muted);font-size:0.9rem;line-height:1.7">
                    Pengembangan aplikasi mobile cross-platform untuk Android dan iOS yang performatif dan elegant.
                </p>
                <div style="margin-top:1rem;display:flex;gap:0.5rem;flex-wrap:wrap">
                    <span class="badge">Flutter</span>
                    <span class="badge">Kotlin</span>
                    <span class="badge">Java</span>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ── CTA Section ───────────────────────────────────────── -->
<section style="padding:4rem 0">
    <div class="container">
        <div class="card reveal" style="padding:3rem;text-align:center;background:linear-gradient(135deg,rgba(99,102,241,0.08),rgba(139,92,246,0.08));border-color:rgba(99,102,241,0.2)">
            <h2 style="font-size:1.8rem;margin-bottom:0.75rem">Ready to work together?</h2>
            <p style="color:var(--text-muted);margin-bottom:2rem;max-width:480px;margin-left:auto;margin-right:auto">
                Mari diskusikan proyek Anda dan wujudkan ide menjadi kenyataan.
            </p>
            <a href="<?= BASE_URL ?>/public/contact.php" class="btn btn-primary btn-lg">
                <i class="fas fa-paper-plane"></i> Mulai Diskusi
            </a>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
