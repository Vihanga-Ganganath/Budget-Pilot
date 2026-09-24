/* Budget Pilot — household view

   The account holder's view of everyone on the account: who is on it, what
   each person has spent against the shared budget, and the full transaction
   feed with a name on every row.

   A member who lands here is shown the notice instead — their own spending
   lives on their dashboard. */

(function () {
  'use strict';

  var BP = window.BudgetPilot;
  var el = function (id) { return document.getElementById(id); };

  var profileId = BP.getSession();
  var profile = profileId ? BP.getProfile(profileId) : null;

  if (!profile) {
    window.location.replace('login?signin=required');
    return;
  }

  var CURRENCIES = { USD: '$', EUR: '\u20AC', GBP: '\u00A3', LKR: 'Rs', INR: '\u20B9', AUD: '$' };
  var prefs = profile.prefs || {};
  var symbol = CURRENCIES[prefs.currency] || '$';

  if (prefs.darkMode) document.body.classList.add('is-dark');

  el('logoutBtn').addEventListener('click', function () {
    BP.clearSession();
    window.location.href = 'logout';
  });

  var avatar = el('appbarAvatar');
  if (profile.avatar) {
    avatar.style.backgroundImage = 'url(' + profile.avatar + ')';
  } else {
    avatar.style.backgroundColor = BP.avatarColor(profile.email || profile.name);
    avatar.textContent = BP.initials(profile.name);
  }

  /* Members don't get this page. */
  if (!BP.isMain(profileId)) {
    el('denied').hidden = false;
    return;
  }
  el('holderView').hidden = false;

  /* ==================================================================
     Helpers
     ================================================================== */

  function money(value) {
    var n = Number(value) || 0;
    return symbol + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function labelFor(key) {
    var match = BP.CATEGORIES.filter(function (c) { return c.key === key; })[0];
    return match ? match.label : 'Other';
  }

  var COLORS = {
    grocery:'#3C8F60', transport:'#3A7CB4', rent:'#3A4A85', loan:'#453A7C',
    tuition:'#96591F', medicine:'#96354A', clothing:'#454E62', other:'#8A93AB'
  };

  function dayLabel(entry) {
    var raw = String(entry.date || entry.loggedAt || '').slice(0, 10);
    var bits = raw.split('-');
    if (bits.length !== 3) return '';
    var d = new Date(Number(bits[0]), Number(bits[1]) - 1, Number(bits[2]));
    if (isNaN(d.getTime())) return '';

    var today = new Date();
    var days = Math.round(
      (new Date(today.getFullYear(), today.getMonth(), today.getDate()) -
       new Date(d.getFullYear(), d.getMonth(), d.getDate())) / 86400000);

    if (days === 0) return 'Today';
    if (days === 1) return 'Yesterday';
    return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
  }

  function faceFor(person) {
    var face = document.createElement('span');
    face.className = 'face';
    if (person.avatar) {
      face.style.backgroundImage = 'url(' + person.avatar + ')';
    } else {
      face.style.backgroundColor = BP.avatarColor(person.email || person.name);
      face.textContent = BP.initials(person.name);
    }
    return face;
  }

  /* ==================================================================
     Figures
     ================================================================== */

  var budget, people, entries, planned, spent;

  function computeFigures() {
    budget = BP.getBudget(profileId) || { categories: {} };
    people = BP.memberSpend(profileId);
    entries = BP.allExpenses(profileId).slice().reverse();   /* newest first */

    planned = 0;
    spent = 0;
    BP.CATEGORIES.forEach(function (c) {
      var row = budget.categories[c.key] || {};
      planned += Number(row.planned) || 0;
      spent += Number(row.spent) || 0;
    });

    el('sumPlanned').textContent = planned > 0 ? money(planned) : 'Not set';
    el('sumSpent').textContent = money(spent);
    el('sumLeft').textContent = planned > 0 ? money(planned - spent) : '—';
    el('sumLeft').classList.toggle('is-over', planned > 0 && planned - spent < 0);

    el('pageSub').textContent = people.length > 1
      ? people.length + ' people spend from one budget. Only you can see this page.'
      : 'Everyone on this account, and what they have spent.';
  }

  /* ==================================================================
     People
     ================================================================== */

  function paintPeople() {
    var list = el('people');
    list.textContent = '';

    el('peopleEmpty').hidden = people.length > 1;

    people.forEach(function (person) {
      var li = document.createElement('li');
      li.className = 'person';

      li.appendChild(faceFor(person));

      var text = document.createElement('div');
      text.className = 'person__text';

      var name = document.createElement('h3');
      name.className = 'person__name';
      name.textContent = person.name;
      if (person.id === profileId) {
        var you = document.createElement('span');
        you.className = 'tagpill';
        you.textContent = 'You';
        name.appendChild(you);
      }
      if (person.role === 'Main') {
        var main = document.createElement('span');
        main.className = 'tagpill tagpill--main';
        main.textContent = 'Account holder';
        name.appendChild(main);
      }
      text.appendChild(name);

      var meta = document.createElement('p');
      meta.className = 'person__meta';
      meta.textContent = person.email + ' \u00B7 ' + (person.count === 0
        ? 'No transactions yet'
        : person.count + (person.count === 1 ? ' transaction' : ' transactions'));
      text.appendChild(meta);

      li.appendChild(text);

      var amount = document.createElement('strong');
      amount.className = 'person__amount';
      amount.textContent = money(person.total);
      li.appendChild(amount);

      /* Share of the household's spending, so it is obvious at a glance. */
      var share = spent > 0 ? Math.round(person.total / spent * 100) : 0;
      var bar = document.createElement('span');
      bar.className = 'person__bar';
      var fill = document.createElement('i');
      fill.style.width = share + '%';
      bar.appendChild(fill);
      li.appendChild(bar);

      var pct = document.createElement('span');
      pct.className = 'person__pct';
      pct.textContent = share + '%';
      li.appendChild(pct);

      /* Edit / Remove — only for family members, never the account holder. */
      var actions = document.createElement('div');
      actions.className = 'person__actions';
      if (person.role !== 'Main' && person.dbId) {
        actions.appendChild(iconButton('Edit ' + person.name, ICON_EDIT, '', function () {
          openEdit(person.dbId);
        }));
        actions.appendChild(iconButton('Remove ' + person.name, ICON_DELETE, 'iconbtn--danger', function () {
          openDelete(person.dbId);
        }));
      }
      li.appendChild(actions);

      list.appendChild(li);
    });
  }

  /* ==================================================================
     Transaction feed
     ================================================================== */

  var who = 'all';

  function paintChips() {
    var wrap = el('whoChips');
    wrap.textContent = '';

    var list = [{ id: 'all', name: 'Everyone' }].concat(people.map(function (p) {
      return { id: p.id, name: p.name.split(/\s+/)[0] };
    }));

    list.forEach(function (spec) {
      var chip = document.createElement('button');
      chip.type = 'button';
      chip.className = 'chip' + (spec.id === who ? ' chip--on' : '');
      chip.textContent = spec.name;
      chip.setAttribute('aria-pressed', spec.id === who ? 'true' : 'false');
      chip.addEventListener('click', function () {
        who = spec.id;
        paintChips();
        paintFeed();
      });
      wrap.appendChild(chip);
    });
  }

  function paintFeed() {
    var list = el('feed');
    list.textContent = '';

    if (who !== 'all' && !people.some(function (p) { return p.id === who; })) who = 'all';
    var rows = entries.filter(function (e) { return who === 'all' || e.by === who; });

    if (!rows.length) {
      el('feedEmpty').hidden = false;
      el('feedEmpty').textContent = entries.length
        ? 'Nothing recorded by this person yet.'
        : 'Nothing logged yet. Expenses and grocery checkouts land here as soon as anyone records one.';
      return;
    }
    el('feedEmpty').hidden = true;

    rows.forEach(function (entry) {
      var li = document.createElement('li');
      li.className = 'row';

      var dot = document.createElement('span');
      dot.className = 'row__dot';
      dot.style.backgroundColor = COLORS[entry.category] || COLORS.other;
      li.appendChild(dot);

      var text = document.createElement('div');
      text.className = 'row__text';

      var title = document.createElement('h3');
      title.className = 'row__name';
      title.textContent = entry.description || labelFor(entry.category);
      text.appendChild(title);

      var meta = document.createElement('p');
      meta.className = 'row__meta';
      meta.textContent = [entry.byName, labelFor(entry.category), dayLabel(entry)]
        .filter(Boolean).join(' · ');
      text.appendChild(meta);

      li.appendChild(text);

      var amount = document.createElement('strong');
      amount.className = 'row__amount';
      amount.textContent = '-' + money(entry.amount);
      li.appendChild(amount);

      list.appendChild(li);
    });
  }

  function paintAll() {
    computeFigures();
    paintPeople();
    paintChips();
    paintFeed();
  }

  /* ==================================================================
     Household members CRUD — every change goes to MySQL through
     CustomerController, then the page redraws from the server's list.
       Create → apiAddMembers     Update → apiUpdateMember
       Read   → apiMembers        Delete → apiDeleteMember
     ================================================================== */

  var ICON_EDIT = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4L19 9l-4-4L4 16z"/><path d="M13.5 6.5l4 4"/></svg>';
  var ICON_DELETE = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16"/><path d="M9 7V4.5h6V7"/><path d="M6.5 7l1 13h9l1-13"/></svg>';

  function iconButton(label, svg, extra, onClick) {
    var b = document.createElement('button');
    b.type = 'button';
    b.className = 'iconbtn ' + (extra || '');
    b.setAttribute('aria-label', label);
    b.title = label;
    b.innerHTML = svg;
    b.addEventListener('click', onClick);
    return b;
  }

  var serverMembers = {};   /* database id → row from apiMembers */

  function toast(message) {
    var t = el('toast');
    t.textContent = message;
    t.hidden = false;
    clearTimeout(toast.timer);
    toast.timer = setTimeout(function () { t.hidden = true; }, 3200);
  }

  /* Takes the member list the server sent back and redraws the page. */
  function applyMembers(members) {
    serverMembers = {};
    members.forEach(function (m) { serverMembers[Number(m.id)] = m; });
    BP.syncHousehold(members);
    paintAll();
    el('peopleStatus').textContent = members.length +
      (members.length === 1 ? ' person' : ' people') + ' on this account.';
  }

  function handleAuthError(res) {
    if (res && /session has ended/i.test(res.message || '')) {
      window.location.replace('login?signin=required');
      return true;
    }
    return false;
  }

  /* ---------- READ ---------- */
  function loadMembers() {
    el('peopleStatus').textContent = 'Loading members…';
    BP.api('apiMembers').then(function (res) {
      if (!res.ok) {
        if (handleAuthError(res)) return;
        el('peopleStatus').textContent = res.message || 'Could not load members.';
        return;
      }
      applyMembers(res.members || []);
    });
  }

  /* ---------- Modal helpers ---------- */
  function openModal(id) { el(id).hidden = false; }
  function closeModal(id) { el(id).hidden = true; }

  ['memberModal', 'deleteModal'].forEach(function (id) {
    Array.prototype.forEach.call(el(id).querySelectorAll('[data-close]'), function (node) {
      node.addEventListener('click', function () { closeModal(id); });
    });
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { closeModal('memberModal'); closeModal('deleteModal'); }
  });

  var FIELDS = { name: 'mName', email: 'mEmail', gender: 'mGender', password: 'mPassword' };

  function clearErrors() {
    el('mFormErr').textContent = '';
    Object.keys(FIELDS).forEach(function (k) {
      el(FIELDS[k] + 'Err').textContent = '';
      var box = el(FIELDS[k]).closest('.control') || el(FIELDS[k]);
      box.classList.remove('is-invalid');
    });
  }

  function showErrors(res) {
    var errors = res.errors || {};
    var shown = false;
    Object.keys(FIELDS).forEach(function (k) {
      if (errors[k]) {
        el(FIELDS[k] + 'Err').textContent = errors[k];
        (el(FIELDS[k]).closest('.control') || el(FIELDS[k])).classList.add('is-invalid');
        shown = true;
      }
    });
    /* apiAddMembers reports problems per member card: errors.members[0]. */
    if (errors.members) {
      var msg = errors.members[0] || errors.members[Object.keys(errors.members)[0]];
      el('mFormErr').textContent = msg;
      shown = true;
    }
    if (!shown) el('mFormErr').textContent = res.message || 'Something went wrong. Please try again.';
  }

  var editingId = null;   /* null = adding (Create), number = editing (Update) */

  /* ---------- CREATE (open form) ---------- */
  el('addMemberBtn').addEventListener('click', function () {
    editingId = null;
    clearErrors();
    el('memberForm').reset();
    el('memberModalTitle').textContent = 'Add family member';
    el('memberSaveBtn').textContent = 'Add member';
    el('mGenderField').hidden = true;
    el('mPasswordLabel').textContent = 'Password';
    el('mPassword').placeholder = '8+ characters';
    openModal('memberModal');
    el('mName').focus();
  });

  /* ---------- UPDATE (open form, filled from the database row) ---------- */
  function openEdit(dbId) {
    var m = serverMembers[Number(dbId)];
    if (!m) { toast('Reload the page and try again.'); return; }

    editingId = Number(dbId);
    clearErrors();
    el('memberForm').reset();
    el('memberModalTitle').textContent = 'Edit ' + m.name;
    el('memberSaveBtn').textContent = 'Save changes';
    el('mName').value = m.name;
    el('mEmail').value = m.email;
    el('mGender').value = m.gender || '';
    el('mGenderField').hidden = false;
    el('mPasswordLabel').textContent = 'New password (optional)';
    el('mPassword').placeholder = 'Leave blank to keep the current one';
    openModal('memberModal');
    el('mName').focus();
  }

  /* ---------- Save: Create or Update ---------- */
  el('memberForm').addEventListener('submit', function (e) {
    e.preventDefault();
    clearErrors();

    var name = el('mName').value.trim();
    var email = el('mEmail').value.trim().toLowerCase();
    var password = el('mPassword').value;
    var btn = el('memberSaveBtn');
    btn.disabled = true;

    var request = editingId === null
      ? BP.api('apiAddMembers', { members: [{ name: name, email: email, password: password }] })
      : BP.api('apiUpdateMember', {
          id: editingId, name: name, email: email,
          gender: el('mGender').value, password: password
        });

    request.then(function (res) {
      btn.disabled = false;
      if (!res.ok) {
        if (handleAuthError(res)) return;
        showErrors(res);
        return;
      }
      closeModal('memberModal');
      applyMembers(res.members || []);
      toast(editingId === null ? name + ' was added to your account.' : 'Changes to ' + name + ' were saved.');
    });
  });

  /* ---------- DELETE ---------- */
  var deletingId = null;

  function openDelete(dbId) {
    var m = serverMembers[Number(dbId)];
    if (!m) { toast('Reload the page and try again.'); return; }
    deletingId = Number(dbId);
    el('deleteErr').textContent = '';
    el('deleteText').textContent = 'Remove ' + m.name + ' (' + m.email + ') from this household?';
    openModal('deleteModal');
  }

  el('deleteConfirmBtn').addEventListener('click', function () {
    var btn = el('deleteConfirmBtn');
    var m = serverMembers[deletingId];
    btn.disabled = true;

    BP.api('apiDeleteMember', { id: deletingId }).then(function (res) {
      btn.disabled = false;
      if (!res.ok) {
        if (handleAuthError(res)) return;
        el('deleteErr').textContent = res.message || 'Could not remove the member.';
        return;
      }
      closeModal('deleteModal');
      applyMembers(res.members || []);
      toast((m ? m.name : 'The member') + ' was removed.');
    });
  });

  /* Draw straight away from this browser's copy, then refresh from MySQL. */
  paintAll();
  loadMembers();
})();
