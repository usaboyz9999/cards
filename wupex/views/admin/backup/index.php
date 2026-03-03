<?php
$isAr  = Lang::isRtl();
$adUrl = Helpers::siteUrl('admin/');
$backups = glob(STORAGE_PATH.'/backups/*.sql') ?: [];
rsort($backups);
$S = Setting::all();

// Table list for selective backup
$tables = ['settings','users','categories','products','product_prices','codes','orders','order_items',
           'carts','wallet_transactions','deposit_requests','coupons','reviews','tickets','ticket_replies',
           'notifications','banners','faqs','pages','visitors','visitor_days','activity_logs','referrals'];
?>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">

  <!-- إنشاء نسخة -->
  <div class="frm-card">
    <h3>💾 <?= $isAr?'إنشاء نسخة احتياطية':'Create Backup' ?></h3>
    <form method="POST" action="<?= $adUrl ?>">
      <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
      <input type="hidden" name="action" value="create_backup">
      <input type="hidden" name="_redirect" value="?p=backup">

      <div class="fg" style="margin-bottom:12px">
        <label><?= $isAr?'نوع النسخ الاحتياطي':'Backup Type' ?></label>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
          <label style="display:flex;align-items:center;gap:6px;cursor:pointer">
            <input type="radio" name="backup_scope" value="full" checked onchange="toggleTableSelect(false)">
            <span style="font-size:13px">🗄️ <?= $isAr?'نسخة كاملة':'Full Backup' ?></span>
          </label>
          <label style="display:flex;align-items:center;gap:6px;cursor:pointer">
            <input type="radio" name="backup_scope" value="selective" onchange="toggleTableSelect(true)">
            <span style="font-size:13px">🔍 <?= $isAr?'انتقائي':'Selective' ?></span>
          </label>
        </div>
      </div>

      <!-- Selective tables -->
      <div id="tableSelect" style="display:none;margin-bottom:14px">
        <div style="display:flex;gap:6px;margin-bottom:8px">
          <button type="button" class="btn btn-sm btn-secondary" onclick="document.querySelectorAll('.tbl-chk').forEach(c=>c.checked=true)"><?= $isAr?'الكل':'All' ?></button>
          <button type="button" class="btn btn-sm btn-secondary" onclick="document.querySelectorAll('.tbl-chk').forEach(c=>c.checked=false)"><?= $isAr?'لا شيء':'None' ?></button>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;max-height:200px;overflow-y:auto;border:1px solid var(--border);border-radius:8px;padding:10px">
          <?php foreach($tables as $tbl): ?>
          <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px">
            <input type="checkbox" name="tables[]" value="<?= $tbl ?>" class="tbl-chk" checked style="accent-color:var(--primary)">
            <?= $tbl ?>
          </label>
          <?php endforeach; ?>
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-full">
        🗄️ <?= $isAr?'إنشاء نسخة الآن':'Create Backup Now' ?>
      </button>
    </form>
  </div>

  <!-- إعدادات النسخ التلقائي -->
  <div class="frm-card">
    <h3>⏰ <?= $isAr?'النسخ التلقائي':'Auto Backup' ?></h3>
    <form method="POST" action="<?= $adUrl ?>">
      <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
      <input type="hidden" name="action" value="save_settings">
      <input type="hidden" name="_redirect" value="?p=backup">
      <div class="fg">
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
          <input type="checkbox" name="auto_backup_enabled" value="1" <?= !empty($S['auto_backup_enabled'])?'checked':'' ?> style="accent-color:var(--primary);width:16px;height:16px">
          <span><?= $isAr?'تفعيل النسخ التلقائي':'Enable Auto Backup' ?></span>
        </label>
      </div>
      <div class="fg">
        <label><?= $isAr?'التكرار':'Frequency' ?></label>
        <select name="auto_backup_freq">
          <option value="daily" <?= ($S['auto_backup_freq']??'weekly')==='daily'?'selected':'' ?>><?= $isAr?'يومي':'Daily' ?></option>
          <option value="weekly" <?= ($S['auto_backup_freq']??'weekly')==='weekly'?'selected':'' ?>><?= $isAr?'أسبوعي':'Weekly' ?></option>
          <option value="monthly" <?= ($S['auto_backup_freq']??'weekly')==='monthly'?'selected':'' ?>><?= $isAr?'شهري':'Monthly' ?></option>
        </select>
      </div>
      <div class="fg">
        <label><?= $isAr?'الاحتفاظ بـ (نسخة)':'Keep last (backups)' ?></label>
        <input type="number" name="auto_backup_keep" value="<?= $S['auto_backup_keep']??5 ?>" min="1" max="30">
      </div>
      <button type="submit" class="btn btn-secondary btn-full">💾 <?= $isAr?'حفظ الإعدادات':'Save Settings' ?></button>
    </form>
  </div>
</div>

<!-- قائمة النسخ -->
<?php if($backups): ?>
<div class="frm-card">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px">
    <h3>📂 <?= $isAr?'النسخ الاحتياطية المحفوظة':'Saved Backups' ?> (<?= count($backups) ?>)</h3>
    <form method="POST" action="<?= $adUrl ?>" onsubmit="return confirmDel('<?= $isAr?'حذف كل النسخ؟':'Delete all backups?' ?>')">
      <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
      <input type="hidden" name="action" value="delete_all_backups">
      <input type="hidden" name="_redirect" value="?p=backup">
      <button type="submit" class="btn btn-sm btn-danger">🗑️ <?= $isAr?'حذف الكل':'Delete All' ?></button>
    </form>
  </div>
  <div class="tbl-wrap"><table><thead><tr>
    <th><?= $isAr?'الملف':'File' ?></th>
    <th><?= $isAr?'الحجم':'Size' ?></th>
    <th><?= $isAr?'التاريخ':'Date' ?></th>
    <th></th>
  </tr></thead><tbody>
  <?php foreach($backups as $b): ?>
  <tr>
    <td style="font-family:monospace;font-size:11px">📄 <?= basename($b) ?></td>
    <td style="font-size:12px"><?= round(filesize($b)/1024,1) ?> KB</td>
    <td style="font-size:11px;color:var(--muted)"><?= date('Y-m-d H:i',filemtime($b)) ?></td>
    <td>
      <div style="display:flex;gap:5px">
        <a href="<?= $adUrl ?>?p=backup&dl=<?= urlencode(basename($b)) ?>" class="btn btn-sm btn-success">⬇️ <?= $isAr?'تحميل':'Download' ?></a>
        <form method="POST" action="<?= $adUrl ?>" style="display:inline" onsubmit="return confirmDel()">
          <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
          <input type="hidden" name="action" value="delete_backup">
          <input type="hidden" name="filename" value="<?= htmlspecialchars(basename($b)) ?>">
          <input type="hidden" name="_redirect" value="?p=backup">
          <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
        </form>
      </div>
    </td>
  </tr>
  <?php endforeach; ?>
  </tbody></table></div>
</div>
<?php else: ?>
<div class="frm-card" style="text-align:center;padding:32px">
  <div style="font-size:40px;margin-bottom:12px">💾</div>
  <p style="color:var(--muted)"><?= $isAr?'لا توجد نسخ احتياطية بعد. أنشئ نسخة الآن.':'No backups yet. Create one now.' ?></p>
</div>
<?php endif; ?>

<script>
function toggleTableSelect(show) {
  document.getElementById('tableSelect').style.display = show ? 'block' : 'none';
}
</script>
