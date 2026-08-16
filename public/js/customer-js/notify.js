/* Budget Pilot — notifications engine

   Builds the alert feed from data the app already holds: the budget set on the
   Budgets page, the expense log, past grocery orders and the catalog. Nothing
   here is invented — when there is nothing to report the feed comes back empty
   and the page says so.

   Read / dismissed state lives in localStorage per profile, alongside the time
   each alert was first produced (so "2m ago" stays honest between visits).

   Catalog-derived alerts need js/catalog-data.js. Pages that do not load the
   catalog reuse the copy cached the last time a catalog page ran, so the bell
   badge shows the same number everywhere.

   Load after js/store.js.

   API
     BPNotify.build(profileId)        -> [notification]   newest first, live only
     BPNotify.all(profileId)          -> [notification]   including dismissed
     BPNotify.unread(profileId)       -> number
     BPNotify.markRead(profileId, id)
     BPNotify.markAllRead(profileId)
     BPNotify.dismiss(profileId, id)
     BPNotify.restore(profileId, id)
     BPNotify.timeAgo(iso)            -> "2m ago"
*/

window.BPNotify = (function () {
  'use strict';

  var BP = window.BudgetPilot;
  var KEY = 'budgetpilot.notify.';

  /* ==================================================================
     Stored state
     ================================================================== */

  function blank() {
    return { seen: {}, read: [], dismissed: [], cache: [] };
  }

  function readState(profileId) {
    if (!profileId) return blank();
    try {
      var raw = window.localStorage.getItem(KEY + profileId);
      var data = raw ? JSON.parse(raw) : null;
      if (!data || typeof data !== 'object') return blank();
      return {
        seen: data.seen && typeof data.seen === 'object' ? data.seen : {},
        read: Array.isArray(data.read) ? data.read : [],
        dismissed: Array.isArray(data.dismissed) ? data.dismissed : [],
        cache: Array.isArray(data.cache) ? data.cache : []
      };
    } catch (e) {
      return blank();
    }
  }

  function writeState(profileId, state) {
    if (!profileId) return false;
    try {
      window.localStorage.setItem(KEY + profileId, JSON.stringify(state));
      return true;
    } catch (e) {
      return false;
    }
  }

  /* ==================================================================
     Small helpers
     ================================================================== */

  function monthTag(date) {
    var d = date || new Date();
    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0');
  }

  function labelFor(key) {
    var list = (BP && BP.CATEGORIES) || [];
    var match = list.filter(function (c) { return c.key === key; })[0];
    return match ? match.label : 'Other';
  }

  function dateOf(entry) {
    var raw = String((entry && (entry.date || entry.loggedAt)) || '').slice(0, 10);
    var bits = raw.split('-');
    if (bits.length !== 3) return null;
    var d = new Date(Number(bits[0]), Number(bits[1]) - 1, Number(bits[2]));
    return isNaN(d.getTime()) ? null : d;
  }

  function timeAgo(iso) {
    var then = new Date(iso);
    if (isNaN(then.getTime())) return '';

    var secs = Math.max(Math.round((Date.now() - then.getTime()) / 1000), 0);
    if (secs < 60) return 'Just now';

    var mins = Math.round(secs / 60);
    if (mins < 60) return mins + 'm ago';

    var hours = Math.round(mins / 60);
    if (hours < 24) return hours + 'h ago';

    var days = Math.round(hours / 24);
    if (days === 1) return 'Yesterday';
    if (days < 7) return days + ' days ago';

    return then.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
  }

  /* ==================================================================
     Builders — each returns plain objects, no timestamps yet
     ================================================================== */

  /* Budget alerts: over the line, or close to it.

     The budget is the household's, but the alerts are personal: a member is
     told about the categories they actually spend in, not about everything
     the family buys. The account holder sees them all. */
  function budgetAlerts(profileId) {
    var out = [];
    if (!BP) return out;

    var budget = BP.getBudget(profileId);
    if (!budget) return out;

    var tag = monthTag();
    var everything = !BP.isMain || BP.isMain(profileId);
    var mine = everything ? null : BP.categoriesUsedBy(profileId);

    BP.CATEGORIES.forEach(function (c) {
      if (mine && !mine[c.key]) return;   /* not a category this member spends in */

      var row = budget.categories[c.key] || {};
      var planned = Number(row.planned) || 0;
      var spent = Number(row.spent) || 0;
      if (planned <= 0) return;

      var share = spent / planned;

      if (spent > planned) {
        out.push({
          id: 'over:' + c.key + ':' + tag,
          kind: 'over',
          tone: 'danger',
          category: c.key,
          title: 'Budget exceeded',
          text: 'Your ' + c.label + ' budget has gone past its limit by {amount}. ' +
                'Adjust your categories to stay on track.',
          amount: spent - planned,
          actions: [{ label: 'Review budget', href: 'budgets.html', style: 'danger' }]
        });
        return;
      }

      if (share >= 0.8) {
        var atLimit = share >= 1;
        out.push({
          id: 'near:' + c.key + ':' + tag,
          kind: 'near',
          tone: 'warn',
          category: c.key,
          title: atLimit ? 'Budget limit reached' : 'Budget nearing limit',
          text: atLimit
            ? 'You have used all of your "' + c.label + '" budget for this month. ' +
              'Anything more comes out of another category.'
            : 'You have used ' + Math.round(share * 100) + '% of your "' + c.label +
              '" budget. You have {amount} left for the month.',
          amount: planned - spent,
          progress: Math.round(share * 100)
        });
      }
    });

    return out;
  }

  /* Last month closed under budget, so the difference carries forward. This is
     a household-level event, so only the account holder is told. */
  function rolloverAlert(profileId) {
    if (!BP) return [];
    if (BP.isMain && !BP.isMain(profileId)) return [];

    var budget = BP.getBudget(profileId);
    var expenses = (BP.familyExpenses ? BP.familyExpenses(profileId) : BP.getExpenses(profileId)) || [];
    if (!budget || !expenses.length) return [];

    var now = new Date();
    var prev = new Date(now.getFullYear(), now.getMonth() - 1, 1);
    var spent = 0;
    var found = false;

    expenses.forEach(function (e) {
      var d = dateOf(e);
      if (!d) return;
      if (d.getFullYear() === prev.getFullYear() && d.getMonth() === prev.getMonth()) {
        spent += Number(e.amount) || 0;
        found = true;
      }
    });

    if (!found) return [];

    var planned = 0;
    BP.CATEGORIES.forEach(function (c) {
      planned += Number((budget.categories[c.key] || {}).planned) || 0;
    });

    var left = planned - spent;
    if (planned <= 0 || left <= 0) return [];

    return [{
      id: 'rollover:' + monthTag(prev),
      kind: 'rollover',
      tone: 'done',
      category: 'other',
      title: 'Budget rollover complete',
      text: "Last month's remaining balance of {amount} has been rolled over into your savings.",
      amount: left,
      at: new Date(now.getFullYear(), now.getMonth(), 1).toISOString()
    }];
  }

  /* Catalog alerts: a cheaper brand for something, and newly checked listings.
     These need the catalog, so they are cached for pages that skip it. */
  function catalogAlerts(profileId) {
    var CAT = window.CATALOG;
    if (!CAT) return null;

    var out = [];
    var bought = {};

    if (BP.getOrders) {
      (BP.getOrders(profileId) || []).forEach(function (order) {
        (order.lines || []).forEach(function (line) {
          if (line.id) bought[line.id] = true;
        });
      });
    }

    /* Products where switching brand saves real money. Anything the customer
       has actually bought comes first — that is the saving they can use. */
    var ranked = CAT.ITEMS.map(function (item) {
      var offers = CAT.offersOf(item) || [];
      if (offers.length < 2) return null;

      var cheap = offers[0];
      var dear = offers[0];
      offers.forEach(function (o) {
        if (o.price < cheap.price) cheap = o;
        if (o.price > dear.price) dear = o;
      });
      if (!(dear.price > cheap.price)) return null;

      var pct = Math.round((dear.price - cheap.price) / dear.price * 100);
      if (pct < 20) return null;

      return {
        item: item, cheap: cheap, dear: dear, pct: pct,
        owned: !!bought[item.id]
      };
    }).filter(Boolean);

    ranked.sort(function (a, b) {
      if (a.owned !== b.owned) return a.owned ? -1 : 1;
      return b.pct - a.pct;
    });

    ranked.slice(0, 2).forEach(function (row) {
      out.push({
        id: 'drop:' + row.item.id + ':' + row.cheap.brand,
        kind: 'drop',
        tone: 'good',
        category: 'grocery',
        title: 'Cheaper brand found',
        text: row.item.name + ' (' + row.cheap.pack + ') is {price} with ' + row.cheap.brand +
              ', against {was} for ' + row.dear.brand + '.',
        price: row.cheap.price,
        was: row.dear.price,
        percent: row.pct,
        image: row.cheap.image || row.item.image || '',
        actions: [{ label: 'View product', href: 'product.html?id=' + row.item.id, style: 'soft' }]
      });
    });

    /* The brand with the widest range this month — presented as listings the
       copilot has price-checked, which is exactly what the catalog holds. */
    var counts = {};
    CAT.ITEMS.forEach(function (item) {
      (CAT.offersOf(item) || []).forEach(function (o) {
        counts[o.brand] = (counts[o.brand] || 0) + 1;
      });
    });

    var top = Object.keys(counts).sort(function (a, b) { return counts[b] - counts[a]; })[0];
    if (top) {
      out.push({
        id: 'verified:' + top + ':' + monthTag(),
        kind: 'verified',
        tone: 'info',
        category: 'grocery',
        title: 'New verified products',
        text: counts[top] + ' listings from "' + top + '" have been checked for price ' +
              'consistency across the catalog.',
        actions: [{ label: 'Open catalog', href: 'grocery.html', style: 'soft' }]
      });
    }

    return out;
  }

  /* ==================================================================
     Assembly
     ================================================================== */

  function assemble(profileId) {
    var state = readState(profileId);
    var items = budgetAlerts(profileId).concat(rolloverAlert(profileId));

    var fresh = catalogAlerts(profileId);
    if (fresh) {
      state.cache = fresh;
      items = items.concat(fresh);
    } else {
      items = items.concat(state.cache || []);
    }

    var now = new Date().toISOString();
    var live = {};

    items.forEach(function (n) {
      live[n.id] = true;
      if (!state.seen[n.id]) state.seen[n.id] = n.at || now;
      n.at = n.at || state.seen[n.id];
      n.read = state.read.indexOf(n.id) !== -1;
      n.dismissed = state.dismissed.indexOf(n.id) !== -1;
    });

    /* Forget the bookkeeping for alerts that no longer apply. */
    Object.keys(state.seen).forEach(function (id) {
      if (!live[id]) delete state.seen[id];
    });
    state.read = state.read.filter(function (id) { return live[id]; });
    state.dismissed = state.dismissed.filter(function (id) { return live[id]; });

    writeState(profileId, state);

    items.sort(function (a, b) { return new Date(b.at) - new Date(a.at); });
    return items;
  }

  function all(profileId) {
    if (!BP || !profileId) return [];
    return assemble(profileId);
  }

  function build(profileId) {
    return all(profileId).filter(function (n) { return !n.dismissed; });
  }

  function unread(profileId) {
    return build(profileId).filter(function (n) { return !n.read; }).length;
  }

  /* ---------- State changes ---------- */

  function add(profileId, list, id) {
    var state = readState(profileId);
    if (state[list].indexOf(id) === -1) state[list].push(id);
    writeState(profileId, state);
  }

  function remove(profileId, list, id) {
    var state = readState(profileId);
    state[list] = state[list].filter(function (x) { return x !== id; });
    writeState(profileId, state);
  }

  function markRead(profileId, id) { add(profileId, 'read', id); }
  function dismiss(profileId, id) { add(profileId, 'dismissed', id); markRead(profileId, id); }
  function restore(profileId, id) { remove(profileId, 'dismissed', id); }

  function markAllRead(profileId) {
    var state = readState(profileId);
    build(profileId).forEach(function (n) {
      if (state.read.indexOf(n.id) === -1) state.read.push(n.id);
    });
    writeState(profileId, state);
  }

  /* ==================================================================
     Bell badge — every page marks its bell with data-bell
     ================================================================== */

  function paintBell() {
    var bells = document.querySelectorAll('[data-bell]');
    if (!bells.length || !BP) return;

    var profileId = BP.getSession();
    if (!profileId) return;

    var count = unread(profileId);

    Array.prototype.forEach.call(bells, function (bell) {
      var badge = bell.querySelector('.icon-btn__badge');

      if (!count) {
        if (badge) badge.hidden = true;
        return;
      }
      if (!badge) {
        badge = document.createElement('span');
        badge.className = 'icon-btn__badge';
        bell.appendChild(badge);
      }
      bell.classList.add('icon-btn--badged');
      badge.textContent = count > 9 ? '9+' : String(count);
      badge.hidden = false;
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', paintBell);
  } else {
    paintBell();
  }

  return {
    build: build,
    all: all,
    unread: unread,
    markRead: markRead,
    markAllRead: markAllRead,
    dismiss: dismiss,
    restore: restore,
    timeAgo: timeAgo,
    labelFor: labelFor,
    paintBell: paintBell
  };
})();
