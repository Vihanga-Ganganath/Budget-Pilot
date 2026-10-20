<?php require APPROOT . '/Views/shared/header.php'; ?>
<?php
$old   = $data['old'];
$card  = "background: #fff; padding: 28px; border-radius: var(--radius-main); border: 1px solid #CBD5E1;";
$label = "display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);";
$field = "width: 100%; padding: 12px 14px; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 16px; margin-bottom: 20px;";
?>

<header class="content-header">
    <div class="header-left">
        <h2 class="page-title" style="color: var(--color-primary);">Add Expense</h2>
        <p class="page-subtitle">Record what you spent. It shows on the Dashboard straight away.</p>
    </div>
</header>

<?php if (!empty($data['flash'])): ?>
    <p style="background: #DCFCE7; color: #166534; padding: 12px 16px; border-radius: 8px; margin: 0 0 20px;"><?php echo htmlspecialchars($data['flash']); ?></p>
<?php endif; ?>

<style>
    .expense-layout { display: grid; grid-template-columns: 3fr 2fr; gap: 24px; align-items: start; }
    @media (max-width: 1080px) { .expense-layout { grid-template-columns: 1fr; } }
</style>
<div class="expense-layout">
    <!-- ===== Form ===== -->
    <section style="<?php echo $card; ?>">
        <form method="post" action="<?php echo URLROOT; ?>/customer/addExpense">
            <label style="<?php echo $label; ?>">Amount ($)</label>
            <input type="number" step="0.01" min="0.01" name="amount" placeholder="0.00" value="<?php echo htmlspecialchars($old['amount'] ?? ''); ?>" style="<?php echo $field; ?>">

            <label style="<?php echo $label; ?>">Category</label>
            <select name="category" style="<?php echo $field; ?> background: #fff;">
                <option value="">Select a category...</option>
                <?php foreach ($data['categories'] as $c): ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo ($old['category'] ?? '') == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                <?php endforeach; ?>
            </select>

            <label style="<?php echo $label; ?>">Description <small style="font-weight: 400; color: var(--color-text-muted);">(optional)</small></label>
            <input type="text" name="description" placeholder="e.g., Keells Super" value="<?php echo htmlspecialchars($old['description'] ?? ''); ?>" style="<?php echo $field; ?>">

            <label style="<?php echo $label; ?>">Date</label>
            <input type="date" name="expense_date" value="<?php echo htmlspecialchars($old['expense_date'] ?? date('Y-m-d')); ?>" style="<?php echo $field; ?>">

            <?php if (!empty($data['error'])): ?>
                <p style="color: #DC2626; margin: 0 0 16px;"><?php echo htmlspecialchars($data['error']); ?></p>
            <?php endif; ?>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <a href="<?php echo URLROOT; ?>/customer/dashboard" style="border: 1px solid var(--color-primary); color: var(--color-primary); padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">Cancel</a>
                <button type="submit" style="background: var(--color-success); border: none; color: #fff; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer;">Save Expense</button>
            </div>
        </form>
    </section>

    <!-- ===== This month: spent / limit per category ===== -->
    <section style="<?php echo $card; ?>">
        <h3 style="margin: 0 0 12px; color: var(--color-primary); font-size: 18px;">This Month</h3>
        <?php foreach ($data['spending'] as $c): ?>
            <?php $limit = $data['plan'][$c['id']] ?? 0; $over = $limit > 0 && $c['total'] > $limit; ?>
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                <span><?php echo htmlspecialchars($c['name']); ?></span>
                <span style="color: <?php echo $over ? '#DC2626' : 'var(--color-text)'; ?>;">
                    $<?php echo number_format($c['total'], 2); ?>
                    <small style="color: var(--color-text-muted);">/ <?php echo $limit > 0 ? '$' . number_format($limit, 2) : 'no limit'; ?></small>
                </span>
            </div>
        <?php endforeach; ?>
        <p style="margin: 12px 0 0; font-size: 13px; color: var(--color-text-muted);">Spent / your limit from Budgets. Red = over the limit.</p>
    </section>
</div>

<?php require APPROOT . '/Views/shared/footer.php'; ?>
