<?php require APPROOT . '/Views/shared/header.php';
$esc=static function($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');};
$csrf=$esc($_SESSION['security_csrf'] ?? ''); ?>
<main class="main-content" style="padding-bottom:40px">
<header class="content-header" style="display:flex;flex-wrap:wrap;gap:16px;justify-content:space-between;align-items:center">
<div><h2 class="page-title">Security &amp; Audit</h2><p class="page-subtitle">Real login activity, recent sessions and security reviews</p></div>
<div style="display:flex;flex-wrap:wrap;gap:12px">
<a class="btn" href="<?= URLROOT ?>/admin/exportSecurityAudit" style="padding:10px;border:1px solid #334155;border-radius:8px">Export Audit CSV</a>
<form method="post" action="<?= URLROOT ?>/admin/revokeSecuritySessions" onsubmit="return confirm('Revoke all other tracked sessions? Users will need to log in again when they next make a request.')">
<input type="hidden" name="csrf" value="<?= $csrf ?>"><button class="btn" type="submit" style="padding:10px;border:1px solid #334155;border-radius:8px">Force Other Users to Re-login</button></form>
</div></header>
<?php if(!empty($data['error'])): ?><p role="alert" style="color:#b91c1c"><?= $esc($data['error']) ?></p><?php endif; ?>
<?php if(!empty($data['flash'])): ?><p role="status" style="color:#166534"><?= $esc($data['flash']) ?></p><?php endif; ?>
<section style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:16px;margin:22px 0">
<?php foreach([['Tracked Active Sessions',$data['sessions'],'Activity in the past 30 minutes'],['Failed Login Attempts',$data['failed'],'Last 24 hours'],['Password Reset Requests',$data['resets'],'Last 7 days']] as $stat): ?>
<div class="dashboard-card" style="padding:24px;background:#fff;border:1px solid #cbd5e1;border-radius:12px"><div><?= $esc($stat[0]) ?></div><div style="font-size:34px;font-weight:700;margin:8px 0"><?= (int)$stat[1] ?></div><small><?= $esc($stat[2]) ?></small></div>
<?php endforeach; ?></section>
<section class="dashboard-card" style="background:#fff;border:1px solid #cbd5e1;border-radius:12px;margin-bottom:25px;overflow:hidden">
<div style="padding:20px"><h3>Suspicious Login Failures</h3><p>Accounts/IPs with at least 3 failed attempts in the last 24 hours. Resolve means reviewed, not unblocked.</p></div>
<div style="overflow-x:auto"><table style="width:100%;border-collapse:collapse;min-width:720px"><thead><tr style="background:#f1f5f9"><th style="padding:14px;text-align:left">Latest attempt</th><th style="padding:14px;text-align:left">Email / Portal</th><th style="padding:14px;text-align:left">IP address</th><th style="padding:14px;text-align:left">Failures</th><th style="padding:14px;text-align:left">Status</th><th style="padding:14px;text-align:left">Action</th></tr></thead><tbody>
<?php if(empty($data['alerts'])): ?><tr><td colspan="6" style="padding:20px">No suspicious login groups detected in the past 24 hours.</td></tr><?php endif; ?>
<?php foreach($data['alerts'] as $alert): ?><tr style="border-top:1px solid #e2e8f0"><td style="padding:14px"><?= $esc($alert['last_attempt']) ?></td><td style="padding:14px"><?= $esc($alert['email']) ?><br><small><?= $esc($alert['role']) ?></small></td><td style="padding:14px"><?= $esc($alert['ip_address']) ?></td><td style="padding:14px"><?= (int)$alert['attempts'] ?></td><td style="padding:14px"><?= $alert['resolved'] ? 'Reviewed' : 'Needs review' ?></td><td style="padding:14px"><?php if(!$alert['resolved']): ?><form method="post" action="<?= URLROOT ?>/admin/resolveSecurityAlert"><input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="email" value="<?= $esc($alert['email']) ?>"><input type="hidden" name="role" value="<?= $esc($alert['role']) ?>"><input type="hidden" name="ip" value="<?= $esc($alert['ip_address']) ?>"><button type="submit" class="btn" style="padding:8px;border:1px solid #64748b;border-radius:6px">Mark Reviewed</button></form><?php else: ?>—<?php endif; ?></td></tr><?php endforeach; ?>
</tbody></table></div></section>
<section class="dashboard-card" style="background:#fff;border:1px solid #cbd5e1;border-radius:12px;overflow:hidden"><div style="padding:20px"><h3>System Audit Log</h3><p>Latest 50 authentication attempts, newest first.</p></div><div style="overflow-x:auto"><table style="width:100%;min-width:650px;border-collapse:collapse"><thead><tr style="background:#f1f5f9"><th style="padding:12px;text-align:left">Time</th><th style="padding:12px;text-align:left">Email</th><th style="padding:12px;text-align:left">Portal</th><th style="padding:12px;text-align:left">IP</th><th style="padding:12px;text-align:left">Result</th></tr></thead><tbody>
<?php if(empty($data['recent'])): ?><tr><td colspan="5" style="padding:20px">No login activity has been recorded yet.</td></tr><?php endif; ?>
<?php foreach($data['recent'] as $entry): ?><tr style="border-top:1px solid #e2e8f0"><td style="padding:12px"><?= $esc($entry['attempted_at']) ?></td><td style="padding:12px"><?= $esc($entry['email']) ?></td><td style="padding:12px"><?= $esc($entry['role']) ?></td><td style="padding:12px"><?= $esc($entry['ip_address']) ?></td><td style="padding:12px"><?= $entry['successful'] ? 'Success':'Failed' ?></td></tr><?php endforeach; ?>
</tbody></table></div></section>
</main><?php require APPROOT . '/Views/shared/footer.php'; ?>
