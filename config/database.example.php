<?php
/**
 * Database Configuration - EXAMPLE FILE
 * Copy file ini jadi database.php dan isi sesuai konfigurasi Anda
 *
 * cp config/database.example.php config/database.php
 */

define('DB_HOST',    '127.0.0.1');      // Termux: 127.0.0.1 | XAMPP/Hosting: localhost
define('DB_NAME',    'azaxm_portfolio');
define('DB_USER',    'root');            // Ganti sesuai user MySQL Anda
define('DB_PASS',    '');               // Ganti sesuai password MySQL Anda
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            die('Koneksi database gagal. Cek konfigurasi di config/database.php');
        }
    }
    return $pdo;
}
