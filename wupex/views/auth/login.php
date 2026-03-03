<?php $lang=Lang::current();$isAr=Lang::isRtl();$t=fn($k)=>Lang::get($k);$S=Setting::all();?>
<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-logo">
      <div style="width:56px;height:56px;background:linear-gradient(135deg,var(--primary),var(--accent));border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:900;color:#fff;margin:0 auto 14px">W</div>
      <div class="auth-title"><?= $t('login') ?></div>
      <div class="auth-sub"><?= htmlspecialchars($S["store_name_$lang"]??'ووبيكس') ?></div>
    </div>
    <?php require VIEWS_PATH.'/partials/flash.php'; ?>
    <form method="POST">
      <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
      <input type="hidden" name="action" value="login">
      <div class="fg"><label><?= $isAr?'البريد الإلكتروني':'Email' ?></label><input type="email" name="email" required autofocus placeholder="email@example.com"></div>
      <div class="fg"><label><?= $isAr?'كلمة المرور':'Password' ?></label><input type="password" name="password" required placeholder="••••••••"></div>
      <label class="chk-row" style="margin-bottom:14px"><input type="checkbox" name="remember"><span><?= $isAr?'تذكرني':'Remember me' ?></span></label>
      <button type="submit" class="btn btn-primary btn-full btn-lg"><?= $t('login') ?></button>
    </form>
    <div style="text-align:center;margin-top:16px;font-size:13px;color:var(--muted)">
      <?= $isAr?'ليس لديك حساب؟':'Don\'t have an account?' ?>
      <a href="?page=register" style="color:var(--primary);font-weight:700"><?= $t('register') ?></a>
    </div>
    <div style="text-align:center;margin-top:8px;font-size:12px">
      <a href="?page=forgot" style="color:var(--muted)"><?= $isAr?'نسيت كلمة المرور؟':'Forgot password?' ?></a>
    </div>
  </div>
</div>
