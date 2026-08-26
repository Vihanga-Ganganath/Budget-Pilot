<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Approvals - Budget Pilot Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/common.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin.css">
    <style>
      /* ── Approval cards ────────────────────────────────────────────── */
      .approval-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:20px;margin-bottom:32px}
      .approval-card{background:#fff;border:1px solid var(--border-color,#E5E7EB);border-radius:12px;padding:22px;display:flex;flex-direction:column;gap:14px;box-shadow:0 1px 4px rgba(0,0,0,.05)}
      .approval-card__head{display:flex;align-items:center;gap:14px}
      .approval-card__avatar{width:46px;height:46px;border-radius:50%;background:#E8EAFF;color:#101C56;display:grid;place-items:center;font-weight:700;font-size:1.1rem;flex:none}
      .approval-card__name{font-weight:600;font-size:.97rem;color:#111827}
      .approval-card__email{font-size:.84rem;color:#6B7280}
      .approval-card__meta{display:grid;gap:6px}
      .approval-card__row{font-size:.87rem;color:#374151;display:flex;gap:8px}
      .approval-card__row span:first-child{color:#9CA3AF;min-width:72px}
      .approval-card__actions{display:flex;gap:10px;margin-top:4px}
      .btn-approve{flex:1;padding:10px;border:0;border-radius:8px;background:#101C56;color:#fff;font-weight:600;font-size:.9rem;cursor:pointer;transition:background .16s}
      .btn-approve:hover{background:#0B1550}
      .btn-reject{flex:1;padding:10px;border:1px solid #E5E7EB;border-radius:8px;background:#fff;color:#C2373C;font-weight:600;font-size:.9rem;cursor:pointer;transition:border-color .16s,background .16s}
      .btn-reject:hover{background:#FDF6F6;border-color:#D9636A}
      .empty-state{text-align:center;padding:48px 20px;color:#9CA3AF}
      .empty-state svg{width:52px;height:52px;margin:0 auto 16px;opacity:.4}
      /* Flash notice */
      .flash-notice{background:#E8F6EE;border:1px solid #BDE6CE;color:#1B6B42;border-radius:8px;padding:11px 16px;margin-bottom:20px;font-size:.9rem;font-weight:500}
      /* Decision table badge */
      .badge-approved{background:#DCFCE7;color:#166534;border-radius:99px;padding:3px 10px;font-size:.78rem;font-weight:600}
      .badge-rejected{background:#FEE2E2;color:#991B1B;border-radius:99px;padding:3px 10px;font-size:.78rem;font-weight:600}
    </style>
</head>
<body data-page="suppliers">

    <div class="dashboard-layout">
        <?php require '../app/Views/admin/_sidebar.php'; ?>

        <main class="main-content">
            <?php require '../app/Views/admin/_topbar.php'; ?>

            <header class="content-header">
                <div class="header-left">
                    <h2 class="page-title">Supplier Approvals</h2>
                    <p class="page-subtitle">Review and verify pending supplier registrations.</p>
                </div>
            </header>

            <?php if (!empty($data['flash'])): ?>
              <div class="flash-notice"><?php echo htmlspecialchars($data['flash']); ?></div>
            <?php endif; ?>

            <!-- Stat Cards -->
            <section class="stat-grid cols-3">
                <div class="stat-card">
                    <div class="stat-card-label">Pending Verification</div>
                    <div class="stat-card-value"><?php echo (int) $data['pending_count']; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-label">Recently Approved</div>
                    <div class="stat-card-value">
                        <?php
                            $approved = array_filter($data['recent_decisions'], fn($r) => $r->account_status === 'active');
                            echo count($approved);
                        ?>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-label">Rejected</div>
                    <div class="stat-card-value">
                        <?php
                            $rejected = array_filter($data['recent_decisions'], fn($r) => $r->account_status === 'suspended');
                            echo count($rejected);
                        ?>
                    </div>
                </div>
            </section>

            <!-- Pending Applications -->
            <div class="card-heading-row">
                <h3>Awaiting Verification (<?php echo (int) $data['pending_count']; ?>)</h3>
            </div>

            <?php if (empty($data['pending'])): ?>
              <div class="approval-grid">
                <div class="empty-state">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  <p style="font-size:1rem;font-weight:600;color:#6B7280">All caught up!</p>
                  <p style="font-size:.9rem">No pending supplier registrations at this time.</p>
                </div>
              </div>
            <?php else: ?>
              <div class="approval-grid">
                <?php foreach ($data['pending'] as $s): ?>
                  <div class="approval-card">
                    <div class="approval-card__head">
                      <div class="approval-card__avatar">
                        <?php echo mb_strtoupper(mb_substr($s->name, 0, 1)); ?>
                      </div>
                      <div>
                        <div class="approval-card__name"><?php echo htmlspecialchars($s->name); ?></div>
                        <div class="approval-card__email"><?php echo htmlspecialchars($s->email); ?></div>
                      </div>
                    </div>
                    <div class="approval-card__meta">
                      <div class="approval-card__row">
                        <span>Company</span>
                        <span><?php echo htmlspecialchars($s->company_name ?? '—'); ?></span>
                      </div>
                      <?php if (!empty($s->phone_number)): ?>
                      <div class="approval-card__row">
                        <span>Phone</span>
                        <span><?php echo htmlspecialchars($s->phone_number); ?></span>
                      </div>
                      <?php endif; ?>
                      <?php if (!empty($s->business_information)): ?>
                      <div class="approval-card__row">
                        <span>About</span>
                        <span><?php echo htmlspecialchars(mb_substr($s->business_information, 0, 100)) . (mb_strlen($s->business_information) > 100 ? '…' : ''); ?></span>
                      </div>
                      <?php endif; ?>
                      <div class="approval-card__row">
                        <span>Applied</span>
                        <span><?php echo date('d M Y, H:i', strtotime($s->created_at)); ?></span>
                      </div>
                    </div>
                    <div class="approval-card__actions">
                      <!-- Approve -->
                      <form method="POST" action="<?php echo URLROOT; ?>/admin/approveSupplier" style="flex:1">
                        <input type="hidden" name="supplier_id" value="<?php echo (int) $s->id; ?>">
                        <button class="btn-approve" type="submit"
                                onclick="return confirm('Approve <?php echo htmlspecialchars(addslashes($s->name)); ?>?')">
                          ✓ Approve
                        </button>
                      </form>
                      <!-- Reject -->
                      <form method="POST" action="<?php echo URLROOT; ?>/admin/rejectSupplier" style="flex:1">
                        <input type="hidden" name="supplier_id" value="<?php echo (int) $s->id; ?>">
                        <button class="btn-reject" type="submit"
                                onclick="return confirm('Reject this application from <?php echo htmlspecialchars(addslashes($s->name)); ?>? This action sets their account to suspended.')">
                          ✕ Reject
                        </button>
                      </form>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <!-- Recent Decisions -->
            <div class="dashboard-card" style="padding:0;">
                <div style="padding:24px 24px 0;">
                    <h3 style="margin-bottom:16px;">Recent Decisions</h3>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Supplier Name</th>
                            <th>Company</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['recent_decisions'])): ?>
                          <tr><td colspan="4" style="text-align:center;color:#9CA3AF;padding:28px">No decisions recorded yet.</td></tr>
                        <?php else: ?>
                          <?php foreach ($data['recent_decisions'] as $r): ?>
                            <tr>
                              <td>
                                <strong><?php echo htmlspecialchars($r->name); ?></strong><br>
                                <span style="font-size:.82rem;color:#9CA3AF"><?php echo htmlspecialchars($r->email); ?></span>
                              </td>
                              <td><?php echo htmlspecialchars($r->company_name ?? '—'); ?></td>
                              <td>
                                <?php if ($r->account_status === 'active'): ?>
                                  <span class="badge-approved">Approved</span>
                                <?php else: ?>
                                  <span class="badge-rejected">Rejected</span>
                                <?php endif; ?>
                              </td>
                              <td><?php echo date('d M Y', strtotime($r->updated_at)); ?></td>
                            </tr>
                          <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="<?php echo URLROOT; ?>/public/js/admin.js"></script>
</body>
</html>
