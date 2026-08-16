/* Budget Pilot — create account page */

(function () {
  'use strict';

  var BP = window.BudgetPilot;

  var el = function (id) { return document.getElementById(id); };

  var ownerPhoto      = el('ownerPhoto');
  var ownerPreview    = el('ownerPreview');
  var ownerPhotoClear = el('ownerPhotoClear');
  var ownerPhotoError = el('ownerPhotoError');

  var fullName = el('fullName');
  var email    = el('email');
  var password = el('password');
  var nic      = el('nic');
  var gender   = el('gender');
  var age      = el('age');
  var income   = el('income');
  var savings  = el('savings');
  var terms    = el('terms');

  var strengthBox   = el('strength');
  var strengthValue = el('strengthValue');
  var ruleItems     = document.querySelectorAll('#rules .rule');

  var addMemberBtn  = el('addMemberBtn');
  var memberList    = el('memberList');
  var memberTpl     = el('memberTemplate');

  var createBtn = el('createBtn');
  var formError = el('formError');

  var ownerSection   = el('ownerSection');
  var financeSection = el('financeSection');
  var modeNotice     = el('modeNotice');

  /* Arriving from the sign-in page's "+" means an account already exists, so
     only the family member part is needed. Without saved profiles there is no
     account to add to, and the full form is shown instead. */
  var addOnly = window.location.hash === '#members' && BP.getProfiles().length > 0;

  var ownerAvatar = null;   // data URL for the account owner

  /* ==================================================================
     Field state helpers
     ================================================================== */

  function setError(input, errorId, message) {
    var errorEl = el(errorId);
    if (errorEl) errorEl.textContent = message || '';
    var wrap = input ? input.closest('.input') : null;
    if (wrap) wrap.classList.toggle('is-invalid', !!message);
  }

  function markValid(input) {
    var wrap = input ? input.closest('.input') : null;
    if (wrap) { wrap.classList.remove('is-invalid'); wrap.classList.add('is-valid'); }
  }

  function clearForm() {
    if (formError) formError.textContent = '';
  }

  /* ==================================================================
     Profile photo (owner)
     ================================================================== */

  function paintPreview(node, dataUrl, fallbackText, fallbackColor) {
    if (dataUrl) {
      node.style.backgroundImage = 'url(' + dataUrl + ')';
      node.style.backgroundColor = 'transparent';
      node.textContent = '';
      node.setAttribute('data-empty', 'false');
    } else {
      node.style.backgroundImage = '';
      node.style.backgroundColor = fallbackColor || '';
      node.textContent = fallbackText || '';
      node.setAttribute('data-empty', 'true');
    }
  }

  if (ownerPhoto) {
    ownerPhoto.addEventListener('change', function () {
      var file = ownerPhoto.files && ownerPhoto.files[0];
      if (!file) return;

      BP.readImage(file, 160, function (dataUrl, error) {
        if (error) { ownerPhotoError.textContent = error; return; }
        ownerPhotoError.textContent = '';
        ownerAvatar = dataUrl;
        paintPreview(ownerPreview, dataUrl);
        ownerPhotoClear.hidden = false;
      });
    });
  }

  if (ownerPhotoClear) {
    ownerPhotoClear.addEventListener('click', function () {
      ownerAvatar = null;
      ownerPhoto.value = '';
      ownerPhotoClear.hidden = true;
      paintPreview(ownerPreview, null, BP.initials(fullName.value), '');
    });
  }

  /* Keep the placeholder initials in sync with the typed name. */
  if (fullName) {
    fullName.addEventListener('input', function () {
      if (!ownerAvatar) {
        paintPreview(ownerPreview, null, BP.initials(fullName.value), '');
      }
      if (fullName.value.trim()) setError(fullName, 'fullNameError', '');
    });
  }

  /* ==================================================================
     Password strength
     ================================================================== */

  function renderStrength(value) {
    var result = BP.checkPassword(value);
    if (!strengthBox) return result;

    strengthBox.hidden = value.length === 0;
    strengthBox.setAttribute('data-level', String(result.level));
    strengthValue.textContent = value.length ? result.label : '';

    ruleItems.forEach(function (item) {
      item.classList.toggle('is-met', !!result.met[item.getAttribute('data-rule')]);
    });
    return result;
  }

  if (password) {
    password.addEventListener('input', function () {
      clearForm();
      var result = renderStrength(password.value);
      if (result.valid) { setError(password, 'passwordError', ''); markValid(password); }
      else if (password.value === '') { setError(password, 'passwordError', ''); }
    });

    password.addEventListener('blur', function () {
      if (password.value === '') return;
      var result = BP.checkPassword(password.value);
      if (!result.valid) setError(password, 'passwordError', 'Your password is missing one of the requirements above.');
    });
  }

  /* ==================================================================
     Email / NIC / age
     ================================================================== */

  if (email) {
    email.addEventListener('blur', function () {
      if (email.value.trim() === '') return;
      var result = BP.checkEmail(email.value);
      if (!result.ok) { setError(email, 'emailError', result.message); return; }
      if (BP.emailTaken(email.value)) {
        setError(email, 'emailError', 'That email already has a profile. Sign in instead.');
        return;
      }
      setError(email, 'emailError', '');
      markValid(email);
    });
    email.addEventListener('input', function () { clearForm(); setError(email, 'emailError', ''); });
  }

  /* NIC: 9 digits followed by V or X, or 12 digits.
     Change this pattern if your NIC uses a different format. */
  var NIC_RE = /^(\d{9}[VvXx]|\d{12})$/;

  function checkNic(value) {
    var v = String(value).trim().replace(/\s+/g, '');
    if (v === '') return { ok: false, message: 'Enter your NIC number.' };
    if (!NIC_RE.test(v)) return { ok: false, message: 'Use 12 digits, or 9 digits followed by V.' };
    return { ok: true, message: '' };
  }

  if (nic) {
    nic.addEventListener('blur', function () {
      if (nic.value.trim() === '') return;
      var result = checkNic(nic.value);
      setError(nic, 'nicError', result.message);
      if (result.ok) markValid(nic);
    });
    nic.addEventListener('input', function () { setError(nic, 'nicError', ''); });
  }

  if (age) age.addEventListener('input', function () { setError(age, 'ageError', ''); });
  if (gender) gender.addEventListener('change', function () { setError(gender, 'genderError', ''); });
  if (terms) terms.addEventListener('change', function () { el('termsError').textContent = ''; });

  /* ==================================================================
     Family members
     ================================================================== */

  var memberSeq = 0;

  function memberCards() {
    return Array.prototype.slice.call(memberList.querySelectorAll('.member'));
  }

  /* Every email on this form: the owner's, plus each member card's. */
  function emailsOnForm(exceptCard) {
    var list = [String(email.value).trim().toLowerCase()];
    memberCards().forEach(function (card) {
      if (card === exceptCard) return;
      list.push(String(card.querySelector('.member__email').value).trim().toLowerCase());
    });
    return list.filter(Boolean);
  }

  function checkMemberEmail(card) {
    var value = card.querySelector('.member__email').value;
    var result = BP.checkEmail(value);
    if (!result.ok) return result;

    var lower = String(value).trim().toLowerCase();
    if (emailsOnForm(card).indexOf(lower) !== -1) {
      return { ok: false, message: 'That email is already on this account.' };
    }
    if (BP.emailTaken(lower)) {
      return { ok: false, message: 'That email already has a profile.' };
    }
    return { ok: true, message: '' };
  }

  function addMember() {
    var card = memberTpl.content.firstElementChild.cloneNode(true);
    var seq = ++memberSeq;

    var preview   = card.querySelector('.member__preview');
    var fileIn    = card.querySelector('.member__file');
    var chooseLbl = card.querySelector('.member__choose');
    var clearBtn  = card.querySelector('.member__clear');
    var nameIn    = card.querySelector('.member__name');
    var emailIn   = card.querySelector('.member__email');
    var passIn    = card.querySelector('.member__password');
    var reveal    = card.querySelector('.member__reveal');
    var levelOut  = card.querySelector('.member__level');
    var errorOut  = card.querySelector('.member__error');

    /* Unique ids so every label points at its own control. */
    [['name', nameIn], ['email', emailIn], ['password', passIn]].forEach(function (pair) {
      var id = 'member-' + seq + '-' + pair[0];
      pair[1].id = id;
      card.querySelector('.member__' + pair[0] + '-label').setAttribute('for', id);
    });
    fileIn.id = 'member-' + seq + '-photo';
    chooseLbl.setAttribute('for', fileIn.id);

    function clearProblem() {
      errorOut.textContent = '';
      card.classList.remove('is-flagged');
      if (formError) formError.textContent = '';
    }

    function refreshInitials() {
      if (card.avatarData) return;
      paintPreview(preview, null, BP.initials(nameIn.value), '');
    }

    nameIn.addEventListener('input', function () { refreshInitials(); clearProblem(); });

    emailIn.addEventListener('input', clearProblem);
    emailIn.addEventListener('blur', function () {
      if (emailIn.value.trim() === '') return;
      var check = checkMemberEmail(card);
      errorOut.textContent = check.message;
      card.classList.toggle('is-flagged', !check.ok);
    });

    fileIn.addEventListener('change', function () {
      var file = fileIn.files && fileIn.files[0];
      if (!file) return;
      BP.readImage(file, 160, function (dataUrl, error) {
        if (error) { errorOut.textContent = error; return; }
        card.avatarData = dataUrl;
        paintPreview(preview, dataUrl);
        clearBtn.hidden = false;
        clearProblem();
      });
    });

    clearBtn.addEventListener('click', function () {
      card.avatarData = null;
      fileIn.value = '';
      clearBtn.hidden = true;
      refreshInitials();
    });

    passIn.addEventListener('input', function () {
      clearProblem();
      if (passIn.value === '') {
        levelOut.textContent = '';
        levelOut.removeAttribute('data-level');
        return;
      }
      var check = BP.checkPassword(passIn.value);
      levelOut.textContent = 'Security level: ' + check.label +
        (check.valid ? '' : ' \u2014 needs 8+ characters with upper, lower, number and symbol.');
      levelOut.setAttribute('data-level', String(check.level));
    });

    reveal.addEventListener('click', function () {
      var hidden = passIn.type === 'password';
      passIn.type = hidden ? 'text' : 'password';
      reveal.setAttribute('aria-label', hidden ? 'Hide password' : 'Show password');
      passIn.focus();
    });

    card.querySelector('.member__remove').addEventListener('click', function () {
      card.remove();
      if (formError) formError.textContent = '';
    });

    memberList.appendChild(card);
    nameIn.focus();
  }

  if (addMemberBtn) addMemberBtn.addEventListener('click', addMember);

  /* ==================================================================
     Submit
     ================================================================== */

  function firstInvalid(list) {
    return list.filter(function (entry) { return !entry.ok; })[0];
  }

  /* Reads and validates every member card. */
  function collectMembers() {
    var members = [];
    var ok = true;

    memberCards().forEach(function (card) {
      var nameIn  = card.querySelector('.member__name');
      var emailIn = card.querySelector('.member__email');
      var passIn  = card.querySelector('.member__password');
      var errOut  = card.querySelector('.member__error');
      var problem = '';

      var emailCheck = checkMemberEmail(card);

      if (nameIn.value.trim().length < 2) problem = 'Enter this member\u2019s full name.';
      else if (!emailCheck.ok) problem = emailCheck.message;
      else if (!BP.checkPassword(passIn.value).valid) {
        problem = 'Set a password with 8+ characters, upper and lower case, a number and a symbol.';
      }

      errOut.textContent = problem;
      card.classList.toggle('is-flagged', !!problem);
      if (problem) { ok = false; return; }

      members.push({
        name: nameIn.value,
        email: emailIn.value.trim().toLowerCase(),
        password: passIn.value,
        avatar: card.avatarData || null,
        role: 'Member'
      });
    });

    return { ok: ok, members: members };
  }

  function persist(profiles, destination) {
    if (!BP.available()) {
      formError.textContent = "Your browser is blocking local storage, so nothing can be saved.";
      return;
    }
    if (!BP.saveProfiles(profiles)) {
      formError.textContent = "Couldn't save \u2014 your images may be too large. Try smaller photos.";
      return;
    }
    window.location.href = destination;
  }

  /* ---- Members-only submit ---- */
  function submitMembersOnly() {
    clearForm();

    if (memberCards().length === 0) {
      formError.textContent = 'Add at least one member first.';
      return;
    }

    var result = collectMembers();
    if (!result.ok) {
      formError.textContent = 'Check the highlighted fields and try again.';
      var firstBad = memberList.querySelector('.member.is-flagged input');
      if (firstBad) {
        firstBad.focus();
        firstBad.scrollIntoView({ block: 'center', behavior: 'smooth' });
      }
      return;
    }

    persist(result.members, 'login.php?added=' + result.members.length);
  }

  /* ---- Full sign-up submit ---- */
  function submitFullAccount() {
    clearForm();

    var checks = [];

    /* Name */
    var nameOk = fullName.value.trim().length >= 2;
    setError(fullName, 'fullNameError', nameOk ? '' : 'Enter your full name.');
    checks.push({ ok: nameOk, node: fullName });

    /* Email */
    var emailResult = BP.checkEmail(email.value);
    var emailOk = emailResult.ok && !BP.emailTaken(email.value);
    setError(email, 'emailError',
      emailResult.ok
        ? (emailOk ? '' : 'That email already has a profile. Sign in instead.')
        : emailResult.message);
    checks.push({ ok: emailOk, node: email });

    /* Password */
    var passResult = renderStrength(password.value);
    setError(password, 'passwordError',
      passResult.valid ? '' : 'Your password is missing one of the requirements above.');
    checks.push({ ok: passResult.valid, node: password });

    /* NIC */
    var nicResult = checkNic(nic.value);
    setError(nic, 'nicError', nicResult.message);
    checks.push({ ok: nicResult.ok, node: nic });

    /* Gender */
    var genderOk = gender.value !== '';
    setError(gender, 'genderError', genderOk ? '' : 'Select an option.');
    checks.push({ ok: genderOk, node: gender });

    /* Age */
    var ageNum = parseInt(age.value, 10);
    var ageOk = !isNaN(ageNum) && ageNum >= 13 && ageNum <= 120;
    setError(age, 'ageError', ageOk ? '' : 'Enter an age between 13 and 120.');
    checks.push({ ok: ageOk, node: age });

    /* Money — optional, but must make sense when filled in */
    var incomeNum  = income.value === '' ? null : Number(income.value);
    var savingsNum = savings.value === '' ? null : Number(savings.value);
    var incomeOk = incomeNum === null || incomeNum >= 0;
    setError(income, 'incomeError', incomeOk ? '' : 'Enter a positive amount.');
    checks.push({ ok: incomeOk, node: income });

    var savingsOk = savingsNum === null || (savingsNum >= 0 && (incomeNum === null || savingsNum <= incomeNum));
    setError(savings, 'savingsError',
      savingsOk ? '' : 'Your savings target is higher than your income.');
    checks.push({ ok: savingsOk, node: savings });

    /* Members */
    var memberResult = collectMembers();
    var members = memberResult.members;
    var membersOk = memberResult.ok;

    /* Terms */
    var termsOk = terms.checked;
    el('termsError').textContent = termsOk ? '' : 'Accept the terms to continue.';

    var failed = firstInvalid(checks);
    if (failed || !membersOk || !termsOk) {
      formError.textContent = 'Check the highlighted fields and try again.';
      var target = failed ? failed.node
        : (!membersOk ? memberList.querySelector('.member.is-flagged input') : terms);
      if (target && target.focus) target.focus();
      if (target && target.scrollIntoView) target.scrollIntoView({ block: 'center', behavior: 'smooth' });
      return;
    }

    var owner = {
      name: fullName.value,
      email: email.value,
      password: password.value,
      avatar: ownerAvatar,
      role: 'Main',
      gender: gender.value,
      age: age.value,
      nic: nic.value.trim(),
      income: income.value,
      savings: savings.value
    };

    persist([owner].concat(members), 'login.php?created=1');
  }

  createBtn.addEventListener('click', function () {
    if (addOnly) submitMembersOnly();
    else submitFullAccount();
  });

  /* ==================================================================
     Modes: full sign-up, or members-only
     ================================================================== */

  function applyAddOnlyMode() {
    var main = BP.getProfiles().filter(function (p) { return p.role === 'Main'; })[0];
    var household = main ? main.name : 'your account';

    if (ownerSection) ownerSection.hidden = true;
    if (financeSection) financeSection.hidden = true;

    el('pageTitle').textContent = 'Add family members';
    el('pageSub').textContent = 'Add people to ' + household + '\u2019s household. They sign in with their own email and password.';

    /* The card headings would just repeat the page title here. */
    el('memberHeading').hidden = true;
    el('memberSub').hidden = true;

    el('createBtnLabel').textContent = 'Add members';
    el('finePrint').textContent = 'New members appear on the sign-in screen once added.';

    if (modeNotice) {
      modeNotice.hidden = false;
      modeNotice.innerHTML = 'Adding to an existing account. ' +
        '<a class="link" href="register.php">Create a new account instead</a>';
    }

    addMember();   // start with one blank card ready to fill
  }

  if (addOnly) {
    applyAddOnlyMode();
  } else if (window.location.hash === '#members') {
    /* No account saved yet, so the full form stays — just scroll to the section. */
    window.setTimeout(function () {
      var section = el('members');
      if (!section) return;
      section.scrollIntoView({ behavior: 'smooth', block: 'start' });
      if (addMemberBtn) addMemberBtn.focus({ preventScroll: true });
    }, 120);
  }

  /* Show / hide the owner password */
  document.querySelectorAll('[data-toggle]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = el(btn.getAttribute('data-toggle'));
      if (!input) return;
      var hidden = input.type === 'password';
      input.type = hidden ? 'text' : 'password';
      btn.setAttribute('aria-label', hidden ? 'Hide password' : 'Show password');
      input.focus();
    });
  });
})();
