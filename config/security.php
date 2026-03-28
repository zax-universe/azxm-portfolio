<?php
/**
 * Security Functions
 * Azaxm Portfolio CMS
 */

// ── Security Headers ──────────────────────────────────────────
function sendSecurityHeaders(): void {
    if (headers_sent()) return;
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: SAMEORIGIN");
    header("X-XSS-Protection: 1; mode=block");
    header("Referrer-Policy: strict-origin-when-cross-origin");
    header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
    header("Content-Security-Policy: "
        . "default-src 'self'; "
        . "script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.tailwindcss.com https://fonts.googleapis.com; "
        . "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com https://fonts.gstatic.com; "
        . "font-src 'self' https://cdnjs.cloudflare.com https://fonts.gstatic.com; "
        . "img-src 'self' data: https:; "
        . "frame-src https://www.google.com https://maps.google.com; "
        . "connect-src 'self';"
    );
}

// ── XSS Prevention ────────────────────────────────────────────
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function eAttr(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// ── Input Sanitization ────────────────────────────────────────
function sanitizeString(string $input, int $maxLength = 255): string {
    $input = trim($input);
    $input = strip_tags($input);
    $input = substr($input, 0, $maxLength);
    return $input;
}

function sanitizeEmail(string $email): string {
    $email = trim($email);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    return strtolower($email);
}

function sanitizeInt(mixed $input): int {
    return (int) filter_var($input, FILTER_SANITIZE_NUMBER_INT);
}

function sanitizeUrl(string $url): string {
    $url = trim($url);
    $url = filter_var($url, FILTER_SANITIZE_URL);
    if (!preg_match('/^(https?:\/\/|mailto:)/', $url)) {
        return '#';
    }
    return $url;
}

// ── CSRF Protection ────────────────────────────────────────────
function generateCsrfToken(): string {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token']) || empty($_SESSION['csrf_token_time'])
        || (time() - $_SESSION['csrf_token_time']) > 3600) {
        $_SESSION['csrf_token']      = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken(string $token): bool {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField(): string {
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

// ── IP Address ────────────────────────────────────────────────
function getClientIp(): string {
    $headers = [
        'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR',
    ];
    foreach ($headers as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = trim(explode(',', $_SERVER[$key])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

// ── Login Security ────────────────────────────────────────────
function isAccountLocked(array $user): bool {
    if ($user['locked_until'] === null) return false;
    return strtotime($user['locked_until']) > time();
}

function resetLoginAttempts(int $userId): void {
    $db   = getDB();
    $stmt = $db->prepare("UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?");
    $stmt->execute([$userId]);
}

function incrementLoginAttempts(int $userId): void {
    $db   = getDB();
    $stmt = $db->prepare("SELECT failed_attempts FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    $attempts    = ($user['failed_attempts'] ?? 0) + 1;
    $lockedUntil = null;

    if ($attempts >= MAX_LOGIN_ATTEMPTS) {
        $lockedUntil = date('Y-m-d H:i:s', time() + LOCKOUT_DURATION);
        $attempts    = 0;
    }

    $stmt = $db->prepare("UPDATE users SET failed_attempts = ?, locked_until = ? WHERE id = ?");
    $stmt->execute([$attempts, $lockedUntil, $userId]);
}

function logLoginAttempt(?int $userId, string $status): void {
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            "INSERT INTO login_logs (user_id, ip_address, user_agent, status) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $userId,
            getClientIp(),
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            $status,
        ]);
    } catch (PDOException $e) {
        error_log('Login log failed: ' . $e->getMessage());
    }
}

// ── File Upload Security ──────────────────────────────────────
function uploadImage(array $file, string $subDir = 'avatars'): array {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload gagal. Kode error: ' . $file['error']];
    }
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'Ukuran file melebihi 2MB.'];
    }

    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, ALLOWED_IMAGE_TYPES, true)) {
        return ['success' => false, 'error' => 'Tipe file tidak diizinkan. Hanya JPG, PNG, GIF, WebP.'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTENSIONS, true)) {
        return ['success' => false, 'error' => 'Ekstensi file tidak valid.'];
    }

    $newFilename = bin2hex(random_bytes(16)) . '.' . $ext;
    $uploadDir   = UPLOAD_PATH . $subDir . '/';
    $uploadPath  = $uploadDir . $newFilename;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return ['success' => false, 'error' => 'Gagal menyimpan file.'];
    }

    return ['success' => true, 'filename' => $subDir . '/' . $newFilename];
}

// Kirim security headers
sendSecurityHeaders();
