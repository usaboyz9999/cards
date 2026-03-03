<?php
$isAr = Lang::isRtl();
$t    = fn($k) => Lang::get($k);
$sym  = htmlspecialchars(Setting::get('currency_symbol','ر.س'));
?>
<div class="page-container"><div class="page-container-inner">
  <h2 style="margin-bottom:18px">📦 <?= $t('my_orders') ?></h2>
  <?php if(empty($orders)): ?>
  <div style="text-align:center;padding:48px;background:var(--card);border:1px solid var(--border);border-radius:16px">
    <div style="font-size:56px;margin-bottom:14px">📦</div>
    <h3 style="font-size:18px;margin-bottom:8px"><?= $isAr?'لا توجد طلبات بعد':'No orders yet' ?></h3>
    <p style="color:var(--muted);margin-bottom:18px"><?= $isAr?'ابدأ التسوق الآن!':'Start shopping now!' ?></p>
    <a href="<?= Helpers::siteUrl() ?>" style="background:linear-gradient(135deg,var(--primary),var(--accent));color:#fff;padding:11px 24px;border-radius:10px;font-weight:700;font-size:13px;text-decoration:none">🛒 <?= $t('shop_now') ?></a>
  </div>
  <?php else: ?>
  <?php foreach($orders as $o): $items = Order::items($o['id']); ?>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;margin-bottom:14px;overflow:hidden">
    <div style="padding:14px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;background:var(--card2)">
      <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
        <span style="font-family:monospace;font-weight:700;color:var(--primary)"><?= htmlspecialchars($o['order_number']) ?></span>
        <span class="bpill bp-<?= $o['status'] ?>"><?= $o['status'] ?></span>
      </div>
      <div style="display:flex;align-items:center;gap:14px">
        <span style="font-size:12px;color:var(--muted)"><?= date('Y-m-d H:i',strtotime($o['created_at'])) ?></span>
        <span style="font-weight:700;color:var(--success)"><?= $sym ?><?= number_format($o['total'],2) ?></span>
      </div>
    </div>
    <div style="padding:14px 18px">
      <?php foreach($items as $item): ?>
      <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.04);font-size:13px">
        <div>
          <span style="font-weight:700"><?= htmlspecialchars($isAr ? ($item['name_ar']??$item['product_name']??'') : ($item['name_en']??$item['product_name']??'')) ?></span>
          <?php if(!empty($item['price_label'])): ?> <span style="color:var(--muted);font-size:11px">(<?= htmlspecialchars($item['price_label']) ?>)</span><?php endif; ?>
        </div>
        <div style="font-weight:700;color:var(--success)"><?= $sym ?><?= number_format($item['price'],2) ?></div>
      </div>
      <?php if($o['status']==='completed' && !empty($item['codes'])): ?>
        <div style="background:linear-gradient(135deg,rgba(16,185,129,.12),rgba(124,58,237,.08));border:1px solid rgba(16,185,129,.3);border-radius:10px;padding:10px 14px;margin-top:6px">
          <div style="font-size:11px;font-weight:700;color:var(--success);margin-bottom:6px">🔑 <?= $isAr?'الأكواد الخاصة بك:':'Your Codes:' ?></div>
          <?php foreach(explode(',', $item['codes']) as $code): ?>
          <div style="font-family:monospace;font-size:13px;font-weight:700;color:var(--text);background:rgba(0,0,0,.3);padding:6px 12px;border-radius:7px;margin-bottom:4px;cursor:pointer;display:flex;align-items:center;justify-content:space-between" onclick="navigator.clipboard?.writeText('<?= htmlspecialchars(trim($code)) ?>');this.style.borderColor='var(--success)'">
            <span><?= htmlspecialchars(trim($code)) ?></span>
            <span style="font-size:10px;color:var(--muted)">📋</span>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>
  <?php endif; ?>
</div>
</div></div>