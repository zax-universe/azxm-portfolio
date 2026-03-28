<?php
/**
 * Session Management
 * Azaxm Portfolio CMS
 */

// Konfigurasi session sebelum session_start()
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);

    // Cookie secure hanya kalau HTTPS
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }

    session_name(SESSION_NAME);
    session_start();
}

function isAdminLoggedIn(): bool {
    if (empty($_SESSION['admin_id']) || empty($_SESSION['admin_user'])) {
        return false;
    }

    // Session timeout
    if (!empty($_SESSION['last_activity'])) {
        if ((time() - $_SESSION['last_activity']) > SESSION_LIFETIME) {
            destroyAdminSession();
            return false;
        }
    }

    // Validasi user agent
    if (!empty($_SESSION['user_agent'])) {
        if ($_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
            destroyAdminSession();
            return false;
        }
    }

    $_SESSION['last_activity'] = time();
    return true;
}

function createAdminSession(int $userId, string $username): void {
    session_regenerate_id(true);
    $_SESSION['admin_id']      = $userId;
    $_SESSION['admin_user']    = $username;
    $_SESSION['last_activity'] = time();
    $_SESSION['login_time']    = time();
    $_SESSION['user_agent']    = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $_SESSION['ip_address']    = getClientIp();
}

function destroyAdminSession(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

function setFlash(string $type, string $message): void {
    $_SESSION['flash'][$type] = $message;
}

function getFlash(string $type): ?string {
    if (!empty($_SESSION['flash'][$type])) {
        $msg = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $msg;
    }
    return null;
}

function displayFlash(): string {
    $html = '';
    foreach (['success', 'error', 'warning', 'info'] as $type) {
        $msg = getFlash($type);
        if ($msg) {
            $icon = match($type) {
                'success' => 'fa-check-circle',
                'error'   => 'fa-times-circle',
                'warning' => 'fa-exclamation-triangle',
                default   => 'fa-info-circle',
            };
            $html .= '<div class="flash flash--' . $type . '">'
                   . '<i class="fas ' . $icon . '"></i> '
                   . e($msg)
                   . '</div>';
        }
    }
    return $html;
}

function redirect(string $url): never {
    if (!headers_sent()) {
        header('Location: ' . $url);
    } else {
        echo '<script>window.location.href="' . addslashes($url) . '";</script>';
    }
    exit;
}
