<?php
$isAr = Lang::isRtl();
$pageRow = Database::fetch("SELECT * FROM ".DB_PREFIX."pages WHERE slug='returns' AND status=1");
?>
<div style="padding:20px;max-width:760px">
  <h2 style="margin-bottom:20px">🔄 <?= $isAr?'سياسة الإرجاع والاستبدال':'Return & Refund Policy' ?></h2>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:24px;font-size:14px;line-height:2;color:var(--muted)">
    <?php if($pageRow): echo $isAr?$pageRow['content_ar']:$pageRow['content_en']; ?>
    <?php else: ?>
    <h3 style="color:var(--text);margin-bottom:12px"><?= $isAr?'سياستنا':'Our Policy' ?></h3>
    <p><?= $isAr?'نظراً لطبيعة المنتجات الرقمية (أكواد وخدمات رقمية)، لا يمكن استرداد المبالغ بعد تسليم الكود. في حالة وجود مشكلة في الكود، يرجى فتح تذكرة دعم خلال 24 ساعة من الشراء.':'Due to the digital nature of our products (codes and digital services), refunds are not possible after code delivery. If you have an issue with a code, please open a support ticket within 24 hours of purchase.' ?></p>
    <?php endif; ?>
  </div>
</div>
