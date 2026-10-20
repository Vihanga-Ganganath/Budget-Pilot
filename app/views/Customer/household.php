<?php require APPROOT . '/Views/shared/header.php'; ?>
<?php
$members = $data['members'] ?? [];
$sum     = $data['summary'] ?? ['budget' => 0, 'spent' => 0, 'left' => 0];
$isHead  = $data['isHead'] ?? false;
$old     = $data['old'] ?? [];
$btnEdit = "display:inline-block; background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 6px 12px; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 14px;";
$input   = "width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text);";
?>

<header class="content-header">
    <div class="header-left">
        <h2 class="page-title">Household Management</h2>
        <p class="page-subtitle">Manage family members and view combined spending.</p>
    </div>
    <div class="header-right">
        <?php if ($isHead): ?>
            <a href="<?php echo URLROOT; ?>/customer/household?add=1" class="btn" style="background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none;">+ Add Family Member</a>
        <?php endif; ?>
    </div>
</header>

<?php if (!empty($data['flash'])): ?>
    <p style="background: #DCFCE7; border: 1px solid #BDE6CE; color: #166534; padding: 12px 16px; border-radius: 8px; margin: 0 0 20px;"><?php echo htmlspecialchars($data['flash']); ?></p>
<?php endif; ?>

<!-- Summary Cards (this month) -->
<section style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px;">
    <div class="dashboard-card" style="background: #fff; padding: 20px; border-radius: var(--radius-main); border: 1px solid #CBD5E1;">
        <h3 style="margin: 0 0 8px 0; font-size: 14px; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase;">Household Budget</h3>
        <div style="font-size: 28px; font-weight: 700; color: var(--color-primary);">$<?php echo number_format($sum['budget'], 2); ?></div>
    </div>
    <div class="dashboard-card" style="background: #fff; padding: 20px; border-radius: var(--radius-main); border: 1px solid #CBD5E1;">
        <h3 style="margin: 0 0 8px 0; font-size: 14px; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase;">Spent by Everyone</h3>
        <div style="font-size: 28px; font-weight: 700; color: var(--color-primary);">$<?php echo number_format($sum['spent'], 2); ?></div>
    </div>
    <div class="dashboard-card" style="background: #fff; padding: 20px; border-radius: var(--radius-main); border: 1px solid #CBD5E1;">
        <h3 style="margin: 0 0 8px 0; font-size: 14px; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase;">Left This Month</h3>
        <div style="font-size: 28px; font-weight: 700; color: <?php echo $sum['left'] < 0 ? '#DC2626' : 'var(--color-success)'; ?>;">$<?php echo number_format($sum['left'], 2); ?></div>
    </div>
</section>

<!-- People List -->
<section class="dashboard-card" style="background: #fff; padding: 24px; border-radius: var(--radius-main); border: 1px solid #CBD5E1; margin-bottom: 24px;">
    <h2 style="margin: 0 0 16px 0; color: var(--color-primary); font-size: 18px;">People on this account</h2>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
            <thead>
                <tr style="border-bottom: 2px solid #CBD5E1;">
                    <th style="text-align: left; padding: 12px; color: var(--color-primary); font-weight: 600;">Name</th>
                    <th style="text-align: left; padding: 12px; color: var(--color-primary); font-weight: 600;">Email</th>
                    <th style="text-align: left; padding: 12px; color: var(--color-primary); font-weight: 600;">Role</th>
                    <th style="text-align: right; padding: 12px; color: var(--color-primary); font-weight: 600;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $m): ?>
                    <?php $isMe = ($m['id'] == $_SESSION['user_id']); ?>
                    <tr style="border-bottom: 1px solid #CBD5E1;">
                        <td style="padding: 16px 12px; color: var(--color-primary); font-weight: 600;"><?php echo htmlspecialchars($m['name']) . ($isMe ? ' (You)' : ''); ?></td>
                        <td style="padding: 16px 12px; color: var(--color-text);"><?php echo htmlspecialchars($m['email']); ?></td>
                        <td style="padding: 16px 12px; color: var(--color-text);"><?php echo $m['household_role'] == 'head' ? '<strong>Account Holder</strong>' : 'Member'; ?></td>
                        <td style="padding: 16px 12px; text-align: right;">
                            <?php if ($isMe): ?>
                                <!-- your own details are edited in Settings -->
                                <a href="<?php echo URLROOT; ?>/customer/settings" style="<?php echo $btnEdit; ?>">Edit</a>
                            <?php elseif ($isHead): ?>
                                <a href="<?php echo URLROOT; ?>/customer/household?edit=<?php echo $m['id']; ?>" style="<?php echo $btnEdit; ?> margin-right: 8px;">Edit</a>
                                <form action="<?php echo URLROOT; ?>/customer/household" method="post" style="display: inline;"
                                      onsubmit="return confirm('Remove this member? Their expenses will also be deleted.');">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                                    <button type="submit" class="btn" style="background: transparent; border: 1px solid #DC2626; color: #DC2626; padding: 6px 12px; border-radius: 6px; cursor: pointer;">Remove</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (!$isHead): ?>
        <p style="margin: 16px 0 0; color: var(--color-text-muted); font-size: 14px;">Only the account holder can add or change family members.</p>
    <?php endif; ?>
</section>

<!-- Add / Edit Member form (opens with ?add=1 or ?edit=ID) -->
<?php if (!empty($data['form'])): ?>
<div class="modal" id="memberModal" style="display: flex; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
    <div style="background: #fff; padding: 32px; border-radius: 12px; width: 100%; max-width: 500px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="margin: 0 0 24px 0; color: var(--color-primary); font-size: 20px;"><?php echo $data['form'] == 'edit' ? 'Edit Family Member' : 'Add Family Member'; ?></h2>
        <form action="<?php echo URLROOT; ?>/customer/household" method="post">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?php echo (int) ($old['id'] ?? 0); ?>">
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Full Name</label>
                <input type="text" name="name" placeholder="e.g., Jane Doe" value="<?php echo htmlspecialchars($old['name'] ?? ''); ?>" style="<?php echo $input; ?>">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Email Address</label>
                <input type="email" name="email" placeholder="name@example.com" value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>" style="<?php echo $input; ?>">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Password</label>
                <input type="password" name="password" placeholder="<?php echo $data['form'] == 'edit' ? 'Leave blank to keep the current password' : 'At least 8 characters'; ?>" style="<?php echo $input; ?>">
            </div>
            <p style="margin: 0 0 16px; color: var(--color-text-muted); font-size: 14px;">Role: <strong>Member</strong> (they log in with this email and password)</p>
            <?php if (!empty($data['error'])): ?>
                <p style="margin: 0 0 16px; color: #DC2626; font-size: 14px;"><?php echo htmlspecialchars($data['error']); ?></p>
            <?php endif; ?>
            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <a href="<?php echo URLROOT; ?>/customer/household" style="background: transparent; border: 1px solid #CBD5E1; color: var(--color-text-muted); padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none;">Cancel</a>
                <button type="submit" style="background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 10px 24px; border-radius: 8px; font-weight: 600; cursor: pointer;">Save Member</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php require APPROOT . '/Views/shared/footer.php'; ?>
