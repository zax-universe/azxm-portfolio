<?php
/**
 * Helper Functions
 * Azaxm Portfolio CMS
 */

function getSetting(string $key, string $default = ''): string {
    static $settings = null;
    if ($settings === null) {
        try {
            $db   = getDB();
            $stmt = $db->query("SELECT setting_key, setting_value FROM website_settings");
            $rows = $stmt->fetchAll();
            $settings = [];
            foreach ($rows as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (PDOException $e) {
            $settings = [];
        }
    }
    return $settings[$key] ?? $default;
}

function getProfile(): array {
    static $profile = null;
    if ($profile === null) {
        try {
            $db      = getDB();
            $stmt    = $db->query("SELECT * FROM profile LIMIT 1");
            $profile = $stmt->fetch() ?: [];
        } catch (PDOException $e) {
            $profile = [];
        }
    }
    return $profile;
}

function getSocialLinks(): array {
    try {
        $db   = getDB();
        $stmt = $db->query("SELECT * FROM social_links WHERE is_active = 1 ORDER BY display_order ASC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function logPageVisit(string $page): void {
    try {
        $db   = getDB();
        $stmt = $db->prepare("INSERT INTO page_visits (page, ip_address, user_agent) VALUES (?, ?, ?)");
        $stmt->execute([$page, getClientIp(), substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255)]);
    } catch (PDOException $e) {
        error_log('Page visit log failed: ' . $e->getMessage());
    }
}

function formatDate(string $date): string {
    $months = [
        1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
        5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
        9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
    ];
    $ts = strtotime($date);
    return date('d', $ts) . ' ' . $months[(int)date('n', $ts)] . ' ' . date('Y', $ts);
}

function truncate(string $text, int $length = 100): string {
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '...';
}

function avatarUrl(string $filename): string {
    if (empty($filename) || $filename === 'default-avatar.png') {
        return BASE_URL . '/assets/images/default-avatar.svg';
    }
    return UPLOAD_URL . $filename;
}

function getTodayVisits(): int {
    try {
        $db   = getDB();
        $stmt = $db->query("SELECT COUNT(*) FROM page_visits WHERE DATE(visit_time) = CURDATE()");
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

function getUnreadMessages(): int {
    try {
        $db   = getDB();
        $stmt = $db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0");
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

function getTotalVisits(): int {
    try {
        $db = getDB();
        return (int)$db->query("SELECT COUNT(*) FROM page_visits")->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}
