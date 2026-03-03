'use strict';
// ── Tabs ──
function goStab(t, updateUrl) {
  document.querySelectorAll('.stab').forEach(el => el.classList.remove('active'));
  document.querySelectorAll('.stab-pane').forEach(el => el.classList.remove('active'));
  const btn = document.querySelector('.stab[data-tab="'+t+'"]');
  const pane = document.getElementById('tab-'+t);
  if(btn) btn.classList.add('active');
  if(pane) pane.classList.add('active');
  if(updateUrl !== false) { const u = new URL(window.location); u.searchParams.set('tab',t); window.history.pushState({},'',u); }
}
// ── Modal ──
function openModal(id) { document.getElementById(id)?.classList.add('open'); document.body.style.overflow='hidden'; }
function closeModal(id) { document.getElementById(id)?.classList.remove('open'); document.body.style.overflow=''; }
// ── Color Sync ──
function syncClr(el) { const s=el.nextElementSibling||el.previousElementSibling; if(s) s.value=el.value; }
function syncTxt(el) { const s=el.previousElementSibling||el.nextElementSibling; if(s&&s.type==='color') s.value=el.value; }
// ── Table Filter ──
function filterRows(v, cls) {
  v = v.toLowerCase();
  document.querySelectorAll('.'+cls).forEach(row => {
    row.style.display = (row.dataset.s||row.innerText).toLowerCase().includes(v) ? '' : 'none';
  });
}
// ── Toast ──
function showToast(msg, type='success') {
  const w=document.getElementById('toastWrap'); if(!w) return;
  const el=document.createElement('div'); el.className=`toast ${type}`;
  el.innerHTML=msg+'<button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;font-size:15px;margin-right:auto">×</button>';
  w.appendChild(el);
  requestAnimationFrame(() => el.classList.add('show'));
  setTimeout(() => { el.classList.remove('show'); setTimeout(()=>el.remove(),350); }, 3200);
}
// ── Apply Theme ──
function applyTheme(p,s,a,b,sb,c) {
  ['--primary','--secondary','--accent','--bg','--sidebar','--card'].forEach((v,i) => {
    const col=[p,s,a,b,sb,c][i];
    if(col){
      document.documentElement.style.setProperty(v,col);
      const inps=document.querySelectorAll(`input[name="${v.replace('--','')}"], input[name="${['primary_color','secondary_color','accent_color','bg_dark','bg_sidebar','bg_card'][i]}"]`);
      inps.forEach(inp=>{ inp.value=col; if(inp.nextElementSibling) inp.nextElementSibling.value=col; if(inp.previousElementSibling&&inp.previousElementSibling.type==='color') inp.previousElementSibling.value=col; });
    }
  });
}
// ── Confirm Delete ──
function confirmDel(msg) { return confirm(msg||'هل تريد الحذف؟ لا يمكن التراجع.'); }
// ── AJAX ──
async function ajaxPost(url, data) {
  const fd = new FormData();
  Object.keys(data).forEach(k => fd.append(k, data[k]));
  fd.append('csrf', window._csrf||'');
  const r = await fetch(url, {method:'POST',body:fd});
  return r.json();
}
// ── Init ──
document.addEventListener('DOMContentLoaded', () => {
  // Init tabs from URL
  const tab = new URLSearchParams(window.location.search).get('tab');
  if(tab) goStab(tab, false);
  else {
    const firstTab = document.querySelector('.stab');
    if(firstTab) goStab(firstTab.dataset.tab, false);
  }
  // Modal close on overlay click
  document.querySelectorAll('.modal-ov').forEach(ov => {
    ov.addEventListener('click', e => { if(e.target===ov) closeModal(ov.id); });
  });
});
