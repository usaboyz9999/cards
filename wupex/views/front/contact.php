<?php
$isAr = Lang::isRtl();
$t    = fn($k) => Lang::get($k);
$S    = Setting::all();
?>
<div class="page-container"><div class="page-container-inner">
  <h2 style="margin-bottom:20px">📞 <?= $t('contact') ?></h2>
  <?php require VIEWS_PATH.'/partials/flash.php'; ?>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:22px">
    <?php if(!empty($S['whatsapp'])): ?>
    <a href="https://wa.me/<?= htmlspecialchars($S['whatsapp']) ?>" target="_blank" style="background:linear-gradient(135deg,#25d366,#128c7e);border-radius:14px;padding:18px;text-align:center;color:#fff;font-weight:700;font-size:14px;transition:all .2s" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
      <div style="font-size:30px;margin-bottom:6px">💬</div>WhatsApp
    </a>
    <?php endif; ?>
    <?php if(!empty($S['telegram'])): ?>
    <a href="https://t.me/<?= htmlspecialchars($S['telegram']) ?>" target="_blank" style="background:linear-gradient(135deg,#0088cc,#229ed9);border-radius:14px;padding:18px;text-align:center;color:#fff;font-weight:700;font-size:14px;transition:all .2s" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
      <div style="font-size:30px;margin-bottom:6px">✈️</div>Telegram
    </a>
    <?php endif; ?>
    <?php if(!empty($S['contact_email'])): ?>
    <a href="mailto:<?= htmlspecialchars($S['contact_email']) ?>" style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px;text-align:center;color:var(--text);font-weight:700;font-size:14px;transition:all .2s" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border)'">
      <div style="font-size:30px;margin-bottom:6px">✉️</div><?= htmlspecialchars($S['contact_email']) ?>
    </a>
    <?php endif; ?>
  </div>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:22px">
    <h3 style="margin-bottom:16px;font-size:15px"><?= $isAr?'أرسل لنا رسالة':'Send us a message' ?></h3>
    <form method="POST">
      <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
      <input type="hidden" name="action" value="contact_message">
      <div class="fg" style="margin-bottom:12px">
        <label><?= $isAr?'اسمك':'Your Name' ?></label>
        <input type="text" name="name" required value="<?= htmlspecialchars(Auth::user()['name']??'') ?>">
      </div>
      <div class="fg" style="margin-bottom:12px">
        <label>Email</label>
        <input type="email" name="email" required value="<?= htmlspecialchars(Auth::user()['email']??'') ?>">
      </div>
      <div class="fg" style="margin-bottom:12px">
        <label><?= $isAr?'الموضوع':'Subject' ?></label>
        <input type="text" name="subject" required>
      </div>
      <div class="fg" style="margin-bottom:14px">
        <label><?= $isAr?'رسالتك':'Message' ?></label>
        <textarea name="message" required rows="4" style="min-height:100px"></textarea>
      </div>
      <button type="submit" class="btn btn-primary">📤 <?= $isAr?'إرسال':'Send' ?></button>
    </form>
  </div>
</div>
</div></div>