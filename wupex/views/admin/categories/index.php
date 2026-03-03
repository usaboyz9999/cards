<?php
$isAr = Lang::isRtl();
$cats = Category::withCount();
$adUrl = Helpers::siteUrl('admin/');
?>
<form method="POST" action="<?= $adUrl ?>" id="catBulkForm">
<input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
<input type="hidden" name="action" value="bulk_delete_categories">
<input type="hidden" name="_redirect" value="?p=categories">

<div style="display:flex;justify-content:flex-end;gap:8px;margin-bottom:14px;flex-wrap:wrap">
  <button type="submit" form="catBulkForm" class="btn btn-danger" id="catBulkBtn" style="display:none" onclick="return confirm('<?= $isAr?'حذف المحدد؟':'Delete selected?' ?>')">🗑️ <?= $isAr?'حذف المحدد':'Delete Selected' ?></button>
  <button type="button" class="btn btn-primary" onclick="openModal('addCatModal')">➕ <?= $isAr?'إضافة تصنيف':'Add Category' ?></button>
</div>

<div class="tbl-wrap"><table><thead><tr>
  <th style="width:36px"><input type="checkbox" id="catChkAll" onchange="toggleBulk(this,'cat-chk','catBulkBtn')"></th>
  <th style="width:40px">#</th>
  <th><?= $isAr?'التصنيف':'Category' ?></th>
  <th><?= $isAr?'المنتجات':'Products' ?></th>
  <th><?= $isAr?'الترتيب':'Order' ?></th>
  <th><?= $isAr?'الحالة':'Status' ?></th>
  <th></th>
</tr></thead><tbody>
<?php foreach($cats as $c): ?>
<tr>
  <td><input type="checkbox" name="ids[]" value="<?= $c['id'] ?>" class="cat-chk" onchange="syncBulkBtn('cat-chk','catBulkBtn')"></td>
  <td style="color:var(--muted);font-size:11px"><?= $c['id'] ?></td>
  <td>
    <div style="display:flex;align-items:center;gap:10px">
      <div style="width:38px;height:38px;border-radius:9px;background:linear-gradient(135deg,<?= $c['color1'] ?>,<?= $c['color2'] ?>);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0"><?= $c['icon'] ?></div>
      <div>
        <div style="font-weight:700"><?= htmlspecialchars($isAr?$c['name_ar']:$c['name_en']) ?></div>
        <div style="font-size:10px;color:var(--muted)"><?= htmlspecialchars($isAr?$c['name_en']:$c['name_ar']) ?></div>
      </div>
    </div>
  </td>
  <td><span style="font-weight:700;color:var(--primary)"><?= $c['product_count'] ?? 0 ?></span></td>
  <td><?= $c['sort_order'] ?></td>
  <td><span class="bpill <?= $c['status']?'bp-active':'bp-inactive' ?>"><?= $c['status']?($isAr?'نشط':'Active'):($isAr?'معطل':'Disabled') ?></span></td>
  <td>
    <div style="display:flex;gap:5px">
      <button class="btn btn-sm btn-info" onclick='editCat(<?= json_encode($c,JSON_UNESCAPED_UNICODE) ?>)'>✏️</button>
      <form method="POST" action="<?= $adUrl ?>" style="display:inline" onsubmit="return confirmDel()">
        <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
        <input type="hidden" name="action" value="delete_category">
        <input type="hidden" name="id" value="<?= $c['id'] ?>">
        <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
      </form>
    </div>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
</form>

<!-- Add Modal -->
<div class="modal-ov" id="addCatModal">
  <div class="modal-box"><div class="modal-hdr"><h3>➕ <?= $isAr?'إضافة تصنيف':'Add Category' ?></h3><button class="modal-close" onclick="closeModal('addCatModal')">×</button></div>
  <form method="POST" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="add_category">
  <div class="modal-body">
    <div class="grid-2">
      <div class="fg"><label>Name AR</label><input type="text" name="name_ar" required></div>
      <div class="fg"><label>Name EN</label><input type="text" name="name_en" required></div>
      <div class="fg"><label><?= $isAr?'الأيقونة':'Icon' ?></label><input type="text" name="icon" value="📦" placeholder="📦"></div>
      <div class="fg"><label>Sort Order</label><input type="number" name="sort_order" value="99"></div>
      <div class="fg"><label>Color 1</label><div class="cg"><input type="color" name="color1" value="#1a1a2e" oninput="syncClr(this)"><input type="text" value="#1a1a2e" oninput="syncTxt(this)"></div></div>
      <div class="fg"><label>Color 2</label><div class="cg"><input type="color" name="color2" value="#7c3aed" oninput="syncClr(this)"><input type="text" value="#7c3aed" oninput="syncTxt(this)"></div></div>
      <div class="fg full"><label>Description AR</label><textarea name="description_ar"></textarea></div>
      <div class="fg full"><label>Description EN</label><textarea name="description_en"></textarea></div>
      <div class="fg"><label><input type="checkbox" name="featured"> <?= $isAr?'مميز':'Featured' ?></label></div>
      <div class="fg"><label><input type="checkbox" name="status" checked> <?= $isAr?'نشط':'Active' ?></label></div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" onclick="closeModal('addCatModal')"><?= $isAr?'إلغاء':'Cancel' ?></button>
    <button type="submit" class="btn btn-primary">💾 <?= $isAr?'إضافة':'Add' ?></button>
  </div>
  </form></div>
</div>

<!-- Edit Modal -->
<div class="modal-ov" id="editCatModal">
  <div class="modal-box"><div class="modal-hdr"><h3>✏️ <?= $isAr?'تعديل التصنيف':'Edit Category' ?></h3><button class="modal-close" onclick="closeModal('editCatModal')">×</button></div>
  <form method="POST" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="edit_category"><input type="hidden" name="id" id="ec_id">
  <div class="modal-body">
    <div class="grid-2">
      <div class="fg"><label>Name AR</label><input type="text" name="name_ar" id="ec_nar" required></div>
      <div class="fg"><label>Name EN</label><input type="text" name="name_en" id="ec_nen" required></div>
      <div class="fg"><label>Icon</label><input type="text" name="icon" id="ec_icon"></div>
      <div class="fg"><label>Sort</label><input type="number" name="sort_order" id="ec_sort"></div>
      <div class="fg"><label>Color 1</label><div class="cg"><input type="color" name="color1" id="ec_c1" oninput="syncClr(this)"><input type="text" oninput="syncTxt(this)"></div></div>
      <div class="fg"><label>Color 2</label><div class="cg"><input type="color" name="color2" id="ec_c2" oninput="syncClr(this)"><input type="text" oninput="syncTxt(this)"></div></div>
      <div class="fg"><label><input type="checkbox" name="status" id="ec_status"> Active</label></div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" onclick="closeModal('editCatModal')">Cancel</button>
    <button type="submit" class="btn btn-primary">💾 Save</button>
  </div>
  </form></div>
</div>

<script>
function toggleBulk(master, cls, btnId) {
  document.querySelectorAll('.'+cls).forEach(c => c.checked = master.checked);
  const btn = document.getElementById(btnId);
  if(btn) btn.style.display = master.checked ? '' : 'none';
}
function syncBulkBtn(cls, btnId) {
  const any = document.querySelectorAll('.'+cls+':checked').length > 0;
  const btn = document.getElementById(btnId);
  if(btn) btn.style.display = any ? '' : 'none';
}
function editCat(c) {
  document.getElementById('ec_id').value = c.id;
  document.getElementById('ec_nar').value = c.name_ar||'';
  document.getElementById('ec_nen').value = c.name_en||'';
  document.getElementById('ec_icon').value = c.icon||'📦';
  document.getElementById('ec_sort').value = c.sort_order||99;
  document.getElementById('ec_c1').value = c.color1||'#1a1a2e';
  document.getElementById('ec_c2').value = c.color2||'#7c3aed';
  document.getElementById('ec_status').checked = !!+c.status;
  openModal('editCatModal');
}
</script>
