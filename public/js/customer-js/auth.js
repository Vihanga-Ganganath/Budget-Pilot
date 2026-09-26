/* Budget Pilot — customer sign-in page.
   The customer page renders saved profiles and checks the typed email and
   password against the selected one. */

(function () {
  'use strict';

  var BP = window.BudgetPilot;

  var emailInput    = document.getElementById('email');
  var passwordInput = document.getElementById('password');
  var emailError    = document.getElementById('emailError');
  var passwordError = document.getElementById('passwordError');
  var formError     = document.getElementById('formError');
  var signInBtn     = document.getElementById('signInBtn');

  var profileRow      = document.getElementById('profileRow');
  var profilesEmpty   = document.getElementById('profilesEmpty');
  var profileSelected = document.getElementById('profileSelected');
  var createSwitch    = document.getElementById('createSwitch');
  var manageBtn       = document.getElementById('manageBtn');
  var confirmBar      = document.getElementById('profileConfirm');
  var confirmText     = document.getElementById('confirmText');
  var confirmRemove   = document.getElementById('confirmRemove');
  var confirmCancel   = document.getElementById('confirmCancel');
  var notice          = document.getElementById('notice');

  var selectedId  = null;

  /* Every household in MySQL, printed into the page by customer-login.php:
     [{ id, name, members: [{ id, name, email, role, avatar }] }]
     When it is there, the picker lists all of them instead of only the
     profiles saved in this browser. */
  var DIRECTORY = Array.isArray(window.BP_HOUSEHOLDS) ? window.BP_HOUSEHOLDS : null;

  function directoryPerson(id) {
    if (!DIRECTORY) return null;
    for (var i = 0; i < DIRECTORY.length; i++) {
      var people = DIRECTORY[i].members || [];
      for (var j = 0; j < people.length; j++) {
        if ('db' + people[j].id === id) return people[j];
      }
    }
    return null;
  }
  var manageMode  = false;
  var pendingId   = null;

  /* ==================================================================
     Field helpers
     ================================================================== */

  function setState(input, errorEl, ok, message) {
    var wrap = input ? input.closest('.input') : null;
    if (wrap) {
      wrap.classList.toggle('is-invalid', !ok && !!message);
      wrap.classList.toggle('is-valid', ok);
    }
    if (errorEl) errorEl.textContent = ok ? '' : (message || '');
  }

  function clearState(input, errorEl) {
    var wrap = input ? input.closest('.input') : null;
    if (wrap) wrap.classList.remove('is-invalid', 'is-valid');
    if (errorEl) errorEl.textContent = '';
  }

  /* ==================================================================
     Profile row (customer page only)
     ================================================================== */

  function buildAvatar(profile) {
    var pic = document.createElement('span');
    pic.className = 'profile__pic';

    if (profile.avatar) {
      pic.style.backgroundImage = 'url(' + profile.avatar + ')';
    } else {
      pic.classList.add('profile__pic--initials');
      pic.style.backgroundColor = BP.avatarColor(profile.email || profile.name);
      pic.textContent = BP.initials(profile.name);
    }
    return pic;
  }

  function selectProfile(id, name) {
    selectedId = id;

    profileRow.querySelectorAll('.profile[role="radio"]').forEach(function (btn) {
      var isThis = btn.getAttribute('data-id') === id;
      btn.classList.toggle('is-selected', isThis);
      btn.setAttribute('aria-checked', isThis ? 'true' : 'false');
    });

    if (profileSelected) profileSelected.textContent = name ? 'Signing in as ' + name : '';
    if (formError) formError.textContent = '';
  }

  function hideConfirm() {
    pendingId = null;
    if (confirmBar) confirmBar.hidden = true;
  }

  function askRemove(profile) {
    pendingId = profile.id;
    confirmText.textContent = 'Remove ' + profile.name + ' from this device? Their account is NOT deleted \u2014 to delete it, sign in and use Settings \u2192 Deactivate Account.';
    confirmBar.hidden = false;
    confirmRemove.focus();
  }

  function removeIcon() {
    var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('viewBox', '0 0 24 24');
    svg.setAttribute('fill', 'none');
    svg.setAttribute('stroke', 'currentColor');
    svg.setAttribute('stroke-width', '3.4');
    svg.setAttribute('stroke-linecap', 'round');
    var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    path.setAttribute('d', 'M6 6l12 12M18 6L6 18');
    svg.appendChild(path);
    return svg;
  }

  /* One avatar button for a person from the database list. */
  function directoryButton(person) {
    var id = 'db' + person.id;
    var btn = document.createElement('button');
    btn.className = 'profile';
    btn.type = 'button';
    btn.setAttribute('role', 'radio');
    btn.setAttribute('aria-checked', 'false');
    btn.setAttribute('data-id', id);
    btn.setAttribute('title', person.name);
    btn.setAttribute('aria-label', person.name);
    btn.appendChild(buildAvatar(person));

    if (person.role === 'Main') {
      var badge = document.createElement('span');
      badge.className = 'profile__badge';
      badge.textContent = 'Main';
      btn.appendChild(badge);
    }

    btn.addEventListener('click', function () {
      selectProfile(id, person.name);
      if (emailInput) {
        emailInput.value = person.email;
        clearState(emailInput, emailError);
      }
      if (passwordInput) {
        passwordInput.value = '';
        clearState(passwordInput, passwordError);
        passwordInput.focus();
      }
    });
    return btn;
  }

  /* All households from the database, one labelled group each. */
  function renderDirectory() {
    profileRow.textContent = '';
    profileRow.classList.add('profiles__row--groups');

    DIRECTORY.forEach(function (hh) {
      var group = document.createElement('div');
      group.className = 'hh';

      var label = document.createElement('p');
      label.className = 'hh__label';
      label.textContent = 'Household ' + hh.id + ' - ' + hh.name;
      group.appendChild(label);

      var row = document.createElement('div');
      row.className = 'profiles__row';
      (hh.members || []).forEach(function (person) {
        row.appendChild(directoryButton(person));
      });
      group.appendChild(row);

      profileRow.appendChild(group);
    });

    /* "+" = create a new household */
    var add = document.createElement('a');
    add.className = 'profile profile--add';
    add.href = (window.URLROOT || '') + '/customer/register';
    add.setAttribute('aria-label', 'Create a new account');
    add.setAttribute('title', 'Create a new account');
    add.innerHTML = '<span aria-hidden="true">+</span>';
    profileRow.appendChild(add);

    var empty = DIRECTORY.length === 0;
    if (profilesEmpty) profilesEmpty.hidden = !empty;
    if (createSwitch) createSwitch.hidden = !empty;
    if (manageBtn) manageBtn.hidden = true;   // nothing to remove from a device list any more

    /* Nobody is picked until the user taps someone. */
    selectedId = null;
    if (profileSelected) profileSelected.textContent = '';
  }

  function renderProfiles() {
    if (!profileRow) return;
    if (DIRECTORY) { renderDirectory(); return; }

    var profiles = BP.getProfiles();
    profileRow.textContent = '';

    profiles.forEach(function (profile) {
      var btn = document.createElement('button');
      btn.className = 'profile';
      btn.type = 'button';
      btn.setAttribute('role', 'radio');
      btn.setAttribute('aria-checked', 'false');
      btn.setAttribute('data-id', profile.id);
      btn.setAttribute('title', profile.name);
      btn.setAttribute('aria-label', profile.name);

      btn.appendChild(buildAvatar(profile));

      if (profile.role === 'Main') {
        var badge = document.createElement('span');
        badge.className = 'profile__badge';
        badge.textContent = 'Main';
        btn.appendChild(badge);
      }

      btn.addEventListener('click', function () { selectProfile(profile.id, profile.name); });

      /* In manage mode each profile gets its own remove control. */
      if (manageMode) {
        var remove = document.createElement('button');
        remove.className = 'profile__remove';
        remove.type = 'button';
        remove.setAttribute('aria-label', 'Remove ' + profile.name);
        remove.appendChild(removeIcon());
        remove.addEventListener('click', function (e) {
          e.stopPropagation();
          askRemove(profile);
        });
        btn.appendChild(remove);
      }

      profileRow.appendChild(btn);
    });

    /* The "+" button goes straight to the add-members section. */
    var add = document.createElement('a');
    add.className = 'profile profile--add';
    add.href = 'register.php#members';
    add.setAttribute('aria-label', 'Add a profile');
    add.innerHTML = '<span aria-hidden="true">+</span>';
    profileRow.appendChild(add);

    if (profilesEmpty) profilesEmpty.hidden = profiles.length > 0;

    /* The create-account prompt is only useful with nothing set up yet. */
    if (createSwitch) createSwitch.hidden = profiles.length > 0;

    if (manageBtn) {
      manageBtn.hidden = profiles.length === 0;
      manageBtn.textContent = manageMode ? 'Done' : 'Manage';
    }

    /* Keep the current pick if it survived, otherwise fall back to the main. */
    var stillThere = profiles.filter(function (p) { return p.id === selectedId; })[0];
    var fallback = profiles.filter(function (p) { return p.role === 'Main'; })[0] || profiles[0];
    var target = stillThere || fallback;

    if (target) {
      selectProfile(target.id, target.name);
    } else {
      selectedId = null;
      if (profileSelected) profileSelected.textContent = '';
    }
  }

  renderProfiles();

  if (manageBtn) {
    manageBtn.addEventListener('click', function () {
      manageMode = !manageMode;
      hideConfirm();
      renderProfiles();
    });
  }

  if (confirmCancel) confirmCancel.addEventListener('click', hideConfirm);

  if (confirmRemove) {
    confirmRemove.addEventListener('click', function () {
      if (!pendingId) return;

      var result = BP.removeProfile(pendingId);
      hideConfirm();
      if (!result.ok) return;

      if (result.remaining === 0) manageMode = false;
      renderProfiles();

      if (notice) {
        notice.hidden = false;
        notice.textContent = result.promoted
          ? result.removed.name + ' removed. ' + result.promoted.name + ' is now the main profile.'
          : result.removed.name + ' removed.';
      }
      if (formError) formError.textContent = '';
    });
  }

  /* Banner after returning from the create-account page */
  if (notice) {
    var query = window.location.search;
    var added = query.match(/added=(\d+)/);

    if (query.indexOf('created=1') !== -1) {
      notice.hidden = false;
      notice.textContent = 'Account created. Select your profile and sign in.';
    } else if (query.indexOf('deactivated=1') !== -1) {
      notice.hidden = false;
      notice.textContent = 'Account deactivated. That profile has been removed.';
    } else if (query.indexOf('reset=1') !== -1) {
      notice.hidden = false;
      notice.textContent = 'Password changed. Sign in with your new password.';
    } else if (query.indexOf('signin=required') !== -1) {
      notice.hidden = false;
      notice.textContent = 'Sign in to open your profile and settings.';
    } else if (added) {
      var count = parseInt(added[1], 10);
      notice.hidden = false;
      notice.textContent = count === 1
        ? 'Member added. They can now sign in with their own email and password.'
        : count + ' members added. They can now sign in with their own emails and passwords.';
    }
  }

  /* ==================================================================
     Validation while typing
     ================================================================== */

  if (emailInput) {
    emailInput.addEventListener('blur', function () {
      if (emailInput.value.trim() === '') { clearState(emailInput, emailError); return; }
      var result = BP.checkEmail(emailInput.value);
      setState(emailInput, emailError, result.ok, result.message);
    });

    emailInput.addEventListener('input', function () {
      if (formError) formError.textContent = '';
      if (emailError && emailError.textContent !== '') {
        var result = BP.checkEmail(emailInput.value);
        setState(emailInput, emailError, result.ok, result.ok ? '' : result.message);
      }
    });
  }

  if (passwordInput) {
    passwordInput.addEventListener('input', function () {
      if (formError) formError.textContent = '';
      clearState(passwordInput, passwordError);
    });
  }

  /* Show / hide password */
  document.querySelectorAll('[data-toggle]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = document.getElementById(btn.getAttribute('data-toggle'));
      if (!input) return;
      var hidden = input.type === 'password';
      input.type = hidden ? 'text' : 'password';
      btn.setAttribute('aria-label', hidden ? 'Hide password' : 'Show password');
      input.focus();
    });
  });

  /* ==================================================================
     Sign in
     ================================================================== */

  function submit() {
    var emailResult = BP.checkEmail(emailInput ? emailInput.value : '');
    setState(emailInput, emailError, emailResult.ok, emailResult.message);

    if (!emailResult.ok) { emailInput.focus(); return; }

    if (!passwordInput.value) {
      setState(passwordInput, passwordError, false, 'Enter your password.');
      passwordInput.focus();
      return;
    }

    /* If a profile is picked, the typed email must belong to it. With no
       profiles on this device (new browser), email + password is enough. */
    var picked = selectedId
      ? (DIRECTORY ? directoryPerson(selectedId) : BP.getProfile(selectedId))
      : null;
    if (picked && String(picked.email).toLowerCase() !== emailInput.value.trim().toLowerCase()) {
      setState(emailInput, emailError, false,
        'That email doesn\u2019t match the selected profile.');
      emailInput.focus();
      return;
    }

    formError.textContent = '';
    signInBtn.disabled = true;

    BP.api('apiLogin', {
      email: emailInput.value.trim(),
      password: passwordInput.value
    }).then(function (res) {
      signInBtn.disabled = false;

      if (!res.ok) {
        if (res.reason === 'credentials') {
          setState(passwordInput, passwordError, false, res.message);
          passwordInput.focus();
        } else {
          formError.textContent = res.message || 'Sign-in failed. Please try again.';
        }
        return;
      }

      /* Bring this browser's profiles in line with the household in MySQL,
         then open the dashboard as the signed-in person. */
      BP.syncHousehold(res.members);
      var me = BP.profileByDbId(res.userId);
      BP.setSession(me ? me.id : 'u' + res.userId);
      window.location.href = 'dashboard';
    });
  }

  if (signInBtn) signInBtn.addEventListener('click', submit);

  [emailInput, passwordInput].forEach(function (input) {
    if (!input) return;
    input.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') submit();
    });
  });

  /* "Forgot password?" is handled by forgot-password.js. */
})();
