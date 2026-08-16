/* Budget Pilot — customer dashboard

   Everything here is read from what the app already stores: the budget set on
   the Budgets page and the expense log written by manual entry and grocery
   checkout. Nothing is invented — where there is no data yet, the panel says
   so rather than showing a placeholder number. */

(function () {
  'use strict';

  var BP = window.BudgetPilot;
  var el = function (id) { return document.getElementById(id); };
  var SVGNS = 'http://www.w3.org/2000/svg';

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
  /* Charts and totals show what the household spends as a whole — everyone on
     the account is spending from the same budget, so the shape of the month is
     the family's. The transaction list below stays private (see sorted()). */
  var expenses = BP.familyExpenses(profileId);
  var myExpenses = BP.getExpenses(profileId);

  /* One colour per category, shared by the donut and the transaction icons. */
  var COLORS = {
    grocery:   '#3C8F60',
    transport: '#3A7CB4',
    rent:      '#3A4A85',
    loan:      '#453A7C',
    tuition:   '#96591F',
    medicine:  '#96354A',
    clothing:  '#454E62',
    other:     '#8A93AB'
  };

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

  function compact(value) {
    var n = Math.round(Number(value) || 0);
    if (n >= 1000) return symbol + (n / 1000).toFixed(n >= 10000 ? 0 : 1) + 'k';
    return symbol + n.toLocaleString('en-US');
  }

  function labelFor(key) {
    var match = BP.CATEGORIES.filter(function (c) { return c.key === key; })[0];
    return match ? match.label : 'Other';
  }

  function svg(name, attrs) {
    var node = document.createElementNS(SVGNS, name);
    Object.keys(attrs || {}).forEach(function (k) { node.setAttribute(k, attrs[k]); });
    return node;
  }

  /* Expense dates are stored as YYYY-MM-DD; parse as local, not UTC. */
  function dateOf(entry) {
    var raw = String(entry.date || entry.loggedAt || '').slice(0, 10);
    var bits = raw.split('-');
    if (bits.length !== 3) return null;
    var d = new Date(Number(bits[0]), Number(bits[1]) - 1, Number(bits[2]));
    return isNaN(d.getTime()) ? null : d;
  }

  function startOfDay(d) {
    return new Date(d.getFullYear(), d.getMonth(), d.getDate());
  }

  function dayGap(from, to) {
    return Math.round((startOfDay(to) - startOfDay(from)) / 86400000);
  }

  /* ==================================================================
     Figures
     ================================================================== */

  var income = Number(budget.income || profile.income) || 0;

  function totals() {
    var planned = 0;
    var spent = 0;
    BP.CATEGORIES.forEach(function (c) {
      var row = budget.categories[c.key] || {};
      planned += Number(row.planned) || 0;
      spent += Number(row.spent) || 0;
    });
    return { planned: planned, spent: spent, left: planned - spent };
  }

  /* Month-on-month change from the expense log. Returns null when there is no
     previous month to compare against — better an absent badge than a made-up
     percentage. */
  function expenseShift() {
    var now = new Date();
    var thisMonth = 0;
    var lastMonth = 0;
    var prev = new Date(now.getFullYear(), now.getMonth() - 1, 1);

    expenses.forEach(function (entry) {
      var d = dateOf(entry);
      if (!d) return;
      var amount = Number(entry.amount) || 0;
      if (d.getFullYear() === now.getFullYear() && d.getMonth() === now.getMonth()) thisMonth += amount;
      else if (d.getFullYear() === prev.getFullYear() && d.getMonth() === prev.getMonth()) lastMonth += amount;
    });

    if (lastMonth <= 0) return null;
    return Math.round((thisMonth - lastMonth) / lastMonth * 100);
  }

  /* Budget health, scored out of 100. Each deduction is listed in the
     explainer modal so the number is never a black box. */
  function healthScore() {
    var sum = totals();
    var reasons = [];
    var score = 100;

    if (sum.planned <= 0) {
      return { score: null, tier: 'No plan', reasons: [] };
    }

    var overshoot = Math.max(sum.spent - sum.planned, 0) / sum.planned;
    if (overshoot > 0) {
      var hit = Math.round(Math.min(overshoot, 1) * 45);
      score -= hit;
      reasons.push('Spending is past the plan overall: \u2212' + hit);
    }

    var overCount = BP.CATEGORIES.filter(function (c) {
      var row = budget.categories[c.key] || {};
      return (Number(row.planned) || 0) > 0 && (Number(row.spent) || 0) > (Number(row.planned) || 0);
    }).length;
    if (overCount) {
      var each = Math.min(overCount * 8, 24);
      score -= each;
      reasons.push(overCount + ' categor' + (overCount === 1 ? 'y is' : 'ies are') + ' over: \u2212' + each);
    }

    if (income > 0 && sum.spent > income) {
      score -= 15;
      reasons.push('Spending is past your income: \u221215');
    }

    if (!budget.finalized) {
      score -= 10;
      reasons.push('Plan is not finalized yet: \u221210');
    }

    /* A blown category should never read as flawless, however small it is
       against the total. */
    if (overCount) {
      score = Math.min(score, 84);
      reasons.push('Top tier is held back while any category is over');
    }

    score = Math.max(0, Math.min(100, score));
    var tier = score >= 85 ? 'Elite' : score >= 70 ? 'Strong' : score >= 50 ? 'Fair' : 'At risk';
    return { score: score, tier: tier, reasons: reasons };
  }

  function paintKpis() {
    var sum = totals();

    el('incomeValue').textContent = income > 0 ? money(income) : '\u2014';
    if (income > 0 && sum.planned > 0) {
      var share = Math.round(sum.planned / income * 100);
      el('incomePill').textContent = share + '% planned';
      el('incomePill').hidden = false;
    }

    el('expenseValue').textContent = money(sum.spent);
    var shift = expenseShift();
    if (shift !== null) {
      var pill = el('expensePill');
      pill.textContent = (shift > 0 ? '+' : '') + shift + '%';
      pill.className = 'pill ' + (shift > 0 ? 'pill--down' : 'pill--up');
      pill.title = 'Compared with last month';
      pill.hidden = false;
    }

    var remaining = el('remainingValue');
    if (sum.planned > 0) {
      remaining.textContent = money(Math.abs(sum.left));
      var ratio = sum.left / sum.planned;
      var low = sum.left < 0 || ratio < 0.15;
      remaining.classList.toggle('is-low', low);

      var rp = el('remainingPill');
      rp.textContent = sum.left < 0 ? 'Over' : ratio < 0.15 ? 'Low' : ratio < 0.4 ? 'Watch' : 'Healthy';
      rp.className = 'pill ' + (low ? 'pill--down' : 'pill--up');
      rp.hidden = false;
      if (sum.left < 0) remaining.textContent = '\u2212' + money(Math.abs(sum.left));
    } else {
      remaining.textContent = '\u2014';
    }

    var health = healthScore();
    el('scoreValue').innerHTML = (health.score === null ? '\u2014' : health.score) +
      '<span class="kpi__of">/100</span>';
    el('scoreTier').textContent = health.tier;
  }

  el('scoreWhy').addEventListener('click', function () {
    var health = healthScore();

    openModal('Budget health score', function (body) {
      var p = document.createElement('p');
      if (health.score === null) {
        p.textContent = 'The score needs a plan to measure against. Set category amounts on the Budgets page and it starts working.';
        body.appendChild(p);
        return;
      }

      p.textContent = 'Starts at 100 and comes down as your spending drifts from the plan. Right now: ' +
        health.score + '.';
      body.appendChild(p);

      if (!health.reasons.length) {
        var clean = document.createElement('p');
        clean.textContent = 'Nothing is pulling it down. Every category is inside its budget.';
        body.appendChild(clean);
        return;
      }

      var ul = document.createElement('ul');
      health.reasons.forEach(function (line) {
        var li = document.createElement('li');
        li.textContent = line;
        ul.appendChild(li);
      });
      body.appendChild(ul);
    }, [{ label: 'Close', style: 'btn--primary' }]);
  });

  /* ==================================================================
     Spending trend
     ================================================================== */

  var range = 'monthly';

  function monthlyBuckets() {
    var now = new Date();
    var out = [];
    for (var i = 5; i >= 0; i--) {
      var start = new Date(now.getFullYear(), now.getMonth() - i, 1);
      out.push({
        label: start.toLocaleDateString(undefined, { month: 'short' }),
        full: start.toLocaleDateString(undefined, { month: 'long', year: 'numeric' }),
        year: start.getFullYear(),
        month: start.getMonth(),
        value: 0
      });
    }

    expenses.forEach(function (entry) {
      var d = dateOf(entry);
      if (!d) return;
      out.forEach(function (b) {
        if (d.getFullYear() === b.year && d.getMonth() === b.month) b.value += Number(entry.amount) || 0;
      });
    });

    return out;
  }

  function weeklyBuckets() {
    var now = startOfDay(new Date());
    var out = [];
    for (var i = 5; i >= 0; i--) {
      var end = new Date(now.getTime() - i * 7 * 86400000);
      var start = new Date(end.getTime() - 6 * 86400000);
      out.push({
        label: start.toLocaleDateString(undefined, { day: 'numeric', month: 'short' }),
        full: 'Week to ' + end.toLocaleDateString(undefined, { day: 'numeric', month: 'short' }),
        start: start,
        end: end,
        value: 0
      });
    }

    expenses.forEach(function (entry) {
      var d = dateOf(entry);
      if (!d) return;
      out.forEach(function (b) {
        if (d >= b.start && d <= b.end) b.value += Number(entry.amount) || 0;
      });
    });

    return out;
  }

  /* A smooth curve through the points, using midpoint control handles so the
     line never overshoots above or below the data. */
  function curve(points) {
    if (!points.length) return '';
    var d = 'M' + points[0].x + ',' + points[0].y;
    for (var i = 0; i < points.length - 1; i++) {
      var a = points[i];
      var b = points[i + 1];
      var mid = (a.x + b.x) / 2;
      d += ' C' + mid + ',' + a.y + ' ' + mid + ',' + b.y + ' ' + b.x + ',' + b.y;
    }
    return d;
  }

  function paintTrend() {
    var buckets = range === 'weekly' ? weeklyBuckets() : monthlyBuckets();
    var anySpend = buckets.some(function (b) { return b.value > 0; });
    var plot = el('trendPlot');

    plot.textContent = '';
    el('trendEmpty').hidden = anySpend;
    if (!anySpend) return;

    var W = 720, H = 260;
    var padL = 12, padR = 12, padT = 18, padB = 34;
    var peak = Math.max.apply(null, buckets.map(function (b) { return b.value; })) || 1;
    var step = (W - padL - padR) / Math.max(buckets.length - 1, 1);

    var points = buckets.map(function (b, i) {
      return {
        x: padL + i * step,
        y: padT + (1 - b.value / peak) * (H - padT - padB),
        bucket: b
      };
    });

    var chart = svg('svg', {
      viewBox: '0 0 ' + W + ' ' + H,
      role: 'img',
      'aria-label': 'Spending over the last six ' + (range === 'weekly' ? 'weeks' : 'months')
    });

    var defs = svg('defs', {});
    var grad = svg('linearGradient', { id: 'trendFade', x1: '0', y1: '0', x2: '0', y2: '1' });
    grad.appendChild(svg('stop', { offset: '0%', 'stop-color': 'currentColor', 'stop-opacity': '.22' }));
    grad.appendChild(svg('stop', { offset: '100%', 'stop-color': 'currentColor', 'stop-opacity': '0' }));
    defs.appendChild(grad);
    chart.appendChild(defs);

    var baseline = H - padB;
    chart.appendChild(svg('line', { class: 'trend__grid', x1: padL, y1: baseline, x2: W - padR, y2: baseline }));

    var area = svg('path', {
      class: 'trend__area',
      d: curve(points) + ' L' + points[points.length - 1].x + ',' + baseline +
         ' L' + points[0].x + ',' + baseline + ' Z',
      fill: 'url(#trendFade)'
    });
    area.style.color = 'var(--accent)';
    chart.appendChild(area);

    chart.appendChild(svg('path', { class: 'trend__line', d: curve(points) }));

    points.forEach(function (p) {
      var hit = svg('circle', { class: 'trend__hit', cx: p.x, cy: p.y, r: 16, tabindex: '0' });
      var title = svg('title', {});
      title.textContent = p.bucket.full + ': ' + money(p.bucket.value);
      hit.appendChild(title);
      chart.appendChild(hit);
      chart.appendChild(svg('circle', { class: 'trend__dot', cx: p.x, cy: p.y, r: 4.5 }));
    });

    points.forEach(function (p) {
      var text = svg('text', {
        class: 'trend__label',
        x: p.x,
        y: H - 10,
        'text-anchor': p === points[0] ? 'start' : p === points[points.length - 1] ? 'end' : 'middle'
      });
      text.textContent = p.bucket.label;
      chart.appendChild(text);
    });

    plot.appendChild(chart);
  }

  document.querySelectorAll('.rangetoggle__btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      range = btn.getAttribute('data-range');
      document.querySelectorAll('.rangetoggle__btn').forEach(function (other) {
        other.classList.toggle('is-on', other === btn);
      });
      paintTrend();
    });
  });

  /* ==================================================================
     By category
     ================================================================== */

  function paintCategories() {
    var rows = BP.CATEGORIES.map(function (c) {
      return { key: c.key, label: c.label, value: Number((budget.categories[c.key] || {}).spent) || 0 };
    }).filter(function (r) { return r.value > 0; });

    var total = rows.reduce(function (sum, r) { return sum + r.value; }, 0);
    var wrap = el('donutWrap');
    var legend = el('legend');

    wrap.textContent = '';
    legend.textContent = '';
    el('catEmpty').hidden = total > 0;
    if (!total) return;

    var size = 220, mid = size / 2, r = 84, C = 2 * Math.PI * r;
    var ring = svg('svg', { viewBox: '0 0 ' + size + ' ' + size, role: 'img', 'aria-label': 'Spending by category' });

    ring.appendChild(svg('circle', { class: 'donut__track', cx: mid, cy: mid, r: r }));

    var offset = 0;
    rows.forEach(function (row) {
      var length = row.value / total * C;
      var seg = svg('circle', {
        class: 'donut__seg',
        cx: mid, cy: mid, r: r,
        stroke: COLORS[row.key] || COLORS.other,
        'stroke-dasharray': length + ' ' + (C - length),
        'stroke-dashoffset': -offset,
        transform: 'rotate(-90 ' + mid + ' ' + mid + ')'
      });
      var title = svg('title', {});
      title.textContent = row.label + ': ' + money(row.value);
      seg.appendChild(title);
      ring.appendChild(seg);
      offset += length;
    });

    var totalText = svg('text', { class: 'donut__total', x: mid, y: mid + 2 });
    totalText.textContent = compact(total);
    ring.appendChild(totalText);

    var cap = svg('text', { class: 'donut__cap', x: mid, y: mid + 22 });
    cap.textContent = 'Total';
    ring.appendChild(cap);

    wrap.appendChild(ring);

    rows.forEach(function (row) {
      var li = document.createElement('li');
      li.className = 'legend__row';

      var dot = document.createElement('span');
      dot.className = 'legend__dot';
      dot.style.background = COLORS[row.key] || COLORS.other;

      var name = document.createElement('span');
      name.className = 'legend__name';
      name.textContent = row.label;

      var share = document.createElement('span');
      share.className = 'legend__share';
      share.textContent = Math.round(row.value / total * 100) + '%';
      share.title = money(row.value);

      li.appendChild(dot);
      li.appendChild(name);
      li.appendChild(share);
      legend.appendChild(li);
    });
  }

  /* ==================================================================
     Recent transactions
     ================================================================== */

  /* Only your own transactions are listed. The account holder's own call to
     getExpenses returns everything, which is what they are entitled to see. */
  function sorted() {
    return myExpenses.slice().sort(function (a, b) {
      return String(b.loggedAt || b.date || '').localeCompare(String(a.loggedAt || a.date || ''));
    });
  }

  function whenText(entry) {
    var d = dateOf(entry);
    if (!d) return 'Recently';
    var gap = dayGap(d, new Date());
    if (gap <= 0) return 'Today';
    if (gap === 1) return 'Yesterday';
    if (gap < 7) return gap + ' days ago';
    return d.toLocaleDateString(undefined, { day: 'numeric', month: 'short' });
  }

  function buildRow(entry) {
    var node = el('txTemplate').content.firstElementChild.cloneNode(true);
    var colour = COLORS[entry.category] || COLORS.other;

    var icon = node.querySelector('.txrow__icon');
    icon.style.background = colour + '22';
    icon.style.color = colour;
    icon.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" ' +
      'stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18l-1.5 11H4.5z"/>' +
      '<path d="M3 9l1.5-4h15L21 9"/></svg>';

    node.querySelector('.txrow__name').textContent = entry.description || labelFor(entry.category);
    node.querySelector('.txrow__meta').textContent = whenText(entry) + ' \u2022 ' + labelFor(entry.category);
    node.querySelector('.txrow__amount').textContent = '\u2212' + money(entry.amount);
    return node;
  }

  function paintTransactions() {
    var recent = sorted().slice(0, 5);
    var list = el('txList');

    list.textContent = '';
    el('txEmpty').hidden = recent.length > 0;
    el('viewAllBtn').hidden = myExpenses.length <= 5;

    recent.forEach(function (entry) { list.appendChild(buildRow(entry)); });
  }

  el('viewAllBtn').addEventListener('click', function () {
    var all = sorted();

    openModal('All transactions', function (body) {
      var count = document.createElement('p');
      count.textContent = all.length + ' recorded, newest first.';
      body.appendChild(count);

      var ul = document.createElement('ul');
      ul.className = 'txfull';
      all.forEach(function (entry) { ul.appendChild(buildRow(entry)); });
      body.appendChild(ul);
    }, [{ label: 'Close', style: 'btn--primary' }]);
  });

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

  var firstName = String(profile.name || '').trim().split(/\s+/)[0];
  el('greeting').textContent = firstName ? 'Welcome back, ' + firstName : 'Welcome back';

  var sum = totals();
  el('greetingSub').textContent = sum.planned <= 0
    ? 'Set your category amounts on the Budgets page to start tracking.'
    : sum.left < 0
      ? 'You are past your plan by ' + money(Math.abs(sum.left)) + ' this period.'
      : 'Your finances are looking healthy today.';

  paintKpis();
  paintTrend();
  paintCategories();
  paintTransactions();
})();
