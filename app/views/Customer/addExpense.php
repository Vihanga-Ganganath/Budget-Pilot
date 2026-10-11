<?php require APPROOT . '/Views/shared/header.php'; ?>
<main class="main-content">
<header class="content-header">
    <div class="header-left">
        <h2 class="page-title" style="color: var(--color-primary);">Add Expense</h2>
        <p class="page-subtitle" style="color: var(--color-text);">Log a new manual expense into your household budget.</p>
    </div>
</header>

<div style="display: grid; grid-template-columns: 3fr 2fr; gap: 24px; margin-bottom: 24px;">
    <!-- LEFT COLUMN: Form -->
    <div>
        <section class="dashboard-card" style="background: #fff; padding: 32px; border-radius: var(--radius-main); border: 1px solid #CBD5E1;">
            <form method="POST" action="<?php echo URLROOT; ?>/customer/addExpense">
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Amount ($)</label>
                    <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required style="width: 100%; padding: 12px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text); font-size: 16px;">
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Category</label>
                    <select name="category" class="form-control" required style="width: 100%; padding: 12px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text); font-size: 16px; background: #fff; appearance: auto;">
                        <option value="">Select a category...</option>
                        <option value="groceries">Groceries</option>
                        <option value="utilities">Utilities</option>
                        <option value="transportation">Transportation</option>
                        <option value="housing">Housing</option>
                        <option value="entertainment">Entertainment</option>
                        <option value="healthcare">Healthcare</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Description / Vendor</label>
                    <input type="text" name="description" class="form-control" placeholder="e.g., Whole Foods Market" required style="width: 100%; padding: 12px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text); font-size: 16px;">
                </div>

                <div class="form-group" style="margin-bottom: 32px;">
                    <label style="display:block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Date</label>
                    <input type="date" name="expense_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required style="width: 100%; padding: 12px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text); font-size: 16px;">
                </div>
                
                <div class="form-actions" style="display: flex; justify-content: flex-end; gap: 12px;">
                    <a href="<?php echo URLROOT; ?>/customer/dashboard" class="btn btn-cancel" style="background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">Cancel</a>
                    <button type="submit" class="btn" style="background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer;">Save Expense</button>
                </div>
            </form>
        </section>
    </div>

    <!-- RIGHT COLUMN: Quick Links & Recent -->
    <div>
        <!-- Quick Categories -->
        <section class="dashboard-card" style="background: #fff; padding: 24px; border-radius: var(--radius-main); border: 1px solid #CBD5E1; margin-bottom: 24px;">
            <h3 style="margin: 0 0 16px 0; color: var(--color-primary); font-size: 18px;">Quick Categories</h3>
            <p style="color: var(--color-text-muted); font-size: 14px; margin-bottom: 16px;">Tap to auto-fill the category above.</p>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                <button type="button" class="btn" style="background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 6px 12px; border-radius: 16px; font-size: 13px; font-weight: 600; cursor: pointer;">Groceries</button>
                <button type="button" class="btn" style="background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 6px 12px; border-radius: 16px; font-size: 13px; font-weight: 600; cursor: pointer;">Transport</button>
                <button type="button" class="btn" style="background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 6px 12px; border-radius: 16px; font-size: 13px; font-weight: 600; cursor: pointer;">Dining Out</button>
                <button type="button" class="btn" style="background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 6px 12px; border-radius: 16px; font-size: 13px; font-weight: 600; cursor: pointer;">Utilities</button>
                <button type="button" class="btn" style="background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 6px 12px; border-radius: 16px; font-size: 13px; font-weight: 600; cursor: pointer;">Healthcare</button>
            </div>
        </section>

        <!-- Recent Activity Placeholder -->
        <section class="dashboard-card" style="background: #fff; padding: 24px; border-radius: var(--radius-main); border: 1px solid #CBD5E1;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="margin: 0; color: var(--color-primary); font-size: 18px;">Recent Activity</h3>
                <a href="<?php echo URLROOT; ?>/customer/dashboard" style="color: var(--color-primary); font-size: 13px; font-weight: 600; text-decoration: none;">View All</a>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #CBD5E1; padding-bottom: 12px;">
                    <div>
                        <strong style="display: block; color: var(--color-primary); font-size: 15px; margin-bottom: 4px;">Whole Foods Market</strong>
                        <span style="color: var(--color-text-muted); font-size: 13px;">Groceries &bull; Today</span>
                    </div>
                    <strong style="color: var(--color-primary); font-size: 16px;">$85.20</strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #CBD5E1; padding-bottom: 12px;">
                    <div>
                        <strong style="display: block; color: var(--color-primary); font-size: 15px; margin-bottom: 4px;">Shell Station</strong>
                        <span style="color: var(--color-text-muted); font-size: 13px;">Transport &bull; Yesterday</span>
                    </div>
                    <strong style="color: var(--color-primary); font-size: 16px;">$42.50</strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong style="display: block; color: var(--color-primary); font-size: 15px; margin-bottom: 4px;">Netflix Subscription</strong>
                        <span style="color: var(--color-text-muted); font-size: 13px;">Entertainment &bull; Oct 05</span>
                    </div>
                    <strong style="color: var(--color-primary); font-size: 16px;">$15.99</strong>
                </div>
            </div>
        </section>
    </div>
</div>
</main>
<?php require APPROOT . '/Views/shared/footer.php'; ?>
