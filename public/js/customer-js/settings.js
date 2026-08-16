/* Budget Pilot — profile & settings */

(function () {
  'use strict';

  var BP = window.BudgetPilot;
  var el = function (id) { return document.getElementById(id); };

  /* ---- Gate: a signed-in profile is required ---- */
  var profileId = BP.getSession();
  var profile = profileId ? BP.getProfile(profileId) : null;

  if (!profile) {
    window.location.replace('customer-login.html?signin=required');
    return;
  }

  var CURRENCIES = {
    USD: '$', EUR: '\u20AC', GBP: '\u00A3', LKR: 'Rs', INR: '\u20B9', AUD: '$'
  };

  /* ---- Who may record financial data ----

     Income sources and pay sheets decide what the household plans against, so
     they belong to the account holder — for every person on the account,
     themselves included. A member sees their own figures but cannot change
     them. A lone profile is its own holder, so nothing is locked. */

  var iAmMain = BP.isMain(profile.id);
  var household = BP.householdMembers();
  var canEditFinance = BP.canEditFinance(profile.id);
  var picksPerson = iAmMain && household.length > 1;

  /* Whose financial data is on screen. Always yourself unless the holder
     switches to someone else with the picker. */
  var financeTarget = profile.id;

  function financeProfile() {
    return BP.getProfile(financeTarget) || profile;
  }

  function firstName(name) {
    return String(name || '').trim().split(/\s+/)[0] || 'the account holder';
  }

  /* Working copy — edits live here until saved. */
  var draft = null;

  function snapshot() {
    var owner = financeProfile();
    return {
      name: profile.name,
      email: profile.email,
      income: profile.income || '',
      savings: profile.savings || '',
      nic: profile.nic || '',
      gender: profile.gender || '',
      age: profile.age || '',
      avatar: profile.avatar || null,
      prefs: Object.assign({ currency: 'USD', fiscalYear: 'January', darkMode: false, smartAlerts: true }, profile.prefs || {}),
      financeFor: financeTarget,
      financeBase: String(BP.baseIncome(owner) || ''),
      incomeSources: (owner.incomeSources || []).slice(),
      documents: (owner.documents || []).slice()
    };
  }

  /* Your own records, whichever person's card is currently on screen. They
     are what your Monthly income box is worked out from. */
  function ownSources() {
    if (financeTarget === profile.id) return draft.incomeSources;
    var me = BP.getProfile(profile.id);
    return (me && me.incomeSources) || [];
  }

  function ownDocuments() {
    if (financeTarget === profile.id) return draft.documents;
    var me = BP.getProfile(profile.id);
    return (me && me.documents) || [];
  }

  /* Your own starting figure, whichever person's card is on screen. */
  function ownBase() {
    if (financeTarget === profile.id) return Number(draft.financeBase) || 0;
    return BP.baseIncome(BP.getProfile(profile.id));
  }

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

  function symbol() { return CURRENCIES[draft.prefs.currency] || '$'; }

  function money(value) {
    var n = Number(value);
    if (!isFinite(n)) return symbol() + '0.00';
    return symbol() + n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function fileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return Math.round(bytes / 1024) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
  }

  function shortDate(iso) {
    var d = new Date(iso);
    return isNaN(d) ? '' : d.toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric' });
  }

  function paintAvatar(node, dataUrl, initials, big) {
    if (dataUrl) {
      node.style.backgroundImage = 'url(' + dataUrl + ')';
      node.style.backgroundColor = 'transparent';
      node.textContent = '';
    } else {
      node.style.backgroundImage = '';
      node.style.backgroundColor = BP.avatarColor(draft.email || draft.name);
      node.textContent = initials;
    }
    if (big) node.style.fontSize = '';
  }

  /* ==================================================================
     Modal
     ================================================================== */

  var modal = el('modal');
  var modalTitle = el('modalTitle');
  var modalBody = el('modalBody');
  var modalActions = el('modalActions');

  function closeModal() {
    modal.hidden = true;
    modalBody.textContent = '';
    modalActions.textContent = '';
  }

  function openModal(title, buildBody, buttons) {
    modalTitle.textContent = title;
    modalBody.textContent = '';
    modalActions.textContent = '';
    buildBody(modalBody);

    buttons.forEach(function (spec) {
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'btn ' + (spec.style || 'btn--ghost');
      btn.textContent = spec.label;
      btn.addEventListener('click', function () {
        if (spec.action) spec.action();
        else closeModal();
      });
      modalActions.appendChild(btn);
    });

    modal.hidden = false;
    var first = modalActions.querySelector('button');
    if (first) first.focus();
  }

  el('modalBackdrop').addEventListener('click', closeModal);
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && !modal.hidden) closeModal();
  });

  /* ==================================================================
     Rendering
     ================================================================== */

  function renderIdentity() {
    el('displayName').textContent = draft.name;
    el('displayRole').textContent = profile.role === 'Main'
      ? 'Main account holder'
      : 'Family member';

    paintAvatar(el('profileAvatar'), draft.avatar, BP.initials(draft.name), true);
    paintAvatar(el('appbarAvatar'), draft.avatar, BP.initials(draft.name));
  }

  /* Monthly income is what was entered at sign-up plus everything recorded
     under Financial Data. It is worked out rather than typed, so the box shows
     the result and the parts are edited below it. */
  function syncIncomeFromSources() {
    var box = el('fIncome');
    var total = Math.round((ownBase() + BP.sourcesTotal(ownSources())) * 100) / 100;

    draft.income = total ? String(total) : '';

    box.readOnly = true;
    box.setAttribute('aria-readonly', 'true');
    el('incomeDerivedHint').hidden = false;
    box.value = draft.income;
  }

  /* The three figures spelled out, so it is clear the sources were added to
     the sign-up income rather than put in its place. */
  function renderBreakdown() {
    var base = Number(draft.financeBase) || 0;
    var extra = BP.sourcesTotal(draft.incomeSources);
    var mine = financeTarget === profile.id;

    el('finBase').value = draft.financeBase;
    el('finBaseSymbol').textContent = symbol();

    el('bdBase').textContent = money(base);
    el('bdSources').textContent = money(extra);
    el('bdTotal').textContent = money(base + extra);
    el('bdWho').textContent = mine
      ? 'Monthly income'
      : firstName(financeProfile().name) + "'s monthly income";
  }

  function renderFields() {
    el('fName').value = draft.name;
    el('fEmail').value = draft.email;
    el('fIncome').value = draft.income;
    el('fSavings').value = draft.savings;
    el('fNic').value = draft.nic;
    el('fGender').value = draft.gender;
    el('fAge').value = draft.age;
    el('fCurrency').value = draft.prefs.currency;
    el('fFiscal').value = draft.prefs.fiscalYear;

    el('incomeSymbol').textContent = symbol();
    el('savingsSymbol').textContent = symbol();
    el('srcSymbol').textContent = symbol();
  }

  /* One place decides what a source is worth per month — the store — so the
     figure shown here and the figure the budget plans on cannot drift apart. */
  function monthlyEquivalent(source) {
    return BP.monthlyFromSource(source);
  }

  function renderSources() {
    var list = el('sourceList');
    list.textContent = '';

    draft.incomeSources.forEach(function (source, index) {
      var li = document.createElement('li');
      li.className = 'source';

      var name = document.createElement('span');
      name.className = 'source__name';
      name.textContent = source.name;

      var freq = document.createElement('span');
      freq.className = 'source__freq';
      freq.textContent = source.frequency === 'once' ? 'one-off' : source.frequency;

      var amount = document.createElement('span');
      amount.className = 'source__amount';
      amount.textContent = money(source.amount);

      li.appendChild(name);
      li.appendChild(freq);
      li.appendChild(amount);

      /* Only the person allowed to record these can take one away. */
      if (canEditFinance) {
        var remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'source__remove';
        remove.setAttribute('aria-label', 'Remove ' + source.name);
        remove.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>';
        remove.addEventListener('click', function () {
          draft.incomeSources.splice(index, 1);
          renderSources();
          renderCheckup();
          markDirty();
        });
        li.appendChild(remove);
      }

      list.appendChild(li);
    });

    el('sourceEmpty').hidden = draft.incomeSources.length > 0;

    renderBreakdown();

    /* Your own records are what your monthly income is made of. */
    syncIncomeFromSources();
  }

  function renderDocs() {
    var list = el('docList');
    list.textContent = '';

    draft.documents.forEach(function (doc, index) {
      var li = document.createElement('li');
      li.className = 'doc';

      var icon = document.createElement('span');
      icon.className = 'doc__icon';
      icon.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/></svg>';

      var text = document.createElement('span');
      text.className = 'doc__text';
      var name = document.createElement('span');
      name.className = 'doc__name';
      name.textContent = doc.name;
      var meta = document.createElement('span');
      meta.className = 'doc__meta';
      meta.textContent = fileSize(doc.size) + ' \u00b7 added ' + shortDate(doc.addedAt);
      text.appendChild(name);
      text.appendChild(meta);

      var open = document.createElement('button');
      open.type = 'button';
      open.className = 'doc__open';
      open.textContent = 'View';
      open.addEventListener('click', function () { openDoc(doc); });

      li.appendChild(icon);
      li.appendChild(text);
      li.appendChild(open);

      if (canEditFinance) {
        var remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'doc__remove';
        remove.setAttribute('aria-label', 'Remove ' + doc.name);
        remove.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>';
        remove.addEventListener('click', function () {
          draft.documents.splice(index, 1);
          renderDocs();
          renderCheckup();
          markDirty();
        });
        li.appendChild(remove);
      }

      list.appendChild(li);
    });

    el('docEmpty').hidden = draft.documents.length > 0;
  }

  function openDoc(doc) {
    try {
      var parts = doc.data.split(',');
      var binary = atob(parts[1]);
      var bytes = new Uint8Array(binary.length);
      for (var i = 0; i < binary.length; i++) bytes[i] = binary.charCodeAt(i);
      var url = URL.createObjectURL(new Blob([bytes], { type: doc.type }));
      window.open(url, '_blank');
      window.setTimeout(function () { URL.revokeObjectURL(url); }, 30000);
    } catch (e) {
      say("That file couldn't be opened.");
    }
  }

  function renderHousehold() {
    var list = el('peopleList');
    if (!list) return;
    list.textContent = '';

    BP.getProfiles().forEach(function (person) {
      var isYou = person.id === profile.id;
      var name = isYou ? draft.name : person.name;
      var mail = isYou ? draft.email : person.email;
      var pic = isYou ? draft.avatar : person.avatar;

      var li = document.createElement('li');
      li.className = 'person';

      var avatar = document.createElement('span');
      avatar.className = 'person__avatar';
      if (pic) {
        avatar.style.backgroundImage = 'url(' + pic + ')';
      } else {
        avatar.style.backgroundColor = BP.avatarColor(mail || name);
        avatar.textContent = BP.initials(name);
      }

      var text = document.createElement('span');
      text.className = 'person__text';
      var who = document.createElement('span');
      who.className = 'person__name';
      who.textContent = name;
      var address = document.createElement('span');
      address.className = 'person__mail';
      address.textContent = mail;
      text.appendChild(who);
      text.appendChild(address);

      var tag = document.createElement('span');
      if (isYou) {
        tag.className = 'person__tag person__tag--you';
        tag.textContent = 'You';
      } else if (person.role === 'Main') {
        tag.className = 'person__tag person__tag--main';
        tag.textContent = 'Main';
      } else {
        tag.className = 'person__tag';
        tag.textContent = 'Member';
      }

      li.appendChild(avatar);
      li.appendChild(text);
      li.appendChild(tag);
      list.appendChild(li);
    });
  }

  /* ==================================================================
     Financial Data — who it belongs to and who may change it
     ================================================================== */

  function buildFinancePicker() {
    if (!picksPerson) return;

    var select = el('finPerson');
    select.textContent = '';

    household.forEach(function (person) {
      var option = document.createElement('option');
      option.value = person.id;
      option.textContent = person.id === profile.id
        ? person.name + ' (you)'
        : person.name + ' \u2014 ' + (person.role === 'Main' ? 'Main' : 'Member');
      select.appendChild(option);
    });

    select.value = financeTarget;
    el('finPersonField').hidden = false;
  }

  /* Loads a different person's records into the card. Unsaved work is never
     dropped without asking, because switching is easy to do by accident. */
  function switchFinanceTo(id) {
    financeTarget = id;
    draft.financeFor = id;
    var owner = financeProfile();
    draft.financeBase = String(BP.baseIncome(owner) || '');
    draft.incomeSources = (owner.incomeSources || []).slice();
    draft.documents = (owner.documents || []).slice();

    if (picksPerson) el('finPerson').value = id;
    renderSources();
    renderDocs();
    renderCheckup();
    markDirty();
  }

  function renderFinanceLock() {
    var note = el('financeLocked');

    if (canEditFinance) {
      note.hidden = true;
      return;
    }

    /* A member reads their figures but does not set them. The card stays on
       screen — they should be able to see what they earn — with every control
       in it switched off. */
    note.hidden = false;
    note.textContent = 'Your income and financial data are recorded by ' +
      firstName(BP.mainProfile() && BP.mainProfile().name) +
      ', the account holder. You can see them here but not change them.';

    document.getElementById('financeCard').classList.add('is-locked');

    ['finBase', 'srcName', 'srcAmount', 'srcFreq', 'addSourceBtn', 'docFile'].forEach(function (id) {
      var node = el(id);
      if (!node) return;
      node.disabled = true;
      node.setAttribute('aria-disabled', 'true');
    });
  }

  if (picksPerson) {
    el('finPerson').addEventListener('change', function () {
      var next = el('finPerson').value;
      if (next === financeTarget) return;

      if (!isDirty()) { switchFinanceTo(next); return; }

      el('finPerson').value = financeTarget;   /* hold until the choice is made */

      openModal('Unsaved changes', function (body) {
        var p = document.createElement('p');
        p.textContent = 'Switching to someone else reloads this card. Save what you have first, or leave it behind.';
        body.appendChild(p);
      }, [
        { label: 'Stay' },
        { label: 'Save and switch', style: 'btn--primary', action: function () {
            closeModal();
            el('saveBtn').click();
            if (!isDirty()) switchFinanceTo(next);
          } },
        { label: 'Discard and switch', style: 'btn--danger', action: function () {
            closeModal();
            draft = snapshot();
            switchFinanceTo(next);
          } }
      ]);
    });
  }

  /* ==================================================================
     Security checkup — every item is a real check
     ================================================================== */

  function checkupItems() {
    var others = BP.getProfiles().length > 1;
    return [
      { done: !!draft.avatar, label: 'Profile photo added',
        hint: 'Makes your profile easy to spot on the sign-in screen.' },
      { done: !!String(draft.nic).trim(), label: 'National ID on file',
        hint: 'Needed to verify who owns the account.' },
      { done: Number(draft.savings) > 0, label: 'Savings goal set',
        hint: 'Gives your budget something to aim at.' },
      { done: ownSources().length > 0, label: 'Income sources recorded',
        hint: canEditFinance
          ? 'Add where your money comes from under Financial Data.'
          : 'Recorded for you by ' + firstName(BP.mainProfile() && BP.mainProfile().name) + '.' },
      { done: ownDocuments().length > 0, label: 'Pay sheet uploaded',
        hint: 'A recent pay sheet backs up the recorded income.' },
      { done: others, label: 'Household set up',
        hint: 'Add family members so everyone has their own sign-in.' }
    ];
  }

  function renderCheckup() {
    var items = checkupItems();
    var done = items.filter(function (i) { return i.done; }).length;
    var percent = Math.round(done / items.length * 100);

    el('checkupFill').style.width = percent + '%';
    el('checkupText').textContent = percent === 100
      ? 'Your account is fully set up. Nothing left to complete.'
      : 'Your account is ' + percent + '% complete. ' + (items.length - done) +
        ' step' + (items.length - done === 1 ? '' : 's') + ' left for maximum protection.';
  }

  el('checkupBtn').addEventListener('click', function () {
    openModal('Security Checkup', function (body) {
      var intro = document.createElement('p');
      intro.textContent = 'Each item below is checked against what you have saved.';
      body.appendChild(intro);

      var list = document.createElement('ul');
      list.className = 'checklist';

      checkupItems().forEach(function (item) {
        var li = document.createElement('li');
        li.className = 'checkitem' + (item.done ? ' is-done' : '');

        var mark = document.createElement('span');
        mark.className = 'checkitem__mark';
        mark.innerHTML = item.done
          ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>'
          : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round"><circle cx="12" cy="12" r="7"/></svg>';

        var text = document.createElement('span');
        var label = document.createElement('span');
        label.className = 'checkitem__label';
        label.textContent = item.label;
        var hint = document.createElement('span');
        hint.className = 'checkitem__hint';
        hint.textContent = item.hint;
        text.appendChild(label);
        text.appendChild(hint);

        li.appendChild(mark);
        li.appendChild(text);
        list.appendChild(li);
      });

      body.appendChild(list);
    }, [{ label: 'Close', style: 'btn--primary' }]);
  });

  /* ==================================================================
     Edit mode + dirty tracking
     ================================================================== */

  function isDirty() {
    return JSON.stringify(draft) !== JSON.stringify(snapshot());
  }

  function markDirty() {
    var dirty = isDirty();
    el('saveBtn').disabled = !dirty;
    el('discardBtn').disabled = !dirty;
    el('dirtyState').textContent = dirty ? 'You have unsaved changes.' : '';
  }

  /* Fields are always editable; this just takes you to them. */
  el('editBtn').addEventListener('click', function () {
    var name = el('fName');
    name.scrollIntoView({ behavior: 'smooth', block: 'center' });
    name.focus({ preventScroll: true });
  });

  /* Field edits feed the draft */
  function bind(id, key, onchange) {
    el(id).addEventListener('input', function () {
      draft[key] = el(id).value;
      el(id + 'Err') && (el(id + 'Err').textContent = '');
      if (onchange) onchange();
      markDirty();
    });
  }

  /* The starting figure. Editing it moves the monthly income straight away,
     since income is that plus the recorded sources. */
  el('finBase').addEventListener('input', function () {
    if (!canEditFinance) return;
    draft.financeBase = el('finBase').value;
    el('finBaseErr').textContent = '';
    renderBreakdown();
    syncIncomeFromSources();
    markDirty();
  });

  bind('fName', 'name', function () { renderIdentity(); renderHousehold(); });
  bind('fEmail', 'email', renderHousehold);
  bind('fIncome', 'income');
  bind('fSavings', 'savings', renderCheckup);
  bind('fNic', 'nic', renderCheckup);
  bind('fAge', 'age');

  el('fGender').addEventListener('change', function () {
    draft.gender = el('fGender').value;
    markDirty();
  });
  el('fCurrency').addEventListener('change', function () {
    draft.prefs.currency = el('fCurrency').value;
    el('incomeSymbol').textContent = symbol();
    el('savingsSymbol').textContent = symbol();
    el('srcSymbol').textContent = symbol();
    renderSources();
    markDirty();
  });
  el('fFiscal').addEventListener('change', function () {
    draft.prefs.fiscalYear = el('fFiscal').value;
    markDirty();
  });

  /* Avatar */
  el('avatarFile').addEventListener('change', function () {
    var file = this.files && this.files[0];
    if (!file) return;
    BP.readImage(file, 220, function (dataUrl, error) {
      if (error) { say(error); return; }
      draft.avatar = dataUrl;
      renderIdentity();
      renderHousehold();
      renderCheckup();
      markDirty();
    });
  });

  /* Toggles */
  function wireToggle(id, key, after) {
    var btn = el(id);
    btn.addEventListener('click', function () {
      var next = btn.getAttribute('aria-checked') !== 'true';
      btn.setAttribute('aria-checked', String(next));
      draft.prefs[key] = next;
      if (after) after(next);
      markDirty();
    });
  }

  wireToggle('darkToggle', 'darkMode', function (on) {
    document.body.classList.toggle('is-dark', on);
  });
  wireToggle('alertToggle', 'smartAlerts');

  /* ==================================================================
     Income sources & documents
     ================================================================== */

  el('addSourceBtn').addEventListener('click', function () {
    if (!canEditFinance) {
      say('Only the account holder can record financial data.');
      return;
    }

    var name = el('srcName').value.trim();
    var amount = Number(el('srcAmount').value);

    if (name.length < 2) {
      el('srcErr').textContent = 'Name the source, such as "Salary — Acme Ltd".';
      el('srcName').focus();
      return;
    }
    if (!isFinite(amount) || amount <= 0) {
      el('srcErr').textContent = 'Enter an amount greater than zero.';
      el('srcAmount').focus();
      return;
    }

    el('srcErr').textContent = '';
    draft.incomeSources.push({ name: name, amount: amount, frequency: el('srcFreq').value });
    el('srcName').value = '';
    el('srcAmount').value = '';
    renderSources();
    renderCheckup();
    markDirty();
    el('srcName').focus();
  });

  el('docFile').addEventListener('change', function () {
    var file = this.files && this.files[0];
    if (!file) return;
    if (!canEditFinance) { this.value = ''; return; }

    BP.readFile(file, 1024 * 1024, function (doc, error) {
      el('docErr').textContent = error || '';
      if (error) return;
      draft.documents.push(doc);
      renderDocs();
      renderCheckup();
      markDirty();
      say(doc.name + ' added. Save changes to keep it.');
    });
    this.value = '';
  });

  /* ==================================================================
     Save / discard
     ================================================================== */

  function validate() {
    var ok = true;

    function fail(id, message) {
      el(id + 'Err').textContent = message;
      el(id).closest('.control') ? el(id).closest('.control').classList.add('is-invalid') : el(id).classList.add('is-invalid');
      ok = false;
    }
    function pass(id) {
      el(id + 'Err').textContent = '';
      el(id).closest('.control') ? el(id).closest('.control').classList.remove('is-invalid') : el(id).classList.remove('is-invalid');
    }

    if (draft.name.trim().length < 2) fail('fName', 'Enter your full name.'); else pass('fName');

    var emailCheck = BP.checkEmail(draft.email);
    if (!emailCheck.ok) fail('fEmail', emailCheck.message);
    else if (BP.emailTakenByOther(draft.email, profile.id)) fail('fEmail', 'Another profile already uses that email.');
    else pass('fEmail');

    var income = draft.income === '' ? null : Number(draft.income);
    if (income !== null && (!isFinite(income) || income < 0)) fail('fIncome', 'Enter a positive amount.'); else pass('fIncome');

    var base = draft.financeBase === '' ? null : Number(draft.financeBase);
    if (base !== null && (!isFinite(base) || base < 0)) fail('finBase', 'Enter a positive amount.');
    else pass('finBase');

    var savings = draft.savings === '' ? null : Number(draft.savings);
    if (savings !== null && (!isFinite(savings) || savings < 0)) fail('fSavings', 'Enter a positive amount.');
    else if (savings !== null && income !== null && savings > income) fail('fSavings', 'Your savings goal is higher than your income.');
    else pass('fSavings');

    var nic = String(draft.nic).trim();
    if (nic !== '' && !/^(\d{9}[VvXx]|\d{12})$/.test(nic)) fail('fNic', 'Use 12 digits, or 9 digits followed by V.'); else pass('fNic');

    var age = draft.age === '' ? null : parseInt(draft.age, 10);
    if (age !== null && (isNaN(age) || age < 13 || age > 120)) fail('fAge', 'Enter an age between 13 and 120.'); else pass('fAge');

    return ok;
  }

  el('saveBtn').addEventListener('click', function () {
    if (!validate()) {
      say('Check the highlighted fields and try again.');
      return;
    }

    var mine = financeTarget === profile.id;

    var patch = {
      name: draft.name.trim(),
      email: draft.email.trim().toLowerCase(),
      income: draft.income,
      savings: draft.savings,
      nic: String(draft.nic).trim(),
      gender: draft.gender,
      age: draft.age,
      avatar: draft.avatar,
      prefs: draft.prefs
    };

    /* Monthly income is worked out, so it is never written from the box. It is
       the starting figure plus the recorded sources, and BP.saveFinance is what
       sets it — for you or for whoever the picker is on. That figure is what
       the Budgets page then plans against. */
    delete patch.income;

    var result = BP.updateProfile(profile.id, patch);

    if (result.ok && canEditFinance) {
      result = BP.saveFinance(
        profile.id, financeTarget, draft.incomeSources, draft.documents, draft.financeBase
      );
    }

    if (!result.ok) {
      say("Couldn't save — storage is full. Remove a document and try again.");
      return;
    }

    profile = BP.getProfile(profile.id);
    household = BP.householdMembers();
    draft = snapshot();
    renderAll();
    markDirty();

    var total = BP.personIncome(financeProfile());
    if (canEditFinance && total > 0) {
      say(mine
        ? 'Changes saved. Monthly income is now ' + money(total) + '.'
        : 'Changes saved. ' + firstName(financeProfile().name) + "'s monthly income is now " +
          money(total) + '.');
    } else {
      say('Changes saved.');
    }
  });

  el('discardBtn').addEventListener('click', function () {
    draft = snapshot();
    document.body.classList.toggle('is-dark', !!draft.prefs.darkMode);
    renderAll();
    markDirty();
    say('Changes discarded.');
  });

  /* ==================================================================
     Data management
     ================================================================== */

  el('exportBtn').addEventListener('click', function () {
    var payload = {
      exportedAt: new Date().toISOString(),
      profile: {
        name: profile.name, email: profile.email, role: profile.role,
        nic: profile.nic, gender: profile.gender, age: profile.age,
        monthlyIncome: profile.income, savingsGoal: profile.savings,
        preferences: profile.prefs
      },
      incomeSources: profile.incomeSources || [],
      documents: (profile.documents || []).map(function (d) {
        return { name: d.name, type: d.type, size: d.size, addedAt: d.addedAt };
      })
    };

    var blob = new Blob([JSON.stringify(payload, null, 2)], { type: 'application/json' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'budget-pilot-' + profile.name.toLowerCase().replace(/\s+/g, '-') + '.json';
    document.body.appendChild(a);
    a.click();
    a.remove();
    window.setTimeout(function () { URL.revokeObjectURL(url); }, 5000);
    say('Export downloaded. Documents are listed by name, not included as files.');
  });

  el('cacheBtn').addEventListener('click', function () {
    var owner = BP.getProfile(profile.id) || profile;
    var docCount = (owner.documents || []).length;
    var bytes = (owner.documents || []).reduce(function (sum, d) { return sum + (d.size || 0); }, 0);

    openModal('Clear Cache', function (body) {
      var p1 = document.createElement('p');
      p1.textContent = docCount === 0
        ? 'There are no stored files taking up space right now.'
        : 'This removes ' + docCount + ' stored file' + (docCount === 1 ? '' : 's') +
          ' (' + fileSize(bytes) + ') that this browser is holding.';
      var p2 = document.createElement('p');
      p2.textContent = 'Your account, profile details, and income sources are kept. Any file you have not downloaded elsewhere will be gone for good.';
      body.appendChild(p1);
      body.appendChild(p2);
    }, [
      { label: 'Cancel' },
      { label: 'Clear cache', style: 'btn--danger', action: function () {
          BP.updateProfile(profile.id, { documents: [] });
          profile = BP.getProfile(profile.id);
          if (financeTarget === profile.id) draft.documents = [];
          closeModal();
          renderDocs();
          renderCheckup();
          markDirty();
          say(docCount ? 'Cleared ' + fileSize(bytes) + ' of stored files.' : 'Nothing to clear.');
        } }
    ]);
  });

  el('deactivateBtn').addEventListener('click', function () {
    openModal('Deactivate Account', function (body) {
      var p1 = document.createElement('p');
      p1.textContent = 'This deletes ' + profile.name + '\u2019s profile, along with saved income sources and uploaded files.';
      var p2 = document.createElement('p');
      p2.textContent = profile.role === 'Main' && BP.getProfiles().length > 1
        ? 'You are the main holder, so the next profile in the household takes over. The profile disappears from the sign-in screen straight away.'
        : 'The profile disappears from the sign-in screen straight away. This cannot be undone.';
      body.appendChild(p1);
      body.appendChild(p2);
    }, [
      { label: 'Keep account' },
      { label: 'Deactivate', style: 'btn--danger', action: function () {
          BP.removeProfile(profile.id);
          BP.clearSession();
          window.location.href = 'customer-login.html?deactivated=1';
        } }
    ]);
  });

  /* ---- Leaving the page ---- */

  /* Anything that navigates away checks for unsaved work first. */
  function leaveTo(url, endSession) {
    function go() {
      if (endSession) BP.clearSession();
      window.location.href = url;
    }

    if (!isDirty()) { go(); return; }

    openModal('Unsaved changes', function (body) {
      var p = document.createElement('p');
      p.textContent = 'You have changes that have not been saved. Leaving now discards them.';
      body.appendChild(p);
    }, [
      { label: 'Stay' },
      { label: 'Save and leave', style: 'btn--primary', action: function () {
          closeModal();
          el('saveBtn').click();
          if (!isDirty()) go();
        } },
      { label: 'Leave anyway', style: 'btn--danger', action: go }
    ]);
  }

  el('homeLink').addEventListener('click', function (e) {
    e.preventDefault();
    leaveTo('index.html', false);
  });

  el('loginLink').addEventListener('click', function (e) {
    e.preventDefault();
    leaveTo('customer-login.html', false);
  });

  el('logoutBtn').addEventListener('click', function () {
    leaveTo('customer-login.html', true);
  });

  document.querySelectorAll('[data-soon]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      say(btn.getAttribute('data-soon') + ' is not built yet.');
    });
  });

  /* ==================================================================
     Start
     ================================================================== */

  function renderAll() {
    renderIdentity();
    renderFields();
    renderSources();
    renderDocs();
    renderCheckup();
    renderHousehold();
    renderFinanceLock();
  }

  draft = snapshot();
  buildFinancePicker();
  document.body.classList.toggle('is-dark', !!draft.prefs.darkMode);
  el('darkToggle').setAttribute('aria-checked', String(!!draft.prefs.darkMode));
  el('alertToggle').setAttribute('aria-checked', String(draft.prefs.smartAlerts !== false));
  renderAll();
  markDirty();
})();
