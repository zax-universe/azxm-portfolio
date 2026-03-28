<?php
/**
 * Admin Dashboard
 * Azaxm Portfolio CMS
 */
require_once dirname(__DIR__) . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

$adminPageTitle = 'Dashboard';

$db = getDB();

// Statistik
$totalVisits   = getTotalVisits();
$todayVisits   = getTodayVisits();
$totalMessages = (int)$db->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
$unreadMsg     = getUnreadMessages();
$totalSkills   = (int)$db->query("SELECT COUNT(*) FROM skills WHERE is_visible=1")->fetchColumn();

// Login terakhir
$lastLogin = $db->query(
    "SELECT login_time, ip_address, status FROM login_logs
     WHERE status = 'success' ORDER BY login_time DESC LIMIT 5"
)->fetchAll();

// Pesan terbaru
$recentMessages = $db->query(
    "SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5"
)->fetchAll();

// Kunjungan per halaman
$pageStats = $db->query(
    "SELECT page, COUNT(*) as visits FROM page_visits GROUP BY page ORDER BY visits DESC LIMIT 5"
)->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';
?>

<!-- Dashboard Stats -->
<div class="dashboard-stats">
    <div class="dash-stat">
        <div class="dash-stat__icon"><i class="fas fa-eye"></i></div>
        <div>
            <div class="dash-stat__value"><?= number_format($totalVisits) ?></div>
            <div class="dash-stat__label">Total Kunjungan</div>
        </div>
    </div>
    <div class="dash-stat">
        <div class="dash-stat__icon" style="background:rgba(16,185,129,0.12);color:#34d399"><i class="fas fa-calendar-day"></i></div>
        <div>
            <div class="dash-stat__value"><?= number_format($todayVisits) ?></div>
            <div class="dash-stat__label">Kunjungan Hari Ini</div>
        </div>
    </div>
    <div class="dash-stat">
        <div class="dash-stat__icon" style="background:rgba(245,158,11,0.12);color:#fbbf24"><i class="fas fa-envelope"></i></div>
        <div>
            <div class="dash-stat__value"><?= number_format($totalMessages) ?></div>
            <div class="dash-stat__label">
                Total Pesan
                <?php if ($unreadMsg > 0): ?>
                <span style="color:var(--accent);font-weight:700"> (<?= $unreadMsg ?> baru)</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="dash-stat">
        <div class="dash-stat__icon" style="background:rgba(59,130,246,0.12);color:#60a5fa"><i class="fas fa-code"></i></div>
        <div>
            <div class="dash-stat__value"><?= $totalSkills ?></div>
            <div class="dash-stat__label">Skills Aktif</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">

    <!-- Recent Messages -->
    <div class="admin-form-card">
        <h3><i class="fas fa-envelope"></i> Pesan Terbaru
            <a href="<?= BASE_URL ?>/admin/messages.php" style="margin-left:auto;font-size:0.78rem;color:var(--accent);font-weight:400">Lihat semua →</a>
        </h3>
        <?php if ($recentMessages): ?>
        <div style="display:flex;flex-direction:column;gap:0.75rem">
            <?php foreach ($recentMessages as $msg): ?>
            <div style="padding:0.875rem;background:var(--bg-2);border-radius:var(--radius-sm);border:1px solid var(--border)">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.5rem;margin-bottom:0.375rem">
                    <span style="font-size:0.875rem;font-weight:600"><?= e($msg['name']) ?></span>
                    <?php if (!$msg['is_read']): ?>
                    <span class="status-badge status-badge--unread">Baru</span>
                    <?php endif; ?>
                </div>
                <p style="font-size:0.78rem;color:var(--text-muted);margin-bottom:0.25rem"><?= e($msg['email']) ?></p>
                <p style="font-size:0.8rem;color:var(--text-light)"><?= e(truncate($msg['message'], 80)) ?></p>
                <p style="font-size:0.72rem;color:var(--text-muted);margin-top:0.375rem"><?= formatDate($msg['created_at']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="color:var(--text-muted);font-size:0.875rem;text-align:center;padding:1.5rem 0">Belum ada pesan masuk.</p>
        <?php endif; ?>
    </div>

    <!-- Right column -->
    <div style="display:flex;flex-direction:column;gap:1.5rem">

        <!-- Page Stats -->
        <div class="admin-form-card">
            <h3><i class="fas fa-chart-bar"></i> Halaman Terpopuler</h3>
            <?php if ($pageStats): ?>
            <div style="display:flex;flex-direction:column;gap:0.625rem">
                <?php foreach ($pageStats as $ps): ?>
                <div style="display:flex;align-items:center;justify-content:space-between;font-size:0.875rem">
                    <span style="color:var(--text-light);font-family:var(--font-mono)">/<?= e($ps['page']) ?></span>
                    <span style="color:var(--accent);font-weight:600"><?= number_format($ps['visits']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p style="color:var(--text-muted);font-size:0.875rem">Belum ada data kunjungan.</p>
            <?php endif; ?>
        </div>

        <!-- Login Log -->
        <div class="admin-form-card">
            <h3><i class="fas fa-history"></i> Login Terakhir</h3>
            <?php if ($lastLogin): ?>
            <div style="display:flex;flex-direction:column;gap:0.5rem">
                <?php foreach ($lastLogin as $log): ?>
                <div style="font-size:0.8rem;display:flex;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid var(--border)">
                    <span style="color:var(--text-muted);font-family:var(--font-mono)"><?= e($log['ip_address']) ?></span>
                    <span style="color:var(--text-light)"><?= formatDate($log['login_time']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p style="color:var(--text-muted);font-size:0.875rem">Tidak ada log login.</p>
            <?php endif; ?>
        </div>

        <!-- Quick Actions -->
        <div class="admin-form-card">
            <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
            <div style="display:flex;flex-direction:column;gap:0.625rem">
                <a href="<?= BASE_URL ?>/admin/profile.php" class="btn btn-outline btn-sm">
                    <i class="fas fa-user-edit"></i> Edit Profile
                </a>
                <a href="<?= BASE_URL ?>/admin/skills.php" class="btn btn-outline btn-sm">
                    <i class="fas fa-code"></i> Kelola Skills
                </a>
                <a href="<?= BASE_URL ?>/admin/messages.php" class="btn btn-outline btn-sm">
                    <i class="fas fa-envelope"></i> Baca Pesan <?php if ($unreadMsg > 0): ?>(<?= $unreadMsg ?>)<?php endif; ?>
                </a>
                <a href="<?= BASE_URL ?>/public/index.php" class="btn btn-outline btn-sm" target="_blank">
                    <i class="fas fa-external-link-alt"></i> View Website
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
