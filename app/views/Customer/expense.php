
<?php require APPROOT . '/Views/shared/header.php'; ?>

      <a class="backlink" href="<?php echo URLROOT; ?>/customer/budgets">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
        Back to Budget Setup
      </a>

      <h1 class="page__title">Manual Expense Entry</h1>
      <p class="page__sub">Update your copilot with recent transaction data.</p>

      <div class="entrylayout">
        <div class="entrylayout__main">
          <div id="entryList"></div>

          <button class="addmore" type="button" id="addEntryBtn">
            
            Add another expense
          </button>
        </div>

        <aside class="entrylayout__side">
          <section class="card pickcard">
            <h2 class="eyebrow" id="pickHeading">Category selection</h2>
            <p class="pickcard__for" id="pickFor"></p>
            <div class="picks" id="pickGrid" role="group" aria-labelledby="pickHeading"></div>
            <p class="err" id="pickErr" role="alert"></p>
          </section>

          <section class="review">
            <span class="eyebrow eyebrow--onnavy">Final review</span>

            <div class="review__top">
              <span class="review__label">Total for all entries:</span>
              <strong class="review__total" id="reviewTotal">$0.00</strong>
            </div>

            <ul class="impact" id="impactList"></ul>
            <p class="review__empty" id="reviewEmpty">Enter an amount and pick a category to see the effect on your budget.</p>

            <button class="btn btn--primary review__save" type="button" id="saveExpenseBtn">
              
              Save expense
            </button>
          </section>
        </aside>
      </div>
    <div class="toast" id="toast" role="status" hidden></div>

<div class="modal" id="modal" hidden>
  <div class="modal__backdrop" id="modalBackdrop"></div>
  <div class="modal__box" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <h2 class="modal__title" id="modalTitle"></h2>
    <div class="modal__body" id="modalBody"></div>
    <div class="modal__actions" id="modalActions">
      </div>
    </div>
  </div>

  <template id="entryTemplate">
  <div class="entry">
    <section class="card entry__card">
      <div class="entry__head">
        <h2 class="entry__title">
          
          Transaction details
        </h2>
        <div class="entry__headright">
          <span class="chip entry__chip">No category</span>
          <button class="entry__drop" type="button">Remove</button>
        </div>
      </div>

      <div class="fieldrow">
        <div class="field">
          <label class="label entry__amountlabel">Amount</label>
          <div class="control control--money control--lg">
            <span class="control__prefix entry__symbol">$</span>
            <input class="entry__amount" type="number" min="0" step="0.01" placeholder="0.00" />
          </div>
        </div>
        <div class="field">
          <label class="label entry__datelabel">Date</label>
          <div class="control">
            <input class="entry__date" type="date" />
          </div>
        </div>
      </div>

      <label class="label entry__desclabel">Description</label>
      <div class="control">
        <input class="entry__desc" type="text" placeholder="e.g. Weekly grocery shopping" />
      </div>
    </section>

    <section class="card entry__card">
      <h2 class="entry__title">
        
        Quick notes
      </h2>
      <div class="control control--area">
        <textarea class="entry__notes" rows="4" placeholder="Any additional context for your financial records..."></textarea>
      </div>
      <div class="tags entry__tags"></div>
    </section>
  </div>
</template>

<template id="pickTemplate">
  <button class="pick" type="button" aria-pressed="false">
    <span class="pick__art"><img class="pick__photo" src="" alt="" /></span>
    <span class="pick__name"></span>
    <span class="pick__tick" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7"/></svg>
    </span>
  </button>
</template>

<script>window.URLROOT = "<?php echo URLROOT; ?>";</script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/store.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/store.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/notify.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/notify.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/household-ui.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/household-ui.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/expense.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/expense.js'); ?>"></script>

<?php require APPROOT . '/Views/shared/footer.php'; ?>






