function closeAllDropdowns(except) {
    document.querySelectorAll('.dropdown-panel').forEach(panel => {
        if (panel !== except) panel.style.display = 'none';
    });
}
function toggleDropdown(panel) {
    if (!panel) return;
    const isOpen = panel.style.display === 'block';
    closeAllDropdowns(panel);
    panel.style.display = isOpen ? 'none' : 'block';
}
document.querySelectorAll('.dropdown-panel').forEach(panel => {
    panel.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});
document.addEventListener('click', function() {
    closeAllDropdowns();
});
