<?php
/**
 * Admin - Settings
 * Azaxm Portfolio CMS
 */
require_once dirname(__DIR__) . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

$adminPageTitle = 'Settings';
$db = getDB();

$action = $_POST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Token CSRF tidak valid.');
        redirect(BASE_URL . '/admin/settings.php');
    }

    // ── Ganti Password ────────────────────────────────────────
    if ($action === 'change_password') {
        $currentPw  = $_POST['current_password'] ?? '';
        $newPw      = $_POST['new_password'] ?? '';
        $confirmPw  = $_POST['confirm_password'] ?? '';

        $stmt = $db->prepare("SELECT password FROM users WHERE id=?");
        $stmt->execute([$_SESSION['admin_id']]);
        $user = $stmt->fetch();

        if (!password_verify($currentPw, $user['password'])) {
            setFlash('error', 'Password lama tidak sesuai.');
        } elseif (strlen($newPw) < 8) {
            setFlash('error', 'Password baru minimal 8 karakter.');
        } elseif (!preg_match('/[A-Z]/', $newPw) || !preg_match('/[0-9]/', $newPw)) {
            setFlash('error', 'Password harus mengandung huruf kapital dan angka.');
        } elseif ($newPw !== $confirmPw) {
            setFlash('error', 'Konfirmasi password tidak cocok.');
        } else {
            $hash = password_hash($newPw, PASSWORD_BCRYPT, ['cost' => 12]);
            $db->prepare("UPDATE users SET password=? WHERE id=?")->execute([$hash, $_SESSION['admin_id']]);
            setFlash('success', 'Password berhasil diubah. Silakan login ulang.');
            destroyAdminSession();
            redirect(BASE_URL . '/admin/login.php');
        }
        redirect(BASE_URL . '/admin/settings.php');
    }

    // ── Update Website Settings ───────────────────────────────
    if ($action === 'update_settings') {
        $settingsMap = [
            'site_title'       => 'text',
            'site_description' => 'textarea',
            'footer_text'      => 'text',
            'maps_embed_url'   => 'text',
        ];
        foreach ($settingsMap as $key => $type) {
            $val = sanitizeString($_POST[$key] ?? '', 1000);
            $db->prepare(
                "INSERT INTO website_settings (setting_key, setting_value, setting_type)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE setting_value=?"
            )->execute([$key, $val, $type, $val]);
        }
        setFlash('success', 'Pengaturan website berhasil disimpan.');
        redirect(BASE_URL . '/admin/settings.php');
    }
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;align-items:start">

    <!-- Website Settings -->
    <div class="admin-form-card">
        <h3><i class="fas fa-globe"></i> Pengaturan Website</h3>
        <form method="POST" action="">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="update_settings">

            <div class="form-group">
                <label class="form-label">Judul Website</label>
                <input type="text" name="site_title" class="form-control" maxlength="100"
                       value="<?= e(getSetting('site_title', 'Azaxm | Portfolio')) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi Website</label>
                <textarea name="site_description" class="form-control" rows="3" maxlength="500"><?= e(getSetting('site_description')) ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Teks Footer</label>
                <input type="text" name="footer_text" class="form-control" maxlength="200"
                       value="<?= e(getSetting('footer_text', '© 2024 Azaxm')) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Google Maps Embed URL</label>
                <input type="text" name="maps_embed_url" class="form-control" maxlength="500"
                       value="<?= e(getSetting('maps_embed_url')) ?>"
                       placeholder="https://www.google.com/maps/embed?pb=...">
                <p style="font-size:0.75rem;color:var(--text-muted);margin-top:0.375rem">
                    Dari Google Maps → Share → Embed a map → salin URL src="..."
                </p>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-save"></i> Simpan Pengaturan
            </button>
        </form>
    </div>

    <!-- Change Password -->
    <div>
        <div class="admin-form-card">
            <h3><i class="fas fa-key"></i> Ganti Password Admin</h3>
            <form method="POST" action="">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="change_password">

                <div class="form-group">
                    <label class="form-label">Password Lama</label>
                    <input type="password" name="current_password" class="form-control"
                           autocomplete="current-password" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="new_password" class="form-control"
                           autocomplete="new-password" required minlength="8">
                    <p style="font-size:0.75rem;color:var(--text-muted);margin-top:0.375rem">
                        Min. 8 karakter, wajib ada huruf kapital dan angka.
                    </p>
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="confirm_password" class="form-control"
                           autocomplete="new-password" required minlength="8">
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-lock"></i> Ganti Password
                </button>
            </form>
        </div>

        <!-- Security Info -->
        <div class="admin-form-card" style="margin-top:0">
            <h3><i class="fas fa-shield-alt"></i> Info Sesi & Keamanan</h3>
            <div style="font-size:0.82rem;display:flex;flex-direction:column;gap:0.625rem;color:var(--text-muted)">
                <div style="display:flex;justify-content:space-between">
                    <span>Username</span>
                    <span style="color:var(--text);font-family:var(--font-mono)"><?= e($_SESSION['admin_user'] ?? '-') ?></span>
                </div>
                <div style="display:flex;justify-content:space-between">
                    <span>Login pada</span>
                    <span style="color:var(--text)"><?= date('d M Y H:i', $_SESSION['login_time'] ?? 0) ?></span>
                </div>
                <div style="display:flex;justify-content:space-between">
                    <span>IP Address</span>
                    <span style="color:var(--text);font-family:var(--font-mono)"><?= e($_SESSION['ip_address'] ?? getClientIp()) ?></span>
                </div>
                <div style="display:flex;justify-content:space-between">
                    <span>Session timeout</span>
                    <span style="color:var(--text)">30 menit inaktif</span>
                </div>
            </div>
            <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border)">
                <a href="<?= BASE_URL ?>/admin/logout.php" class="btn btn-danger btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Logout Sekarang
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
