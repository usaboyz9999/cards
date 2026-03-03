<?php
Auth::requireLogin();
$isAr = Lang::isRtl();
$S    = Setting::all();
$user = Auth::user();
$pts  = (int)($user['points']??0);
$ptsVal = (float)($S['point_value']??0.1);
$ptsEarn = (float)($S['points_per_sar']??1);
$txns = Database::fetchAll("SELECT * FROM ".DB_PREFIX."points_transactions WHERE user_id=? ORDER BY created_at DESC LIMIT 30",[Auth::id()]);
?>
<div class="page-container"><div class="page-container-inner">
  <h2 style="margin-bottom:20px">💎 <?= $isAr?'نقاط المكافآت':'Reward Points' ?></h2>

  <div style="background:linear-gradient(135deg,var(--primary),var(--accent));border-radius:16px;padding:22px;text-align:center;margin-bottom:20px">
    <div style="font-size:12px;opacity:.8;margin-bottom:6px"><?= $isAr?'نقاطك الحالية':'Your Points' ?></div>
    <div style="font-size:40px;font-weight:900"><?= number_format($pts) ?> 💎</div>
    <div style="margin-top:8px;font-size:13px;opacity:.9"><?= $isAr?'تعادل':'Worth' ?> <?= htmlspecialchars($S['currency_symbol']??'ر.س') ?><?= number_format($pts*$ptsVal,2) ?></div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px">
    <div style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:16px;text-align:center">
      <div style="font-size:24px">📈</div>
      <div style="font-size:11px;color:var(--muted);margin:6px 0"><?= $isAr?'نقطة لكل':'Points per' ?> <?= htmlspecialchars($S['currency_symbol']??'ر.س') ?></div>
      <div style="font-weight:900;font-size:18px"><?= $ptsEarn ?></div>
    </div>
    <div style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:16px;text-align:center">
      <div style="font-size:24px">💰</div>
      <div style="font-size:11px;color:var(--muted);margin:6px 0"><?= $isAr?'قيمة النقطة':'Point Value' ?></div>
      <div style="font-weight:900;font-size:18px"><?= htmlspecialchars($S['currency_symbol']??'ر.س') ?><?= $ptsVal ?></div>
    </div>
  </div>

  <?php if($txns): ?>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px">
    <h3 style="font-size:14px;font-weight:700;margin-bottom:12px">📋 <?= $isAr?'سجل النقاط':'Points History' ?></h3>
    <?php foreach($txns as $tx): ?>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.04);font-size:13px">
      <div>
        <span style="font-weight:700;color:<?= $tx['amount']>0?'var(--success)':'var(--danger)' ?>"><?= $tx['amount']>0?'+':'' ?><?= number_format($tx['amount']) ?> 💎</span>
        <span style="margin-<?= $isAr?'right':'left' ?>:8px;color:var(--muted)"><?= htmlspecialchars($tx['type']) ?></span>
      </div>
      <span style="font-size:11px;color:var(--muted)"><?= date('Y-m-d',strtotime($tx['created_at'])) ?></span>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <div style="text-align:center;padding:36px;background:var(--card);border:1px solid var(--border);border-radius:14px">
    <div style="font-size:48px;margin-bottom:12px">💎</div>
    <div style="color:var(--muted)"><?= $isAr?'لا توجد نقاط بعد. تسوق واكسب نقاطاً!':'No points yet. Shop and earn points!' ?></div>
  </div>
  <?php endif; ?>
</div>
</div></div>