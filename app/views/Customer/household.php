<?php require APPROOT . '/Views/shared/header.php'; ?>

<header class="content-header">
    <div class="header-left">
        <h2 class="page-title">Household Management</h2>
        <p class="page-subtitle">Manage family members and view combined spending.</p>
    </div>
    <div class="header-right">
        <button class="btn" onclick="document.getElementById('memberModal').style.display='flex'" style="background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">+ Add Family Member</button>
    </div>
</header>

<!-- Summary Cards -->
<section style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px;">
    <div class="dashboard-card" style="background: #fff; padding: 20px; border-radius: var(--radius-main); border: 1px solid #CBD5E1;">
        <h3 style="margin: 0 0 8px 0; font-size: 14px; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase;">Household Budget</h3>
        <div style="font-size: 28px; font-weight: 700; color: var(--color-primary);">$4,200.00</div>
    </div>
    <div class="dashboard-card" style="background: #fff; padding: 20px; border-radius: var(--radius-main); border: 1px solid #CBD5E1;">
        <h3 style="margin: 0 0 8px 0; font-size: 14px; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase;">Spent by Everyone</h3>
        <div style="font-size: 28px; font-weight: 700; color: var(--color-primary);">$1,450.00</div>
    </div>
    <div class="dashboard-card" style="background: #fff; padding: 20px; border-radius: var(--radius-main); border: 1px solid #CBD5E1;">
        <h3 style="margin: 0 0 8px 0; font-size: 14px; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase;">Left This Month</h3>
        <div style="font-size: 28px; font-weight: 700; color: var(--color-success);">$2,750.00</div>
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
                <tr style="border-bottom: 1px solid #CBD5E1;">
                    <td style="padding: 16px 12px; color: var(--color-primary); font-weight: 600;">Alex Perera (You)</td>
                    <td style="padding: 16px 12px; color: var(--color-text);">alex@example.com</td>
                    <td style="padding: 16px 12px; color: var(--color-text); font-weight: 600;">Account Holder</td>
                    <td style="padding: 16px 12px; text-align: right;">
                        <button class="btn" style="background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 6px 12px; border-radius: 6px; cursor: pointer;">Edit</button>
                    </td>
                </tr>
                <tr style="border-bottom: 1px solid #CBD5E1;">
                    <td style="padding: 16px 12px; color: var(--color-primary); font-weight: 600;">Sarah Perera</td>
                    <td style="padding: 16px 12px; color: var(--color-text);">sarah@example.com</td>
                    <td style="padding: 16px 12px; color: var(--color-text);">Member</td>
                    <td style="padding: 16px 12px; text-align: right;">
                        <button class="btn" style="background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 6px 12px; border-radius: 6px; cursor: pointer; margin-right: 8px;">Edit</button>
                        <button class="btn" style="background: transparent; border: 1px solid #DC2626; color: #DC2626; padding: 6px 12px; border-radius: 6px; cursor: pointer;">Remove</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<!-- Add / Edit Modal Layout (Static) -->
<div class="modal" id="memberModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
    <div style="background: #fff; padding: 32px; border-radius: 12px; width: 100%; max-width: 500px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="margin: 0 0 24px 0; color: var(--color-primary); font-size: 20px;">Add Family Member</h2>
        <form>
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Full Name</label>
                <input type="text" placeholder="e.g., Jane Doe" style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text);">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Email Address</label>
                <input type="email" placeholder="name@example.com" style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text);">
            </div>
            <div style="margin-bottom: 24px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Role</label>
                <select style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text); background: #fff; appearance: auto;">
                    <option>Member</option>
                    <option>Account Holder</option>
                </select>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" style="background: transparent; border: 1px solid #CBD5E1; color: var(--color-text-muted); padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;" onclick="document.getElementById('memberModal').style.display='none'">Cancel</button>
                <button type="button" style="background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 10px 24px; border-radius: 8px; font-weight: 600; cursor: pointer;">Save Member</button>
            </div>
        </form>
    </div>
</div>

<?php require APPROOT . '/Views/shared/footer.php'; ?>
