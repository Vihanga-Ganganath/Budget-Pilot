/**
 * Budget Pilot — Admin Console
 * Shared sidebar/topbar renderer + demo data store + button/graph interactivity.
 * Pure vanilla JS, no external libraries (per project constraints).
 * All data below is DUMMY / DEMO data persisted in localStorage so the
 * console feels alive without a backend.
 */

/* ============================================================
   1. SHARED SIDEBAR + TOPBAR (guarantees identical design everywhere)
   ============================================================ */

const ADMIN_NAV_ITEMS = [
    { key: 'dashboard',  href: '/BudgetPilot/admin/dashboard',          label: 'Dashboard',
      icon: '<rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect>' },
    { key: 'users',      href: '/BudgetPilot/admin/users',    label: 'User Management',
      icon: '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>' },
    { key: 'suppliers',  href: '/BudgetPilot/admin/suppliers', label: 'Supplier Approvals',
      icon: '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line>' },
    { key: 'products',   href: '/BudgetPilot/admin/products', label: 'Product Moderation',
      icon: '<path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line>' },
    { key: 'security',   href: '/BudgetPilot/admin/security',     label: 'Security &amp; Audit',
      icon: '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>' },
    { key: 'reports',    href: '/BudgetPilot/admin/reports',             label: 'Reports',
      icon: '<line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line>' }
];




/* ============================================================
   2. DEMO DATA STORE (localStorage-backed)
   ============================================================ */

const STORE_KEY = 'bp_admin_demo_v2';

function defaultAdminData() {
    return {
        users: [
            { id: 1, name: 'Alex Perera', email: 'alex.p@example.com', role: 'CUSTOMER', status: 'ACTIVE', lastLogin: '2 hours ago', initial: 'A', color: '#059669', bg: '#D1FAE5' },
            { id: 2, name: 'GreenHarvest Foods', email: 'vendor@greenharvest.co', role: 'SUPPLIER', status: 'ACTIVE', lastLogin: 'Yesterday', initial: 'G', color: '#2563EB', bg: '#DBEAFE' },
            { id: 3, name: 'Sarah Perera', email: 'sarah.admin@budgetpilot.com', role: 'ADMIN', status: 'ACTIVE', lastLogin: 'Just now', initial: 'S', color: '#3730A3', bg: '#E0E7FF' },
            { id: 4, name: 'Marcus Vance', email: 'm.vance@unknown.net', role: 'CUSTOMER', status: 'LOCKED', lastLogin: 'Oct 12, 2025', initial: 'M', color: '#DC2626', bg: '#FEE2E2' },
            { id: 5, name: 'Priya Fernando', email: 'priya.f@example.com', role: 'CUSTOMER', status: 'ACTIVE', lastLogin: '3 days ago', initial: 'P', color: '#059669', bg: '#D1FAE5' },
            { id: 6, name: 'Nimal Dairy Co.', email: 'contact@nimaldairy.lk', role: 'SUPPLIER', status: 'SUSPENDED', lastLogin: '2 weeks ago', initial: 'N', color: '#B45309', bg: '#FEF3C7' },
            { id: 7, name: 'Dilki Silva', email: 'dilki.s@budgetpilot.com', role: 'ADMIN', status: 'ACTIVE', lastLogin: '1 day ago', initial: 'D', color: '#3730A3', bg: '#E0E7FF' },
            { id: 8, name: 'Kasun Jayawardena', email: 'kasun.j@example.com', role: 'CUSTOMER', status: 'ACTIVE', lastLogin: '5 hours ago', initial: 'K', color: '#059669', bg: '#D1FAE5' },
            { id: 9, name: 'FreshMart Lanka', email: 'orders@freshmart.lk', role: 'SUPPLIER', status: 'ACTIVE', lastLogin: '6 hours ago', initial: 'F', color: '#2563EB', bg: '#DBEAFE' },
            { id: 10, name: 'Unknown User', email: 'temp0234@mailinator.com', role: 'CUSTOMER', status: 'LOCKED', lastLogin: 'Sep 30, 2025', initial: 'U', color: '#DC2626', bg: '#FEE2E2' },
            { id: 11, name: 'Ishara Bandara', email: 'ishara.b@example.com', role: 'CUSTOMER', status: 'ACTIVE', lastLogin: '1 hour ago', initial: 'I', color: '#059669', bg: '#D1FAE5' },
            { id: 12, name: 'CityFresh Grocers', email: 'hello@cityfresh.lk', role: 'SUPPLIER', status: 'SUSPENDED', lastLogin: '1 month ago', initial: 'C', color: '#B45309', bg: '#FEF3C7' }
        ],
        totalUsersCount: 12458,
        activeUsersCount: 11204,
        suspendedUsersCount: 843,
        lockedUsersCount: 411,

        pendingSuppliers: [
            { id: 1, name: 'GreenLeaf Grocers', category: 'Fresh Produce Distributor', docs: ['📄 Business Reg', '🧾 Tax Cert'] },
            { id: 2, name: 'PureDairy Ltd.', category: 'Dairy & Cold Chain', docs: ['📄 Business Reg', '🏥 Health Safety Cert'] }
        ],
        supplierDecisions: [
            { name: 'Apex Manufacturing', category: 'Industrial', status: 'APPROVED', reviewedBy: 'S. Jenkins', date: 'Oct 24, 2025' },
            { name: 'Global Logistics Co.', category: 'Transport', status: 'APPROVED', reviewedBy: 'M. Rossi', date: 'Oct 23, 2025' },
            { name: 'TechNova Solutions', category: 'IT Services', status: 'REJECTED', reviewedBy: 'S. Jenkins', date: 'Oct 22, 2025' }
        ],
        pendingCount: 24,
        verifiedThisWeek: 156,
        rejectedCount: 8,

        products: [
            { id: 1, name: 'Organic Whole Milk 1L', sku: 'MLK-ORG-100', category: 'Dairy', brand: "Nature's Best", flag: 'duplicate', flagLabel: '⧉ DUPLICATE SKU', pillClass: 'pill-blue' },
            { id: 2, name: 'Artisan Sourdough Loaf', sku: 'BAK-SD-056', category: 'Bakery', brand: 'Local Ovens Inc.', flag: 'incomplete', flagLabel: '⚠ INCOMPLETE INFO', pillClass: 'pill-red' },
            { id: 3, name: 'Premium Grade A Eggs 12pk', sku: 'DAE-EGG-012', category: 'Dairy', brand: 'Farm Fresh', flag: 'pending', flagLabel: '◷ PENDING REVIEW', pillClass: 'pill-amber' },
            { id: 4, name: 'Cold Pressed Olive Oil 500ml', sku: 'OIL-OLV-204', category: 'Pantry', brand: 'MediterraCo', flag: 'pending', flagLabel: '◷ PENDING REVIEW', pillClass: 'pill-amber' },
            { id: 5, name: 'Free Range Chicken Breast 1kg', sku: 'MET-CHK-311', category: 'Meat', brand: 'FarmFresh Poultry', flag: 'duplicate', flagLabel: '⧉ DUPLICATE SKU', pillClass: 'pill-blue' }
        ],
        pendingReviews: 1432,
        flaggedDuplicates: 84,
        incompleteData: 215,

        auditLog: [
            { time: '2025-10-27 14:32:01', admin: 'Sarah Jenkins', actionType: 'policy', action: '✎ Modified Policy', target: 'Supplier Agreement v2', ip: '192.168.1.105', flagged: false },
            { time: '2025-10-27 13:15:44', admin: 'System Auto', actionType: 'system', action: '✓ Backup Complete', target: 'DB_Cluster_A', ip: 'Internal', flagged: false },
            { time: '2025-10-27 12:05:11', admin: 'Unknown', actionType: 'login', action: '✕ Auth Failed', target: 'j.doe@admin…', ip: '192.168.1.45', flagged: true },
            { time: '2025-10-27 10:42:19', admin: 'Marcus Chen', actionType: 'data', action: '👁 Viewed Report', target: 'Q3 Financials', ip: '10.8.0.52', flagged: false },
            { time: '2025-10-27 09:30:00', admin: 'Elena Rodriguez', actionType: 'data', action: '＋ User Created', target: 'SupplierID: 8492', ip: '192.168.1.112', flagged: false },
            { time: '2025-10-27 08:15:22', admin: 'System Auto', actionType: 'system', action: '✓ Patch Applied', target: 'Core Services v1.2', ip: 'Internal', flagged: false }
        ],
        suspiciousLogins: [
            { id: 1, email: 'j.doe@admin.budgetpilot', ip: '192.168.1.45', time: '2 mins ago', risk: 'high', blocked: false },
            { id: 2, email: 'sys.admin@budgetpilot', ip: '45.22.11.90', time: '1 hr ago', risk: 'medium', blocked: false },
            { id: 3, email: 'a.smith@admin.budgetpilot', ip: '104.31.11.12', time: '3 hrs ago', risk: 'medium', blocked: false }
        ],
        lockdownActive: false,

        reportPeriods: {
            '7':  { transactions: '28,104', budget: '$980k',  avgTx: '$32.10', suppliers: '780', txTrend: '+3.1%', budgetTrend: '+2.0%', avgTrend: '-0.8%', supplierTrend: '+1.2%',
                    categories: [ ['Grocery', '$280k', 100], ['Utilities', '$190k', 68], ['Transport', '$140k', 50], ['Dining Out', '$95k', 34], ['Entertainment', '$60k', 21] ] },
            '30': { transactions: '124,592', budget: '$4.2M', avgTx: '$34.50', suppliers: '842', txTrend: '+12.5%', budgetTrend: '+8.2%', avgTrend: '-1.4%', supplierTrend: '+5.1%',
                    categories: [ ['Grocery', '$1.2M', 100], ['Utilities', '$850k', 71], ['Transport', '$620k', 52], ['Dining Out', '$410k', 34], ['Entertainment', '$280k', 23] ] },
            '90': { transactions: '358,220', budget: '$11.8M', avgTx: '$33.90', suppliers: '905', txTrend: '+18.9%', budgetTrend: '+14.6%', avgTrend: '+0.5%', supplierTrend: '+9.7%',
                    categories: [ ['Grocery', '$3.4M', 100], ['Utilities', '$2.3M', 68], ['Transport', '$1.7M', 50], ['Dining Out', '$1.1M', 32], ['Entertainment', '$780k', 23] ] }
        },

        notifications: [
            { title: 'Security Alert', message: '3 failed admin login attempts detected.', time: '5 min ago', type: 'critical' },
            { title: 'Supplier Approved', message: "You approved 'GreenLeaf Grocers'.", time: '1 hr ago', type: 'success' },
            { title: 'New Product Flagged', message: "'Organic Whole Milk 1L' flagged as duplicate SKU.", time: '2 hr ago', type: 'warning' },
            { title: 'Weekly Report Ready', message: 'Your Oct 20–27 platform report is ready to view.', time: '1 day ago', type: 'info' }
        ],

        profile: {
            fullName: 'Sarah Jenkins',
            email: 'sarah.jenkins@budgetpilot.com',
            emailNotifications: true,
            systemAlerts: false,
            darkMode: false,
            twoFactorEnabled: true
        }
    };
}

function getAdminData() {
    try {
        const raw = localStorage.getItem(STORE_KEY);
        if (!raw) throw new Error('no data');
        return JSON.parse(raw);
    } catch (e) {
        const fresh = defaultAdminData();
        localStorage.setItem(STORE_KEY, JSON.stringify(fresh));
        return fresh;
    }
}

function saveAdminData(data) {
    localStorage.setItem(STORE_KEY, JSON.stringify(data));
}

function resetAdminData() {
    localStorage.removeItem(STORE_KEY);
    toast('Demo data has been reset.', 'success');
    setTimeout(() => location.reload(), 700);
}

/* ============================================================
   3. TOAST + DROPDOWN UTILITIES
   ============================================================ */

function toast(message, type = 'success') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }
    const el = document.createElement('div');
    el.className = 'top-toast' + (type === 'error' ? ' error' : '');
    el.innerHTML = `
        <div class="toast-content">
            <span>${type === 'error' ? '⚠️' : '✅'}</span>
            <span>${message}</span>
        </div>
        <button class="toast-close" aria-label="Close">&times;</button>`;
    el.querySelector('.toast-close').addEventListener('click', () => el.remove());
    container.appendChild(el);
    setTimeout(() => el.remove(), 4000);
}

function closeAllDropdowns(except) {
    document.querySelectorAll('.dropdown-panel').forEach(panel => {
        if (panel !== except) panel.style.display = 'none';
    });
}

function toggleDropdown(panel, html) {
    const isOpen = panel.style.display === 'block';
    closeAllDropdowns(panel);
    if (isOpen) {
        panel.style.display = 'none';
    } else {
        if (html !== undefined) panel.innerHTML = html;
        panel.style.display = 'block';
    }
}

document.addEventListener('click', (e) => {
    if (!e.target.closest('.dropdown-panel') && !e.target.closest('.icon-btn-dash') && !e.target.closest('#avatar-btn') && !e.target.closest('.row-actions-btn') && !e.target.closest('.activity-menu-btn')) {
        closeAllDropdowns();
    }
});



/* ============================================================
   5. CSV / FILE DOWNLOAD HELPER
   ============================================================ */

function downloadFile(filename, content, mime = 'text/csv') {
    const blob = new Blob([content], { type: mime });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
}

function arrayToCsv(rows) {
    return rows.map(row => row.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(',')).join('\n');
}

/* ============================================================
   6. PAGE: DASHBOARD
   ============================================================ */

function initDashboardPage() {
    const data = getAdminData();

    // Chart period toggle (1M / 1Y) — swaps between two pre-drawn datasets
    const chip1M = document.getElementById('chip-1m');
    const chip1Y = document.getElementById('chip-1y');
    const chart1M = document.getElementById('chart-line-1m');
    const chart1Y = document.getElementById('chart-line-1y');
    function showPeriod(period) {
        if (!chart1M || !chart1Y) return;
        chart1M.style.display = period === '1m' ? 'block' : 'none';
        chart1Y.style.display = period === '1y' ? 'block' : 'none';
        if (chip1M) chip1M.classList.toggle('active', period === '1m');
        if (chip1Y) chip1Y.classList.toggle('active', period === '1y');
    }
    if (chip1M) chip1M.addEventListener('click', () => showPeriod('1m'));
    if (chip1Y) chip1Y.addEventListener('click', () => showPeriod('1y'));

    // Export System Report
    const exportBtn = document.getElementById('export-report-btn');
    if (exportBtn) {
        exportBtn.addEventListener('click', () => {
            const rows = [['Metric', 'Value'],
                ['Total Users', data.totalUsersCount],
                ['Active Suppliers', 132],
                ['Pending Approvals', data.pendingCount],
                ['Security Alerts', data.suspiciousLogins.length]];
            downloadFile('budget-pilot-system-report.csv', arrayToCsv(rows));
            toast('System report exported.');
        });
    }

    // Recent activity ⋮ menus
    document.querySelectorAll('.activity-row').forEach(row => {
        const btn = row.querySelector('.activity-menu-btn');
        if (!btn) return;
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const panel = row.querySelector('.dropdown-panel') || (() => {
                const p = document.createElement('div');
                p.className = 'dropdown-panel dropdown-panel-inline';
                row.style.position = 'relative';
                row.appendChild(p);
                return p;
            })();
            toggleDropdown(panel, `
                <div class="dropdown-item"><a href="#" class="act-view">View Details</a></div>
                <div class="dropdown-item"><a href="#" class="act-dismiss">Dismiss</a></div>`);
            setTimeout(() => {
                const viewLink = panel.querySelector('.act-view');
                const dismissLink = panel.querySelector('.act-dismiss');
                if (viewLink) viewLink.addEventListener('click', (ev) => { ev.preventDefault(); toast('Opening activity details…'); closeAllDropdowns(); });
                if (dismissLink) dismissLink.addEventListener('click', (ev) => {
                    ev.preventDefault();
                    row.style.opacity = '0';
                    setTimeout(() => row.remove(), 200);
                    toast('Activity dismissed.');
                });
            }, 0);
        });
    });
}

/* ============================================================
   7. PAGE: USER MANAGEMENT
   ============================================================ */

let userMgmtState = { page: 1, pageSize: 6, filterRole: 'all', filterStatus: 'all', search: '' };

function initUserManagementPage() {
    renderUserStats();
    renderUserTable();

    const searchInput = document.getElementById('user-search-input');
    if (searchInput) searchInput.addEventListener('input', () => {
        userMgmtState.search = searchInput.value.toLowerCase();
        userMgmtState.page = 1;
        renderUserTable();
    });

    const roleFilter = document.getElementById('role-filter');
    if (roleFilter) roleFilter.addEventListener('change', () => {
        userMgmtState.filterRole = roleFilter.value;
        userMgmtState.page = 1;
        renderUserTable();
    });

    const statusFilter = document.getElementById('status-filter');
    if (statusFilter) statusFilter.addEventListener('change', () => {
        userMgmtState.filterStatus = statusFilter.value;
        userMgmtState.page = 1;
        renderUserTable();
    });

    const moreFiltersBtn = document.getElementById('more-filters-btn');
    if (moreFiltersBtn) moreFiltersBtn.addEventListener('click', () => {
        toast('Showing all available filters.');
    });

    const selectAll = document.getElementById('select-all-checkbox');
    if (selectAll) selectAll.addEventListener('change', () => {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = selectAll.checked);
    });

    // Add Admin User modal
    const addForm = document.getElementById('add-admin-form');
    if (addForm) addForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const data = getAdminData();
        const name = addForm.full_name.value.trim();
        const email = addForm.email.value.trim();
        if (!name || !email) return;
        const newUser = {
            id: Date.now(),
            name, email,
            role: 'ADMIN', status: 'ACTIVE', lastLogin: 'Just now',
            initial: name.charAt(0).toUpperCase(), color: '#3730A3', bg: '#E0E7FF'
        };
        data.users.unshift(newUser);
        data.totalUsersCount += 1;
        data.activeUsersCount += 1;
        saveAdminData(data);
        addForm.reset();
        document.getElementById('add-admin-modal').style.display = 'none';
        renderUserStats();
        renderUserTable();
        toast(`${name} was added as an Admin.`);
    });
}

function getFilteredUsers() {
    const data = getAdminData();
    return data.users.filter(u => {
        if (userMgmtState.filterRole !== 'all' && u.role.toLowerCase() !== userMgmtState.filterRole) return false;
        if (userMgmtState.filterStatus !== 'all' && u.status.toLowerCase() !== userMgmtState.filterStatus) return false;
        if (userMgmtState.search && !(u.name.toLowerCase().includes(userMgmtState.search) || u.email.toLowerCase().includes(userMgmtState.search))) return false;
        return true;
    });
}

function renderUserStats() {
    const data = getAdminData();
    const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
    set('stat-total-users', data.totalUsersCount.toLocaleString());
    set('stat-active-users', data.activeUsersCount.toLocaleString());
    set('stat-suspended-users', data.suspendedUsersCount.toLocaleString());
    set('stat-locked-users', data.lockedUsersCount.toLocaleString());
}

const STATUS_PILL_CLASS = { ACTIVE: 'pill-green', SUSPENDED: 'pill-amber', LOCKED: 'pill-red' };
const ROLE_PILL_CLASS = { CUSTOMER: 'pill-gray', SUPPLIER: 'pill-blue', ADMIN: 'pill-navy' };

function renderUserTable() {
    const tbody = document.getElementById('user-table-body');
    if (!tbody) return;
    const filtered = getFilteredUsers();
    const totalPages = Math.max(1, Math.ceil(filtered.length / userMgmtState.pageSize));
    userMgmtState.page = Math.min(userMgmtState.page, totalPages);
    const start = (userMgmtState.page - 1) * userMgmtState.pageSize;
    const pageItems = filtered.slice(start, start + userMgmtState.pageSize);

    tbody.innerHTML = pageItems.map(u => `
        <tr class="${u.status === 'LOCKED' ? 'flagged-row' : ''}" data-user-id="${u.id}">
            <td><input type="checkbox" class="admin-checkbox row-checkbox"></td>
            <td>
                <div class="cell-user">
                    <div class="cell-avatar" style="background:${u.bg};color:${u.color};">${u.initial}</div>
                    <div>
                        <div class="cell-name">${u.name}</div>
                        <div class="cell-sub">${u.email}</div>
                    </div>
                </div>
            </td>
            <td><span class="pill ${ROLE_PILL_CLASS[u.role] || 'pill-gray'}">${u.role}</span></td>
            <td><span class="pill ${STATUS_PILL_CLASS[u.status] || 'pill-gray'}"><span class="pill-dot"></span>${u.status}</span></td>
            <td>${u.lastLogin}</td>
            <td style="position:relative;"><button class="row-actions-btn" data-user-id="${u.id}">⋮</button></td>
        </tr>`).join('') || `<tr><td colspan="6" style="text-align:center;color:var(--text-light);padding:32px;">No users match your filters.</td></tr>`;

    const info = document.getElementById('table-footer-info');
    if (info) info.textContent = filtered.length
        ? `Showing ${start + 1} to ${Math.min(start + userMgmtState.pageSize, filtered.length)} of ${filtered.length} entries`
        : 'No entries found';

    renderPagination(document.getElementById('pagination-container'), userMgmtState.page, totalPages, (p) => {
        userMgmtState.page = p;
        renderUserTable();
    });

    tbody.querySelectorAll('.row-actions-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const userId = Number(btn.getAttribute('data-user-id'));
            const cell = btn.parentElement;
            const data = getAdminData();
            const user = data.users.find(u => u.id === userId);
            if (!user) return;
            const panel = document.createElement('div');
            panel.className = 'dropdown-panel dropdown-panel-right';
            cell.appendChild(panel);
            toggleDropdown(panel, `
                <div class="dropdown-item"><a href="#" class="act-toggle-lock">${user.status === 'LOCKED' ? 'Unlock Account' : 'Lock Account'}</a></div>
                <div class="dropdown-item"><a href="#" class="act-toggle-suspend">${user.status === 'SUSPENDED' ? 'Reactivate' : 'Suspend Account'}</a></div>
                <div class="dropdown-item"><a href="#" class="act-delete" style="color:#DC2626;">Delete User</a></div>`);
            setTimeout(() => {
                const lockLink = panel.querySelector('.act-toggle-lock');
                const suspendLink = panel.querySelector('.act-toggle-suspend');
                const deleteLink = panel.querySelector('.act-delete');
                if (lockLink) lockLink.addEventListener('click', (ev) => {
                    ev.preventDefault();
                    user.status = user.status === 'LOCKED' ? 'ACTIVE' : 'LOCKED';
                    saveAdminData(data);
                    renderUserTable();
                    toast(`${user.name} is now ${user.status.toLowerCase()}.`);
                });
                if (suspendLink) suspendLink.addEventListener('click', (ev) => {
                    ev.preventDefault();
                    user.status = user.status === 'SUSPENDED' ? 'ACTIVE' : 'SUSPENDED';
                    saveAdminData(data);
                    renderUserTable();
                    toast(`${user.name} is now ${user.status.toLowerCase()}.`);
                });
                if (deleteLink) deleteLink.addEventListener('click', (ev) => {
                    ev.preventDefault();
                    if (!confirm(`Remove ${user.name} from the platform?`)) return;
                    data.users = data.users.filter(u => u.id !== userId);
                    data.totalUsersCount = Math.max(0, data.totalUsersCount - 1);
                    saveAdminData(data);
                    renderUserStats();
                    renderUserTable();
                    toast(`${user.name} was removed.`);
                });
            }, 0);
        });
    });
}

function renderPagination(container, current, totalPages, onChange) {
    if (!container) return;
    let html = `<button class="page-btn" data-p="${current - 1}" ${current === 1 ? 'disabled' : ''}>‹</button>`;
    for (let p = 1; p <= totalPages; p++) {
        if (totalPages > 6 && p > 2 && p < totalPages - 1 && Math.abs(p - current) > 1) {
            if (p === 3 || p === totalPages - 2) html += `<span style="color:var(--text-light); padding:0 4px;">…</span>`;
            continue;
        }
        html += `<button class="page-btn ${p === current ? 'active' : ''}" data-p="${p}">${p}</button>`;
    }
    html += `<button class="page-btn" data-p="${current + 1}" ${current === totalPages ? 'disabled' : ''}>›</button>`;
    container.innerHTML = html;
    container.querySelectorAll('.page-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const p = Number(btn.getAttribute('data-p'));
            if (p >= 1 && p <= totalPages) onChange(p);
        });
    });
}

/* ============================================================
   8. PAGE: SUPPLIER APPROVALS
   ============================================================ */

function initSupplierApprovalsPage() {
    renderSupplierStats();
    renderPendingSuppliers();
    renderSupplierDecisions();

    const viewAllLink = document.getElementById('view-all-suppliers-link');
    if (viewAllLink) viewAllLink.addEventListener('click', (e) => {
        e.preventDefault();
        toast('Showing all suppliers awaiting verification.');
    });
}

function renderSupplierStats() {
    const data = getAdminData();
    const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
    set('stat-pending-verification', data.pendingCount);
    set('stat-verified-week', data.verifiedThisWeek);
    set('stat-rejected', data.rejectedCount);
}

function renderPendingSuppliers() {
    const data = getAdminData();
    const container = document.getElementById('pending-suppliers-container');
    if (!container) return;
    if (!data.pendingSuppliers.length) {
        container.innerHTML = `<p style="color:var(--text-light);">No suppliers awaiting verification. 🎉</p>`;
        return;
    }
    container.innerHTML = data.pendingSuppliers.map(s => `
        <div class="approval-card" data-supplier-id="${s.id}">
            <div class="approval-card-top">
                <div class="approval-supplier-info">
                    <div class="approval-supplier-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                    </div>
                    <div>
                        <div class="approval-supplier-name">${s.name}</div>
                        <div class="approval-supplier-cat">${s.category}</div>
                    </div>
                </div>
                <span class="pill pill-amber">PENDING</span>
            </div>
            <div class="approval-doc-label">Submitted Documents</div>
            <div class="approval-doc-badges">
                ${s.docs.map(d => `<span class="doc-badge">${d}</span>`).join('')}
            </div>
            <div class="approval-actions">
                <button class="btn btn-approve-green btn-sm act-approve">Approve</button>
                <button class="btn btn-reject btn-sm act-reject">Reject</button>
            </div>
        </div>`).join('');

    container.querySelectorAll('.approval-card').forEach(card => {
        const id = Number(card.getAttribute('data-supplier-id'));
        card.querySelector('.act-approve').addEventListener('click', () => decideSupplier(id, 'APPROVED'));
        card.querySelector('.act-reject').addEventListener('click', () => decideSupplier(id, 'REJECTED'));
    });
}

function decideSupplier(id, decision) {
    const data = getAdminData();
    const idx = data.pendingSuppliers.findIndex(s => s.id === id);
    if (idx === -1) return;
    const supplier = data.pendingSuppliers[idx];
    data.pendingSuppliers.splice(idx, 1);
    data.supplierDecisions.unshift({
        name: supplier.name, category: supplier.category.split(' ')[0], status: decision,
        reviewedBy: 'You', date: new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
    });
    data.pendingCount = Math.max(0, data.pendingCount - 1);
    if (decision === 'APPROVED') data.verifiedThisWeek += 1; else data.rejectedCount += 1;
    saveAdminData(data);
    renderSupplierStats();
    renderPendingSuppliers();
    renderSupplierDecisions();
    toast(`${supplier.name} was ${decision.toLowerCase()}.`);
}

function renderSupplierDecisions() {
    const data = getAdminData();
    const tbody = document.getElementById('decisions-table-body');
    if (!tbody) return;
    tbody.innerHTML = data.supplierDecisions.slice(0, 8).map(d => `
        <tr>
            <td class="cell-name">${d.name}</td>
            <td>${d.category}</td>
            <td><span class="pill ${d.status === 'APPROVED' ? 'pill-green' : 'pill-red'}">${d.status === 'APPROVED' ? '✓' : '✕'} ${d.status}</span></td>
            <td>${d.reviewedBy}</td>
            <td>${d.date}</td>
        </tr>`).join('');
}

/* ============================================================
   9. PAGE: PRODUCT MODERATION
   ============================================================ */

let productModState = { search: '', category: 'all', issue: 'all', sort: 'newest' };

function initProductModerationPage() {
    renderModerationStats();
    renderModerationTable();

    const searchInput = document.getElementById('product-search-input');
    if (searchInput) searchInput.addEventListener('input', () => { productModState.search = searchInput.value.toLowerCase(); renderModerationTable(); });

    const categoryFilter = document.getElementById('category-filter');
    if (categoryFilter) categoryFilter.addEventListener('change', () => { productModState.category = categoryFilter.value; renderModerationTable(); });

    const issueFilter = document.getElementById('issue-filter');
    if (issueFilter) issueFilter.addEventListener('change', () => { productModState.issue = issueFilter.value; renderModerationTable(); });

    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) sortSelect.addEventListener('change', () => { productModState.sort = sortSelect.value; renderModerationTable(); });

    const reviewDuplicatesLink = document.getElementById('review-duplicates-link');
    if (reviewDuplicatesLink) reviewDuplicatesLink.addEventListener('click', (e) => {
        e.preventDefault();
        const issueSelect = document.getElementById('issue-filter');
        if (issueSelect) { issueSelect.value = 'duplicate'; productModState.issue = 'duplicate'; renderModerationTable(); }
        toast('Filtered to duplicate SKU issues.');
    });

    const reviewIncompleteLink = document.getElementById('review-incomplete-link');
    if (reviewIncompleteLink) reviewIncompleteLink.addEventListener('click', (e) => {
        e.preventDefault();
        const issueSelect = document.getElementById('issue-filter');
        if (issueSelect) { issueSelect.value = 'incomplete'; productModState.issue = 'incomplete'; renderModerationTable(); }
        toast('Filtered to incomplete data issues.');
    });

    const selectAll = document.getElementById('select-all-products');
    if (selectAll) selectAll.addEventListener('change', () => {
        document.querySelectorAll('.product-row-checkbox').forEach(cb => cb.checked = selectAll.checked);
    });

    const exportBtn = document.getElementById('export-list-btn');
    if (exportBtn) exportBtn.addEventListener('click', () => {
        const data = getAdminData();
        const rows = [['Product', 'SKU', 'Category', 'Brand', 'Flag'], ...data.products.map(p => [p.name, p.sku, p.category, p.brand, p.flag])];
        downloadFile('flagged-products.csv', arrayToCsv(rows));
        toast('Product list exported.');
    });

    const autoApproveBtn = document.getElementById('auto-approve-btn');
    if (autoApproveBtn) autoApproveBtn.addEventListener('click', () => {
        const data = getAdminData();
        const before = data.products.length;
        data.products = data.products.filter(p => p.flag !== 'pending');
        const approvedCount = before - data.products.length;
        data.pendingReviews = Math.max(0, data.pendingReviews - approvedCount);
        saveAdminData(data);
        renderModerationStats();
        renderModerationTable();
        toast(`Auto-approved ${approvedCount} pending review${approvedCount === 1 ? '' : 's'}.`);
    });
}

function renderModerationStats() {
    const data = getAdminData();
    const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
    set('stat-pending-reviews', data.pendingReviews.toLocaleString());
    set('stat-flagged-duplicates', data.flaggedDuplicates);
    set('stat-incomplete-data', data.incompleteData);
}

function renderModerationTable() {
    const data = getAdminData();
    const tbody = document.getElementById('moderation-table-body');
    if (!tbody) return;

    let items = data.products.filter(p => {
        if (productModState.category !== 'all' && p.category.toLowerCase() !== productModState.category) return false;
        if (productModState.issue !== 'all' && p.flag !== productModState.issue) return false;
        if (productModState.search && !(p.name.toLowerCase().includes(productModState.search) || p.sku.toLowerCase().includes(productModState.search))) return false;
        return true;
    });
    items = items.slice().sort((a, b) => productModState.sort === 'newest' ? b.id - a.id : a.id - b.id);

    tbody.innerHTML = items.map(p => `
        <tr class="${p.flag !== 'pending' ? 'flagged-row' : ''}" data-product-id="${p.id}">
            <td><input type="checkbox" class="admin-checkbox product-row-checkbox"></td>
            <td>
                <div class="cell-user">
                    <div class="cell-thumb" style="display:flex;align-items:center;justify-content:center;background:var(--input-bg);color:var(--text-light);font-size:0.7rem;">IMG</div>
                    <div>
                        <div class="cell-name">${p.name}</div>
                        <div class="cell-sub">SKU: ${p.sku}</div>
                    </div>
                </div>
            </td>
            <td>${p.category}<div class="cell-sub">${p.brand}</div></td>
            <td><span class="pill ${p.pillClass}">${p.flagLabel}</span></td>
            <td style="position:relative;"><button class="row-actions-btn" data-product-id="${p.id}">⋮</button></td>
        </tr>`).join('') || `<tr><td colspan="5" style="text-align:center;color:var(--text-light);padding:32px;">No products match your filters.</td></tr>`;

    const info = document.getElementById('mod-table-footer-info');
    if (info) info.textContent = `Showing 1 to ${items.length} of ${data.pendingReviews.toLocaleString()} entries`;

    tbody.querySelectorAll('.row-actions-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const productId = Number(btn.getAttribute('data-product-id'));
            const cell = btn.parentElement;
            const panel = document.createElement('div');
            panel.className = 'dropdown-panel dropdown-panel-right';
            cell.appendChild(panel);
            toggleDropdown(panel, `
                <div class="dropdown-item"><a href="#" class="act-approve-product">Approve</a></div>
                <div class="dropdown-item"><a href="#" class="act-reject-product" style="color:#DC2626;">Reject</a></div>`);
            setTimeout(() => {
                panel.querySelector('.act-approve-product').addEventListener('click', (ev) => { ev.preventDefault(); resolveProduct(productId, 'approved'); });
                panel.querySelector('.act-reject-product').addEventListener('click', (ev) => { ev.preventDefault(); resolveProduct(productId, 'rejected'); });
            }, 0);
        });
    });
}

function resolveProduct(productId, decision) {
    const data = getAdminData();
    const idx = data.products.findIndex(p => p.id === productId);
    if (idx === -1) return;
    const product = data.products[idx];
    data.products.splice(idx, 1);
    data.pendingReviews = Math.max(0, data.pendingReviews - 1);
    if (product.flag === 'duplicate') data.flaggedDuplicates = Math.max(0, data.flaggedDuplicates - 1);
    if (product.flag === 'incomplete') data.incompleteData = Math.max(0, data.incompleteData - 1);
    saveAdminData(data);
    renderModerationStats();
    renderModerationTable();
    toast(`${product.name} was ${decision}.`);
}

/* ============================================================
   10. PAGE: SECURITY & AUDIT
   ============================================================ */

function initSecurityAuditPage() {
    renderSuspiciousLogins();
    renderAuditLog();

    const lockdownBtn = document.getElementById('lockdown-btn');
    if (lockdownBtn) lockdownBtn.addEventListener('click', () => {
        const data = getAdminData();
        data.lockdownActive = !data.lockdownActive;
        saveAdminData(data);
        applyLockdownState();
        addAuditEntry(data.lockdownActive ? '🔒 Lockdown Enabled' : '🔓 Lockdown Disabled', 'All Admin Accounts', 'system');
        toast(data.lockdownActive ? 'Lockdown Mode activated — all new admin sessions require re-verification.' : 'Lockdown Mode deactivated.', data.lockdownActive ? 'error' : 'success');
    });
    applyLockdownState();

    const exportLogsBtn = document.getElementById('export-logs-btn');
    if (exportLogsBtn) exportLogsBtn.addEventListener('click', () => {
        const data = getAdminData();
        const rows = [['Timestamp', 'Admin', 'Action', 'Target', 'IP Address'], ...data.auditLog.map(l => [l.time, l.admin, l.action, l.target, l.ip])];
        downloadFile('security-audit-log.csv', arrayToCsv(rows));
        toast('Audit log exported.');
    });

    const actionFilter = document.getElementById('audit-action-filter');
    if (actionFilter) actionFilter.addEventListener('change', () => renderAuditLog(actionFilter.value));

    const viewDetailsBtn = document.getElementById('view-alert-details-btn');
    if (viewDetailsBtn) viewDetailsBtn.addEventListener('click', () => {
        toast('Critical alert: 3 failed logins from 192.168.1.45 targeting admin accounts.', 'error');
    });
}

function applyLockdownState() {
    const data = getAdminData();
    const btn = document.getElementById('lockdown-btn');
    if (btn) btn.innerHTML = data.lockdownActive
        ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px;"><path d="M18 6L6 18M6 6l12 12"></path></svg>Disable Lockdown'
        : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>Lockdown Mode';
    if (btn) btn.classList.toggle('btn-danger-solid', data.lockdownActive);
    if (btn) btn.classList.toggle('btn-navy', !data.lockdownActive);
}

function renderSuspiciousLogins() {
    const data = getAdminData();
    const container = document.getElementById('suspicious-logins-container');
    if (!container) return;
    if (!data.suspiciousLogins.length) {
        container.innerHTML = `<p style="color:var(--text-light);">No suspicious logins detected. ✅</p>`;
        return;
    }
    container.innerHTML = data.suspiciousLogins.map(s => `
        <div class="suspicious-login-card" data-login-id="${s.id}">
            <div class="suspicious-login-top">
                <span class="suspicious-login-email">${s.email}</span>
                <span class="pill ${s.risk === 'high' ? 'pill-red' : 'pill-amber'}">${s.risk.toUpperCase()} RISK</span>
            </div>
            <div class="suspicious-login-ip">IP: ${s.ip} · ${s.time}</div>
            <div class="risk-bar-track"><div class="risk-bar-fill ${s.risk === 'high' ? 'high' : 'medium'}"></div></div>
            <button class="btn-block-ip" ${s.blocked ? 'disabled' : ''}>${s.blocked ? '✅ Blocked' : '🚫 Block IP'}</button>
        </div>`).join('');

    container.querySelectorAll('.suspicious-login-card').forEach(card => {
        const btn = card.querySelector('.btn-block-ip');
        if (btn.disabled) return;
        btn.addEventListener('click', () => {
            const id = Number(card.getAttribute('data-login-id'));
            const data = getAdminData();
            const login = data.suspiciousLogins.find(s => s.id === id);
            if (!login) return;
            login.blocked = true;
            saveAdminData(data);
            addAuditEntry('🚫 IP Blocked', login.ip, 'security', true);
            renderSuspiciousLogins();
            renderAuditLog();
            toast(`IP ${login.ip} has been blocked.`, 'error');
        });
    });
}

function addAuditEntry(action, target, actionType, flagged = false) {
    const data = getAdminData();
    data.auditLog.unshift({
        time: new Date().toISOString().slice(0, 19).replace('T', ' '),
        admin: 'You', actionType, action, target, ip: 'Internal', flagged
    });
    saveAdminData(data);
}

function renderAuditLog(filterType = 'all') {
    const data = getAdminData();
    const tbody = document.getElementById('audit-table-body');
    if (!tbody) return;
    const items = filterType === 'all' ? data.auditLog : data.auditLog.filter(l => l.actionType === filterType);
    tbody.innerHTML = items.map(l => `
        <tr class="${l.flagged ? 'flagged-row' : ''}">
            <td>${l.time}</td>
            <td>${l.admin}</td>
            <td><span class="pill ${l.flagged ? 'pill-red' : (l.actionType === 'system' ? 'pill-green' : 'pill-blue')}">${l.action}</span></td>
            <td>${l.target}</td>
            <td style="${l.flagged ? 'color:#DC2626;font-weight:600;' : ''}">${l.ip}</td>
        </tr>`).join('') || `<tr><td colspan="5" style="text-align:center;color:var(--text-light);padding:32px;">No matching log entries.</td></tr>`;

    const info = document.getElementById('audit-footer-info');
    if (info) info.textContent = `Showing 1-${items.length} of ${data.auditLog.length} entries`;
}

/* ============================================================
   11. PAGE: REPORTS & ANALYTICS
   ============================================================ */

function initReportsPage() {
    const periodSelect = document.getElementById('period-select');
    if (periodSelect) {
        periodSelect.addEventListener('change', () => renderReportPeriod(periodSelect.value));
        renderReportPeriod(periodSelect.value);
    } else {
        renderReportPeriod('30');
    }

    const exportPdfBtn = document.getElementById('export-pdf-btn');
    if (exportPdfBtn) exportPdfBtn.addEventListener('click', () => {
        toast('Preparing PDF — opening print dialog…');
        setTimeout(() => window.print(), 400);
    });

    const catMenuBtn = document.querySelector('#category-performance-container')?.closest('.dashboard-card')?.querySelector('.activity-menu-btn');
    if (catMenuBtn) {
        catMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const parent = catMenuBtn.parentElement;
            const panel = document.createElement('div');
            panel.className = 'dropdown-panel dropdown-panel-right';
            parent.style.position = 'relative';
            parent.appendChild(panel);
            toggleDropdown(panel, `
                <div class="dropdown-item"><a href="#" class="act-cat-csv">Download as CSV</a></div>
                <div class="dropdown-item"><a href="#" class="act-cat-refresh">Refresh Data</a></div>`);
            setTimeout(() => {
                const csvLink = panel.querySelector('.act-cat-csv');
                const refreshLink = panel.querySelector('.act-cat-refresh');
                if (csvLink) csvLink.addEventListener('click', (ev) => {
                    ev.preventDefault();
                    const data = getAdminData();
                    const period = document.getElementById('period-select')?.value || '30';
                    const stats = data.reportPeriods[period];
                    const rows = [['Category', 'Value'], ...stats.categories.map(c => [c[0], c[1]])];
                    downloadFile('category-performance.csv', arrayToCsv(rows));
                    toast('Category performance exported.');
                    closeAllDropdowns();
                });
                if (refreshLink) refreshLink.addEventListener('click', (ev) => {
                    ev.preventDefault();
                    toast('Category performance refreshed.');
                    closeAllDropdowns();
                });
            }, 0);
        });
    }

    const viewFullListBtn = document.getElementById('view-full-suppliers-btn');
    if (viewFullListBtn) viewFullListBtn.addEventListener('click', () => {
        toast('Opening full supplier performance list…');
    });
}

function renderReportPeriod(period) {
    const data = getAdminData();
    const stats = data.reportPeriods[period] || data.reportPeriods['30'];
    const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
    set('stat-total-transactions', stats.transactions);
    set('stat-total-budget', stats.budget);
    set('stat-avg-transaction', stats.avgTx);
    set('stat-active-suppliers-report', stats.suppliers);
    setTrend('trend-transactions', stats.txTrend);
    setTrend('trend-budget', stats.budgetTrend);
    setTrend('trend-avg', stats.avgTrend);
    setTrend('trend-suppliers', stats.supplierTrend);

    const catContainer = document.getElementById('category-performance-container');
    if (catContainer) {
        catContainer.innerHTML = stats.categories.map(([name, value, pct]) => `
            <div class="category-bar-row">
                <div class="category-bar-label"><span class="cat-name">${name}</span><span class="cat-value">${value}</span></div>
                <div class="category-bar-track"><div class="category-bar-fill" style="width:${pct}%;"></div></div>
            </div>`).join('');
    }
}

function setTrend(id, trendValue) {
    const el = document.getElementById(id);
    if (!el) return;
    const isUp = trendValue.trim().startsWith('+');
    el.textContent = `${isUp ? '↑' : '↓'} ${trendValue.replace('+', '').replace('-', '')}`;
    el.classList.remove('up', 'down');
    el.classList.add(isUp ? 'up' : 'down');
}

/* ============================================================
   12. PAGE: SETTINGS
   ============================================================ */

function initSettingsPage() {
    const data = getAdminData();

    const nameInput = document.getElementById('profile-name-input');
    const emailInput = document.getElementById('profile-email-input');
    if (nameInput) nameInput.value = data.profile.fullName;
    if (emailInput) emailInput.value = data.profile.email;

    const saveProfileBtn = document.getElementById('save-profile-btn');
    if (saveProfileBtn) saveProfileBtn.addEventListener('click', () => {
        const d = getAdminData();
        if (nameInput) d.profile.fullName = nameInput.value.trim() || d.profile.fullName;
        if (emailInput) d.profile.email = emailInput.value.trim() || d.profile.email;
        saveAdminData(d);
        toast('Profile saved successfully.');
    });

    const updateSecurityBtn = document.getElementById('update-security-btn');
    if (updateSecurityBtn) updateSecurityBtn.addEventListener('click', () => {
        const current = document.getElementById('current-password-input');
        const next = document.getElementById('new-password-input');
        const confirmPw = document.getElementById('confirm-password-input');
        if (next && confirmPw && next.value && next.value !== confirmPw.value) {
            toast('New password and confirmation do not match.', 'error');
            return;
        }
        [current, next, confirmPw].forEach(i => { if (i) i.value = ''; });
        toast('Security settings updated.');
    });

    const emailToggle = document.getElementById('email-notif-toggle');
    if (emailToggle) {
        emailToggle.checked = data.profile.emailNotifications;
        emailToggle.addEventListener('change', () => {
            const d = getAdminData();
            d.profile.emailNotifications = emailToggle.checked;
            saveAdminData(d);
            toast(`Email notifications ${emailToggle.checked ? 'enabled' : 'disabled'}.`);
        });
    }

    const alertsToggle = document.getElementById('system-alerts-toggle');
    if (alertsToggle) {
        alertsToggle.checked = data.profile.systemAlerts;
        alertsToggle.addEventListener('change', () => {
            const d = getAdminData();
            d.profile.systemAlerts = alertsToggle.checked;
            saveAdminData(d);
            toast(`System alerts ${alertsToggle.checked ? 'enabled' : 'disabled'}.`);
        });
    }

    const darkToggle = document.getElementById('dark-mode-toggle');
    if (darkToggle) {
        darkToggle.checked = data.profile.darkMode;
        darkToggle.addEventListener('change', () => setDarkMode(darkToggle.checked));
    }

    const twoFaToggle = document.getElementById('twofa-toggle');
    if (twoFaToggle) {
        twoFaToggle.checked = data.profile.twoFactorEnabled;
        twoFaToggle.addEventListener('change', () => {
            const d = getAdminData();
            d.profile.twoFactorEnabled = twoFaToggle.checked;
            saveAdminData(d);
            const statusLabel = document.querySelector('.twofa-status-label');
            if (statusLabel) statusLabel.textContent = `Status: ${twoFaToggle.checked ? 'Enabled' : 'Disabled'}`;
            toast(`Two-factor authentication ${twoFaToggle.checked ? 'enabled' : 'disabled'}.`);
        });
    }

    const exportDataBtn = document.getElementById('export-data-btn');
    if (exportDataBtn) exportDataBtn.addEventListener('click', () => {
        const d = getAdminData();
        downloadFile('my-admin-data.json', JSON.stringify(d.profile, null, 2), 'application/json');
        toast('Personal data exported.');
    });

    const deleteAccountBtn = document.getElementById('delete-account-btn');
    if (deleteAccountBtn) deleteAccountBtn.addEventListener('click', () => {
        if (confirm('Are you sure you want to request account deletion? This cannot be undone.')) {
            toast('Account deletion request submitted. Our team will follow up by email.', 'error');
        }
    });
}


/* ============================================================
   4. TOPBAR INTERACTIVITY (notifications / quick settings / avatar)
   ============================================================ */

document.addEventListener('DOMContentLoaded', function() {
    
    const dropdowns = [
        { btn: 'avatar-btn', panel: 'avatar-dropdown' },
        { btn: 'notif-bell-btn', panel: 'notif-dropdown' },
        { btn: 'quick-settings-btn', panel: 'quick-settings-dropdown' },
        { btn: 'help-btn', panel: 'help-dropdown' }
    ];

    // 1. Dropdown Buttons ක්ලික් කිරීම
    dropdowns.forEach(item => {
        const btn = document.getElementById(item.btn);
        const panel = document.getElementById(item.panel);
        
        if (btn && panel) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                // අනිත් ඔක්කොම වහනවා
                dropdowns.forEach(d => {
                    const p = document.getElementById(d.panel);
                    if (p && p !== panel) p.style.display = 'none';
                });
                // ක්ලික් කරපු එක Open/Close කරනවා
                panel.style.display = panel.style.display === 'block' ? 'none' : 'block';
            });
        }
    });

    // 2. පිටතින් ක්ලික් කළාම ඔක්කොම වැහීම
    document.addEventListener('click', function() {
        dropdowns.forEach(d => {
            const p = document.getElementById(d.panel);
            if (p) p.style.display = 'none';
        });
    });

    // 3. Dropdown එක ඇතුළේ ක්ලික් කළාම වැහෙන එක නවත්වනවා
    document.querySelectorAll('.dropdown-panel').forEach(panel => {
        panel.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });

    // ==========================================
    // 4. Dark Mode Logic එක
    // ==========================================
    const darkModeToggle = document.getElementById('quick-dark-toggle');
    
    // කලින් Dark Mode දාලා නම් ඒක මතක තියාගන්න (Local Storage එකෙන්)
    if (localStorage.getItem('budgetPilotDarkMode') === 'true') {
        document.body.classList.add('dark-theme');
        if (darkModeToggle) darkModeToggle.checked = true;
    }

    // Toggle බට්න් එක එබුවාම
    if (darkModeToggle) {
        darkModeToggle.addEventListener('change', function() {
            if (this.checked) {
                document.body.classList.add('dark-theme');
                localStorage.setItem('budgetPilotDarkMode', 'true');
            } else {
                document.body.classList.remove('dark-theme');
                localStorage.setItem('budgetPilotDarkMode', 'false');
            }
        });
    }
});


// ==========================================
// Dark Mode Helper Functions (Restored)
// ==========================================
function isDarkModeOn() {
    return document.body.classList.contains('dark-theme');
}

function setDarkMode(on) {
    document.body.classList.toggle('dark-theme', on);
    const data = getAdminData();
    data.profile.darkMode = on;
    saveAdminData(data);
    const settingsToggle = document.getElementById('dark-mode-toggle');
    if (settingsToggle) settingsToggle.checked = on;
    const quickToggle = document.getElementById('quick-dark-toggle');
    if (quickToggle) quickToggle.checked = on;
}

function applyStoredDarkMode() {
    const data = getAdminData();
    if (data.profile.darkMode) document.body.classList.add('dark-theme');
}


/* ============================================================
   13. BOOTSTRAP
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {
    applyStoredDarkMode();
    
    // අපි Sidebar සහ Topbar PHP වලින් හදපු නිසා, කලින් JS එකෙන් ඒවා හදපු 
    // functions දැන් මෙතනින් කෝල් කරන්නේ නැහැ. (ඒවා මකලා තියෙන්නේ)

    const page = document.body.getAttribute('data-page');
    switch (page) {
        case 'dashboard': initDashboardPage(); break;
        case 'users': initUserManagementPage(); break;
        case 'suppliers': initSupplierApprovalsPage(); break;
        case 'products': initProductModerationPage(); break;
        case 'security': initSecurityAuditPage(); break;
        case 'reports': initReportsPage(); break;
        case 'settings': initSettingsPage(); break;
    }
});
