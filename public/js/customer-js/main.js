/* Budget Pilot — home page behaviour

   The splitter in the hero is the demo: a month's income arrives, then divides
   itself across the spending categories at the same weights the Budgets page
   uses, leaving the unallocated remainder as a cushion.

   Everything degrades safely: with motion turned off, the splitter shows its
   finished state straight away. */

(function () {
  'use strict';

  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ==================================================================
     Sticky top bar
     ================================================================== */

  var topbar = document.getElementById('topbar');
  if (topbar) {
    var onScroll = function () {
      topbar.classList.toggle('is-stuck', window.scrollY > 12);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ==================================================================
     The income splitter

     Shows what the Budgets page does with a salary: each category takes its
     own percentage of gross income (the weights in js/store.js), and what is
     not handed out stays as a cushion.
     ================================================================== */

  var splitter = document.getElementById('splitter');

  if (splitter) {
    var INCOME = 2000;
    var slots = Array.prototype.slice.call(splitter.querySelectorAll('.slot'));
    var incomeEl = document.getElementById('splitterIncome');
    var drain = document.getElementById('splitterDrain');
    var buffer = document.getElementById('splitterBuffer');
    var replay = document.getElementById('splitterReplay');

    var allocated = slots.reduce(function (sum, slot) {
      return sum + (Number(slot.style.getPropertyValue('--w')) || 0);
    }, 0);
    var bufferPct = 100 - allocated;

    var timers = [];

    function clearTimers() {
      timers.forEach(window.clearTimeout);
      timers = [];
    }

    function after(ms, fn) { timers.push(window.setTimeout(fn, ms)); }

    function reset() {
      clearTimers();
      slots.forEach(function (slot) { slot.classList.remove('is-filled'); });
      buffer.classList.remove('is-on');
      drain.style.width = '0%';
      incomeEl.textContent = '$' + INCOME.toLocaleString('en-US');
    }

    function fillAll() {
      slots.forEach(function (slot) { slot.classList.add('is-filled'); });
      drain.style.width = allocated + '%';
      buffer.classList.add('is-on');
    }

    function run() {
      reset();

      if (reduce) {
        fillAll();
        return;
      }

      /* The money leaves the bar as the columns fill, so the two read as one
         movement rather than two separate animations. */
      var handed = 0;

      slots.forEach(function (slot, i) {
        after(320 + i * 130, function () {
          slot.classList.add('is-filled');
          handed += Number(slot.style.getPropertyValue('--w')) || 0;
          drain.style.width = handed + '%';
        });
      });

      after(320 + slots.length * 130 + 260, function () {
        buffer.classList.add('is-on');
      });
    }

    if ('IntersectionObserver' in window && !reduce) {
      reset();
      var seen = false;
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting && !seen) {
            seen = true;
            run();
          }
        });
      }, { threshold: 0.3 });
      io.observe(splitter);
    } else {
      run();
    }

    if (replay) replay.addEventListener('click', run);
  }

  /* ==================================================================
     Scroll reveals
     ================================================================== */

  var revealables = document.querySelectorAll('.reveal');

  if (revealables.length) {
    var showAll = function () {
      Array.prototype.forEach.call(revealables, function (n) { n.classList.add('is-in'); });
    };

    if (!('IntersectionObserver' in window) || reduce) {
      showAll();
    } else {
      var revealer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          entry.target.classList.add('is-in');
          revealer.unobserve(entry.target);
        });
      }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

      Array.prototype.forEach.call(revealables, function (n, i) {
        n.style.transitionDelay = (i % 3) * 80 + 'ms';
        revealer.observe(n);
      });

      /* Belt and braces: nothing on this page is allowed to stay invisible
         because an observer never fired. */
      window.setTimeout(showAll, 4000);
    }
  }

  /* ==================================================================
     Footer subscribe
     ================================================================== */

  var subInput = document.getElementById('subEmail');
  var subBtn = document.getElementById('subBtn');
  var subMsg = document.getElementById('subMsg');

  if (subInput && subMsg) {
    var submit = function () {
      var value = subInput.value.trim();
      var valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);

      if (!valid) {
        subMsg.style.color = '#F2A0A0';
        subMsg.textContent = 'Enter an email address like name@example.com.';
        subInput.focus();
        return;
      }
      subMsg.style.color = '#9BEE95';
      subMsg.textContent = 'Subscribed. One email a month, nothing else.';
      subInput.value = '';
    };

    if (subBtn) subBtn.addEventListener('click', submit);
    subInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') submit();
    });
  }
})();
