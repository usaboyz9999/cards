<?php
$S    = Setting::all();
$lang = Lang::current();
$isAr = Lang::isRtl();
$t    = fn($k) => Lang::get($k);
$cartCount  = Cart::count(Auth::id());
$notifCount = Notification::unreadCount(Auth::id());
?>
<div class="topbar">
  <div class="topbar-left">
    <div class="search-wrap srch-wrap">
      <span class="search-ico">🔍</span>
      <input type="text" id="searchInput" class="search-input"
             placeholder="<?= $t('search_ph') ?>"
              autocomplete="off">
    </div>
    <select id="sortSel" class="sort-sel">
      <option value="default"><?= $isAr?'ترتيب: افتراضي':'Sort: Default' ?></option>
      <option value="price_low"><?= $isAr?'السعر: الأقل':'Price: Low' ?></option>
      <option value="price_high"><?= $isAr?'السعر: الأعلى':'Price: High' ?></option>
      <option value="newest"><?= $isAr?'الأحدث':'Newest' ?></option>
      <option value="popular"><?= $isAr?'الأكثر مبيعاً':'Most Popular' ?></option>
      <option value="name"><?= $isAr?'الاسم أبجدياً':'Name A-Z' ?></option>
    </select>
  </div>
  <div class="topbar-right">
    <div class="lang-toggle">
      <button class="lang-btn <?= $lang==='ar'?'active':'' ?>" onclick="location.href='?lang=ar'">ع</button>
      <button class="lang-btn <?= $lang==='en'?'active':'' ?>" onclick="location.href='?lang=en'">EN</button>
    </div>
    <?php if(Auth::check()): ?>
    <a href="?page=account&tab=notifications" class="tb-btn notif-btn" data-nav onclick="navTo(this,event)">
      🔔<?php if($notifCount): ?><span class="notif-dot"></span><?php endif; ?>
    </a>
    <a href="?page=account" class="tb-btn" data-nav onclick="navTo(this,event)">
      👤 <span><?= htmlspecialchars(Auth::user()['name'] ?? '') ?></span>
    </a>
    <?php else: ?>
    <a href="?page=login" class="tb-btn" data-nav onclick="navTo(this,event)"><?= $t('login') ?></a>
    <a href="?page=register" class="tb-btn primary" data-nav onclick="navTo(this,event)"><?= $t('register') ?></a>
    <?php endif; ?>
    <button class="tb-btn" id="cartBtn" onclick="openCart();loadCartItems()">
      🛒<?php if($cartCount): ?><span class="cart-badge"><?= $cartCount ?></span><?php endif; ?>
    </button>
    <?php if(Auth::isAdmin()): ?>
    <a href="<?= Helpers::siteUrl('admin/') ?>" class="tb-btn primary" target="_blank">⚙️</a>
    <?php endif; ?>
  </div>
</div>
