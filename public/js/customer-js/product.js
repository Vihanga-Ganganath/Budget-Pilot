/* Budget Pilot — product detail */

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

  var TILES = {
    cleaning:'#DCEAF2', personal:'#EFE2EC', dry:'#EFE7D8', cooking:'#EDE3D2',
    beverages:'#E2ECE4', snacks:'#F1E6DA', household:'#E7E9F0'
  };

  /* ---- Which product, and which brand of it ---- */
  function param(name) {
    var match = new RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
    return match ? decodeURIComponent(match[1].replace(/\+/g, ' ')) : '';
  }

  var item = CAT.ITEMS.filter(function (i) { return i.id === param('id'); })[0];

  if (!item) {
    window.location.replace('grocery.php');
    return;
  }

  var offer = CAT.offerByBrand(item, param('brand')) || CAT.bestUnitOffer(item);
  var cart = BP.getCart(profileId);

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

  function perUnit(o) { return o.price / o.unitQty; }
  function unitWord(o) { return o.unitLabel === 'each' ? 'ea' : o.unitLabel; }
  function imageFor(o) { return BP.assetUrl((o && o.image ? o.image : (item.image || '')).trim()); }

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
     Gallery
     ================================================================== */

  function paintGallery() {
    var main = el('galleryMain');
    var photo = el('galleryPhoto');
    var source = imageFor(offer);

    main.style.setProperty('--tile', TILES[item.category] || '#EDEFF4');
    el('galleryBrand').textContent = offer.brand;

    if (source) {
      photo.src = source;
      photo.alt = offer.brand + ' ' + item.name;
      photo.hidden = false;
      el('galleryEmpty').hidden = true;
      main.classList.add('has-photo');
      photo.onerror = function () {
        photo.hidden = true;
        el('galleryEmpty').hidden = false;
        main.classList.remove('has-photo');
      };
    } else {
      photo.hidden = true;
      el('galleryEmpty').hidden = false;
      main.classList.remove('has-photo');
    }

    /* One thumbnail per brand — the brands double as the gallery. */
    var thumbs = el('galleryThumbs');
    thumbs.textContent = '';

    CAT.offersOf(item).forEach(function (option) {
      var li = document.createElement('li');
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'thumb' + (option.brand === offer.brand ? ' is-on' : '');
      btn.setAttribute('aria-label', 'View ' + option.brand);

      var src = imageFor(option);
      if (src) {
        var img = document.createElement('img');
        img.src = src;
        img.alt = '';
        img.addEventListener('error', function () {
          img.remove();
          var label = document.createElement('span');
          label.className = 'thumb__label';
          label.textContent = option.brand;
          btn.appendChild(label);
        });
        btn.appendChild(img);
      } else {
        var label = document.createElement('span');
        label.className = 'thumb__label';
        label.textContent = option.brand;
        btn.appendChild(label);
      }

      btn.addEventListener('click', function () { choose(option.brand); });
      li.appendChild(btn);
      thumbs.appendChild(li);
    });
  }

  /* ==================================================================
     Summary
     ================================================================== */

  function stars(rating) {
    var full = Math.floor(rating);
    var half = rating - full >= 0.5;
    var out = '';
    for (var i = 0; i < 5; i++) {
      out += i < full ? '\u2605' : (i === full && half ? '\u2605' : '\u2606');
    }
    return out;
  }

  function paintSummary() {
    el('crumbCategory').textContent = categoryLabel(item.category);
    el('crumbCategory').href = 'grocery.php?category=' + item.category;
    el('crumbName').textContent = item.name;
    el('productName').textContent = item.name;
    document.title = item.name + ' — Budget Pilot';

    var rating = el('rating');
    rating.textContent = '';
    if (offer.rating) {
      var starSpan = document.createElement('span');
      starSpan.className = 'rating__stars';
      starSpan.textContent = stars(offer.rating);
      var score = document.createElement('strong');
      score.textContent = offer.rating.toFixed(1);
      var count = document.createElement('span');
      count.textContent = '(' + Number(offer.reviews).toLocaleString('en-US') + ' reviews)';
      rating.appendChild(starSpan);
      rating.appendChild(score);
      rating.appendChild(count);
    }

    el('price').textContent = money(offer.price);
    el('unitPrice').textContent = money(perUnit(offer)) + ' / ' + unitWord(offer);

    /* Brand picker doubles as the variant selector. */
    var picker = el('brandPicker');
    picker.textContent = '';
    CAT.offersOf(item).forEach(function (option) {
      var chip = document.createElement('button');
      chip.type = 'button';
      chip.className = 'brandchip' + (option.brand === offer.brand ? ' is-on' : '');
      chip.textContent = option.brand;
      chip.setAttribute('aria-pressed', String(option.brand === offer.brand));
      chip.addEventListener('click', function () { choose(option.brand); });
      picker.appendChild(chip);
    });

    var specs = el('specs');
    specs.textContent = '';
    [['Package size', offer.pack], ['Origin', offer.origin || item.origin], ['Storage', item.storage]]
      .forEach(function (row) {
        if (!row[1]) return;
        var wrap = document.createElement('div');
        wrap.className = 'specs__row';
        var dt = document.createElement('dt');
        dt.textContent = row[0];
        var dd = document.createElement('dd');
        dd.textContent = row[1];
        wrap.appendChild(dt);
        wrap.appendChild(dd);
        specs.appendChild(wrap);
      });

    el('description').textContent = offer.description || item.description || item.blurb;
  }

  function paintFacts() {
    el('factsTitle').textContent = item.factsTitle || 'Product Facts';
    el('factsNote').textContent = item.factsNote ? '*' + item.factsNote : '';

    var list = el('factsList');
    list.textContent = '';

    (item.facts || []).forEach(function (fact) {
      var row = document.createElement('li');
      row.className = 'facts__row';
      var label = document.createElement('span');
      label.textContent = fact.label;
      var value = document.createElement('strong');
      value.textContent = fact.value;
      row.appendChild(label);
      row.appendChild(value);
      list.appendChild(row);
    });
  }

  /* ==================================================================
     Alternatives — the other brands of this same product
     ================================================================== */

  function paintAlternatives() {
    var wrap = el('alternatives');
    wrap.textContent = '';

    var all = CAT.offersOf(item).slice().sort(function (a, b) { return perUnit(a) - perUnit(b); });
    var bestUnit = CAT.bestUnitOffer(item);
    var biggestPack = CAT.offersOf(item).reduce(function (a, b) { return a.unitQty >= b.unitQty ? a : b; });

    if (all.length < 2) {
      var none = document.createElement('section');
      none.className = 'card alt alt--empty';
      none.textContent = 'Only one brand stocks this product at the moment.';
      wrap.appendChild(none);
      return;
    }

    all.forEach(function (option) {
      var isCurrent = option.brand === offer.brand;
      var card = document.createElement('section');
      card.className = 'card alt' + (isCurrent ? ' alt--current' : '');

      var head = document.createElement('div');
      head.className = 'alt__head';

      var pill = document.createElement('span');
      if (isCurrent) {
        pill.className = 'pill pill--current';
        pill.textContent = 'Viewing';
      } else if (option.brand === bestUnit.brand) {
        pill.className = 'pill pill--best';
        pill.textContent = 'Best value';
      } else if (option.brand === biggestPack.brand && biggestPack.unitQty > offer.unitQty) {
        pill.className = 'pill pill--bulk';
        pill.textContent = 'Bulk savings';
      } else {
        pill.className = 'pill pill--bulk';
        pill.textContent = 'Alternative';
      }

      var price = document.createElement('span');
      price.className = 'alt__price';
      price.textContent = money(option.price);

      head.appendChild(pill);
      head.appendChild(price);

      var body = document.createElement('div');
      body.className = 'alt__body';

      var thumb = document.createElement('span');
      thumb.className = 'alt__thumb';
      var src = imageFor(option);
      if (src) {
        var img = document.createElement('img');
        img.src = src;
        img.alt = '';
        img.addEventListener('error', function () {
          img.remove();
          thumb.textContent = option.brand.slice(0, 2).toUpperCase();
        });
        thumb.appendChild(img);
      } else {
        thumb.textContent = option.brand.slice(0, 2).toUpperCase();
      }

      var text = document.createElement('span');
      text.className = 'alt__text';
      var brandName = document.createElement('span');
      brandName.className = 'alt__brand';
      brandName.textContent = option.brand;

      var note = document.createElement('span');
      note.className = 'alt__note';
      var diff = Math.round((perUnit(offer) - perUnit(option)) / perUnit(offer) * 100);
      if (isCurrent) {
        note.textContent = money(perUnit(option)) + ' per ' + unitWord(option) + ' \u00b7 ' + option.pack;
      } else if (diff > 0) {
        note.classList.add('is-save');
        note.textContent = 'Save ' + diff + '% per ' + unitWord(option) + ' \u00b7 ' + option.pack;
      } else if (diff < 0) {
        note.textContent = Math.abs(diff) + '% dearer per ' + unitWord(option) + ' \u00b7 ' + option.pack;
      } else {
        note.textContent = 'Same per ' + unitWord(option) + ' \u00b7 ' + option.pack;
      }

      text.appendChild(brandName);
      text.appendChild(note);
      body.appendChild(thumb);
      body.appendChild(text);

      var link = document.createElement('button');
      link.type = 'button';
      link.className = 'alt__link';
      link.textContent = isCurrent ? 'Compare all brands \u2192' : 'Switch to this brand \u2192';
      link.addEventListener('click', function () {
        if (isCurrent) compareAll();
        else choose(option.brand);
      });

      card.appendChild(head);
      card.appendChild(body);
      card.appendChild(link);
      wrap.appendChild(card);
    });
  }

  /* ==================================================================
     Compare
     ================================================================== */

  function compareAll() {
    var offers = CAT.offersOf(item).slice().sort(function (a, b) { return perUnit(a) - perUnit(b); });
    var cheapest = CAT.cheapestOffer(item);
    var bestUnit = CAT.bestUnitOffer(item);

    openModal(item.name, function (body) {
      var intro = document.createElement('p');
      intro.textContent = offers.length + ' brands stock this. Sorted by price per ' + unitWord(bestUnit) + '.';
      body.appendChild(intro);

      var table = document.createElement('table');
      table.className = 'ctable';

      var head = document.createElement('tr');
      ['Brand', 'Pack', 'Price', 'Per ' + unitWord(bestUnit), ''].forEach(function (title) {
        var th = document.createElement('th');
        th.textContent = title;
        head.appendChild(th);
      });
      table.appendChild(head);

      offers.forEach(function (option) {
        var tr = document.createElement('tr');

        var brand = document.createElement('td');
        brand.innerHTML = '<strong></strong>';
        brand.querySelector('strong').textContent = option.brand;
        if (option.brand === bestUnit.brand) brand.className = 'win';

        var pack = document.createElement('td');
        pack.textContent = option.pack;

        var price = document.createElement('td');
        price.textContent = money(option.price);
        if (option.brand === cheapest.brand) price.className = 'win';

        var unit = document.createElement('td');
        unit.textContent = money(perUnit(option));
        if (option.brand === bestUnit.brand) unit.className = 'win';

        var action = document.createElement('td');
        var pick = document.createElement('button');
        pick.type = 'button';
        pick.className = 'btn btn--outline btn--tiny';
        pick.textContent = 'View';
        pick.addEventListener('click', function () {
          closeModal();
          choose(option.brand);
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
      note.textContent = cheapest.brand === bestUnit.brand
        ? bestUnit.brand + ' is both the cheapest to buy and the best value per ' + unitWord(bestUnit) +
          ', ' + saving + '% below the priciest option.'
        : cheapest.brand + ' costs least up front at ' + money(cheapest.price) + ', but ' + bestUnit.brand +
          ' works out ' + saving + '% cheaper per ' + unitWord(bestUnit) + ' thanks to the ' + bestUnit.pack + ' pack.';
      body.appendChild(note);
    }, [{ label: 'Close' }]);
  }

  el('compareBtn').addEventListener('click', compareAll);

  /* ==================================================================
     Cart
     ================================================================== */

  function paintCart() {
    var count = cart.reduce(function (sum, line) { return sum + line.qty; }, 0);
    el('cartBadge').hidden = count === 0;
    el('cartBadge').textContent = count;
    el('navCount').hidden = count === 0;
    el('navCount').textContent = count;
  }

  el('addBtn').addEventListener('click', function () {
    var line = cart.filter(function (l) { return l.id === item.id && l.brand === offer.brand; })[0];
    if (line) line.qty += 1;
    else cart.push({ id: item.id, brand: offer.brand, qty: 1 });

    BP.saveCart(profileId, cart);
    paintCart();
    say(offer.brand + ' ' + item.name + ' added to cart.');
  });

  el('cartBtn').addEventListener('click', function () { window.location.href = 'cart.php'; });
  if (el('cartNav')) el('cartNav').addEventListener('click', function () { window.location.href = 'cart.php'; });

  /* ---- Save for later ---- */
  el('saveBtn').addEventListener('click', function () {
    var on = el('saveBtn').getAttribute('aria-pressed') === 'true';
    el('saveBtn').setAttribute('aria-pressed', String(!on));
    say(on ? 'Removed from saved items.' : 'Saved for later.');
  });

  /* ==================================================================
     Switching brand
     ================================================================== */

  function choose(brand) {
    var next = CAT.offerByBrand(item, brand);
    if (!next) return;
    offer = next;

    /* Keep the address bar in step so the page can be shared or reloaded. */
    if (window.history && window.history.replaceState) {
      window.history.replaceState({}, '', 'product.php?id=' + item.id + '&brand=' + encodeURIComponent(brand));
    }
    paintAll();
  }

  function paintAll() {
    paintGallery();
    paintSummary();
    paintFacts();
    paintAlternatives();
  }

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

  paintAll();
  paintCart();
})();
