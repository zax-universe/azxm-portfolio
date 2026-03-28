<?php
require_once dirname(__DIR__) . '/config/config.php';
if (isAdminLoggedIn()) {
    redirect(BASE_URL . '/admin/dashboard.php');
} else {
    redirect(BASE_URL . '/admin/login.php');
}
