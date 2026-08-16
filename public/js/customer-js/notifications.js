/* Budget Pilot — notifications page

   Renders the feed produced by js/notify.js. Every alert here comes from the
   customer's own budget, expense log, order history or the catalog, so the
   list is empty until there is something real to say. */

(function () {
  'use strict';

  var BP = window.BudgetPilot;
  var NOTIFY = window.BPNotify;
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

  var filter = 'all';
  var showHistory = false;

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
    var n = Number(value) || 0;
    return symbol + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  /* Splits the alert text on its {amount} / {price} / {was} slots so the
     figures can be styled without ever injecting HTML. */
  function writeText(node, item) {
    node.textContent = '';
    var parts = String(item.text).split(/(\{amount\}|\{price\}|\{was\})/);

    parts.forEach(function (part) {
      if (part === '{amount}') {
        var strong = document.createElement('strong');
        strong.className = 'note__amount';
        strong.textContent = money(item.amount);
        node.appendChild(strong);
      } else if (part === '{price}') {
        var now = document.createElement('strong');
        now.className = 'note__now';
        now.textContent = money(item.price);
        node.appendChild(now);
      } else if (part === '{was}') {
        var was = document.createElement('s');
        was.className = 'note__was';
        was.textContent = money(item.was);
        node.appendChild(was);
      } else if (part) {
        node.appendChild(document.createTextNode(part));
      }
    });
  }

  var ICONS = {
    over: '<path d="M12 3.5 22 20H2z"/><path d="M12 9.5v5M12 17.2v.2"/>',
    near: '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/>',
    drop: '<path d="M3 7l7 8 4-4 7 6"/><path d="M21 12v5h-5"/>',
    verified: '<circle cx="12" cy="12" r="9"/><path d="M8.5 12.2l2.4 2.4 4.6-5"/>',
    rollover: '<circle cx="12" cy="12" r="9"/><path d="M8.5 12.2l2.4 2.4 4.6-5"/>'
  };

  function iconFor(kind) {
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" ' +
           'stroke-linecap="round" stroke-linejoin="round">' + (ICONS[kind] || ICONS.verified) + '</svg>';
  }

  /* ==================================================================
     Filter chips
     ================================================================== */

  /* Chip order follows the design: the everyday categories first. */
  var CHIP_ORDER = ['grocery', 'transport', 'clothing', 'rent', 'tuition', 'loan', 'medicine', 'other'];

  function buildChips() {
    var wrap = el('chips');
    wrap.textContent = '';

    var list = [{ key: 'all', label: 'All' }];
    CHIP_ORDER.forEach(function (key) {
      var match = BP.CATEGORIES.filter(function (c) { return c.key === key; })[0];
      if (match) list.push({ key: match.key, label: match.label });
    });

    list.forEach(function (spec) {
      var chip = document.createElement('button');
      chip.type = 'button';
      chip.className = 'chip' + (spec.key === filter ? ' chip--on' : '');
      chip.textContent = spec.label;
      chip.setAttribute('aria-pressed', spec.key === filter ? 'true' : 'false');
      chip.addEventListener('click', function () {
        filter = spec.key;
        buildChips();
        paint();
      });
      wrap.appendChild(chip);
    });
  }

  /* ==================================================================
     One notification
     ================================================================== */

  function actionButton(spec, item) {
    var btn = document.createElement('a');
    btn.className = 'noteact noteact--' + (spec.style || 'soft');
    btn.href = spec.href;
    btn.textContent = spec.label;
    btn.addEventListener('click', function () {
      NOTIFY.markRead(profileId, item.id);
    });
    return btn;
  }

  function buildNote(item) {
    var node = el('noteTemplate').content.firstElementChild.cloneNode(true);

    node.classList.add('note--' + item.tone);
    if (!item.read) node.classList.add('is-unread');
    if (item.dismissed) node.classList.add('is-dismissed');

    node.querySelector('.note__icon').innerHTML = iconFor(item.kind);
    node.querySelector('.note__title').textContent = item.title;
    node.querySelector('.note__time').textContent = NOTIFY.timeAgo(item.at);

    var body = node.querySelector('.note__body');
    var text = node.querySelector('.note__text');

    /* Price alerts put the packshot beside the sentence. */
    if (item.kind === 'drop' && item.image) {
      var media = document.createElement('div');
      media.className = 'note__media';

      var img = document.createElement('img');
      img.src = item.image;
      img.alt = '';
      img.loading = 'lazy';
      img.addEventListener('error', function () { media.removeChild(img); });
      media.appendChild(img);

      var copy = document.createElement('div');
      copy.className = 'note__copy';
      writeText(text, item);
      copy.appendChild(text);

      if (item.percent) {
        var save = document.createElement('p');
        save.className = 'note__save';
        save.textContent = 'Save ' + item.percent + '% by switching brand';
        copy.appendChild(save);
      }

      media.appendChild(copy);
      body.appendChild(media);
    } else {
      writeText(text, item);
    }

    if (item.progress != null) {
      var meter = document.createElement('div');
      meter.className = 'notemeter';
      var fill = document.createElement('span');
      fill.className = 'notemeter__fill';
      fill.style.width = Math.min(item.progress, 100) + '%';
      meter.appendChild(fill);
      body.appendChild(meter);
    }

    var actions = document.createElement('div');
    actions.className = 'note__actions';

    (item.actions || []).forEach(function (spec) {
      actions.appendChild(actionButton(spec, item));
    });

    if (item.dismissed) {
      var restore = document.createElement('button');
      restore.type = 'button';
      restore.className = 'noteact noteact--plain';
      restore.textContent = 'Restore';
      restore.addEventListener('click', function () {
        NOTIFY.restore(profileId, item.id);
        say('Alert restored.');
        paint();
      });
      actions.appendChild(restore);
    } else {
      var dismiss = document.createElement('button');
      dismiss.type = 'button';
      dismiss.className = 'noteact noteact--plain';
      dismiss.textContent = 'Dismiss';
      dismiss.addEventListener('click', function () {
        NOTIFY.dismiss(profileId, item.id);
        say('Alert dismissed. It is kept in your history.');
        paint();
      });
      actions.appendChild(dismiss);
    }

    if (!item.read && !item.dismissed) {
      var read = document.createElement('button');
      read.type = 'button';
      read.className = 'noteact noteact--plain';
      read.textContent = 'Mark as read';
      read.addEventListener('click', function () {
        NOTIFY.markRead(profileId, item.id);
        paint();
      });
      actions.appendChild(read);
    }

    if (actions.childNodes.length) body.appendChild(actions);

    return node;
  }

  /* ==================================================================
     Paint
     ================================================================== */

  function paint() {
    var all = NOTIFY.all(profileId);
    var list = all.filter(function (n) {
      if (showHistory ? false : n.dismissed) return false;
      return filter === 'all' || n.category === filter;
    });

    var notes = el('notes');
    notes.textContent = '';
    list.forEach(function (item) { notes.appendChild(buildNote(item)); });

    var empty = el('emptyMsg');
    if (list.length) {
      empty.hidden = true;
    } else {
      empty.hidden = false;
      empty.textContent = all.length
        ? 'Nothing under this category right now. Pick another filter to see the rest.'
        : 'No alerts yet. Set your category amounts on the Budgets page and log an expense — warnings, price checks and rollovers land here.';
    }

    el('historyLabel').textContent = showHistory
      ? 'Hide notification history'
      : 'View notification history';

    var unread = all.filter(function (n) { return !n.read && !n.dismissed; }).length;
    el('markAllBtn').disabled = unread === 0;

    NOTIFY.paintBell();
  }

  /* ==================================================================
     Chrome
     ================================================================== */

  el('markAllBtn').addEventListener('click', function () {
    NOTIFY.markAllRead(profileId);
    say('All alerts marked as read.');
    paint();
  });

  el('historyBtn').addEventListener('click', function () {
    showHistory = !showHistory;
    paint();
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

  buildChips();
  paint();
})();
