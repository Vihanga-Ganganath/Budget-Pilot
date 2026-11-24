<?php $pageCss = 'customer-css/household'; require APPROOT . '/Views/shared/header.php'; ?>
<?php
$members = $data['members'] ?? [];
$sum     = $data['summary'] ?? ['budget' => 0, 'spent' => 0, 'left' => 0];
$isHead  = !empty($data['is_head']);
$old     = $data['old'] ?? [];
?>

<header class="content-header">
    <div class="header-left">
        <h2 class="page-title">Household Management</h2>
        <p class="page-subtitle">Manage family members and view combined spending.</p>
    </div>
    <div class="header-right">
        <?php if ($isHead): ?>
            <a href="<?php echo URLROOT; ?>/customer/household?add=1" class="household-btn-add">+ Add Family Member</a>
        <?php endif; ?>
    </div>
</header>

<?php if (!empty($data['flash'])): ?>
    <p class="household-flash-notice"><?php echo htmlspecialchars($data['flash']); ?></p>
<?php endif; ?>

<!-- Summary Cards (this month) -->
<section class="household-summary-grid">
    <div class="dashboard-card household-stat-card">
        <h3 class="household-stat-label">Household Budget</h3>
        <div class="household-stat-value">$<?php echo number_format($sum['budget'], 2); ?></div>
    </div>
    <div class="dashboard-card household-stat-card">
        <h3 class="household-stat-label">Spent by Everyone</h3>
        <div class="household-stat-value">$<?php echo number_format($sum['spent'], 2); ?></div>
    </div>
    <div class="dashboard-card household-stat-card">
        <h3 class="household-stat-label">Left This Month</h3>
        <div class="household-stat-value <?php echo $sum['left'] < 0 ? 'is-over' : 'is-ok'; ?>">$<?php echo number_format($sum['left'], 2); ?></div>
    </div>
</section>

<!-- People List -->
<section class="dashboard-card household-table-card">
    <h2 class="household-table-title">People on this account</h2>

    <div class="household-table-wrap">
        <table class="household-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th class="text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $m): ?>
                    <?php $isMe = ($m['id'] == $_SESSION['user_id']); ?>
                    <tr>
                        <td class="cell-name"><?php echo htmlspecialchars($m['name']) . ($isMe ? ' (You)' : ''); ?></td>
                        <td><?php echo htmlspecialchars($m['email']); ?></td>
                        <td><?php echo $m['household_role'] == 'head' ? '<strong>Account Holder</strong>' : 'Member'; ?></td>
                        <td class="text-right">
                            <?php if ($isMe): ?>
                                <!-- your own details are edited in Settings -->
                                <a href="<?php echo URLROOT; ?>/customer/settings" class="household-btn-edit">Edit</a>
                            <?php elseif ($isHead): ?>
                                <a href="<?php echo URLROOT; ?>/customer/household?edit=<?php echo $m['id']; ?>" class="household-btn-edit mr-8">Edit</a>
                                <form action="<?php echo URLROOT; ?>/customer/household" method="post" class="household-inline-form"
                                      onsubmit="return confirm('Remove this member? Their expenses will also be deleted.');">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                                    <button type="submit" class="btn household-btn-remove">Remove</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (!$isHead): ?>
        <p class="household-role-note">Only the account holder can add or change family members.</p>
    <?php endif; ?>
</section>

<!-- Add / Edit Member form (opens with ?add=1 or ?edit=ID) -->
<?php if (!empty($data['form'])): ?>
<div class="modal household-modal-overlay" id="memberModal">
    <div class="household-modal-box">
        <h2 class="household-modal-title"><?php echo $data['form'] == 'edit' ? 'Edit Family Member' : 'Add Family Member'; ?></h2>
        <form action="<?php echo URLROOT; ?>/customer/household" method="post">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?php echo (int) ($old['id'] ?? 0); ?>">
            <div class="household-modal-field">
                <label class="household-modal-label">Full Name</label>
                <input type="text" name="name" placeholder="e.g., Jane Doe" value="<?php echo htmlspecialchars($old['name'] ?? ''); ?>" class="household-modal-input">
            </div>
            <div class="household-modal-field">
                <label class="household-modal-label">Email Address</label>
                <input type="email" name="email" placeholder="name@example.com" value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>" class="household-modal-input">
            </div>
            <div class="household-modal-field">
                <label class="household-modal-label">Password</label>
                <input type="password" name="password" placeholder="<?php echo $data['form'] == 'edit' ? 'Leave blank to keep the current password' : 'At least 8 characters'; ?>" class="household-modal-input">
            </div>
            <p class="household-role-subnote">Role: <strong>Member</strong> (they log in with this email and password)</p>
            <?php if (!empty($data['error'])): ?>
                <p class="household-error-text"><?php echo htmlspecialchars($data['error']); ?></p>
            <?php endif; ?>
            <div class="household-modal-btn-row">
                <a href="<?php echo URLROOT; ?>/customer/household" class="household-modal-btn-cancel">Cancel</a>
                <button type="submit" class="household-modal-btn-save">Save Member</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php require APPROOT . '/Views/shared/footer.php'; ?>
