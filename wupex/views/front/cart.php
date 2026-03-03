<?php
$isAr      = Lang::isRtl();
$S         = Setting::all();
$sym       = htmlspecialchars($S['currency_symbol']??'ر.س');
$cartItems = Cart::items(Auth::id());
$cartTotal = Cart::total(Auth::id());
$csrf      = Session::csrf();
$siteUrl   = Helpers::siteUrl();
?>
<style>
.cart-item-row{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:14px 16px;margin-bottom:10px;display:flex;align-items:center;gap:14px;transition:all .3s}
.cart-item-row.removing{opacity:0;transform:scale(.95)}
.cart-summary{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px;position:sticky;top:80px}
</style>

<div class="page-container">
<div class="page-container-inner">
  <?php require VIEWS_PATH.'/partials/flash.php'; ?>
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
    <h2 style="font-size:20px;font-weight:900">🛒 <?= $isAr?'سلة التسوق':'Shopping Cart' ?></h2>
    <?php if($cartItems): ?><span style="background:var(--primary);color:#fff;border-radius:20px;padding:3px 12px;font-size:12px;font-weight:700"><?= count($cartItems) ?></span><?php endif; ?>
  </div>

  <?php if(empty($cartItems)): ?>
  <div style="text-align:center;padding:60px 40px;background:var(--card);border:1px solid var(--border);border-radius:16px">
    <div style="font-size:56px;margin-bottom:14px">🛒</div>
    <h3 style="margin-bottom:8px"><?= $isAr?'سلتك فارغة':'Your cart is empty' ?></h3>
    <p style="color:var(--muted);margin-bottom:18px"><?= $isAr?'أضف منتجات وابدأ التسوق':'Add products and start shopping' ?></p>
    <a href="<?= $siteUrl ?>" style="background:linear-gradient(135deg,var(--primary),var(--accent));color:#fff;padding:11px 26px;border-radius:10px;font-weight:700"><?= $isAr?'تسوق الآن':'Shop Now' ?></a>
  </div>
  <?php else: ?>

  <div style="display:grid;grid-template-columns:1fr 300px;gap:18px;align-items:start">
    <!-- Items -->
    <div id="cartItemsList">
      <?php foreach($cartItems as $item): ?>
      <div class="cart-item-row" id="ci-<?= $item['id'] ?>">
        <div style="width:52px;height:52px;border-radius:10px;background:linear-gradient(135deg,<?= htmlspecialchars($item['color1']??'#1a1a2e') ?>,<?= htmlspecialchars($item['color2']??'#7c3aed') ?>);display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0">
          <?= $item['icon']??'📦' ?>
        </div>
        <div style="flex:1">
          <div style="font-weight:700;font-size:14px"><?= htmlspecialchars($isAr?$item['name_ar']:$item['name_en']) ?></div>
          <?php if(!empty($item['price_label'])): ?><div style="font-size:11px;color:var(--muted)"><?= htmlspecialchars($item['price_label']) ?></div><?php endif; ?>
          <div style="font-weight:700;color:var(--success);margin-top:3px"><?= $sym ?><?= number_format($item['price']*$item['quantity'],2) ?></div>
        </div>
        <div style="display:flex;align-items:center;gap:8px">
          <div style="display:flex;align-items:center;gap:4px;background:var(--bg);border:1px solid var(--border);border-radius:8px;overflow:hidden">
            <button onclick="cartQty(<?= $item['id'] ?>,-1)" style="width:30px;height:30px;background:none;border:none;cursor:pointer;font-size:16px;font-weight:700;color:var(--muted)">−</button>
            <span id="qty-<?= $item['id'] ?>" style="min-width:28px;text-align:center;font-weight:700;font-size:13px"><?= $item['quantity'] ?></span>
            <button onclick="cartQty(<?= $item['id'] ?>,1)" style="width:30px;height:30px;background:none;border:none;cursor:pointer;font-size:16px;font-weight:700;color:var(--muted)">+</button>
          </div>
          <button onclick="cartRemove(<?= $item['id'] ?>)" style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:var(--danger);width:34px;height:34px;border-radius:8px;cursor:pointer;font-size:15px" title="<?= $isAr?'حذف':'Remove' ?>">🗑️</button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Summary -->
    <div class="cart-summary">
      <h3 style="font-size:14px;font-weight:800;margin-bottom:14px">📋 <?= $isAr?'ملخص الطلب':'Order Summary' ?></h3>
      <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:13px">
        <span style="color:var(--muted)"><?= $isAr?'المجموع':'Subtotal' ?></span>
        <span id="cartSubtotal" style="font-weight:700"><?= $sym ?><?= number_format($cartTotal,2) ?></span>
      </div>
      <?php if(!empty($S['shipping_enabled']) && $S['shipping_cost']>0): ?>
      <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:13px">
        <span style="color:var(--muted)"><?= $isAr?'الشحن':'Shipping' ?></span>
        <span style="font-weight:700"><?= $sym ?><?= number_format($S['shipping_cost'],2) ?></span>
      </div>
      <?php endif; ?>
      <!-- Coupon -->
      <div style="margin:12px 0">
        <div style="display:flex;gap:6px">
          <input type="text" id="couponInput" placeholder="<?= $isAr?'كود الخصم':'Coupon code' ?>"
                 style="flex:1;background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:8px 10px;color:inherit;font-family:inherit;font-size:12px;outline:none">
          <button onclick="applyCoupon()" style="background:var(--primary);color:#fff;border:none;padding:8px 14px;border-radius:8px;font-weight:700;cursor:pointer;font-size:12px;font-family:inherit">✓</button>
        </div>
        <div id="couponMsg" style="font-size:11px;margin-top:5px"></div>
      </div>
      <div id="discountRow" style="display:none;justify-content:space-between;padding:6px 0;font-size:13px;color:var(--success)">
        <span><?= $isAr?'الخصم':'Discount' ?></span><span id="discountVal"></span>
      </div>
      <hr style="border:none;border-top:1px solid var(--border);margin:12px 0">
      <div style="display:flex;justify-content:space-between;font-size:16px;font-weight:900;margin-bottom:16px">
        <span><?= $isAr?'الإجمالي':'Total' ?></span>
        <span id="cartTotal" style="color:var(--success)"><?= $sym ?><?= number_format($cartTotal,2) ?></span>
      </div>
      <a href="<?= Helpers::siteUrl('?page=checkout') ?>"
         style="display:block;text-align:center;background:linear-gradient(135deg,var(--primary),var(--accent));color:#fff;padding:13px;border-radius:11px;font-weight:700;text-decoration:none;font-size:14px">
        💳 <?= $isAr?'إتمام الشراء':'Checkout' ?>
      </a>
      <a href="<?= $siteUrl ?>" style="display:block;text-align:center;color:var(--muted);font-size:12px;margin-top:10px"><?= $isAr?'← متابعة التسوق':'← Continue Shopping' ?></a>
    </div>
  </div>
  <?php endif; ?>
</div>
</div>

<script>
const _sym = '<?= addslashes($sym) ?>';
const _csrf = '<?= $csrf ?>';

async function cartRemove(cartId) {
  const row = document.getElementById('ci-' + cartId);
  if(row) row.classList.add('removing');
  try {
    const r = await fetch('?action=cart_remove', {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:`cart_id=${cartId}&csrf=${_csrf}`
    });
    const d = await r.json();
    if(d.success) {
      setTimeout(()=>{ if(row) row.remove(); updateCartPageTotals(); }, 280);
      showToast(d.msg||'تم الحذف ✓','success');
      updateCartBadge(d.count);
    }
  } catch(e) { if(row) row.classList.remove('removing'); }
}

async function cartQty(cartId, delta) {
  const qEl = document.getElementById('qty-' + cartId);
  if(!qEl) return;
  const cur = parseInt(qEl.textContent);
  const nq  = Math.max(1, cur + delta);
  if(nq === cur) return;
  try {
    const r = await fetch('?action=cart_update_qty', {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:`cart_id=${cartId}&qty=${nq}&csrf=${_csrf}`
    });
    const d = await r.json();
    if(d.success) {
      qEl.textContent = nq;
      updateCartPageTotals();
      showToast((window._t?.cart_updated||'تم التحديث ✓'),'success');
    }
  } catch(e) {}
}

function updateCartPageTotals() {
  let total = 0;
  document.querySelectorAll('.cart-item-row:not(.removing)').forEach(row => {
    const id  = row.id.replace('ci-','');
    const qty = parseInt(document.getElementById('qty-'+id)?.textContent||1);
    const priceText = row.querySelector('[style*="color:var(--success)"]')?.textContent||'0';
    // We can't easily recalculate, so just reload totals via API
  });
  // Re-fetch cart total
  fetch('?action=cart_items').then(r=>r.json()).then(d=>{
    const sub = document.getElementById('cartSubtotal');
    const tot = document.getElementById('cartTotal');
    if(sub) sub.textContent = _sym + d.total;
    if(tot) tot.textContent = _sym + d.total;
    updateCartBadge(d.count);
    // If cart empty, refresh page
    if(!d.items || !d.items.length) {
      setTimeout(()=>location.reload(), 400);
    }
  }).catch(()=>{});
}
</script>
