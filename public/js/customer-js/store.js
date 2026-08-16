/* ==========================================================================
   Budget Pilot — shared profile store
   --------------------------------------------------------------------------
   Saves accounts in the browser's localStorage so the create-account page and
   the customer sign-in page share the same profiles.

   IMPORTANT — this is a front-end demo store, not real authentication.
   Passwords are hashed with a simple non-cryptographic hash purely so plain
   text isn't sitting in localStorage. Anyone with access to the browser can
   read and edit this data. Real sign-in must be verified on a server.
   ========================================================================== */

window.BudgetPilot = (function () {
  'use strict';

  var KEY = 'budgetPilot.v1';

  /* Resolves a catalog/category image path (e.g. "img/1.1.jpg") against the
     app's public assets folder, so it works no matter which URL the current
     page was reached through. */
  function assetUrl(path) {
    if (!path) return path;
    var root = window.URLROOT || '';
    return root + '/public/assets/' + String(path).replace(/^\/+/, '');
  }

  /* ---------- Storage ---------- */

  function read() {
    try {
      var raw = window.localStorage.getItem(KEY);
      var data = raw ? JSON.parse(raw) : null;
      if (!data || !Array.isArray(data.profiles)) return { profiles: [] };
      return data;
    } catch (e) {
      return { profiles: [] };
    }
  }

  function write(data) {
    try {
      window.localStorage.setItem(KEY, JSON.stringify(data));
      return true;
    } catch (e) {
      return false;   // quota exceeded, or storage blocked
    }
  }

  function available() {
    try {
      window.localStorage.setItem('__bp_test', '1');
      window.localStorage.removeItem('__bp_test');
      return true;
    } catch (e) {
      return false;
    }
  }

  /* ---------- Password hashing (obfuscation only) ---------- */

  function hash(text) {
    var h = 5381;
    var salted = 'bp::' + String(text);
    for (var i = 0; i < salted.length; i++) {
      h = ((h << 5) + h) + salted.charCodeAt(i);
      h = h & h;                       // keep it a 32-bit int
    }
    return 'h' + (h >>> 0).toString(36);
  }

  /* ---------- Profiles ---------- */

  function getProfiles() {
    return read().profiles;
  }

  function emailTaken(email) {
    var target = String(email).trim().toLowerCase();
    return getProfiles().some(function (p) { return p.email === target; });
  }

  /* profiles: array of { name, email, password, avatar, role, ...extras } */
  function saveProfiles(profiles) {
    var data = read();

    profiles.forEach(function (p) {
      data.profiles.push({
        id: 'p' + Date.now().toString(36) + Math.random().toString(36).slice(2, 7),
        name: String(p.name || '').trim(),
        email: String(p.email || '').trim().toLowerCase(),
        passwordHash: hash(p.password),
        avatar: p.avatar || null,
        role: p.role || 'Member',
        gender: p.gender || '',
        age: p.age || '',
        nic: p.nic || '',
        income: p.income || '',
        baseIncome: p.income || '',   /* what they typed at sign-up, kept apart
                                         from anything added later */
        savings: p.savings || '',
        prefs: { theme: 'light', alerts: true, currency: 'USD', fiscalStart: 'January' },
        finance: { sources: [], payslips: [] }
      });
    });

    return write(data);
  }

  /* Returns { ok, reason } — reason is 'email' or 'password' when it fails. */
  function verify(profileId, email, password) {
    var profile = getProfiles().filter(function (p) { return p.id === profileId; })[0];
    if (!profile) return { ok: false, reason: 'profile' };

    if (profile.email !== String(email).trim().toLowerCase()) {
      return { ok: false, reason: 'email', profile: profile };
    }
    if (profile.passwordHash !== hash(password)) {
      return { ok: false, reason: 'password', profile: profile };
    }
    return { ok: true, profile: profile };
  }

  function getProfile(id) {
    return getProfiles().filter(function (p) { return p.id === id; })[0] || null;
  }

  /* Applies a patch to one profile. Pass `password` to set a new one. */
  function updateProfile(id, patch) {
    var data = read();
    var found = false;

    data.profiles = data.profiles.map(function (p) {
      if (p.id !== id) return p;
      found = true;

      var next = {};
      Object.keys(p).forEach(function (k) { next[k] = p[k]; });
      Object.keys(patch).forEach(function (k) { next[k] = patch[k]; });

      if (patch.password) {
        next.passwordHash = hash(patch.password);
        delete next.password;
      }
      if (patch.email) next.email = String(patch.email).trim().toLowerCase();

      next.prefs = next.prefs || { theme: 'light', alerts: true, currency: 'USD', fiscalStart: 'January' };
      next.finance = next.finance || { sources: [], payslips: [] };
      return next;
    });

    if (!found) return false;
    return write(data);
  }

  /* True when the email belongs to somebody other than `id`. */
  function emailTakenByOther(email, id) {
    var target = String(email).trim().toLowerCase();
    return getProfiles().some(function (p) { return p.id !== id && p.email === target; });
  }

  function checkPasswordFor(id, password) {
    var profile = getProfile(id);
    return !!profile && profile.passwordHash === hash(password);
  }

  /* ---------- Session ---------- */

  var SESSION_KEY = 'budgetPilot.session';

  function setSession(id) {
    try { window.localStorage.setItem(SESSION_KEY, id); return true; } catch (e) { return false; }
  }
  function getSession() {
    try { return window.localStorage.getItem(SESSION_KEY); } catch (e) { return null; }
  }
  function endSession() {
    try { window.localStorage.removeItem(SESSION_KEY); } catch (e) {}
  }

  /* Deletes a profile. If the main holder is removed while others remain,
     the next profile is promoted so the household always has a main. */
  function removeProfile(id) {
    var data = read();
    var removed = data.profiles.filter(function (p) { return p.id === id; })[0];
    if (!removed) return { ok: false };

    data.profiles = data.profiles.filter(function (p) { return p.id !== id; });

    var promoted = null;
    if (removed.role === 'Main' && data.profiles.length > 0) {
      data.profiles[0].role = 'Main';
      promoted = data.profiles[0];
    }

    write(data);
    if (getSession() === id) endSession();
    return { ok: true, removed: removed, promoted: promoted, remaining: data.profiles.length };
  }

  /* ---------- Household ----------

     One account, several people. The person who signed up holds role 'Main';
     anyone added afterwards holds 'Member'. There is ONE budget and ONE
     expense log for the whole household, both kept on the main profile, so a
     member's shopping comes off the same category everybody else is spending
     from.

     What differs is who can see what:
       - the main holder sees every transaction, whoever made it
       - a member sees only their own transactions, but the same running
         budget totals as everyone else

     Every expense carries `by` (the profile that logged it) so that split can
     be made. Rows saved before members existed have no `by` and are treated as
     the main holder's. */

  function mainProfile() {
    return getProfiles().filter(function (p) { return p.role === 'Main'; })[0] || null;
  }

  function isMain(id) {
    var p = getProfile(id);
    return !!p && p.role === 'Main';
  }

  /* The profile that owns the shared budget and expense log. Falls back to the
     caller when there is no main holder, so a lone profile still works. */
  function householdId(id) {
    if (isMain(id)) return id;
    var main = mainProfile();
    return main ? main.id : id;
  }

  /* Everyone on the account, main holder first. */
  function householdMembers() {
    var all = getProfiles();
    return all.filter(function (p) { return p.role === 'Main'; })
      .concat(all.filter(function (p) { return p.role !== 'Main'; }));
  }

  /* True when this expense row belongs to the given person. */
  function loggedBy(entry, id) {
    if (entry.by) return entry.by === id;
    return isMain(id);   /* pre-household rows belong to the account holder */
  }

  /* ---------- Income sources (Financial Data) ----------

     Each person's pay is recorded on their own profile as a list of sources.
     Sources are paid at different rhythms, so every one is converted to a
     monthly figure before it counts. One-off money is not something a monthly
     plan can lean on, so it counts as nothing here.

     Recorded sources are what "monthly income" means from then on: the figure
     typed straight onto the profile is only used while the list is empty. */

  function monthlyFromSource(source) {
    var amount = Number(source && source.amount) || 0;
    var freq = source && source.frequency;
    if (freq === 'weekly') return amount * 52 / 12;
    if (freq === 'annual') return amount / 12;
    if (freq === 'once') return 0;
    return amount;
  }

  function sourcesTotal(list) {
    if (!Array.isArray(list)) return 0;
    var total = list.reduce(function (sum, s) { return sum + monthlyFromSource(s); }, 0);
    return Math.round(total * 100) / 100;
  }

  /* The figure entered at sign-up, held separately so that recording income
     sources later adds to it instead of replacing it. Profiles made before
     this field existed keep their typed income as the starting figure. */
  function baseIncome(person) {
    if (!person) return 0;
    if (person.baseIncome !== undefined && person.baseIncome !== null && person.baseIncome !== '') {
      return Number(person.baseIncome) || 0;
    }
    /* Nothing recorded yet, so `income` is still just what was typed. */
    var list = Array.isArray(person.incomeSources) ? person.incomeSources : [];
    if (list.length) return 0;
    return Number(person.income) || 0;
  }

  /* What one person earns a month: their starting income plus everything
     recorded under Financial Data. */
  function personIncome(person) {
    if (!person) return 0;
    var list = Array.isArray(person.incomeSources) ? person.incomeSources : [];
    return Math.round((baseIncome(person) + sourcesTotal(list)) * 100) / 100;
  }

  /* Financial data is the account holder's to manage — for everyone on the
     account, themselves included. A member cannot record their own, because
     what each person earns decides the whole household's plan. A lone profile
     is its own holder, so nothing is locked until a second person exists. */
  function canEditFinance(actorId) {
    if (isMain(actorId)) return true;
    return householdMembers().length < 2;
  }

  /* Saves one person's income sources and pay sheets, and keeps their monthly
     income in step with what those sources add up to. */
  function saveFinance(actorId, targetId, sources, documents, startingIncome) {
    if (!canEditFinance(actorId)) return { ok: false, reason: 'not-main' };

    var target = getProfile(targetId);
    if (!target) return { ok: false, reason: 'profile' };

    var list = Array.isArray(sources) ? sources : [];
    var base = startingIncome === undefined || startingIncome === null || startingIncome === ''
      ? baseIncome(target)
      : Math.max(Number(startingIncome) || 0, 0);

    var patch = {
      incomeSources: list,
      documents: Array.isArray(documents) ? documents : [],
      baseIncome: String(base),
      income: String(Math.round((base + sourcesTotal(list)) * 100) / 100)
    };

    return updateProfile(targetId, patch);
  }

  /* Sets only the starting figure, leaving recorded sources untouched. */
  function setBaseIncome(actorId, targetId, amount) {
    if (!canEditFinance(actorId)) return { ok: false, reason: 'not-main' };

    var target = getProfile(targetId);
    if (!target) return { ok: false, reason: 'profile' };

    var base = Math.max(Number(amount) || 0, 0);
    var extra = sourcesTotal(target.incomeSources);

    return updateProfile(targetId, {
      baseIncome: base ? String(base) : '',
      income: base || extra ? String(Math.round((base + extra) * 100) / 100) : ''
    });
  }

  /* ---------- Family income ----------

     The budget is planned against what the household earns altogether, so each
     person's salary is held on their own profile and the plan works from the
     sum. Only the account holder enters these figures — a member cannot set
     their own, and cannot set anyone else's. */

  function memberIncomes(id) {
    return householdMembers().map(function (person) {
      return {
        id: person.id,
        name: person.name,
        role: person.role,
        avatar: person.avatar || null,
        email: person.email,
        income: personIncome(person),
        hasSources: Array.isArray(person.incomeSources) && person.incomeSources.length > 0
      };
    });
  }

  /* Everything the household earns in a month. */
  function familyIncome(id) {
    return memberIncomes(id).reduce(function (sum, person) { return sum + person.income; }, 0);
  }

  function setMemberIncome(actorId, memberId, amount) {
    if (!isMain(actorId) && actorId !== memberId) return { ok: false, reason: 'not-main' };
    if (!isMain(actorId) && mainProfile()) return { ok: false, reason: 'not-main' };

    /* What is typed here is that person's starting income. Anything recorded
       under Financial Data is added on top of it, so the two never collide. */
    var value = Math.max(Number(amount) || 0, 0);
    var target = getProfile(memberId);
    var extra = target ? sourcesTotal(target.incomeSources) : 0;

    return updateProfile(memberId, {
      baseIncome: value ? String(value) : '',
      income: value || extra ? String(Math.round((value + extra) * 100) / 100) : ''
    });
  }

  /* The household's whole spending log, open to everyone on the account. The
     charts are meant to show what the family spends as a whole; use
     getExpenses when listing individual transactions, which stays private. */
  function familyExpenses(id) {
    var p = getProfile(householdId(id));
    if (!p || !Array.isArray(p.expenses)) return [];
    return p.expenses;
  }

  /* The categories this person has actually spent in — used to keep their
     alerts to things they are part of. */
  function categoriesUsedBy(id) {
    var seen = {};
    familyExpenses(id).forEach(function (entry) {
      if (loggedBy(entry, id) && entry.category) seen[entry.category] = true;
    });
    return seen;
  }

  /* ---------- Budgets ---------- */

  /* Spending categories in display order. Each weight is that category's
     percentage of gross monthly income. The weights add up to 94, not 100 —
     the remaining 6% is deliberately left unallocated as a buffer (see
     suggestBudget). Medicine, clothing and other share the same weight, so
     those three are suggested the same amount. */
  var CATEGORIES = [
    { key: 'grocery',   label: 'Grocery',   weight: 15 },
    { key: 'transport', label: 'Transport', weight: 10 },
    { key: 'rent',      label: 'Rent',      weight: 13 },
    { key: 'loan',      label: 'Loan',      weight: 33 },
    { key: 'tuition',   label: 'Tuition',   weight: 8 },
    { key: 'medicine',  label: 'Medicine',  weight: 5 },
    { key: 'clothing',  label: 'Clothing',  weight: 5 },
    { key: 'other',     label: 'Other',     weight: 5 }
  ];

  /* Sum of the weights above — the share of income that gets allocated.
     Whatever is left of 100 comes back from suggestBudget as `buffer`. */
  var WEIGHT_TOTAL = CATEGORIES.reduce(function (sum, c) { return sum + c.weight; }, 0);

  function emptyBudget(income) {
    var categories = {};
    CATEGORIES.forEach(function (c) {
      categories[c.key] = { planned: 0, spent: 0, suggested: null };
    });
    return { income: income || '', categories: categories, finalized: false, savedAt: null };
  }

  /* The budget belongs to the household, not the person reading it, so a
     member sees exactly the same figures as the main holder. */
  function getBudget(id) {
    var p = getProfile(householdId(id));
    if (!p) return null;
    var stored = p.budget;
    var base = emptyBudget(familyIncome(id));

    if (!stored) return base;

    /* Income is read live from the profiles rather than from whatever was
       stored alongside the plan, so financial data added in Settings shows up
       here straight away. The planned amounts are left exactly as they were —
       what the extra income does is widen the unallocated remainder. */
    base.finalized = !!stored.finalized;
    base.savedAt = stored.savedAt || null;

    /* Nobody has an income on file yet — fall back to whatever was typed
       against the plan itself so it is not lost. */
    if (!(familyIncome(id) > 0) && stored.income != null) base.income = stored.income;

    CATEGORIES.forEach(function (c) {
      var row = (stored.categories || {})[c.key] || {};
      base.categories[c.key] = {
        planned: Number(row.planned) || 0,
        spent: Number(row.spent) || 0,
        suggested: row.suggested == null ? null : Number(row.suggested)
      };
    });
    return base;
  }

  /* Setting the plan is the account holder's job. A member spending against
     it must not be able to rewrite everyone's categories, so their save is
     refused rather than quietly written to their own profile. */
  function saveBudget(id, budget) {
    if (!isMain(id) && mainProfile()) return { ok: false, reason: 'not-main' };
    return updateProfile(householdId(id), { budget: budget });
  }

  /* Splits the income across the categories, each getting its own weight as
     a percentage of gross income. The weights stop short of 100, so what is
     not handed out comes back as `buffer` — an untouched cushion available
     for savings or unplanned costs. allocated + buffer always equals income.

     Note: this deliberately ignores the profile's `savings` field. That is a
     savings balance captured at signup, not a monthly set-aside, and taking
     it off the top made every category shrink by an unrelated amount. */
  function suggestBudget(income) {
    var total = Math.max(Number(income) || 0, 0);

    var result = {
      income: total,
      spendable: total,
      allocations: {},
      allocated: 0,
      buffer: 0,
      bufferPercent: 100 - WEIGHT_TOTAL
    };
    var running = 0;

    CATEGORIES.forEach(function (c) {
      var share = Math.round(total * c.weight / 100);
      running += share;
      result.allocations[c.key] = share;
    });

    /* Rounding can push the parts a hair past the buffer; never report a
       negative cushion. */
    result.allocated = Math.min(running, total);
    result.buffer = Math.max(total - running, 0);

    return result;
  }

  /* ---------- Manual expenses ---------- */

  /* The household's whole log, newest last. Only the main holder should ever
     see this in full — everything else goes through getExpenses. */
  function householdExpenses(id) {
    var p = getProfile(householdId(id));
    if (!p || !Array.isArray(p.expenses)) return [];
    return p.expenses;
  }

  /* What this person is allowed to see: the main holder gets every
     transaction on the account, a member gets only their own. Both read the
     same budget totals elsewhere, so a member still sees what is left. */
  function getExpenses(id) {
    var log = householdExpenses(id);
    if (isMain(id)) return log;
    return log.filter(function (entry) { return loggedBy(entry, id); });
  }

  /* Every transaction with the name of whoever logged it. Refused for anyone
     but the main holder. */
  function allExpenses(id) {
    if (!isMain(id)) return [];
    var names = {};
    getProfiles().forEach(function (p) { names[p.id] = p.name; });
    var main = mainProfile();

    return householdExpenses(id).map(function (entry) {
      var by = entry.by || (main ? main.id : id);
      var row = {};
      Object.keys(entry).forEach(function (k) { row[k] = entry[k]; });
      row.by = by;
      row.byName = entry.byName || names[by] || 'Removed member';
      return row;
    });
  }

  /* What each person on the account has spent, newest activity included. */
  function memberSpend(id) {
    if (!isMain(id)) return [];
    var totals = {};

    allExpenses(id).forEach(function (entry) {
      var row = totals[entry.by] || (totals[entry.by] = { total: 0, count: 0, last: null });
      row.total += Number(entry.amount) || 0;
      row.count++;
      var when = entry.loggedAt || entry.date || null;
      if (when && (!row.last || when > row.last)) row.last = when;
    });

    return householdMembers().map(function (person) {
      var row = totals[person.id] || { total: 0, count: 0, last: null };
      return {
        id: person.id,
        name: person.name,
        email: person.email,
        avatar: person.avatar || null,
        role: person.role,
        total: row.total,
        count: row.count,
        last: row.last
      };
    });
  }

  /* Records one or more expenses and adds each amount to its category's
     `spent`, which is what shrinks the remaining budget on the Budgets page.
     Entries with no amount or an unknown category are skipped rather than
     silently mis-filed. Returns { ok, recorded, total, skipped }. */
  function addExpenses(id, entries) {
    var p = getProfile(id);
    if (!p) return { ok: false, reason: 'profile' };

    var list = Array.isArray(entries) ? entries : [entries];
    var budget = getBudget(id);
    var log = householdExpenses(id).slice();   /* the shared log, not just theirs */
    var recorded = [];
    var skipped = 0;
    var total = 0;

    list.forEach(function (entry) {
      var key = entry && entry.category;
      var amount = Math.max(Number(entry && entry.amount) || 0, 0);

      if (!key || !budget.categories[key] || amount <= 0) { skipped++; return; }

      budget.categories[key].spent = (Number(budget.categories[key].spent) || 0) + amount;
      total += amount;

      var row = {
        id: 'e' + Date.now().toString(36) + Math.random().toString(36).slice(2, 7),
        category: key,
        amount: amount,
        date: entry.date || new Date().toISOString().slice(0, 10),
        description: String(entry.description || '').trim(),
        notes: String(entry.notes || '').trim(),
        tags: Array.isArray(entry.tags) ? entry.tags.slice() : [],
        loggedAt: new Date().toISOString(),
        by: id,
        byName: p.name
      };
      log.push(row);
      recorded.push(row);
    });

    if (!recorded.length) return { ok: false, reason: 'empty', skipped: skipped };

    budget.savedAt = new Date().toISOString();
    var result = updateProfile(householdId(id), { expenses: log, budget: budget });
    if (!result || !result.ok) return { ok: false, reason: 'storage' };

    return { ok: true, recorded: recorded, total: total, skipped: skipped };
  }

  /* ---------- Shopping cart ---------- */

  function getCart(id) {
    var p = getProfile(id);
    if (!p || !Array.isArray(p.cart)) return [];
    return p.cart;
  }

  /* cart entries: { id, qty } — item details are looked up in the catalog. */
  function saveCart(id, cart) {
    return updateProfile(id, { cart: cart });
  }

  /* ---------- Current session ---------- */

  var SESSION_KEY = 'budgetPilot.session';

  function setSession(id) {
    try { window.localStorage.setItem(SESSION_KEY, id); } catch (e) {}
  }

  function getSession() {
    try { return window.localStorage.getItem(SESSION_KEY); } catch (e) { return null; }
  }

  function clearSession() {
    try { window.localStorage.removeItem(SESSION_KEY); } catch (e) {}
  }

  function getProfile(id) {
    return getProfiles().filter(function (p) { return p.id === id; })[0] || null;
  }

  /* Merges the given fields into a profile and saves. */
  function updateProfile(id, patch) {
    var data = read();
    var found = false;

    data.profiles = data.profiles.map(function (p) {
      if (p.id !== id) return p;
      found = true;

      Object.keys(patch).forEach(function (key) {
        if (key === 'password') {
          if (patch.password) p.passwordHash = hash(patch.password);
          return;
        }
        if (key === 'prefs') {
          p.prefs = Object.assign({}, p.prefs || {}, patch.prefs);
          return;
        }
        p[key] = patch[key];
      });
      return p;
    });

    if (!found) return { ok: false };
    return { ok: write(data) };
  }

  /* True when the email belongs to someone other than the given profile. */
  function emailTakenByOther(email, id) {
    var target = String(email).trim().toLowerCase();
    return getProfiles().some(function (p) { return p.email === target && p.id !== id; });
  }

  function clearAll() {
    try { window.localStorage.removeItem(KEY); } catch (e) {}
  }

  /* ---------- Avatar helpers ---------- */

  function initials(name) {
    var parts = String(name).trim().split(/\s+/).filter(Boolean);
    if (!parts.length) return '?';
    if (parts.length === 1) return parts[0].charAt(0).toUpperCase();
    return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
  }

  var SWATCHES = ['#2F3F66', '#1E7FB0', '#22A45D', '#8A5A3B', '#6B4A8F', '#B67A18'];

  function avatarColor(seed) {
    var text = String(seed || '');
    var sum = 0;
    for (var i = 0; i < text.length; i++) sum += text.charCodeAt(i);
    return SWATCHES[sum % SWATCHES.length];
  }

  /* Reads an image file, crops it square and scales it down so it fits
     comfortably in localStorage. Calls back with a data URL. */
  function readImage(file, size, callback) {
    if (!file || !/^image\//.test(file.type)) {
      callback(null, 'Choose an image file, such as a JPG or PNG.');
      return;
    }
    if (file.size > 8 * 1024 * 1024) {
      callback(null, 'That image is over 8MB. Choose a smaller one.');
      return;
    }

    var reader = new FileReader();
    reader.onerror = function () { callback(null, "That image couldn't be read."); };
    reader.onload = function () {
      var img = new Image();
      img.onerror = function () { callback(null, "That image couldn't be read."); };
      img.onload = function () {
        var side = Math.min(img.width, img.height);
        var canvas = document.createElement('canvas');
        canvas.width = size;
        canvas.height = size;
        canvas.getContext('2d').drawImage(
          img,
          (img.width - side) / 2, (img.height - side) / 2, side, side,
          0, 0, size, size
        );
        callback(canvas.toDataURL('image/jpeg', 0.82), null);
      };
      img.src = reader.result;
    };
    reader.readAsDataURL(file);
  }

  /* Reads any small document as a data URL so it can be stored and reopened.
     Large files are refused — localStorage has only a few MB to work with. */
  function readFile(file, maxBytes, callback) {
    if (!file) { callback(null, 'Choose a file first.'); return; }
    if (file.size > maxBytes) {
      callback(null, 'That file is over ' + Math.round(maxBytes / (1024 * 1024)) +
        'MB. Choose a smaller one, or export it as a PDF first.');
      return;
    }

    var reader = new FileReader();
    reader.onerror = function () { callback(null, "That file couldn't be read."); };
    reader.onload = function () {
      callback({
        name: file.name,
        type: file.type || 'application/octet-stream',
        size: file.size,
        data: reader.result,
        addedAt: new Date().toISOString()
      }, null);
    };
    reader.readAsDataURL(file);
  }

  /* ---------- Validation shared by both pages ---------- */

  var EMAIL_RE = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9-]+(\.[A-Za-z0-9-]+)*\.[A-Za-z]{2,}$/;

  function checkEmail(value) {
    var email = String(value).trim();
    if (email === '') return { ok: false, message: 'Enter an email address.' };
    if (email.indexOf('@') === -1) return { ok: false, message: 'Include an @ — for example name@example.com.' };
    if (email.indexOf('..') !== -1) return { ok: false, message: 'Remove the repeated dot in the address.' };
    if (!EMAIL_RE.test(email)) return { ok: false, message: 'Enter a valid email, such as name@example.com.' };
    return { ok: true, message: '' };
  }

  var RULES = {
    length: function (p) { return p.length >= 8; },
    upper:  function (p) { return /[A-Z]/.test(p); },
    lower:  function (p) { return /[a-z]/.test(p); },
    number: function (p) { return /[0-9]/.test(p); },
    symbol: function (p) { return /[^A-Za-z0-9]/.test(p); }
  };

  /* Index 0 is never shown — an empty field displays no label at all. */
  var LEVELS = ['', 'Weak', 'Fair', 'Good', 'Strong'];

  function checkPassword(password) {
    var pw = String(password);
    var met = {};
    var passed = 0;

    Object.keys(RULES).forEach(function (key) {
      met[key] = RULES[key](pw);
      if (met[key]) passed++;
    });

    var level = 0;
    if (pw.length > 0) {
      if (passed <= 2) level = 1;
      else if (passed <= 4) level = 2;
      else level = pw.length >= 12 ? 4 : 3;

      if (pw.length >= 16 && passed >= 3) level = Math.max(level, 3);
    }

    return { met: met, passed: passed, valid: passed === 5, level: level, label: LEVELS[level] };
  }

  return {
    available: available,
    assetUrl: assetUrl,
    getProfiles: getProfiles,
    saveProfiles: saveProfiles,
    removeProfile: removeProfile,
    getProfile: getProfile,
    updateProfile: updateProfile,
    emailTakenByOther: emailTakenByOther,
    CATEGORIES: CATEGORIES,
    WEIGHT_TOTAL: WEIGHT_TOTAL,
    getBudget: getBudget,
    getCart: getCart,
    saveCart: saveCart,
    saveBudget: saveBudget,
    suggestBudget: suggestBudget,
    setSession: setSession,
    getSession: getSession,
    clearSession: clearSession,
    readFile: readFile,
    getProfile: getProfile,
    updateProfile: updateProfile,
    emailTakenByOther: emailTakenByOther,
    checkPasswordFor: checkPasswordFor,
    CATEGORIES: CATEGORIES,
    getBudget: getBudget,
    getCart: getCart,
    saveCart: saveCart,
    saveBudget: saveBudget,
    suggestBudget: suggestBudget,
    getExpenses: getExpenses,
    addExpenses: addExpenses,
    mainProfile: mainProfile,
    isMain: isMain,
    householdId: householdId,
    householdMembers: householdMembers,
    allExpenses: allExpenses,
    familyExpenses: familyExpenses,
    memberSpend: memberSpend,
    memberIncomes: memberIncomes,
    familyIncome: familyIncome,
    setMemberIncome: setMemberIncome,
    monthlyFromSource: monthlyFromSource,
    sourcesTotal: sourcesTotal,
    baseIncome: baseIncome,
    personIncome: personIncome,
    canEditFinance: canEditFinance,
    saveFinance: saveFinance,
    setBaseIncome: setBaseIncome,
    categoriesUsedBy: categoriesUsedBy,
    setSession: setSession,
    getSession: getSession,
    endSession: endSession,
    emailTaken: emailTaken,
    verify: verify,
    clearAll: clearAll,
    initials: initials,
    avatarColor: avatarColor,
    readImage: readImage,
    checkEmail: checkEmail,
    checkPassword: checkPassword
  };
})();
