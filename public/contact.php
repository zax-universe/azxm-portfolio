<?php
/**
 * Halaman Contact
 * Azaxm Portfolio CMS
 */
require_once dirname(__DIR__) . '/config/config.php';

$activePage = 'contact';
$pageTitle  = 'Contact';
$pageDesc   = 'Hubungi Azaxm untuk kerja sama atau pertanyaan';

logPageVisit('contact');

$profile     = getProfile();
$socialLinks = getSocialLinks();
$mapsUrl     = getSetting('maps_embed_url', '');

$errors   = [];
$success  = false;
$formData = ['name' => '', 'email' => '', 'message' => ''];

// ── Handle Form Submit ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validasi CSRF
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token keamanan tidak valid. Silakan refresh halaman dan coba lagi.';
    }

    // Honeypot check (field tersembunyi, jika diisi berarti bot)
    if (!empty($_POST['website'])) {
        // Diam-diam abaikan (jangan beri tahu bot)
        $success = true;
    }

    if (!$success && empty($errors)) {
        $name    = sanitizeString($_POST['name'] ?? '', 100);
        $email   = sanitizeEmail($_POST['email'] ?? '');
        $message = sanitizeString($_POST['message'] ?? '', 5000);

        $formData = compact('name', 'email', 'message');

        // Validasi
        if (empty($name) || strlen($name) < 2) {
            $errors[] = 'Nama harus diisi minimal 2 karakter.';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Alamat email tidak valid.';
        }
        if (empty($message) || strlen($message) < 10) {
            $errors[] = 'Pesan harus diisi minimal 10 karakter.';
        }

        // Rate limiting sederhana: max 3 pesan per IP per jam
        if (empty($errors)) {
            $db   = getDB();
            $ip   = getClientIp();
            $stmt = $db->prepare(
                "SELECT COUNT(*) FROM contact_messages
                 WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)"
            );
            $stmt->execute([$ip]);
            if ($stmt->fetchColumn() >= 3) {
                $errors[] = 'Terlalu banyak pesan. Silakan coba lagi dalam 1 jam.';
            }
        }

        // Simpan ke database
        if (empty($errors)) {
            $db   = getDB();
            $stmt = $db->prepare(
                "INSERT INTO contact_messages (name, email, message, ip_address)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$name, $email, $message, getClientIp()]);
            $success  = true;
            $formData = ['name' => '', 'email' => '', 'message' => ''];
        }
    }
}
?>
<?php require_once BASE_PATH . '/includes/header.php'; ?>
<?php require_once BASE_PATH . '/includes/navbar.php'; ?>

<div class="page-wrapper">
<div class="container">

    <!-- Page Header -->
    <div style="padding:3rem 0 2rem;opacity:0;animation:fadeSlideUp 0.5s ease both">
        <p class="mono" style="color:var(--primary);font-size:0.85rem;margin-bottom:0.5rem">// contact.php</p>
        <h1 class="section-title gradient-text">Get In Touch</h1>
        <p style="color:var(--text-muted)">Mari terhubung dan diskusikan proyek Anda</p>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;align-items:start">

        <!-- ── Contact Form ──────────────────────────────── -->
        <div class="reveal">
            <div class="card" style="padding:2rem">
                <h3 style="font-size:1rem;font-weight:600;margin-bottom:1.5rem;display:flex;align-items:center;gap:0.5rem">
                    <i class="fas fa-paper-plane" style="color:var(--accent)"></i>
                    Kirim Pesan
                </h3>

                <?php if ($success): ?>
                <div class="flash flash--success" style="animation:none;margin-bottom:1rem">
                    <i class="fas fa-check-circle"></i>
                    Pesan Anda berhasil terkirim! Saya akan membalas secepatnya.
                </div>
                <?php endif; ?>

                <?php foreach ($errors as $err): ?>
                <div class="flash flash--error" style="animation:none;margin-bottom:0.5rem">
                    <i class="fas fa-times-circle"></i> <?= e($err) ?>
                </div>
                <?php endforeach; ?>

                <form method="POST" action="" id="contactForm" novalidate>
                    <?= csrfField() ?>
                    <!-- Honeypot field (tersembunyi dari user nyata) -->
                    <div style="position:absolute;left:-9999px;top:-9999px;visibility:hidden" aria-hidden="true">
                        <input type="text" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="name">Nama Lengkap *</label>
                        <input type="text"
                               id="name"
                               name="name"
                               class="form-control"
                               placeholder="John Doe"
                               value="<?= e($formData['name']) ?>"
                               maxlength="100"
                               required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Alamat Email *</label>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-control"
                               placeholder="john@example.com"
                               value="<?= e($formData['email']) ?>"
                               maxlength="100"
                               required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="message">Pesan *</label>
                        <textarea id="message"
                                  name="message"
                                  class="form-control"
                                  placeholder="Hei Azaxm, saya ingin mendiskusikan proyek..."
                                  rows="5"
                                  maxlength="5000"
                                  required><?= e($formData['message']) ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
                        <i class="fas fa-paper-plane"></i> Kirim Pesan
                    </button>
                </form>
            </div>
        </div>

        <!-- ── Social Links & Info ───────────────────────── -->
        <div>
            <!-- Contact Info -->
            <div class="card reveal" style="padding:2rem;margin-bottom:1.5rem">
                <h3 style="font-size:1rem;font-weight:600;margin-bottom:1.25rem;display:flex;align-items:center;gap:0.5rem">
                    <i class="fas fa-address-card" style="color:var(--accent)"></i>
                    Informasi Kontak
                </h3>
                <?php if (!empty($profile['email'])): ?>
                <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.875rem;font-size:0.9rem">
                    <i class="fas fa-envelope" style="color:var(--primary);width:18px;text-align:center"></i>
                    <a href="mailto:<?= e($profile['email']) ?>" style="color:var(--text-light)"><?= e($profile['email']) ?></a>
                </div>
                <?php endif; ?>
                <?php if (!empty($profile['location'])): ?>
                <div style="display:flex;align-items:center;gap:0.75rem;font-size:0.9rem">
                    <i class="fas fa-map-marker-alt" style="color:var(--primary);width:18px;text-align:center"></i>
                    <span style="color:var(--text-light)"><?= e($profile['location']) ?></span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Social Links -->
            <?php if ($socialLinks): ?>
            <div class="reveal reveal-delay-1">
                <h3 style="font-size:0.8rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--text-muted);margin-bottom:1rem;font-family:var(--font-mono)">
                    Social Media
                </h3>
                <div style="display:flex;flex-direction:column;gap:0.625rem">
                    <?php foreach ($socialLinks as $sl): ?>
                    <a href="<?= e(sanitizeUrl($sl['url'])) ?>"
                       class="social-link"
                       target="<?= str_starts_with($sl['url'], 'mailto:') ? '_self' : '_blank' ?>"
                       rel="noopener noreferrer">
                        <div class="social-link__icon">
                            <i class="<?= e($sl['icon']) ?>"></i>
                        </div>
                        <div class="social-link__text">
                            <div class="social-link__platform"><?= e($sl['platform']) ?></div>
                            <div class="social-link__username"><?= e($sl['username'] ?? '') ?></div>
                        </div>
                        <i class="fas fa-arrow-right social-link__arrow"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── Google Maps ────────────────────────────────────── -->
    <?php if (!empty($mapsUrl)): ?>
    <div class="card reveal" style="padding:0;overflow:hidden;margin-top:2.5rem;height:300px">
        <iframe src="<?= e($mapsUrl) ?>"
                width="100%"
                height="300"
                style="border:0;display:block"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                title="Lokasi Azaxm">
        </iframe>
    </div>
    <?php endif; ?>

</div>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
