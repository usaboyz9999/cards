<?php $lang=Lang::current();$isAr=Lang::isRtl();$t=fn($k)=>Lang::get($k);$S=Setting::all();?>
<div class="auth-wrap">
  <div class="auth-card" style="max-width:500px">
    <div class="auth-logo">
      <div style="width:56px;height:56px;background:linear-gradient(135deg,var(--primary),var(--accent));border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px;margin:0 auto 14px;color:#fff;font-weight:900">W</div>
      <div class="auth-title"><?= $t('register') ?></div>
      <div class="auth-sub"><?= htmlspecialchars($S["store_name_$lang"]??'ووبيكس') ?></div>
    </div>
    <?php require VIEWS_PATH.'/partials/flash.php'; ?>
    <form method="POST">
      <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
      <input type="hidden" name="action" value="register">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div class="fg"><label><?= $isAr?'الاسم الكامل':'Full Name' ?> *</label><input type="text" name="name" required></div>
        <div class="fg"><label><?= $isAr?'اسم المستخدم':'Username' ?></label><input type="text" name="username"></div>
      </div>
      <div class="fg"><label><?= $isAr?'البريد الإلكتروني':'Email' ?> *</label><input type="email" name="email" required></div>
      <div class="fg"><label><?= $isAr?'الهاتف':'Phone' ?></label><input type="text" name="phone"></div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div class="fg"><label><?= $isAr?'كلمة المرور':'Password' ?> *</label><input type="password" name="password" required minlength="8"></div>
        <div class="fg"><label><?= $isAr?'تأكيد كلمة المرور':'Confirm Password' ?> *</label><input type="password" name="confirm_password" required></div>
      </div>
      <?php if(!empty($S['referral_enabled'])): ?>
      <div class="fg"><label><?= $isAr?'كود الإحالة (اختياري)':'Referral Code (optional)' ?></label><input type="text" name="referral_code" value="<?= htmlspecialchars($_GET['ref']??'') ?>"></div>
      <?php endif; ?>
      <label class="chk-row" style="margin-bottom:16px"><input type="checkbox" name="agree" required><span><?= $isAr?'أوافق على الشروط والأحكام':'I agree to terms & conditions' ?></span></label>
      <button type="submit" class="btn btn-primary btn-full btn-lg"><?= $t('register') ?></button>
    </form>
    <div style="text-align:center;margin-top:16px;font-size:13px;color:var(--muted)">
      <?= $isAr?'لديك حساب بالفعل؟':'Already have an account?' ?>
      <a href="?page=login" style="color:var(--primary);font-weight:700"><?= $t('login') ?></a>
    </div>
  </div>
</div>
