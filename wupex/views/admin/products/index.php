<?php
$isAr = Lang::isRtl();
$sym  = htmlspecialchars(Setting::get('currency_symbol','ر.س'));
$cats = Category::all();
$catMap = array_column($cats, null, 'id');

$page = max(1, Helpers::getInt('page',1));
$pp   = 25;
$f    = ['search'=>Helpers::getStr('q'),'category_id'=>Helpers::getInt('cat'),'status'=>$_GET['st']??''];
$data = Product::all($f, $page, $pp);
$products = $data['items'];
$total    = $data['total'];
$pages    = $data['pages'];
$adUrl = Helpers::siteUrl('admin/');
?>

<!-- Bulk Delete Form -->
<form method="POST" action="<?= $adUrl ?>" id="bulkForm">
<input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
<input type="hidden" name="action" value="bulk_delete_products">
<input type="hidden" name="_redirect" value="?p=products">

<!-- Header Actions -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px">
  <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
    <form method="GET" style="display:flex;gap:6px;flex-wrap:wrap">
      <input type="hidden" name="p" value="products">
      <input type="text" name="q" value="<?= htmlspecialchars($f['search']) ?>" placeholder="<?= $isAr?'بحث...':'Search...' ?>" style="width:180px">
      <select name="cat">
        <option value=""><?= $isAr?'كل الفئات':'All Categories' ?></option>
        <?php foreach($cats as $c): ?><option value="<?= $c['id'] ?>" <?= $f['category_id']==$c['id']?'selected':'' ?>><?= htmlspecialchars($isAr?$c['name_ar']:$c['name_en']) ?></option><?php endforeach; ?>
      </select>
      <select name="st">
        <option value=""><?= $isAr?'كل الحالات':'All' ?></option>
        <option value="1" <?= $f['status']==='1'?'selected':'' ?>><?= $isAr?'نشط':'Active' ?></option>
        <option value="0" <?= $f['status']==='0'?'selected':'' ?>><?= $isAr?'معطل':'Disabled' ?></option>
      </select>
      <button type="submit" class="btn btn-secondary">🔍</button>
      <a href="<?= $adUrl ?>?p=products" class="btn btn-secondary">✕</a>
    </form>
  </div>
  <div style="display:flex;gap:8px">
    <button type="submit" form="bulkForm" class="btn btn-danger" id="bulkDelBtn" style="display:none" onclick="return confirm('<?= $isAr?'حذف المحدد؟':'Delete selected?' ?>')">🗑️ <?= $isAr?'حذف المحدد':'Delete Selected' ?></button>
    <span style="font-size:12px;color:var(--muted);align-self:center"><?= $total ?> <?= $isAr?'منتج':'products' ?></span>
    <button type="button" class="btn btn-primary" onclick="openModal('addProductModal')">➕ <?= $isAr?'إضافة منتج':'Add Product' ?></button>
  </div>
</div>

<!-- Products Table -->
<div class="tbl-wrap">
  <table>
    <thead><tr>
      <th style="width:36px"><input type="checkbox" id="chkAll" onchange="toggleBulk(this,'prod-chk','bulkDelBtn')"></th>
      <th><?= $isAr?'المنتج':'Product' ?></th>
      <th><?= $isAr?'الفئة':'Category' ?></th>
      <th><?= $isAr?'السعر':'Price' ?></th>
      <th><?= $isAr?'المبيعات':'Sales' ?></th>
      <th><?= $isAr?'المخزون':'Stock' ?></th>
      <th><?= $isAr?'الحالة':'Status' ?></th>
      <th></th>
    </tr></thead>
    <tbody>
    <?php foreach($products as $p): ?>
    <tr>
      <td><input type="checkbox" name="ids[]" value="<?= $p['id'] ?>" class="prod-chk" onchange="syncBulkBtn('prod-chk','bulkDelBtn')"></td>
      <td>
        <div style="display:flex;align-items:center;gap:10px">
          <div style="width:36px;height:36px;border-radius:9px;background:linear-gradient(135deg,<?= $p['color1'] ?>,<?= $p['color2'] ?>);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0"><?= $p['icon'] ?></div>
          <div>
            <div style="font-weight:700;font-size:13px"><?= htmlspecialchars($isAr?$p['name_ar']:$p['name_en']) ?></div>
            <div style="font-size:10px;color:var(--muted)"><?= htmlspecialchars($isAr?$p['name_en']:$p['name_ar']) ?></div>
          </div>
        </div>
      </td>
      <td style="font-size:12px"><?= htmlspecialchars($catMap[$p['category_id']]['name_ar']??'-') ?></td>
      <td style="font-weight:700;color:var(--success)"><?= $sym ?><?= number_format($p['price'],2) ?><?php if($p['price_max']>0): ?> - <?= $sym ?><?= number_format($p['price_max'],2) ?><?php endif; ?></td>
      <td><?= $p['sales_count'] ?></td>
      <td><?php if($p['delivery_type']==='instant'): ?><span class="bpill <?= $p['stock']?'bp-active':'bp-inactive' ?>"><?= $p['stock']?($isAr?'متوفر':'In Stock'):($isAr?'نفد':'Out') ?></span><?php else: ?><span class="bpill bp-processing"><?= $isAr?'تسليم':'Delivery' ?></span><?php endif; ?></td>
      <td><span class="bpill <?= $p['status']?'bp-active':'bp-inactive' ?>"><?= $p['status']?($isAr?'نشط':'Active'):($isAr?'معطل':'Off') ?></span></td>
      <td>
        <div style="display:flex;gap:4px">
          <button class="btn btn-sm btn-info" onclick='editProduct(<?= json_encode($p,JSON_UNESCAPED_UNICODE) ?>)'>✏️</button>
          <form method="POST" action="<?= $adUrl ?>" style="display:inline" onsubmit="return confirmDel()">
            <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
            <input type="hidden" name="action" value="delete_product">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">
            <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
          </form>
        </div>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

</form><!-- end bulkForm -->

<!-- Pagination -->
<?php if($pages > 1): ?>
<div style="display:flex;gap:6px;justify-content:center;margin-top:14px;flex-wrap:wrap">
  <?php for($i=1;$i<=$pages;$i++): ?>
  <a href="?p=products&page=<?= $i ?>&q=<?= urlencode($f['search']) ?>&cat=<?= $f['category_id'] ?>&st=<?= $f['status'] ?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
  <?php endfor; ?>
</div>
<?php endif; ?>

<!-- Add Product Modal -->
<div class="modal-ov" id="addProductModal">
  <div class="modal-box" style="max-width:700px"><div class="modal-hdr"><h3>➕ <?= $isAr?'إضافة منتج':'Add Product' ?></h3><button class="modal-close" onclick="closeModal('addProductModal')">×</button></div>
  <form method="POST" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="add_product">
  <div class="modal-body">
    <div class="grid-2">
      <div class="fg"><label>Name AR *</label><input type="text" name="name_ar" required></div>
      <div class="fg"><label>Name EN</label><input type="text" name="name_en"></div>
      <div class="fg"><label><?= $isAr?'التصنيف':'Category' ?></label>
        <select name="category_id" required>
          <?php foreach($cats as $c): ?><option value="<?= $c['id'] ?>"><?= htmlspecialchars($isAr?$c['name_ar']:$c['name_en']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="fg"><label><?= $isAr?'أيقونة':'Icon' ?></label><input type="text" name="icon" value="🎮" placeholder="🎮"></div>
      <div class="fg"><label><?= $isAr?'السعر الأدنى':'Min Price' ?></label><input type="number" name="price" step="0.01" required></div>
      <div class="fg"><label><?= $isAr?'السعر الأقصى (0=واحد)':'Max Price (0=single)' ?></label><input type="number" name="price_max" step="0.01" value="0"></div>
      <div class="fg"><label>Color 1</label><div class="cg"><input type="color" name="color1" value="#1a1a2e" oninput="syncClr(this)"><input type="text" value="#1a1a2e" oninput="syncTxt(this)"></div></div>
      <div class="fg"><label>Color 2</label><div class="cg"><input type="color" name="color2" value="#7c3aed" oninput="syncClr(this)"><input type="text" value="#7c3aed" oninput="syncTxt(this)"></div></div>
      <div class="fg"><label><?= $isAr?'طريقة التسليم':'Delivery' ?></label>
        <select name="delivery_type">
          <option value="instant"><?= $isAr?'فوري':'Instant' ?></option>
          <option value="manual"><?= $isAr?'يدوي':'Manual' ?></option>
        </select>
      </div>
      <div class="fg"><label>Badge</label><input type="text" name="badge" placeholder="HOT / NEW"></div>
      <div class="fg"><label><?= $isAr?'الصورة':'Image' ?></label><input type="file" name="image" accept="image/*"></div>
      <div class="fg"><label>Sort</label><input type="number" name="sort_order" value="99"></div>
      <div class="fg full"><label>Description AR</label><textarea name="description_ar" rows="2"></textarea></div>
      <div class="fg full"><label>Description EN</label><textarea name="description_en" rows="2"></textarea></div>
      <div class="fg"><label><input type="checkbox" name="featured"> <?= $isAr?'مميز':'Featured' ?></label></div>
      <div class="fg"><label><input type="checkbox" name="stock" checked> <?= $isAr?'في المخزون':'In Stock' ?></label></div>
      <div class="fg"><label><input type="checkbox" name="status" checked> <?= $isAr?'نشط':'Active' ?></label></div>
    </div>
    <!-- Prices -->
    <div style="margin-top:14px"><label style="font-weight:700;display:block;margin-bottom:8px">💰 <?= $isAr?'أسعار مخصصة (اختياري)':'Custom Prices (optional)' ?></label>
      <div id="priceRows"></div>
      <button type="button" class="btn btn-secondary btn-sm" onclick="addPriceRow()">+ <?= $isAr?'إضافة سعر':'Add Price' ?></button>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" onclick="closeModal('addProductModal')"><?= $isAr?'إلغاء':'Cancel' ?></button>
    <button type="submit" class="btn btn-primary">💾 <?= $isAr?'إضافة':'Add' ?></button>
  </div>
  </form></div>
</div>

<!-- Edit Product Modal -->
<div class="modal-ov" id="editProductModal">
  <div class="modal-box" style="max-width:700px"><div class="modal-hdr"><h3>✏️ <?= $isAr?'تعديل المنتج':'Edit Product' ?></h3><button class="modal-close" onclick="closeModal('editProductModal')">×</button></div>
  <form method="POST" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="edit_product"><input type="hidden" name="id" id="edit_pid">
  <div class="modal-body">
    <div class="grid-2">
      <div class="fg"><label>Name AR *</label><input type="text" name="name_ar" id="edit_name_ar" required></div>
      <div class="fg"><label>Name EN</label><input type="text" name="name_en" id="edit_name_en"></div>
      <div class="fg"><label><?= $isAr?'التصنيف':'Category' ?></label>
        <select name="category_id" id="edit_cat">
          <?php foreach($cats as $c): ?><option value="<?= $c['id'] ?>"><?= htmlspecialchars($isAr?$c['name_ar']:$c['name_en']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="fg"><label>Icon</label><input type="text" name="icon" id="edit_icon"></div>
      <div class="fg"><label>Min Price</label><input type="number" name="price" id="edit_price" step="0.01"></div>
      <div class="fg"><label>Max Price</label><input type="number" name="price_max" id="edit_price_max" step="0.01"></div>
      <div class="fg"><label>Color 1</label><div class="cg"><input type="color" name="color1" id="edit_c1" oninput="syncClr(this)"><input type="text" oninput="syncTxt(this)"></div></div>
      <div class="fg"><label>Color 2</label><div class="cg"><input type="color" name="color2" id="edit_c2" oninput="syncClr(this)"><input type="text" oninput="syncTxt(this)"></div></div>
      <div class="fg"><label>Delivery</label>
        <select name="delivery_type" id="edit_delivery">
          <option value="instant">Instant</option><option value="manual">Manual</option>
        </select>
      </div>
      <div class="fg"><label>Badge</label><input type="text" name="badge" id="edit_badge"></div>
      <div class="fg"><label>Image</label><input type="file" name="image" accept="image/*"></div>
      <div class="fg"><label>Sort</label><input type="number" name="sort_order" id="edit_sort"></div>
      <div class="fg full"><label>Description AR</label><textarea name="description_ar" id="edit_desc_ar" rows="2"></textarea></div>
      <div class="fg full"><label>Description EN</label><textarea name="description_en" id="edit_desc_en" rows="2"></textarea></div>
      <div class="fg"><label><input type="checkbox" name="featured" id="edit_featured"> Featured</label></div>
      <div class="fg"><label><input type="checkbox" name="stock" id="edit_stock"> In Stock</label></div>
      <div class="fg"><label><input type="checkbox" name="status" id="edit_status"> Active</label></div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" onclick="closeModal('editProductModal')">Cancel</button>
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
  const master = document.getElementById('chkAll');
  if(master) { const all=document.querySelectorAll('.'+cls); master.indeterminate = any && document.querySelectorAll('.'+cls+':checked').length < all.length; master.checked = document.querySelectorAll('.'+cls+':checked').length === all.length; }
}
function editProduct(p) {
  document.getElementById('edit_pid').value = p.id;
  document.getElementById('edit_name_ar').value = p.name_ar||'';
  document.getElementById('edit_name_en').value = p.name_en||'';
  document.getElementById('edit_cat').value = p.category_id||'';
  document.getElementById('edit_icon').value = p.icon||'🎮';
  document.getElementById('edit_price').value = p.price||'';
  document.getElementById('edit_price_max').value = p.price_max||0;
  document.getElementById('edit_c1').value = p.color1||'#1a1a2e';
  document.getElementById('edit_c2').value = p.color2||'#7c3aed';
  document.getElementById('edit_delivery').value = p.delivery_type||'instant';
  document.getElementById('edit_badge').value = p.badge||'';
  document.getElementById('edit_sort').value = p.sort_order||99;
  document.getElementById('edit_desc_ar').value = p.description_ar||'';
  document.getElementById('edit_desc_en').value = p.description_en||'';
  document.getElementById('edit_featured').checked = !!+p.featured;
  document.getElementById('edit_stock').checked = !!+p.stock;
  document.getElementById('edit_status').checked = !!+p.status;
  openModal('editProductModal');
}
let priceCount = 0;
function addPriceRow() {
  const i = priceCount++;
  const row = document.createElement('div');
  row.className = 'grid-2';
  row.style.marginBottom = '8px';
  row.innerHTML = `<div class="fg"><label>Label AR</label><input type="text" name="pr_ar[]" placeholder="عادي"></div><div class="fg"><label>Label EN</label><input type="text" name="pr_en[]" placeholder="Standard"></div><div class="fg"><label>Price</label><input type="number" name="pr_price[]" step="0.01"></div><div class="fg" style="align-self:flex-end"><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.grid-2').remove()">✕</button></div>`;
  document.getElementById('priceRows').appendChild(row);
}
</script>
