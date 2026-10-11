<?php require APPROOT . '/Views/shared/header.php'; ?>
<main class="main-content">
<header class="content-header" style="margin-bottom: 24px;">
    <div class="header-left">
        <h2 class="page-title" style="color: var(--color-primary);">Product Moderation</h2>
        <p class="page-subtitle" style="color: var(--color-text);">Review and manage pending product submissions.</p>
    </div>
    <div class="header-right" style="display: flex; gap: 12px;">
        <button class="btn" style="background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">
            Export List
        </button>
        <button class="btn" style="background: var(--color-primary); border: 1px solid var(--color-primary); color: #fff; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">
            Auto-Approve Batch
        </button>
    </div>
</header>

<!-- Stat Cards -->
<section style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
    <div class="dashboard-card" style="background: var(--color-primary); color: #fff; padding: 24px; border-radius: var(--radius-main); box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div style="font-size: 14px; font-weight: 600; opacity: 0.9; margin-bottom: 8px;">Pending Reviews <span style="background: rgba(255,255,255,0.15); color: #fff; padding: 2px 6px; border-radius: 4px; font-size: 11px; margin-left: 8px;">12%</span></div>
        <div style="font-size: 36px; font-weight: 700; margin-bottom: 4px;">1,432</div>
        <div style="font-size: 13px; opacity: 0.8;">Requires manual verification</div>
    </div>
    <div class="dashboard-card" style="background: #fff; border: 1px solid #CBD5E1; color: var(--color-primary); padding: 24px; border-radius: var(--radius-main); box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="font-size: 14px; font-weight: 600; margin-bottom: 8px;">Incomplete Data</div>
        <div style="font-size: 36px; font-weight: 700; margin-bottom: 4px;">215</div>
        <div style="font-size: 13px; color: var(--color-text-muted);">Missing critical attributes <a href="#" style="color: var(--color-primary); font-weight: 600; margin-left: 8px; text-decoration: underline;">View List</a></div>
    </div>
</section>

<!-- Filter Bar -->
<section class="dashboard-card" style="background: #fff; padding: 24px; border-radius: var(--radius-main); border: 1px solid #CBD5E1; margin-bottom: 24px;">
    <div style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
        <input type="text" placeholder="Search SKU or Name" style="flex: 1; min-width: 200px; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text);">
        <select style="padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text); background: #fff; appearance: auto;">
            <option value="all">All Categories</option>
            <option value="dairy">Dairy</option>
            <option value="bakery">Bakery</option>
            <option value="pantry">Pantry</option>
        </select>
        <div style="flex-grow: 1;"></div>
        <span style="font-size: 13px; font-weight: 600; color: var(--color-text-muted);">Sort by:</span>
        <select style="padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; color: var(--color-text); background: #fff; appearance: auto;">
            <option value="newest">Newest First</option>
            <option value="oldest">Oldest First</option>
        </select>
    </div>
</section>

<!-- Moderation Table -->
<section class="dashboard-card" style="background: #fff; border-radius: var(--radius-main); border: 1px solid #CBD5E1; overflow: hidden;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #CBD5E1; background: #f8f9fa;">
                    <th style="text-align: left; padding: 16px; color: var(--color-primary); font-weight: 600;">Product Details</th>
                    <th style="text-align: left; padding: 16px; color: var(--color-primary); font-weight: 600;">Category / Brand</th>
                    <th style="text-align: left; padding: 16px; color: var(--color-primary); font-weight: 600;">Status</th>
                    <th style="text-align: right; padding: 16px; color: var(--color-primary); font-weight: 600;">Actions</th>
                </tr>
            </thead>
            <tbody>
                
                <!-- Row 1 -->
                <tr style="border-bottom: 1px solid #CBD5E1;">
                    <td style="padding: 16px; display: flex; align-items: center; gap: 16px;">
                        <div style="width: 48px; height: 48px; background: #f1f5f9; border: 1px solid #CBD5E1; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: var(--color-text-muted);">[Img]</div>
                        <div>
                            <strong style="display: block; color: var(--color-primary); font-size: 15px; margin-bottom: 4px;">Organic Whole Milk</strong>
                            <span style="color: var(--color-text-muted); font-size: 13px;">SKU: MILK-ORG-1G</span>
                        </div>
                    </td>
                    <td style="padding: 16px;">
                        <strong style="display: block; color: var(--color-text); font-size: 14px; margin-bottom: 4px;">Dairy</strong>
                        <span style="color: var(--color-text-muted); font-size: 13px;">GreenHarvest Farms</span>
                    </td>
                    <td style="padding: 16px;">
                        <span style="background: rgba(30, 41, 59, 0.1); color: var(--color-primary); font-weight: 600; font-size: 12px; padding: 6px 10px; border-radius: 4px; border: 1px solid var(--color-primary);">Pending</span>
                    </td>
                    <td style="padding: 16px; text-align: right;">
                        <button class="btn" style="background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px; margin-right: 8px;">Reject</button>
                        <button class="btn" style="background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px;">Approve</button>
                    </td>
                </tr>

                <!-- Row 2 -->
                <tr style="border-bottom: 1px solid #CBD5E1;">
                    <td style="padding: 16px; display: flex; align-items: center; gap: 16px;">
                        <div style="width: 48px; height: 48px; background: #f1f5f9; border: 1px solid #CBD5E1; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: var(--color-text-muted);">[Img]</div>
                        <div>
                            <strong style="display: block; color: var(--color-primary); font-size: 15px; margin-bottom: 4px;">Almond Butter (Crunchy)</strong>
                            <span style="color: var(--color-text-muted); font-size: 13px;">SKU: NUT-ALM-CRU</span>
                        </div>
                    </td>
                    <td style="padding: 16px;">
                        <strong style="display: block; color: var(--color-text); font-size: 14px; margin-bottom: 4px;">Pantry</strong>
                        <span style="color: var(--color-text-muted); font-size: 13px;">NutriNaturals</span>
                    </td>
                    <td style="padding: 16px;">
                        <span style="background: rgba(30, 41, 59, 0.1); color: var(--color-primary); font-weight: 600; font-size: 12px; padding: 6px 10px; border-radius: 4px; border: 1px solid var(--color-primary);">Pending</span>
                    </td>
                    <td style="padding: 16px; text-align: right;">
                        <button class="btn" style="background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px; margin-right: 8px;">Reject</button>
                        <button class="btn" style="background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px;">Approve</button>
                    </td>
                </tr>

                <!-- Row 3 -->
                <tr>
                    <td style="padding: 16px; display: flex; align-items: center; gap: 16px;">
                        <div style="width: 48px; height: 48px; background: #f1f5f9; border: 1px solid #CBD5E1; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: var(--color-text-muted);">[Img]</div>
                        <div>
                            <strong style="display: block; color: var(--color-primary); font-size: 15px; margin-bottom: 4px;">Premium Coffee Beans</strong>
                            <span style="color: var(--color-text-muted); font-size: 13px;">SKU: COF-PRM-1KG</span>
                        </div>
                    </td>
                    <td style="padding: 16px;">
                        <strong style="display: block; color: var(--color-text); font-size: 14px; margin-bottom: 4px;">Beverages</strong>
                        <span style="color: var(--color-text-muted); font-size: 13px;">Morning Roast Co.</span>
                    </td>
                    <td style="padding: 16px;">
                        <span style="background: rgba(30, 41, 59, 0.1); color: var(--color-primary); font-weight: 600; font-size: 12px; padding: 6px 10px; border-radius: 4px; border: 1px solid var(--color-primary);">Pending</span>
                    </td>
                    <td style="padding: 16px; text-align: right;">
                        <button class="btn" style="background: transparent; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px; margin-right: 8px;">Reject</button>
                        <button class="btn" style="background: var(--color-success); border: 1px solid var(--color-success); color: #fff; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px;">Approve</button>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
    
    <div style="padding: 16px 24px; border-top: 1px solid #CBD5E1; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa;">
        <span style="color: var(--color-text-muted); font-size: 13px;">Showing 3 of 1,432 entries</span>
        <div style="display: flex; gap: 4px;">
            <button style="background: var(--color-primary); border: 1px solid var(--color-primary); color: #fff; padding: 6px 12px; border-radius: 4px; font-size: 13px; font-weight: 600; cursor: pointer;">1</button>
            <button style="background: transparent; border: 1px solid #CBD5E1; color: var(--color-primary); padding: 6px 12px; border-radius: 4px; font-size: 13px; font-weight: 600; cursor: pointer;">2</button>
            <button style="background: transparent; border: 1px solid #CBD5E1; color: var(--color-primary); padding: 6px 12px; border-radius: 4px; font-size: 13px; font-weight: 600; cursor: pointer;">3</button>
        </div>
    </div>
</section>
</main>
<?php require APPROOT . '/Views/shared/footer.php'; ?>
