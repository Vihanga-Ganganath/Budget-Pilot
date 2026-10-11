
<?php require APPROOT . '/Views/shared/header.php'; ?>

      <nav class="crumbs" aria-label="Breadcrumb">
        <a href="<?php echo URLROOT; ?>/customer/grocery">Catalog</a>
        <span aria-hidden="true"></span>
        <a href="<?php echo URLROOT; ?>/customer/grocery" id="crumbCategory">Category</a>
        <span aria-hidden="true"></span>
        <span id="crumbName">Product</span>
      </nav>

      <div class="detail">
        <!-- ---------- Gallery ---------- -->
        <div class="detail__left">
          <div class="gallery">
            <div class="gallery__main" id="galleryMain">
              <span class="tag tag--verified">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/></svg>
                Verified Item
              </span>
              <span class="tag tag--brand" id="galleryBrand">Brand</span>
              <img class="gallery__photo" id="galleryPhoto" src="" alt="" />
              <p class="gallery__empty" id="galleryEmpty">Add a picture for this brand in catalog-data.js</p>
            </div>
            <ul class="gallery__thumbs" id="galleryThumbs"></ul>
          </div>

          <section class="card facts">
            <h2 class="facts__title">
              
              <span id="factsTitle">Product Facts</span>
            </h2>
            <ul class="facts__list" id="factsList"></ul>
            <p class="facts__note" id="factsNote"></p>
          </section>
        </div>

        <!-- ---------- Summary ---------- -->
        <div class="detail__right">
          <section class="card summary">
            <div class="summary__head">
              <h1 class="summary__name" id="productName">Product</h1>
              <button class="summary__save" type="button" id="saveBtn" aria-pressed="false" aria-label="Save for later">
                
              </button>
            </div>

            <p class="rating" id="rating"></p>

            <div class="pricebox">
              <div class="pricebox__cell">
                <span class="pricebox__label">Current price</span>
                <strong class="pricebox__value" id="price"></strong>
              </div>
              <div class="pricebox__cell">
                <span class="pricebox__label">Unit price</span>
                <strong class="pricebox__value pricebox__value--sm" id="unitPrice"></strong>
              </div>
            </div>

            <p class="brandline">Brand</p>
            <div class="product__brands" id="brandPicker" role="group" aria-label="Choose a brand"></div>

            <dl class="specs" id="specs"></dl>

            <h2 class="summary__sub">Description</h2>
            <p class="summary__desc" id="description"></p>

            <div class="summary__actions">
            <button class="btn btn--primary" type="button" id="addBtn">
              
              Add to Cart
            </button>
            <button class="btn btn--outline" type="button" id="compareBtn">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 7H3l4-4M3 7h14a4 4 0 0 1 4 4M17 17h4l-4 4M21 17H7a4 4 0 0 1-4-4"/></svg>
              Compare brands
            </button>
            </div>
          </section>

          

          <section class="alternatives">
            <h2 class="section__title">Other Brands &amp; Alternatives</h2>
            <div class="alts" id="alternatives"></div>
          </section>
        </div>
      </div>
    <div class="toast" id="toast" role="status" hidden></div>

<div class="modal" id="modal" hidden>
  <div class="modal__backdrop" id="modalBackdrop"></div>
  <div class="modal__box modal__box--wide" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <h2 class="modal__title" id="modalTitle"></h2>
    <div class="modal__body" id="modalBody"></div>
    <div class="modal__actions" id="modalActions">
      </div>
    </div>
  </div>

  <script>window.URLROOT = "<?php echo URLROOT; ?>";</script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/store.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/store.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/catalog-data.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/catalog-data.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/notify.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/notify.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/household-ui.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/household-ui.js'); ?>"></script>
<script src="<?php echo URLROOT; ?>/public/js/customer-js/product.js?v=<?php echo @filemtime(APPROOT . '/../public/js/customer-js/product.js'); ?>"></script>

<?php require APPROOT . '/Views/shared/footer.php'; ?>







