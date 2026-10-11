
<?php require APPROOT . '/Views/shared/header.php'; ?>
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
                        <span><?php echo htmlspecialchars($s->company_name ?? ''); ?></span>
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
                        <span><?php echo htmlspecialchars(mb_substr($s->business_information, 0, 100)) . (mb_strlen($s->business_information) > 100 ? '' : ''); ?></span>
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
                           Approve
                        </button>
                      </form>
                      <!-- Reject -->
                      <form method="POST" action="<?php echo URLROOT; ?>/admin/rejectSupplier" style="flex:1">
                        <input type="hidden" name="supplier_id" value="<?php echo (int) $s->id; ?>">
                        <button class="btn-reject" type="submit"
                                onclick="return confirm('Reject this application from <?php echo htmlspecialchars(addslashes($s->name)); ?>? This action sets their account to suspended.')">
                           Reject
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
                              <td><?php echo htmlspecialchars($r->company_name ?? ''); ?></td>
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
        <script src="<?php echo URLROOT; ?>/public/js/admin.js?v=<?php echo filemtime(APPROOT . '/../public/js/admin.js'); ?>"></script>

<?php require APPROOT . '/Views/shared/footer.php'; ?>






