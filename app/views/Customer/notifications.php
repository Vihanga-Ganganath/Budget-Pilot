
<?php require APPROOT . '/Views/shared/header.php'; ?>

      <div class="nothead">
        <div>
          <h1 class="page__title">Notifications</h1>
          <p class="page__sub">Stay updated with your budget and shopping alerts.</p>
        </div>
        <button class="btn btn--outline" type="button" id="markAllBtn">
          
          Mark all as read
        </button>
      </div>

      <div class="chips" id="chips" role="group" aria-label="Filter by category"></div>

      <ul class="notes" id="notes"></ul>

      <p class="empty empty--big" id="emptyMsg" hidden></p>

      <div class="nothistory">
        <button class="historybtn" type="button" id="historyBtn">
          <span id="historyLabel">View notification history</span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>
        </button>
      </div>
    <div class="toast" id="toast" role="status" hidden></div>

<template id="noteTemplate">
  <li class="note">
    <span class="note__icon" aria-hidden="true"></span>
    <div class="note__body">
      <div class="note__head">
        <h2 class="note__title"></h2>
        <span class="note__time"></span>
      </div>
      <p class="note__text"></p>
    </div>
  </li>
</template>

<script>window.URLROOT = "<?php echo URLROOT; ?>";</script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/store.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/store.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/orders.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/orders.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/catalog-data.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/catalog-data.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/notify.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/notify.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/household-ui.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/household-ui.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/notifications.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/notifications.js'); ?>"></script>

<?php require APPROOT . '/Views/shared/footer.php'; ?>






