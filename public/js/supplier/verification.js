
function toast(m) {
    const t = document.getElementById('toast');
    t.textContent = m;
    t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 2200);
}

// Document action buttons
document.querySelectorAll('.doc').forEach(b => b.onclick = () =>
    toast(`${b.dataset.name}: action opened.`)
);

// Update All — marks pending as verified
document.getElementById('updateAll').onclick = () => {
    const p = document.querySelector('.pending');
    if (p) { p.textContent = '✓ VERIFIED'; p.className = 'pill green'; }
    toast('All verification documents refreshed.');
};

document.getElementById('addProduct').onclick = () =>
    location.href = URLROOT + '/supplier/catalog';

document.getElementById('help').onclick = e => {
    e.preventDefault();
    alert('Help Center opened.');
};
