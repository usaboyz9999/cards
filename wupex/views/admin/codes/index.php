<?php
$isAr     = Lang::isRtl();
$lang     = Lang::current();
$pid      = Helpers::getInt('product_id');
$priceId  = Helpers::getInt('price_id') ?: null;
$products = Database::fetchAll("SELECT id,name_ar,name_en,icon FROM ".DB_PREFIX."products WHERE status=1 ORDER BY sort_order ASC");
$prices   = $pid ? Database::fetchAll("SELECT * FROM ".DB_PREFIX."product_prices WHERE product_id=? ORDER BY sort_order",[$pid]) : [];
// جلب الأكواد مباشرة
$codesWhere = "product_id=?";
$codesParams = [$pid];
if($priceId) { $codesWhere .= " AND price_id=?"; $codesParams[] = $priceId; }
$codes = $pid ? Database::fetchAll(
    "SELECT c.*,pp.label_ar,pp.label_en FROM ".DB_PREFIX."codes c
     LEFT JOIN ".DB_PREFIX."product_prices pp ON pp.id=c.price_id
     WHERE $codesWhere ORDER BY c.created_at DESC LIMIT 200",
    $codesParams
) : [];
$availCount = $pid ? Database::count('codes', $codesWhere." AND status='available'", $codesParams) : 0;
?>
<div style="display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap;align-items:center">
  <form method="GET" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
    <input type="hidden" name="p" value="codes">
    <select name="product_id" onchange="this.form.submit()" style="min-width:220px">
      <option value=""><?= $isAr?'اختر منتجاً...':'Select product...' ?></option>
      <?php foreach($products as $pr): ?>
      <option value="<?= $pr['id'] ?>" <?= $pid==$pr['id']?'selected':'' ?>><?= $pr['icon'] ?> <?= htmlspecialchars($lang==='ar'?$pr['name_ar']:$pr['name_en']) ?></option>
      <?php endforeach; ?>
    </select>
    <?php if($prices): ?>
    <select name="price_id" onchange="this.form.submit()">
      <option value=""><?= $isAr?'كل الأسعار':'All prices' ?></option>
      <?php foreach($prices as $pr): ?><option value="<?= $pr['id'] ?>" <?= $priceId==$pr['id']?'selected':'' ?>><?= htmlspecialchars($lang==='ar'?$pr['label_ar']:$pr['label_en']) ?></option><?php endforeach; ?>
    </select>
    <?php endif; ?>
  </form>
  <?php if($pid): ?>
  <button class="btn btn-primary" onclick="openModal('importCodesModal')">📤 <?= $isAr?'رفع أكواد':'Import Codes' ?></button>
  <button class="btn btn-secondary" onclick="openModal('addCodeModal')">➕ <?= $isAr?'كود واحد':'Single Code' ?></button>
  <?php endif; ?>
</div>

<?php if($pid): ?>
<div class="tbl-wrap">
  <div class="tbl-hdr">
    <div class="tbl-title">🔑 <?= $isAr?'الأكواد':'Codes' ?>
      <span style="color:var(--success);margin-<?= $isAr?'right':'left' ?>:8px;font-size:12px"><?= $availCount ?> <?= $isAr?'متاح':'available' ?></span>
      <span style="color:var(--muted);font-size:12px">/ <?= count($codes) ?> <?= $isAr?'إجمالي':'total' ?></span>
    </div>
  </div>
  <form method="POST" action="<?= Helpers::siteUrl('admin/') ?>" id="codeBulkForm">
  <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
  <input type="hidden" name="action" value="bulk_delete_codes">
  <input type="hidden" name="product_id" value="<?= $pid ?>">
  <input type="hidden" name="_redirect" value="?p=codes&product_id=<?= $pid ?>">
  <div style="display:flex;gap:8px;margin-bottom:8px;align-items:center">
    <button type="submit" class="btn btn-danger btn-sm" id="codeBulkBtn" style="display:none" onclick="return confirm('حذف المحدد؟')">🗑️ <?= $isAr?'حذف المحدد':'Delete Selected' ?></button>
  </div>
  <table><thead><tr>
    <th style="width:36px"><input type="checkbox" id="codeChkAll" onchange="toggleBulk(this,'code-chk','codeBulkBtn')"></th>
    <th><?= $isAr?'الكود':'Code' ?></th>
    <th><?= $isAr?'السعر':'Price Tier' ?></th>
    <th><?= $isAr?'الحالة':'Status' ?></th>
    <th><?= $isAr?'تاريخ البيع':'Sold At' ?></th>
    <th></th>
  </tr></thead><tbody>
  <?php if(empty($codes)): ?>
  <tr><td colspan="6" style="text-align:center;padding:30px;color:var(--muted)">🔑 <?= $isAr?'لا توجد أكواد بعد':'No codes yet' ?></td></tr>
  <?php else: foreach($codes as $c): ?>
  <tr>
    <td><div style="font-family:monospace;font-size:12px;font-weight:700;background:var(--bg);padding:5px 10px;border-radius:7px;display:inline-block"><?= htmlspecialchars($c['code']) ?></div></td>
    <td style="font-size:12px;color:var(--muted)"><?= htmlspecialchars($lang==='ar'?($c['label_ar']??'—'):($c['label_en']??'—')) ?></td>
    <td><span class="bpill <?= $c['status']==='available'?'bp-active':'bp-out' ?>"><?= $c['status'] ?></span></td>
    <td style="font-size:11px;color:var(--muted)"><?= $c['sold_at']?date('Y-m-d H:i',strtotime($c['sold_at'])):'—' ?></td>
    <td>
      <?php if($c['status']==='available'): ?>
      <form method="POST" style="display:inline" onsubmit="return confirm('<?= $isAr?'حذف الكود؟':'Delete code?' ?>')">
        <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
        <input type="hidden" name="action" value="delete_code">
        <input type="hidden" name="id" value="<?= $c['id'] ?>">
        <input type="hidden" name="product_id" value="<?= $pid ?>">
        <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
      </form>
      <?php endif; ?>
    </td>
  </tr>
  <?php endforeach; endif; ?>
  </tbody></table>
</div>

<!-- Import Modal -->
<div class="modal-ov" id="importCodesModal">
  <div class="modal-box"><div class="modal-hdr"><h3>📤 <?= $isAr?'رفع أكواد':'Import Codes' ?></h3><button class="modal-close" onclick="closeModal('importCodesModal')">×</button></div>
  <form method="POST" action="<?= Helpers::siteUrl('admin/') ?>">
    <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
    <input type="hidden" name="action" value="import_codes">
    <input type="hidden" name="product_id" value="<?= $pid ?>">
    <input type="hidden" name="_redirect" value="?p=codes&product_id=<?= $pid ?>">
  <div class="modal-body">
    <?php if($prices): ?>
    <div class="fg" style="margin-bottom:12px"><label><?= $isAr?'السعر المرتبط':'Price Tier' ?></label>
      <select name="price_id"><option value=""><?= $isAr?'بدون':'No tier' ?></option>
        <?php foreach($prices as $pr): ?><option value="<?= $pr['id'] ?>"><?= htmlspecialchars($lang==='ar'?$pr['label_ar']:$pr['label_en']) ?></option><?php endforeach; ?>
      </select>
    </div>
    <?php endif; ?>
    <div class="fg"><label><?= $isAr?'الأكواد (كل كود في سطر)':'Codes (one per line)' ?></label>
      <textarea name="codes" rows="10" required placeholder="CODE001&#10;CODE002&#10;CODE003" style="min-height:180px;font-family:monospace;font-size:12px"></textarea>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" onclick="closeModal('importCodesModal')"><?= $isAr?'إلغاء':'Cancel' ?></button>
    <button type="submit" class="btn btn-primary">📤 <?= $isAr?'رفع':'Import' ?></button>
  </div>
  </form></div>
</div>

<!-- Single Code Modal -->
<div class="modal-ov" id="addCodeModal">
  <div class="modal-box" style="max-width:400px"><div class="modal-hdr"><h3>➕ <?= $isAr?'إضافة كود':'Add Code' ?></h3><button class="modal-close" onclick="closeModal('addCodeModal')">×</button></div>
  <form method="POST" action="<?= Helpers::siteUrl('admin/') ?>">
    <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
    <input type="hidden" name="action" value="import_codes">
    <input type="hidden" name="product_id" value="<?= $pid ?>">
    <input type="hidden" name="_redirect" value="?p=codes&product_id=<?= $pid ?>">
  <div class="modal-body">
    <?php if($prices): ?>
    <div class="fg" style="margin-bottom:12px"><label>Price Tier</label>
      <select name="price_id"><option value=""><?= $isAr?'بدون':'None' ?></option>
        <?php foreach($prices as $pr): ?><option value="<?= $pr['id'] ?>"><?= htmlspecialchars($lang==='ar'?$pr['label_ar']:$pr['label_en']) ?></option><?php endforeach; ?>
      </select>
    </div>
    <?php endif; ?>
    <div class="fg"><label><?= $isAr?'الكود':'Code' ?></label>
      <input type="text" name="codes" required placeholder="XXXX-XXXX-XXXX" style="font-family:monospace;letter-spacing:1px">
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" onclick="closeModal('addCodeModal')"><?= $isAr?'إلغاء':'Cancel' ?></button>
    <button type="submit" class="btn btn-primary">💾</button>
  </div>
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
</script>
<?php else: ?>
<div style="text-align:center;padding:48px;background:var(--card);border:1px solid var(--border);border-radius:16px">
  <div style="font-size:56px;margin-bottom:14px">🔑</div>
  <h3 style="margin-bottom:8px"><?= $isAr?'اختر منتجاً لعرض أكواده':'Select a product to manage its codes' ?></h3>
</div>
<?php endif; ?>
