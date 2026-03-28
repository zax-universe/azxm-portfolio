<?php
/**
 * Konfigurasi Global Aplikasi
 * Azaxm Portfolio CMS
 */

// ── Path & URL ──────────────────────────────────────────────
define('BASE_PATH', dirname(__DIR__));

// Auto-detect BASE_URL
// Untuk Termux: ganti manual jadi 'http://localhost:9000'
// Untuk shared hosting: biarkan auto-detect
$_protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$_host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('BASE_URL', $_protocol . '://' . $_host);
unset($_protocol, $_host);

// ── Upload Settings ──────────────────────────────────────────
define('UPLOAD_PATH', BASE_PATH . '/assets/uploads/');
define('UPLOAD_URL',  BASE_URL  . '/assets/uploads/');
define('MAX_FILE_SIZE', 2 * 1024 * 1024);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_EXTENSIONS',  ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// ── Session Settings ─────────────────────────────────────────
define('SESSION_LIFETIME',    1800);
define('SESSION_NAME',        'AZAXM_SESS');

// ── Login Security ────────────────────────────────────────────
define('MAX_LOGIN_ATTEMPTS',  5);
define('LOCKOUT_DURATION',    900);

// ── Admin Path ────────────────────────────────────────────────
define('ADMIN_PATH', BASE_PATH . '/admin');

// ── Environment ───────────────────────────────────────────────
// 'development' = tampilkan error | 'production' = sembunyikan error
define('APP_ENV', 'production');

// ── Error Reporting ───────────────────────────────────────────
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// ── Timezone ──────────────────────────────────────────────────
date_default_timezone_set('Asia/Jakarta');

// ── Require Core Files ───────────────────────────────────────
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/config/security.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/session.php';
