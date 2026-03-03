<?php
Auth::requireLogin();
$isAr = Lang::isRtl();
$S    = Setting::all();
$user = Auth::user();
$code = $user['referral_code'] ?? '';
$refLink = Helpers::siteUrl('?page=register&ref='.$code);
$commission = (float)($S['referral_commission']??5);
$refs = Database::fetchAll("SELECT r.*,u.name as rname,u.created_at as ujoin FROM ".DB_PREFIX."referrals r LEFT JOIN ".DB_PREFIX."users u ON u.id=r.referred_id WHERE r.referrer_id=? ORDER BY r.created_at DESC LIMIT 20",[Auth::id()]);
$totalComm = array_sum(array_column($refs,'commission'));
?>
<div class="page-container"><div class="page-container-inner">
  <h2 style="margin-bottom:20px">🔗 <?= $isAr?'نظام الإحالات':'Referral System' ?></h2>

  <div style="background:linear-gradient(135deg,#10b981,#059669);border-radius:16px;padding:22px;margin-bottom:20px">
    <div style="font-size:13px;opacity:.8;margin-bottom:6px"><?= $isAr?'رابطك الخاص':'Your Referral Link' ?></div>
    <div style="display:flex;gap:8px;align-items:center">
      <input id="refLinkInput" type="text" value="<?= htmlspecialchars($refLink) ?>"
        style="flex:1;background:rgba(0,0,0,.2);border:1px solid rgba(255,255,255,.2);border-radius:8px;padding:10px 12px;color:#fff;font-size:12px;font-family:monospace"
        readonly>
      <button onclick="navigator.clipboard.writeText(document.getElementById('refLinkInput').value);this.textContent='✅'"
        style="background:rgba(255,255,255,.2);border:none;color:#fff;padding:10px 14px;border-radius:8px;cursor:pointer;font-size:13px;font-weight:700;transition:all .2s;white-space:nowrap">
        📋 <?= $isAr?'نسخ':'Copy' ?>
      </button>
    </div>
    <div style="margin-top:12px;display:flex;gap:14px;font-size:13px">
      <div style="flex:1;text-align:center;background:rgba(0,0,0,.15);border-radius:10px;padding:10px">
        <div style="font-size:22px;font-weight:900"><?= count($refs) ?></div>
        <div style="opacity:.8;font-size:11px"><?= $isAr?'أشخاص سجّلوا':'People Referred' ?></div>
      </div>
      <div style="flex:1;text-align:center;background:rgba(0,0,0,.15);border-radius:10px;padding:10px">
        <div style="font-size:22px;font-weight:900"><?= htmlspecialchars($S['currency_symbol']??'ر.س') ?><?= number_format($totalComm,2) ?></div>
        <div style="opacity:.8;font-size:11px"><?= $isAr?'إجمالي العمولات':'Total Earned' ?></div>
      </div>
      <div style="flex:1;text-align:center;background:rgba(0,0,0,.15);border-radius:10px;padding:10px">
        <div style="font-size:22px;font-weight:900"><?= $commission ?>%</div>
        <div style="opacity:.8;font-size:11px"><?= $isAr?'عمولتك':'Your Commission' ?></div>
      </div>
    </div>
  </div>

  <?php if($refs): ?>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px">
    <h3 style="font-size:14px;font-weight:700;margin-bottom:12px">👥 <?= $isAr?'أشخاص سجّلوا برابطك':'People You Referred' ?></h3>
    <?php foreach($refs as $r): ?>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.04);font-size:13px">
      <div style="font-weight:700"><?= htmlspecialchars($r['rname']??'-') ?></div>
      <div>
        <span style="color:var(--success);font-weight:700">+<?= htmlspecialchars($S['currency_symbol']??'ر.س') ?><?= number_format($r['commission'],2) ?></span>
        <span style="margin-<?= $isAr?'right':'left' ?>:8px;font-size:11px;color:var(--muted)"><?= date('Y-m-d',strtotime($r['created_at'])) ?></span>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
</div></div>