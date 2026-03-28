<?php
/**
 * Halaman About Me
 * Azaxm Portfolio CMS
 */
require_once dirname(__DIR__) . '/config/config.php';

$activePage = 'about';
$pageTitle  = 'About Me';
$pageDesc   = 'Kenali lebih dekat Azaxm - Full-stack Developer, Bot Developer, Mobile Developer';

logPageVisit('about');

$profile = getProfile();

// Ambil semua skill categories + skills
$db   = getDB();
$cats = $db->query(
    "SELECT sc.*, GROUP_CONCAT(s.name ORDER BY s.display_order SEPARATOR '||') as skill_names,
            GROUP_CONCAT(s.icon ORDER BY s.display_order SEPARATOR '||') as skill_icons
     FROM skill_categories sc
     LEFT JOIN skills s ON s.category_id = sc.id AND s.is_visible = 1
     GROUP BY sc.id
     ORDER BY sc.display_order ASC"
)->fetchAll();
?>
<?php require_once BASE_PATH . '/includes/header.php'; ?>
<?php require_once BASE_PATH . '/includes/navbar.php'; ?>

<div class="page-wrapper">
<div class="container">

    <!-- Page Header -->
    <div style="padding:3rem 0 2rem;opacity:0;animation:fadeSlideUp 0.5s ease both">
        <p class="mono" style="color:var(--primary);font-size:0.85rem;margin-bottom:0.5rem">// about.php</p>
        <h1 class="section-title gradient-text">About Me</h1>
        <p style="color:var(--text-muted)">Siapa saya dan apa yang saya lakukan</p>
    </div>

    <!-- Profile Block -->
    <div class="card reveal" style="padding:2.5rem;margin-bottom:2rem">
        <div style="display:flex;gap:2.5rem;align-items:flex-start;flex-wrap:wrap">

            <!-- Avatar -->
            <div style="flex-shrink:0">
                <div class="avatar-ring">
                    <img src="<?= e(avatarUrl($profile['avatar'] ?? '')) ?>"
                         alt="<?= e($profile['full_name'] ?? 'Azaxm') ?>"
                         onerror="this.src='<?= BASE_URL ?>/assets/images/default-avatar.svg'">
                </div>
                <!-- Location badge -->
                <?php if (!empty($profile['location'])): ?>
                <div style="margin-top:1rem;display:flex;align-items:center;gap:0.5rem;color:var(--text-muted);font-size:0.82rem;justify-content:center">
                    <i class="fas fa-map-marker-alt" style="color:var(--primary)"></i>
                    <?= e($profile['location']) ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Bio -->
            <div style="flex:1;min-width:280px">
                <h2 style="font-size:1.6rem;margin-bottom:0.25rem">
                    <?= e($profile['full_name'] ?? 'Azaxm') ?>
                </h2>
                <p style="color:var(--accent);font-size:0.9rem;margin-bottom:1.25rem;font-family:var(--font-mono)">
                    <?= e($profile['title'] ?? 'Full-stack Developer') ?>
                </p>
                <p style="color:var(--text-light);line-height:1.8;font-size:0.95rem;margin-bottom:1.5rem">
                    <?= nl2br(e($profile['bio'] ?? '')) ?>
                </p>

                <!-- Quick info -->
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:0.75rem">
                    <?php if (!empty($profile['email'])): ?>
                    <div style="display:flex;align-items:center;gap:0.625rem;font-size:0.875rem;color:var(--text-muted)">
                        <i class="fas fa-envelope" style="color:var(--primary);width:16px"></i>
                        <a href="mailto:<?= e($profile['email']) ?>" style="color:var(--text-light);word-break:break-all"><?= e($profile['email']) ?></a>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($profile['phone'])): ?>
                    <div style="display:flex;align-items:center;gap:0.625rem;font-size:0.875rem;color:var(--text-muted)">
                        <i class="fas fa-phone" style="color:var(--primary);width:16px"></i>
                        <span style="color:var(--text-light)"><?= e($profile['phone']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($profile['github_url'])): ?>
                    <div style="display:flex;align-items:center;gap:0.625rem;font-size:0.875rem;color:var(--text-muted)">
                        <i class="fab fa-github" style="color:var(--primary);width:16px"></i>
                        <a href="<?= e(sanitizeUrl($profile['github_url'])) ?>" target="_blank" rel="noopener" style="color:var(--text-light)">GitHub</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Divider -->
    <div class="divider"></div>

    <!-- Skill Areas -->
    <div style="margin-bottom:3rem">
        <div class="section-heading reveal">
            <h2 style="font-size:1.4rem;font-weight:600">Skill Areas</h2>
        </div>
        <p style="color:var(--text-muted);margin-bottom:2rem;font-size:0.9rem" class="reveal">Teknologi dan tools yang saya kuasai</p>

        <div class="grid-3" style="gap:1.5rem">
            <?php
            $areaData = [
                ['icon' => 'fa-globe',       'title' => 'Web Development',    'desc' => 'Frontend & Backend development dengan teknologi modern.',   'delay' => ''],
                ['icon' => 'fa-robot',       'title' => 'Bot Development',    'desc' => 'Otomatisasi dan bot untuk WhatsApp, Telegram, Discord.',    'delay' => 'reveal-delay-1'],
                ['icon' => 'fa-mobile-alt',  'title' => 'Mobile Development', 'desc' => 'Aplikasi Android & iOS cross-platform dengan Flutter.',      'delay' => 'reveal-delay-2'],
            ];
            foreach ($areaData as $area):
            ?>
            <div class="card reveal <?= $area['delay'] ?>" style="padding:1.75rem">
                <i class="fas <?= $area['icon'] ?>" style="font-size:1.5rem;background:var(--gradient-text);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:0.875rem;display:block"></i>
                <h3 style="font-size:1rem;margin-bottom:0.5rem"><?= $area['title'] ?></h3>
                <p style="color:var(--text-muted);font-size:0.875rem;line-height:1.7"><?= $area['desc'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Divider -->
    <div class="divider"></div>

    <!-- Technologies -->
    <div style="margin-bottom:3rem">
        <div class="section-heading reveal">
            <h2 style="font-size:1.4rem;font-weight:600">Technologies & Tools</h2>
        </div>
        <p style="color:var(--text-muted);margin-bottom:2rem;font-size:0.9rem" class="reveal">Stack yang saya gunakan sehari-hari</p>

        <?php foreach ($cats as $cat): ?>
        <div style="margin-bottom:2rem" class="reveal">
            <h4 style="font-size:0.8rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--text-muted);margin-bottom:1rem;font-family:var(--font-mono)">
                <?= e($cat['name']) ?>
            </h4>
            <div style="display:flex;flex-wrap:wrap;gap:0.5rem">
                <?php
                $names = explode('||', $cat['skill_names'] ?? '');
                $icons = explode('||', $cat['skill_icons'] ?? '');
                foreach ($names as $i => $name):
                    if (empty(trim($name))) continue;
                    $icon = $icons[$i] ?? 'fas fa-code';
                ?>
                <span class="badge">
                    <i class="<?= e($icon) ?>"></i>
                    <?= e($name) ?>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Stats -->
    <div class="divider"></div>
    <div class="grid-2" style="gap:1.5rem;margin-bottom:3rem">
        <div class="card stat-card reveal" style="padding:2.5rem">
            <span class="stat-card__number" data-count="<?= (int)($profile['experience_years'] ?? 5) ?>" data-suffix="+">0</span>
            <span class="stat-card__label">Tahun Pengalaman</span>
        </div>
        <div class="card stat-card reveal reveal-delay-1" style="padding:2.5rem">
            <span class="stat-card__number" data-count="<?= (int)($profile['projects_completed'] ?? 50) ?>" data-suffix="+">0</span>
            <span class="stat-card__label">Proyek Selesai</span>
        </div>
    </div>

</div>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
