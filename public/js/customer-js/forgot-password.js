/* Budget Pilot — "Forgot password?" on the customer sign-in page.
   No email is sent. The person proves who they are with what is saved on
   their account. Steps (the server decides which comes next, never this page):
     email    → which account                          (apiForgotStart)
     nic      → the NIC saved on the account           (apiForgotNic)
     pin      → the 6-digit security PIN set in
                Settings, only when
                users.two_factor_enabled is on         (apiForgotPin)
     password → choose a new password                  (apiForgotReset)   */

(function () {
  'use strict';

  var BP = window.BudgetPilot;
  function el(id) { return document.getElementById(id); }

  var signInView = el('signInView');
  var forgotView = el('forgotView');
  var forgotBtn  = el('forgotBtn');
  if (!forgotView || !forgotBtn) return;

  var TEXT = {
    email:    ['Reset your password', 'Enter the email you sign in with.'],
    nic:      ['Confirm it\u2019s you', 'Enter the NIC number saved on your account.'],
    pin:      ['Two-factor check', 'Two-factor authentication is on for this account. Enter the 6-digit security PIN you set in Settings.'],
    password: ['Choose a new password', 'You\u2019re verified. Pick a password you haven\u2019t used here before.']
  };
  var ORDER = ['email', 'nic', 'pin', 'password'];

  var usedPin = false;

  /* ---------- helpers ---------- */

  function fieldError(id, message) {
    var input = el(id);
    var out = el(id + 'Error');
    if (out) out.textContent = message || '';
    var wrap = input ? input.closest('.input') : null;
    if (wrap) wrap.classList.toggle('is-invalid', !!message);
  }

  function clearErrors() {
    ['fpEmail', 'fpNic', 'fpPin', 'fpNew', 'fpConfirm'].forEach(function (id) { fieldError(id, ''); });
    el('fpError').textContent = '';
  }

  function showErrors(res, fallback) {
    var errors = res.errors || {};
    Object.keys(errors).forEach(function (id) { fieldError(id, errors[id]); });
    if (!Object.keys(errors).length) el('fpError').textContent = res.message || fallback;
  }

  function show(step) {
    clearErrors();
    forgotView.querySelectorAll('.fp-step').forEach(function (box) {
      box.hidden = box.getAttribute('data-step') !== step;
    });
    el('fpTitle').textContent = TEXT[step][0];
    el('fpSub').textContent = TEXT[step][1];

    /* No total shown: accounts without two-factor skip the PIN step. */
    if (step === 'pin') usedPin = true;
    if (step === 'email') usedPin = false;
    var number = ORDER.indexOf(step) + 1;
    if (step === 'password' && !usedPin) number -= 1;
    el('fpSteps').textContent = 'Step ' + number;

    var first = forgotView.querySelector('.fp-step[data-step="' + step + '"] input');
    if (first) first.focus();
  }

  function busy(btn, on) { btn.disabled = on; }

  /* The server lost the request (expired, or too many wrong tries). */
  function restart(res) {
    show('email');
    el('fpError').textContent = res.message || 'Start again with your email.';
  }

  el('fpPin').addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '').slice(0, 6);
  });

  /* ---------- open / close ---------- */

  function open() {
    var typed = el('email') ? el('email').value.trim() : '';
    el('fpEmail').value = typed;
    ['fpNic', 'fpPin', 'fpNew', 'fpConfirm'].forEach(function (id) { el(id).value = ''; });
    signInView.hidden = true;
    forgotView.hidden = false;
    show('email');
  }

  function close() {
    forgotView.hidden = true;
    signInView.hidden = false;
  }

  forgotBtn.addEventListener('click', open);
  el('fpBackBtn').addEventListener('click', close);

  /* ---------- step 1: email ---------- */

  function startReset() {
    clearErrors();
    var check = BP.checkEmail(el('fpEmail').value);
    if (!check.ok) { fieldError('fpEmail', check.message); el('fpEmail').focus(); return; }

    var btn = el('fpEmailBtn');
    busy(btn, true);
    BP.api('apiForgotStart', { email: el('fpEmail').value.trim().toLowerCase() }).then(function (res) {
      busy(btn, false);
      if (!res.ok) { showErrors(res, 'Could not start the reset. Please try again.'); return; }
      el('fpNic').value = '';
      el('fpPin').value = '';
      show(res.next);
    });
  }
  el('fpEmailBtn').addEventListener('click', startReset);

  /* ---------- step 2: NIC ---------- */

  function verifyNic() {
    clearErrors();
    var nic = el('fpNic').value.replace(/\s+/g, '');
    if (!/^(\d{9}[VvXx]|\d{12})$/.test(nic)) {
      fieldError('fpNic', 'Use 12 digits, or 9 digits followed by V.');
      return;
    }

    var btn = el('fpNicBtn');
    busy(btn, true);
    BP.api('apiForgotNic', { nic: nic }).then(function (res) {
      busy(btn, false);
      if (res.ok) { show(res.next); return; }
      if (res.reason === 'restart') { restart(res); return; }
      showErrors(res, 'Could not check the NIC. Please try again.');
    });
  }
  el('fpNicBtn').addEventListener('click', verifyNic);

  /* ---------- step 3: security PIN (two-factor accounts) ---------- */

  function verifyPin() {
    clearErrors();
    var pin = el('fpPin').value;
    if (!/^\d{6}$/.test(pin)) { fieldError('fpPin', 'Enter your 6-digit security PIN.'); return; }

    var btn = el('fpPinBtn');
    busy(btn, true);
    BP.api('apiForgotPin', { pin: pin }).then(function (res) {
      busy(btn, false);
      if (res.ok) { show(res.next); return; }
      if (res.reason === 'restart') { restart(res); return; }
      el('fpPin').value = '';
      showErrors(res, 'Could not check the PIN. Please try again.');
    });
  }
  el('fpPinBtn').addEventListener('click', verifyPin);

  /* ---------- last step: new password ---------- */

  function savePassword() {
    clearErrors();
    var pw = el('fpNew').value;
    var confirm = el('fpConfirm').value;
    var bad = false;

    if (!BP.checkPassword(pw).valid) {
      fieldError('fpNew', 'Use 8+ characters with upper and lower case, a number and a symbol.');
      bad = true;
    }
    if (pw !== confirm) { fieldError('fpConfirm', 'The passwords do not match.'); bad = true; }
    if (bad) return;

    var btn = el('fpSaveBtn');
    busy(btn, true);
    BP.api('apiForgotReset', { password: pw, confirm: confirm }).then(function (res) {
      busy(btn, false);
      if (res.ok) {
        window.location.href = 'login?reset=1';
        return;
      }
      if (res.reason === 'restart') { restart(res); return; }
      showErrors(res, 'Could not save the new password. Please try again.');
    });
  }
  el('fpSaveBtn').addEventListener('click', savePassword);

  /* Enter submits the step you are on. */
  [['fpEmail', startReset], ['fpNic', verifyNic], ['fpPin', verifyPin],
   ['fpNew', savePassword], ['fpConfirm', savePassword]].forEach(function (pair) {
    el(pair[0]).addEventListener('keydown', function (e) {
      if (e.key === 'Enter') { e.preventDefault(); pair[1](); }
    });
  });
})();
