document.getElementById('showPass')?.addEventListener('click', () => {
    const p = document.getElementById('password');
    if (p) {
        p.type = p.type === 'password' ? 'text' : 'password';
    }
});
