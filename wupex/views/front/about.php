<?php
$isAr = Lang::isRtl();
$S    = Setting::all();
$lang = Lang::current();
$pageRow = Database::fetch("SELECT * FROM ".DB_PREFIX."pages WHERE slug='about' AND status=1");
?>
<div class="page-container"><div class="page-container-inner">
  <h2 style="margin-bottom:16px">ℹ️ <?= $isAr?'من نحن':'About Us' ?></h2>
  <?php if($pageRow): ?>
  <div style="font-size:14px;color:var(--muted);line-height:2;background:var(--card);border-radius:14px;padding:24px;border:1px solid var(--border)">
    <?= $isAr ? ($pageRow['content_ar']??'') : ($pageRow['content_en']??'') ?>
  </div>
  <?php else: ?>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:24px;text-align:center">
    <div style="font-size:56px;margin-bottom:14px">🛒</div>
    <div style="font-size:22px;font-weight:900;margin-bottom:10px"><?= htmlspecialchars($S["store_name_$lang"]??'ووبيكس') ?></div>
    <div style="font-size:14px;color:var(--muted);line-height:1.9"><?= htmlspecialchars($S["store_tagline_$lang"]??'') ?></div>
    <div style="display:flex;justify-content:center;gap:20px;margin-top:20px;flex-wrap:wrap">
      <?php $feats = $isAr
        ? ['⚡ تسليم فوري','🔒 آمن وموثوق','💳 دفع سهل','🎮 منتجات متنوعة','💰 أسعار تنافسية','🌟 دعم 24/7']
        : ['⚡ Instant Delivery','🔒 Secure & Trusted','💳 Easy Payment','🎮 Variety of Products','💰 Competitive Prices','🌟 24/7 Support'];
      foreach($feats as $f): ?>
      <div style="background:var(--card2);border:1px solid var(--border);border-radius:10px;padding:10px 16px;font-size:13px;font-weight:700"><?= $f ?></div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
</div></div>