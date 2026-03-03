<?php
$isAr = Lang::isRtl();
$t    = fn($k) => Lang::get($k);
$faqs = Database::fetchAll("SELECT * FROM ".DB_PREFIX."faqs WHERE status=1 ORDER BY sort_order ASC");
?>
<div class="page-container"><div class="page-container-inner">
  <h2 style="margin-bottom:20px">❓ <?= $t('faq') ?></h2>
  <?php foreach($faqs as $i => $f): ?>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:12px;margin-bottom:10px;overflow:hidden">
    <button onclick="var a=this.nextElementSibling;a.style.display=a.style.display==='none'?'block':'none'"
      style="width:100%;text-align:<?= $isAr?'right':'left' ?>;padding:14px 16px;background:none;border:none;color:var(--text);font-size:14px;font-weight:700;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:space-between;gap:10px">
      <span><?= htmlspecialchars($isAr ? $f['question_ar'] : $f['question_en']) ?></span>
      <span style="color:var(--primary);flex-shrink:0">+</span>
    </button>
    <div style="display:none;padding:0 16px 14px;font-size:13px;color:var(--muted);line-height:1.8">
      <?= htmlspecialchars($isAr ? $f['answer_ar'] : $f['answer_en']) ?>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if(empty($faqs)): ?>
  <div class="empty-state"><div class="ico">❓</div><h3><?= $isAr?'لا توجد أسئلة بعد':'No FAQs yet' ?></h3></div>
  <?php endif; ?>
</div>
</div></div>