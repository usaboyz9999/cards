<?php $isAr=Lang::isRtl(); $lang=Lang::current();
$metas=Database::fetchAll("SELECT * FROM ".DB_PREFIX."seo_metas ORDER BY page_slug ASC");
?>
<div style="display:flex;justify-content:flex-end;margin-bottom:14px">
  <button class="btn btn-primary" onclick="openModal('addSeoModal')">➕ <?= $isAr?'إضافة صفحة':'Add Page' ?></button>
</div>
<div class="tbl-wrap"><table><thead><tr>
  <th>Slug</th><th>Title</th><th>Description</th><th></th>
</tr></thead><tbody>
<?php foreach($metas as $m): ?>
<tr>
  <td style="font-family:monospace;font-weight:700;color:var(--primary)"><?= htmlspecialchars($m['page_slug']) ?></td>
  <td style="font-size:12px"><?= htmlspecialchars(mb_substr($lang==='ar'?$m['meta_title_ar']:$m['meta_title_en'],0,50)) ?></td>
  <td style="font-size:11px;color:var(--muted)"><?= htmlspecialchars(mb_substr($lang==='ar'?$m['meta_desc_ar']:$m['meta_desc_en'],0,60)) ?></td>
  <td><form method="POST" style="display:inline" onsubmit="return confirmDel()"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="delete_seo"><input type="hidden" name="id" value="<?= $m['id'] ?>"><button type="submit" class="btn btn-sm btn-danger">🗑️</button></form></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<div class="modal-ov" id="addSeoModal"><div class="modal-box"><div class="modal-hdr"><h3>➕ SEO Meta</h3><button class="modal-close" onclick="closeModal('addSeoModal')">×</button></div>
<form method="POST"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="save_seo_meta">
<div class="modal-body">
  <div class="grid-2">
    <div class="fg full"><label>Page Slug</label><input type="text" name="page_slug" required placeholder="home, products, about..."></div>
    <div class="fg"><label>Meta Title AR</label><input type="text" name="meta_title_ar"></div>
    <div class="fg"><label>Meta Title EN</label><input type="text" name="meta_title_en"></div>
    <div class="fg full"><label>Meta Desc AR</label><textarea name="meta_desc_ar"></textarea></div>
    <div class="fg full"><label>Meta Desc EN</label><textarea name="meta_desc_en"></textarea></div>
  </div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="closeModal('addSeoModal')">Cancel</button><button type="submit" class="btn btn-primary">💾</button></div>
</form></div></div>
