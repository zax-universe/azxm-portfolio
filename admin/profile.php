<?php
/**
 * Admin - Edit Profile
 * Azaxm Portfolio CMS
 */
require_once dirname(__DIR__) . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

$adminPageTitle = 'Edit Profile';
$db      = getDB();
$profile = getProfile();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Token CSRF tidak valid.');
        redirect(BASE_URL . '/admin/profile.php');
    }

    $fullName    = sanitizeString($_POST['full_name']    ?? '', 100);
    $title       = sanitizeString($_POST['title']        ?? '', 200);
    $bio         = sanitizeString($_POST['bio']          ?? '', 5000);
    $email       = sanitizeEmail($_POST['email']         ?? '');
    $phone       = sanitizeString($_POST['phone']        ?? '', 20);
    $location    = sanitizeString($_POST['location']     ?? '', 100);
    $expYears    = sanitizeInt($_POST['experience_years'] ?? 0);
    $projCount   = sanitizeInt($_POST['projects_completed'] ?? 0);
    $githubUrl   = sanitizeUrl($_POST['github_url']      ?? '');
    $avatarFile  = $profile['avatar'] ?? 'default-avatar.png';

    // Handle avatar upload
    if (!empty($_FILES['avatar']['name'])) {
        $upload = uploadImage($_FILES['avatar'], 'avatars');
        if ($upload['success']) {
            $avatarFile = $upload['filename'];
        } else {
            setFlash('error', $upload['error']);
            redirect(BASE_URL . '/admin/profile.php');
        }
    }

    $stmt = $db->prepare(
        "UPDATE profile SET
            full_name = ?, title = ?, bio = ?, avatar = ?,
            email = ?, phone = ?, location = ?,
            experience_years = ?, projects_completed = ?, github_url = ?
         WHERE id = ?"
    );
    $stmt->execute([
        $fullName, $title, $bio, $avatarFile,
        $email, $phone, $location,
        $expYears, $projCount, $githubUrl,
        $profile['id']
    ]);

    setFlash('success', 'Profile berhasil diperbarui.');
    redirect(BASE_URL . '/admin/profile.php');
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<form method="POST" enctype="multipart/form-data" action="">
<?= csrfField() ?>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:1.5rem;align-items:start">

    <!-- Avatar -->
    <div class="admin-form-card">
        <h3><i class="fas fa-camera"></i> Foto Profil</h3>
        <div style="text-align:center;margin-bottom:1.25rem">
            <div class="avatar-ring" style="margin:0 auto">
                <img id="avatarPreview"
                     src="<?= e(avatarUrl($profile['avatar'] ?? '')) ?>"
                     alt="Avatar Preview"
                     onerror="this.src='<?= BASE_URL ?>/assets/images/default-avatar.svg'">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label" for="avatar">Ganti Avatar</label>
            <input type="file" id="avatar" name="avatar" class="form-control"
                   accept="image/jpeg,image/png,image/gif,image/webp">
            <p style="font-size:0.75rem;color:var(--text-muted);margin-top:0.5rem">
                JPG, PNG, GIF, WebP · Maks 2MB
            </p>
        </div>
    </div>

    <!-- Info -->
    <div>
        <div class="admin-form-card">
            <h3><i class="fas fa-user"></i> Informasi Dasar</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div class="form-group">
                    <label class="form-label" for="full_name">Nama Lengkap *</label>
                    <input type="text" id="full_name" name="full_name" class="form-control"
                           value="<?= e($profile['full_name'] ?? '') ?>" maxlength="100" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="title">Title / Jabatan</label>
                    <input type="text" id="title" name="title" class="form-control"
                           value="<?= e($profile['title'] ?? '') ?>" maxlength="200">
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control"
                           value="<?= e($profile['email'] ?? '') ?>" maxlength="100">
                </div>
                <div class="form-group">
                    <label class="form-label" for="phone">Telepon</label>
                    <input type="text" id="phone" name="phone" class="form-control"
                           value="<?= e($profile['phone'] ?? '') ?>" maxlength="20">
                </div>
                <div class="form-group">
                    <label class="form-label" for="location">Lokasi</label>
                    <input type="text" id="location" name="location" class="form-control"
                           value="<?= e($profile['location'] ?? '') ?>" maxlength="100">
                </div>
                <div class="form-group">
                    <label class="form-label" for="github_url">GitHub URL</label>
                    <input type="url" id="github_url" name="github_url" class="form-control"
                           value="<?= e($profile['github_url'] ?? '') ?>" maxlength="255">
                </div>
                <div class="form-group">
                    <label class="form-label" for="experience_years">Tahun Pengalaman</label>
                    <input type="number" id="experience_years" name="experience_years" class="form-control"
                           value="<?= (int)($profile['experience_years'] ?? 0) ?>" min="0" max="50">
                </div>
                <div class="form-group">
                    <label class="form-label" for="projects_completed">Proyek Selesai</label>
                    <input type="number" id="projects_completed" name="projects_completed" class="form-control"
                           value="<?= (int)($profile['projects_completed'] ?? 0) ?>" min="0">
                </div>
            </div>
        </div>

        <div class="admin-form-card">
            <h3><i class="fas fa-align-left"></i> Bio</h3>
            <div class="form-group">
                <textarea name="bio" class="form-control" rows="6" maxlength="5000"
                          placeholder="Ceritakan tentang diri Anda..."><?= e($profile['bio'] ?? '') ?></textarea>
                <p style="font-size:0.75rem;color:var(--text-muted);margin-top:0.375rem">Maks 5000 karakter</p>
            </div>
        </div>
    </div>
</div>

<div style="display:flex;gap:1rem;justify-content:flex-end;margin-top:0.5rem">
    <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-outline">Batal</a>
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Simpan Perubahan
    </button>
</div>
</form>

<script>
// Preview avatar sebelum upload
document.getElementById('avatar').addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('avatarPreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
