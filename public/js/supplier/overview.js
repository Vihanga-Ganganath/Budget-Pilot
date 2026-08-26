
// Brand list — 22 registered brands
const brands = ['BiteJoy','BrewMasters','ChefsChoice','ChipZo','Cracko','Crunchies','DailyChoice','DairyPure','DentaCare','DermaCare','EcoClean','EssenceCo','Eve','FreshSip','GoldenHarvest','HomeGuard','OrganicLife','PureGlow','PureGrain','PureWash','Stell','Tango'];

document.getElementById('brandList').innerHTML = brands.map(name =>
    `<div style="padding:12px 14px;border:1px solid #d8dce5;border-radius:8px;background:#fafbfc;font-weight:600">${name}</div>`
).join('');

// Toast helper
const toastEl = document.getElementById('toast');
function toast(m) {
    toastEl.textContent = m;
    toastEl.style.display = 'block';
    setTimeout(() => toastEl.style.display = 'none', 2200);
}

// Bar chart
const chart = document.getElementById('chart');
const mine = [42, 57, 76, 66, 88, 82], avg = [32, 38, 45, 50, 55, 52], months = ['Jan','Feb','Mar','Apr','May','Jun'];
chart.innerHTML = months.map((m, i) =>
    `<div style="flex:1"><div class="bar-group"><div class="bar" style="height:${mine[i]}%"></div><div class="bar avg" style="height:${avg[i]}%"></div></div><div class="month">${m}</div></div>`
).join('');

// Buttons
document.getElementById('insights').onclick = () => {
    toast('Opportunity analysis opened.');
    setTimeout(() => alert('Opportunity: expand the eco-friendly catalog and premium sustainable products.'), 200);
};

document.getElementById('activities').onclick = () =>
    alert('All activities:\n• Organic Produce Updated\n• Freshness Certification\n• Inventory Restock');

document.getElementById('addInsight').onclick = e => {
    const widget = e.currentTarget;
    widget.classList.add('added');
    widget.innerHTML = '<span class="insight-plus">✓</span><strong>Market Alert Added</strong><span class="small">Organic product demand is up 24%. This insight is now displayed on your dashboard.</span>';
    toast('Insight widget added successfully.');
};

document.getElementById('help').onclick = e => {
    e.preventDefault();
    alert('Help Center: contact your project support team or open the supplier documentation.');
};

document.getElementById('addProduct').onclick = () =>
    location.href = URLROOT + '/supplier/catalog';
