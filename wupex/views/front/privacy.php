<?php
$isAr = Lang::isRtl();
$pageRow = Database::fetch("SELECT * FROM ".DB_PREFIX."pages WHERE slug='privacy' AND status=1");
?>
<div style="padding:20px;max-width:760px">
  <h2 style="margin-bottom:20px">🛡️ <?= $isAr?'سياسة الخصوصية':'Privacy Policy' ?></h2>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:24px;font-size:14px;line-height:2;color:var(--muted)">
    <?php if($pageRow): echo $isAr?$pageRow['content_ar']:$pageRow['content_en']; ?>
    <?php else: ?>
    <h3 style="color:var(--text);margin-bottom:12px"><?= $isAr?'جمع البيانات':'Data Collection' ?></h3>
    <p style="margin-bottom:12px"><?= $isAr?'نجمع البيانات الضرورية فقط لتشغيل المتجر وتقديم الخدمة.':'We collect only necessary data to operate the store and provide services.' ?></p>
    <h3 style="color:var(--text);margin-bottom:12px"><?= $isAr?'حماية البيانات':'Data Protection' ?></h3>
    <p><?= $isAr?'نحمي بياناتك بأحدث تقنيات التشفير ولا نشاركها مع أطراف ثالثة.':'We protect your data with latest encryption and never share it with third parties.' ?></p>
    <?php endif; ?>
  </div>
</div>
