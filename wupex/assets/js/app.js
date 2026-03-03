// ================================================================
// ووبيكس — Wupex Main JS  v8
// ================================================================
'use strict';

/* ── Sidebar Toggle ── */
function toggleSidebar() {
  document.getElementById('sidebar')?.classList.toggle('collapsed');
  document.getElementById('mainContent')?.classList.toggle('expanded');
}

/* ── Scroll to Products section ── */
function scrollToProducts() {
  const el = document.getElementById('prodsSection') || document.querySelector('.prods-wrap');
  if (!el) return;
  const main = document.getElementById('mainContent');
  if (main) {
    const top = el.getBoundingClientRect().top + main.scrollTop - 80;
    main.scrollTo({ top, behavior: 'smooth' });
  } else {
    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}

/* ── Toast (settings-driven) ── */
const _toastCfg = {
  enabled:  window._toastEnabled ?? true,
  pos:      window._toastPos ?? 'bottom-right',
  duration: window._toastDur ?? 1000,
  autohide: window._toastAutoHide ?? true,
};

function showToast(msg, type = 'success', dur) {
  if (!_toastCfg.enabled) return;
  const duration = dur ?? _toastCfg.duration;
  const wrap = document.getElementById('toastWrap');
  if (!wrap) return;
  const el = document.createElement('div');
  el.className = `toast ${type}`;
  el.innerHTML = `<span>${msg}</span><button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;font-size:16px;padding:0 0 0 8px">×</button>`;
  wrap.appendChild(el);
  requestAnimationFrame(() => el.classList.add('show'));
  if (_toastCfg.autohide) {
    setTimeout(() => { el.classList.remove('show'); setTimeout(() => el.remove(), 350); }, duration);
  }
}

/* ── Product Filter / Search / Sort ── */
let curCat = 'all', curSearch = '', curSort = 'default', curBadge = '';

function filterCat(catId, el, cardEl) {
  curCat = catId; curBadge = '';
  applyFilters();
  document.querySelectorAll('.nav-item[data-filter]').forEach(e => e.classList.remove('active'));
  document.querySelectorAll('.cat-card').forEach(e => e.classList.remove('active'));
  if (el) el.classList.add('active');
  if (cardEl) cardEl.classList.add('active');
  const title = document.getElementById('secTitle');
  if (title) {
    if (catId === 'all') { title.textContent = window._t?.all_products || 'كل المنتجات'; }
    else { const c = window._cats?.find(x => x.id == catId); if(c) title.textContent = (c.icon||'') + ' ' + (document.documentElement.lang==='ar' ? c.name_ar : c.name_en); }
  }
  scrollToProducts();
}

function filterBadge(badge, el) {
  curBadge = badge; curCat = 'all';
  applyFilters();
  if (el) el.classList.add('active');
  scrollToProducts();
}

function sortProducts(v) {
  curSort = v;
  const sel = document.getElementById('sortSel');
  if (sel) sel.value = v;
  applyFilters();
}

function applyFilters() {
  const cards = document.querySelectorAll('.prod-card');
  const term = curSearch.toLowerCase().trim();
  let visible = [];
  cards.forEach(c => {
    const cat    = c.dataset.cat || '';
    const name   = (c.dataset.name || '').toLowerCase();
    const badge  = (c.dataset.badge || '').toLowerCase();
    const catOk  = curCat === 'all' || cat == curCat;
    const termOk = !term || name.includes(term);
    const badgeOk= !curBadge || badge === curBadge.toLowerCase();
    const show = catOk && termOk && badgeOk;
    c.style.display = show ? '' : 'none';
    if (show) visible.push(c);
  });
  // Sort
  if (curSort !== 'default') {
    const grid = document.querySelector('.prods-grid');
    if (grid) {
      visible.sort((a, b) => {
        const pa = parseFloat(a.dataset.price)||0, pb = parseFloat(b.dataset.price)||0;
        const sa = parseInt(a.dataset.sales)||0,   sb = parseInt(b.dataset.sales)||0;
        const na = a.dataset.name||'',             nb = b.dataset.name||'';
        if (curSort === 'price_low')  return pa - pb;
        if (curSort === 'price_high') return pb - pa;
        if (curSort === 'popular')    return sb - sa;
        if (curSort === 'name')       return na.localeCompare(nb, 'ar');
        return 0;
      });
      visible.forEach(c => grid.appendChild(c));
    }
  }
  // Empty state
  const emp = document.getElementById('emptyProducts');
  if (emp) emp.style.display = visible.length ? 'none' : 'block';
}

/* ── Search Dropdown ── */
function initSearchDropdown() {
  const inp = document.getElementById('searchInput');
  if (!inp) return;
  let dd = document.getElementById('searchDrop');
  if (!dd) {
    dd = document.createElement('div');
    dd.id = 'searchDrop';
    dd.style.cssText = 'position:absolute;top:calc(100% + 4px);right:0;left:0;background:var(--card);border:1px solid var(--border);border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,.2);z-index:999;max-height:320px;overflow-y:auto;display:none';
    inp.closest('.srch-wrap')?.appendChild(dd) || inp.parentElement?.appendChild(dd);
    if(inp.closest('.srch-wrap')) inp.closest('.srch-wrap').style.position='relative';
  }
  inp.addEventListener('input', () => {
    const q = inp.value.trim().toLowerCase();
    curSearch = q;
    applyFilters();
    if (q.length < 1) { dd.style.display = 'none'; return; }
    const prods = window._products || [];
    const results = prods.filter(p => {
      const n = ((p.name_ar||'') + ' ' + (p.name_en||'')).toLowerCase();
      return n.includes(q);
    }).slice(0, 8);
    if (!results.length) { dd.style.display = 'none'; return; }
    dd.innerHTML = results.map(p => {
      const name = document.documentElement.lang === 'ar' ? (p.name_ar||p.name_en) : (p.name_en||p.name_ar);
      return `<div onclick="selectSearchResult(${p.id})" style="display:flex;align-items:center;gap:10px;padding:10px 14px;cursor:pointer;transition:.15s;border-bottom:1px solid var(--border)" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
        <div style="width:34px;height:34px;border-radius:8px;background:linear-gradient(135deg,${p.color1||'#1a1a2e'},${p.color2||'#7c3aed'});display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0">${p.icon||'📦'}</div>
        <div style="flex:1;min-width:0">
          <div style="font-weight:700;font-size:13px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${name}</div>
          <div style="font-size:11px;color:var(--success);font-weight:700">${window._sym||''}${parseFloat(p.price||0).toFixed(2)}</div>
        </div>
      </div>`;
    }).join('');
    dd.style.display = 'block';
  });
  inp.addEventListener('keydown', e => {
    if (e.key === 'Enter') { dd.style.display = 'none'; scrollToProducts(); }
    if (e.key === 'Escape') { dd.style.display = 'none'; inp.value = ''; curSearch = ''; applyFilters(); }
  });
  document.addEventListener('click', e => { if (!inp.contains(e.target) && !dd.contains(e.target)) dd.style.display = 'none'; });
}

function selectSearchResult(pid) {
  const dd = document.getElementById('searchDrop');
  if (dd) dd.style.display = 'none';
  openProduct(pid);
}

/* ── Product Modal ── */
let _openPid = null, _selectedPriceId = null, _selectedPrice = 0;

function openProduct(pid) {
  const p = (window._products||[]).find(x => x.id == pid);
  if (!p) return;
  _openPid = pid;
  _selectedPriceId = null;
  _selectedPrice = parseFloat(p.price) || 0;
  const isAr = document.documentElement.lang === 'ar';
  const name = isAr ? p.name_ar : p.name_en;
  const desc = isAr ? p.description_ar : p.description_en;
  const grad = `linear-gradient(135deg,${p.color1||'#1a1a2e'},${p.color2||'#7c3aed'})`;
  const dl   = p.delivery_type;
  const dlLbl= dl==='instant'?(isAr?'⚡ تسليم فوري':'⚡ Instant'):(dl==='manual'?(isAr?'⏳ يدوي':'⏳ Manual'):(isAr?'📧 إيميل':'📧 Email'));
  document.getElementById('mImg').style.background = grad;
  document.getElementById('mImg').innerHTML = `<div style="font-size:64px;line-height:1">${p.icon||'📦'}</div>`;
  document.getElementById('mTitle').textContent = name || '';
  document.getElementById('mCat').textContent = '';
  document.getElementById('mDesc').textContent = desc || '';
  document.getElementById('mDelivery').innerHTML = `<span style="font-size:11px;background:rgba(124,58,237,.1);color:var(--primary);padding:3px 10px;border-radius:20px;font-weight:700">${dlLbl}</span>`;
  document.getElementById('mProductId').value = pid;
  // Rating
  const rating = parseFloat(p.rating_avg||0);
  const rEl = document.getElementById('mRating');
  if (rEl && rating > 0) { rEl.innerHTML = '⭐'.repeat(Math.round(rating)) + ` <span style="font-size:11px;color:var(--muted)">(${p.rating_count||0})</span>`; }
  else if (rEl) rEl.innerHTML = '';
  // Prices
  const prices = (window._prices||{})[pid] || [];
  const mPrices = document.getElementById('mPrices');
  if (mPrices) {
    if (prices.length) {
      mPrices.innerHTML = prices.map((pr,i) => {
        const lbl = isAr ? pr.label_ar : pr.label_en;
        if (i === 0) { _selectedPriceId = pr.id; _selectedPrice = parseFloat(pr.price); }
        return `<button class="price-opt ${i===0?'selected':''}" id="po-${pr.id}" onclick="selectPrice(${pr.id},${pr.price})">
          <span class="po-lbl">${lbl||''}</span>
          <span class="po-price">${window._sym||''}${parseFloat(pr.price).toFixed(2)}</span>
        </button>`;
      }).join('');
    } else {
      mPrices.innerHTML = `<div class="price-opt selected" style="pointer-events:none">
        <span class="po-lbl">${isAr?'السعر':'Price'}</span>
        <span class="po-price">${window._sym||''}${_selectedPrice.toFixed(2)}</span>
      </div>`;
    }
  }
  // Wishlist btn
  const wb = document.getElementById('mWishBtn');
  if (wb) {
    wb.dataset.id = pid;
    const wl = window._wishlist || [];
    wb.innerHTML = wl.includes(+pid) ? '❤️' : '🤍';
    wb.style.background = wl.includes(+pid) ? 'rgba(239,68,68,.15)' : '';
  }
  // Flags
  const flags = (p.countries||'').split(',').filter(Boolean);
  const fEl = document.getElementById('mFlags');
  if (fEl) fEl.innerHTML = flags.map(f => `<span class="flag-ic">${f.trim()}</span>`).join('');
  document.getElementById('productModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function selectPrice(priceId, price) {
  _selectedPriceId = priceId; _selectedPrice = parseFloat(price);
  document.querySelectorAll('.price-opt').forEach(b => b.classList.remove('selected'));
  const btn = document.getElementById('po-' + priceId);
  if (btn) btn.classList.add('selected');
}

function closeModal(id) {
  const m = id ? document.getElementById(id) : document.getElementById('productModal');
  if (m) { m.classList.remove('open'); if (!id || id==='productModal') document.body.style.overflow = ''; }
}

/* ── Add to Cart ── */
async function addToCart() {
  if (!_openPid) return;
  const btn = document.querySelector('.btn-add-cart');
  if (btn) { btn.disabled = true; btn.innerHTML = '⏳'; }
  try {
    const resp = await fetch('?action=cart_add', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `product_id=${_openPid}&price_id=${_selectedPriceId||0}&price=${_selectedPrice}&csrf=${window._csrf||''}`
    });
    const data = await resp.json();
    if (data.success) {
      showToast(data.msg || (window._t?.added_cart || '✅ أُضيف للسلة'), 'success');
      updateCartBadge(data.count);
      closeModal('productModal');
    } else {
      showToast(data.msg || 'خطأ', 'error');
    }
  } catch(e) { showToast('خطأ في الاتصال', 'error'); }
  if (btn) { btn.disabled = false; btn.innerHTML = '🛒 ' + (window._t?.add_cart || 'إضافة للسلة'); }
}

/* ── Cart Badge ── */
function updateCartBadge(count) {
  document.querySelectorAll('.cart-badge,.cart-count').forEach(el => {
    el.textContent = count || 0;
    el.style.display = count > 0 ? '' : 'none';
  });
}

/* ── Wishlist Toggle ── */
async function toggleWishlist(pid) {
  if (!pid) return;
  const btn = document.getElementById('mWishBtn');
  if (!btn) return;
  try {
    const r = await fetch('?action=wishlist_toggle', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `product_id=${pid}&csrf=${window._csrf||''}`
    });
    const d = await r.json();
    if (d.redirect) { window.location.href = d.redirect; return; }
    if (d.success) {
      if (!window._wishlist) window._wishlist = [];
      if (d.added) { window._wishlist.push(+pid); btn.innerHTML = '❤️'; btn.style.background='rgba(239,68,68,.15)'; }
      else { window._wishlist = window._wishlist.filter(x => x != pid); btn.innerHTML = '🤍'; btn.style.background=''; }
      // Update heart on product card
      const card = document.querySelector(`.prod-card[data-id="${pid}"] .w-badge`);
    }
  } catch(e) {}
}

/* ── Modals ── */
function openModal(id) { document.getElementById(id)?.classList.add('open'); }
function closeAllModals() { document.querySelectorAll('.modal-ov.open').forEach(m=>m.classList.remove('open')); document.body.style.overflow=''; }
document.addEventListener('keydown', e => { if(e.key==='Escape') closeAllModals(); });

/* ── Confirm Delete ── */
function confirmDel(msg) {
  return confirm(msg || (document.documentElement.lang==='ar' ? 'هل أنت متأكد من الحذف؟' : 'Are you sure you want to delete?'));
}

/* ── Admin: Toggle All Checkboxes ── */
function toggleAll(masterCb, cls) {
  document.querySelectorAll('.'+cls).forEach(c => c.checked = masterCb.checked);
  updateBulkBar();
}
function updateBulkBar() {
  const checked = document.querySelectorAll('.row-check:checked');
  const bar = document.getElementById('bulkBar');
  if (bar) bar.style.display = checked.length ? 'flex' : 'none';
  const cnt = document.getElementById('bulkCount');
  if (cnt) cnt.textContent = checked.length;
}


/* ── Filter Featured ── */
function filterFeatured() {
  const cards = document.querySelectorAll('.prod-card');
  cards.forEach(c => {
    c.style.display = (c.dataset.featured === '1') ? '' : 'none';
  });
  const emp = document.getElementById('emptyProducts');
  if (emp) {
    const vis = document.querySelectorAll('.prod-card[data-featured="1"]');
    emp.style.display = vis.length ? 'none' : 'block';
  }
  scrollToProducts();
}

/* ── DOMContentLoaded ── */
document.addEventListener('DOMContentLoaded', () => {
  // Restore scroll
  const main = document.getElementById('mainContent');
  const saved = sessionStorage.getItem('fr_scroll');
  if (saved && main) { main.scrollTop = parseInt(saved); sessionStorage.removeItem('fr_scroll'); }

  // Toast wrap position
  applyToastPosition();

  // Sort select
  const sortSel = document.getElementById('sortSel');
  if (sortSel) sortSel.addEventListener('change', e => sortProducts(e.target.value));

  // Search dropdown
  initSearchDropdown();

  // Product card filter (category clicks in sidebar)
  document.querySelectorAll('.nav-item[data-filter]').forEach(el => {
    el.addEventListener('click', () => {
      const cat = el.dataset.filter || 'all';
      filterCat(cat, el, null);
    });
  });
});

/* ── Apply Toast Position ── */
function applyToastPosition() {
  const wrap = document.getElementById('toastWrap');
  if (!wrap) return;
  const pos = _toastCfg.pos || 'bottom-right';
  wrap.style.cssText = '';
  const base = 'position:fixed;z-index:3000;display:flex;flex-direction:column;gap:8px;max-width:320px;';
  const positions = {
    'bottom-right': base + 'bottom:24px;right:24px;',
    'bottom-left':  base + 'bottom:24px;left:24px;',
    'top-right':    base + 'top:80px;right:24px;',
    'top-left':     base + 'top:80px;left:24px;',
    'top-center':   base + 'top:80px;left:50%;transform:translateX(-50%);',
    'bottom-center':base + 'bottom:24px;left:50%;transform:translateX(-50%);',
  };
  wrap.style.cssText = positions[pos] || positions['bottom-right'];
}
