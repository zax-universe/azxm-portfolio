<?php
/**
 * Admin Login
 * Azaxm Portfolio CMS
 */
require_once dirname(__DIR__) . '/config/config.php';

// Sudah login → redirect ke dashboard
if (isAdminLoggedIn()) {
    redirect(BASE_URL . '/admin/dashboard.php');
}

$error    = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Token keamanan tidak valid. Refresh dan coba lagi.';
    }

    // Honeypot
    if (empty($error) && !empty($_POST['hp_field'])) {
        // Bot detected — fake success
        sleep(2);
        redirect(BASE_URL . '/admin/dashboard.php');
    }

    if (empty($error)) {
        $username = sanitizeString($_POST['username'] ?? '', 50);
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'Username dan password harus diisi.';
        } else {
            $db   = getDB();
            $stmt = $db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if (!$user) {
                // Username tidak ditemukan
                logLoginAttempt(null, 'failed');
                $error = 'Username atau password salah.';
            } elseif (isAccountLocked($user)) {
                $remaining = ceil((strtotime($user['locked_until']) - time()) / 60);
                $error = "Akun dikunci. Coba lagi dalam {$remaining} menit.";
            } elseif (!password_verify($password, $user['password'])) {
                incrementLoginAttempts($user['id']);
                logLoginAttempt($user['id'], 'failed');
                $remaining = MAX_LOGIN_ATTEMPTS - ($user['failed_attempts'] + 1);
                if ($remaining > 0) {
                    $error = "Password salah. {$remaining} percobaan tersisa.";
                } else {
                    $error = 'Terlalu banyak percobaan. Akun dikunci 15 menit.';
                }
            } else {
                // Login berhasil
                resetLoginAttempts($user['id']);
                logLoginAttempt($user['id'], 'success');

                // Update last_login & last_ip
                $upd = $db->prepare(
                    "UPDATE users SET last_login = NOW(), last_ip = ? WHERE id = ?"
                );
                $upd->execute([getClientIp(), $user['id']]);

                createAdminSession($user['id'], $user['username']);
                redirect(BASE_URL . '/admin/dashboard.php');
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Azaxm CMS</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <style>
        body { display:flex; align-items:center; justify-content:center; min-height:100vh; }
        .login-box {
            width: 100%;
            max-width: 400px;
            padding: 1.5rem;
            animation: fadeSlideUp 0.5s ease both;
        }
        .login-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 2.5rem 2rem;
            box-shadow: var(--shadow-lg);
        }
        .login-brand {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-grid-bg {
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.015) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.015) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
            mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black, transparent);
        }
        .pw-wrap { position: relative; }
        .pw-toggle {
            position: absolute; right: 1rem; top: 50%;
            transform: translateY(-50%);
            background: none; border: none; color: var(--text-muted);
            cursor: pointer; font-size: 0.9rem; padding: 0;
        }
    </style>
</head>
<body class="bg-az-bg">
<div class="login-grid-bg" aria-hidden="true"></div>

<div class="login-box">
    <div class="login-card">
        <!-- Brand -->
        <div class="login-brand">
            <div class="brand-logo" style="font-size:1.4rem;margin-bottom:0.5rem">
                <span class="brand-logo__bracket">&lt;</span>
                <span class="brand-logo__name">Azaxm</span>
                <span class="brand-logo__bracket">/&gt;</span>
            </div>
            <p style="color:var(--text-muted);font-size:0.82rem;font-family:var(--font-mono)">// admin panel</p>
        </div>

        <?php if ($error): ?>
        <div class="flash flash--error" style="animation:none;margin-bottom:1.25rem">
            <i class="fas fa-shield-alt"></i> <?= e($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="" novalidate>
            <?= csrfField() ?>
            <!-- Honeypot -->
            <div style="position:absolute;left:-9999px;visibility:hidden" aria-hidden="true">
                <input type="text" name="hp_field" tabindex="-1" autocomplete="off">
            </div>

            <div class="form-group">
                <label class="form-label" for="username">
                    <i class="fas fa-user" style="color:var(--primary);margin-right:0.375rem"></i>Username
                </label>
                <input type="text"
                       id="username"
                       name="username"
                       class="form-control"
                       value="<?= e($username) ?>"
                       placeholder="admin"
                       autocomplete="username"
                       maxlength="50"
                       required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">
                    <i class="fas fa-lock" style="color:var(--primary);margin-right:0.375rem"></i>Password
                </label>
                <div class="pw-wrap">
                    <input type="password"
                           id="password"
                           name="password"
                           class="form-control"
                           placeholder="••••••••"
                           autocomplete="current-password"
                           required
                           style="padding-right:3rem">
                    <button type="button" class="pw-toggle" id="pwToggle" aria-label="Toggle password visibility">
                        <i class="fas fa-eye" id="pwIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:0.5rem">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>

        <div style="margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid var(--border);text-align:center">
            <a href="<?= BASE_URL ?>/public/index.php" style="font-size:0.82rem;color:var(--text-muted)">
                <i class="fas fa-arrow-left" style="margin-right:0.3rem"></i>Kembali ke Website
            </a>
        </div>
    </div>

    <p style="text-align:center;margin-top:1rem;font-size:0.75rem;color:var(--text-muted)">
        <i class="fas fa-shield-alt" style="margin-right:0.25rem"></i>
        Koneksi aman · Percobaan login dibatasi
    </p>
</div>

<script>
    // Toggle password visibility
    const pwToggle = document.getElementById('pwToggle');
    const pwInput  = document.getElementById('password');
    const pwIcon   = document.getElementById('pwIcon');
    if (pwToggle) {
        pwToggle.addEventListener('click', function () {
            const isText = pwInput.type === 'text';
            pwInput.type = isText ? 'password' : 'text';
            pwIcon.className = isText ? 'fas fa-eye' : 'fas fa-eye-slash';
        });
    }
</script>
</body>
</html>
