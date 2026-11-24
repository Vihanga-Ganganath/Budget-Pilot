<?php $pageCss = 'customer-css/budgets'; require APPROOT . '/Views/shared/header.php'; ?>
<?php
$isHead = !empty($data['is_head']);
$ro     = $isHead ? '' : 'disabled'; // family members can only look
function money($n) { return '$' . number_format($n, 2); }
$left = ($data['available_to_spend'] ?? 0) - ($data['total_planned'] ?? 0);
?>



<header class="content-header">
    <div class="header-left">
        <h2 class="page-title bp-page-title">Budget Setup</h2>
        <p class="page-subtitle">Plan for <?php echo date('F Y'); ?></p>
    </div>
</header>

<?php if (!empty($data['flash'])): ?>
    <p class="bp-flash-success"><?php echo htmlspecialchars($data['flash']); ?></p>
<?php endif; ?>
<?php if (!empty($data['error'])): ?>
    <p class="bp-flash-error"><?php echo htmlspecialchars($data['error']); ?></p>
<?php endif; ?>
<?php if (!$isHead): ?>
    <p class="bp-readonly-note">Only the account holder can change the budget.</p>
<?php endif; ?>

<!-- ===== 3 numbers ===== -->
<div class="budget-top">
    <section class="bp-card">
        <p class="bp-label">Total Income</p>
        <strong class="bp-stat-value bp-stat-value--success">$<?php echo number_format($data['total_income'] ?? 0, 2); ?></strong>
    </section>
    <section class="bp-card">
        <p class="bp-label">Savings Goal</p>
        <strong class="bp-stat-value bp-stat-value--primary">$<?php echo number_format($data['savings_goal'] ?? 0, 2); ?></strong>
    </section>
    <section class="bp-card">
        <p class="bp-label">Available to Spend</p>
        <strong class="bp-stat-value bp-stat-value--primary">$<?php echo number_format($data['available_to_spend'] ?? 0, 2); ?></strong>
    </section>
</div>

<!-- ===== Income (left) + additional income (right) ===== -->
<div class="budget-main">
    <section class="bp-card">
        <h3 class="bp-section-heading">Monthly Income</h3>
        <form action="<?php echo URLROOT; ?>/customer/budgets" method="post">
            <input type="hidden" name="action" value="incomes">
            <?php foreach ($data['incomes'] as $p): ?>
                <div class="bp-row">
                    <span><?php echo htmlspecialchars($p['name']); ?>
                        <small class="bp-muted">(<?php echo $p['household_role'] == 'head' ? 'main income' : 'family member'; ?>)</small>
                    </span>
                    <input type="number" name="income[<?php echo $p['id']; ?>]" min="0" step="0.01" value="<?php echo $p['monthly_income']; ?>" class="bp-input" <?php echo $ro; ?>>
                </div>
            <?php endforeach; ?>
            <?php if ($isHead): ?>
                <div class="bp-form-action-right"><button type="submit" class="bp-btn-success">Save Income</button></div>
            <?php endif; ?>
        </form>
    </section>

    <section class="bp-card">
        <h3 class="bp-section-heading">Additional Income <small class="bp-muted--sm">(optional)</small></h3>
        <?php foreach ($data['extras'] as $e): ?>
            <form action="<?php echo URLROOT; ?>/customer/budgets" method="post" class="bp-row bp-row--flush">
                <input type="hidden" name="action" value="remove_income">
                <input type="hidden" name="id" value="<?php echo $e['id']; ?>">
                <span><?php echo htmlspecialchars($e['description']); ?></span>
                <span><?php echo money($e['amount']); ?>
                    <?php if ($isHead): ?><button type="submit" class="bp-remove-btn">Remove</button><?php endif; ?>
                </span>
            </form>
        <?php endforeach; ?>
        <?php if (empty($data['extras'])): ?>
            <p class="bp-empty-text">No additional income this month.</p>
        <?php endif; ?>
        <?php if ($isHead): ?>
            <form action="<?php echo URLROOT; ?>/customer/budgets" method="post" class="bp-extra-income-form">
                <input type="hidden" name="action" value="add_income">
                <input type="text" name="name" placeholder="Name" class="bp-extra-income-name">
                <input type="number" name="amount" min="0.01" step="0.01" placeholder="Amount" class="bp-input">
                <button type="submit" class="bp-btn-success bp-btn-success--sm">Add</button>
            </form>
        <?php endif; ?>
    </section>
</div>

<!-- ===== Monthly plan (full width) ===== -->
<section class="bp-card bp-card--plan">
    <div class="bp-plan-header">
        <div>
            <h3 class="bp-plan-title">Monthly Plan</h3>
            <p class="bp-plan-subtitle">Allocate limits for mandatory fixed commitments and variable monthly expenses.</p>
        </div>
        <?php if ($isHead): ?>
            <a href="<?php echo URLROOT; ?>/customer/budgets?suggest=1" class="bp-btn-primary">Suggest Plan</a>
        <?php endif; ?>
    </div>

    <form action="<?php echo URLROOT; ?>/customer/budgets" method="post" id="budgetPlanForm">
        <input type="hidden" name="action" value="save_plan">

        <!-- ==================== 1. FIXED MANDATORY EXPENSES ==================== -->
        <div class="bp-plan-section">
            <div class="bp-fixed-header">
                <div>
                    <h4 class="bp-fixed-title">
                        Fixed Mandatory Expenses
                    </h4>
                    <p class="bp-plan-subtitle">
                        Rent, Loan repayments (with late penalty &amp; duration), and Tuition fees.
                    </p>
                </div>
                <?php if ($isHead): ?>
                    <button type="button" id="openFixedModalBtn" class="bp-btn-add-fixed">
                        <span>+</span> Add New Fixed Expense
                    </button>
                <?php endif; ?>
            </div>

            <div class="plan-grid" id="fixedGrid">
                <?php 
                $fixedRows = $data['fixed_rows'] ?? [];
                ?>

                <?php foreach ($fixedRows as $idx => $fr): ?>
                    <?php 
                        $catLower       = strtolower(trim($fr['category_name']));
                        $isLoan         = ($catLower === 'loan');
                        $isPaid         = !empty($fr['is_paid']);
                        $penaltyActive  = !$isPaid && $isLoan && !empty($fr['penalty_applied']);
                        $isPastDue      = !$isPaid && !empty($fr['due_date']) && strtotime($fr['due_date']) < strtotime(date('Y-m-d'));

                        $cardStateClass = '';
                        if ($isPaid) {
                            $cardStateClass = 'is-paid';
                        } elseif ($penaltyActive) {
                            $cardStateClass = 'is-penalty-applied';
                        } elseif ($isPastDue) {
                            $cardStateClass = 'is-past-due';
                        }
                    ?>
                    <div class="fixed-item-card <?php echo $cardStateClass; ?>" data-index="<?php echo $idx; ?>">
                        <input type="hidden" name="fixed[<?php echo $idx; ?>][budget_id]" value="<?php echo (int)($fr['budget_id'] ?? 0); ?>">
                        <input type="hidden" name="fixed[<?php echo $idx; ?>][category_id]" value="<?php echo (int)($fr['category_id'] ?? 0); ?>">

                        <!-- Card Header -->
                        <div class="bp-card-top">
                            <div>
                                <span class="bp-card-cat"><?php echo htmlspecialchars($fr['category_name']); ?></span>
                                <strong class="bp-card-name">
                                    <?php echo !empty($fr['custom_name']) ? htmlspecialchars($fr['custom_name']) : htmlspecialchars($fr['category_name']); ?>
                                </strong>
                            </div>
                            <div class="bp-card-actions">
                                <?php if ($isHead): ?>
                                    <button type="button" class="toggle-edit-btn" title="Edit this item">
                                        ✎ Edit
                                    </button>
                                    <?php if (!empty($fr['budget_id']) && !empty($fr['is_custom'])): ?>
                                        <button type="button" class="delete-fixed-btn" data-id="<?php echo (int)$fr['budget_id']; ?>" title="Delete item">Remove</button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- 1. DISPLAY-ONLY MODE (Default) -->
                        <div class="fixed-display-mode">
                            <div class="bp-meta-row">
                                <span class="bp-muted">Monthly Limit:</span>
                                <strong class="bp-meta-limit"><?php echo money($fr['limit']); ?></strong>
                            </div>
                            <div class="bp-meta-row">
                                <span class="bp-muted">Due Date:</span>
                                <span><?php echo !empty($fr['due_date']) ? date('M j, Y', strtotime($fr['due_date'])) : '<em class="bp-not-set">Not set</em>'; ?></span>
                            </div>

                            <?php if ($isLoan): ?>
                                <div class="bp-meta-row">
                                    <span class="bp-muted">Late Penalty Rate:</span>
                                    <span><?php echo number_format((float)$fr['penalty_rate'], 2); ?>%</span>
                                </div>
                                <div class="bp-meta-row--last">
                                    <span class="bp-muted">Loan Duration:</span>
                                    <span><?php echo !empty($fr['duration_months']) ? (int)$fr['duration_months'] . ' months' : '<em class="bp-not-set">Not set</em>'; ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- 2. EDIT MODE (Hidden by default until Edit button clicked) -->
                        <div class="fixed-edit-mode">
                            <label class="bp-label bp-label--sm">Custom Specific Name</label>
                            <input type="text" name="fixed[<?php echo $idx; ?>][custom_name]" placeholder="e.g. Son's Maths Class, Car Loan" value="<?php echo htmlspecialchars($fr['custom_name'] ?? ''); ?>" class="bp-input bp-input--full bp-input--left bp-input--mb8" <?php echo $ro; ?>>

                            <label class="bp-label bp-label--sm">Monthly Limit ($)</label>
                            <input type="number" name="fixed[<?php echo $idx; ?>][limit]" min="0" step="0.01" value="<?php echo $fr['limit']; ?>" class="bp-input bp-input--full bp-input--mb8" <?php echo $ro; ?>>

                            <label class="bp-label bp-label--sm">Due Date</label>
                            <input type="date" name="fixed[<?php echo $idx; ?>][due_date]" value="<?php echo htmlspecialchars($fr['due_date'] ?? ''); ?>" class="bp-input bp-input--full bp-input--left bp-input--mb8" <?php echo $ro; ?>>

                            <?php if ($isLoan): ?>
                                <label class="bp-label bp-label--sm">Late Penalty Rate (%)</label>
                                <input type="number" name="fixed[<?php echo $idx; ?>][penalty_rate]" min="0" max="100" step="0.01" value="<?php echo htmlspecialchars(isset($fr['penalty_rate']) ? $fr['penalty_rate'] : '0.00'); ?>" class="bp-input bp-input--full bp-input--mb8" <?php echo $ro; ?>>

                                <label class="bp-label bp-label--sm">Loan Duration (Months)</label>
                                <input type="number" name="fixed[<?php echo $idx; ?>][duration_months]" min="1" step="1" placeholder="e.g. 24" value="<?php echo htmlspecialchars($fr['duration_months'] ?? ''); ?>" class="bp-input bp-input--full bp-input--mb8" <?php echo $ro; ?>>
                            <?php endif; ?>

                            <div class="bp-edit-actions">
                                <button type="button" class="cancel-edit-btn bp-btn-secondary bp-btn-secondary--xs">Cancel</button>
                                <button type="submit" class="bp-btn-success bp-btn-success--xs">Save</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ==================== 2. VARIABLE EXPENSES ==================== -->
        <div class="bp-plan-section">
            <div class="bp-variable-header">
                <h4 class="bp-fixed-title">
                    Variable Expenses
                </h4>
                <p class="bp-plan-subtitle">
                    Flexible everyday household spending (Grocery, Transportation, Clothing, Medicine, etc.).
                </p>
            </div>

            <div class="plan-grid">
                <?php 
                $variableRows = $data['variable_rows'] ?? [];
                ?>
                <?php foreach ($variableRows as $vr): ?>
                    <div class="bp-variable-card">
                        <strong class="bp-variable-name"><?php echo htmlspecialchars($vr['name']); ?></strong>
                        <small class="bp-variable-suggested">Suggested: <?php echo money($vr['suggested']); ?></small>
                        
                        <label class="bp-label bp-label--sm">Monthly Limit ($)</label>
                        <input type="number" name="variable[<?php echo $vr['id'] ?? $vr['category_id']; ?>]" min="0" step="0.01" value="<?php echo $vr['limit']; ?>" class="bp-input bp-input--full" <?php echo $ro; ?>>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="bp-plan-footer">
            <span>Planned: <strong><?php echo money($data['total_planned'] ?? 0); ?></strong>
                <span class="bp-plan-left-space <?php echo $left < 0 ? 'bp-left--over' : 'bp-left--ok'; ?>">
                    <?php echo $left < 0 ? 'Over by ' . money(-$left) : 'Left: ' . money($left); ?>
                </span>
            </span>
            <?php if ($isHead): ?>
                <button type="submit" class="bp-btn-success">Save Plan</button>
            <?php endif; ?>
        </div>
    </form>
</section>

<!-- ==================== MODAL FOR ADDING NEW FIXED EXPENSE ==================== -->
<?php if ($isHead): ?>
<div id="addFixedModal">
    <div class="bp-modal-dialog">
        <div class="bp-modal-header">
            <h3 class="bp-modal-title">Add New Fixed Expense</h3>
            <button type="button" id="closeFixedModalBtn" class="bp-modal-close">&times;</button>
        </div>

        <form action="<?php echo URLROOT; ?>/customer/budgets" method="post" id="addFixedForm">
            <input type="hidden" name="action" value="add_fixed">

            <label class="bp-label bp-label--md">Category</label>
            <select name="category_id" id="modalFixedCategory" required class="bp-input bp-input--full bp-input--left bp-input--mb14 bp-modal-select">
                <?php 
                $fixedCats = !empty($data['fixed_categories']) ? $data['fixed_categories'] : [
                    ['id' => 5, 'name' => 'Rent'],
                    ['id' => 4, 'name' => 'Tuition'],
                    ['id' => 6, 'name' => 'Loan']
                ];
                foreach ($fixedCats as $fc): ?>
                    <option value="<?php echo (int)$fc['id']; ?>" data-name="<?php echo strtolower(trim($fc['name'])); ?>">
                        <?php echo htmlspecialchars($fc['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label class="bp-label bp-label--md">Custom Name</label>
            <input type="text" name="custom_name" placeholder="e.g. Son's Maths Class, Apartment Rent, Car Loan" required class="bp-input bp-input--full bp-input--left bp-input--mb14">

            <label class="bp-label bp-label--md">Amount ($)</label>
            <input type="number" name="limit" min="0.01" step="0.01" placeholder="0.00" required class="bp-input bp-input--full bp-input--mb14">

            <label class="bp-label bp-label--md">Due Date</label>
            <input type="date" name="due_date" required class="bp-input bp-input--full bp-input--left bp-input--mb14">

            <!-- Conditional Loan-Only Fields (Hidden for Rent & Tuition) -->
            <div id="modalLoanOnlyFields">
                <p class="bp-loan-terms-title">Loan-Specific Terms</p>

                <label class="bp-label bp-label--md">Late Penalty Rate (%)</label>
                <input type="number" name="penalty_rate" id="modalPenaltyRate" min="0" max="100" step="0.01" placeholder="e.g. 5.00" value="0.00" class="bp-input bp-input--full bp-input--mb12">

                <label class="bp-label bp-label--md">Loan Duration (Months)</label>
                <input type="number" name="duration_months" id="modalDurationMonths" min="1" step="1" placeholder="e.g. 24" class="bp-input bp-input--full">
            </div>

            <div class="bp-modal-footer">
                <button type="button" id="cancelFixedModalBtn" class="bp-btn-secondary">Cancel</button>
                <button type="submit" class="bp-btn-success">Save Fixed Expense</button>
            </div>
        </form>
    </div>
</div>

<!-- Hidden helper form for deleting custom fixed items -->
<form id="deleteFixedForm" action="<?php echo URLROOT; ?>/customer/budgets" method="post">
    <input type="hidden" name="action" value="delete_fixed">
    <input type="hidden" name="budget_id" id="deleteFixedBudgetId" value="">
</form>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Display-Only vs Edit Toggle for Fixed Expense Cards
    document.querySelectorAll('.fixed-item-card').forEach(function(card) {
        const displayMode = card.querySelector('.fixed-display-mode');
        const editMode = card.querySelector('.fixed-edit-mode');
        const editBtn = card.querySelector('.toggle-edit-btn');
        const cancelBtn = card.querySelector('.cancel-edit-btn');

        if (editBtn && displayMode && editMode) {
            editBtn.addEventListener('click', function() {
                const isEditing = editMode.style.display === 'block';
                editMode.style.display = isEditing ? 'none' : 'block';
                displayMode.style.display = isEditing ? 'block' : 'none';
                editBtn.textContent = isEditing ? '✎ Edit' : 'Close';
            });
        }

        if (cancelBtn && displayMode && editMode) {
            cancelBtn.addEventListener('click', function() {
                editMode.style.display = 'none';
                displayMode.style.display = 'block';
                if (editBtn) editBtn.textContent = '✎ Edit';
            });
        }

        const delBtn = card.querySelector('.delete-fixed-btn');
        if (delBtn) {
            delBtn.addEventListener('click', function() {
                const bId = delBtn.getAttribute('data-id');
                if (bId && confirm('Are you sure you want to remove this fixed expense?')) {
                    document.getElementById('deleteFixedBudgetId').value = bId;
                    document.getElementById('deleteFixedForm').submit();
                }
            });
        }
    });

    // 2. Modal Handling & Conditional Fields for Fixed Expenses
    const modal = document.getElementById('addFixedModal');
    const openBtn = document.getElementById('openFixedModalBtn');
    const closeBtn = document.getElementById('closeFixedModalBtn');
    const cancelModalBtn = document.getElementById('cancelFixedModalBtn');
    const catSelect = document.getElementById('modalFixedCategory');
    const loanOnlyDiv = document.getElementById('modalLoanOnlyFields');
    const penaltyInput = document.getElementById('modalPenaltyRate');
    const durationInput = document.getElementById('modalDurationMonths');

    function updateModalFields() {
        if (!catSelect || !loanOnlyDiv) return;
        const selectedOption = catSelect.options[catSelect.selectedIndex];
        const catName = selectedOption ? (selectedOption.getAttribute('data-name') || selectedOption.text).toLowerCase().trim() : '';

        if (catName === 'loan') {
            loanOnlyDiv.style.display = 'block';
            if (penaltyInput) penaltyInput.disabled = false;
            if (durationInput) durationInput.disabled = false;
        } else {
            // Rent or Tuition: hide penalty and loan duration fields completely
            loanOnlyDiv.style.display = 'none';
            if (penaltyInput) {
                penaltyInput.value = '0.00';
                penaltyInput.disabled = true;
            }
            if (durationInput) {
                durationInput.value = '';
                durationInput.disabled = true;
            }
        }
    }

    if (openBtn && modal) {
        openBtn.addEventListener('click', function() {
            modal.style.display = 'flex';
            updateModalFields();
        });
    }

    [closeBtn, cancelModalBtn].forEach(function(btn) {
        if (btn && modal) {
            btn.addEventListener('click', function() {
                modal.style.display = 'none';
            });
        }
    });

    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) modal.style.display = 'none';
        });
    }

    if (catSelect) {
        catSelect.addEventListener('change', updateModalFields);
        updateModalFields(); // Initialize on load
    }
});
</script>
<?php require APPROOT . '/Views/shared/footer.php'; ?>