/* Budget Pilot — Settings → Deactivate Account
   ---------------------------------------------------------------------------
   Kept in its own file on purpose: it does not depend on the rest of the
   Settings page script, so the button always works.

   Who is deleted comes from MySQL (apiMembers), not from the browser's saved
   profiles, so the popup always names the person PHP has signed in.

     Member → only their own account is deleted.
     Head   → only their account (next member becomes head), or, with the
              checkbox ticked, the whole household.
   --------------------------------------------------------------------------- */

(function () {
  'use strict';

  var ROOT = (window.URLROOT || '') + '/customer/';
  var btn = document.getElementById('deactivateBtn');
  if (!btn) return;

  function post(action, body) {
    return fetch(ROOT + action, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify(body || {})
    }).then(function (res) {
      return res.json().catch(function () {
        return { ok: false, message: 'The server sent an unexpected reply (HTTP ' + res.status + ').' };
      });
    }).catch(function () {
      return { ok: false, message: 'Could not reach the server. Check that XAMPP is running.' };
    });
  }

  function node(tag, props, text) {
    var n = document.createElement(tag);
    Object.keys(props || {}).forEach(function (k) { n[k] = props[k]; });
    if (text) n.textContent = text;
    return n;
  }

  /* Uses the page's #modal markup if it is there, otherwise builds one. */
  function getModal() {
    var modal = document.getElementById('modal');
    if (!modal) {
      modal = node('div', { className: 'modal', id: 'modal' });
      modal.innerHTML =
        '<div class="modal__backdrop"></div>' +
        '<div class="modal__box" role="dialog" aria-modal="true">' +
        '<h2 class="modal__title" id="modalTitle"></h2>' +
        '<div class="modal__body" id="modalBody"></div>' +
        '<div class="modal__actions" id="modalActions"></div></div>';
      document.body.appendChild(modal);
    }
    return {
      root: modal,
      title: modal.querySelector('.modal__title'),
      body: modal.querySelector('.modal__body'),
      actions: modal.querySelector('.modal__actions')
    };
  }

  function forgetInBrowser(deletedEmail, wholeHousehold) {
    try {
      if (wholeHousehold) {
        localStorage.removeItem('budgetPilot.v1');
      } else {
        var data = JSON.parse(localStorage.getItem('budgetPilot.v1') || '{"profiles":[]}');
        data.profiles = (data.profiles || []).filter(function (p) {
          return String(p.email).toLowerCase() !== deletedEmail;
        });
        localStorage.setItem('budgetPilot.v1', JSON.stringify(data));
      }
      localStorage.removeItem('budgetPilot.session');
    } catch (e) { /* storage blocked — nothing to forget */ }
  }

  btn.addEventListener('click', function () {
    var m = getModal();
    m.title.textContent = 'Deactivate Account';
    m.body.textContent = 'Loading your account…';
    m.actions.textContent = '';
    m.root.hidden = false;

    function close() { m.root.hidden = true; }
    var backdrop = m.root.querySelector('.modal__backdrop');
    if (backdrop) backdrop.onclick = close;

    /* Ask MySQL who is signed in and who else is on the household. */
    post('apiMembers').then(function (res) {
      if (!res.ok) {
        if (/session has ended/i.test(res.message || '')) {
          window.location.replace('login?signin=required');
          return;
        }
        m.body.textContent = res.message || 'Could not load your account.';
        m.actions.appendChild(node('button', { type: 'button', className: 'btn btn--ghost', onclick: close }, 'Close'));
        return;
      }

      var members = res.members || [];
      var me = members.filter(function (p) { return Number(p.id) === Number(res.userId); })[0];
      if (!me) {
        m.body.textContent = 'Your account could not be found. Sign in again.';
        return;
      }
      var isHead = me.household_role === 'head';
      var others = members.filter(function (p) { return Number(p.id) !== Number(me.id); });

      m.body.textContent = '';
      m.body.appendChild(node('p', {}, 'This permanently deletes the account of ' + me.name + ' (' + me.email + ').'));
      var what = node('p');
      m.body.appendChild(what);

      var all = null;
      if (isHead && others.length) {
        var row = node('label');
        row.style.cssText = 'display:flex;gap:8px;align-items:flex-start;margin:4px 0 14px;cursor:pointer;';
        all = node('input', { type: 'checkbox', id: 'deactivateAll' });
        row.appendChild(all);
        row.appendChild(node('span', {}, 'Also delete every family member\u2019s account (' +
          others.map(function (p) { return p.name; }).join(', ') + ')'));
        m.body.appendChild(row);
        all.addEventListener('change', describe);
      }

      function describe() {
        what.textContent = all && all.checked
          ? 'The whole household is deleted: all ' + members.length + ' accounts. This cannot be undone.'
          : isHead && others.length
            ? 'You are the main holder, so ' + others[0].name + ' becomes the main holder and keeps their account.'
            : isHead
              ? 'You are the only person on this account, so the household is deleted too. This cannot be undone.'
              : 'You will be removed from the household. This cannot be undone.';
      }
      describe();

      m.body.appendChild(node('label', { className: 'label', htmlFor: 'deactivatePw' }, 'Enter your password to confirm'));
      var pw = node('input', { className: 'control', id: 'deactivatePw', type: 'password', autocomplete: 'current-password' });
      m.body.appendChild(pw);
      var err = node('p', { className: 'err', id: 'deactivateErr' });
      err.setAttribute('role', 'alert');
      err.style.color = '#c0392b';
      m.body.appendChild(err);

      var keep = node('button', { type: 'button', className: 'btn btn--ghost', onclick: close }, 'Keep account');
      var del = node('button', { type: 'button', className: 'btn btn--danger' }, 'Delete account');
      m.actions.appendChild(keep);
      m.actions.appendChild(del);
      pw.focus();

      function submit() {
        err.textContent = '';
        if (!pw.value) { err.textContent = 'Enter your password to confirm.'; pw.focus(); return; }

        var whole = !!(all && all.checked);
        del.disabled = true;
        del.textContent = 'Deleting…';

        post('apiDeleteAccount', { id: Number(me.id), password: pw.value, wholeHousehold: whole })
          .then(function (out) {
            del.disabled = false;
            del.textContent = 'Delete account';
            if (!out.ok) {
              err.textContent = (out.errors && out.errors.password) || out.message ||
                'Could not delete the account. Please try again.';
              pw.focus();
              return;
            }
            forgetInBrowser(String(me.email).toLowerCase(), whole || !others.length);
            window.location.href = 'logout?deactivated=1';
          });
      }

      del.addEventListener('click', submit);
      pw.addEventListener('keydown', function (e) { if (e.key === 'Enter') submit(); });
    });
  });
})();
