/* Budget Pilot — Settings: two-factor authentication card.
   Turning it on saves a 6-digit security PIN (users.two_factor_pin_hash) and
   sets users.two_factor_enabled = 1. While it is on, the forgot-password flow
   asks for this PIN after the NIC. The PIN can be changed while it's on;
   turning it off removes the PIN. Every change needs the current password. */

(function () {
  'use strict';

  var BP = window.BudgetPilot;
  function el(id) { return document.getElementById(id); }
  if (!el('tfaCard') || !BP) return;

  var FIELDS = ['tfaPin', 'tfaPinConfirm', 'tfaPassword'];
  var enabled = false;
  var toast = el('toast');
  var toastTimer = null;

  function say(message) {
    if (!toast) return;
    toast.textContent = message;
    toast.hidden = false;
    window.clearTimeout(toastTimer);
    toastTimer = window.setTimeout(function () { toast.hidden = true; }, 3400);
  }

  function setError(id, message) {
    el(id + 'Err').textContent = message || '';
    el(id).classList.toggle('is-invalid', !!message);
  }
  function clearErrors() { FIELDS.forEach(function (id) { setError(id, ''); }); }
  function clearInputs() { FIELDS.forEach(function (id) { el(id).value = ''; }); }

  function render(state) {
    enabled = state.enabled;
    el('tfaBadge').textContent = enabled ? 'On' : 'Off';
    el('tfaBadge').classList.toggle('is-on', enabled);

    el('tfaBtn').textContent = enabled ? 'Save new PIN' : 'Turn on';
    el('tfaOffBtn').hidden = !enabled;
    el('tfaPinLabel').textContent = enabled ? 'New security PIN' : 'Security PIN';
    el('tfaPinHint').textContent = enabled
      ? 'Only fill the PIN boxes if you want to change your PIN.'
      : '6 digits. Avoid easy ones like 123456 or 111111.';

    /* Turning on needs a NIC: the reset flow checks it before the PIN. */
    var blocked = !enabled && !state.hasNic;
    el('tfaNoNic').hidden = !blocked;
    el('tfaBtn').disabled = blocked;
    FIELDS.forEach(function (id) { el(id).disabled = blocked; });
  }

  function load() {
    BP.api('apiTwoFactorStatus').then(function (res) {
      if (!res.ok) { el('tfaBadge').textContent = 'Unavailable'; return; }
      render(res);
    });
  }
  load();

  /* Saving a NIC under Account Details can unlock the card. */
  var saveBtn = el('saveBtn');
  if (saveBtn) saveBtn.addEventListener('click', function () { window.setTimeout(load, 1200); });

  /* Digits only in the PIN boxes. */
  ['tfaPin', 'tfaPinConfirm'].forEach(function (id) {
    el(id).addEventListener('input', function () {
      this.value = this.value.replace(/\D/g, '').slice(0, 6);
    });
  });

  function send(body, btn, done) {
    btn.disabled = true;
    BP.api('apiTwoFactorSet', body).then(function (res) {
      btn.disabled = false;
      if (res.ok) {
        clearInputs();
        render({ enabled: res.enabled, hasNic: true });
        say(done);
        return;
      }
      var errors = res.errors || {};
      Object.keys(errors).forEach(function (id) { if (el(id)) setError(id, errors[id]); });
      if (!Object.keys(errors).length) say(res.message || 'Could not change two-factor. Please try again.');
    });
  }

  /* Turn on, or save a new PIN while it's on. */
  function savePin() {
    clearErrors();
    var pin = el('tfaPin').value;
    var confirm = el('tfaPinConfirm').value;
    var pw = el('tfaPassword').value;
    var bad = false;

    if (!/^\d{6}$/.test(pin)) { setError('tfaPin', 'The PIN must be exactly 6 digits.'); bad = true; }
    else if (pin !== confirm) { setError('tfaPinConfirm', 'The PINs do not match.'); bad = true; }
    if (!pw) { setError('tfaPassword', 'Enter your password to confirm.'); bad = true; }
    if (bad) return;

    send({ enabled: true, pin: pin, pinConfirm: confirm, password: pw }, el('tfaBtn'),
         enabled ? 'Your security PIN has been changed.' : 'Two-factor authentication is on.');
  }

  function turnOff() {
    clearErrors();
    var pw = el('tfaPassword').value;
    if (!pw) { setError('tfaPassword', 'Enter your password to confirm.'); return; }
    if (!window.confirm('Turn off two-factor? Your security PIN will be removed, and only your NIC will be needed to reset your password.')) return;
    send({ enabled: false, password: pw }, el('tfaOffBtn'), 'Two-factor authentication is off.');
  }

  el('tfaBtn').addEventListener('click', savePin);
  el('tfaOffBtn').addEventListener('click', turnOff);
  FIELDS.forEach(function (id) {
    el(id).addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); savePin(); } });
  });
})();
