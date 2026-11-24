<?php $pageCss = ['admin-css/base', 'admin-css/security']; require APPROOT . '/Views/shared/header.php';
$esc = static function ($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
};
$csrf = $esc($_SESSION['security_csrf'] ?? ''); ?>
<main class="main-content security-main">
    <header class="content-header security-header">
        <div>
            <h2 class="page-title">Security &amp; Audit</h2>
            <p class="page-subtitle">Real login activity, recent sessions and security reviews</p>
        </div>
        <div class="security-header-actions">
            <a class="btn security-btn-audit" href="<?= URLROOT ?>/admin/exportSecurityAudit">Export Audit CSV</a>
            <form method="post" action="<?= URLROOT ?>/admin/revokeSecuritySessions" onsubmit="return confirm('Revoke all other tracked sessions? Users will need to log in again when they next make a request.')">
                <input type="hidden" name="csrf" value="<?= $csrf ?>"><button class="btn security-btn-audit" type="submit">Force Other Users to Re-login</button>
            </form>
        </div>
    </header>
    <?php if (!empty($data['error'])): ?><p role="alert" class="security-error"><?= $esc($data['error']) ?></p><?php endif; ?>
    <?php if (!empty($data['flash'])): ?><p role="status" class="security-success"><?= $esc($data['flash']) ?></p><?php endif; ?>
    <section class="security-stat-grid">
        <?php foreach ([['Tracked Active Sessions', $data['sessions'], 'Activity in the past 30 minutes'], ['Failed Login Attempts', $data['failed'], 'Last 24 hours'], ['Password Reset Requests', $data['resets'], 'Last 7 days']] as $stat): ?>
            <div class="dashboard-card security-stat-card">
                <div><?= $esc($stat[0]) ?></div>
                <div class="security-stat-value"><?= (int)$stat[1] ?></div><small><?= $esc($stat[2]) ?></small>
            </div>
        <?php endforeach; ?>
    </section>
    <section class="dashboard-card security-card security-card-mb">
        <div class="security-card-header">
            <h3>Suspicious Login Failures</h3>
            <p>Accounts/IPs with at least 3 failed attempts in the last 24 hours. Resolve means reviewed, not unblocked.</p>
        </div>
        <div class="security-table-wrap">
            <table class="security-table security-table-min-720">
                <thead>
                    <tr>
                        <th>Latest attempt</th>
                        <th>Email / Portal</th>
                        <th>IP address</th>
                        <th>Failures</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['alerts'])): ?><tr>
                            <td colspan="6" class="empty-msg">No suspicious login groups detected in the past 24 hours.</td>
                        </tr><?php endif; ?>
                    <?php foreach ($data['alerts'] as $alert): ?><tr>
                            <td><?= $esc($alert['last_attempt']) ?></td>
                            <td><?= $esc($alert['email']) ?><br><small><?= $esc($alert['role']) ?></small></td>
                            <td><?= $esc($alert['ip_address']) ?></td>
                            <td><?= (int)$alert['attempts'] ?></td>
                            <td><?= $alert['resolved'] ? 'Reviewed' : 'Needs review' ?></td>
                            <td><?php if (!$alert['resolved']): ?><form method="post" action="<?= URLROOT ?>/admin/resolveSecurityAlert"><input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="email" value="<?= $esc($alert['email']) ?>"><input type="hidden" name="role" value="<?= $esc($alert['role']) ?>"><input type="hidden" name="ip" value="<?= $esc($alert['ip_address']) ?>"><button type="submit" class="btn security-btn-review">Mark Reviewed</button></form><?php else: ?>—<?php endif; ?></td>
                        </tr><?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <section class="dashboard-card security-card">
        <div class="security-card-header">
            <h3>System Audit Log</h3>
            <p>Latest 50 authentication attempts, newest first.</p>
        </div>
        <div class="security-table-wrap">
            <table class="security-table compact security-table-min-650">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Email</th>
                        <th>Portal</th>
                        <th>IP</th>
                        <th>Result</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['recent'])): ?><tr>
                            <td colspan="5" class="empty-msg">No login activity has been recorded yet.</td>
                        </tr><?php endif; ?>
                    <?php foreach ($data['recent'] as $entry): ?><tr>
                            <td><?= $esc($entry['attempted_at']) ?></td>
                            <td><?= $esc($entry['email']) ?></td>
                            <td><?= $esc($entry['role']) ?></td>
                            <td><?= $esc($entry['ip_address']) ?></td>
                            <td><?= $entry['successful'] ? 'Success' : 'Failed' ?></td>
                        </tr><?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main><?php require APPROOT . '/Views/shared/footer.php'; ?>
