<?php
$isAr  = Lang::isRtl();
$adUrl = Helpers::siteUrl('admin/');
$tab   = Helpers::getStr('tab','home');

// Stats
$todayHome  = Visitor::todayCount();
$monthHome  = Visitor::monthCount();
$totalHome  = Database::count('visitors','is_home=1');
$totalAll   = Database::count('visitors');
?>
<!-- Tabs -->
<div style="display:flex;gap:4px;margin-bottom:16px;background:var(--card);border:1px solid var(--border);border-radius:10px;padding:5px">
  <?php foreach(['home'=>'🏠 '.($isAr?'الرئيسية (فريد)':'Home (Unique)'), 'all'=>'📋 '.($isAr?'جميع الصفحات':'All Pages'), 'devices'=>'🖥️ '.($isAr?'أسماء الأجهزة':'Device Names')] as $t=>$lbl): ?>
  <a href="<?= $adUrl ?>?p=visitors&tab=<?= $t ?>" style="padding:7px 14px;border-radius:7px;font-size:12px;font-weight:700;text-decoration:none;transition:.2s;<?= $tab===$t?'background:var(--primary);color:#fff':'color:var(--muted)' ?>"><?= $lbl ?></a>
  <?php endforeach; ?>
</div>

<!-- Stats Row -->
<div class="stats-row" style="margin-bottom:16px">
  <div class="stat-c"><div class="stat-ic">🏠</div><div class="stat-num"><?= $todayHome ?></div><div class="stat-lbl"><?= $isAr?'زيارات الرئيسية اليوم':'Home Today' ?></div></div>
  <div class="stat-c"><div class="stat-ic">📅</div><div class="stat-num"><?= $monthHome ?></div><div class="stat-lbl"><?= $isAr?'هذا الشهر (رئيسية)':'Home This Month' ?></div></div>
  <div class="stat-c"><div class="stat-ic">📊</div><div class="stat-num"><?= $totalAll ?></div><div class="stat-lbl"><?= $isAr?'إجمالي كل الصفحات':'Total Page Views' ?></div></div>
  <div class="stat-c"><div class="stat-ic">🌐</div><div class="stat-num"><?= Database::count('visitor_days') ?></div><div class="stat-lbl"><?= $isAr?'إجمالي زوار فريدون':'Unique Visitors' ?></div></div>
</div>

<?php if($tab === 'home'): ?>
<!-- زيارات الرئيسية - زيارة واحدة لكل IP يومياً -->
<?php
$days = Database::fetchAll("
    SELECT vd.*, d.device_name
    FROM ".DB_PREFIX."visitor_days vd
    LEFT JOIN ".DB_PREFIX."ip_devices d ON d.ip_address = vd.ip_address
    ORDER BY vd.created_at DESC LIMIT 200
");
?>
<div style="display:flex;gap:8px;margin-bottom:10px;flex-wrap:wrap;align-items:center">
  <span style="font-size:12px;color:var(--muted)"><?= count($days) ?> <?= $isAr?'زيارة':'visits' ?></span>
  <form method="POST" action="<?= $adUrl ?>" style="margin-<?= $isAr?'right':'left' ?>:auto" onsubmit="return confirmDel('<?= $isAr?'مسح كل سجل الزوار؟':'Clear all visitor log?' ?>')">
    <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
    <input type="hidden" name="action" value="clear_visitors">
    <input type="hidden" name="_redirect" value="?p=visitors&tab=home">
    <button type="submit" class="btn btn-sm btn-danger">🗑️ <?= $isAr?'مسح الكل':'Clear All' ?></button>
  </form>
</div>
<div class="tbl-wrap"><table><thead><tr>
  <th>IP</th>
  <th><?= $isAr?'اسم الجهاز':'Device Name' ?></th>
  <th><?= $isAr?'المتصفح':'Browser' ?></th>
  <th><?= $isAr?'التاريخ':'Date' ?></th>
  <th><?= $isAr?'المستخدم':'User' ?></th>
  <th></th>
</tr></thead><tbody>
<?php foreach($days as $v):
  $ua = $v['user_agent'] ?? '';
  $browser = str_contains($ua,'Firefox')?'Firefox':(str_contains($ua,'Chrome')?'Chrome':(str_contains($ua,'Safari')?'Safari':(str_contains($ua,'Edge')?'Edge':'Other')));
  $device  = str_contains($ua,'Mobile')?'📱':(str_contains($ua,'Tablet')?'📲':'🖥️');
?>
<tr>
  <td style="font-family:monospace;font-size:12px;font-weight:600"><?= htmlspecialchars($v['ip_address']) ?></td>
  <td>
    <?php if($v['device_name']): ?>
    <span style="color:var(--primary);font-weight:700;font-size:12px">🏷️ <?= htmlspecialchars($v['device_name']) ?></span>
    <?php else: ?>
    <span style="color:var(--muted);font-size:11px"><?= $isAr?'غير محدد':'Unset' ?></span>
    <?php endif; ?>
  </td>
  <td style="font-size:11px;color:var(--muted)"><?= $device ?> <?= $browser ?></td>
  <td style="font-size:11px;color:var(--muted)"><?= date('Y-m-d',strtotime($v['created_at'])) ?></td>
  <td style="font-size:11px;color:var(--muted)"><?= $v['user_id']?'👤 #'.$v['user_id']:'-' ?></td>
  <td>
    <button onclick="setDeviceName('<?= htmlspecialchars($v['ip_address'],ENT_QUOTES) ?>','<?= htmlspecialchars($v['device_name']??'',ENT_QUOTES) ?>')" class="btn btn-sm btn-secondary">🏷️</button>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>

<?php elseif($tab === 'all'): ?>
<!-- جميع زيارات الصفحات -->
<?php
$allVisits = Database::fetchAll("
    SELECT v.*, d.device_name
    FROM ".DB_PREFIX."visitors v
    LEFT JOIN ".DB_PREFIX."ip_devices d ON d.ip_address = v.ip_address
    ORDER BY v.visited_at DESC LIMIT 300
");
?>
<div style="display:flex;gap:8px;margin-bottom:10px;align-items:center;flex-wrap:wrap">
  <span style="font-size:12px;color:var(--muted)"><?= $isAr?'آخر 300 زيارة':'Last 300 page views' ?></span>
  <form method="POST" action="<?= $adUrl ?>" style="margin-<?= $isAr?'right':'left' ?>:auto" onsubmit="return confirmDel()">
    <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
    <input type="hidden" name="action" value="clear_all_visitors">
    <input type="hidden" name="_redirect" value="?p=visitors&tab=all">
    <button type="submit" class="btn btn-sm btn-danger">🗑️ <?= $isAr?'مسح الكل':'Clear All' ?></button>
  </form>
</div>
<div class="tbl-wrap"><table><thead><tr>
  <th>IP</th>
  <th><?= $isAr?'اسم الجهاز':'Device' ?></th>
  <th>URL</th>
  <th><?= $isAr?'المتصفح':'Browser' ?></th>
  <th><?= $isAr?'الوقت':'Time' ?></th>
</tr></thead><tbody>
<?php foreach($allVisits as $v):
  $ua = $v['user_agent']??'';
  $browser = str_contains($ua,'Firefox')?'Firefox':(str_contains($ua,'Chrome')?'Chrome':(str_contains($ua,'Safari')?'Safari':'Other'));
  $device  = str_contains($ua,'Mobile')?'📱':'🖥️';
?>
<tr>
  <td style="font-family:monospace;font-size:11px"><?= htmlspecialchars($v['ip_address']) ?></td>
  <td style="font-size:11px">
    <?= $v['device_name']?'🏷️ '.htmlspecialchars($v['device_name']):($device.' '.$browser) ?>
  </td>
  <td style="font-size:11px;color:var(--muted);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= htmlspecialchars($v['page_url']??'') ?>">
    <?= htmlspecialchars(mb_substr($v['page_url']??'/',0,50)) ?>
  </td>
  <td style="font-size:10px;color:var(--muted)"><?= htmlspecialchars(mb_substr($ua,0,30)) ?></td>
  <td style="font-size:11px;color:var(--muted)"><?= date('Y-m-d H:i',strtotime($v['visited_at'])) ?></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>

<?php elseif($tab === 'devices'): ?>
<!-- إدارة أسماء الأجهزة -->
<?php $devices = Database::fetchAll("SELECT * FROM ".DB_PREFIX."ip_devices ORDER BY updated_at DESC"); ?>
<div style="display:flex;gap:8px;margin-bottom:14px;align-items:center">
  <span style="font-size:12px;color:var(--muted)"><?= count($devices) ?> <?= $isAr?'جهاز مسمى':'named devices' ?></span>
  <button class="btn btn-primary" onclick="openModal('addDeviceModal')" style="margin-<?= $isAr?'right':'left' ?>:auto">➕ <?= $isAr?'إضافة جهاز':'Add Device' ?></button>
</div>
<div class="tbl-wrap"><table><thead><tr>
  <th>IP</th>
  <th><?= $isAr?'اسم الجهاز':'Device Name' ?></th>
  <th><?= $isAr?'ملاحظات':'Notes' ?></th>
  <th><?= $isAr?'آخر تحديث':'Updated' ?></th>
  <th></th>
</tr></thead><tbody>
<?php foreach($devices as $d): ?>
<tr>
  <td style="font-family:monospace;font-size:12px;font-weight:600"><?= htmlspecialchars($d['ip_address']) ?></td>
  <td style="font-weight:700">🏷️ <?= htmlspecialchars($d['device_name']) ?></td>
  <td style="font-size:12px;color:var(--muted)"><?= htmlspecialchars($d['notes']??'-') ?></td>
  <td style="font-size:11px;color:var(--muted)"><?= date('Y-m-d H:i',strtotime($d['updated_at'])) ?></td>
  <td>
    <div style="display:flex;gap:4px">
      <button onclick="editDevice(<?= $d['id'] ?>,'<?= htmlspecialchars($d['ip_address'],ENT_QUOTES) ?>','<?= htmlspecialchars($d['device_name'],ENT_QUOTES) ?>','<?= htmlspecialchars($d['notes']??'',ENT_QUOTES) ?>')" class="btn btn-sm btn-info">✏️</button>
      <form method="POST" action="<?= $adUrl ?>" style="display:inline" onsubmit="return confirmDel()">
        <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
        <input type="hidden" name="action" value="delete_ip_device">
        <input type="hidden" name="id" value="<?= $d['id'] ?>">
        <input type="hidden" name="_redirect" value="?p=visitors&tab=devices">
        <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
      </form>
    </div>
  </td>
</tr>
<?php endforeach; if(empty($devices)): ?>
<tr><td colspan="5" style="text-align:center;padding:20px;color:var(--muted)"><?= $isAr?'لا توجد أجهزة مسماة':'No named devices' ?></td></tr>
<?php endif; ?>
</tbody></table></div>
<?php endif; ?>

<!-- Add Device Modal -->
<div class="modal-ov" id="addDeviceModal">
  <div class="modal-box" style="max-width:400px">
    <div class="modal-hdr"><h3 id="deviceModalTitle">🖥️ <?= $isAr?'تسمية جهاز':'Name Device' ?></h3><button class="modal-close" onclick="closeModal('addDeviceModal')">×</button></div>
    <form method="POST" action="<?= $adUrl ?>" id="deviceForm">
      <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
      <input type="hidden" name="action" id="deviceAction" value="add_ip_device">
      <input type="hidden" name="device_id" id="deviceId" value="">
      <input type="hidden" name="_redirect" value="?p=visitors&tab=devices">
      <div class="modal-body">
        <div class="fg"><label>IP <?= $isAr?'العنوان':'Address' ?></label><input type="text" name="ip_address" id="deviceIP" required placeholder="192.168.1.1"></div>
        <div class="fg"><label><?= $isAr?'اسم الجهاز':'Device Name' ?></label><input type="text" name="device_name" id="deviceName" required placeholder="<?= $isAr?'مثل: جهاز المنزل':'e.g. Home PC' ?>"></div>
        <div class="fg"><label><?= $isAr?'ملاحظات':'Notes' ?></label><input type="text" name="notes" id="deviceNotes" placeholder="<?= $isAr?'اختياري':'Optional' ?>"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('addDeviceModal')"><?= $isAr?'إلغاء':'Cancel' ?></button>
        <button type="submit" class="btn btn-primary">💾 <?= $isAr?'حفظ':'Save' ?></button>
      </div>
    </form>
  </div>
</div>

<script>
function setDeviceName(ip, current) {
  document.getElementById('deviceIP').value = ip;
  document.getElementById('deviceIP').readOnly = true;
  document.getElementById('deviceName').value = current;
  document.getElementById('deviceAction').value = 'add_ip_device';
  openModal('addDeviceModal');
}
function editDevice(id, ip, name, notes) {
  document.getElementById('deviceId').value = id;
  document.getElementById('deviceIP').value = ip;
  document.getElementById('deviceIP').readOnly = true;
  document.getElementById('deviceName').value = name;
  document.getElementById('deviceNotes').value = notes;
  document.getElementById('deviceAction').value = 'edit_ip_device';
  openModal('addDeviceModal');
}
</script>
