/* Budget Pilot — analytics

   Everything on this page is derived from the signed-in profile: the budget
   saved on the Budgets page and whatever expenses have been logged (manual
   entry or cart checkout). Nothing here is hard-coded sample data, so an
   account with no history shows empty states rather than invented figures. */

(function () {
  'use strict';

  var BP = window.BudgetPilot;
  if (!BP) return;

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

  var budget = BP.getBudget(profileId) || {};
  if (!budget.categories) budget.categories = {};

  /* --------------------------------------------------------------------
     BENCHMARKS — optional, and empty on purpose.

     Brand shares are worked out from your own checked-out shopping lists,
     and each brand is compared against how much of your basket it usually
     takes. If you ever have real community figures to compare against, put
     them here as brand-name -> percentage and they take over as the
     comparison line for that brand:

        var BENCHMARKS = { 'keells': 41, 'cargills': 38 };
     -------------------------------------------------------------------- */
  var BENCHMARKS = {};

  /* ==================================================================
     Small helpers
     ================================================================== */

  var toast = el('toast');
  var toastTimer = null;

  function say(message) {
    if (!toast) return;
    toast.textContent = message;
    toast.hidden = false;
    window.clearTimeout(toastTimer);
    toastTimer = window.setTimeout(function () { toast.hidden = true; }, 3400);
  }

  function money(value) {
    return symbol + Math.round(Number(value) || 0).toLocaleString('en-US');
  }

  function money2(value) {
    return symbol + Math.abs(Number(value) || 0)
      .toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function pct(part, whole) {
    return whole > 0 ? Math.round(part / whole * 100) : 0;
  }

  function label(key) {
    for (var i = 0; i < BP.CATEGORIES.length; i++) {
      if (BP.CATEGORIES[i].key === key) return BP.CATEGORIES[i].label;
    }
    return key ? key.charAt(0).toUpperCase() + key.slice(1) : 'Other';
  }

  function text(node, value) { if (node) node.textContent = value; }

  /* ==================================================================
     Period
     ================================================================== */

  var now = new Date();
  var monthStart = new Date(now.getFullYear(), now.getMonth(), 1);
  var monthEnd = new Date(now.getFullYear(), now.getMonth() + 1, 0);
  var daysInMonth = monthEnd.getDate();
  var dayOfMonth = now.getDate();
  var daysLeft = Math.max(daysInMonth - dayOfMonth, 0);

  function longDate(date) {
    return date.toLocaleDateString(undefined, { month:'long', day:'numeric', year:'numeric' });
  }

  function shortDate(date) {
    return date.toLocaleDateString(undefined, { month:'short', day:'numeric', year:'numeric' });
  }

  text(el('reportRange'), longDate(monthStart) + ' \u2013 ' + longDate(monthEnd));

  /* ==================================================================
     Transactions

     The store has grown a few different shapes over the life of this app,
     so read whichever one is present instead of assuming.
     ================================================================== */

  function rawTransactions() {
    var readers = [
      /* The report describes the household: everyone spends from one budget,
         so the totals, categories and insights are the family's. */
      function () { return typeof BP.familyExpenses === 'function' ? BP.familyExpenses(profileId) : null; },
      function () { return typeof BP.getExpenses === 'function' ? BP.getExpenses(profileId) : null; },
      function () { return typeof BP.getTransactions === 'function' ? BP.getTransactions(profileId) : null; },
      function () { return typeof BP.getLog === 'function' ? BP.getLog(profileId) : null; },
      function () { return budget.expenses; },
      function () { return budget.log; },
      function () { return budget.transactions; },
      function () { return profile.expenses; },
      function () { return profile.transactions; }
    ];

    for (var i = 0; i < readers.length; i++) {
      try {
        var list = readers[i]();
        if (list && list.length) return Array.prototype.slice.call(list);
      } catch (e) { /* reader not available — try the next one */ }
    }
    return [];
  }

  function normalize(item) {
    if (!item || typeof item !== 'object') return null;

    var raw = item.amount != null ? item.amount : (item.value != null ? item.value : item.total);
    var amount = Number(raw);
    if (!isFinite(amount) || amount === 0) return null;

    var kind = String(item.type || item.kind || '').toLowerCase();
    var isIncome = kind === 'income' || kind === 'credit' || (!kind && amount < 0 && item.income);

    var stamp = item.date || item.at || item.loggedAt || item.createdAt || item.savedAt || item.when;
    var date = stamp ? new Date(stamp) : null;
    if (!date || isNaN(date.getTime())) date = new Date();

    var key = item.category || item.categoryKey || item.key || 'other';

    return {
      date: date,
      name: item.merchant || item.shop || item.brand || item.label ||
            item.note || item.name || item.description || label(key),
      merchant: item.merchant || item.shop || item.brand || '',
      category: key,
      categoryLabel: label(key),
      amount: Math.abs(amount),
      income: !!isIncome
    };
  }

  var iAmMain = typeof BP.isMain === 'function' && BP.isMain(profileId);

  function isMine(row) {
    if (!row || !row.by) return iAmMain;   /* rows logged before members existed */
    return row.by === profileId;
  }

  var transactions = [];
  var rows = rawTransactions();
  for (var t = 0; t < rows.length; t++) {
    var entry = normalize(rows[t]);
    if (entry) {
      entry.mine = isMine(rows[t]);
      entry.byName = (rows[t] && rows[t].byName) || '';
      transactions.push(entry);
    }
  }

  function inMonth(entry, date) {
    return entry.date.getFullYear() === date.getFullYear() &&
           entry.date.getMonth() === date.getMonth();
  }

  var prevMonthDate = new Date(now.getFullYear(), now.getMonth() - 1, 1);

  var thisMonthTx = transactions.filter(function (e) { return inMonth(e, now); });
  var prevMonthTx = transactions.filter(function (e) { return inMonth(e, prevMonthDate); });

  function sum(list, wantIncome) {
    return list.reduce(function (total, e) {
      return e.income === !!wantIncome ? total + e.amount : total;
    }, 0);
  }

  /* ==================================================================
     Headline numbers

     Category `spent` on the budget is the authoritative running total, so
     that drives the report; the log fills in the detail rows.
     ================================================================== */

  var perCategory = BP.CATEGORIES.map(function (category) {
    var row = budget.categories[category.key] || {};
    return {
      key: category.key,
      label: category.label,
      planned: Number(row.planned) || 0,
      spent: Number(row.spent) || 0
    };
  });

  var planned = perCategory.reduce(function (a, c) { return a + c.planned; }, 0);
  var spent = perCategory.reduce(function (a, c) { return a + c.spent; }, 0);
  var income = Number(budget.income) || Number(profile.income) || 0;

  /* Fall back to the log if no category totals have been recorded. */
  if (!spent) spent = sum(thisMonthTx, false);

  var target = planned > 0 ? planned : income;
  var remaining = target - spent;
  var usedPercent = pct(spent, target);

  /* Pace: what this month lands at if the rest of it looks like the days
     already spent. */
  var dailyRate = dayOfMonth > 0 ? spent / dayOfMonth : 0;
  var projected = dailyRate * daysInMonth;
  var projectedSurplus = target - projected;
  var onTrack = target > 0 ? projected <= target : true;

  var dailyAllowance = daysLeft > 0 ? remaining / daysLeft : remaining;

  /* ---------- Spend vs budget card ---------- */

  var chip = el('paceChip');
  if (target <= 0) {
    text(chip, 'No budget set');
    chip.classList.add('is-warn');
  } else if (remaining < 0) {
    text(chip, 'Over budget');
    chip.classList.add('is-warn');
  } else if (onTrack) {
    text(chip, 'On track');
  } else {
    text(chip, 'Spending fast');
    chip.classList.add('is-warn');
  }

  text(el('progressFigures'), money(spent) + ' / ' + money(target));

  var fill = el('progressFill');
  fill.style.width = Math.min(usedPercent, 100) + '%';
  if (remaining < 0) fill.classList.add('is-over');

  var copy = el('progressCopy');
  copy.textContent = '';

  function frag(value, className) {
    var node = document.createElement(className ? 'span' : 'b');
    if (className) node.className = className;
    node.textContent = value;
    return node;
  }

  function line(parts) {
    parts.forEach(function (part) {
      copy.appendChild(typeof part === 'string' ? document.createTextNode(part) : part);
    });
  }

  if (target <= 0) {
    line(['Set your monthly limits on the ']);
    var link = document.createElement('a');
    link.href = 'budgets.html';
    link.textContent = 'Budgets page';
    copy.appendChild(link);
    copy.appendChild(document.createTextNode(' and this report fills in from there.'));
  } else if (remaining < 0) {
    line([
      'You have spent ', frag(usedPercent + '%'), ' of your monthly budget \u2014 ',
      frag(money(Math.abs(remaining)), 'an-warn'), ' past the limit with ',
      daysLeft + ' day' + (daysLeft === 1 ? '' : 's'), ' still to go.'
    ]);
  } else {
    line([
      'You have consumed ', frag(usedPercent + '%'), ' of your monthly budget. With ',
      daysLeft + ' day' + (daysLeft === 1 ? '' : 's') + ' remaining, your average daily spend limit is ',
      frag(money(dailyAllowance)), ' to stay within target.'
    ]);
  }

  text(el('remainingValue'), (remaining < 0 ? '\u2212' : '') + money(Math.abs(remaining)));

  var projectionStat = el('projectionStat');
  if (projectedSurplus >= 0) {
    text(el('projectionLabel'), 'Projected surplus');
    text(el('projectionValue'), money(projectedSurplus));
  } else {
    projectionStat.classList.remove('an-stat--good');
    projectionStat.classList.add('an-stat--bad');
    text(el('projectionLabel'), 'Projected overspend');
    text(el('projectionValue'), money(Math.abs(projectedSurplus)));
  }

  /* ---------- Savings card ---------- */

  var savings = income > 0 ? income - spent : target - spent;
  var prevSpent = sum(prevMonthTx, false);
  var prevSavings = prevMonthTx.length && income > 0 ? income - prevSpent : null;

  text(el('savingsValue'), (savings < 0 ? '\u2212' : '') + money(Math.abs(savings)));
  text(el('savingsSub'), prevSavings != null ? 'This month vs previous' : 'Kept back so far this month');

  var deltaNode = el('savingsDelta');
  if (prevSavings != null && prevSavings !== 0) {
    var change = Math.round((savings - prevSavings) / Math.abs(prevSavings) * 100);
    var monthName = prevMonthDate.toLocaleDateString(undefined, { month:'short' });
    deltaNode.textContent = (change >= 0 ? '\u2197 +' : '\u2198 ') + change + '% vs ' + monthName;
    if (change < 0) deltaNode.classList.add('is-down');
  } else {
    deltaNode.hidden = true;
  }

  var goal = Number(profile.savingsGoal) || Number(profile.goal) || 0;
  var note = el('savingsNote');
  if (goal > 0 && savings > 0) {
    note.textContent = 'That is ' + pct(savings, goal) + '% of your ' + money(goal) + ' savings goal.';
  } else if (savings > 0 && income > 0) {
    note.textContent = 'You are holding on to ' + pct(savings, income) + '% of what you earned this month.';
  } else if (savings <= 0) {
    note.textContent = 'Spending has caught up with income this month. Trimming one category is usually enough to turn this around.';
  } else {
    note.hidden = true;
  }

  /* ==================================================================
     Top spending categories
     ================================================================== */

  var ICONS = {
    grocery:   '<path d="M3 9h18l-1.5 11H4.5z"/><path d="M3 9l1.5-4h15L21 9"/>',
    transport: '<path d="M4 16.5v-4l2-5h12l2 5v4"/><path d="M4 16.5h16"/><circle cx="7.5" cy="18" r="1.4"/><circle cx="16.5" cy="18" r="1.4"/>',
    rent:      '<path d="M3 10.5L12 3l9 7.5"/><path d="M5.5 9.5V20h13V9.5"/>',
    loan:      '<circle cx="12" cy="12" r="8.5"/><path d="M9.5 9.5h5M9.5 12h5M12 9.5v7"/>',
    tuition:   '<path d="M3 8.5L12 4.5l9 4-9 4z"/><path d="M7 11v5c0 1.4 2.2 2.5 5 2.5s5-1.1 5-2.5v-5"/>',
    medicine:  '<rect x="3" y="7" width="18" height="11" rx="2.5"/><path d="M9 7V5.5h6V7M12 10.5v5M9.5 13h5"/>',
    clothing:  '<path d="M8 3l4 2 4-2 4 3-2.5 3V21H6.5V9L4 6z"/>',
    other:     '<circle cx="12" cy="12" r="8.5"/><path d="M12 8v5M12 16h.01"/>'
  };

  function iconFor(key) {
    var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('viewBox', '0 0 24 24');
    svg.setAttribute('fill', 'none');
    svg.setAttribute('stroke', 'currentColor');
    svg.setAttribute('stroke-width', '1.7');
    svg.setAttribute('stroke-linecap', 'round');
    svg.setAttribute('stroke-linejoin', 'round');
    svg.setAttribute('aria-hidden', 'true');
    svg.innerHTML = ICONS[key] || ICONS.other;
    return svg;
  }

  var ranked = perCategory
    .filter(function (c) { return c.spent > 0; })
    .sort(function (a, b) { return b.spent - a.spent; })
    .slice(0, 3);

  var catList = el('topCategories');

  if (!ranked.length) {
    el('catsEmpty').hidden = false;
  } else {
    var biggest = ranked[0].spent;

    ranked.forEach(function (category, index) {
      var li = document.createElement('li');
      li.className = 'an-cat';

      var art = document.createElement('span');
      art.className = 'an-cat__icon';
      art.appendChild(iconFor(category.key));
      li.appendChild(art);

      var body = document.createElement('div');

      var head = document.createElement('div');
      head.className = 'an-cat__line';

      var name = document.createElement('span');
      name.className = 'an-cat__name';
      name.textContent = category.label;

      var value = document.createElement('span');
      value.className = 'an-cat__value';
      value.textContent = money(category.spent);

      head.appendChild(name);
      head.appendChild(value);
      body.appendChild(head);

      var track = document.createElement('div');
      track.className = 'an-track';

      var bar = document.createElement('span');
      bar.className = 'an-track__fill' + (index === 1 ? ' an-cat__fill--alt' : '');
      bar.style.width = Math.max(pct(category.spent, biggest), 4) + '%';

      /* Over its own allocation reads as a warning, not a ranking. */
      if (category.planned > 0 && category.spent > category.planned) bar.classList.add('is-over');

      track.appendChild(bar);
      body.appendChild(track);

      li.appendChild(body);
      catList.appendChild(li);
    });
  }

  /* ==================================================================
     Smart insights
     ================================================================== */

  function insight(name, tone, build) {
    return { name: name, tone: tone, build: build };
  }

  var insights = [];

  var overspent = perCategory.filter(function (c) {
    return c.planned > 0 && c.spent > c.planned;
  }).sort(function (a, b) { return (b.spent - b.planned) - (a.spent - a.planned); });

  if (overspent.length) {
    var worst = overspent[0];
    insights.push(insight('Budget pressure', 'warn', function (p) {
      p.appendChild(document.createTextNode(worst.label + ' is '));
      p.appendChild(frag(money(worst.spent - worst.planned) + ' over', 'an-warn'));
      p.appendChild(document.createTextNode(' its allocation' +
        (overspent.length > 1 ? ', along with ' + (overspent.length - 1) + ' other categor' +
          (overspent.length === 2 ? 'y' : 'ies') : '') + '.'));
    }));
  }

  if (ranked.length && spent > 0) {
    var top = ranked[0];
    insights.push(insight('Where it concentrates', '', function (p) {
      p.appendChild(document.createTextNode(top.label + ' takes '));
      p.appendChild(frag(pct(top.spent, spent) + '%'));
      p.appendChild(document.createTextNode(' of everything you spent this month.'));
    }));
  }

  if (income > 0 && planned > 0 && income > planned) {
    insights.push(insight('Unallocated income', 'good', function (p) {
      p.appendChild(frag(money(income - planned)));
      p.appendChild(document.createTextNode(' of your income has no category attached to it — a natural buffer, or the start of a savings transfer.'));
    }));
  }

  var untouched = perCategory.filter(function (c) { return c.planned > 0 && c.spent === 0; });
  if (untouched.length >= 2) {
    insights.push(insight('Quiet categories', '', function (p) {
      p.appendChild(document.createTextNode(untouched.length + ' funded categories have no spending logged yet: ' +
        untouched.slice(0, 3).map(function (c) { return c.label; }).join(', ') +
        (untouched.length > 3 ? ' and others.' : '.')));
    }));
  }

  var largest = thisMonthTx.filter(function (e) { return !e.income; })
    .sort(function (a, b) { return b.amount - a.amount; })[0];

  if (largest && spent > 0 && largest.amount / spent >= 0.15) {
    insights.push(insight('Single large charge', '', function (p) {
      p.appendChild(document.createTextNode(largest.name + ' at ' + money(largest.amount) + ' is '));
      p.appendChild(frag(pct(largest.amount, spent) + '%'));
      p.appendChild(document.createTextNode(' of the month on its own.'));
    }));
  }

  if (prevMonthTx.length) {
    var swing = spent - prevSpent;
    insights.push(insight('Month on month', swing <= 0 ? 'good' : 'warn', function (p) {
      p.appendChild(document.createTextNode('Spending is '));
      p.appendChild(frag(money(Math.abs(swing)) + (swing <= 0 ? ' lower' : ' higher'),
        swing <= 0 ? null : 'an-warn'));
      p.appendChild(document.createTextNode(' than the same point last month.'));
    }));
  }

  if (!insights.length) {
    insights.push(insight('Nothing to report yet', '', function (p) {
      p.appendChild(document.createTextNode('Log a few expenses and this panel starts calling out overspending, concentration and month-on-month swings.'));
    }));
  }

  var insightList = el('insightsList');
  insights.slice(0, 4).forEach(function (item) {
    var li = document.createElement('li');
    li.className = 'an-insight' + (item.tone ? ' an-insight--' + item.tone : '');

    var name = document.createElement('span');
    name.className = 'an-insight__name';
    name.textContent = item.name;
    li.appendChild(name);

    var p = document.createElement('p');
    p.className = 'an-insight__copy';
    item.build(p);
    li.appendChild(p);

    insightList.appendChild(li);
  });

  /* ==================================================================
     Brand popularity
     ================================================================== */

  var brandList = el('brandList');
  var marketCopy = el('marketInsight');
  var brandLead = el('brandsLead');

  var haveOrders = typeof BP.brandStats === 'function';

  /* This month's baskets, and everything checked out before this month —
     the second set is what "usually" means for this shopper. */
  var current = haveOrders ? BP.brandStats(profileId, { from: monthStart }) : { brands: [], orders: 0 };
  var history = haveOrders ? BP.brandStats(profileId, { to: monthStart }) : { brands: [], orders: 0 };

  var usingHistory = false;

  if (!current.orders && history.orders) {
    current = haveOrders ? BP.brandStats(profileId) : current;
    usingHistory = true;
  }

  function baselineFor(name) {
    var override = BENCHMARKS[name.toLowerCase()];
    if (override != null) return { value: override, source: 'community' };

    if (usingHistory || !history.orders) return null;

    for (var i = 0; i < history.brands.length; i++) {
      if (history.brands[i].brand.toLowerCase() === name.toLowerCase()) {
        return { value: history.brands[i].share, source: 'you' };
      }
    }
    return { value: 0, source: 'you' };
  }

  var brands = current.brands.slice(0, 4).map(function (row) {
    return {
      name: row.brand,
      amount: row.amount,
      units: row.units,
      share: row.share,
      topItems: row.topItems || [],
      baseline: baselineFor(row.brand)
    };
  });

  if (!brands.length) {
    el('brandsEmpty').hidden = false;
    if (brandLead) brandLead.hidden = true;
    marketCopy.textContent = 'Check out a basket from the cart and your brand split appears here, built from the items in it.';
  } else {
    if (brandLead) {
      brandLead.textContent = usingHistory
        ? 'No baskets checked out yet this month — this is your split across all ' +
          current.orders + ' saved shopping list' + (current.orders === 1 ? '' : 's') + '.'
        : 'Your share of spend across ' + current.orders + ' basket' +
          (current.orders === 1 ? '' : 's') + ' this month, against how you usually shop.';
    }

    brands.forEach(function (brand) {
      var li = document.createElement('li');

      var head = document.createElement('div');
      head.className = 'an-brand__line';

      var name = document.createElement('span');
      name.className = 'an-brand__name';
      name.textContent = brand.name;

      var share = document.createElement('span');
      share.className = 'an-brand__share';
      var strong = document.createElement('b');
      strong.textContent = brand.share + '%';
      share.appendChild(strong);

      var isNew = brand.baseline && brand.baseline.source === 'you' && brand.baseline.value === 0;
      var tail = ' of your basket spend';

      if (isNew) {
        tail += ' \u00b7 new to you';
      } else if (brand.baseline) {
        tail += ' \u00b7 ' + brand.baseline.value + '% ' +
          (brand.baseline.source === 'community' ? 'avg' : 'usual');
      }
      share.appendChild(document.createTextNode(tail));

      head.appendChild(name);
      head.appendChild(share);
      li.appendChild(head);

      var track = document.createElement('div');
      track.className = 'an-brand__track';

      var bar = document.createElement('span');
      bar.className = 'an-brand__fill';
      bar.style.width = brand.share + '%';
      track.appendChild(bar);

      if (brand.baseline && !isNew) {
        var mark = document.createElement('span');
        mark.className = 'an-brand__mark';
        mark.style.left = Math.min(brand.baseline.value, 99) + '%';
        mark.title = (brand.baseline.source === 'community' ? 'Community average ' : 'Your usual share ') +
          brand.baseline.value + '%';
        track.appendChild(mark);
      }

      li.appendChild(track);

      if (brand.topItems.length) {
        var items = document.createElement('p');
        items.className = 'an-brand__items';
        items.textContent = money(brand.amount) + ' \u00b7 ' + brand.units + ' unit' +
          (brand.units === 1 ? '' : 's') + ' \u00b7 ' + brand.topItems.join(', ');
        li.appendChild(items);
      }

      brandList.appendChild(li);
    });

    var lead = brands[0];
    marketCopy.textContent = '';

    if (lead.baseline && lead.baseline.value > 0) {
      var gap = lead.share - lead.baseline.value;
      var against = lead.baseline.source === 'community' ? 'the average shopper' : 'your usual pattern';

      if (Math.abs(gap) < 3) {
        marketCopy.appendChild(document.createTextNode(lead.name + ' holds a steady '));
        marketCopy.appendChild(frag(lead.share + '%'));
        marketCopy.appendChild(document.createTextNode(' of your basket spend, in line with ' + against + '.'));
      } else {
        marketCopy.appendChild(document.createTextNode('You are leaning '));
        marketCopy.appendChild(frag(Math.abs(gap) + '% ' + (gap > 0 ? 'harder' : 'less')));
        marketCopy.appendChild(document.createTextNode(' on ' + lead.name + ' than ' + against +
          '. Concentration earns loyalty points faster, but it also makes one brand\u2019s prices your prices.'));
      }
    } else {
      marketCopy.textContent = lead.name + ' takes ' + lead.share + '% of your basket spend — ' +
        'the largest single share' + (usingHistory ? ' on record.' : ' this month.');
    }
  }

  /* ==================================================================
     Significant transactions
     ================================================================== */

  /* Totals above are the household's; this list is not. A member sees only
     what they logged, the account holder sees everything. */
  var listable = iAmMain ? thisMonthTx : thisMonthTx.filter(function (e) { return e.mine; });

  var significant = listable.slice().sort(function (a, b) {
    return b.amount - a.amount;
  }).slice(0, 8).sort(function (a, b) { return b.date - a.date; });

  if (typeof BP.householdMembers === 'function' && BP.householdMembers().length > 1) {
    var scope = el('txScope');
    if (scope) {
      scope.hidden = false;
      scope.textContent = iAmMain
        ? 'Everything logged on the account this month, whoever logged it. The figures above cover the whole household.'
        : 'Your own transactions this month. The figures above cover the whole household.';
    }
  }

  var txBody = el('txBody');

  if (!significant.length) {
    el('txEmpty').hidden = false;
  } else {
    significant.forEach(function (entry) {
      var tr = document.createElement('tr');

      var date = document.createElement('td');
      date.className = 'an-tx__date';
      date.textContent = shortDate(entry.date);

      var name = document.createElement('td');
      name.className = 'an-tx__name';
      name.textContent = entry.name;
      if (iAmMain && entry.byName) {
        var by = document.createElement('span');
        by.className = 'an-tx__by';
        by.textContent = entry.byName;
        name.appendChild(by);
      }

      var cat = document.createElement('td');
      var tag = document.createElement('span');
      tag.className = 'an-tag' + (entry.income ? ' an-tag--income' : '');
      tag.textContent = entry.income ? 'Income' : entry.categoryLabel;
      cat.appendChild(tag);

      var amount = document.createElement('td');
      amount.className = 'an-amount' + (entry.income ? ' an-amount--in' : '');
      amount.textContent = (entry.income ? '+' : '\u2212') + money2(entry.amount);

      tr.appendChild(date);
      tr.appendChild(name);
      tr.appendChild(cat);
      tr.appendChild(amount);
      txBody.appendChild(tr);
    });
  }

  /* ==================================================================
     Export
     ================================================================== */

  function csvCell(value) {
    var s = String(value == null ? '' : value);
    return /[",\n]/.test(s) ? '"' + s.replace(/"/g, '""') + '"' : s;
  }

  function csvRow(cells) {
    return cells.map(csvCell).join(',');
  }

  el('csvBtn').addEventListener('click', function () {
    var lines = [];

    lines.push(csvRow(['Budget Pilot — monthly summary']));
    lines.push(csvRow(['Period', longDate(monthStart) + ' to ' + longDate(monthEnd)]));
    lines.push(csvRow(['Currency', prefs.currency || 'USD']));
    lines.push('');

    lines.push(csvRow(['Income', income]));
    lines.push(csvRow(['Budgeted', target]));
    lines.push(csvRow(['Spent', spent]));
    lines.push(csvRow(['Remaining', remaining]));
    lines.push(csvRow(['Savings achieved', savings]));
    lines.push('');

    lines.push(csvRow(['Category', 'Planned', 'Spent', 'Remaining', 'Used %']));
    perCategory.forEach(function (c) {
      lines.push(csvRow([c.label, c.planned, c.spent, c.planned - c.spent, pct(c.spent, c.planned)]));
    });

    if (significant.length) {
      lines.push('');
      lines.push(csvRow(['Date', 'Merchant / service', 'Category', 'Type', 'Amount']));
      thisMonthTx.slice().sort(function (a, b) { return b.date - a.date; }).forEach(function (e) {
        lines.push(csvRow([
          e.date.toISOString().slice(0, 10),
          e.name,
          e.income ? 'Income' : e.categoryLabel,
          e.income ? 'income' : 'expense',
          (e.income ? '' : '-') + e.amount.toFixed(2)
        ]));
      });
    }

    var blob = new Blob(['\ufeff' + lines.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
    var url = URL.createObjectURL(blob);
    var link = document.createElement('a');

    link.href = url;
    link.download = 'budget-pilot-' + now.getFullYear() + '-' +
      String(now.getMonth() + 1).replace(/^(\d)$/, '0$1') + '.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.setTimeout(function () { URL.revokeObjectURL(url); }, 1000);

    say('Summary exported as CSV.');
  });

  el('pdfBtn').addEventListener('click', function () {
    say('Choose "Save as PDF" as the destination in the print dialog.');
    window.setTimeout(function () { window.print(); }, 300);
  });

  /* ==================================================================
     Chrome
     ================================================================== */

  var avatar = el('appbarAvatar');
  if (avatar) {
    if (profile.avatar) {
      avatar.style.backgroundImage = 'url(' + profile.avatar + ')';
    } else {
      avatar.style.backgroundColor = BP.avatarColor(profile.email || profile.name);
      avatar.textContent = BP.initials(profile.name);
    }
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
})();
