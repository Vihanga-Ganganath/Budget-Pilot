(function () {
  'use strict';

  var sidebar = document.getElementById('sidebar');
  var toggle = document.querySelector('[data-nav-toggle]');
  if (!sidebar) return;

  var body = document.body;
  var narrow = window.matchMedia('(max-width: 960px)');
  var closeBtn = sidebar.querySelector('[data-nav-close]');

  var scrim = document.querySelector('.navscrim');
  if (!scrim) {
      scrim = document.createElement('div');
      scrim.className = 'navscrim';
      scrim.setAttribute('aria-hidden', 'true');
      document.body.appendChild(scrim);
  }

  function isOpen() { return body.classList.contains('nav-open'); }

  function open() {
    body.classList.add('nav-open');
    if (toggle) toggle.setAttribute('aria-expanded', 'true');
  }

  function close() {
    if (!isOpen()) return;
    body.classList.remove('nav-open');
    if (toggle) toggle.setAttribute('aria-expanded', 'false');
  }

  if (toggle) {
      toggle.addEventListener('click', function () {
        if (isOpen()) close(); else open();
      });
  }
  
  if (closeBtn) closeBtn.addEventListener('click', function () { close(); });
  if (scrim) scrim.addEventListener('click', function () { close(); });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && isOpen()) close();
  });

  sidebar.addEventListener('click', function (e) {
    if (e.target.closest && e.target.closest('a[href]')) close();
  });

  var onChange = function () { if (!narrow.matches) close(); };
  if (narrow.addEventListener) narrow.addEventListener('change', onChange);
  else if (narrow.addListener) narrow.addListener(onChange);
})();
