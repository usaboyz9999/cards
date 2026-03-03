<?php $isAr = isset($lang) ? $lang==='ar' : true; ?>
<div style="display:flex;align-items:center;justify-content:center;min-height:60vh;text-align:center;padding:40px">
  <div>
    <div style="font-size:80px;margin-bottom:16px">🔍</div>
    <h1 style="font-size:48px;font-weight:900;color:var(--primary);margin-bottom:8px">404</h1>
    <h2 style="font-size:20px;margin-bottom:10px"><?= $isAr?'الصفحة غير موجودة':'Page Not Found' ?></h2>
    <p style="color:var(--muted);margin-bottom:22px"><?= $isAr?'الصفحة التي تبحث عنها غير موجودة أو تم نقلها.':'The page you are looking for does not exist or has been moved.' ?></p>
    <a href="<?= Helpers::siteUrl() ?>" style="background:linear-gradient(135deg,var(--primary),var(--accent));color:#fff;padding:12px 28px;border-radius:10px;font-weight:700;font-size:14px;text-decoration:none">🏠 <?= $isAr?'العودة للرئيسية':'Back to Home' ?></a>
  </div>
</div>
