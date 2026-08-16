/* Budget Pilot — manual expense entry
   Logs one or more expenses and adds each amount to its category's `spent`,
   which is what shrinks the remaining budget on the Budgets page. */

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

  var CURRENCIES = { USD:'$', EUR:'\u20AC', GBP:'\u00A3', LKR:'Rs', INR:'\u20B9', AUD:'$' };
  var prefs = profile.prefs || {};
  var symbol = CURRENCIES[prefs.currency] || '$';

  var budget = BP.getBudget(profileId);

  /* Same image paths as the Budgets page. Anything left empty falls back to a
     plain tile instead of a broken picture. */
  var PHOTOS = {
    grocery:   'img/grocery.jpg',
    transport: 'img/transport.jpg',
    rent:      'img/rent.jpg',
    loan:      'img/loan.jpg',
    tuition:   'img/tution.jpg',
    medicine:  'img/medicine.jpg',
    clothing:  'img/clothing.jpg',
    other:     ''
  };

  var TAGS = ['Business', 'TaxDeductible', 'Vacation'];

  /* ==================================================================
     Helpers
     ================================================================== */

  var toast = el('toast');
  var toastTimer = null;

  function say(message) {
    toast.textContent = message;
    toast.hidden = false;
    window.clearTimeout(toastTimer);
    toastTimer = window.setTimeout(function () { toast.hidden = true; }, 3400);
  }

  function money(value) {
    var n = Number(value) || 0;
    return symbol + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function whole(value) {
    return symbol + Math.round(Number(value) || 0).toLocaleString('en-US');
  }

  function labelFor(key) {
    var match = BP.CATEGORIES.filter(function (c) { return c.key === key; })[0];
    return match ? match.label : '';
  }

  function today() {
    var now = new Date();
    return new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 10);
  }

  function param(name) {
    var hit = new RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
    return hit ? decodeURIComponent(hit[1].replace(/\+/g, ' ')) : '';
  }

  /* ==================================================================
     State — one object per expense being drafted
     ================================================================== */

  var seed = param('category');
  var validSeed = BP.CATEGORIES.some(function (c) { return c.key === seed; }) ? seed : '';

  var nextId = 1;
  var entries = [];
  var activeId = null;

  function blank() {
    return {
      id: nextId++,
      category: validSeed,
      amount: '',
      date: today(),
      description: '',
      notes: '',
      tags: []
    };
  }

  function entryById(id) {
    return entries.filter(function (e) { return e.id === id; })[0] || null;
  }

  function active() {
    return entryById(activeId) || entries[0] || null;
  }

  /* ==================================================================
     Entry cards
     ================================================================== */

  var list = el('entryList');
  var nodes = {};

  function buildEntry(entry) {
    var node = el('entryTemplate').content.firstElementChild.cloneNode(true);

    var amount = node.querySelector('.entry__amount');
    var date = node.querySelector('.entry__date');
    var desc = node.querySelector('.entry__desc');
    var notes = node.querySelector('.entry__notes');
    var drop = node.querySelector('.entry__drop');

    node.querySelector('.entry__symbol').textContent = symbol;

    var uid = 'entry-' + entry.id;
    amount.id = uid + '-amount';
    date.id = uid + '-date';
    desc.id = uid + '-desc';
    node.querySelector('.entry__amountlabel').setAttribute('for', amount.id);
    node.querySelector('.entry__datelabel').setAttribute('for', date.id);
    node.querySelector('.entry__desclabel').setAttribute('for', desc.id);

    amount.value = entry.amount;
    date.value = entry.date;
    desc.value = entry.description;
    notes.value = entry.notes;

    amount.addEventListener('input', function () {
      entry.amount = amount.value;
      node.classList.remove('is-flagged');
      paintReview();
    });
    date.addEventListener('input', function () { entry.date = date.value; });
    desc.addEventListener('input', function () { entry.description = desc.value; });
    notes.addEventListener('input', function () { entry.notes = notes.value; });

    /* Clicking anywhere in a card makes it the one the category panel edits. */
    node.addEventListener('focusin', function () { focusEntry(entry.id); });
    node.addEventListener('click', function () { focusEntry(entry.id); });

    var tagWrap = node.querySelector('.entry__tags');
    TAGS.forEach(function (name) {
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'tag';
      btn.textContent = '#' + name;
      btn.setAttribute('aria-pressed', 'false');
      btn.addEventListener('click', function () {
        var at = entry.tags.indexOf(name);
        if (at === -1) entry.tags.push(name); else entry.tags.splice(at, 1);
        var on = entry.tags.indexOf(name) !== -1;
        btn.classList.toggle('is-on', on);
        btn.setAttribute('aria-pressed', on ? 'true' : 'false');
      });
      tagWrap.appendChild(btn);
    });

    drop.addEventListener('click', function (event) {
      event.stopPropagation();
      removeEntry(entry.id);
    });

    nodes[entry.id] = node;
    list.appendChild(node);
    paintEntry(entry);
  }

  function paintEntry(entry) {
    var node = nodes[entry.id];
    if (!node) return;

    var chip = node.querySelector('.entry__chip');
    chip.textContent = entry.category ? labelFor(entry.category) : 'No category';
    chip.classList.toggle('is-set', !!entry.category);

    /* A lone expense has nothing to be removed in favour of. */
    node.querySelector('.entry__drop').hidden = entries.length < 2;
    node.classList.toggle('is-active', entry.id === activeId && entries.length > 1);
  }

  function paintAllEntries() {
    entries.forEach(paintEntry);
  }

  function focusEntry(id) {
    if (activeId === id) return;
    activeId = id;
    paintAllEntries();
    paintPicks();
  }

  function addEntry() {
    var entry = blank();
    entries.push(entry);
    buildEntry(entry);
    focusEntry(entry.id);
    paintAllEntries();
    paintReview();
    var input = nodes[entry.id].querySelector('.entry__amount');
    if (input) input.focus();
  }

  function removeEntry(id) {
    if (entries.length < 2) return;
    entries = entries.filter(function (e) { return e.id !== id; });
    if (nodes[id]) nodes[id].remove();
    delete nodes[id];
    if (activeId === id) activeId = entries[0].id;
    paintAllEntries();
    paintPicks();
    paintReview();
  }

  el('addEntryBtn').addEventListener('click', addEntry);

  /* ==================================================================
     Category selection — applies to the focused expense
     ================================================================== */

  var pickNodes = {};

  function buildPicks() {
    var grid = el('pickGrid');
    var tpl = el('pickTemplate');
    grid.textContent = '';

    BP.CATEGORIES.forEach(function (category) {
      var node = tpl.content.firstElementChild.cloneNode(true);
      var img = node.querySelector('.pick__photo');
      var source = (PHOTOS[category.key] || '').trim();

      node.querySelector('.pick__name').textContent = category.label;
      node.setAttribute('aria-label', category.label);

      if (!source) {
        node.classList.add('pick--plain');
        node.querySelector('.pick__art').remove();
      } else {
        img.src = source;
        img.alt = '';
        img.addEventListener('error', function () {
          node.classList.add('pick--plain');
          var art = node.querySelector('.pick__art');
          if (art) art.remove();
        });
      }

      node.addEventListener('click', function () {
        var entry = active();
        if (!entry) return;
        entry.category = entry.category === category.key ? '' : category.key;
        el('pickErr').textContent = '';
        paintEntry(entry);
        paintPicks();
        paintReview();
      });

      pickNodes[category.key] = node;
      grid.appendChild(node);
    });
  }

  function paintPicks() {
    var entry = active();
    var chosen = entry ? entry.category : '';

    BP.CATEGORIES.forEach(function (c) {
      var node = pickNodes[c.key];
      if (node) node.setAttribute('aria-pressed', chosen === c.key ? 'true' : 'false');
    });

    el('pickFor').textContent = entries.length > 1
      ? 'Applies to expense ' + (entries.indexOf(entry) + 1) + ' of ' + entries.length + '.'
      : 'Where this expense is charged.';
  }

  /* ==================================================================
     Final review — what each category looks like after saving
     ================================================================== */

  function draftTotals() {
    var byKey = {};
    var total = 0;

    entries.forEach(function (entry) {
      var amount = Number(entry.amount) || 0;
      if (!entry.category || amount <= 0) return;
      byKey[entry.category] = (byKey[entry.category] || 0) + amount;
      total += amount;
    });

    return { byKey: byKey, total: total };
  }

  function paintReview() {
    var draft = draftTotals();
    var wrap = el('impactList');
    var anyOver = false;

    el('reviewTotal').textContent = money(draft.total);
    wrap.textContent = '';

    var keys = BP.CATEGORIES.map(function (c) { return c.key; })
      .filter(function (k) { return draft.byKey[k]; });

    keys.forEach(function (key) {
      var row = budget.categories[key] || { planned: 0, spent: 0 };
      var planned = Number(row.planned) || 0;
      var spent = Number(row.spent) || 0;
      var adding = draft.byKey[key];
      var after = spent + adding;
      var left = planned - after;
      var over = planned > 0 && after > planned;
      if (over) anyOver = true;

      var li = document.createElement('li');
      li.className = 'impact__row' + (over ? ' is-over' : '');

      var line = document.createElement('p');
      line.className = 'impact__line';
      var name = document.createElement('span');
      name.textContent = labelFor(key);
      var amount = document.createElement('span');
      amount.textContent = '\u2212' + money(adding);
      line.appendChild(name);
      line.appendChild(amount);
      li.appendChild(line);

      var note = document.createElement('p');
      note.className = 'impact__note';
      if (planned <= 0) {
        note.textContent = 'No budget set for this category yet.';
      } else if (over) {
        note.textContent = whole(after) + ' of ' + whole(planned) + ' — ' + whole(Math.abs(left)) + ' over.';
      } else {
        note.textContent = whole(after) + ' of ' + whole(planned) + ' — ' + whole(left) + ' left.';
      }
      li.appendChild(note);

      var bar = document.createElement('div');
      bar.className = 'impact__bar';
      var fill = document.createElement('span');
      fill.className = 'impact__fill';
      fill.style.width = (planned > 0 ? Math.min(Math.round(after / planned * 100), 100) : 0) + '%';
      bar.appendChild(fill);
      li.appendChild(bar);

      wrap.appendChild(li);
    });

    el('reviewEmpty').hidden = keys.length > 0;
    document.querySelector('.review').classList.toggle('is-over', anyOver);
    el('saveExpenseBtn').disabled = draft.total <= 0;
  }

  /* ==================================================================
     Modal
     ================================================================== */

  var modal = el('modal');

  function closeModal() {
    modal.hidden = true;
    el('modalBody').textContent = '';
    el('modalActions').textContent = '';
  }

  function openModal(title, buildBody, buttons) {
    el('modalTitle').textContent = title;
    el('modalBody').textContent = '';
    el('modalActions').textContent = '';
    buildBody(el('modalBody'));

    buttons.forEach(function (spec) {
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'btn ' + (spec.style || 'btn--ghost');
      btn.textContent = spec.label;
      btn.addEventListener('click', function () {
        if (spec.action) spec.action(); else closeModal();
      });
      el('modalActions').appendChild(btn);
    });

    modal.hidden = false;
    var first = el('modalActions').querySelector('button');
    if (first) first.focus();
  }

  el('modalBackdrop').addEventListener('click', closeModal);
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && !modal.hidden) closeModal();
  });

  /* ==================================================================
     Saving
     ================================================================== */

  function firstProblem() {
    for (var i = 0; i < entries.length; i++) {
      var entry = entries[i];
      var amount = Number(entry.amount) || 0;
      if (amount > 0 && !entry.category) return { entry: entry, why: 'category' };
      if (amount <= 0 && entry.category) return { entry: entry, why: 'amount' };
    }
    return null;
  }

  el('saveExpenseBtn').addEventListener('click', function () {
    var draft = draftTotals();

    if (draft.total <= 0) {
      say('Enter an amount and pick a category first.');
      return;
    }

    var problem = firstProblem();
    if (problem) {
      focusEntry(problem.entry.id);
      if (problem.why === 'category') {
        el('pickErr').textContent = 'Pick a category for this expense.';
        say('One expense has an amount but no category.');
      } else {
        var node = nodes[problem.entry.id];
        if (node) {
          node.classList.add('is-flagged');
          node.querySelector('.entry__amount').focus();
        }
        say('One expense has a category but no amount.');
      }
      return;
    }

    var lines = BP.CATEGORIES
      .filter(function (c) { return draft.byKey[c.key]; })
      .map(function (c) { return c.label + ' ' + money(draft.byKey[c.key]); });

    openModal('Record this spending', function (body) {
      var p1 = document.createElement('p');
      p1.textContent = 'Recording ' + money(draft.total) + ' across ' + lines.length +
        ' categor' + (lines.length === 1 ? 'y' : 'ies') + ': ' + lines.join(', ') + '.';
      body.appendChild(p1);

      var p2 = document.createElement('p');
      p2.textContent = 'Each amount comes off what is left in that budget. This cannot be undone from here.';
      body.appendChild(p2);
    }, [
      { label: 'Keep editing' },
      { label: 'Record spending', style: 'btn--primary', action: function () {
          var payload = entries
            .filter(function (e) { return (Number(e.amount) || 0) > 0 && e.category; })
            .map(function (e) {
              return {
                category: e.category,
                amount: Number(e.amount),
                date: e.date,
                description: e.description,
                notes: e.notes,
                tags: e.tags
              };
            });

          var result = BP.addExpenses(profileId, payload);
          closeModal();

          if (!result.ok) {
            say("Couldn't save that. Storage may be full.");
            return;
          }

          window.location.href = 'budgets.html?logged=' + encodeURIComponent(result.total.toFixed(2));
        } }
    ]);
  });

  /* ==================================================================
     Chrome
     ================================================================== */

  var avatar = el('appbarAvatar');
  if (profile.avatar) {
    avatar.style.backgroundImage = 'url(' + profile.avatar + ')';
  } else {
    avatar.style.backgroundColor = BP.avatarColor(profile.email || profile.name);
    avatar.textContent = BP.initials(profile.name);
  }

  if (prefs.darkMode) document.body.classList.add('is-dark');

  el('logoutBtn').addEventListener('click', function () {
    BP.clearSession();
    window.location.href = 'customer-login.html';
  });

  document.querySelectorAll('[data-soon]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      say(btn.getAttribute('data-soon') + ' is not built yet.');
    });
  });

  /* ==================================================================
     Start
     ================================================================== */

  var first = blank();
  entries.push(first);
  activeId = first.id;

  buildEntry(first);
  buildPicks();
  paintPicks();
  paintReview();

  if (validSeed) {
    var input = nodes[first.id].querySelector('.entry__amount');
    if (input) input.focus();
  }
})();
