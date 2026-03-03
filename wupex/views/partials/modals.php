<?php $t = fn($k) => Lang::get($k); $sym = htmlspecialchars(Setting::get('currency_symbol','ر.س')); ?>
<!-- Product Modal -->
<div class="modal-ov" id="productModal">
  <div class="modal-box">
    <div class="modal-img" id="mImg"></div>
    <div class="modal-body">
      <div class="modal-hdr">
        <div class="modal-title" id="mTitle"></div>
        <button class="modal-close-x" onclick="closeModal()">×</button>
      </div>
      <div class="modal-cat" id="mCat"></div>
      <div id="mRating" class="modal-rating"></div>
      <div id="mDelivery" style="margin-bottom:8px"></div>
      <div class="modal-flags" id="mFlags"></div>
      <div class="modal-desc" id="mDesc"></div>
      <div class="modal-section-title"><?= $t('select_amount') ?></div>
      <div class="modal-prices" id="mPrices"></div>
      <input type="hidden" id="mProductId">
      <div class="modal-actions">
        <button class="btn-buy btn-add-cart" data-txt="🛒 <?= $t('add_cart') ?>" onclick="addToCart()">🛒 <?= $t('add_cart') ?></button>
        <button class="btn-wish" id="mWishBtn" onclick="toggleWishlist(this.dataset.id)">❤️</button>
        <button class="btn-close" onclick="closeModal()"><?= $t('close') ?></button>
      </div>
    </div>
  </div>
</div>
