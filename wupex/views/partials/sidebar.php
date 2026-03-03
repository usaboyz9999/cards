<?php
$S       = Setting::all();
$lang    = Lang::current();
$isAr    = Lang::isRtl();
$cartCount  = Cart::count(Auth::id());
$storeName  = htmlspecialchars($S["store_name_$lang"] ?? 'ووبيكس');
$curPage    = $_GET['page'] ?? 'home';
function navActive2(string $p, string $cur): string { return $p===$cur?'active':''; }
?>
<aside class="sidebar" id="sidebar">
  <div class="logo-wrap" onclick="toggleSidebar()" title="<?= $isAr?'طي القائمة':'Collapse' ?>">
    <?php if(!empty($S['logo_url'])): ?>
    <img src="<?= htmlspecialchars($S['logo_url']) ?>" alt="logo" style="width:38px;height:38px;object-fit:contain;border-radius:8px;flex-shrink:0">
    <?php else: ?><div class="logo-ic">W</div><?php endif; ?>
    <div class="logo-nm"><b><?= $storeName ?></b></div>
  </div>

  <nav class="sb-nav">
    <div class="nav-sec"><?= $isAr?'التصفح':'Browse' ?></div>
    <a href="<?= Helpers::siteUrl() ?>" class="nav-item <?= ($curPage==='home')?'active':'' ?>" data-nav onclick="navTo(this,event)">
      <span class="nav-ic">🏠</span><span class="nav-lbl"><?= $isAr?'الرئيسية':'Home' ?></span>
    </a>
    <a href="<?= Helpers::siteUrl('?page=products') ?>" class="nav-item <?= navActive2('products',$curPage) ?>" data-nav onclick="navTo(this,event)">
      <span class="nav-ic">📦</span><span class="nav-lbl"><?= $isAr?'كل المنتجات':'All Products' ?></span>
    </a>
    <a href="<?= Helpers::siteUrl('?page=wishlist') ?>" class="nav-item <?= navActive2('wishlist',$curPage) ?>" data-nav onclick="navTo(this,event)">
      <span class="nav-ic">❤️</span><span class="nav-lbl"><?= $isAr?'المفضلة':'Wishlist' ?></span>
    </a>

    <?php if(!Auth::check()): ?>
    <div class="nav-sec"><?= $isAr?'الدخول':'Access' ?></div>
    <a href="<?= Helpers::siteUrl('?page=login') ?>" class="nav-item <?= navActive2('login',$curPage) ?>" data-nav onclick="navTo(this,event)">
      <span class="nav-ic">🔐</span><span class="nav-lbl"><?= $isAr?'تسجيل الدخول':'Login' ?></span>
    </a>
    <a href="<?= Helpers::siteUrl('?page=register') ?>" class="nav-item <?= navActive2('register',$curPage) ?>" data-nav onclick="navTo(this,event)">
      <span class="nav-ic">✍️</span><span class="nav-lbl"><?= $isAr?'إنشاء حساب':'Register' ?></span>
    </a>
    <?php else: ?>
    <div class="nav-sec"><?= $isAr?'حسابي':'My Account' ?></div>
    <a href="<?= Helpers::siteUrl('?page=account') ?>" class="nav-item <?= navActive2('account',$curPage) ?>" data-nav onclick="navTo(this,event)">
      <span class="nav-ic">👤</span><span class="nav-lbl"><?= htmlspecialchars(Auth::user()['name']??'') ?></span>
    </a>
    <a href="<?= Helpers::siteUrl('?page=account&tab=orders') ?>" class="nav-item" data-nav onclick="navTo(this,event)">
      <span class="nav-ic">📦</span><span class="nav-lbl"><?= $isAr?'طلباتي':'My Orders' ?></span>
    </a>
    <?php if(!empty($S['wallet_enabled'])): ?>
    <a href="<?= Helpers::siteUrl('?page=account&tab=wallet') ?>" class="nav-item" data-nav onclick="navTo(this,event)">
      <span class="nav-ic">💰</span><span class="nav-lbl"><?= $isAr?'المحفظة':'Wallet' ?></span>
    </a>
    <?php endif; ?>
    <a href="<?= Helpers::siteUrl('?page=logout') ?>" class="nav-item" style="color:var(--danger)" data-nav onclick="navTo(this,event)">
      <span class="nav-ic">🚪</span><span class="nav-lbl"><?= $isAr?'خروج':'Logout' ?></span>
    </a>
    <?php endif; ?>
  </nav>

  <div class="sb-footer">
    <a href="<?= Helpers::siteUrl('?lang='.($isAr?'en':'ar')) ?>" class="sb-footer-btn">
      🌐 <span class="sb-footer-lbl"><?= $isAr?'EN':'AR' ?></span>
    </a>
    <button class="sb-footer-btn" onclick="openCart();loadCartItems()">
      🛒<?php if($cartCount): ?><span class="cart-badge" style="font-size:10px;padding:1px 5px"><?= $cartCount ?></span><?php endif; ?>
    </button>
    <?php if(Auth::isAdmin()): ?>
    <a href="<?= Helpers::siteUrl('admin/') ?>" class="sb-footer-btn" target="_blank">
      ⚙️ <span class="sb-footer-lbl"><?= $isAr?'إدارة':'Admin' ?></span>
    </a>
    <?php endif; ?>
  </div>
</aside>
