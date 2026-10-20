<?php require APPROOT . '/Views/shared/header.php'; ?>
<?php
$isHead = $data['isHead'];
$ro     = $isHead ? '' : 'disabled'; // family members can only look
$card   = "background: #fff; padding: 24px; border-radius: var(--radius-main); border: 1px solid #CBD5E1;";
$label  = "margin: 0 0 6px; font-size: 13px; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase;";
$row    = "display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #E2E8F0;";
$input  = "width: 130px; padding: 8px 10px; border: 1px solid #CBD5E1; border-radius: 6px; text-align: right;";
$btn    = "background: var(--color-success); border: none; color: #fff; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;";
function money($n) { return '$' . number_format($n, 2); }
$left = $data['available'] - $data['totalPlanned'];
?>

<header class="content-header">
    <div class="header-left">
        <h2 class="page-title" style="color: var(--color-primary);">Budget Setup</h2>
        <p class="page-subtitle">Plan for <?php echo date('F Y'); ?></p>
    </div>
</header>

<?php if (!empty($data['flash'])): ?>
    <p style="background: #DCFCE7; color: #166534; padding: 12px 16px; border-radius: 8px; margin: 0 0 20px;"><?php echo htmlspecialchars($data['flash']); ?></p>
<?php endif; ?>
<?php if (!empty($data['error'])): ?>
    <p style="background: #FEE2E2; color: #B91C1C; padding: 12px 16px; border-radius: 8px; margin: 0 0 20px;"><?php echo htmlspecialchars($data['error']); ?></p>
<?php endif; ?>
<?php if (!$isHead): ?>
    <p style="color: var(--color-text-muted); margin: 0 0 20px;">Only the account holder can change the budget.</p>
<?php endif; ?>

<!-- ===== 3 numbers ===== -->
<div class="budget-top">
    <section style="<?php echo $card; ?>">
        <p style="<?php echo $label; ?>">Total Income</p>
        <strong style="font-size: 26px; color: var(--color-success);"><?php echo money($data['totalIncome']); ?></strong>
    </section>
    <section style="<?php echo $card; ?>">
        <p style="<?php echo $label; ?>">Savings Goal</p>
        <strong style="font-size: 26px; color: var(--color-primary);"><?php echo money($data['savings']); ?></strong>
    </section>
    <section style="<?php echo $card; ?>">
        <p style="<?php echo $label; ?>">Available to Spend</p>
        <strong style="font-size: 26px; color: var(--color-primary);"><?php echo money($data['available']); ?></strong>
    </section>
</div>

<!-- ===== Income (left) + additional income (right) ===== -->
<div class="budget-main">
    <section style="<?php echo $card; ?>">
        <h3 style="margin: 0 0 8px; color: var(--color-primary); font-size: 18px;">Monthly Income</h3>
        <form action="<?php echo URLROOT; ?>/customer/budgets" method="post">
            <input type="hidden" name="action" value="incomes">
            <?php foreach ($data['incomes'] as $p): ?>
                <div style="<?php echo $row; ?>">
                    <span><?php echo htmlspecialchars($p['name']); ?>
                        <small style="color: var(--color-text-muted);">(<?php echo $p['household_role'] == 'head' ? 'main income' : 'family member'; ?>)</small>
                    </span>
                    <input type="number" name="income[<?php echo $p['id']; ?>]" min="0" step="0.01" value="<?php echo $p['monthly_income']; ?>" style="<?php echo $input; ?>" <?php echo $ro; ?>>
                </div>
            <?php endforeach; ?>
            <?php if ($isHead): ?>
                <div style="text-align: right; margin-top: 12px;"><button type="submit" style="<?php echo $btn; ?>">Save Income</button></div>
            <?php endif; ?>
        </form>
    </section>

    <section style="<?php echo $card; ?>">
        <h3 style="margin: 0 0 8px; color: var(--color-primary); font-size: 18px;">Additional Income <small style="font-weight: 400; color: var(--color-text-muted);">(optional)</small></h3>
        <?php foreach ($data['extras'] as $e): ?>
            <form action="<?php echo URLROOT; ?>/customer/budgets" method="post" style="<?php echo $row; ?> margin: 0;">
                <input type="hidden" name="action" value="remove_income">
                <input type="hidden" name="id" value="<?php echo $e['id']; ?>">
                <span><?php echo htmlspecialchars($e['description']); ?></span>
                <span><?php echo money($e['amount']); ?>
                    <?php if ($isHead): ?><button type="submit" style="background: none; border: none; color: #DC2626; cursor: pointer; margin-left: 8px;">Remove</button><?php endif; ?>
                </span>
            </form>
        <?php endforeach; ?>
        <?php if (empty($data['extras'])): ?>
            <p style="color: var(--color-text-muted); margin: 8px 0;">No additional income this month.</p>
        <?php endif; ?>
        <?php if ($isHead): ?>
            <form action="<?php echo URLROOT; ?>/customer/budgets" method="post" style="display: flex; gap: 8px; margin-top: 12px;">
                <input type="hidden" name="action" value="add_income">
                <input type="text" name="name" placeholder="Name" style="flex: 1; min-width: 0; padding: 8px 10px; border: 1px solid #CBD5E1; border-radius: 6px;">
                <input type="number" name="amount" min="0.01" step="0.01" placeholder="Amount" style="<?php echo $input; ?>">
                <button type="submit" style="<?php echo $btn; ?> padding: 8px 14px;">Add</button>
            </form>
        <?php endif; ?>
    </section>
</div>

<!-- ===== Monthly plan (full width) ===== -->
<section style="<?php echo $card; ?> margin-top: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 16px;">
        <h3 style="margin: 0; color: var(--color-primary); font-size: 18px;">Monthly Plan</h3>
        <?php if ($isHead): ?>
            <a href="<?php echo URLROOT; ?>/customer/budgets?suggest=1" style="background: var(--color-primary); color: #fff; padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none;">Suggest Plan</a>
        <?php endif; ?>
    </div>

    <form action="<?php echo URLROOT; ?>/customer/budgets" method="post">
        <input type="hidden" name="action" value="save_plan">
        <div class="plan-grid">
            <?php foreach ($data['rows'] as $r): ?>
                <div style="border: 1px solid #E2E8F0; border-radius: 10px; padding: 14px;">
                    <strong style="display: block; margin-bottom: 4px;"><?php echo htmlspecialchars($r['name']); ?></strong>
                    <small style="display: block; color: var(--color-text-muted); margin-bottom: 8px;">Suggested: <?php echo money($r['suggested']); ?></small>
                    <input type="number" name="limit[<?php echo $r['id']; ?>]" min="0" step="0.01" value="<?php echo $r['limit']; ?>" style="<?php echo $input; ?> width: 100%;" <?php echo $ro; ?>>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if (empty($data['rows'])): ?>
            <p style="color: #DC2626;">No categories found. Run update_budget_categories.sql in phpMyAdmin.</p>
        <?php endif; ?>

        <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-top: 20px;">
            <span>Planned: <strong><?php echo money($data['totalPlanned']); ?></strong> &nbsp;
                <span style="color: <?php echo $left < 0 ? '#DC2626' : 'var(--color-success)'; ?>;">
                    <?php echo $left < 0 ? 'Over by ' . money(-$left) : 'Left: ' . money($left); ?>
                </span>
            </span>
            <?php if ($isHead): ?>
                <button type="submit" style="<?php echo $btn; ?>">Save Plan</button>
            <?php endif; ?>
        </div>
    </form>
</section>

<?php require APPROOT . '/Views/shared/footer.php'; ?>
