<?php
$isAr = Lang::isRtl();
$t    = fn($k) => Lang::get($k);
$S    = Setting::all();
$lang = Lang::current();
$sym  = htmlspecialchars($S['currency_symbol']??'ر.س');
$cartItems = Cart::items(Auth::id());
$cartTotal = Cart::total(Auth::id());
$user = Auth::user();
if(empty($cartItems)) { Helpers::redirect(Helpers::siteUrl('?page=cart')); }
$tax = (!empty($S['tax_enabled'])) ? round($cartTotal * (float)($S['tax_percent']??0) / 100, 2) : 0;
$shipping = (!empty($S['shipping_enabled']) && $cartTotal < (float)($S['shipping_free_above']??200)) ? (float)($S['shipping_cost']??15) : 0;
$grandTotal = $cartTotal + $tax + $shipping;
$walletBalance = $user ? Wallet::balance($user['id']) : 0;
?>
<div style="padding:20px;max-width:860px">
  <h2 style="margin-bottom:20px">✅ <?= $t('checkout') ?></h2>
  <?php require VIEWS_PATH.'/partials/flash.php'; ?>
  <form method="POST">
    <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
    <input type="hidden" name="action" value="place_order">
    <div style="display:grid;grid-template-columns:1fr 380px;gap:18px;align-items:start">
      <!-- Left: Details -->
      <div>
        <!-- Customer Info -->
        <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px;margin-bottom:14px">
          <h3 style="font-size:14px;font-weight:700;margin-bottom:14px">👤 <?= $isAr?'بيانات العميل':'Customer Info' ?></h3>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="fg"><label><?= $isAr?'الاسم':'Name' ?></label><input type="text" name="customer_name" required value="<?= htmlspecialchars($user['name']??'') ?>"></div>
            <div class="fg"><label>Email</label><input type="email" name="customer_email" required value="<?= htmlspecialchars($user['email']??'') ?>"></div>
            <div class="fg"><label><?= $isAr?'الهاتف':'Phone' ?></label><input type="text" name="customer_phone" value="<?= htmlspecialchars($user['phone']??'') ?>"></div>
          </div>
        </div>
        <!-- Payment Method -->
        <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px;margin-bottom:14px">
          <h3 style="font-size:14px;font-weight:700;margin-bottom:14px">💳 <?= $isAr?'طريقة الدفع':'Payment Method' ?></h3>
          <?php if(!empty($S['payment_wallet']) && $user && $walletBalance >= $grandTotal): ?>
          <label style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:var(--card2);border:2px solid var(--primary);border-radius:10px;cursor:pointer;margin-bottom:8px">
            <input type="radio" name="payment_method" value="wallet" checked style="accent-color:var(--primary)">
            <span>💰 <?= $isAr?'المحفظة الإلكترونية':'E-Wallet' ?></span>
            <span style="margin-right:auto;color:var(--success);font-size:12px;font-weight:700"><?= $sym ?><?= number_format($walletBalance,2) ?></span>
          </label>
          <?php elseif(!empty($S['payment_wallet']) && $user): ?>
          <div style="padding:10px 14px;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.3);border-radius:10px;font-size:12px;color:#ef4444;margin-bottom:8px">
            💰 <?= $isAr?'رصيد المحفظة غير كافٍ - ':'Wallet balance insufficient - ' ?><?= $sym ?><?= number_format($walletBalance,2) ?>
            <a href="?page=deposit" style="color:var(--primary);font-weight:700"> <?= $isAr?'شحن المحفظة':'Top Up' ?></a>
          </div>
          <?php endif; ?>
          <?php if(!empty($S['payment_bank'])): ?>
          <label style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:var(--card2);border:1px solid var(--border);border-radius:10px;cursor:pointer;margin-bottom:8px">
            <input type="radio" name="payment_method" value="bank" style="accent-color:var(--primary)">
            <span>🏦 <?= $isAr?'تحويل بنكي':'Bank Transfer' ?></span>
          </label>
          <?php endif; ?>
        </div>
        <!-- Coupon -->
        <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px">
          <h3 style="font-size:14px;font-weight:700;margin-bottom:12px">🏷️ <?= $isAr?'كوبون الخصم':'Discount Coupon' ?></h3>
          <div style="display:flex;gap:8px">
            <input type="text" name="coupon_code" id="couponInput" placeholder="<?= $t('coupon_placeholder') ?>" style="flex:1">
            <button type="button" class="btn btn-secondary" onclick="applyCoupon()"><?= $t('apply_coupon') ?></button>
          </div>
          <div id="couponMsg" style="font-size:12px;margin-top:8px"></div>
          <input type="hidden" name="coupon_applied" id="couponApplied">
          <input type="hidden" name="coupon_discount" id="couponDiscountVal">
        </div>
      </div>
      <!-- Right: Summary -->
      <div>
        <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px;position:sticky;top:80px">
          <h3 style="font-size:14px;font-weight:700;margin-bottom:14px">📋 <?= $isAr?'ملخص الطلب':'Order Summary' ?></h3>
          <?php foreach($cartItems as $ci): $grad="linear-gradient(135deg,{$ci['color1']},{$ci['color2']})"; ?>
          <div style="display:flex;align-items:center;gap:9px;margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid var(--border)">
            <div style="width:36px;height:36px;border-radius:8px;background:<?= $grad ?>;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0"><?= $ci['icon'] ?></div>
            <div style="flex:1;font-size:12px;font-weight:700"><?= htmlspecialchars($isAr?$ci['name_ar']:$ci['name_en']) ?></div>
            <div style="font-weight:700;color:var(--success);font-size:13px"><?= $sym ?><?= number_format($ci['price']*$ci['quantity'],2) ?></div>
          </div>
          <?php endforeach; ?>
          <div style="padding-top:8px">
            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:7px;color:var(--muted)"><span><?= $t('subtotal') ?></span><span><?= $sym ?><?= number_format($cartTotal,2) ?></span></div>
            <?php if($tax > 0): ?><div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:7px;color:var(--muted)"><span><?= $t('tax') ?> (<?= $S['tax_percent'] ?>%)</span><span><?= $sym ?><?= number_format($tax,2) ?></span></div><?php endif; ?>
            <?php if($shipping > 0): ?><div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:7px;color:var(--muted)"><span><?= $t('shipping') ?></span><span><?= $sym ?><?= number_format($shipping,2) ?></span></div><?php endif; ?>
            <?php if($shipping == 0 && !empty($S['shipping_enabled'])): ?><div style="font-size:11px;color:var(--success);margin-bottom:7px">✅ <?= $isAr?'شحن مجاني':'Free shipping' ?></div><?php endif; ?>
            <div id="discountRow" style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:7px;color:var(--success);display:none"><span><?= $t('discount') ?></span><span id="discountAmt"></span></div>
            <div style="display:flex;justify-content:space-between;font-size:17px;font-weight:900;border-top:1px solid var(--border);padding-top:10px;margin-top:8px">
              <span><?= $t('total') ?></span>
              <span style="color:var(--success)" id="totalRow"><?= $sym ?><?= number_format($grandTotal,2) ?></span>
            </div>
          </div>
          <input type="hidden" name="cart_total" value="<?= $cartTotal ?>">
          <input type="hidden" name="tax" value="<?= $tax ?>">
          <input type="hidden" name="shipping" value="<?= $shipping ?>">
          <input type="hidden" name="grand_total" id="grandTotalInput" value="<?= $grandTotal ?>">
          <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top:16px">
            🚀 <?= $isAr?'تأكيد الطلب':'Confirm Order' ?>
          </button>
          <div style="text-align:center;margin-top:10px;font-size:11px;color:var(--muted)">
            🔒 <?= $isAr?'طلبك محمي وآمن':'Your order is protected & secure' ?>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
