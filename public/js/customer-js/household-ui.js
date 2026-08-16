/* Budget Pilot — household chrome

   One account can hold several people: the person who signed up (role 'Main')
   and anyone they added (role 'Member'). They share one budget, so this file
   applies the two rules that differ between them on every page:

     [data-main-only]    hidden from members — the Household view, mainly
     [data-member-lock]  disabled for members — the controls that set the plan

   It also fills #householdNote, if the page has one, with a line explaining
   whose budget is on screen.

   Load after js/store.js. */

(function () {
  'use strict';

  var BP = window.BudgetPilot;
  if (!BP || !BP.isMain) return;

  var profileId = BP.getSession();
  if (!profileId) return;

  var me = BP.getProfile(profileId);
  if (!me) return;

  var main = BP.mainProfile();
  var iAmMain = BP.isMain(profileId);
  var people = BP.householdMembers();
  var shared = people.length > 1;

  function firstName(name) {
    return String(name || '').trim().split(/\s+/)[0] || 'the account holder';
  }

  function apply() {
    /* Views only the account holder gets. */
    if (!iAmMain) {
      Array.prototype.forEach.call(document.querySelectorAll('[data-main-only]'), function (node) {
        node.hidden = true;
      });
    }

    /* A member spends against the plan but does not set it. Locking is done
       here rather than in each page so a new page cannot forget it. */
    if (!iAmMain && shared) {
      document.body.classList.add('household-locked');

      Array.prototype.forEach.call(document.querySelectorAll('[data-member-lock]'), function (node) {
        if ('disabled' in node) node.disabled = true;
        node.setAttribute('aria-disabled', 'true');
        node.title = 'Only ' + firstName(main && main.name) + ' can change this on a shared account.';
      });

      /* Settings has its own income box; say where the figure comes from. */
      var ownerHint = document.getElementById('incomeOwnerHint');
      if (ownerHint) ownerHint.hidden = false;
    }

    /* Some headings mean something different to a member: the transaction
       list is only theirs, while the budget totals are everyone's. */
    if (!iAmMain && shared) {
      Array.prototype.forEach.call(document.querySelectorAll('[data-member-label]'), function (node) {
        node.textContent = node.getAttribute('data-member-label');
      });
    }

    var slot = document.getElementById('householdNote');
    if (!slot || !shared) return;

    slot.hidden = false;
    slot.textContent = iAmMain
      ? 'Shared account — ' + people.length + ' people spend from these categories. ' +
        'Everything they buy comes off the same budget.'
      : "You're on " + firstName(main && main.name) + "'s account. Totals and charts " +
        'cover the whole household; the transactions listed are only yours.';
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', apply);
  } else {
    apply();
  }
})();
