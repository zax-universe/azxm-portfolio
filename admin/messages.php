<?php
/**
 * Admin - Messages Inbox
 * Azaxm Portfolio CMS
 */
require_once dirname(__DIR__) . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

$adminPageTitle = 'Messages';
$db = getDB();

$action = $_POST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCsrfToken($_POST['csrf_token'] ?? '')) {
    $id = sanitizeInt($_POST['id'] ?? 0);
    if ($action === 'read') {
        $db->prepare("UPDATE contact_messages SET is_read=1 WHERE id=?")->execute([$id]);
    } elseif ($action === 'delete') {
        $db->prepare("DELETE FROM contact_messages WHERE id=?")->execute([$id]);
        setFlash('success', 'Pesan dihapus.');
    }
    redirect(BASE_URL . '/admin/messages.php');
}

// View single message & mark as read
$viewMsg = null;
if (isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM contact_messages WHERE id=?");
    $stmt->execute([sanitizeInt($_GET['id'])]);
    $viewMsg = $stmt->fetch();
    if ($viewMsg && !$viewMsg['is_read']) {
        $db->prepare("UPDATE contact_messages SET is_read=1 WHERE id=?")->execute([$viewMsg['id']]);
    }
}

$messages = $db->query(
    "SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 50"
)->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';
?>

<div style="display:grid;grid-template-columns:<?= $viewMsg ? '1fr 1fr' : '1fr' ?>;gap:1.5rem;align-items:start">

    <!-- Message List -->
    <div class="admin-form-card">
        <h3><i class="fas fa-inbox"></i> Inbox (<?= count($messages) ?>)</h3>
        <div style="display:flex;flex-direction:column;gap:0.5rem">
            <?php foreach ($messages as $msg): ?>
            <a href="?id=<?= $msg['id'] ?>"
               style="display:block;padding:0.875rem;border-radius:var(--radius-sm);border:1px solid <?= $viewMsg && $viewMsg['id'] == $msg['id'] ? 'var(--primary)' : 'var(--border)' ?>;background:<?= !$msg['is_read'] ? 'rgba(99,102,241,0.05)' : 'var(--bg-2)' ?>;transition:all 0.25s;text-decoration:none">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.5rem;margin-bottom:0.25rem">
                    <span style="font-weight:<?= $msg['is_read'] ? '500' : '700' ?>;font-size:0.9rem;color:var(--text)">
                        <?= e($msg['name']) ?>
                    </span>
                    <?php if (!$msg['is_read']): ?>
                    <span class="status-badge status-badge--unread" style="font-size:0.65rem">NEW</span>
                    <?php endif; ?>
                </div>
                <p style="font-size:0.78rem;color:var(--text-muted);margin-bottom:0.25rem"><?= e($msg['email']) ?></p>
                <p style="font-size:0.8rem;color:var(--text-light)"><?= e(truncate($msg['message'], 70)) ?></p>
                <p style="font-size:0.7rem;color:var(--text-muted);margin-top:0.375rem"><?= formatDate($msg['created_at']) ?></p>
            </a>
            <?php endforeach; ?>
            <?php if (empty($messages)): ?>
            <div style="text-align:center;padding:3rem;color:var(--text-muted)">
                <i class="fas fa-inbox" style="font-size:2rem;margin-bottom:0.75rem;display:block"></i>
                Belum ada pesan masuk.
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Message Detail -->
    <?php if ($viewMsg): ?>
    <div class="admin-form-card">
        <h3><i class="fas fa-envelope-open"></i> Detail Pesan</h3>

        <div style="background:var(--bg-2);border-radius:var(--radius-sm);padding:1.25rem;margin-bottom:1.25rem">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:1rem;font-size:0.875rem">
                <div>
                    <span style="color:var(--text-muted);font-size:0.75rem;display:block;margin-bottom:0.2rem">Dari</span>
                    <span style="font-weight:600"><?= e($viewMsg['name']) ?></span>
                </div>
                <div>
                    <span style="color:var(--text-muted);font-size:0.75rem;display:block;margin-bottom:0.2rem">Email</span>
                    <a href="mailto:<?= e($viewMsg['email']) ?>" style="color:var(--primary)"><?= e($viewMsg['email']) ?></a>
                </div>
                <div>
                    <span style="color:var(--text-muted);font-size:0.75rem;display:block;margin-bottom:0.2rem">IP Address</span>
                    <span style="font-family:var(--font-mono);font-size:0.8rem"><?= e($viewMsg['ip_address'] ?? '-') ?></span>
                </div>
                <div>
                    <span style="color:var(--text-muted);font-size:0.75rem;display:block;margin-bottom:0.2rem">Tanggal</span>
                    <span><?= formatDate($viewMsg['created_at']) ?></span>
                </div>
            </div>

            <div style="border-top:1px solid var(--border);padding-top:1rem">
                <span style="color:var(--text-muted);font-size:0.75rem;display:block;margin-bottom:0.5rem">Pesan</span>
                <p style="line-height:1.8;color:var(--text);white-space:pre-wrap"><?= e($viewMsg['message']) ?></p>
            </div>
        </div>

        <div style="display:flex;gap:0.75rem">
            <a href="mailto:<?= e($viewMsg['email']) ?>?subject=Re: Pesan dari Website Azaxm" class="btn btn-primary btn-sm">
                <i class="fas fa-reply"></i> Balas via Email
            </a>
            <form method="POST" style="display:inline">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $viewMsg['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm"
                        data-confirm="Hapus pesan ini secara permanen?">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </form>
            <a href="<?= BASE_URL ?>/admin/messages.php" class="btn btn-outline btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
