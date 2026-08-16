/* Budget Pilot — order history

   Extends window.BudgetPilot with somewhere to keep a shopping list once it
   has been checked out. The cart is a working document that gets emptied;
   this is the permanent record of what was actually bought, which is what
   the Analytics page reads to work out brand loyalty.

   Load after js/store.js and before any page script that uses it.

   API
     BP.saveOrder(profileId, order)        -> { ok, order }
     BP.getOrders(profileId, options)      -> [order]        newest first
     BP.getOrder(profileId, orderId)       -> order | null
     BP.deleteOrder(profileId, orderId)    -> { ok }
     BP.clearOrders(profileId)             -> { ok }
     BP.brandStats(profileId, options)     -> { total, units, orders, brands: [] }

   options accepts { from: Date|string, to: Date|string, limit: number }.

   An order looks like:
     { id, at, category, total, itemCount, note,
       lines: [ { id, name, brand, pack, qty, price, lineTotal, unitLabel, unitQty } ] }
*/

(function () {
  'use strict';

  var BP = window.BudgetPilot;
  if (!BP) return;

  var KEY = 'budgetpilot.orders.';
  var MAX_ORDERS = 60;   /* plenty of history without filling storage */

  /* ==================================================================
     Storage
     ================================================================== */

  function keyFor(profileId) { return KEY + profileId; }

  function read(profileId) {
    if (!profileId) return [];
    try {
      var raw = window.localStorage.getItem(keyFor(profileId));
      var list = raw ? JSON.parse(raw) : [];
      return Object.prototype.toString.call(list) === '[object Array]' ? list : [];
    } catch (e) {
      return [];
    }
  }

  function write(profileId, list) {
    try {
      window.localStorage.setItem(keyFor(profileId), JSON.stringify(list.slice(0, MAX_ORDERS)));
      return { ok: true };
    } catch (e) {
      return { ok: false, error: e };
    }
  }

  /* ==================================================================
     Shaping
     ================================================================== */

  function num(value) {
    var n = Number(value);
    return isFinite(n) ? n : 0;
  }

  function cleanLine(line) {
    if (!line || typeof line !== 'object') return null;

    var qty = Math.max(Math.round(num(line.qty)) || 1, 1);
    var price = num(line.price);
    var brand = String(line.brand || '').trim();

    if (!brand && !line.name) return null;

    return {
      id: line.id != null ? line.id : null,
      name: String(line.name || '').trim(),
      brand: brand || 'Unbranded',
      pack: String(line.pack || '').trim(),
      qty: qty,
      price: price,
      lineTotal: num(line.lineTotal) || price * qty,
      unitLabel: line.unitLabel || '',
      unitQty: num(line.unitQty) || 0
    };
  }

  function newId() {
    return 'ord-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 7);
  }

  /* ==================================================================
     Public API
     ================================================================== */

  BP.saveOrder = function (profileId, order) {
    if (!profileId || !order) return { ok: false };

    var lines = [];
    var source = order.lines || order.items || [];
    for (var i = 0; i < source.length; i++) {
      var line = cleanLine(source[i]);
      if (line) lines.push(line);
    }
    if (!lines.length) return { ok: false };

    var total = num(order.total) || lines.reduce(function (sum, l) { return sum + l.lineTotal; }, 0);
    var units = lines.reduce(function (sum, l) { return sum + l.qty; }, 0);

    var who = BP.getProfile ? BP.getProfile(profileId) : null;

    var record = {
      id: order.id || newId(),
      at: order.at || new Date().toISOString(),
      category: order.category || 'grocery',
      note: order.note || '',
      total: Math.round(total * 100) / 100,
      itemCount: num(order.itemCount) || units,
      lines: lines,
      by: profileId,
      byName: who ? who.name : ''
    };

    var list = read(profileId);
    list.unshift(record);

    var result = write(profileId, list);
    result.order = record;
    return result;
  };

  /* Each person's shopping stays under their own key. The account holder
     reads every member's list merged together; a member reads only their
     own — the same split the expense log uses. */
  function readScoped(profileId) {
    if (!BP.isMain || !BP.isMain(profileId)) return read(profileId);

    var merged = [];
    (BP.householdMembers ? BP.householdMembers() : []).forEach(function (person) {
      read(person.id).forEach(function (order) {
        if (!order) return;
        if (!order.by) order.by = person.id;
        if (!order.byName) order.byName = person.name;
        merged.push(order);
      });
    });
    return merged;
  }

  BP.getOrders = function (profileId, options) {
    var list = readScoped(profileId);
    var opts = options || {};

    var from = opts.from ? new Date(opts.from).getTime() : null;
    var to = opts.to ? new Date(opts.to).getTime() : null;

    var out = list.filter(function (order) {
      if (!order || !order.at) return false;
      var stamp = new Date(order.at).getTime();
      if (isNaN(stamp)) return false;
      if (from != null && stamp < from) return false;
      if (to != null && stamp > to) return false;
      return true;
    });

    out.sort(function (a, b) { return new Date(b.at) - new Date(a.at); });
    return opts.limit ? out.slice(0, opts.limit) : out;
  };

  BP.getOrder = function (profileId, orderId) {
    var list = readScoped(profileId);
    for (var i = 0; i < list.length; i++) {
      if (list[i] && list[i].id === orderId) return list[i];
    }
    return null;
  };

  BP.deleteOrder = function (profileId, orderId) {
    var list = read(profileId).filter(function (order) { return !order || order.id !== orderId; });
    return write(profileId, list);
  };

  BP.clearOrders = function (profileId) {
    try {
      window.localStorage.removeItem(keyFor(profileId));
      return { ok: true };
    } catch (e) {
      return { ok: false, error: e };
    }
  };

  /* Brand loyalty across whatever window of orders is asked for. */
  BP.brandStats = function (profileId, options) {
    var orders = BP.getOrders(profileId, options);
    var byBrand = {};
    var total = 0;
    var units = 0;

    orders.forEach(function (order) {
      (order.lines || []).forEach(function (line) {
        var brand = line.brand || 'Unbranded';
        var row = byBrand[brand] || (byBrand[brand] = {
          brand: brand, amount: 0, units: 0, orders: 0, items: {}
        });

        row.amount += num(line.lineTotal);
        row.units += num(line.qty);
        if (line.name) row.items[line.name] = (row.items[line.name] || 0) + num(line.qty);

        total += num(line.lineTotal);
        units += num(line.qty);
      });

      /* Count each brand once per basket, not once per line. */
      var seen = {};
      (order.lines || []).forEach(function (line) {
        var brand = line.brand || 'Unbranded';
        if (seen[brand]) return;
        seen[brand] = true;
        byBrand[brand].orders += 1;
      });
    });

    var brands = Object.keys(byBrand).map(function (name) {
      var row = byBrand[name];
      return {
        brand: row.brand,
        amount: Math.round(row.amount * 100) / 100,
        units: row.units,
        orders: row.orders,
        share: total > 0 ? Math.round(row.amount / total * 100) : 0,
        topItems: Object.keys(row.items).sort(function (a, b) {
          return row.items[b] - row.items[a];
        }).slice(0, 3)
      };
    }).sort(function (a, b) { return b.amount - a.amount; });

    return { total: total, units: units, orders: orders.length, brands: brands };
  };
})();
