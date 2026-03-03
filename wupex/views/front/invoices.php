<?php
Auth::requireLogin();
$isAr = Lang::isRtl();
$sym  = htmlspecialchars(Setting::get('currency_symbol','ر.س'));
$orders = Order::userOrders(Auth::id());
?>
<div class="page-container"><div class="page-container-inner">
  <h2 style="margin-bottom:20px">🧾 <?= $isAr?'فواتيري':'My Invoices' ?></h2>
  <?php if(empty($orders)): ?>
  <div style="text-align:center;padding:48px;background:var(--card);border:1px solid var(--border);border-radius:14px">
    <div style="font-size:48px;margin-bottom:12px">🧾</div>
    <div style="color:var(--muted)"><?= $isAr?'لا توجد فواتير بعد':'No invoices yet' ?></div>
  </div>
  <?php else: foreach($orders as $o): ?>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:16px 18px;margin-bottom:10px;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap">
    <div>
      <div style="font-family:monospace;font-weight:700;color:var(--primary);font-size:13px"><?= htmlspecialchars($o['order_number']) ?></div>
      <div style="font-size:11px;color:var(--muted);margin-top:3px"><?= date('Y-m-d H:i',strtotime($o['created_at'])) ?></div>
    </div>
    <div style="display:flex;align-items:center;gap:10px">
      <span class="bpill bp-<?= $o['status'] ?>"><?= $o['status'] ?></span>
      <span style="font-weight:700;color:var(--success)"><?= $sym ?><?= number_format($o['total'],2) ?></span>
      <a href="<?= Helpers::siteUrl('?page=invoice&id='.$o['id']) ?>" style="background:var(--card2);border:1px solid var(--border);padding:5px 12px;border-radius:8px;font-size:12px;font-weight:700;color:var(--text)">🧾 <?= $isAr?'فاتورة':'Invoice' ?></a>
    </div>
  </div>
  <?php endforeach; endif; ?>
</div>
</div></div>