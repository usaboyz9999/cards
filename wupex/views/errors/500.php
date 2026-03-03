<?php $isAr = isset($lang) ? $lang==='ar' : true; ?>
<div style="display:flex;align-items:center;justify-content:center;min-height:60vh;text-align:center;padding:40px">
  <div>
    <div style="font-size:80px;margin-bottom:16px">💥</div>
    <h1 style="font-size:48px;font-weight:900;color:var(--danger);margin-bottom:8px">500</h1>
    <h2 style="font-size:20px;margin-bottom:10px"><?= $isAr?'خطأ في الخادم':'Server Error' ?></h2>
    <p style="color:var(--muted);margin-bottom:22px"><?= $isAr?'حدث خطأ غير متوقع. يرجى المحاولة لاحقاً.':'An unexpected error occurred. Please try again later.' ?></p>
    <a href="<?= Helpers::siteUrl() ?>" style="background:linear-gradient(135deg,var(--primary),var(--accent));color:#fff;padding:12px 28px;border-radius:10px;font-weight:700;font-size:14px;text-decoration:none">🏠 <?= $isAr?'العودة للرئيسية':'Back to Home' ?></a>
  </div>
</div>
