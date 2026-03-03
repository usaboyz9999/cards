<?php $isAr = Lang::isRtl(); ?>
<div style="padding:20px;max-width:800px">
  <h2 style="margin-bottom:20px"><?= htmlspecialchars($isAr ? ($pageRow['title_ar']??'') : ($pageRow['title_en']??'')) ?></h2>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:24px;font-size:14px;line-height:2;color:var(--muted)">
    <?= $isAr ? ($pageRow['content_ar']??'') : ($pageRow['content_en']??'') ?>
  </div>
</div>
