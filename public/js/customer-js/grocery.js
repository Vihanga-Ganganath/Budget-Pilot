/* Budget Pilot — grocery catalog */

(function () {
  'use strict';

  var BP = window.BudgetPilot;
  var CAT = window.CATALOG;
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

  /* Tile colour behind a product until its picture is added. */
  var TILES = {
    cleaning:'#DCEAF2', personal:'#EFE2EC', dry:'#EFE7D8', cooking:'#EDE3D2',
    beverages:'#E2ECE4', snacks:'#F1E6DA', household:'#E7E9F0'
  };

  var cart = BP.getCart(profileId);
  var chosen = {};   // product id -> brand the shopper is looking at

  /* ==================================================================
     Helpers
     ================================================================== */

  var toast = el('toast');
  var toastTimer = null;

  function say(message) {
    toast.textContent = message;
    toast.hidden = false;
    window.clearTimeout(toastTimer);
    toastTimer = window.setTimeout(function () { toast.hidden = true; }, 3200);
  }

  function money(value) {
    return symbol + Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function unitText(offer) {
    return '(' + money(perUnit(offer)) + (offer.unitLabel === 'each' ? ' each' : '/' + offer.unitLabel) + ')';
  }

  function itemById(id) {
    return CAT.ITEMS.filter(function (i) { return i.id === id; })[0];
  }

  /* Which brand is showing for a product. Defaults to the best value per unit,
     unless the shopper has filtered to one brand or picked one themselves. */
  function currentOffer(item) {
    var brandFilter = el('filterBrand').value;

    if (chosen[item.id]) {
      var picked = CAT.offerByBrand(item, chosen[item.id]);
      if (picked) return picked;
    }
    if (brandFilter !== 'all') {
      var filtered = CAT.offerByBrand(item, brandFilter);
      if (filtered) return filtered;
    }
    return CAT.bestUnitOffer(item);
  }

  function perUnit(offer) { return offer.price / offer.unitQty; }

  /* Each brand has its own picture. Falls back to the product-level one, then
     to a plain tile if neither is filled in. */
  function offerImage(item, offer) {
    return (offer && offer.image ? offer.image : (item.image || '')).trim();
  }

  function unitWord(offer) { return offer.unitLabel === 'each' ? 'item' : offer.unitLabel; }

  function categoryLabel(key) {
    var found = CAT.CATEGORIES.filter(function (c) { return c.key === key; })[0];
    return found ? found.label : key;
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
     Filters
     ================================================================== */

  function fillCategories() {
    var select = el('filterCategory');
    CAT.CATEGORIES.forEach(function (category) {
      var option = document.createElement('option');
      option.value = category.key;
      option.textContent = category.label;
      select.appendChild(option);
    });
  }

  /* Brands narrow to whatever the chosen category actually stocks. */
  function fillBrands() {
    var select = el('filterBrand');
    var current = select.value;
    var brands = CAT.brandsFor(el('filterCategory').value);

    select.textContent = '';
    var all = document.createElement('option');
    all.value = 'all';
    all.textContent = 'Brand: All';
    select.appendChild(all);

    brands.forEach(function (brand) {
      var option = document.createElement('option');
      option.value = brand;
      option.textContent = brand;
      select.appendChild(option);
    });

    select.value = brands.indexOf(current) !== -1 ? current : 'all';
  }

  function inCart(id) {
    return cart.some(function (line) { return line.id === id; });
  }

  function visibleItems() {
    var category = el('filterCategory').value;
    var brand = el('filterBrand').value;
    var range = el('filterPrice').value;
    var query = el('searchBox').value.trim().toLowerCase();
    var sort = el('sortBy').value;

    var list = CAT.ITEMS.filter(function (item) {
      if (category !== 'all' && item.category !== category) return false;

      /* A product stays if any of its brands match. */
      if (brand !== 'all' && !CAT.offerByBrand(item, brand)) return false;

      if (range !== 'all') {
        var bounds = range.split('-');
        var price = currentOffer(item).price;
        if (price < Number(bounds[0]) || price >= Number(bounds[1])) return false;
      }

      if (query) {
        var brands = CAT.offersOf(item).map(function (o) { return o.brand; }).join(' ');
        var haystack = (item.name + ' ' + brands + ' ' + item.blurb + ' ' + categoryLabel(item.category)).toLowerCase();
        if (haystack.indexOf(query) === -1) return false;
      }

      if (el('onlyDeals').checked && CAT.savingPercent(item) < 15) return false;
      if (el('onlyCart').checked && !inCart(item.id)) return false;
      return true;
    });

    if (sort === 'price-asc')  list.sort(function (a, b) { return currentOffer(a).price - currentOffer(b).price; });
    if (sort === 'price-desc') list.sort(function (a, b) { return currentOffer(b).price - currentOffer(a).price; });
    if (sort === 'name')       list.sort(function (a, b) { return a.name.localeCompare(b.name); });
    if (sort === 'unit-asc')   list.sort(function (a, b) { return perUnit(currentOffer(a)) - perUnit(currentOffer(b)); });
    if (sort === 'saving')     list.sort(function (a, b) { return CAT.savingPercent(b) - CAT.savingPercent(a); });

    return list;
  }

  /* ==================================================================
     Product cards
     ================================================================== */

  function buildCard(item) {
    var node = el('productTemplate').content.firstElementChild.cloneNode(true);
    var art = node.querySelector('.product__art');
    var img = node.querySelector('.product__photo');
    var offer = currentOffer(item);

    art.style.setProperty('--tile', TILES[item.category] || '#EDEFF4');

    var source = offerImage(item, offer);
    if (!source) {
      img.remove();
    } else {
      img.src = source;
      img.alt = offer.brand + ' ' + item.name;
      art.classList.add('has-photo');
      img.addEventListener('error', function () {
        img.remove();
        art.classList.remove('has-photo');
        if (window.console) console.warn('Grocery catalog: could not load "' + source + '".');
      });
    }

    /* Badge marks the brand that is best value per unit for this product. */
    var badge = node.querySelector('.product__badge');
    if (offer.brand === CAT.bestUnitOffer(item).brand && CAT.offersOf(item).length > 1) {
      badge.hidden = false;
      badge.textContent = 'Best Value';
    }

    var href = 'product.html?id=' + item.id + '&brand=' + encodeURIComponent(offer.brand);
    art.setAttribute('href', href);
    var link = node.querySelector('.product__link');
    link.textContent = item.name;
    link.setAttribute('href', href);
    node.querySelector('.product__blurb').textContent = item.blurb;

    /* Brand picker — the point of the catalog, so it sits above the price. */
    var picker = node.querySelector('.product__brands');
    CAT.offersOf(item).forEach(function (option) {
      var chip = document.createElement('button');
      chip.type = 'button';
      chip.className = 'brandchip' + (option.brand === offer.brand ? ' is-on' : '');
      chip.textContent = option.brand;
      chip.setAttribute('aria-pressed', String(option.brand === offer.brand));
      chip.addEventListener('click', function () {
        chosen[item.id] = option.brand;
        render();
      });
      picker.appendChild(chip);
    });

    node.querySelector('.product__pack').textContent = offer.pack;
    node.querySelector('.product__price').textContent = money(offer.price);
    node.querySelector('.product__unit').textContent = unitText(offer);

    /* How much switching brand would save, when it is worth saying. */
    var saving = CAT.savingPercent(item);
    var savingNote = node.querySelector('.product__saving');
    if (CAT.offersOf(item).length > 1 && saving >= 5) {
      var best = CAT.bestUnitOffer(item);
      savingNote.textContent = offer.brand === best.brand
        ? 'Cheapest per ' + unitWord(offer) + ' of ' + CAT.offersOf(item).length + ' brands'
        : 'Save ' + saving + '% per ' + unitWord(best) + ' with ' + best.brand;
      savingNote.classList.toggle('is-tip', offer.brand !== best.brand);
    } else {
      savingNote.remove();
    }

    node.querySelector('.product__compare').addEventListener('click', function () { compareBrands(item); });
    node.querySelector('.product__add').addEventListener('click', function () { addToCart(item, offer); });

    return node;
  }

  function render() {
    var list = visibleItems();
    var grid = el('productGrid');
    grid.textContent = '';

    list.forEach(function (item) { grid.appendChild(buildCard(item)); });

    el('noResults').hidden = list.length > 0;

    el('resultCount').textContent = list.length === CAT.ITEMS.length
      ? 'Comparing ' + CAT.ITEMS.length + ' products across ' + CAT.allBrands().length + ' verified brands'
      : 'Showing ' + list.length + ' of ' + CAT.ITEMS.length + ' products';
  }

  /* ==================================================================
     Compare
     ================================================================== */

  /* Puts one product's brands side by side. This is the whole point of the
     catalog: same item, different brand, different price per unit. */
  function compareBrands(item) {
    var offers = CAT.offersOf(item).slice();
    var cheapest = CAT.cheapestOffer(item);
    var bestUnit = CAT.bestUnitOffer(item);

    offers.sort(function (a, b) { return perUnit(a) - perUnit(b); });

    openModal(item.name, function (body) {
      var intro = document.createElement('p');
      intro.textContent = offers.length + ' brands stock this. Sorted by price per ' + unitWord(bestUnit) + '.';
      body.appendChild(intro);

      var table = document.createElement('table');
      table.className = 'ctable';

      var anyPhoto = offers.some(function (o) { return offerImage(item, o); });

      var head = document.createElement('tr');
      var columns = anyPhoto ? [''] : [];
      columns = columns.concat(['Brand', 'Pack', 'Price', 'Per ' + unitWord(bestUnit), '']);
      columns.forEach(function (title) {
        var th = document.createElement('th');
        th.textContent = title;
        head.appendChild(th);
      });
      table.appendChild(head);

      offers.forEach(function (offer) {
        var tr = document.createElement('tr');

        if (anyPhoto) {
          var thumbCell = document.createElement('td');
          var source = offerImage(item, offer);
          if (source) {
            var thumb = document.createElement('img');
            thumb.className = 'ctable__thumb';
            thumb.src = source;
            thumb.alt = '';
            thumb.addEventListener('error', function () { thumb.remove(); });
            thumbCell.appendChild(thumb);
          }
          tr.appendChild(thumbCell);
        }

        var brand = document.createElement('td');
        brand.innerHTML = '<strong></strong>';
        brand.querySelector('strong').textContent = offer.brand;
        if (offer.brand === bestUnit.brand) brand.className = 'win';

        var pack = document.createElement('td');
        pack.textContent = offer.pack;

        var price = document.createElement('td');
        price.textContent = money(offer.price);
        if (offer.brand === cheapest.brand) price.className = 'win';

        var unit = document.createElement('td');
        unit.textContent = money(perUnit(offer));
        if (offer.brand === bestUnit.brand) unit.className = 'win';

        var action = document.createElement('td');
        var pick = document.createElement('button');
        pick.type = 'button';
        pick.className = 'btn btn--outline btn--tiny';
        pick.textContent = 'Add';
        pick.addEventListener('click', function () {
          chosen[item.id] = offer.brand;
          addToCart(item, offer);
          closeModal();
          render();
        });
        action.appendChild(pick);

        tr.appendChild(brand);
        tr.appendChild(pack);
        tr.appendChild(price);
        tr.appendChild(unit);
        tr.appendChild(action);
        table.appendChild(tr);
      });

      body.appendChild(table);

      var note = document.createElement('p');
      note.className = 'budgetnote';
      var saving = CAT.savingPercent(item);

      if (offers.length < 2) {
        note.textContent = 'Only one brand stocks this at the moment.';
      } else if (cheapest.brand === bestUnit.brand) {
        note.textContent = bestUnit.brand + ' is both the cheapest to buy and the best value per ' +
          unitWord(bestUnit) + ' \u2014 ' + saving + '% below the priciest option.';
      } else {
        note.textContent = cheapest.brand + ' costs least up front at ' + money(cheapest.price) +
          ', but ' + bestUnit.brand + ' works out ' + saving + '% cheaper per ' + unitWord(bestUnit) +
          ' because of the larger ' + bestUnit.pack + ' pack.';
      }
      body.appendChild(note);
    }, [{ label: 'Close' }]);
  }

  /* ==================================================================
     Cart
     ================================================================== */

  function persistCart() {
    BP.saveCart(profileId, cart);
    profile = BP.getProfile(profileId);
    paintCart();
  }

  function addToCart(item, offer, quiet) {
    var line = cart.filter(function (l) { return l.id === item.id && l.brand === offer.brand; })[0];
    if (line) line.qty += 1;
    else cart.push({ id: item.id, brand: offer.brand, qty: 1 });

    persistCart();
    if (!quiet) say(offer.brand + ' ' + item.name + ' added to cart.');
    if (el('onlyCart').checked) render();
  }

  function cartCount() {
    return cart.reduce(function (sum, line) { return sum + line.qty; }, 0);
  }

  function lineOffer(line) {
    var item = itemById(line.id);
    if (!item) return null;
    return CAT.offerByBrand(item, line.brand) || CAT.cheapestOffer(item);
  }

  function cartTotal() {
    return cart.reduce(function (sum, line) {
      var offer = lineOffer(line);
      return sum + (offer ? offer.price * line.qty : 0);
    }, 0);
  }

  function paintCart() {
    var count = cartCount();
    el('cartBadge').hidden = count === 0;
    el('cartBadge').textContent = count;
    el('navCount').hidden = count === 0;
    el('navCount').textContent = count;
  }

  function openCart() {
    openModal('Shopping Cart', function (body) {
      if (!cart.length) {
        var empty = document.createElement('p');
        empty.textContent = 'Your cart is empty. Add something from the catalog.';
        body.appendChild(empty);
        return;
      }

      var list = document.createElement('ul');
      list.className = 'cartlist';

      cart.forEach(function (line) {
        var item = itemById(line.id);
        var offer = lineOffer(line);
        if (!item || !offer) return;

        var row = document.createElement('li');
        row.className = 'cartrow';

        var text = document.createElement('span');
        text.className = 'cartrow__text';
        text.innerHTML = '<span class="cartrow__name"></span><span class="cartrow__meta"></span>';
        text.querySelector('.cartrow__name').textContent = item.name;
        text.querySelector('.cartrow__meta').textContent = offer.brand + ' \u00b7 ' + offer.pack + ' \u00b7 ' + money(offer.price);

        var qty = document.createElement('span');
        qty.className = 'qty';

        var minus = document.createElement('button');
        minus.type = 'button';
        minus.textContent = '\u2212';
        minus.setAttribute('aria-label', 'One less ' + item.name);
        minus.addEventListener('click', function () {
          line.qty -= 1;
          if (line.qty <= 0) {
            cart = cart.filter(function (l) { return !(l.id === line.id && l.brand === line.brand); });
          }
          persistCart();
          closeModal();
          openCart();
          if (el('onlyCart').checked) render();
        });

        var value = document.createElement('span');
        value.textContent = line.qty;

        var plus = document.createElement('button');
        plus.type = 'button';
        plus.textContent = '+';
        plus.setAttribute('aria-label', 'One more ' + item.name);
        plus.addEventListener('click', function () {
          line.qty += 1;
          persistCart();
          closeModal();
          openCart();
        });

        qty.appendChild(minus);
        qty.appendChild(value);
        qty.appendChild(plus);

        var price = document.createElement('span');
        price.className = 'cartrow__price';
        price.textContent = money(offer.price * line.qty);

        row.appendChild(text);
        row.appendChild(qty);
        row.appendChild(price);
        list.appendChild(row);
      });

      body.appendChild(list);

      var total = document.createElement('div');
      total.className = 'carttotal';
      total.innerHTML = '<span>Total</span>';
      var strong = document.createElement('strong');
      strong.textContent = money(cartTotal());
      total.appendChild(strong);
      body.appendChild(total);

      /* If a grocery budget is set, say where this basket lands against it. */
      var budget = BP.getBudget(profileId);
      var planned = budget && budget.categories.grocery ? Number(budget.categories.grocery.planned) : 0;

      if (planned > 0) {
        var note = document.createElement('p');
        var spend = cartTotal();
        note.className = 'budgetnote' + (spend > planned ? ' is-over' : '');
        note.textContent = spend > planned
          ? 'That is ' + money(spend - planned) + ' over your ' + money(planned) + ' grocery budget.'
          : money(planned - spend) + ' still left in your ' + money(planned) + ' grocery budget.';
        body.appendChild(note);
      }
    }, cart.length ? [
      { label: 'Keep shopping' },
      { label: 'Empty cart', style: 'btn--danger', action: function () {
          cart = [];
          persistCart();
          closeModal();
          render();
          say('Cart emptied.');
        } }
    ] : [{ label: 'Close' }]);
  }

  el('cartBtn').addEventListener('click', function () { window.location.href = 'cart.html'; });
  if (el('cartNav')) el('cartNav').addEventListener('click', function () { window.location.href = 'cart.html'; });

  /* ==================================================================
     Wiring
     ================================================================== */

  el('filterCategory').addEventListener('change', function () { fillBrands(); render(); });
  ['filterBrand', 'filterPrice', 'sortBy'].forEach(function (id) {
    el(id).addEventListener('change', render);
  });
  ['onlyDeals', 'onlyCart'].forEach(function (id) {
    el(id).addEventListener('change', render);
  });
  el('searchBox').addEventListener('input', render);

  el('moreBtn').addEventListener('click', function () {
    var open = el('morePanel').hidden;
    el('morePanel').hidden = !open;
    el('moreBtn').setAttribute('aria-expanded', String(open));
    if (open) el('searchBox').focus();
  });

  el('resetBtn').addEventListener('click', function () {
    el('filterCategory').value = 'all';
    fillBrands();
    el('filterBrand').value = 'all';   // fillBrands keeps a still-valid brand
    el('filterPrice').value = 'all';
    el('searchBox').value = '';
    el('sortBy').value = 'default';
    el('onlyDeals').checked = false;
    el('onlyCart').checked = false;
    render();
    say('Filters reset.');
  });

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

  /* ---- Start ---- */
  fillCategories();

  /* The detail page breadcrumb links back with ?category=... */
  var wanted = (new RegExp('[?&]category=([^&]*)').exec(window.location.search) || [])[1];
  if (wanted && CAT.CATEGORIES.some(function (c) { return c.key === wanted; })) {
    el('filterCategory').value = wanted;
  }

  fillBrands();
  paintCart();
  render();
})();
