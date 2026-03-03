<?php $t = fn($k) => Lang::get($k); $sym = htmlspecialchars(Setting::get('currency_symbol','ر.س')); ?>
<div class="cart-overlay" id="cartOverlay" onclick="closeCart()"></div>
<div class="cart-sidebar" id="cartSidebar">
  <div class="cart-hdr">
    <div class="cart-title">🛒 <?= $t('cart') ?></div>
    <button onclick="closeCart()" style="background:none;border:none;color:var(--muted);font-size:22px;cursor:pointer">×</button>
  </div>
  <div class="cart-body" id="cartBody">
    <div class="cart-empty"><div class="ico">🛒</div><p><?= $t('cart_empty') ?></p></div>
  </div>
  <div class="cart-footer">
    <div class="cart-total">
      <span><?= $t('total') ?></span>
      <span id="cartTotalVal">0 <?= $sym ?></span>
    </div>
    <button class="cart-checkout-btn" onclick="window.location.href='?page=checkout'">
      ✅ <?= $t('checkout') ?>
    </button>
    <div style="text-align:center;margin-top:8px">
      <a href="?page=cart" style="font-size:12px;color:var(--muted)"><?= Lang::isRtl()?'عرض السلة الكاملة':'View Full Cart' ?></a>
    </div>
  </div>
</div>
