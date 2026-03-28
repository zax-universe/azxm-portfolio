<?php
/**
 * Admin Auth Check
 * Sertakan file ini di setiap halaman admin
 */
if (!defined('BASE_URL')) {
    require_once dirname(__DIR__, 2) . '/config/config.php';
}

if (!isAdminLoggedIn()) {
    setFlash('error', 'Silakan login terlebih dahulu.');
    redirect(BASE_URL . '/admin/login.php');
}
