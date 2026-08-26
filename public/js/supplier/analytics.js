
// Line chart renderer
const chart = document.getElementById('lineChart');

function draw(values) {
    const pts = values.map((v, i) => `${(i / (values.length - 1)) * 96 + 2},${100 - v}`).join(' ');
    chart.innerHTML = `<svg viewBox="0 0 100 100" preserveAspectRatio="none">
        <polyline points="${pts}" fill="none" stroke="#080b78" stroke-width="1.5"/>
        <polyline points="${pts}" fill="none" stroke="#18752b" stroke-width=".4" transform="translate(0 4)"/>
    </svg>`;
}

draw([48, 42, 52, 73, 86, 65]);

// Period toggle buttons
document.querySelectorAll('.period').forEach(b => b.onclick = () => {
    document.querySelectorAll('.period').forEach(x => x.classList.remove('primary'));
    b.classList.add('primary');
    draw(b.dataset.period === 'monthly' ? [48, 42, 52, 73, 86, 65] : [52, 61, 70, 78, 73, 88]);
});

// Export quarterly report as CSV
document.getElementById('exportReport').onclick = () => {
    const text = 'Metric,Value\nProjected Revenue,$42910\nSupplier Interactions,2.4K\nMarket Confidence,High\nFresh Produce,42%';
    const a = document.createElement('a');
    a.href = URL.createObjectURL(new Blob([text], {type: 'text/csv'}));
    a.download = 'quarterly-report.csv';
    a.click();
};

document.getElementById('addProduct').onclick = () =>
    location.href = URLROOT + '/supplier/catalog';

document.getElementById('help').onclick = e => {
    e.preventDefault();
    alert('Help Center opened.');
};
