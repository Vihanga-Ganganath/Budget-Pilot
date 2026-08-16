/* Budget Pilot — budget setup */

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
  var cards = {};

  /* --------------------------------------------------------------------
     CATEGORY IMAGES — add your file paths here.

     Download whatever pictures you like, put them in an "img" folder next
     to this project, and fill in the paths below. Anything left empty just
     shows a plain tinted band until you add it.

     Example:  grocery: 'img/grocery.jpg',
     -------------------------------------------------------------------- */
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

  /* Colour shown behind a category until its image is added. */
  var TINTS = {
    grocery:   ['#A8E0BC', '#3C8F60'],
    transport: ['#9BCDF0', '#3A7CB4'],
    rent:      ['#A9BCE8', '#3A4A85'],
    loan:      ['#B4A8E2', '#453A7C'],
    tuition:   ['#EBC48C', '#96591F'],
    medicine:  ['#EFA9B4', '#96354A'],
    clothing:  ['#AEB8CC', '#454E62'],
    other:     ['#9AA4BC', '#404A61']
  };

  function applyArt(node, category) {
    var tint = TINTS[category.key] || TINTS.other;
    node.style.background = 'linear-gradient(150deg, ' + tint[0] + ', ' + tint[1] + ')';

    var img = node.querySelector('.cat__photo');
    var source = (PHOTOS[category.key] || '').trim();

    if (!source) {
      img.remove();            // nothing set yet — leave the tinted band
      return;
    }

    img.src = source;
    img.alt = category.label;

    /* A wrong path shouldn't leave a broken-image icon on the card. */
    img.addEventListener('error', function () {
      img.remove();
      if (window.console) {
        console.warn('Budget Pilot: could not load "' + source + '" for ' + category.label + '.');
      }
    });
  }

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
    var n = Math.round(Number(value) || 0);
    return symbol + n.toLocaleString('en-US');
  }

  function incomeValue() {
    var n = Number(el('income').value);
    return isFinite(n) && n > 0 ? n : 0;
  }

  function plannedTotal() {
    return BP.CATEGORIES.reduce(function (sum, c) {
      return sum + (Number(budget.categories[c.key].planned) || 0);
    }, 0);
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
     Cards
     ================================================================== */

  function buildCards() {
    var grid = el('categoryGrid');
    var tpl = el('categoryTemplate');
    grid.textContent = '';

    BP.CATEGORIES.forEach(function (category) {
      var node = tpl.content.firstElementChild.cloneNode(true);

      applyArt(node.querySelector('.cat__art'), category);

      node.querySelector('.cat__name').textContent = category.label;
      node.querySelector('.cat__symbol').textContent = symbol;

      var input = node.querySelector('.cat__amount');
      input.id = 'plan-' + category.key;
      input.setAttribute('aria-label', category.label + ' budget');

      input.addEventListener('input', function () {
        var value = Number(input.value);
        budget.categories[category.key].planned = isFinite(value) && value > 0 ? value : 0;
        paintCard(category.key);
        paintTotal();
      });

      grid.appendChild(node);
      cards[category.key] = node;
    });
  }

  function paintCard(key) {
    var node = cards[key];
    var row = budget.categories[key];
    var planned = Number(row.planned) || 0;
    var spent = Number(row.spent) || 0;

    var input = node.querySelector('.cat__amount');
    if (document.activeElement !== input) input.value = planned ? planned : '';

    node.querySelector('.cat__suggested').textContent = row.suggested != null ? money(row.suggested) : '\u2014';
    node.querySelector('.cat__spent').textContent = money(spent);

    /* Nothing has been spent yet, so the bar sits at zero until it is. */
    var percent = planned > 0 ? Math.round(spent / planned * 100) : 0;
    var capped = Math.min(percent, 100);

    node.querySelector('.cat__fill').style.width = capped + '%';
    node.querySelector('.cat__used').textContent = percent + '% used';

    var left = planned - spent;
    node.querySelector('.cat__left').textContent = left >= 0
      ? money(left) + ' left'
      : money(Math.abs(left)) + ' over';

    node.classList.toggle('is-full', percent >= 100 && percent < 105);
    node.classList.toggle('is-over', percent > 100);
  }

  function paintTotal() {
    var total = plannedTotal();
    var income = incomeValue();
    var card = document.querySelector('.total');

    el('totalPlanned').textContent = money(total);

    var percent = income > 0 ? Math.min(Math.round(total / income * 100), 100) : 0;
    el('totalFill').style.width = percent + '%';

    var over = income > 0 && total > income;
    card.classList.toggle('is-over', over);

    /* Income can rise on its own — someone records financial data in Settings
       and the household total goes up. The plan is not rewritten when that
       happens; the gap simply shows here as unallocated money. */
    var chip = el('unallocated');
    var left = income - total;

    if (!income) {
      el('totalHint').textContent = 'Set your income, then auto-suggest a plan.';
      chip.hidden = true;
    } else {
      el('totalHint').textContent = 'of ' + money(income) + ' income.';
      chip.hidden = false;
      chip.classList.toggle('total__chip--over', left < 0);
      el('unallocatedLabel').textContent = left < 0 ? 'Over income' : 'Unallocated';
      el('unallocatedValue').textContent = money(Math.abs(left));
    }
  }

  function paintAll() {
    BP.CATEGORIES.forEach(function (category) { paintCard(category.key); });
    paintTotal();
  }

  /* ==================================================================
     Auto-suggest
     ================================================================== */

  el('suggestBtn').addEventListener('click', function () {
    var income = incomeValue();

    if (!income) {
      el('incomeErr').textContent = shared
        ? "Add at least one person's salary first."
        : 'Enter your monthly income first.';
      (shared ? el('salaryList').querySelector('input') || el('income') : el('income')).focus();
      return;
    }
    el('incomeErr').textContent = '';

    var plan = BP.suggestBudget(income);

    BP.CATEGORIES.forEach(function (c) {
      var amount = plan.allocations[c.key];
      budget.categories[c.key].planned = amount;
      budget.categories[c.key].suggested = amount;
    });

    paintAll();

    say(plan.buffer > 0
      ? 'Plan drafted. ' + money(plan.buffer) + ' left unallocated as a cushion.'
      : 'Plan drafted across ' + BP.CATEGORIES.length + ' categories. Adjust any of them.');
  });

  /* ==================================================================
     Income field
     ================================================================== */

  /* ==================================================================
     Income field

     On a shared account the plan is built on what everyone earns together.
     The account holder enters each person's salary here; a member cannot add
     or change any of them, including their own.
     ================================================================== */

  var people = BP.memberIncomes(profileId);
  var iAmMain = BP.isMain(profileId);
  var shared = people.length > 1;

  function paintFamilyIncome() {
    var total = BP.familyIncome(profileId);
    el('familyIncome').textContent = money(total);
    el('income').value = total || '';
    budget.income = String(total || '');
    paintTotal();
  }

  function buildSalaries() {
    if (!shared) return;

    el('salaries').hidden = false;

    /* The single income box becomes the read-only sum of the salaries. */
    el('income').readOnly = true;
    el('income').setAttribute('aria-readonly', 'true');
    el('incomeLabel').textContent = 'Total monthly family income';

    el('salariesHint').textContent = iAmMain
      ? "Enter each person's monthly salary. Together they make the income the plan is built on."
      : 'Salaries are set by the account holder. The plan is built on the household total.';

    var list = el('salaryList');
    list.textContent = '';

    people.forEach(function (person) {
      var li = document.createElement('li');
      li.className = 'salary';

      var name = document.createElement('span');
      name.className = 'salary__name';
      name.textContent = person.name;
      if (person.id === profileId) {
        var you = document.createElement('span');
        you.className = 'salary__you';
        you.textContent = 'you';
        name.appendChild(you);
      }
      li.appendChild(name);

      var control = document.createElement('span');
      control.className = 'control control--money salary__control';

      var prefix = document.createElement('span');
      prefix.className = 'control__prefix';
      prefix.textContent = symbol;
      control.appendChild(prefix);

      var input = document.createElement('input');
      input.type = 'number';
      input.min = '0';
      input.step = '1';
      input.placeholder = '0';
      input.value = person.income || '';
      input.setAttribute('aria-label', 'Monthly salary for ' + person.name);

      /* Once someone has income sources recorded in Settings, that total is
         their salary — typing over it here would only put the two figures out
         of step, so the box shows it and stays shut. */
      if (person.hasSources) {
        input.readOnly = true;
        input.setAttribute('aria-readonly', 'true');
        var from = document.createElement('span');
        from.className = 'salary__from';
        from.textContent = 'sign-up income + financial data';
        name.appendChild(from);
      } else if (!iAmMain) {
        /* Only the holder edits these. */
        input.readOnly = true;
        input.setAttribute('aria-readonly', 'true');
      } else {
        input.addEventListener('input', function () {
          var result = BP.setMemberIncome(profileId, person.id, input.value);
          if (!result || !result.ok) {
            say("Couldn't save that salary.");
            return;
          }
          person.income = Number(input.value) || 0;
          el('incomeErr').textContent = '';
          paintFamilyIncome();
        });
      }

      control.appendChild(input);
      li.appendChild(control);
      list.appendChild(li);
    });

    paintFamilyIncome();
  }

  /* A lone profile plans on what they earn. Once they have recorded income
     sources in Settings, that total is the income and the box is not typed
     into — adding financial data is what changes it. */
  function applyOwnIncome() {
    if (shared) return;

    var derived = Array.isArray(profile.incomeSources) && profile.incomeSources.length > 0;
    var live = BP.personIncome(profile) || Number(budget.income) || 0;

    el('income').value = live || '';
    budget.income = String(live || '');

    if (derived) {
      el('income').readOnly = true;
      el('income').setAttribute('aria-readonly', 'true');
      el('incomeLabel').textContent = 'Monthly income (sign-up income + financial data)';
    }
  }

  el('income').addEventListener('input', function () {
    if (shared) return;              /* derived from the salaries above */
    el('incomeErr').textContent = '';
    budget.income = el('income').value;
    paintTotal();
  });

  /* ==================================================================
     Saving
     ================================================================== */

  function persist(finalized) {
    budget.income = shared ? String(BP.familyIncome(profileId)) : el('income').value;
    budget.finalized = !!finalized;

    /* A lone profile typing straight into the box: that is their starting
       figure, so anything recorded under Financial Data still adds on top. */
    if (!shared && !el('income').readOnly) {
      BP.setBaseIncome(profileId, profileId, el('income').value);
    }

    budget.savedAt = new Date().toISOString();

    var result = BP.saveBudget(profileId, budget);
    if (!result.ok) {
      say("Couldn't save your budget. Storage may be full.");
      return false;
    }
    profile = BP.getProfile(profileId);
    showSavedNote();
    return true;
  }

  function showSavedNote() {
    if (!budget.savedAt) { el('savedNote').textContent = ''; return; }
    var when = new Date(budget.savedAt);
    el('savedNote').textContent = (budget.finalized ? 'Plan finalized ' : 'Saved ') +
      when.toLocaleDateString(undefined, { day:'numeric', month:'short' }) + ' at ' +
      when.toLocaleTimeString(undefined, { hour:'2-digit', minute:'2-digit' }) + '.';
  }

  el('saveBtn').addEventListener('click', function () {
    if (!incomeValue()) {
      el('incomeErr').textContent = 'Enter your monthly income first.';
      el('income').focus();
      return;
    }
    if (persist(budget.finalized)) say('Configuration saved.');
  });

  el('finalizeBtn').addEventListener('click', function () {
    var income = incomeValue();
    var total = plannedTotal();

    if (!income) {
      el('incomeErr').textContent = 'Enter your monthly income first.';
      el('income').focus();
      return;
    }
    if (total <= 0) {
      say('Set at least one category before finalizing.');
      return;
    }

    var over = total > income;
    var leftover = income - total;

    openModal('Finalize monthly plan', function (body) {
      var p1 = document.createElement('p');
      p1.textContent = 'Planned ' + money(total) + ' against income of ' + money(income) + '.';
      body.appendChild(p1);

      var p2 = document.createElement('p');
      p2.textContent = over
        ? 'That is ' + money(total - income) + ' more than you earn. You can still finalize, but the plan will not balance.'
        : leftover > 0
          ? money(leftover) + ' stays unallocated, which is available for savings or unplanned costs.'
          : 'Every currency unit is allocated.';
      body.appendChild(p2);
    }, [
      { label: 'Keep editing' },
      { label: 'Finalize', style: 'btn--primary', action: function () {
          closeModal();
          if (persist(true)) say('Monthly plan finalized.');
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

  /* Arriving from checkout: confirm what was just recorded. */
  var spent = (new RegExp('[?&]spent=([^&]*)').exec(window.location.search) || [])[1];
  if (spent) {
    var amount = Number(decodeURIComponent(spent));
    if (isFinite(amount) && amount > 0) {
      var row = budget.categories.grocery;
      var left = (Number(row.planned) || 0) - (Number(row.spent) || 0);
      window.setTimeout(function () {
        say(money(amount) + ' recorded against Grocery. ' + (left >= 0
          ? money(left) + ' left in that budget.'
          : money(Math.abs(left)) + ' over that budget.'));
      }, 200);
    }
  }

  el('incomeSymbol').textContent = symbol;
  applyOwnIncome();
  buildSalaries();
  buildCards();
  paintAll();
  showSavedNote();
})();
