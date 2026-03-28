<?php
/**
 * Halaman Skills
 * Azaxm Portfolio CMS
 */
require_once dirname(__DIR__) . '/config/config.php';

$activePage = 'skills';
$pageTitle  = 'Skills';
$pageDesc   = 'Daftar skill dan teknologi yang dikuasai oleh Azaxm';

logPageVisit('skills');

$db = getDB();

// Ambil skill categories
$categories = $db->query(
    "SELECT * FROM skill_categories ORDER BY display_order ASC"
)->fetchAll();

// Ambil skills per category
$skillsByCategory = [];
$stmt = $db->query(
    "SELECT s.*, sc.name as cat_name
     FROM skills s
     JOIN skill_categories sc ON sc.id = s.category_id
     WHERE s.is_visible = 1
     ORDER BY s.category_id ASC, s.display_order ASC"
);
foreach ($stmt->fetchAll() as $skill) {
    $skillsByCategory[$skill['category_id']][] = $skill;
}
?>
<?php require_once BASE_PATH . '/includes/header.php'; ?>
<?php require_once BASE_PATH . '/includes/navbar.php'; ?>

<div class="page-wrapper">
<div class="container">

    <!-- Page Header -->
    <div style="padding:3rem 0 2rem;opacity:0;animation:fadeSlideUp 0.5s ease both">
        <p class="mono" style="color:var(--primary);font-size:0.85rem;margin-bottom:0.5rem">// skills.php</p>
        <h1 class="section-title gradient-text">My Skills</h1>
        <p style="color:var(--text-muted)">Teknologi dan tools yang saya kuasai</p>
    </div>

    <?php foreach ($categories as $cat): ?>
    <?php $skills = $skillsByCategory[$cat['id']] ?? []; ?>
    <?php if (empty($skills)) continue; ?>

    <div style="margin-bottom:3.5rem">

        <!-- Category Header -->
        <div class="section-heading reveal">
            <h2 style="font-size:1.25rem;font-weight:600"><?= e($cat['name']) ?></h2>
        </div>

        <?php
        // Kategori pertama: tampilkan progress bar
        // Kategori lainnya: grid card
        if ($cat['display_order'] == 1):
        ?>
        <!-- Progress Bars (Programming Languages) -->
        <div class="card reveal" style="padding:2rem;margin-top:1.25rem">
            <?php foreach ($skills as $idx => $skill): ?>
            <div class="progress-wrap">
                <div class="progress-label">
                    <span class="skill-name">
                        <?php if (!empty($skill['icon'])): ?>
                        <i class="<?= e($skill['icon']) ?>" style="color:var(--accent);width:18px;text-align:center"></i>
                        <?php endif; ?>
                        <?= e($skill['name']) ?>
                    </span>
                    <span class="skill-pct"><?= (int)$skill['level'] ?>%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill"
                         data-level="<?= (int)$skill['level'] ?>"
                         data-delay="<?= $idx * 120 ?>">
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php else: ?>

        <!-- Grid Cards (Frameworks & Tools) -->
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:1rem;margin-top:1.25rem">
            <?php foreach ($skills as $idx => $skill): ?>
            <div class="card reveal" style="padding:1.5rem;text-align:center;--delay:<?= $idx * 80 ?>ms;transition-delay:calc(var(--delay))">
                <?php if (!empty($skill['icon'])): ?>
                <i class="<?= e($skill['icon']) ?>" style="font-size:2rem;background:var(--gradient-text);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:0.75rem;display:block"></i>
                <?php endif; ?>
                <div style="font-size:0.9rem;font-weight:600;margin-bottom:0.5rem"><?= e($skill['name']) ?></div>
                <div style="font-size:0.75rem;color:var(--text-muted);font-family:var(--font-mono)"><?= (int)$skill['level'] ?>%</div>
                <!-- Mini progress -->
                <div style="height:3px;background:var(--border-2);border-radius:999px;margin-top:0.75rem;overflow:hidden">
                    <div class="progress-fill" data-level="<?= (int)$skill['level'] ?>" data-delay="<?= $idx * 100 ?>"
                         style="height:100%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>

        <?php if (!($cat === end($categories))): ?>
        <div class="divider"></div>
        <?php endif; ?>

    </div>
    <?php endforeach; ?>

    <!-- Tech stack at a glance -->
    <div class="card reveal" style="padding:2rem;margin-top:1rem;margin-bottom:3rem;background:linear-gradient(135deg,rgba(99,102,241,0.06),rgba(139,92,246,0.06));border-color:rgba(99,102,241,0.2)">
        <h3 style="font-size:1rem;margin-bottom:1.25rem;color:var(--text-light)">
            <i class="fas fa-layer-group" style="color:var(--accent);margin-right:0.5rem"></i>
            Full Stack Overview
        </h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1.5rem;font-size:0.875rem">
            <div>
                <p style="color:var(--text-muted);font-family:var(--font-mono);font-size:0.78rem;margin-bottom:0.5rem">Frontend</p>
                <div style="display:flex;flex-wrap:wrap;gap:0.375rem">
                    <span class="badge">HTML5</span><span class="badge">CSS3</span>
                    <span class="badge">JavaScript</span><span class="badge">React</span>
                    <span class="badge">TypeScript</span>
                </div>
            </div>
            <div>
                <p style="color:var(--text-muted);font-family:var(--font-mono);font-size:0.78rem;margin-bottom:0.5rem">Backend</p>
                <div style="display:flex;flex-wrap:wrap;gap:0.375rem">
                    <span class="badge">PHP</span><span class="badge">Node.js</span>
                    <span class="badge">Express</span><span class="badge">Python</span>
                    <span class="badge">Laravel</span>
                </div>
            </div>
            <div>
                <p style="color:var(--text-muted);font-family:var(--font-mono);font-size:0.78rem;margin-bottom:0.5rem">Database</p>
                <div style="display:flex;flex-wrap:wrap;gap:0.375rem">
                    <span class="badge">MySQL</span><span class="badge">MongoDB</span>
                    <span class="badge">Redis</span><span class="badge">SQLite</span>
                </div>
            </div>
            <div>
                <p style="color:var(--text-muted);font-family:var(--font-mono);font-size:0.78rem;margin-bottom:0.5rem">DevOps</p>
                <div style="display:flex;flex-wrap:wrap;gap:0.375rem">
                    <span class="badge">Docker</span><span class="badge">Git</span>
                    <span class="badge">Linux</span><span class="badge">Nginx</span>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
