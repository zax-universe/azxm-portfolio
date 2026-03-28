<?php
/**
 * Admin - Manage Social Links
 * Azaxm Portfolio CMS
 */
require_once dirname(__DIR__) . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

$adminPageTitle = 'Social Links';
$db = getDB();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Token CSRF tidak valid.');
        redirect(BASE_URL . '/admin/social.php');
    }

    if ($action === 'add') {
        $platform = sanitizeString($_POST['platform'] ?? '', 50);
        $username = sanitizeString($_POST['username'] ?? '', 100);
        $url      = sanitizeUrl($_POST['url'] ?? '');
        $icon     = sanitizeString($_POST['icon'] ?? '', 100);
        $order    = sanitizeInt($_POST['display_order'] ?? 0);

        if (empty($platform) || $url === '#') {
            setFlash('error', 'Platform dan URL harus diisi dengan benar.');
        } else {
            $db->prepare(
                "INSERT INTO social_links (platform, username, url, icon, display_order) VALUES (?,?,?,?,?)"
            )->execute([$platform, $username, $url, $icon, $order]);
            setFlash('success', 'Social link berhasil ditambahkan.');
        }
        redirect(BASE_URL . '/admin/social.php');
    }

    if ($action === 'toggle') {
        $id      = sanitizeInt($_POST['id'] ?? 0);
        $current = sanitizeInt($_POST['current'] ?? 0);
        $db->prepare("UPDATE social_links SET is_active=? WHERE id=?")->execute([!$current, $id]);
        setFlash('success', 'Status berhasil diubah.');
        redirect(BASE_URL . '/admin/social.php');
    }

    if ($action === 'delete') {
        $id = sanitizeInt($_POST['id'] ?? 0);
        $db->prepare("DELETE FROM social_links WHERE id=?")->execute([$id]);
        setFlash('success', 'Social link dihapus.');
        redirect(BASE_URL . '/admin/social.php');
    }
}

$links = $db->query("SELECT * FROM social_links ORDER BY display_order ASC")->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';
?>

<div style="display:grid;grid-template-columns:300px 1fr;gap:1.5rem;align-items:start">

    <!-- Tambah Form -->
    <div class="admin-form-card">
        <h3><i class="fas fa-plus-circle"></i> Tambah Social Link</h3>
        <form method="POST" action="">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="add">

            <div class="form-group">
                <label class="form-label">Platform *</label>
                <input type="text" name="platform" class="form-control" required maxlength="50"
                       placeholder="e.g. Telegram">
            </div>
            <div class="form-group">
                <label class="form-label">Username / Handle</label>
                <input type="text" name="username" class="form-control" maxlength="100"
                       placeholder="@username">
            </div>
            <div class="form-group">
                <label class="form-label">URL *</label>
                <input type="text" name="url" class="form-control" required maxlength="255"
                       placeholder="https://t.me/username atau mailto:...">
            </div>
            <div class="form-group">
                <label class="form-label">Icon (Font Awesome)</label>
                <input type="text" name="icon" class="form-control" maxlength="100"
                       placeholder="fab fa-telegram">
            </div>
            <div class="form-group">
                <label class="form-label">Urutan</label>
                <input type="number" name="display_order" class="form-control" value="0" min="0">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambahkan
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="admin-form-card">
        <h3><i class="fas fa-share-alt"></i> Daftar Social Links</h3>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Platform</th>
                        <th>Username</th>
                        <th>URL</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($links as $link): ?>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.625rem">
                                <i class="<?= e($link['icon']) ?>" style="color:var(--accent);width:18px;text-align:center"></i>
                                <span style="font-weight:500"><?= e($link['platform']) ?></span>
                            </div>
                        </td>
                        <td style="color:var(--text-muted);font-size:0.82rem"><?= e($link['username'] ?? '-') ?></td>
                        <td>
                            <a href="<?= e(sanitizeUrl($link['url'])) ?>" target="_blank"
                               style="font-size:0.78rem;color:var(--primary);font-family:var(--font-mono);word-break:break-all">
                                <?= e(truncate($link['url'], 40)) ?>
                            </a>
                        </td>
                        <td>
                            <form method="POST" style="display:inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="id" value="<?= $link['id'] ?>">
                                <input type="hidden" name="current" value="<?= $link['is_active'] ?>">
                                <button type="submit"
                                        class="status-badge <?= $link['is_active'] ? 'status-badge--active' : 'status-badge--inactive' ?>"
                                        style="cursor:pointer;border:none;background:inherit">
                                    <?= $link['is_active'] ? '● Aktif' : '○ Nonaktif' ?>
                                </button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" style="display:inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $link['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm" style="padding:0.3rem 0.75rem"
                                        data-confirm="Hapus '<?= e($link['platform']) ?>'?">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($links)): ?>
                    <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:2rem">Belum ada social link.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
