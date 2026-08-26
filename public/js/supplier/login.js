
const toastEl = document.getElementById('toast');
function toast(msg) {
    toastEl.textContent = msg;
    toastEl.style.display = 'block';
    setTimeout(() => toastEl.style.display = 'none', 2200);
}

document.getElementById('showPass').onclick = () => {
    const p = document.getElementById('password');
    p.type = p.type === 'password' ? 'text' : 'password';
};

document.getElementById('forgot').onclick = e => {
    e.preventDefault();
    toast('Password reset link would be sent to your email.');
};

document.getElementById('create').onclick = e => {
    e.preventDefault();
    toast('Account creation form would open here.');
};
