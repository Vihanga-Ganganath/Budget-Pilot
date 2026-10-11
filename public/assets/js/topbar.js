/**
 * Budget Pilot - Unified Topbar JS
 * Handles dropdown toggles for notifications and profile avatars.
 */
(function () {
  'use strict';
  document.addEventListener('DOMContentLoaded', function() {
    const profileBtn = document.getElementById('topbarProfileDropdownBtn');
    const profilePanel = document.getElementById('topbarProfilePanel');
    const notifBtn = document.getElementById('topbarNotifBtn');
    const notifPanel = document.getElementById('topbarNotifPanel');
    const panels = [
      { btn: profileBtn, panel: profilePanel },
      { btn: notifBtn, panel: notifPanel }
    ];
    panels.forEach(item => {
      if (item.btn && item.panel) {
        item.btn.addEventListener('click', (e) => {
          e.stopPropagation();
          panels.forEach(other => {
            if (other.panel && other.panel !== item.panel) {
              other.panel.classList.remove('show');
            }
          });
          item.panel.classList.toggle('show');
        });
        item.panel.addEventListener('click', (e) => {
          e.stopPropagation();
        });
      }
    });
    document.addEventListener('click', () => {
      panels.forEach(item => {
        if (item.panel) item.panel.classList.remove('show');
      });
    });
  });
})();
