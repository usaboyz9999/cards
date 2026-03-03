<?php
/**
 * هيكل لوحة التحكم الرئيسي
 */
if(!defined('WUPEX')) die('Access denied.');
$S    = Setting::all();
$lang = Lang::current();
$isAr = Lang::isRtl();
$dir  = Lang::dir();
$t    = fn($k) => Lang::get($k);
$pg   = $_GET['p'] ?? 'dashboard';
$tab  = $_GET['tab'] ?? '';

// حساب الإشعارات والطلبات المعلقة
$pendingOrders   = Database::count('orders', "status='pending'");
$pendingTickets  = Database::count('tickets', "status='open'");
$pendingReviews  = Database::count('reviews', "status='pending'");
$pendingDeposits = Database::count('deposit_requests', "status='pending'");
$totalUsers      = Database::count('users', "role='user'");
$storeName       = htmlspecialchars($S["store_name_$lang"] ?? 'ووبيكس');

function admLink(string $p, string $t='', string $cur='', string $curT=''): string {
    $active = ($p === $cur && ($t==='' || $t===$curT)) ? 'active' : '';
    return $active;
}
?>
<!DOCTYPE html>
<html lang="<?= $lang==='ar' ? 'ar-u-nu-latn' : 'en' ?>" dir="<?= $dir ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? htmlspecialchars($pageTitle).' | ' : '' ?>Admin - <?= $storeName ?></title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&family=Exo+2:wght@700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= Helpers::assetUrl('css/admin.css') ?>">
<style>:root{
  --primary:<?= htmlspecialchars($S['primary_color']??'#7c3aed') ?>;
  --secondary:<?= htmlspecialchars($S['secondary_color']??'#f97316') ?>;
  --accent:<?= htmlspecialchars($S['accent_color']??'#ec4899') ?>;
  --bg:<?= htmlspecialchars($S['bg_dark']??'#09071a') ?>;
  --sidebar:<?= htmlspecialchars($S['bg_sidebar']??'#0e0b1f') ?>;
  --card:<?= htmlspecialchars($S['bg_card']??'#14102a') ?>;
}</style>
</head>
<body>

<!-- ══ SIDEBAR ══ -->
<aside class="adm-sb">
  <div class="adm-logo">
    <div class="adm-logo-ic">W</div>
    <div><div class="adm-logo-nm"><?= $storeName ?></div><div class="adm-logo-sub"><?= $isAr?'لوحة التحكم':'Admin Panel' ?></div></div>
  </div>

  <nav class="adm-nav">
    <!-- الرئيسية -->
    <div class="adm-sec"><?= $isAr?'الرئيسية':'Overview' ?></div>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=dashboard" class="adm-lnk <?= admLink('dashboard','',$pg,$tab) ?>"><span class="adm-ic">📊</span><span><?= $isAr?'لوحة التحكم':'Dashboard' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=reports" class="adm-lnk <?= admLink('reports','',$pg,$tab) ?>"><span class="adm-ic">📈</span><span><?= $isAr?'التقارير':'Reports' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=visitors" class="adm-lnk <?= admLink('visitors','',$pg,$tab) ?>"><span class="adm-ic">👁️</span><span><?= $isAr?'الزوار':'Visitors' ?></span></a>

    <!-- المبيعات -->
    <div class="adm-sec"><?= $isAr?'المبيعات':'Sales' ?></div>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=orders" class="adm-lnk <?= admLink('orders','',$pg,$tab) ?>"><span class="adm-ic">📦</span><span><?= $isAr?'الطلبات':'Orders' ?></span><?php if($pendingOrders): ?><span class="adm-badge orange"><?= $pendingOrders ?></span><?php endif; ?></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=deposits" class="adm-lnk <?= admLink('deposits','',$pg,$tab) ?>"><span class="adm-ic">💳</span><span><?= $isAr?'طلبات الإيداع':'Deposits' ?></span><?php if($pendingDeposits): ?><span class="adm-badge red"><?= $pendingDeposits ?></span><?php endif; ?></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=transactions" class="adm-lnk <?= admLink('transactions','',$pg,$tab) ?>"><span class="adm-ic">💰</span><span><?= $isAr?'المعاملات':'Transactions' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=wallet" class="adm-lnk <?= admLink('wallet','',$pg,$tab) ?>"><span class="adm-ic">👛</span><span><?= $isAr?'المحفظة':'Wallet' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=coupons" class="adm-lnk <?= admLink('coupons','',$pg,$tab) ?>"><span class="adm-ic">🏷️</span><span><?= $isAr?'الكوبونات':'Coupons' ?></span></a>

    <!-- المنتجات -->
    <div class="adm-sec"><?= $isAr?'الكتالوج':'Catalog' ?></div>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=products" class="adm-lnk <?= admLink('products','',$pg,$tab) ?>"><span class="adm-ic">🎮</span><span><?= $isAr?'المنتجات':'Products' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=categories" class="adm-lnk <?= admLink('categories','',$pg,$tab) ?>"><span class="adm-ic">📂</span><span><?= $isAr?'التصنيفات':'Categories' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=codes" class="adm-lnk <?= admLink('codes','',$pg,$tab) ?>"><span class="adm-ic">🔑</span><span><?= $isAr?'الأكواد':'Codes' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=banners" class="adm-lnk <?= admLink('banners','',$pg,$tab) ?>"><span class="adm-ic">🖼️</span><span><?= $isAr?'البانرات':'Banners' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=reviews" class="adm-lnk <?= admLink('reviews','',$pg,$tab) ?>"><span class="adm-ic">⭐</span><span><?= $isAr?'التقييمات':'Reviews' ?></span><?php if($pendingReviews): ?><span class="adm-badge"><?= $pendingReviews ?></span><?php endif; ?></a>

    <!-- المستخدمون -->
    <div class="adm-sec"><?= $isAr?'المستخدمون':'Users' ?></div>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=users" class="adm-lnk <?= admLink('users','',$pg,$tab) ?>"><span class="adm-ic">👥</span><span><?= $isAr?'المستخدمون':'Users' ?></span><span class="adm-badge green"><?= $totalUsers ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=tickets" class="adm-lnk <?= admLink('tickets','',$pg,$tab) ?>"><span class="adm-ic">🎫</span><span><?= $isAr?'تذاكر الدعم':'Support' ?></span><?php if($pendingTickets): ?><span class="adm-badge red"><?= $pendingTickets ?></span><?php endif; ?></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=notifications" class="adm-lnk <?= admLink('notifications','',$pg,$tab) ?>"><span class="adm-ic">🔔</span><span><?= $isAr?'الإشعارات':'Notifications' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=referrals" class="adm-lnk <?= admLink('referrals','',$pg,$tab) ?>"><span class="adm-ic">🔗</span><span><?= $isAr?'الإحالات':'Referrals' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=points" class="adm-lnk <?= admLink('points','',$pg,$tab) ?>"><span class="adm-ic">💎</span><span><?= $isAr?'النقاط والولاء':'Points' ?></span></a>

    <a href="<?= Helpers::siteUrl('admin/') ?>?p=admins" class="adm-lnk <?= admLink('admins','',$pg,$tab) ?>"><span class="adm-ic">👑</span><span><?= $isAr?'إدارة المسؤولين':'Admins' ?></span></a>

    <!-- التخصيص -->
    <div class="adm-sec"><?= $isAr?'التخصيص':'Customize' ?></div>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=settings&tab=general" class="adm-lnk <?= ($pg==='settings'&&$tab==='general')?'active':'' ?>"><span class="adm-ic">⚙️</span><span><?= $isAr?'الإعدادات العامة':'General' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=settings&tab=appearance" class="adm-lnk <?= ($pg==='settings'&&$tab==='appearance')?'active':'' ?>"><span class="adm-ic">🎨</span><span><?= $isAr?'المظهر':'Appearance' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=settings&tab=hero" class="adm-lnk <?= ($pg==='settings'&&$tab==='hero')?'active':'' ?>"><span class="adm-ic">🦸</span><span><?= $isAr?'البانر الرئيسي':'Hero Banner' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=settings&tab=ticker" class="adm-lnk <?= ($pg==='settings'&&$tab==='ticker')?'active':'' ?>"><span class="adm-ic">📢</span><span><?= $isAr?'الشريط المتحرك':'Ticker' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=settings&tab=popup" class="adm-lnk <?= ($pg==='settings'&&$tab==='popup')?'active':'' ?>"><span class="adm-ic">🎉</span><span><?= $isAr?'النافذة المنبثقة':'Popup' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=settings&tab=payment" class="adm-lnk <?= ($pg==='settings'&&$tab==='payment')?'active':'' ?>"><span class="adm-ic">💳</span><span><?= $isAr?'الدفع':'Payment' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=settings&tab=shipping" class="adm-lnk <?= ($pg==='settings'&&$tab==='shipping')?'active':'' ?>"><span class="adm-ic">🚚</span><span><?= $isAr?'الشحن':'Shipping' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=settings&tab=wallet_s" class="adm-lnk <?= ($pg==='settings'&&$tab==='wallet_s')?'active':'' ?>"><span class="adm-ic">💰</span><span><?= $isAr?'المحفظة':'Wallet Settings' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=settings&tab=social" class="adm-lnk <?= ($pg==='settings'&&$tab==='social')?'active':'' ?>"><span class="adm-ic">📱</span><span><?= $isAr?'التواصل الاجتماعي':'Social' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=settings&tab=email" class="adm-lnk <?= ($pg==='settings'&&$tab==='email')?'active':'' ?>"><span class="adm-ic">✉️</span><span><?= $isAr?'البريد الإلكتروني':'Email' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=settings&tab=loyalty" class="adm-lnk <?= ($pg==='settings'&&$tab==='loyalty')?'active':'' ?>"><span class="adm-ic">🏆</span><span><?= $isAr?'النقاط والإحالات':'Loyalty' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=settings&tab=maintenance" class="adm-lnk <?= ($pg==='settings'&&$tab==='maintenance')?'active':'' ?>"><span class="adm-ic">🔧</span><span><?= $isAr?'الصيانة':'Maintenance' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=settings&tab=advanced" class="adm-lnk <?= ($pg==='settings'&&$tab==='advanced')?'active':'' ?>"><span class="adm-ic">🛠️</span><span><?= $isAr?'متقدم':'Advanced' ?></span></a>

    <!-- SEO والظهور -->
    <div class="adm-sec">SEO</div>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=seo" class="adm-lnk <?= admLink('seo','',$pg,$tab) ?>"><span class="adm-ic">🔎</span><span>SEO <?= $isAr?'ومحركات البحث':'& Search' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=langs" class="adm-lnk <?= admLink('langs','',$pg,$tab) ?>"><span class="adm-ic">🌐</span><span><?= $isAr?'اللغات':'Languages' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=pages" class="adm-lnk <?= admLink('pages','',$pg,$tab) ?>"><span class="adm-ic">📄</span><span><?= $isAr?'الصفحات':'Pages' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=faqs" class="adm-lnk <?= admLink('faqs','',$pg,$tab) ?>"><span class="adm-ic">❓</span><span><?= $isAr?'الأسئلة الشائعة':'FAQs' ?></span></a>

    <!-- الأمان -->
    <div class="adm-sec"><?= $isAr?'الأمان':'Security' ?></div>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=security" class="adm-lnk <?= admLink('security','',$pg,$tab) ?>"><span class="adm-ic">🛡️</span><span><?= $isAr?'الأمان المتقدم':'Security' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=activity" class="adm-lnk <?= admLink('activity','',$pg,$tab) ?>"><span class="adm-ic">📋</span><span><?= $isAr?'سجل النشاطات':'Activity Log' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=backup" class="adm-lnk <?= admLink('backup','',$pg,$tab) ?>"><span class="adm-ic">💾</span><span><?= $isAr?'النسخ الاحتياطي':'Backup' ?></span></a>
  </nav>

  <div class="adm-footer">
    <a href="<?= Helpers::siteUrl() ?>" class="adm-ftr-btn" target="_blank">🏠 <span><?= $isAr?'المتجر':'Store' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=settings&tab=general" class="adm-ftr-btn">⚙️ <span><?= $isAr?'إعدادات':'Settings' ?></span></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=logout" class="adm-ftr-btn" style="color:#ef4444">🚪 <span><?= $isAr?'خروج':'Logout' ?></span></a>
  </div>
</aside>

<!-- ══ MAIN ══ -->
<main class="adm-main">
  <!-- Topbar -->
  <div class="adm-topbar">
    <div class="pg-title"><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : ($isAr?'لوحة التحكم':'Dashboard') ?></div>
    <div style="display:flex;align-items:center;gap:9px">
      <div class="lang-toggle" style="display:flex;background:var(--card);border:1px solid var(--border);border-radius:9px;overflow:hidden">
        <a href="?<?= http_build_query(array_merge($_GET,['lang'=>'ar'])) ?>" style="padding:7px 12px;font-size:12px;font-weight:700;color:<?= $lang==='ar'?'#fff':'var(--muted)' ?>;background:<?= $lang==='ar'?'var(--primary)':'none' ?>">ع</a>
        <a href="?<?= http_build_query(array_merge($_GET,['lang'=>'en'])) ?>" style="padding:7px 12px;font-size:12px;font-weight:700;color:<?= $lang==='en'?'#fff':'var(--muted)' ?>;background:<?= $lang==='en'?'var(--primary)':'none' ?>">EN</a>
      </div>
      <span style="font-size:12px;color:var(--muted)">👤 <?= htmlspecialchars(Auth::user()['name']??'Admin') ?></span>
      <span style="font-size:11px;color:var(--muted)"><?= date('Y-m-d H:i') ?></span>
    </div>
  </div>

  <!-- Content -->
  <div class="adm-content">
    <?php
    $types=['success','error','warning','info'];
    foreach($types as $type){ $msg=Session::flash($type); if($msg) echo "<div class='flash $type'>$msg</div>"; }
    ?>
    <?= $content ?? '' ?>
  </div>
</main>

<div id="toastWrap" class="toast-wrap"></div>
<script>window._csrf='<?= Session::csrf() ?>';</script>
<script src="<?= Helpers::assetUrl('js/admin.js') ?>"></script>
</body>
</html>
<script>
// ── حفظ موضع التمرير عند التنقل ──
(function(){
  var KEY = 'adm_scroll';
  // استعادة الموضع عند تحميل الصفحة
  var saved = sessionStorage.getItem(KEY);
  if(saved){
    var mainEl = document.querySelector('.adm-main');
    if(mainEl){ mainEl.scrollTop = parseInt(saved); }
    sessionStorage.removeItem(KEY);
  }
  // حفظ الموضع قبل الانتقال
  document.querySelectorAll('.adm-lnk, .qa-item, .btn').forEach(function(a){
    if(a.tagName === 'A' || (a.tagName === 'BUTTON' && !a.type) || a.type === 'submit') return;
    a.addEventListener('click', function(){
      var m = document.querySelector('.adm-main');
      if(m) sessionStorage.setItem(KEY, m.scrollTop);
    });
  });
  document.querySelectorAll('.adm-lnk').forEach(function(a){
    a.addEventListener('click', function(){
      var m = document.querySelector('.adm-main');
      if(m) sessionStorage.setItem(KEY, m.scrollTop);
    });
  });
})();
</script>
