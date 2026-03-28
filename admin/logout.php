<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

destroyAdminSession();
redirect(BASE_URL . '/admin/login.php');
