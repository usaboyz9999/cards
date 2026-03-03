<?php $isAr=Lang::isRtl(); $lang=Lang::current();
$pages=Database::fetchAll("SELECT * FROM ".DB_PREFIX."pages ORDER BY sort_order ASC");
?>
<div style="display:flex;justify-content:flex-end;margin-bottom:14px">
  <button class="btn btn-primary" onclick="openModal('addPageModal')">➕ <?= $isAr?'إضافة صفحة':'Add Page' ?></button>
</div>
<div class="tbl-wrap"><table><thead><tr>
  <th>Slug</th><th>Title</th><th>Status</th><th></th>
</tr></thead><tbody>
<?php foreach($pages as $pg): ?>
<tr>
  <td style="font-family:monospace;font-weight:700;color:var(--primary)"><?= htmlspecialchars($pg['slug']) ?></td>
  <td><strong><?= htmlspecialchars($lang==='ar'?$pg['title_ar']:$pg['title_en']) ?></strong></td>
  <td><span class="bpill <?= $pg['status']?'bp-active':'bp-inactive' ?>"><?= $pg['status']?'Active':'Off' ?></span></td>
  <td>
    <div style="display:flex;gap:5px">
      <button class="btn btn-sm btn-info" onclick='editPage(<?= json_encode($pg,JSON_UNESCAPED_UNICODE) ?>)'>✏️</button>
      <form method="POST" style="display:inline" onsubmit="return confirmDel()"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="delete_page"><input type="hidden" name="id" value="<?= $pg['id'] ?>"><button type="submit" class="btn btn-sm btn-danger">🗑️</button></form>
    </div>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<div class="modal-ov" id="addPageModal"><div class="modal-box"><div class="modal-hdr"><h3>➕ <?= $isAr?'صفحة جديدة':'New Page' ?></h3><button class="modal-close" onclick="closeModal('addPageModal')">×</button></div>
<form method="POST"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="add_page">
<div class="modal-body"><div class="grid-2">
  <div class="fg full"><label>Slug</label><input type="text" name="slug" required placeholder="about, privacy, terms..."></div>
  <div class="fg"><label>Title AR</label><input type="text" name="title_ar" required></div>
  <div class="fg"><label>Title EN</label><input type="text" name="title_en"></div>
  <div class="fg full"><label>Content AR</label><textarea name="content_ar" style="min-height:120px"></textarea></div>
  <div class="fg full"><label>Content EN</label><textarea name="content_en" style="min-height:120px"></textarea></div>
</div>
<label class="chk-row" style="margin-top:10px"><input type="checkbox" name="status" value="1" checked><span>Active</span></label>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="closeModal('addPageModal')">Cancel</button><button type="submit" class="btn btn-primary">💾</button></div>
</form></div></div>
<div class="modal-ov" id="editPageModal"><div class="modal-box"><div class="modal-hdr"><h3>✏️ Edit Page</h3><button class="modal-close" onclick="closeModal('editPageModal')">×</button></div>
<form method="POST"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="edit_page"><input type="hidden" name="id" id="ep2_id">
<div class="modal-body"><div class="grid-2">
  <div class="fg"><label>Title AR</label><input type="text" name="title_ar" id="ep2_tar"></div>
  <div class="fg"><label>Title EN</label><input type="text" name="title_en" id="ep2_ten"></div>
  <div class="fg full"><label>Content AR</label><textarea name="content_ar" id="ep2_car" style="min-height:120px"></textarea></div>
  <div class="fg full"><label>Content EN</label><textarea name="content_en" id="ep2_cen" style="min-height:120px"></textarea></div>
</div><label class="chk-row" style="margin-top:10px"><input type="checkbox" name="status" id="ep2_st" value="1"><span>Active</span></label></div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="closeModal('editPageModal')">Cancel</button><button type="submit" class="btn btn-primary">💾</button></div>
</form></div></div>
<script>function editPage(p){document.getElementById('ep2_id').value=p.id;document.getElementById('ep2_tar').value=p.title_ar||'';document.getElementById('ep2_ten').value=p.title_en||'';document.getElementById('ep2_car').value=p.content_ar||'';document.getElementById('ep2_cen').value=p.content_en||'';document.getElementById('ep2_st').checked=p.status==1;openModal('editPageModal');}</script>
