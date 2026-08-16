/* Budget Pilot — shopping cart */

(function () {
  'use strict';

  var BP = window.BudgetPilot;
  var CAT = window.CATALOG;
  var el = function (id) { return document.getElementById(id); };

  var profileId = BP.getSession();
  var profile = profileId ? BP.getProfile(profileId) : null;

  if (!profile) {
    window.location.replace('login.php?signin=required');
    return;
  }

  var CURRENCIES = { USD:'$', EUR:'\u20AC', GBP:'\u00A3', LKR:'Rs', INR:'\u20B9', AUD:'$' };
  var prefs = profile.prefs || {};
  var symbol = CURRENCIES[prefs.currency] || '$';

  var cart = BP.getCart(profileId);
  var budget = BP.getBudget(profileId);

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
    return symbol + Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function itemById(id) {
    return CAT.ITEMS.filter(function (i) { return i.id === id; })[0];
  }

  function offerOf(line) {
    var item = itemById(line.id);
    if (!item) return null;
    return CAT.offerByBrand(item, line.brand) || CAT.cheapestOffer(item);
  }

  function imageOf(item, offer) {
    return BP.assetUrl((offer && offer.image ? offer.image : (item.image || '')).trim());
  }

  function perUnit(offer) { return offer.price / offer.unitQty; }
  function unitWord(offer) { return offer.unitLabel === 'each' ? 'ea' : offer.unitLabel; }

  function itemCount() {
    return cart.reduce(function (sum, line) { return sum + line.qty; }, 0);
  }

  function cartTotal() {
    return cart.reduce(function (sum, line) {
      var offer = offerOf(line);
      return sum + (offer ? offer.price * line.qty : 0);
    }, 0);
  }

  /* Budget figures. "Committed" is what earlier checkouts already recorded. */
  function grocery() { return budget.categories.grocery || { planned: 0, spent: 0 }; }
  function planned() { return Number(grocery().planned) || 0; }
  function committed() { return Number(grocery().spent) || 0; }
  function projected() { return committed() + cartTotal(); }

  function persistCart() {
    BP.saveCart(profileId, cart);
    profile = BP.getProfile(profileId);
  }

  /* The cart is emptied at checkout, so take a copy of what was in it first.
     Brand and pack are the parts Analytics needs later. */
  function snapshot() {
    var lines = [];

    cart.forEach(function (line) {
      var item = itemById(line.id);
      var offer = offerOf(line);
      if (!item || !offer) return;

      lines.push({
        id: item.id,
        name: item.name,
        brand: offer.brand,
        pack: offer.pack,
        qty: line.qty,
        price: offer.price,
        lineTotal: offer.price * line.qty,
        unitLabel: offer.unitLabel,
        unitQty: offer.unitQty
      });
    });

    return lines;
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
     Lines
     ================================================================== */

  function paintLines() {
    var list = el('lineList');
    list.textContent = '';

    cart.forEach(function (line, index) {
      var item = itemById(line.id);
      var offer = offerOf(line);
      if (!item || !offer) return;

      var node = el('lineTemplate').content.firstElementChild.cloneNode(true);
      var href = 'product.php?id=' + item.id + '&brand=' + encodeURIComponent(offer.brand);

      var art = node.querySelector('.line__art');
      var photo = node.querySelector('.line__photo');
      art.setAttribute('href', href);

      var source = imageOf(item, offer);
      if (source) {
        photo.src = source;
        photo.alt = offer.brand + ' ' + item.name;
        photo.addEventListener('error', function () {
          photo.remove();
          art.textContent = offer.brand.slice(0, 2).toUpperCase();
        });
      } else {
        photo.remove();
        art.textContent = offer.brand.slice(0, 2).toUpperCase();
      }

      var name = node.querySelector('.line__name');
      name.textContent = item.name;
      name.setAttribute('href', href);

      /* Flag the lines worth a second look. */
      var best = CAT.bestUnitOffer(item);
      var meta = node.querySelector('.line__meta');
      meta.textContent = offer.brand + ' \u00b7 ' + offer.pack + ' \u00b7 ' + money(perUnit(offer)) + '/' + unitWord(offer) + ' ';

      var tag = document.createElement('span');
      if (offer.brand === best.brand) {
        tag.className = 'tagline';
        tag.textContent = '\u00b7 Best value brand';
      } else {
        tag.className = 'tagline tagline--warn';
        tag.textContent = '\u00b7 Cheaper brand available';
      }
      meta.appendChild(tag);

      node.querySelector('.line__qty').textContent = line.qty;
      node.querySelector('.line__price').textContent = money(offer.price * line.qty);
      node.querySelector('.line__unit').textContent = line.qty > 1 ? money(offer.price) + ' each' : offer.pack;

      node.querySelector('.line__minus').addEventListener('click', function () {
        line.qty -= 1;
        if (line.qty <= 0) cart.splice(index, 1);
        persistCart();
        paintAll();
      });
      node.querySelector('.line__plus').addEventListener('click', function () {
        line.qty += 1;
        persistCart();
        paintAll();
      });
      node.querySelector('.line__remove').addEventListener('click', function () {
        cart.splice(index, 1);
        persistCart();
        paintAll();
        say(item.name + ' removed.');
      });

      list.appendChild(node);
    });

    var count = itemCount();
    el('itemsTitle').textContent = 'Itemized list (' + count + ' item' + (count === 1 ? '' : 's') + ')';
    el('emptyCart').hidden = cart.length > 0;
    el('clearBtn').hidden = cart.length === 0;
    el('cartBadge').hidden = count === 0;
    el('cartBadge').textContent = count;
    el('navCount').hidden = count === 0;
    el('navCount').textContent = count;
  }

  /* ==================================================================
     Cheaper-brand suggestions
     ================================================================== */

  function swapCandidates() {
    var out = [];

    cart.forEach(function (line) {
      var item = itemById(line.id);
      var offer = offerOf(line);
      if (!item || !offer) return;

      /* Cheapest brand of the same product, by what this basket would pay. */
      var best = CAT.offersOf(item).reduce(function (a, b) { return a.price <= b.price ? a : b; });
      var saving = (offer.price - best.price) * line.qty;
      if (best.brand === offer.brand || saving <= 0) return;

      out.push({ line: line, item: item, from: offer, to: best, saving: saving });
    });

    return out.sort(function (a, b) { return b.saving - a.saving; });
  }

  function paintSwaps() {
    var swaps = swapCandidates();
    var wrap = el('swapList');
    wrap.textContent = '';

    el('swapSection').hidden = swaps.length === 0;

    swaps.slice(0, 4).forEach(function (swap) {
      var node = el('swapTemplate').content.firstElementChild.cloneNode(true);
      var art = node.querySelector('.swap__art');
      var photo = node.querySelector('.swap__photo');
      var source = imageOf(swap.item, swap.to);

      if (source) {
        photo.src = source;
        photo.alt = swap.to.brand + ' ' + swap.item.name;
        photo.addEventListener('error', function () {
          photo.remove();
          art.textContent = swap.to.brand.slice(0, 2).toUpperCase();
        });
      } else {
        photo.remove();
        art.textContent = swap.to.brand.slice(0, 2).toUpperCase();
      }

      node.querySelector('.swap__from').textContent = 'Replace ' + swap.from.brand + ' with';
      node.querySelector('.swap__to').textContent = swap.to.brand + ' ' + swap.item.name;
      node.querySelector('.swap__amount').textContent = money(swap.to.price);
      node.querySelector('.swap__save').textContent = '(save ' + money(swap.saving) + ')';

      node.querySelector('.swap__btn').addEventListener('click', function () {
        swap.line.brand = swap.to.brand;
        persistCart();
        paintAll();
        say('Swapped to ' + swap.to.brand + '. ' + money(swap.saving) + ' saved.');
      });

      wrap.appendChild(node);
    });
  }

  function totalSaving() {
    return swapCandidates().reduce(function (sum, swap) { return sum + swap.saving; }, 0);
  }

  function paintSavings() {
    var saving = totalSaving();
    var total = cartTotal();

    el('savingsCard').hidden = saving <= 0;
    if (saving <= 0) return;

    el('savingsValue').textContent = money(saving);
    el('savingsPill').textContent = total > 0
      ? Math.round(saving / total * 100) + '% off this basket'
      : '';
  }

  /* ==================================================================
     Budget panels
     ================================================================== */

  function paintBudget() {
    var limit = planned();
    var total = cartTotal();
    var soFar = projected();

    el('noBudget').hidden = limit > 0;

    var percent = limit > 0 ? Math.round(soFar / limit * 100) : 0;
    var card = el('progressCard');

    el('progressPercent').textContent = limit > 0 ? percent + '%' : '—';
    el('projectedSpend').textContent = money(soFar);
    el('progressFill').style.width = Math.min(percent, 100) + '%';
    card.classList.toggle('is-over', limit > 0 && soFar > limit);
    card.classList.toggle('is-close', limit > 0 && soFar <= limit && percent >= 85);

    /* Alert banner */
    var over = limit > 0 && soFar > limit;
    el('overAlert').hidden = !over;
    if (over) {
      el('alertBody').textContent = 'This cart puts you ' + money(soFar - limit) + ' past your ' +
        money(limit) + ' grocery budget' +
        (committed() > 0 ? ', including ' + money(committed()) + ' already spent this period.' : '.') +
        ' Swap to a cheaper brand below to pull it back.';
    }

    /* Order summary */
    var count = itemCount();
    el('subtotalLabel').textContent = 'Subtotal (' + count + ' item' + (count === 1 ? '' : 's') + ')';
    el('subtotal').textContent = money(total);
    el('total').textContent = money(total);
    document.querySelector('.summary__row--total').classList.toggle('is-over', over);

    var state = el('budgetState');
    state.hidden = limit <= 0;
    if (limit > 0) {
      var left = limit - soFar;
      state.classList.toggle('is-over', over);
      el('budgetHead').textContent = over ? 'Over budget' : 'Within budget';
      el('budgetBody').textContent = over
        ? 'You would be ' + money(Math.abs(left)) + ' past your grocery budget.'
        : money(left) + ' of your ' + money(limit) + ' grocery budget would be left.';
      el('stateFill').style.width = Math.min(percent, 100) + '%';
    }

    el('checkoutBtn').disabled = cart.length === 0;
  }

  /* ==================================================================
     Checkout — the cart total lands on the grocery budget
     ================================================================== */

  el('checkoutBtn').addEventListener('click', function () {
    if (!cart.length) return;

    var total = cartTotal();
    var limit = planned();
    var after = committed() + total;

    openModal('Confirm checkout', function (body) {
      var p1 = document.createElement('p');
      p1.textContent = 'Checking out ' + itemCount() + ' item' + (itemCount() === 1 ? '' : 's') +
        ' for ' + money(total) + '.';
      body.appendChild(p1);

      var p2 = document.createElement('p');
      if (limit > 0) {
        p2.textContent = 'This is recorded against your grocery budget, taking spending to ' +
          money(after) + ' of ' + money(limit) + '.' +
          (after > limit ? ' That is ' + money(after - limit) + ' over.' : '');
      } else {
        p2.textContent = 'No grocery budget is set, so this is recorded as spending only.';
      }
      body.appendChild(p2);

      var p3 = document.createElement('p');
      p3.textContent = 'Your cart is emptied once this is done.';
      body.appendChild(p3);
    }, [
      { label: 'Keep shopping' },
      { label: 'Confirm checkout', style: 'btn--primary', action: function () {
          /* Keep the shopping list itself. The expense log records how much
             was spent; this records what was bought, which is what the
             Analytics brand comparison reads. */
          var lines = snapshot();

          if (typeof BP.saveOrder === 'function' && lines.length) {
            var stored = BP.saveOrder(profileId, {
              category: 'grocery',
              total: total,
              itemCount: itemCount(),
              note: 'Grocery checkout',
              lines: lines
            });
            if (!stored.ok && window.console) {
              console.warn('Budget Pilot: the shopping list could not be saved. Storage may be full.');
            }
          }

          /* Record the spend on the grocery category. Going through
             addExpenses keeps checkout in the same log the dashboard reads,
             so it shows up in the trend and the transaction list too. */
          BP.addExpenses(profileId, {
            category: 'grocery',
            amount: total,
            description: 'Grocery checkout \u2014 ' + itemCount() +
              ' item' + (itemCount() === 1 ? '' : 's'),
            tags: ['Grocery']
          });

          cart = [];
          persistCart();
          budget = BP.getBudget(profileId);

          closeModal();
          paintAll();
          window.location.href = 'budgets.php?spent=' + encodeURIComponent(total.toFixed(2));
        } }
    ]);
  });

  /* ==================================================================
     Other controls
     ================================================================== */

  el('clearBtn').addEventListener('click', function () {
    openModal('Clear the cart', function (body) {
      var p = document.createElement('p');
      p.textContent = 'This removes all ' + itemCount() + ' items. Nothing is charged to your budget.';
      body.appendChild(p);
    }, [
      { label: 'Cancel' },
      { label: 'Clear all', style: 'btn--danger', action: function () {
          cart = [];
          persistCart();
          closeModal();
          paintAll();
          say('Cart cleared.');
        } }
    ]);
  });

  el('optimizeBtn').addEventListener('click', function () {
    var swaps = swapCandidates();

    if (!swaps.length) {
      say('Every line is already on its cheapest brand.');
      return;
    }

    openModal('Optimization tools', function (body) {
      var p = document.createElement('p');
      p.textContent = 'Switching all ' + swaps.length + ' line' + (swaps.length === 1 ? '' : 's') +
        ' to their cheapest brand saves ' + money(totalSaving()) + '.';
      body.appendChild(p);

      var list = document.createElement('ul');
      list.className = 'checklist';
      swaps.forEach(function (swap) {
        var li = document.createElement('li');
        li.className = 'checkitem is-done';
        li.innerHTML = '<span class="checkitem__mark"></span>';
        var text = document.createElement('span');
        text.textContent = swap.item.name + ': ' + swap.from.brand + ' \u2192 ' + swap.to.brand +
          ' (save ' + money(swap.saving) + ')';
        li.appendChild(text);
        list.appendChild(li);
      });
      body.appendChild(list);
    }, [
      { label: 'Close' },
      { label: 'Swap them all', style: 'btn--primary', action: function () {
          var saved = totalSaving();
          swaps.forEach(function (swap) { swap.line.brand = swap.to.brand; });
          persistCart();
          closeModal();
          paintAll();
          say('All swapped. ' + money(saved) + ' saved.');
        } }
    ]);
  });

  /* ---- Chrome ---- */
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
    window.location.href = 'login.php';
  });

  document.querySelectorAll('[data-soon]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      say(btn.getAttribute('data-soon') + ' is not built yet.');
    });
  });

  /* ==================================================================
     Start
     ================================================================== */

  function paintAll() {
    paintLines();
    paintSwaps();
    paintSavings();
    paintBudget();
  }

  paintAll();
})();
