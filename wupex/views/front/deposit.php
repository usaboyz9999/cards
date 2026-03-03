<?php
Auth::requireLogin();
$isAr = Lang::isRtl();
$S    = Setting::all();
$sym  = htmlspecialchars($S['currency_symbol']??'ر.س');
$user = Auth::user();
$balance = Wallet::balance($user['id']);
$minDep  = (float)($S['min_deposit']??5);
$maxDep  = (float)($S['max_deposit']??2000);
$bonus   = (float)($S['wallet_bonus_percent']??0);
?>
<div class="page-container"><div class="page-container-inner">
  <h2 style="margin-bottom:20px">💳 <?= $isAr?'شحن المحفظة':'Top Up Wallet' ?></h2>
  <?php require VIEWS_PATH.'/partials/flash.php'; ?>

  <!-- الرصيد الحالي -->
  <div style="background:linear-gradient(135deg,var(--primary),var(--accent));border-radius:16px;padding:22px;margin-bottom:20px;text-align:center">
    <div style="font-size:12px;opacity:.8;margin-bottom:6px"><?= $isAr?'رصيدك الحالي':'Current Balance' ?></div>
    <div style="font-size:32px;font-weight:900"><?= $sym ?><?= number_format($balance,2) ?></div>
    <?php if($bonus>0): ?>
    <div style="margin-top:8px;font-size:12px;background:rgba(255,255,255,.15);padding:4px 12px;border-radius:20px;display:inline-block">
      🎁 <?= $isAr?'مكافأة':'Bonus' ?> <?= $bonus ?>% <?= $isAr?'على كل إيداع':'on every deposit' ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- نموذج الإيداع -->
  <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:22px;margin-bottom:16px">
    <h3 style="font-size:14px;font-weight:700;margin-bottom:16px">📋 <?= $isAr?'طلب إيداع جديد':'New Deposit Request' ?></h3>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
      <input type="hidden" name="action" value="submit_deposit">
      <div class="fg" style="margin-bottom:12px">
        <label><?= $isAr?'المبلغ':'Amount' ?> (<?= $sym ?>)</label>
        <input type="number" name="amount" required min="<?= $minDep ?>" max="<?= $maxDep ?>" step="0.01"
               placeholder="<?= $minDep ?> - <?= $maxDep ?>">
        <span style="font-size:11px;color:var(--muted)"><?= $isAr?'الحد الأدنى':'Min' ?>: <?= $sym ?><?= $minDep ?> | <?= $isAr?'الحد الأقصى':'Max' ?>: <?= $sym ?><?= $maxDep ?></span>
      </div>
      <div class="fg" style="margin-bottom:12px">
        <label><?= $isAr?'طريقة الدفع':'Payment Method' ?></label>
        <select name="payment_method">
          <?php if(!empty($S['payment_bank'])): ?><option value="bank"><?= $isAr?'تحويل بنكي':'Bank Transfer' ?></option><?php endif; ?>
          <?php if(!empty($S['payment_card'])): ?><option value="card"><?= $isAr?'بطاقة ائتمان':'Credit Card' ?></option><?php endif; ?>
        </select>
      </div>
      <?php if(!empty($S['bank_info'])): ?>
      <div style="background:rgba(124,58,237,.08);border:1px solid rgba(124,58,237,.25);border-radius:10px;padding:12px 14px;margin-bottom:12px;font-size:12px;line-height:1.9">
        🏦 <strong><?= $isAr?'معلومات الحساب البنكي':'Bank Account Info' ?>:</strong><br>
        <?= nl2br(htmlspecialchars($S['bank_info'])) ?>
      </div>
      <?php endif; ?>
      <div class="fg" style="margin-bottom:12px">
        <label><?= $isAr?'رقم مرجع / إيصال الدفع (اختياري)':'Payment Ref / Receipt (optional)' ?></label>
        <input type="text" name="ref_number" placeholder="TXN-XXXX">
      </div>
      <div class="fg" style="margin-bottom:14px">
        <label><?= $isAr?'صورة الإيصال (اختياري)':'Receipt Image (optional)' ?></label>
        <input type="file" name="receipt" accept="image/*,.pdf">
      </div>
      <button type="submit" class="btn btn-primary btn-full">📤 <?= $isAr?'إرسال طلب الإيداع':'Submit Deposit Request' ?></button>
    </form>
  </div>

  <!-- سجل الإيداعات -->
  <?php $deps = Database::fetchAll("SELECT * FROM ".DB_PREFIX."deposit_requests WHERE user_id=? ORDER BY created_at DESC LIMIT 10",[Auth::id()]); ?>
  <?php if($deps): ?>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px">
    <h3 style="font-size:14px;font-weight:700;margin-bottom:12px">📋 <?= $isAr?'طلباتي السابقة':'My Previous Requests' ?></h3>
    <?php foreach($deps as $d): ?>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.04);font-size:13px">
      <div>
        <span class="bpill bp-<?= $d['status'] ?>"><?= $d['status'] ?></span>
        <span style="margin:0 8px;font-weight:700"><?= $sym ?><?= number_format($d['amount'],2) ?></span>
      </div>
      <span style="font-size:11px;color:var(--muted)"><?= date('Y-m-d',strtotime($d['created_at'])) ?></span>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
</div></div>