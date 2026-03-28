<?php
/**
 * Admin - Manage Skills
 * Azaxm Portfolio CMS
 */
require_once dirname(__DIR__) . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

$adminPageTitle = 'Manage Skills';
$db = getDB();

// ── Handle Actions ───────────────────────────────────────────
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Token CSRF tidak valid.');
        redirect(BASE_URL . '/admin/skills.php');
    }

    if ($action === 'add') {
        $catId  = sanitizeInt($_POST['category_id'] ?? 0);
        $name   = sanitizeString($_POST['name'] ?? '', 100);
        $icon   = sanitizeString($_POST['icon'] ?? '', 100);
        $level  = max(0, min(100, sanitizeInt($_POST['level'] ?? 0)));
        $order  = sanitizeInt($_POST['display_order'] ?? 0);

        if (empty($name) || $catId < 1) {
            setFlash('error', 'Nama skill dan kategori harus diisi.');
        } else {
            $stmt = $db->prepare(
                "INSERT INTO skills (category_id, name, icon, level, display_order) VALUES (?,?,?,?,?)"
            );
            $stmt->execute([$catId, $name, $icon, $level, $order]);
            setFlash('success', "Skill '{$name}' berhasil ditambahkan.");
        }
        redirect(BASE_URL . '/admin/skills.php');
    }

    if ($action === 'edit') {
        $id    = sanitizeInt($_POST['id'] ?? 0);
        $catId = sanitizeInt($_POST['category_id'] ?? 0);
        $name  = sanitizeString($_POST['name'] ?? '', 100);
        $icon  = sanitizeString($_POST['icon'] ?? '', 100);
        $level = max(0, min(100, sanitizeInt($_POST['level'] ?? 0)));
        $order = sanitizeInt($_POST['display_order'] ?? 0);
        $vis   = isset($_POST['is_visible']) ? 1 : 0;

        $stmt = $db->prepare(
            "UPDATE skills SET category_id=?, name=?, icon=?, level=?, display_order=?, is_visible=?
             WHERE id=?"
        );
        $stmt->execute([$catId, $name, $icon, $level, $order, $vis, $id]);
        setFlash('success', 'Skill berhasil diperbarui.');
        redirect(BASE_URL . '/admin/skills.php');
    }

    if ($action === 'delete') {
        $id = sanitizeInt($_POST['id'] ?? 0);
        $db->prepare("DELETE FROM skills WHERE id=?")->execute([$id]);
        setFlash('success', 'Skill berhasil dihapus.');
        redirect(BASE_URL . '/admin/skills.php');
    }
}

// ── Fetch Data ───────────────────────────────────────────────
$categories = $db->query("SELECT * FROM skill_categories ORDER BY display_order ASC")->fetchAll();
$skills     = $db->query(
    "SELECT s.*, sc.name as cat_name
     FROM skills s LEFT JOIN skill_categories sc ON sc.id = s.category_id
     ORDER BY s.category_id ASC, s.display_order ASC"
)->fetchAll();

// Edit mode
$editSkill = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM skills WHERE id=?");
    $stmt->execute([sanitizeInt($_GET['id'])]);
    $editSkill = $stmt->fetch();
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<div style="display:grid;grid-template-columns:320px 1fr;gap:1.5rem;align-items:start">

    <!-- Form Tambah / Edit -->
    <div class="admin-form-card">
        <h3>
            <i class="fas fa-<?= $editSkill ? 'edit' : 'plus-circle' ?>"></i>
            <?= $editSkill ? 'Edit Skill' : 'Tambah Skill' ?>
        </h3>

        <form method="POST" action="">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="<?= $editSkill ? 'edit' : 'add' ?>">
            <?php if ($editSkill): ?>
            <input type="hidden" name="id" value="<?= (int)$editSkill['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label class="form-label">Kategori *</label>
                <select name="category_id" class="form-control" required>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"
                        <?= ($editSkill && $editSkill['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                        <?= e($cat['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Skill *</label>
                <input type="text" name="name" class="form-control" required maxlength="100"
                       value="<?= e($editSkill['name'] ?? '') ?>" placeholder="e.g. JavaScript">
            </div>

            <div class="form-group">
                <label class="form-label">Icon (Font Awesome class)</label>
                <input type="text" name="icon" class="form-control" maxlength="100"
                       value="<?= e($editSkill['icon'] ?? '') ?>" placeholder="fab fa-js">
            </div>

            <div class="form-group">
                <label class="form-label">
                    Level: <span id="skillLevelPreview" style="color:var(--accent)"><?= $editSkill['level'] ?? 80 ?>%</span>
                </label>
                <input type="range" id="skillLevel" name="level" class="form-control"
                       min="0" max="100" step="5"
                       value="<?= (int)($editSkill['level'] ?? 80) ?>"
                       style="padding:0.5rem 0;cursor:pointer">
            </div>

            <div class="form-group">
                <label class="form-label">Urutan Tampil</label>
                <input type="number" name="display_order" class="form-control" min="0"
                       value="<?= (int)($editSkill['display_order'] ?? 0) ?>">
            </div>

            <?php if ($editSkill): ?>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:0.625rem;cursor:pointer">
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_visible" value="1"
                               <?= ($editSkill['is_visible'] ?? 1) ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="form-label" style="margin:0">Tampilkan di website</span>
                </label>
            </div>
            <?php endif; ?>

            <div style="display:flex;gap:0.625rem;flex-wrap:wrap">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-save"></i> <?= $editSkill ? 'Simpan' : 'Tambahkan' ?>
                </button>
                <?php if ($editSkill): ?>
                <a href="<?= BASE_URL ?>/admin/skills.php" class="btn btn-outline btn-sm">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Skills Table -->
    <div class="admin-form-card">
        <h3><i class="fas fa-list"></i> Daftar Skills</h3>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Skill</th>
                        <th>Kategori</th>
                        <th>Level</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($skills as $s): ?>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.625rem">
                                <?php if ($s['icon']): ?>
                                <i class="<?= e($s['icon']) ?>" style="color:var(--accent);width:18px;text-align:center"></i>
                                <?php endif; ?>
                                <span style="font-weight:500"><?= e($s['name']) ?></span>
                            </div>
                        </td>
                        <td style="color:var(--text-muted);font-size:0.82rem"><?= e($s['cat_name']) ?></td>
                        <td>
                            <div class="skill-level-bar">
                                <div class="skill-level-bar__track">
                                    <div class="skill-level-bar__fill" style="width:<?= (int)$s['level'] ?>%"></div>
                                </div>
                                <span class="skill-level-bar__pct"><?= (int)$s['level'] ?>%</span>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge <?= $s['is_visible'] ? 'status-badge--active' : 'status-badge--inactive' ?>">
                                <?= $s['is_visible'] ? 'Aktif' : 'Hidden' ?>
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:0.375rem">
                                <a href="?action=edit&id=<?= $s['id'] ?>" class="btn btn-outline btn-sm" style="padding:0.3rem 0.75rem">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" style="display:inline">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" style="padding:0.3rem 0.75rem"
                                            data-confirm="Hapus skill '<?= e($s['name']) ?>'?">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($skills)): ?>
                    <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:2rem">Belum ada skill.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
