
// 22 registered brands
const brands = ['BiteJoy','BrewMasters','ChefsChoice','ChipZo','Cracko','Crunchies','DailyChoice','DairyPure','DentaCare','DermaCare','EcoClean','EssenceCo','Eve','FreshSip','GoldenHarvest','HomeGuard','OrganicLife','PureGlow','PureGrain','PureWash','Stell','Tango'];

// Products stored in localStorage (demo data — replace with API calls for real backend)
let products = JSON.parse(localStorage.getItem('bpProducts') || 'null') || [
    {name:'Organic Whole Milk',      sku:'GRO-MLK-001', brand:'DairyPure',   category:'Dairy',   price:4.99,  emoji:'🥛'},
    {name:'Hass Avocados (4pk)',     sku:'GRO-PRO-042', brand:'OrganicLife',  category:'Produce', price:5.50,  emoji:'🥑'},
    {name:'Artisanal Sourdough',     sku:'BAK-SOU-012', brand:'ChefsChoice',  category:'Bakery',  price:6.25,  emoji:'🍞'},
    {name:'Fresh Atlantic Salmon',   sku:'SEA-SAL-088', brand:'DailyChoice',  category:'Seafood', price:18.99, emoji:'🐟'},
    {name:'Organic Greek Yogurt',    sku:'DAI-YOG-005', brand:'DairyPure',   category:'Dairy',   price:5.50,  emoji:'🥛'},
    {name:'Fair Trade Coffee Beans', sku:'PAN-COF-021', brand:'BrewMasters',  category:'Pantry',  price:12.99, emoji:'☕'}
];
products.forEach(p => p.brand = p.brand || brands[0]);

function save() { localStorage.setItem('bpProducts', JSON.stringify(products)); }

function toast(m) {
    const t = document.getElementById('toast');
    t.textContent = m;
    t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 2200);
}

function render(list = products) {
    document.getElementById('rows').innerHTML = list.map(p =>
        `<tr>
          <td><b>${p.emoji} &nbsp; ${p.name}</b><br><span class="small">SKU: ${p.sku}</span></td>
          <td>${p.brand}</td>
          <td>${p.category}</td>
          <td>$${Number(p.price).toFixed(2)}</td>
          <td><span class="pill green">● Live</span></td>
          <td><button class="btn" onclick="menu(${products.indexOf(p)})">⋮</button></td>
        </tr>`
    ).join('');
    document.getElementById('countPill').textContent = products.length + ' Products';
    document.getElementById('activeCount').textContent = (1402 - products.length + 6).toLocaleString();
}

function menu(i) {
    const p = products[i];
    const edit = confirm(`Edit ${p.name}?\nPress Cancel to delete instead.`);
    if (edit) {
        const name = prompt('Product name', p.name);
        if (name) {
            p.name = name;
            const price = prompt('Price', p.price);
            if (price) p.price = Number(price);
            save(); render(); toast('Product updated');
        }
    } else if (confirm('Delete this product?')) {
        products.splice(i, 1);
        save(); render(); toast('Product deleted');
    }
}

function add() {
    const name = prompt('Product name');
    if (!name) return;
    const sku = prompt('SKU', 'NEW-001') || 'NEW-001';
    const brand = prompt('Brand — enter one from this list:\n\n' + brands.join(', '), brands[0]);
    if (!brand) return;
    const matchedBrand = brands.find(b => b.toLowerCase() === brand.trim().toLowerCase());
    if (!matchedBrand) { alert('That brand is not in the registered brand list.'); return; }
    const category = prompt('Category', 'Grocery') || 'Grocery';
    const price = Number(prompt('Price', '5.99') || 5.99);
    products.unshift({name, sku, brand: matchedBrand, category, price, emoji: '📦'});
    save(); render(); toast('Product added');
}

document.getElementById('addProduct').onclick = add;
document.getElementById('search').oninput = e => {
    const q = e.target.value.toLowerCase();
    render(products.filter(p => (p.name + p.sku + p.brand + p.category).toLowerCase().includes(q)));
};
document.getElementById('filter').onclick = () => {
    const brand = prompt('Enter a brand to filter:\n\n' + brands.join(', '));
    if (!brand) return;
    render(products.filter(p => p.brand.toLowerCase() === brand.trim().toLowerCase()));
};
document.getElementById('export').onclick = () => {
    const csv = 'Product,SKU,Brand,Category,Price\n' +
        products.map(p => `"${p.name}",${p.sku},${p.brand},${p.category},${p.price}`).join('\n');
    const a = document.createElement('a');
    a.href = URL.createObjectURL(new Blob([csv], {type: 'text/csv'}));
    a.download = 'budget-pilot-products.csv';
    a.click();
    toast('CSV exported');
};
document.getElementById('fix').onclick = () =>
    alert('Catalog issues: Artisanal Sourdough and Fair Trade Coffee Beans need image updates.');
document.getElementById('report').onclick = () =>
    alert('Market report: Organic Demand +24%. Bulk Pricing Index: Stable.');
document.getElementById('help').onclick = e => { e.preventDefault(); alert('Help Center opened.'); };

render();
if (location.hash === '#add') setTimeout(add, 300);
