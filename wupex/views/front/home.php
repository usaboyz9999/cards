<div class="page-container" style="padding-top:0">
<?php
$S    = Setting::all();
$lang = Lang::current();
$isAr = Lang::isRtl();
$t    = fn($k) => Lang::get($k);
$sym  = htmlspecialchars($S['currency_symbol']??'ر.س');
$heroGrad = "linear-gradient(135deg,{$S['hero_bg_start']},{$S['hero_bg_mid']} 50%,{$S['hero_bg_end']})";
$perRow = intval($S['products_per_row']??6);
?>

<!-- ── HERO ── -->
<div class="hero" style="background:<?= $heroGrad ?>;margin:16px -28px 0;border-radius:0">
  <div class="hero-side">
    <div class="hero-big"><?= htmlspecialchars($S['hero_text_left']??'أسرع') ?></div>
    <div class="hero-sub"><?= htmlspecialchars($S["hero_subtext_$lang"]??$S['hero_subtext_ar']??'') ?></div>
    <?php if(Auth::check()): ?>
    <div style="margin-top:10px;font-size:13px;color:rgba(255,255,255,.75)">
      👋 <?= $isAr?'مرحباً':'Hello' ?>, <strong><?= htmlspecialchars(Auth::user()['name']??'') ?></strong>
    </div>
    <?php endif; ?>
  </div>
  <div class="hero-center">
    <div class="hero-char"><?= $S['hero_character']??'🧑‍💻' ?></div>
    <div class="hero-chips">
      <?php foreach(array_slice($allCategories,0,6) as $c): ?>
      <div class="chip" onclick="filterCat(<?= $c['id'] ?>,null,null)"><?= $c['icon'] ?></div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="hero-side" style="text-align:<?= $isAr?'left':'right' ?>">
    <div class="hero-big"><?= htmlspecialchars($S['hero_text_right']??'سلس') ?></div>
    <div class="hero-sub"><?= htmlspecialchars($S["store_tagline_$lang"]??'') ?></div>
    <?php if(!empty($S['wallet_enabled']) && Auth::check()): ?>
    <div style="margin-top:10px;font-size:12px;color:rgba(255,255,255,.75)">
      💰 <?= $t('available_balance') ?>:
      <strong style="color:#fff"><?= $sym ?><?= number_format(Wallet::balance(Auth::id()),2) ?></strong>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- ── CATEGORIES GRID ── -->
<div class="cats-grid">
  <div class="cat-card active" id="catAll" onclick="filterCat('all',null,this)">
    <div class="cat-ic">🏠</div>
    <div class="cat-nm"><?= $t('all') ?> (<?= count($allProducts) ?>)</div>
  </div>
  <?php foreach($allCategories as $cat):
    $catName = htmlspecialchars($cat["name_$lang"] ?? $cat['name_ar']);
  ?>
  <div class="cat-card" onclick="filterCat(<?= $cat['id'] ?>,null,this)">
    <div class="cat-ic"><?= $cat['icon'] ?></div>
    <div class="cat-nm"><?= $catName ?> (<?= $cat['products_count'] ?>)</div>
  </div>
  <?php endforeach; ?>
</div>

<!-- ── FILTER TAGS ── -->
<div class="filter-bar">
  <button class="f-btn active" id="fb-all" onclick="filterCat('all',null,document.getElementById('catAll'))">
    <?= $t('all') ?> <span class="f-cnt"><?= count($allProducts) ?></span>
  </button>
  <button class="f-btn" onclick="filterFeatured()">⭐ <?= $t('featured') ?></button>
  <button class="f-btn" onclick="filterBadge('NEW')">🆕 <?= $t('new_arrivals') ?></button>
  <button class="f-btn" onclick="filterBadge('HOT')">🔥 <?= $t('hot_deals') ?></button>
  <button class="f-btn" onclick="filterBadge('TOP')">🏆 <?= $t('top') ?></button>
  <?php foreach($allCategories as $cat): if(($cat['products_count']??0)<1) continue; ?>
  <button class="f-btn" onclick="filterCat(<?= $cat['id'] ?>)">
    <?= $cat['icon'] ?> <?= htmlspecialchars($cat["name_$lang"]??$cat['name_ar']) ?>
    <span class="f-cnt"><?= $cat['products_count'] ?></span>
  </button>
  <?php endforeach; ?>
</div>

<!-- ── SECTION HEADER ── -->
<div class="sec-hdr">
  <div class="sec-title" id="secTitle"><?= $t('all_products') ?></div>
  <div class="res-cnt" id="resCnt"><?= count($allProducts) ?> <?= $t('results') ?></div>
</div>

<!-- ── PRODUCTS GRID ── -->
<div class="prods-wrap" id="prodsSection">
<div class="prods-grid" id="prodsGrid" style="--cols:<?= $perRow ?>">
<?php foreach($allProducts as $i => $p):
  $pName  = htmlspecialchars($p["name_$lang"] ?? $p['name_ar']);
  $bl     = $p['badge'] ?? '';
  $hasImg = !empty($p['image']);
  $grad   = "linear-gradient(135deg,{$p['color1']},{$p['color2']})";
  $delay  = min($i * 0.03, 1.5);
  $flags  = $p['countries'] ? explode(',', $p['countries']) : [];
?>
<div class="prod-card"
     style="animation-delay:<?= $delay ?>s"
     data-id="<?= $p['id'] ?>"
     data-cat="<?= $p['category_id'] ?>"
     data-name="<?= htmlspecialchars(mb_strtolower($p['name_ar'].' '.$p['name_en'])) ?>"
     data-price="<?= $p['price'] ?>"
     data-featured="<?= !empty($p['featured'])?'1':'0' ?>"
     data-badge="<?= htmlspecialchars(strtoupper($bl)) ?>"
     data-sales="<?= (int)($p['sales_count']??0) ?>"
     onclick="openProduct(<?= $p['id'] ?>)">

  <div class="card-img" style="background:<?= $grad ?>">
    <div class="w-badge">w</div>
    <?php if($bl): ?><div class="prod-badge <?= strtoupper($bl) ?>"><?= htmlspecialchars($bl) ?></div><?php endif; ?>
    <?php if($hasImg): ?>
    <img src="<?= Helpers::imageUrl($p['image']) ?>" alt="<?= $pName ?>" loading="lazy" onerror="this.style.display='none'">
    <?php endif; ?>
    <div class="card-overlay"></div>
    <div class="card-emoji"><?= $p['icon'] ?></div>
    <?php if($p['delivery_type']==='instant'): ?>
    <div class="stock-badge">⚡ <?= $isAr?'فوري':'Instant' ?></div>
    <?php endif; ?>
  </div>
  <div class="card-info">
    <div class="card-name" title="<?= $pName ?>"><?= $pName ?></div>
    <?php if(!empty($S['show_prices'])): ?>
    <div class="card-price">
      <?= $sym ?><?= number_format($p['price'],2) ?>
      <?php if($p['price_max']>0 && $p['price_max']!=$p['price']): ?>
      - <?= $sym ?><?= number_format($p['price_max'],2) ?>
      <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php if(!empty($S['show_flags']) && $flags): ?>
    <div class="card-flags">
      <?php foreach(array_slice($flags,0,4) as $f): ?><span class="flag"><?= htmlspecialchars(trim($f)) ?></span><?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php if(empty($p['stock'])): ?>
  <div class="oos-ov"><span><?= $t('out_of_stock') ?></span></div>
  <?php endif; ?>
</div>
<?php endforeach; ?>
</div>

<div class="empty-state" id="emptyState" style="display:none">
  <div class="ico">🔍</div>
  <h3><?= $t('no_results') ?></h3>
  <p><?= $t('try_other') ?></p>
</div>
</div>

</div><!-- /home-page-container -->