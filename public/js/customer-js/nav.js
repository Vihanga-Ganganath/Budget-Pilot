/* Budget Pilot — app navigation on small screens

   Below 960px the sidebar is a drawer (see the end of settings.css). This
   opens and closes it: the menu button in the app bar, the close button and
   the dimmed backdrop, the Escape key, and following any link inside it.
   On wider screens it does nothing — the sidebar is simply always there. */

(function () {
  'use strict';

  var sidebar = document.getElementById('sidebar');
  var toggle = document.querySelector('[data-nav-toggle]');
  if (!sidebar || !toggle) return;

  var body = document.body;
  var narrow = window.matchMedia('(max-width: 960px)');
  var closeBtn = sidebar.querySelector('[data-nav-close]');

  var scrim = document.createElement('div');
  scrim.className = 'navscrim';
  scrim.setAttribute('aria-hidden', 'true');
  document.body.appendChild(scrim);

  function isOpen() { return body.classList.contains('nav-open'); }

  function open() {
    body.classList.add('nav-open');
    toggle.setAttribute('aria-expanded', 'true');
    /* after the slide starts, so the browser does not jump-scroll to it */
    window.setTimeout(function () {
      var first = sidebar.querySelector('a[href], button');
      if (first) first.focus();
    }, 60);
  }

  function close(returnFocus) {
    if (!isOpen()) return;
    body.classList.remove('nav-open');
    toggle.setAttribute('aria-expanded', 'false');
    if (returnFocus) toggle.focus();
  }

  toggle.addEventListener('click', function () {
    if (isOpen()) close(true); else open();
  });
  if (closeBtn) closeBtn.addEventListener('click', function () { close(true); });
  scrim.addEventListener('click', function () { close(true); });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && isOpen()) close(true);
  });

  /* Keep Tab inside the open drawer instead of wandering behind the scrim. */
  sidebar.addEventListener('keydown', function (e) {
    if (e.key !== 'Tab' || !isOpen()) return;
    var items = sidebar.querySelectorAll('a[href], button:not([disabled])');
    var visible = Array.prototype.filter.call(items, function (n) { return n.offsetParent !== null; });
    if (!visible.length) return;
    var first = visible[0];
    var last = visible[visible.length - 1];
    if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
    else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
  });

  /* A tapped link navigates away; close so the back button shows a clean page. */
  sidebar.addEventListener('click', function (e) {
    if (e.target.closest && e.target.closest('a[href]')) close(false);
  });

  /* Rotating a tablet to landscape brings the sidebar back — drop the open state. */
  var onChange = function () { if (!narrow.matches) close(false); };
  if (narrow.addEventListener) narrow.addEventListener('change', onChange);
  else if (narrow.addListener) narrow.addListener(onChange);
})();
