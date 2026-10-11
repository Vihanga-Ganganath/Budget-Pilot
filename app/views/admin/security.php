<?php require APPROOT . '/Views/shared/header.php'; ?>
<main class="main-content">
<header class="content-header" style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div class="header-left">
        <h2 class="page-title" style="color: var(--color-primary);">Security &amp; Audit</h2>
        <p class="page-subtitle" style="color: var(--color-text);">Monitor active sessions and review system-wide login activity.</p>
    </div>
    <div class="header-right" style="display: flex; gap: 12px;">
        <button class="btn" style="background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">
            Export Audit Log
        </button>
        <button class="btn" style="background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">
            Force All Users to Re-login
        </button>
    </div>
</header>

<!-- Summary Cards -->
<section style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 24px;">
    <!-- Card 1 -->
    <div class="dashboard-card" style="background: var(--color-primary); color: #fff; padding: 24px; border-radius: var(--radius-main); box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div style="font-size: 14px; font-weight: 600; opacity: 0.9; margin-bottom: 8px;">Total Active Sessions</div>
        <div style="font-size: 36px; font-weight: 700; margin-bottom: 4px;">42</div>
        <div style="font-size: 13px; opacity: 0.8;">Currently logged in users</div>
    </div>
    
    <!-- Card 2 -->
    <div class="dashboard-card" style="background: #fff; border: 1px solid #CBD5E1; color: var(--color-primary); padding: 24px; border-radius: var(--radius-main); box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="font-size: 14px; font-weight: 600; margin-bottom: 8px;">Failed Login Attempts</div>
        <div style="font-size: 36px; font-weight: 700; margin-bottom: 4px;">5</div>
        <div style="font-size: 13px; color: var(--color-text-muted);">Recorded in the last 24h</div>
    </div>

    <!-- Card 3 -->
    <div class="dashboard-card" style="background: #fff; border: 1px solid #CBD5E1; color: var(--color-primary); padding: 24px; border-radius: var(--radius-main); box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="font-size: 14px; font-weight: 600; margin-bottom: 8px;">Password Resets</div>
        <div style="font-size: 36px; font-weight: 700; margin-bottom: 4px;">12</div>
        <div style="font-size: 13px; color: var(--color-text-muted);">Initiated this week</div>
    </div>
</section>

<!-- Suspicious Login Failures Table -->
<section class="dashboard-card" style="background: #fff; border-radius: var(--radius-main); border: 1px solid #CBD5E1; overflow: hidden; margin-bottom: 40px;">
    <div style="padding: 24px; border-bottom: 1px solid #CBD5E1;">
        <h3 style="margin: 0; color: var(--color-primary); font-size: 18px;">Suspicious Login Failures</h3>
    </div>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #CBD5E1; background: #f8f9fa;">
                    <th style="text-align: left; padding: 16px; color: var(--color-primary); font-weight: 600;">Date &amp; Time</th>
                    <th style="text-align: left; padding: 16px; color: var(--color-primary); font-weight: 600;">User / Email</th>
                    <th style="text-align: left; padding: 16px; color: var(--color-primary); font-weight: 600;">Failed Attempts</th>
                    <th style="text-align: center; padding: 16px; color: var(--color-primary); font-weight: 600;">Status</th>
                    <th style="text-align: right; padding: 16px; color: var(--color-primary); font-weight: 600;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Row 1 -->
                <tr style="border-bottom: 1px solid #CBD5E1;">
                    <td style="padding: 16px; color: var(--color-text); font-size: 14px;">
                        <strong style="display: block; color: var(--color-primary); font-size: 14px;">Oct 10, 2026</strong>
                        09:45 AM
                    </td>
                    <td style="padding: 16px;">
                        <strong style="display: block; color: var(--color-primary); font-size: 15px; margin-bottom: 4px;">Unknown User</strong>
                        <span style="color: var(--color-text-muted); font-size: 13px;">admin_attempt@mail.com</span>
                    </td>
                    <td style="padding: 16px; color: var(--color-text); font-size: 14px;">
                        <strong style="color: var(--color-primary);">5 attempts</strong> in 5 mins
                    </td>
                    <td style="padding: 16px; text-align: center;">
                        <span style="background: rgba(30, 41, 59, 0.1); color: var(--color-primary); font-weight: 600; font-size: 12px; padding: 6px 12px; border-radius: 4px; border: 1px solid var(--color-primary);">Flagged</span>
                    </td>
                    <td style="padding: 16px; text-align: right;">
                        <button style="background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px;">Resolve</button>
                    </td>
                </tr>

                <!-- Row 2 -->
                <tr style="border-bottom: 1px solid #CBD5E1;">
                    <td style="padding: 16px; color: var(--color-text); font-size: 14px;">
                        <strong style="display: block; color: var(--color-primary); font-size: 14px;">Oct 10, 2026</strong>
                        08:12 AM
                    </td>
                    <td style="padding: 16px;">
                        <strong style="display: block; color: var(--color-primary); font-size: 15px; margin-bottom: 4px;">Sarah Jenkins</strong>
                        <span style="color: var(--color-text-muted); font-size: 13px;">sarah.j@example.com</span>
                    </td>
                    <td style="padding: 16px; color: var(--color-text); font-size: 14px;">
                        <strong style="color: var(--color-primary);">12 attempts</strong> in 1 hr
                    </td>
                    <td style="padding: 16px; text-align: center;">
                        <span style="background: rgba(30, 41, 59, 0.1); color: var(--color-primary); font-weight: 600; font-size: 12px; padding: 6px 12px; border-radius: 4px; border: 1px solid var(--color-primary);">Locked Out</span>
                    </td>
                    <td style="padding: 16px; text-align: right;">
                        <button style="background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px;">Resolve</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div style="padding: 16px 24px; border-top: 1px solid #CBD5E1; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa;">
        <span style="color: var(--color-text-muted); font-size: 13px;">Showing 2 of 2 entries</span>
    </div>
</section>
</main>
<?php require APPROOT . '/Views/shared/footer.php'; ?>