<?php
/**
 * الهيكل الرئيسي للواجهة الأمامية
 */
$S = Setting::all();
$lang = Lang::current();
$isAr = Lang::isRtl();
$dir  = Lang::dir();
$st   = fn($k,$d='') => $S[$k] ?? $d;
$sym  = htmlspecialchars($st('currency_symbol','ر.س'));
$cartCount = Cart::count(Auth::id());
$notifCount = Notification::unreadCount(Auth::id());
$hasTicker = !empty($S['ticker_enabled']);
$hasMaint  = !empty($S['maintenance_mode']);
?>
<!DOCTYPE html>
<html lang="<?= $lang==='ar' ? 'ar-u-nu-latn' : 'en' ?>" dir="<?= $dir ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="<?= htmlspecialchars($st('meta_description','')) ?>">
<title><?= isset($pageTitle) ? htmlspecialchars($pageTitle).' - ' : '' ?><?= htmlspecialchars($st("store_name_$lang",'ووبيكس')) ?></title>
<?php if(!empty($S['favicon_url'])): ?><link rel="icon" href="<?= htmlspecialchars($S['favicon_url']) ?>"><?php endif; ?>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&family=Exo+2:wght@700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= Helpers::assetUrl('css/app.css') ?>">
<?php if(!empty($S['custom_css'])): ?><style><?= $S['custom_css'] ?></style><?php endif; ?>
<style>
:root{
  --primary:<?= htmlspecialchars($st('primary_color','#7c3aed')) ?>;
  --secondary:<?= htmlspecialchars($st('secondary_color','#f97316')) ?>;
  --accent:<?= htmlspecialchars($st('accent_color','#ec4899')) ?>;
  --bg:<?= htmlspecialchars($st('bg_dark','#0d0a1a')) ?>;
  --sidebar:<?= htmlspecialchars($st('bg_sidebar','#110e22')) ?>;
  --card:<?= htmlspecialchars($st('bg_card','#1a1530')) ?>;
}
</style>
</head>
<body class="<?= $hasTicker?'has-ticker':'' ?> <?= $hasMaint?'has-maint':'' ?>">

<?php if($hasMaint && Auth::isAdmin()): ?>
<div class="maint-bar">🔧 <?= Lang::get('maintenance_mode_admin','الموقع في وضع الصيانة - يظهر للمدير فقط') ?></div>
<?php endif; ?>

<?php require VIEWS_PATH.'/partials/ticker.php'; ?>
<?php require VIEWS_PATH.'/partials/popup.php'; ?>

<div style="display:flex;min-height:100vh">
  <?php require VIEWS_PATH.'/partials/sidebar.php'; ?>

  <main class="main" id="mainContent">
    <?php require VIEWS_PATH.'/partials/navbar.php'; ?>
    <?php require VIEWS_PATH.'/partials/flash.php'; ?>
    <?= $content ?? '' ?>
    <?php require VIEWS_PATH.'/partials/footer.php'; ?>
  </main>
</div>

<?php require VIEWS_PATH.'/partials/modals.php'; ?>
<?php require VIEWS_PATH.'/partials/cart_sidebar.php'; ?>

<div id="toastWrap" class="toast-wrap"></div>

<script>
window._csrf    = '<?= Session::csrf() ?>';
window._sym     = '<?= addslashes($sym) ?>';
window._lang    = '<?= $lang ?>';
window._loggedIn= <?= Auth::check()?'true':'false' ?>;
window._isAr    = <?= Lang::isRtl()?'true':'false' ?>;
window._wishlist= <?= json_encode(Auth::check()?array_column(Database::fetchAll("SELECT product_id FROM ".DB_PREFIX."wishlists WHERE user_id=?",[Auth::id()]),'product_id'):[]) ?>;
window._toastEnabled = <?= !empty($S['toast_enabled']??'1')?'true':'false' ?>;
window._toastPos     = '<?= htmlspecialchars($S['toast_position']??'bottom-right') ?>';
window._toastDur     = <?= intval($S['toast_duration']??1000) ?>;
window._toastAutoHide= <?= ($S['toast_autohide']??'1')?'true':'false' ?>;
window._products= <?= json_encode(array_values($allProducts ?? []), JSON_UNESCAPED_UNICODE) ?>;
window._cats    = <?= json_encode(array_values($allCategories ?? []), JSON_UNESCAPED_UNICODE) ?>;
window._prices  = <?= json_encode($allPrices ?? [], JSON_UNESCAPED_UNICODE) ?>;
window._popupEnabled= <?= !empty($S['popup_enabled'])?'true':'false' ?>;
window._popupDelay  = <?= intval($S['popup_delay']??2) ?>;
window._popupOnce   = <?= !empty($S['popup_show_once'])?'true':'false' ?>;
window._t = <?= json_encode(Lang::current()==='ar' ? require LANG_PATH.'/ar.php' : require LANG_PATH.'/en.php', JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="<?= Helpers::assetUrl('js/app.js') ?>"></script>
</body>
</html>
<script>
// ── حفظ موضع التمرير عند فلترة التصنيفات ──
(function(){
  var KEY = 'fr_scroll';
  var saved = sessionStorage.getItem(KEY);
  if(saved && window.location.hash === ''){
    var m = document.getElementById('mainContent');
    if(m) m.scrollTop = parseInt(saved);
    sessionStorage.removeItem(KEY);
  }
  // حفظ الموضع عند الضغط على روابط التنقل الأخرى (ليس filter)
  document.querySelectorAll('a[data-nav]').forEach(function(a){
    a.addEventListener('click', function(){
      var m = document.getElementById('mainContent');
      if(m) sessionStorage.setItem(KEY, m.scrollTop);
    });
  });
})();
</script>
