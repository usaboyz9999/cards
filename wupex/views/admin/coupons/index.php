<?php
$isAr  = Lang::isRtl();
$sym   = htmlspecialchars(Setting::get('currency_symbol','ر.س'));
$adUrl = Helpers::siteUrl('admin/');
$data  = Coupon::all();
$coupons = $data['items'];

// Edit mode
$editId   = Helpers::getInt('edit');
$editCoup = $editId ? Database::fetch("SELECT * FROM ".DB_PREFIX."coupons WHERE id=?",[$editId]) : null;
?>
<div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;align-items:center">
  <span style="font-size:12px;color:var(--muted)"><?= count($coupons) ?> <?= $isAr?'كوبون':'coupons' ?></span>
  <button type="button" class="btn btn-danger" id="cpnBulkBtn" style="display:none;margin-<?= $isAr?'right':'left' ?>:auto" onclick="submitCouponBulk()">🗑️ <?= $isAr?'حذف المحدد':'Delete Selected' ?></button>
  <button class="btn btn-primary" onclick="openModal('addCouponModal')" style="margin-<?= $isAr?'right':'left' ?>:auto">➕ <?= $isAr?'إضافة كوبون':'Add Coupon' ?></button>
</div>

<!-- Edit Modal (pre-filled if editing) -->
<?php if($editCoup): ?>
<div class="frm-card" style="margin-bottom:16px;border:1px solid var(--primary)">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
    <h3>✏️ <?= $isAr?'تعديل الكوبون':'Edit Coupon' ?>: <span style="color:var(--primary);font-family:monospace"><?= htmlspecialchars($editCoup['code']) ?></span></h3>
    <a href="<?= $adUrl ?>?p=coupons" class="btn btn-sm btn-secondary">✕ <?= $isAr?'إلغاء':'Cancel' ?></a>
  </div>
  <form method="POST" action="<?= $adUrl ?>">
    <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
    <input type="hidden" name="action" value="edit_coupon">
    <input type="hidden" name="id" value="<?= $editCoup['id'] ?>">
    <input type="hidden" name="_redirect" value="?p=coupons">
    <div class="grid-2">
      <div class="fg"><label><?= $isAr?'الكود':'Code' ?></label><input type="text" name="code" value="<?= htmlspecialchars($editCoup['code']) ?>" required style="text-transform:uppercase"></div>
      <div class="fg"><label><?= $isAr?'النوع':'Type' ?></label>
        <select name="type">
          <option value="percent" <?= $editCoup['type']==='percent'?'selected':'' ?>>% <?= $isAr?'نسبة':'Percent' ?></option>
          <option value="fixed" <?= $editCoup['type']==='fixed'?'selected':'' ?>><?= $isAr?'مبلغ ثابت':'Fixed Amount' ?></option>
          <option value="free_shipping" <?= $editCoup['type']==='free_shipping'?'selected':'' ?>><?= $isAr?'شحن مجاني':'Free Shipping' ?></option>
        </select>
      </div>
      <div class="fg"><label><?= $isAr?'القيمة':'Value' ?></label><input type="number" name="value" step="0.01" value="<?= $editCoup['value'] ?>" required></div>
      <div class="fg"><label><?= $isAr?'الحد الأدنى للطلب':'Min Order' ?></label><input type="number" name="min_order" step="0.01" value="<?= $editCoup['min_order'] ?>"></div>
      <div class="fg"><label><?= $isAr?'حد أقصى للخصم (0 = بلا حد)':'Max Discount' ?></label><input type="number" name="max_discount" step="0.01" value="<?= $editCoup['max_discount'] ?>"></div>
      <div class="fg"><label><?= $isAr?'أقصى عدد استخدام (0 = لانهائي)':'Max Uses' ?></label><input type="number" name="max_uses" value="<?= $editCoup['max_uses'] ?>"></div>
      <div class="fg"><label><?= $isAr?'يبدأ في':'Starts At' ?></label><input type="date" name="starts_at" value="<?= $editCoup['starts_at']?date('Y-m-d',strtotime($editCoup['starts_at'])):'' ?>"></div>
      <div class="fg"><label><?= $isAr?'ينتهي في':'Expires At' ?></label><input type="date" name="expires_at" value="<?= $editCoup['expires_at']?date('Y-m-d',strtotime($editCoup['expires_at'])):'' ?>"></div>
      <div class="fg"><label><?= $isAr?'الحالة':'Status' ?></label>
        <select name="status">
          <option value="1" <?= $editCoup['status']?'selected':'' ?>><?= $isAr?'نشط':'Active' ?></option>
          <option value="0" <?= !$editCoup['status']?'selected':'' ?>><?= $isAr?'معطل':'Disabled' ?></option>
        </select>
      </div>
      <div class="fg"><label><?= $isAr?'ملاحظات':'Notes' ?></label><input type="text" name="notes" value="<?= htmlspecialchars($editCoup['notes']??'') ?>" placeholder="<?= $isAr?'اختياري':'Optional' ?>"></div>
    </div>
    <button type="submit" class="btn btn-primary">💾 <?= $isAr?'حفظ التعديلات':'Save Changes' ?></button>
  </form>
</div>
<?php endif; ?>

<form method="POST" action="<?= $adUrl ?>" id="cpnBulkForm"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="bulk_delete_coupons"><input type="hidden" name="_redirect" value="?p=coupons">
<div class="tbl-wrap"><table><thead><tr>
  <th style="width:36px"><input type="checkbox" id="cpnChkAll" onchange="toggleBulk(this,'row-check','cpnBulkBtn')"></th>
  <th><?= $isAr?'الكود':'Code' ?></th>
  <th><?= $isAr?'النوع':'Type' ?></th>
  <th><?= $isAr?'القيمة':'Value' ?></th>
  <th><?= $isAr?'الحد الأدنى':'Min' ?></th>
  <th><?= $isAr?'الاستخدام':'Uses' ?></th>
  <th><?= $isAr?'البداية':'Starts' ?></th>
  <th><?= $isAr?'الانتهاء':'Expires' ?></th>
  <th><?= $isAr?'الحالة':'Status' ?></th>
  <th></th>
</tr></thead><tbody>
<?php foreach($coupons as $c): ?>
<tr>
  <td><input type="checkbox" name="ids[]" class="row-check" value="<?= $c['id'] ?>" onchange="syncBulkBtn('row-check','cpnBulkBtn')"></td>
  <td>
    <div style="display:flex;align-items:center;gap:8px">
      <strong style="font-family:monospace;font-size:13px;letter-spacing:1px;color:var(--primary)"><?= htmlspecialchars($c['code']) ?></strong>
      <button onclick="navigator.clipboard.writeText('<?= htmlspecialchars($c['code']) ?>').then(()=>showToast('✅ Copied','success'))" style="background:none;border:none;cursor:pointer;opacity:.5;font-size:12px" title="Copy">📋</button>
    </div>
    <?php if(!empty($c['notes'])): ?><div style="font-size:10px;color:var(--muted)"><?= htmlspecialchars($c['notes']) ?></div><?php endif; ?>
  </td>
  <td><span class="bpill bp-top"><?= $c['type'] ?></span></td>
  <td style="font-weight:700"><?= $c['type']==='percent'?$c['value'].'%':($c['type']==='fixed'?$sym.$c['value']:'🚚') ?></td>
  <td style="font-size:12px"><?= $c['min_order']>0?$sym.$c['min_order']:'-' ?></td>
  <td style="font-size:13px">
    <span style="color:var(--primary);font-weight:700"><?= $c['used_count'] ?></span>
    <span style="color:var(--muted)"> / <?= $c['max_uses']>0?$c['max_uses']:'∞' ?></span>
  </td>
  <td style="font-size:11px;color:var(--muted)"><?= $c['starts_at']?date('Y-m-d',strtotime($c['starts_at'])):'-' ?></td>
  <td style="font-size:11px;color:<?= ($c['expires_at']&&strtotime($c['expires_at'])<time())?'var(--danger)':'var(--muted)' ?>">
    <?= $c['expires_at']?date('Y-m-d',strtotime($c['expires_at'])):'∞' ?>
  </td>
  <td>
    <form method="POST" action="<?= $adUrl ?>" style="display:inline">
      <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
      <input type="hidden" name="action" value="toggle_coupon">
      <input type="hidden" name="id" value="<?= $c['id'] ?>">
      <button type="submit" class="bpill <?= $c['status']?'bp-active':'bp-inactive' ?>" style="cursor:pointer;border:none">
        <?= $c['status']?($isAr?'نشط':'Active'):($isAr?'معطل':'Off') ?>
      </button>
    </form>
  </td>
  <td>
    <div style="display:flex;gap:4px">
      <a href="<?= $adUrl ?>?p=coupons&edit=<?= $c['id'] ?>" class="btn btn-sm btn-info">✏️</a>
      <form method="POST" action="<?= $adUrl ?>" style="display:inline" onsubmit="return confirmDel()">
        <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
        <input type="hidden" name="action" value="delete_coupon">
        <input type="hidden" name="id" value="<?= $c['id'] ?>">
        <input type="hidden" name="_redirect" value="?p=coupons">
        <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
      </form>
    </div>
  </td>
</tr>
<?php endforeach; if(empty($coupons)): ?>
<tr><td colspan="10" style="text-align:center;padding:24px;color:var(--muted)">🏷️ <?= $isAr?'لا توجد كوبونات':'No coupons' ?></td></tr>
<?php endif; ?>
</tbody></table></div>

<!-- Add Coupon Modal -->
<div class="modal-ov" id="addCouponModal">
  <div class="modal-box"><div class="modal-hdr"><h3>➕ <?= $isAr?'إضافة كوبون':'Add Coupon' ?></h3><button class="modal-close" onclick="closeModal('addCouponModal')">×</button></div>
  <form method="POST" action="<?= $adUrl ?>"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="add_coupon"><input type="hidden" name="_redirect" value="?p=coupons">
  <div class="modal-body">
    <div class="grid-2">
      <div class="fg"><label><?= $isAr?'الكود':'Code' ?> *</label><input type="text" name="code" required style="text-transform:uppercase" placeholder="SAVE20"></div>
      <div class="fg"><label><?= $isAr?'النوع':'Type' ?></label><select name="type"><option value="percent">% <?= $isAr?'نسبة':'Percent' ?></option><option value="fixed"><?= $isAr?'مبلغ ثابت':'Fixed' ?></option><option value="free_shipping"><?= $isAr?'شحن مجاني':'Free Ship' ?></option></select></div>
      <div class="fg"><label><?= $isAr?'القيمة':'Value' ?> *</label><input type="number" name="value" step="0.01" required></div>
      <div class="fg"><label><?= $isAr?'الحد الأدنى':'Min Order' ?></label><input type="number" name="min_order" step="0.01" value="0"></div>
      <div class="fg"><label><?= $isAr?'حد الخصم':'Max Discount' ?></label><input type="number" name="max_discount" step="0.01" value="0"></div>
      <div class="fg"><label><?= $isAr?'أقصى استخدام':'Max Uses' ?></label><input type="number" name="max_uses" value="0" placeholder="0=∞"></div>
      <div class="fg"><label><?= $isAr?'يبدأ':'Starts' ?></label><input type="date" name="starts_at"></div>
      <div class="fg"><label><?= $isAr?'ينتهي':'Expires' ?></label><input type="date" name="expires_at"></div>
      <div class="fg full"><label><?= $isAr?'ملاحظات':'Notes' ?></label><input type="text" name="notes" placeholder="<?= $isAr?'وصف اختياري':'Optional description' ?>"></div>
    </div>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="closeModal('addCouponModal')"><?= $isAr?'إلغاء':'Cancel' ?></button><button type="submit" class="btn btn-primary">💾 <?= $isAr?'حفظ':'Save' ?></button></div>
  </form></div>
</div>

<script>
function toggleBulk(master, cls, btnId) {
  document.querySelectorAll('.'+cls).forEach(x => x.checked = master.checked);
  const btn = document.getElementById(btnId);
  if(btn) btn.style.display = master.checked ? '' : 'none';
}
function syncBulkBtn(cls, btnId) {
  const any = document.querySelectorAll('.'+cls+':checked').length > 0;
  const btn = document.getElementById(btnId);
  if(btn) btn.style.display = any ? '' : 'none';
}
function submitCouponBulk() {
  if(!confirm('<?php echo $isAr?'حذف الكوبونات المحددة؟':'Delete selected coupons?'; ?>')) return;
  // Copy checked values to form
  const form = document.getElementById('cpnBulkForm');
  document.querySelectorAll('.row-check:checked').forEach(cb => {
    const inp = document.createElement('input');
    inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = cb.value;
    form.appendChild(inp);
  });
  form.submit();
}
</script>