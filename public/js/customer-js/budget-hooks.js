/* Budget Pilot — budgets page hooks

   Loaded after budgets.js, which has already drawn the category cards. This
   adds a "Log an expense" link to each one so a category can be carried
   straight into the manual entry page, and reports back when spending has
   just been recorded. Kept separate so budgets.js stays untouched. */

(function () {
  'use strict';

  var BP = window.BudgetPilot;
  var grid = document.getElementById('categoryGrid');
  if (!BP || !grid) return;

  /* ---------- Styles for the link this script injects ---------- */

  var style = document.createElement('style');
  style.textContent =
    '.cat__log{display:inline-flex;align-items:center;gap:6px;margin-top:12px;' +
    'font-size:.84rem;font-weight:600;color:var(--accent);text-decoration:none;}' +
    '.cat__log svg{width:15px;height:15px;}' +
    '.cat__log:hover{text-decoration:underline;}' +
    '.cat__log:focus-visible{outline:2px solid var(--accent);outline-offset:3px;border-radius:4px;}';
  document.head.appendChild(style);

  var ICON =
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" ' +
    'stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' +
    '<circle cx="12" cy="12" r="9"/><path d="M12 8.5v7M8.5 12h7"/></svg>';

  /* ---------- One link per card ---------- */

  /* Cards are drawn in CATEGORIES order, so position identifies the category. */
  var cards = grid.querySelectorAll('.cat');

  Array.prototype.forEach.call(cards, function (card, index) {
    var category = BP.CATEGORIES[index];
    if (!category || card.querySelector('.cat__log')) return;

    var link = document.createElement('a');
    link.className = 'cat__log';
    link.href = 'expense.html?category=' + encodeURIComponent(category.key);
    link.innerHTML = ICON;
    link.appendChild(document.createTextNode('Log an expense'));
    link.setAttribute('aria-label', 'Log an expense against ' + category.label);

    (card.querySelector('.cat__body') || card).appendChild(link);
  });

  /* ---------- Confirmation after returning from the entry page ---------- */

  var logged = (new RegExp('[?&]logged=([^&]*)').exec(window.location.search) || [])[1];
  if (!logged) return;

  var amount = Number(decodeURIComponent(logged));
  if (!isFinite(amount) || amount <= 0) return;

  var toast = document.getElementById('toast');
  if (!toast) return;

  var CURRENCIES = { USD:'$', EUR:'\u20AC', GBP:'\u00A3', LKR:'Rs', INR:'\u20B9', AUD:'$' };
  var profile = BP.getProfile(BP.getSession()) || {};
  var symbol = CURRENCIES[(profile.prefs || {}).currency] || '$';

  window.setTimeout(function () {
    toast.textContent = symbol +
      amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) +
      ' recorded. Category totals below are updated.';
    toast.hidden = false;
    window.setTimeout(function () { toast.hidden = true; }, 4000);
  }, 250);
})();
