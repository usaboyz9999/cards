<?php
$isAr  = Lang::isRtl();
$S     = Setting::all();
$sym   = htmlspecialchars($S['currency_symbol']??'ر.س');
$sUrl  = Helpers::siteUrl();
$csrf  = Session::csrf();
$items = Auth::check() ? Database::fetchAll(
    "SELECT p.* FROM ".DB_PREFIX."wishlists w JOIN ".DB_PREFIX."products p ON p.id=w.product_id WHERE w.user_id=? ORDER BY w.created_at DESC",
    [Auth::id()]
) : [];
?>
<div class="page-container">
<div class="page-container-inner">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:10px">
    <h2 style="font-size:20px;font-weight:900">❤️ <?= $isAr?'المفضلة':'Wishlist' ?></h2>
    <?php if($items): ?>
    <button onclick="clearAllWishlist()" style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:var(--danger);padding:8px 18px;border-radius:9px;font-weight:700;cursor:pointer;font-family:inherit;font-size:13px">
      🗑️ <?= $isAr?'حذف الكل':'Clear All' ?>
    </button>
    <?php endif; ?>
  </div>

  <div id="wishGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px">
    <?php if(empty($items)): ?>
    <div id="emptyWish" style="grid-column:1/-1;text-align:center;padding:60px 40px;background:var(--card);border:1px solid var(--border);border-radius:16px">
      <div style="font-size:56px;margin-bottom:14px">❤️</div>
      <h3 style="margin-bottom:8px"><?= $isAr?'المفضلة فارغة':'Wishlist is empty' ?></h3>
      <p style="color:var(--muted);margin-bottom:18px"><?= $isAr?'أضف منتجات تعجبك للمفضلة':'Save products you love' ?></p>
      <a href="<?= $sUrl ?>" style="background:linear-gradient(135deg,var(--primary),var(--accent));color:#fff;padding:10px 24px;border-radius:10px;font-weight:700"><?= $isAr?'تصفح المنتجات':'Browse Products' ?></a>
    </div>
    <?php else: ?>
    <?php foreach($items as $p):
      $grad = "linear-gradient(135deg,{$p['color1']},{$p['color2']})";
      $name = htmlspecialchars($isAr?$p['name_ar']:$p['name_en']);
    ?>
    <div class="wish-card" id="wc-<?= $p['id'] ?>" style="background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden;transition:all .3s;cursor:pointer" onclick="openProduct(<?= $p['id'] ?>)">
      <div style="height:110px;background:<?= $grad ?>;display:flex;align-items:center;justify-content:center;font-size:40px;position:relative">
        <?= $p['icon']??'📦' ?>
        <button onclick="event.stopPropagation();removeWish(<?= $p['id'] ?>)" style="position:absolute;top:8px;right:8px;background:rgba(0,0,0,.4);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center" title="<?= $isAr?'إزالة':'Remove' ?>">×</button>
      </div>
      <div style="padding:10px 12px">
        <div style="font-weight:700;font-size:13px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= $name ?></div>
        <div style="color:var(--success);font-weight:700;font-size:13px;margin-top:4px"><?= $sym ?><?= number_format($p['price'],2) ?></div>
      </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
</div>

<script>
const _wcsrf = '<?= $csrf ?>';

async function removeWish(pid) {
  const card = document.getElementById('wc-' + pid);
  if(card) { card.style.transform='scale(.9)'; card.style.opacity='0'; }
  try {
    const r = await fetch('?action=wishlist_toggle', {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:`product_id=${pid}&csrf=${_wcsrf}`
    });
    const d = await r.json();
    setTimeout(()=>{
      if(card) card.remove();
      checkWishEmpty();
      showToast((window._isAr?'تم الإزالة من المفضلة':'Removed from wishlist'),'success');
    }, 280);
  } catch(e) { if(card){card.style.transform='';card.style.opacity='';} }
}

async function clearAllWishlist() {
  const cards = document.querySelectorAll('.wish-card');
  if(!cards.length) return;
  if(!confirm(window._isAr?'حذف كل المفضلة؟':'Clear all wishlist?')) return;
  // Remove all visually first
  cards.forEach(c=>{ c.style.transform='scale(.9)'; c.style.opacity='0'; });
  // Remove each
  const ids = Array.from(cards).map(c=>c.id.replace('wc-',''));
  for(const pid of ids) {
    try {
      await fetch('?action=wishlist_toggle', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`product_id=${pid}&csrf=${_wcsrf}`
      });
    } catch(e){}
  }
  setTimeout(()=>{ cards.forEach(c=>c.remove()); checkWishEmpty(); showToast(window._isAr?'تم حذف كل المفضلة':'Wishlist cleared','success'); }, 300);
}

function checkWishEmpty() {
  const grid = document.getElementById('wishGrid');
  if(!grid) return;
  const cards = grid.querySelectorAll('.wish-card');
  if(!cards.length) {
    grid.innerHTML = `<div id="emptyWish" style="grid-column:1/-1;text-align:center;padding:60px 40px;background:var(--card);border:1px solid var(--border);border-radius:16px">
      <div style="font-size:56px;margin-bottom:14px">❤️</div>
      <h3 style="margin-bottom:8px">${window._isAr?'المفضلة فارغة':'Wishlist is empty'}</h3>
      <a href="/" style="background:linear-gradient(135deg,var(--primary),var(--accent));color:#fff;padding:10px 24px;border-radius:10px;font-weight:700;display:inline-block;margin-top:8px">${window._isAr?'تصفح المنتجات':'Browse Products'}</a>
    </div>`;
    // Hide clear all button
    document.querySelectorAll('[onclick="clearAllWishlist()"]').forEach(b=>b.style.display='none');
  }
}
</script>
