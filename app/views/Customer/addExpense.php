<?php $pageCss = 'customer-css/addExpense'; require APPROOT . '/Views/shared/header.php'; ?>
<?php
$old = $data['old'];
?>

<header class="content-header">
    <div class="header-left">
        <h2 class="page-title text-navy">Add Expense</h2>
        <p class="page-subtitle">Record what you spent. It shows on the Dashboard straight away.</p>
    </div>
</header>

<?php if (!empty($data['flash'])): ?>
    <p class="dashboard-flash"><?php echo htmlspecialchars($data['flash']); ?></p>
<?php endif; ?>

<!-- ===== PENDING FIXED PAYMENTS (One-Click Pay) ===== -->
<?php if (!empty($data['pending_fixed'])): ?>
    <section class="pending-fixed-card">
        <div class="pending-fixed-header">
            <div>
                <h3 class="pending-fixed-title">
                    Pending Fixed Payments
                </h3>
                <p class="pending-fixed-subtitle">
                    Mandatory fixed expenses due this month (Rent, Loan, Tuition). One click automatically records the expense and marks it paid.
                </p>
            </div>
            <form method="post" action="<?php echo URLROOT; ?>/customer/markAllAsPaid" class="m-0">
                <input type="hidden" name="action" value="mark_all_as_paid">
                <button type="submit" class="pending-pay-all-btn">
                    I paid all (<?php echo count($data['pending_fixed']); ?>)
                </button>
            </form>
        </div>

        <div class="pending-fixed-grid">
            <?php foreach ($data['pending_fixed'] as $pf): ?>
                <?php 
                    $isOverdue = !empty($pf['due_date']) && strtotime($pf['due_date']) < strtotime(date('Y-m-d'));
                    $displayName = !empty($pf['custom_name']) ? $pf['custom_name'] : $pf['category_name'];
                ?>
                <div class="pending-item-card">
                    <div>
                        <div class="pending-item-top">
                            <div>
                                <strong class="pending-item-name"><?php echo htmlspecialchars($displayName); ?></strong>
                                <span class="pending-item-cat"><?php echo htmlspecialchars($pf['category_name']); ?></span>
                            </div>
                            <span class="pending-item-amount">$<?php echo number_format($pf['amount'], 2); ?></span>
                        </div>

                        <div class="pending-item-meta">
                            <?php if (!empty($pf['due_date'])): ?>
                                <div>Due: <strong class="<?= $isOverdue ? 'text-danger' : '' ?>"><?php echo date('M j, Y', strtotime($pf['due_date'])); ?></strong>
                                    <?php if ($isOverdue): ?>
                                        <span class="overdue-tag">Overdue</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (strtolower(trim($pf['category_name'])) === 'loan'): ?>
                                <?php if (!empty($pf['penalty_rate']) && (float)$pf['penalty_rate'] > 0): ?>
                                    <div class="mt-2">Penalty: <?php echo number_format($pf['penalty_rate'], 1); ?>%</div>
                                <?php endif; ?>
                                <?php if (!empty($pf['duration_months'])): ?>
                                    <div class="mt-2">Duration: <?php echo (int)$pf['duration_months']; ?> months</div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <form method="post" action="<?php echo URLROOT; ?>/customer/markAsPaid" class="m-0">
                        <input type="hidden" name="action" value="mark_as_paid">
                        <input type="hidden" name="budget_id" value="<?php echo $pf['id']; ?>">
                        <button type="submit" class="pending-pay-btn">
                            Paid it ($<?php echo number_format($pf['amount'], 2); ?>)
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<div class="expense-layout">
    <!-- ===== Form ===== -->
    <section class="expense-card">
        <form method="post" action="<?php echo URLROOT; ?>/customer/addExpense">
            <label class="expense-label">Amount ($)</label>
            <input type="number" step="0.01" min="0.01" name="amount" placeholder="0.00" value="<?php echo htmlspecialchars($old['amount'] ?? ''); ?>" class="expense-field">

            <label class="expense-label">Category</label>
            <select name="category" class="expense-field expense-field-select">
                <option value="">Select a category...</option>
                <?php foreach ($data['categories'] as $c): ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo ($old['category'] ?? '') == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                <?php endforeach; ?>
            </select>

            <label class="expense-label">Description <small class="text-muted font-normal">(optional)</small></label>
            <input type="text" name="description" placeholder="e.g., Keells Super" value="<?php echo htmlspecialchars($old['description'] ?? ''); ?>" class="expense-field">

            <label class="expense-label">Date</label>
            <input type="date" name="expense_date" value="<?php echo htmlspecialchars($old['expense_date'] ?? date('Y-m-d')); ?>" class="expense-field">

            <?php if (!empty($data['error'])): ?>
                <p class="text-danger mb-16"><?php echo htmlspecialchars($data['error']); ?></p>
            <?php endif; ?>

            <div class="expense-actions-row">
                <a href="<?php echo URLROOT; ?>/customer/dashboard" class="expense-btn-cancel">Cancel</a>
                <button type="submit" class="expense-btn-submit">Save Expense</button>
            </div>
        </form>
    </section>

    <!-- ===== This month: spent / limit per category ===== -->
    <section class="expense-card">
        <h3 class="mb-12 text-navy fs-18">This Month's Budget & Spending</h3>
        <?php foreach ($data['spending'] as $c): ?>
            <?php 
                $limit = (float)($data['plan'][$c['id']] ?? ($c['limit'] ?? 0)); 
                $spent = (float)($c['total'] ?? 0);
                $remaining = isset($c['remaining']) ? (float)$c['remaining'] : round($limit - $spent, 2);
                $over = ($limit > 0 && $spent > $limit); 
            ?>
            <div class="spending-track-row">
                <div>
                    <strong class="spending-cat-name"><?php echo htmlspecialchars($c['name']); ?></strong>
                    <div class="spending-cat-remaining <?php echo $remaining < 0 ? 'is-over' : 'is-ok'; ?>">
                        <?php if ($limit <= 0): ?>
                            <span class="text-muted">Allocated: $0.00</span>
                        <?php elseif ($remaining < 0): ?>
                            Over limit by $<?php echo number_format(abs($remaining), 2); ?>
                        <?php else: ?>
                            Remaining: $<?php echo number_format($remaining, 2); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="<?= $over ? 'text-danger' : '' ?> text-right">
                    <strong class="fs-15">$<?php echo number_format($spent, 2); ?></strong>
                    <small class="text-muted d-block fs-12">/ $<?php echo number_format($limit, 2); ?></small>
                </div>
            </div>
        <?php endforeach; ?>
        <p class="mt-12 fs-12 text-muted">Real-time tracking: Spent vs. Allocated Monthly Limit.</p>
    </section>
</div>

<?php require APPROOT . '/Views/shared/footer.php'; ?>
