<?php
$isAr = Lang::isRtl();
$S    = Setting::all();
?>
<!-- Settings Tabs -->
<div class="stabs">
  <?php
  $tabs = [
    ['general',     $isAr?'عام':'General',       '⚙️'],
    ['appearance',  $isAr?'مظهر':'Appearance',   '🎨'],
    ['hero',        $isAr?'بانر رئيسي':'Hero',   '🦸'],
    ['ticker',      $isAr?'شريط متحرك':'Ticker', '📢'],
    ['popup',       $isAr?'نافذة منبثقة':'Popup','🎉'],
    ['payment',     $isAr?'الدفع':'Payment',      '💳'],
    ['shipping',    $isAr?'الشحن':'Shipping',     '🚚'],
    ['wallet_s',    $isAr?'المحفظة':'Wallet',     '💰'],
    ['social',      $isAr?'سوشيال':'Social',     '📱'],
    ['email',       $isAr?'البريد':'Email',        '✉️'],
    ['loyalty',     $isAr?'الولاء':'Loyalty',     '🏆'],
    ['toast',       $isAr?'الإشعارات':'Toasts',   '🔔'],
    ['footer_s',    $isAr?'تذييل الصفحة':'Footer',   '📋'],
    ['toast',       $isAr?'الإشعارات':'Toasts',        '🔔'],
    ['footer_s',    $isAr?'تذييل الصفحة':'Footer',     '📋'],
    ['maintenance', $isAr?'صيانة':'Maintenance',  '🔧'],
    ['advanced',    $isAr?'متقدم':'Advanced',     '🛠️'],
  ];
  $curTab = $_GET['tab'] ?? 'general';
  foreach($tabs as [$id,$lbl,$ic]):
  ?>
  <button class="stab <?= $curTab===$id?'active':'' ?>" data-tab="<?= $id ?>" onclick="goStab('<?= $id ?>')"><?= $ic ?> <?= $lbl ?></button>
  <?php endforeach; ?>
</div>

<form method="POST">
<input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
<input type="hidden" name="action" value="save_settings">

<!-- GENERAL -->
<div class="stab-pane <?= $curTab==='general'?'active':'' ?>" id="tab-general">
  <div class="frm-card"><h3>⚙️ <?= $isAr?'إعدادات عامة':'General Settings' ?></h3>
    <div class="grid-3">
      <div class="fg"><label><?= $isAr?'اسم المتجر عربي':'Store Name AR' ?></label><input type="text" name="store_name_ar" value="<?= htmlspecialchars($S['store_name_ar']??'ووبيكس') ?>"></div>
      <div class="fg"><label>Store Name EN</label><input type="text" name="store_name_en" value="<?= htmlspecialchars($S['store_name_en']??'Wupex') ?>"></div>
      <div class="fg"><label><?= $isAr?'العملة':'Currency' ?></label><input type="text" name="currency" value="<?= htmlspecialchars($S['currency']??'SAR') ?>"></div>
      <div class="fg"><label><?= $isAr?'رمز العملة':'Currency Symbol' ?></label><input type="text" name="currency_symbol" value="<?= htmlspecialchars($S['currency_symbol']??'ر.س') ?>"></div>
      <div class="fg"><label><?= $isAr?'اللغة الافتراضية':'Default Language' ?></label>
        <select name="default_lang"><option value="ar" <?= ($S['default_lang']??'ar')==='ar'?'selected':'' ?>>العربية</option><option value="en" <?= ($S['default_lang']??'')==='en'?'selected':'' ?>>English</option></select>
      </div>
      <div class="fg"><label><?= $isAr?'منتجات في الصف':'Products Per Row' ?></label>
        <select name="products_per_row">
          <?php foreach([4,5,6,7,8] as $n): ?><option value="<?= $n ?>" <?= ($S['products_per_row']??6)==$n?'selected':'' ?>><?= $n ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="fg"><label>Logo URL</label><input type="text" name="logo_url" value="<?= htmlspecialchars($S['logo_url']??'') ?>" placeholder="https://..."></div>
      <div class="fg"><label>Favicon URL</label><input type="text" name="favicon_url" value="<?= htmlspecialchars($S['favicon_url']??'') ?>" placeholder="https://..."></div>
      <div class="fg"><label><?= $isAr?'إيميل التواصل':'Contact Email' ?></label><input type="email" name="contact_email" value="<?= htmlspecialchars($S['contact_email']??'') ?>"></div>
    </div>
    <div style="display:flex;gap:20px;margin-top:12px;flex-wrap:wrap">
      <label class="chk-row"><input type="checkbox" name="show_prices" value="1" <?= !empty($S['show_prices'])?'checked':'' ?>><span><?= $isAr?'إظهار الأسعار':'Show Prices' ?></span></label>
      <label class="chk-row"><input type="checkbox" name="show_flags" value="1" <?= !empty($S['show_flags'])?'checked':'' ?>><span><?= $isAr?'إظهار الأعلام':'Show Flags' ?></span></label>
      <label class="chk-row"><input type="checkbox" name="registration_enabled" value="1" <?= !empty($S['registration_enabled'])?'checked':'' ?>><span><?= $isAr?'تفعيل التسجيل':'Enable Registration' ?></span></label>
      <label class="chk-row"><input type="checkbox" name="guest_checkout" value="1" <?= !empty($S['guest_checkout'])?'checked':'' ?>><span><?= $isAr?'سداد كضيف':'Guest Checkout' ?></span></label>
      <label class="chk-row"><input type="checkbox" name="reviews_enabled" value="1" <?= !empty($S['reviews_enabled'])?'checked':'' ?>><span><?= $isAr?'تفعيل التقييمات':'Enable Reviews' ?></span></label>
    </div>
  </div>
</div>

<!-- APPEARANCE -->
<div class="stab-pane <?= $curTab==='appearance'?'active':'' ?>" id="tab-appearance">
  <div class="frm-card"><h3>🎨 <?= $isAr?'الألوان':'Colors' ?></h3>
    <div class="grid-3">
      <?php $cols=[['primary_color','Primary','#7c3aed'],['secondary_color','Secondary','#f97316'],['accent_color','Accent','#ec4899'],['bg_dark','Background','#09071a'],['bg_sidebar','Sidebar','#0e0b1f'],['bg_card','Card','#14102a']]; ?>
      <?php foreach($cols as [$k,$l,$d]): $v=htmlspecialchars($S[$k]??$d); ?>
      <div class="fg"><label><?= $l ?></label><div class="cg"><input type="color" name="<?= $k ?>" value="<?= $v ?>" oninput="syncClr(this);document.documentElement.style.setProperty('--<?= str_replace(['primary_color','secondary_color','accent_color','bg_dark','bg_sidebar','bg_card'],['primary','secondary','accent','bg','sidebar','card'],str_replace('_color','',explode('_',$k,2)[0]??$k)) ?>',this.value)"><input type="text" value="<?= $v ?>" oninput="syncTxt(this)"></div></div>
      <?php endforeach; ?>
    </div>
    <h3 style="margin-top:18px">🎨 <?= $isAr?'ثيمات جاهزة':'Preset Themes' ?></h3>
    <div class="theme-presets">
      <div class="theme-chip" style="background:linear-gradient(135deg,#7c3aed,#ec4899)" onclick="applyTheme('#7c3aed','#f97316','#ec4899','#0d0a1a','#110e22','#1a1530')">💜 <?= $isAr?'بنفسجي':'Purple' ?></div>
      <div class="theme-chip" style="background:linear-gradient(135deg,#0ea5e9,#06b6d4)" onclick="applyTheme('#0ea5e9','#f97316','#06b6d4','#070d1a','#0c1422','#111d30')">💙 <?= $isAr?'أزرق':'Blue' ?></div>
      <div class="theme-chip" style="background:linear-gradient(135deg,#10b981,#059669)" onclick="applyTheme('#10b981','#f97316','#06d6a0','#071a0e','#0c2014','#112a1a')">💚 <?= $isAr?'أخضر':'Green' ?></div>
      <div class="theme-chip" style="background:linear-gradient(135deg,#ef4444,#dc2626)" onclick="applyTheme('#ef4444','#f59e0b','#ec4899','#1a0707','#220c0c','#2a1111')">❤️ <?= $isAr?'أحمر':'Red' ?></div>
      <div class="theme-chip" style="background:linear-gradient(135deg,#f59e0b,#f97316)" onclick="applyTheme('#f59e0b','#7c3aed','#f97316','#1a1207','#22180c','#2a2011')">🧡 <?= $isAr?'ذهبي':'Gold' ?></div>
      <div class="theme-chip" style="background:linear-gradient(135deg,#111827,#1f2937)" onclick="applyTheme('#6366f1','#f97316','#8b5cf6','#111827','#1f2937','#273349')">🌑 <?= $isAr?'ليلي':'Dark' ?></div>
    </div>
  </div>
</div>

<!-- HERO -->
<div class="stab-pane <?= $curTab==='hero'?'active':'' ?>" id="tab-hero">
  <div class="frm-card"><h3>🦸 Hero Banner</h3>
    <div class="grid-3">
      <div class="fg"><label><?= $isAr?'نص يسار':'Left Text' ?></label><input type="text" name="hero_text_left" value="<?= htmlspecialchars($S['hero_text_left']??'أسرع') ?>"></div>
      <div class="fg"><label><?= $isAr?'نص يمين':'Right Text' ?></label><input type="text" name="hero_text_right" value="<?= htmlspecialchars($S['hero_text_right']??'سلس') ?>"></div>
      <div class="fg"><label><?= $isAr?'الشخصية (إيموجي)':'Character (emoji)' ?></label><input type="text" name="hero_character" value="<?= htmlspecialchars($S['hero_character']??'🧑‍💻') ?>"></div>
      <div class="fg"><label>Subtitle AR</label><input type="text" name="hero_subtext_ar" value="<?= htmlspecialchars($S['hero_subtext_ar']??'') ?>"></div>
      <div class="fg"><label>Subtitle EN</label><input type="text" name="hero_subtext_en" value="<?= htmlspecialchars($S['hero_subtext_en']??'') ?>"></div>
      <div class="fg"></div>
      <div class="fg"><label>BG Color 1</label><div class="cg"><input type="color" name="hero_bg_start" value="<?= htmlspecialchars($S['hero_bg_start']??'#4c1d95') ?>" oninput="syncClr(this)"><input type="text" value="<?= htmlspecialchars($S['hero_bg_start']??'#4c1d95') ?>" oninput="syncTxt(this)"></div></div>
      <div class="fg"><label>BG Color 2</label><div class="cg"><input type="color" name="hero_bg_mid" value="<?= htmlspecialchars($S['hero_bg_mid']??'#7c3aed') ?>" oninput="syncClr(this)"><input type="text" value="<?= htmlspecialchars($S['hero_bg_mid']??'#7c3aed') ?>" oninput="syncTxt(this)"></div></div>
      <div class="fg"><label>BG Color 3</label><div class="cg"><input type="color" name="hero_bg_end" value="<?= htmlspecialchars($S['hero_bg_end']??'#ec4899') ?>" oninput="syncClr(this)"><input type="text" value="<?= htmlspecialchars($S['hero_bg_end']??'#ec4899') ?>" oninput="syncTxt(this)"></div></div>
    </div>
  </div>
</div>

<!-- TICKER -->
<div class="stab-pane <?= $curTab==='ticker'?'active':'' ?>" id="tab-ticker">
  <div class="frm-card"><h3>📢 <?= $isAr?'الشريط المتحرك':'Ticker' ?></h3>
    <div style="margin-bottom:12px"><label class="chk-row"><input type="checkbox" name="ticker_enabled" value="1" <?= !empty($S['ticker_enabled'])?'checked':'' ?>><span><?= $isAr?'تفعيل الشريط':'Enable Ticker' ?></span></label></div>
    <div class="grid-2">
      <div class="fg"><label>Text AR</label><input type="text" name="ticker_text_ar" value="<?= htmlspecialchars($S['ticker_text_ar']??'') ?>"></div>
      <div class="fg"><label>Text EN</label><input type="text" name="ticker_text_en" value="<?= htmlspecialchars($S['ticker_text_en']??'') ?>"></div>
      <div class="fg"><label><?= $isAr?'السرعة (ثانية)':'Speed (seconds)' ?></label><input type="number" name="ticker_speed" min="5" max="120" value="<?= intval($S['ticker_speed']??30) ?>"></div>
      <div class="fg"><label><?= $isAr?'لون الخلفية':'BG Color' ?></label><div class="cg"><input type="color" name="ticker_bg" value="<?= htmlspecialchars($S['ticker_bg']??'#7c3aed') ?>" oninput="syncClr(this)"><input type="text" value="<?= htmlspecialchars($S['ticker_bg']??'#7c3aed') ?>" oninput="syncTxt(this)"></div></div>
      <div class="fg"><label><?= $isAr?'اتجاه الحركة':'Direction' ?></label>
        <select name="ticker_direction"><option value="right" <?= ($S['ticker_direction']??'right')==='right'?'selected':'' ?>><?= $isAr?'يمين لليسار':'Right to Left' ?></option><option value="left" <?= ($S['ticker_direction']??'')==='left'?'selected':'' ?>><?= $isAr?'يسار لليمين':'Left to Right' ?></option></select>
      </div>
    </div>
    <label class="chk-row" style="margin-top:8px"><input type="checkbox" name="ticker_pause_hover" value="1" <?= !empty($S['ticker_pause_hover'])?'checked':'' ?>><span><?= $isAr?'إيقاف عند المرور':'Pause on Hover' ?></span></label>
  </div>
</div>

<!-- POPUP -->
<div class="stab-pane <?= $curTab==='popup'?'active':'' ?>" id="tab-popup">
  <div class="frm-card"><h3>🎉 <?= $isAr?'النافذة المنبثقة':'Popup' ?></h3>
    <div style="margin-bottom:12px"><label class="chk-row"><input type="checkbox" name="popup_enabled" value="1" <?= !empty($S['popup_enabled'])?'checked':'' ?>><span><?= $isAr?'تفعيل النافذة':'Enable Popup' ?></span></label></div>
    <div class="grid-2">
      <div class="fg"><label>Title AR</label><input type="text" name="popup_title_ar" value="<?= htmlspecialchars($S['popup_title_ar']??'') ?>"></div>
      <div class="fg"><label>Title EN</label><input type="text" name="popup_title_en" value="<?= htmlspecialchars($S['popup_title_en']??'') ?>"></div>
      <div class="fg"><label>Message AR</label><textarea name="popup_message_ar"><?= htmlspecialchars($S['popup_message_ar']??'') ?></textarea></div>
      <div class="fg"><label>Message EN</label><textarea name="popup_message_en"><?= htmlspecialchars($S['popup_message_en']??'') ?></textarea></div>
      <div class="fg"><label>Button AR</label><input type="text" name="popup_btn_ar" value="<?= htmlspecialchars($S['popup_btn_ar']??'ابدأ') ?>"></div>
      <div class="fg"><label>Button EN</label><input type="text" name="popup_btn_en" value="<?= htmlspecialchars($S['popup_btn_en']??'Start') ?>"></div>
      <div class="fg"><label>Emoji</label><input type="text" name="popup_emoji" value="<?= htmlspecialchars($S['popup_emoji']??'🎉') ?>"></div>
      <div class="fg"><label><?= $isAr?'التأخير (ثانية)':'Delay (seconds)' ?></label><input type="number" name="popup_delay" min="0" max="30" value="<?= intval($S['popup_delay']??2) ?>"></div>
      <div class="fg"><label>BG Color</label><div class="cg"><input type="color" name="popup_bg" value="<?= htmlspecialchars($S['popup_bg']??'#14102a') ?>" oninput="syncClr(this)"><input type="text" value="<?= htmlspecialchars($S['popup_bg']??'#14102a') ?>" oninput="syncTxt(this)"></div></div>
    </div>
    <label class="chk-row" style="margin-top:8px"><input type="checkbox" name="popup_show_once" value="1" <?= !empty($S['popup_show_once'])?'checked':'' ?>><span><?= $isAr?'عرض مرة واحدة فقط':'Show Once Per Session' ?></span></label>
  </div>
</div>

<!-- PAYMENT -->
<div class="stab-pane <?= $curTab==='payment'?'active':'' ?>" id="tab-payment">
  <div class="frm-card"><h3>💳 <?= $isAr?'طرق الدفع':'Payment Methods' ?></h3>
    <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:16px">
      <label class="chk-row"><input type="checkbox" name="payment_wallet" value="1" <?= !empty($S['payment_wallet'])?'checked':'' ?>><span>💰 <?= $isAr?'المحفظة الإلكترونية':'E-Wallet' ?></span></label>
      <label class="chk-row"><input type="checkbox" name="payment_bank" value="1" <?= !empty($S['payment_bank'])?'checked':'' ?>><span>🏦 <?= $isAr?'التحويل البنكي':'Bank Transfer' ?></span></label>
      <label class="chk-row"><input type="checkbox" name="payment_card" value="1" <?= !empty($S['payment_card'])?'checked':'' ?>><span>💳 <?= $isAr?'بطاقة ائتمان':'Credit Card' ?></span></label>
      <label class="chk-row"><input type="checkbox" name="payment_paypal" value="1" <?= !empty($S['payment_paypal'])?'checked':'' ?>><span>🅿️ PayPal</span></label>
    </div>
    <div class="grid-2">
      <div class="fg"><label><?= $isAr?'معلومات البنك':'Bank Info' ?></label><textarea name="bank_info"><?= htmlspecialchars($S['bank_info']??'') ?></textarea></div>
      <div></div>
      <label class="chk-row"><input type="checkbox" name="tax_enabled" value="1" <?= !empty($S['tax_enabled'])?'checked':'' ?>><span><?= $isAr?'تفعيل الضريبة':'Enable Tax' ?></span></label>
      <div class="fg"><label><?= $isAr?'نسبة الضريبة %':'Tax % ' ?></label><input type="number" name="tax_percent" min="0" max="100" value="<?= intval($S['tax_percent']??15) ?>"></div>
    </div>
  </div>
</div>

<!-- SHIPPING -->
<div class="stab-pane <?= $curTab==='shipping'?'active':'' ?>" id="tab-shipping">
  <div class="frm-card"><h3>🚚 <?= $isAr?'الشحن':'Shipping' ?></h3>
    <label class="chk-row" style="margin-bottom:14px"><input type="checkbox" name="shipping_enabled" value="1" <?= !empty($S['shipping_enabled'])?'checked':'' ?>><span><?= $isAr?'تفعيل الشحن':'Enable Shipping' ?></span></label>
    <div class="grid-3">
      <div class="fg"><label><?= $isAr?'تكلفة الشحن':'Shipping Cost' ?></label><input type="number" name="shipping_cost" step="0.01" value="<?= htmlspecialchars($S['shipping_cost']??'15') ?>"></div>
      <div class="fg"><label><?= $isAr?'شحن مجاني عند':'Free Shipping Above' ?></label><input type="number" name="shipping_free_above" step="0.01" value="<?= htmlspecialchars($S['shipping_free_above']??'200') ?>"></div>
      <div class="fg"><label><?= $isAr?'ملاحظة الشحن':'Shipping Note' ?></label><input type="text" name="shipping_note" value="<?= htmlspecialchars($S['shipping_note']??'') ?>"></div>
    </div>
  </div>
</div>

<!-- WALLET SETTINGS -->
<div class="stab-pane <?= $curTab==='wallet_s'?'active':'' ?>" id="tab-wallet_s">
  <div class="frm-card"><h3>💰 <?= $isAr?'إعدادات المحفظة':'Wallet Settings' ?></h3>
    <label class="chk-row" style="margin-bottom:14px"><input type="checkbox" name="wallet_enabled" value="1" <?= !empty($S['wallet_enabled'])?'checked':'' ?>><span><?= $isAr?'تفعيل المحفظة':'Enable Wallet' ?></span></label>
    <div class="grid-3">
      <div class="fg"><label><?= $isAr?'الحد الأدنى للإيداع':'Min Deposit' ?></label><input type="number" name="wallet_min_deposit" step="0.01" value="<?= htmlspecialchars($S['wallet_min_deposit']??'10') ?>"></div>
      <div class="fg"><label><?= $isAr?'الحد الأقصى للإيداع':'Max Deposit' ?></label><input type="number" name="wallet_max_deposit" step="0.01" value="<?= htmlspecialchars($S['wallet_max_deposit']??'5000') ?>"></div>
      <div class="fg"><label><?= $isAr?'نسبة المكافأة %':'Bonus %' ?></label><input type="number" name="wallet_bonus" min="0" max="100" value="<?= htmlspecialchars($S['wallet_bonus']??'0') ?>"></div>
    </div>
    <div class="fg"><label><?= $isAr?'شروط المحفظة':'Wallet Terms' ?></label><textarea name="wallet_terms"><?= htmlspecialchars($S['wallet_terms']??'') ?></textarea></div>
  </div>
</div>

<!-- SOCIAL -->
<div class="stab-pane <?= $curTab==='social'?'active':'' ?>" id="tab-social">
  <div class="frm-card"><h3>📱 <?= $isAr?'التواصل الاجتماعي':'Social Media' ?></h3>
    <div class="grid-2">
      <?php $socials=[['whatsapp','📱 WhatsApp'],['telegram','✈️ Telegram'],['snapchat','👻 Snapchat'],['instagram','📸 Instagram'],['twitter','🐦 Twitter/X'],['facebook','📘 Facebook'],['tiktok','🎵 TikTok'],['youtube','▶️ YouTube']]; ?>
      <?php foreach($socials as [$k,$l]): ?>
      <div class="fg"><label><?= $l ?></label><input type="text" name="<?= $k ?>" value="<?= htmlspecialchars($S[$k]??'') ?>" placeholder="@username or number"></div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- EMAIL -->
<div class="stab-pane <?= $curTab==='email'?'active':'' ?>" id="tab-email">
  <div class="frm-card"><h3>✉️ <?= $isAr?'إعدادات البريد':'Email Settings' ?></h3>
    <div style="display:flex;align-items:center;justify-content:space-between;background:<?= !empty($S['mail_enabled'])?'rgba(16,185,129,.08)':'rgba(239,68,68,.06)' ?>;border:1px solid <?= !empty($S['mail_enabled'])?'rgba(16,185,129,.25)':'rgba(239,68,68,.2)' ?>;border-radius:10px;padding:12px 16px;margin-bottom:16px">
      <div>
        <div style="font-weight:700;font-size:13px"><?= $isAr?'حالة البريد الإلكتروني':'Email Status' ?></div>
        <div style="font-size:11px;color:var(--muted);margin-top:3px"><?= $isAr?'تفعيل أو تعطيل إرسال البريد الإلكتروني':'Enable or disable email sending' ?></div>
      </div>
      <label style="position:relative;display:inline-flex;align-items:center;cursor:pointer;gap:10px">
        <span style="font-size:12px;color:<?= !empty($S['mail_enabled'])?'var(--success)':'var(--danger)' ?>;font-weight:700"><?= !empty($S['mail_enabled'])?($isAr?'مفعّل':'Enabled'):($isAr?'معطّل':'Disabled') ?></span>
        <input type="checkbox" name="mail_enabled" value="1" <?= !empty($S['mail_enabled'])?'checked':'' ?> style="width:18px;height:18px;accent-color:var(--primary)">
      </label>
    </div>
    <div class="grid-2">
      <div class="fg"><label>SMTP Host</label><input type="text" name="mail_host" value="<?= htmlspecialchars($S['mail_host']??'') ?>"></div>
      <div class="fg"><label>SMTP Port</label><input type="number" name="mail_port" value="<?= htmlspecialchars($S['mail_port']??'587') ?>"></div>
      <div class="fg"><label>Username</label><input type="text" name="mail_user" value="<?= htmlspecialchars($S['mail_user']??'') ?>"></div>
      <div class="fg"><label>Password</label><input type="password" name="mail_pass" placeholder="••••••••"></div>
      <div class="fg"><label>From Email</label><input type="email" name="mail_from" value="<?= htmlspecialchars($S['mail_from']??'') ?>"></div>
      <div class="fg"><label>From Name</label><input type="text" name="mail_from_name" value="<?= htmlspecialchars($S['mail_from_name']??'') ?>"></div>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:14px;margin-top:10px">
      <label class="chk-row"><input type="checkbox" name="notify_new_order" value="1" <?= !empty($S['notify_new_order'])?'checked':'' ?>><span><?= $isAr?'إشعار طلب جديد':'Notify New Order' ?></span></label>
      <label class="chk-row"><input type="checkbox" name="notify_new_user" value="1" <?= !empty($S['notify_new_user'])?'checked':'' ?>><span><?= $isAr?'إشعار تسجيل جديد':'Notify New User' ?></span></label>
    </div>
  </div>
</div>

<!-- LOYALTY -->
<div class="stab-pane <?= $curTab==='loyalty'?'active':'' ?>" id="tab-loyalty">
  <div class="frm-card"><h3>💎 <?= $isAr?'النقاط والمكافآت':'Points & Rewards' ?></h3>
    <div style="display:flex;gap:14px;flex-wrap:wrap;margin-bottom:14px">
      <label class="chk-row"><input type="checkbox" name="points_enabled" value="1" <?= !empty($S['points_enabled'])?'checked':'' ?>><span><?= $isAr?'تفعيل النقاط':'Enable Points' ?></span></label>
      <label class="chk-row"><input type="checkbox" name="referral_enabled" value="1" <?= !empty($S['referral_enabled'])?'checked':'' ?>><span><?= $isAr?'تفعيل الإحالات':'Enable Referrals' ?></span></label>
    </div>
    <div class="grid-3">
      <div class="fg"><label><?= $isAr?'نقاط لكل ريال':'Points Per SAR' ?></label><input type="number" name="points_per_sar" min="0" step="0.1" value="<?= htmlspecialchars($S['points_per_sar']??'1') ?>"></div>
      <div class="fg"><label><?= $isAr?'قيمة النقطة (ريال)':'Point Value (SAR)' ?></label><input type="number" name="points_redeem_rate" min="0" step="0.001" value="<?= htmlspecialchars($S['points_redeem_rate']??'0.01') ?>"></div>
      <div class="fg"><label><?= $isAr?'عمولة الإحالة %':'Referral Commission %' ?></label><input type="number" name="referral_commission" min="0" max="50" value="<?= htmlspecialchars($S['referral_commission']??'5') ?>"></div>
    </div>
  </div>
</div>

<!-- MAINTENANCE -->

<!-- ── Toast Settings Tab ── -->
<div class="stab-pane <?= $curTab==='toast'?'active':'' ?>" id="tab-toast">
  <div class="frm-card"><h3>🔔 <?= $isAr?'إعدادات الإشعارات (Toast)':'Toast Notifications' ?></h3>
    <div class="grid-2">
      <div class="fg full">
        <label class="chk-row">
          <input type="checkbox" name="toast_enabled" value="1" <?= !empty($S['toast_enabled'])?'checked':'' ?>>
          <span><?= $isAr?'تفعيل الإشعارات':'Enable Toast Notifications' ?></span>
        </label>
      </div>
      <div class="fg"><label><?= $isAr?'مكان الإشعار':'Position' ?></label>
        <select name="toast_position">
          <?php foreach(['bottom-right'=>$isAr?'أسفل يمين':'Bottom Right','bottom-left'=>$isAr?'أسفل يسار':'Bottom Left','top-right'=>$isAr?'أعلى يمين':'Top Right','top-left'=>$isAr?'أعلى يسار':'Top Left','top-center'=>$isAr?'أعلى وسط':'Top Center','bottom-center'=>$isAr?'أسفل وسط':'Bottom Center'] as $v=>$l): ?>
          <option value="<?= $v ?>" <?= ($S['toast_position']??'bottom-right')===$v?'selected':'' ?>><?= $l ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="fg"><label><?= $isAr?'مدة الظهور (مللي ثانية)':'Duration (ms)' ?></label>
        <input type="number" name="toast_duration" value="<?= intval($S['toast_duration']??1000) ?>" min="500" max="10000" step="100">
        <small style="color:var(--muted)"><?= $isAr?'1000 = ثانية واحدة':'1000 = 1 second' ?></small>
      </div>
      <div class="fg">
        <label class="chk-row">
          <input type="checkbox" name="toast_autohide" value="1" <?= ($S['toast_autohide']??'1')?'checked':'' ?>>
          <span><?= $isAr?'إخفاء تلقائي':'Auto Hide' ?></span>
        </label>
      </div>
    </div>
  </div>
</div>

<!-- ── Footer Settings Tab ── -->
<div class="stab-pane <?= $curTab==='footer_s'?'active':'' ?>" id="tab-footer_s">
  <div class="frm-card"><h3>📋 <?= $isAr?'تصميم تذييل الصفحة':'Footer Design' ?></h3>
    <div class="grid-2">
      <div class="fg"><label><?= $isAr?'لون خلفية Footer':'Footer Background' ?></label>
        <div class="cg"><input type="color" name="footer_bg" value="<?= htmlspecialchars($S['footer_bg']??'#0f0f1a') ?>" oninput="syncClr(this)"><input type="text" value="<?= htmlspecialchars($S['footer_bg']??'#0f0f1a') ?>" oninput="syncTxt(this)"></div>
      </div>
      <div class="fg"><label><?= $isAr?'لون النص':'Text Color' ?></label>
        <div class="cg"><input type="color" name="footer_color" value="<?= htmlspecialchars($S['footer_color']??'#a1a1aa') ?>" oninput="syncClr(this)"><input type="text" value="<?= htmlspecialchars($S['footer_color']??'#a1a1aa') ?>" oninput="syncTxt(this)"></div>
      </div>
      <div class="fg"><label><?= $isAr?'حجم الخط (px)':'Font Size (px)' ?></label>
        <input type="number" name="footer_font_size" value="<?= intval($S['footer_font_size']??13) ?>" min="10" max="20">
      </div>
      <div class="fg"><label><?= $isAr?'الارتفاع (padding py px)':'Padding (py px)' ?></label>
        <input type="text" name="footer_padding" value="<?= htmlspecialchars($S['footer_padding']??'20px 28px') ?>" placeholder="20px 28px">
      </div>
      <div class="fg"><label><?= $isAr?'اتجاه الكتابة':'Text Direction' ?></label>
        <select name="footer_direction">
          <option value="auto" <?= ($S['footer_direction']??'auto')==='auto'?'selected':'' ?>><?= $isAr?'تلقائي (من الإعدادات)':'Auto (from settings)' ?></option>
          <option value="rtl"  <?= ($S['footer_direction']??'')==='rtl'?'selected':'' ?>>RTL <?= $isAr?'(يمين لليسار)':'' ?></option>
          <option value="ltr"  <?= ($S['footer_direction']??'')==='ltr'?'selected':'' ?>>LTR <?= $isAr?'(يسار لليمين)':'' ?></option>
        </select>
      </div>
      <div class="fg"><label><?= $isAr?'مكان شعار المتجر':'Logo Position' ?></label>
        <select name="footer_logo_position">
          <option value="col1" <?= ($S['footer_logo_position']??'col1')==='col1'?'selected':'' ?>><?= $isAr?'العمود الأول':'Column 1' ?></option>
          <option value="col2" <?= ($S['footer_logo_position']??'')==='col2'?'selected':'' ?>><?= $isAr?'العمود الثاني':'Column 2' ?></option>
          <option value="col3" <?= ($S['footer_logo_position']??'')==='col3'?'selected':'' ?>><?= $isAr?'العمود الثالث':'Column 3' ?></option>
          <option value="col4" <?= ($S['footer_logo_position']??'')==='col4'?'selected':'' ?>><?= $isAr?'العمود الرابع':'Column 4' ?></option>
        </select>
      </div>
      <div class="fg"><label><?= $isAr?'مكان حقوق الملكية':'Copyright Position' ?></label>
        <select name="footer_copyright_position">
          <option value="center" <?= ($S['footer_copyright_position']??'center')==='center'?'selected':'' ?>><?= $isAr?'وسط':'Center' ?></option>
          <option value="right"  <?= ($S['footer_copyright_position']??'')==='right'?'selected':'' ?>><?= $isAr?'يمين':'Right' ?></option>
          <option value="left"   <?= ($S['footer_copyright_position']??'')==='left'?'selected':'' ?>><?= $isAr?'يسار':'Left' ?></option>
        </select>
      </div>
      <div class="fg"><label><?= $isAr?'نص حقوق الملكية':'Copyright Text' ?></label>
        <input type="text" name="footer_copyright" value="<?= htmlspecialchars($S['footer_copyright']??'') ?>" placeholder="<?= $isAr?'© 2025 متجري':'© 2025 My Store' ?>">
      </div>
    </div>
  </div>

  <!-- Footer Links Manager -->
  <div class="frm-card"><h3>🔗 <?= $isAr?'روابط تذييل الصفحة':'Footer Links' ?></h3>
    <p style="font-size:12px;color:var(--muted);margin-bottom:16px"><?= $isAr?'يمكنك إضافة أو تعديل أو إعادة ترتيب روابط Footer. تحديد العمود (1-4) يحدد موقع الرابط.':'Add, edit, or reorder footer links. Column (1-4) determines placement.' ?></p>
    <?php
    $footerLinks = json_decode($S['footer_links']??'[]', true) ?: [
      ['col'=>1,'label_ar'=>'الرئيسية','label_en'=>'Home','url'=>'/'],
      ['col'=>1,'label_ar'=>'المنتجات','label_en'=>'Products','url'=>'?page=shop'],
      ['col'=>2,'label_ar'=>'الأسئلة الشائعة','label_en'=>'FAQ','url'=>'?page=faq'],
      ['col'=>2,'label_ar'=>'تواصل معنا','label_en'=>'Contact','url'=>'?page=contact'],
      ['col'=>3,'label_ar'=>'من نحن','label_en'=>'About','url'=>'?page=about'],
      ['col'=>3,'label_ar'=>'سياسة الخصوصية','label_en'=>'Privacy','url'=>'?page=privacy'],
      ['col'=>4,'label_ar'=>'سياسة الإرجاع','label_en'=>'Returns','url'=>'?page=returns'],
    ];
    ?>
    <div id="footerLinksList">
      <?php foreach($footerLinks as $i=>$lnk): ?>
      <div class="footer-link-row" style="display:grid;grid-template-columns:40px 1fr 1fr 120px 40px;gap:8px;align-items:center;margin-bottom:8px">
        <span style="text-align:center;color:var(--muted);cursor:grab">⠿</span>
        <input type="text" name="fl_label_ar[]" value="<?= htmlspecialchars($lnk['label_ar']??'') ?>" placeholder="<?= $isAr?'اسم (عربي)':'Label AR' ?>" style="background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:7px 10px;color:inherit;font-family:inherit;font-size:12px;width:100%">
        <input type="text" name="fl_url[]" value="<?= htmlspecialchars($lnk['url']??'') ?>" placeholder="URL or ?page=..." style="background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:7px 10px;color:inherit;font-family:inherit;font-size:12px;width:100%">
        <select name="fl_col[]" style="background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:7px 10px;color:inherit;font-family:inherit;font-size:12px">
          <?php for($c=1;$c<=4;$c++): ?><option value="<?= $c ?>" <?= ($lnk['col']??1)==$c?'selected':'' ?>><?= $isAr?'عمود':'Col' ?> <?= $c ?></option><?php endfor; ?>
        </select>
        <button type="button" onclick="this.closest('.footer-link-row').remove()" style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:var(--danger);width:34px;height:34px;border-radius:8px;cursor:pointer">×</button>
      </div>
      <?php endforeach; ?>
    </div>
    <button type="button" onclick="addFooterLink()" class="btn btn-sm btn-secondary" style="margin-top:8px">+ <?= $isAr?'إضافة رابط':'Add Link' ?></button>
    <input type="hidden" name="footer_links" id="footerLinksJSON" value="">
  </div>
</div>

<div class="stab-pane <?= $curTab==='maintenance'?'active':'' ?>" id="tab-maintenance">
  <div class="frm-card"><h3>🔧 <?= $isAr?'وضع الصيانة':'Maintenance Mode' ?></h3>
    <div class="alert alert-warning">⚠️ <?= $isAr?'عند تفعيل الصيانة، المدير فقط يمكنه رؤية المتجر مع شريط تحذيري.':'When enabled, only admins can browse the store with a warning bar.' ?></div>
    <label class="chk-row" style="margin-bottom:14px"><input type="checkbox" name="maintenance_mode" value="1" <?= !empty($S['maintenance_mode'])?'checked':'' ?>><span><?= $isAr?'تفعيل وضع الصيانة':'Enable Maintenance Mode' ?></span></label>
    <div class="grid-2">
      <div class="fg"><label>Message AR</label><textarea name="maintenance_msg_ar"><?= htmlspecialchars($S['maintenance_msg_ar']??'') ?></textarea></div>
      <div class="fg"><label>Message EN</label><textarea name="maintenance_msg_en"><?= htmlspecialchars($S['maintenance_msg_en']??'') ?></textarea></div>
    </div>
  </div>
</div>

<!-- ADVANCED -->
<div class="stab-pane <?= $curTab==='advanced'?'active':'' ?>" id="tab-advanced">
  <div class="frm-card"><h3>🛠️ <?= $isAr?'إعدادات متقدمة':'Advanced Settings' ?></h3>
    <div class="fg"><label>Custom CSS</label>
    <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:8px">
      <button type="button" class="btn btn-sm btn-secondary" onclick="injectCSS('/* Dark compact mode */\n.prod-card{border-radius:8px!important}\n.nav-item{border-radius:6px!important}')">🌑 Compact</button>
      <button type="button" class="btn btn-sm btn-secondary" onclick="injectCSS('/* Rounded cards */\n:root{--r:20px}\n.prod-card,.frm-card,.stat-c{border-radius:20px!important}')">⭕ Rounded</button>
      <button type="button" class="btn btn-sm btn-secondary" onclick="injectCSS('/* Large text */\nbody{font-size:16px}\n.nav-lbl{font-size:14px}')">🔠 Large Text</button>
      <button type="button" class="btn btn-sm btn-secondary" onclick="injectCSS('/* Hide badges */\n.nav-badge{display:none!important}')">🚫 No Badges</button>
      <button type="button" class="btn btn-sm btn-danger" onclick="document.getElementById('customCss').value=''">🗑️ Clear</button>
    </div>
    <textarea id="customCss" name="custom_css" style="min-height:200px;font-family:monospace;font-size:12px;tab-size:2"><?= htmlspecialchars($S['custom_css']??'') ?></textarea>
    <span style="font-size:11px;color:var(--muted)"><?= $isAr?'يُطبَّق على الواجهة الأمامية فقط':'Applied to frontend only' ?></span>
    </div>
    <script>function injectCSS(css){ var ta=document.getElementById('customCss'); ta.value=(ta.value?ta.value+'\n':'')+css; ta.focus(); }</script>
    <div class="grid-2" style="margin-top:12px">
      <div class="fg"><label>Meta Description AR</label><textarea name="meta_description_ar"><?= htmlspecialchars($S['meta_description_ar']??'') ?></textarea></div>
      <div class="fg"><label>Meta Description EN</label><textarea name="meta_description_en"><?= htmlspecialchars($S['meta_description_en']??'') ?></textarea></div>
      <div class="fg"><label>Footer Text AR</label><input type="text" name="footer_text_ar" value="<?= htmlspecialchars($S['footer_text_ar']??'') ?>"></div>
      <div class="fg"><label>Footer Text EN</label><input type="text" name="footer_text_en" value="<?= htmlspecialchars($S['footer_text_en']??'') ?>"></div>
    </div>
  </div>
</div>

<div style="padding:16px 0;border-top:1px solid var(--border);margin-top:4px;display:flex;gap:10px">
  <button type="submit" class="btn btn-primary btn-lg">💾 <?= $isAr?'حفظ جميع الإعدادات':'Save All Settings' ?></button>
  <a href="<?= Helpers::siteUrl('admin/') ?>?p=settings&tab=<?= htmlspecialchars($_GET['tab']??'general') ?>" class="btn btn-secondary"><?= $isAr?'إلغاء':'Cancel' ?></a>
</div>

<script>
function addFooterLink() {
  const list = document.getElementById('footerLinksList');
  const row = document.createElement('div');
  row.className = 'footer-link-row';
  row.style.cssText = 'display:grid;grid-template-columns:40px 1fr 1fr 120px 40px;gap:8px;align-items:center;margin-bottom:8px';
  const isAr = document.documentElement.lang === 'ar';
  row.innerHTML = `<span style="text-align:center;color:var(--muted)">⠿</span>
    <input type="text" name="fl_label_ar[]" placeholder="${isAr?'اسم الرابط':'Link label'}" style="background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:7px 10px;color:inherit;font-family:inherit;font-size:12px;width:100%">
    <input type="text" name="fl_url[]" placeholder="?page=..." style="background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:7px 10px;color:inherit;font-family:inherit;font-size:12px;width:100%">
    <select name="fl_col[]" style="background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:7px 10px;color:inherit;font-family:inherit;font-size:12px">
      <option value="1">${isAr?'عمود':'Col'} 1</option><option value="2">${isAr?'عمود':'Col'} 2</option><option value="3">${isAr?'عمود':'Col'} 3</option><option value="4">${isAr?'عمود':'Col'} 4</option>
    </select>
    <button type="button" onclick="this.closest('.footer-link-row').remove()" style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#ef4444;width:34px;height:34px;border-radius:8px;cursor:pointer">×</button>`;
  list.appendChild(row);
}
// Serialize footer links before submit
document.querySelector('#tab-footer_s')?.closest('form')?.addEventListener('submit', function() {
  const rows = document.querySelectorAll('.footer-link-row');
  const links = Array.from(rows).map(r => ({
    label_ar: r.querySelector('[name="fl_label_ar[]"]')?.value || '',
    url: r.querySelector('[name="fl_url[]"]')?.value || '',
    col: parseInt(r.querySelector('[name="fl_col[]"]')?.value || 1),
  })).filter(l => l.label_ar || l.url);
  document.getElementById('footerLinksJSON').value = JSON.stringify(links);
});
</script>
</form>
<script>
const curTab = '<?= htmlspecialchars($_GET['tab']??'general') ?>';
document.addEventListener('DOMContentLoaded', () => { if(curTab) goStab(curTab, false); });
</script>
