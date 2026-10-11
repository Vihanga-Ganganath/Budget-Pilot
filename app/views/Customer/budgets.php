<?php require APPROOT . '/Views/shared/header.php'; ?>
<main class="main-content">
<header class="content-header">
    <div class="header-left">
        <h2 class="page-title" style="color: var(--color-primary);">Budget Setup</h2>
        <p class="page-subtitle" style="color: var(--color-text);">Configure your monthly spending limits and let Budget Pilot optimize your finances.</p>
    </div>
</header>

<div class="setup" style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- LEFT COLUMN: Income and Categories -->
    <div class="setup__main">
        
        <!-- Income Sources Section -->
        <section class="dashboard-card" style="background: #fff; padding: 24px; border-radius: var(--radius-main); border: 1px solid #CBD5E1; margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="margin: 0; color: var(--color-primary); font-size: 18px;">Income Sources</h3>
                <button class="btn" onclick="document.getElementById('incomeModal').style.display='flex'" style="background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px;">+ Add Income</button>
            </div>
            
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #CBD5E1;">
                        <th style="text-align: left; padding: 12px; color: var(--color-primary); font-weight: 600;">Source Name</th>
                        <th style="text-align: right; padding: 12px; color: var(--color-primary); font-weight: 600;">Monthly Amount</th>
                        <th style="text-align: right; padding: 12px; color: var(--color-primary); font-weight: 600;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid #CBD5E1;">
                        <td style="padding: 16px 12px; color: var(--color-primary); font-weight: 600;">Primary Salary</td>
                        <td style="padding: 16px 12px; color: var(--color-text); text-align: right; font-weight: 500;">$4,500.00</td>
                        <td style="padding: 16px 12px; text-align: right;">
                            <button class="btn" style="background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;">Edit</button>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #CBD5E1;">
                        <td style="padding: 16px 12px; color: var(--color-primary); font-weight: 600;">Freelance Work</td>
                        <td style="padding: 16px 12px; color: var(--color-text); text-align: right; font-weight: 500;">$850.00</td>
                        <td style="padding: 16px 12px; text-align: right;">
                            <button class="btn" style="background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;">Edit</button>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td style="padding: 16px 12px; color: var(--color-primary); font-weight: 700; text-align: right;">Total Estimated Income:</td>
                        <td style="padding: 16px 12px; color: var(--color-success); font-weight: 700; text-align: right; font-size: 18px;">$5,350.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </section>

        <!-- Categories block kept as static container for future backend loop -->
        <section class="dashboard-card" style="background: #fff; padding: 24px; border-radius: var(--radius-main); border: 1px solid #CBD5E1; margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="margin: 0; color: var(--color-primary); font-size: 18px;">Monthly Plan (Categories)</h3>
                <button class="btn" style="background: var(--color-primary); border: 1px solid var(--color-primary); color: #fff; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px;">Auto-Suggest</button>
            </div>
            
            <p style="color: var(--color-text-muted); font-size: 14px;">Categories will be loaded here dynamically from the database.</p>
        </section>

        <!-- Save button -->
        <div style="text-align: right; padding-bottom: 40px;">
            <button class="btn" style="background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 16px;">Save Configuration</button>
        </div>

    </div>

    <!-- RIGHT COLUMN: Goals & Summary -->
    <div class="setup__side">
        <!-- Savings Goals Section -->
        <section class="dashboard-card" style="background: #fff; padding: 24px; border-radius: var(--radius-main); border: 1px solid #CBD5E1; margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="margin: 0; color: var(--color-primary); font-size: 18px;">Savings Goals</h3>
                <button class="btn" onclick="document.getElementById('goalModal').style.display='flex'" style="background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 6px 12px; border-radius: 4px; font-weight: 600; cursor: pointer; font-size: 12px;">+ Add Goal</button>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <!-- Goal Card -->
                <div style="border: 1px solid #CBD5E1; border-radius: 8px; padding: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <h4 style="margin: 0; color: var(--color-primary); font-size: 16px;">Emergency Fund</h4>
                        <span style="background: #f1f5f9; color: var(--color-primary); font-size: 11px; padding: 4px 8px; border-radius: 12px; font-weight: 600; border: 1px solid #e2e8f0;">Household</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <div>
                            <p style="margin: 0 0 4px 0; color: var(--color-text-muted); font-size: 12px;">Target Amount</p>
                            <strong style="color: var(--color-primary); font-size: 18px;">$10,000.00</strong>
                        </div>
                        <button class="btn" style="background: transparent; border: none; color: var(--color-primary); cursor: pointer; font-size: 12px; text-decoration: underline;">Edit</button>
                    </div>
                </div>

                <!-- Goal Card -->
                <div style="border: 1px solid #CBD5E1; border-radius: 8px; padding: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <h4 style="margin: 0; color: var(--color-primary); font-size: 16px;">New Car</h4>
                        <span style="background: #f1f5f9; color: var(--color-primary); font-size: 11px; padding: 4px 8px; border-radius: 12px; font-weight: 600; border: 1px solid #e2e8f0;">Individual</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <div>
                            <p style="margin: 0 0 4px 0; color: var(--color-text-muted); font-size: 12px;">Target Amount</p>
                            <strong style="color: var(--color-primary); font-size: 18px;">$3,500.00</strong>
                        </div>
                        <button class="btn" style="background: transparent; border: none; color: var(--color-primary); cursor: pointer; font-size: 12px; text-decoration: underline;">Edit</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Total Planned Summary -->
        <section class="dashboard-card" style="background: var(--color-primary); padding: 24px; border-radius: var(--radius-main); color: #fff;">
            <span style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; opacity: 0.9;">Total Planned</span>
            <strong style="display: block; font-size: 32px; margin-bottom: 16px;">$0.00</strong>
            <p style="font-size: 13px; opacity: 0.8; margin-bottom: 16px;">Set your income, then auto-suggest a plan.</p>
            <div style="background: rgba(255,255,255,0.2); height: 8px; border-radius: 4px; overflow: hidden;">
                <div style="background: #fff; width: 0%; height: 100%;"></div>
            </div>
        </section>
    </div>
</div>

<!-- Modals -->

<!-- Add Income Modal -->
<div class="modal" id="incomeModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
    <div style="background: #fff; padding: 32px; border-radius: 12px; width: 100%; max-width: 400px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="margin: 0 0 24px 0; color: var(--color-primary); font-size: 20px;">Add Income Source</h2>
        <form>
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Source Name</label>
                <input type="text" placeholder="e.g., Primary Salary" style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text);">
            </div>
            <div style="margin-bottom: 24px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Monthly Amount ($)</label>
                <input type="number" step="0.01" placeholder="0.00" style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text);">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" style="background: transparent; border: 1px solid #CBD5E1; color: var(--color-text-muted); padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;" onclick="document.getElementById('incomeModal').style.display='none'">Cancel</button>
                <button type="button" style="background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 10px 24px; border-radius: 8px; font-weight: 600; cursor: pointer;">Add Income</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Goal Modal -->
<div class="modal" id="goalModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
    <div style="background: #fff; padding: 32px; border-radius: 12px; width: 100%; max-width: 400px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="margin: 0 0 24px 0; color: var(--color-primary); font-size: 20px;">Add Savings Goal</h2>
        <form>
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Goal Name</label>
                <input type="text" placeholder="e.g., Emergency Fund" style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text);">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Target Amount ($)</label>
                <input type="number" step="0.01" placeholder="0.00" style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text);">
            </div>
            <div style="margin-bottom: 24px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-primary);">Goal Type</label>
                <select style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text); background: #fff; appearance: auto;">
                    <option>Individual</option>
                    <option>Household</option>
                </select>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" style="background: transparent; border: 1px solid #CBD5E1; color: var(--color-text-muted); padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;" onclick="document.getElementById('goalModal').style.display='none'">Cancel</button>
                <button type="button" style="background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 10px 24px; border-radius: 8px; font-weight: 600; cursor: pointer;">Add Goal</button>
            </div>
        </form>
    </div>
</div>

</main>
<?php require APPROOT . '/Views/shared/footer.php'; ?>
