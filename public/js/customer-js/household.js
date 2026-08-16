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
    window.location.replace('customer-login.html?signin=required');
    return;
  }

  var CURRENCIES = { USD: '$', EUR: '\u20AC', GBP: '\u00A3', LKR: 'Rs', INR: '\u20B9', AUD: '$' };
  var prefs = profile.prefs || {};
  var symbol = CURRENCIES[prefs.currency] || '$';

  if (prefs.darkMode) document.body.classList.add('is-dark');

  el('logoutBtn').addEventListener('click', function () {
    BP.clearSession();
    window.location.href = 'customer-login.html';
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

  var budget = BP.getBudget(profileId) || { categories: {} };
  var people = BP.memberSpend(profileId);
  var entries = BP.allExpenses(profileId).slice().reverse();   /* newest first */

  var planned = 0;
  var spent = 0;
  BP.CATEGORIES.forEach(function (c) {
    var row = budget.categories[c.key] || {};
    planned += Number(row.planned) || 0;
    spent += Number(row.spent) || 0;
  });

  el('sumPlanned').textContent = planned > 0 ? money(planned) : 'Not set';
  el('sumSpent').textContent = money(spent);
  el('sumLeft').textContent = planned > 0 ? money(planned - spent) : '—';
  if (planned > 0 && planned - spent < 0) el('sumLeft').classList.add('is-over');

  el('pageSub').textContent = people.length > 1
    ? people.length + ' people spend from one budget. Only you can see this page.'
    : 'Everyone on this account, and what they have spent.';

  /* ==================================================================
     People
     ================================================================== */

  function paintPeople() {
    var list = el('people');
    list.textContent = '';

    if (people.length <= 1) {
      el('peopleEmpty').hidden = false;
    }

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
      meta.textContent = person.count === 0
        ? 'No transactions yet'
        : person.count + (person.count === 1 ? ' transaction' : ' transactions');
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

  paintPeople();
  paintChips();
  paintFeed();
})();
