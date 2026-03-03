<?php
$isAr  = Lang::isRtl();
$adUrl = Helpers::siteUrl('admin/');
$tab   = Helpers::getStr('tab','list');

// All permissions
$allPerms = [
  'dashboard'     => $isAr?'لوحة التحكم':'Dashboard',
  'products'      => $isAr?'المنتجات':'Products',
  'categories'    => $isAr?'التصنيفات':'Categories',
  'codes'         => $isAr?'الأكواد':'Codes',
  'orders'        => $isAr?'الطلبات':'Orders',
  'deposits'      => $isAr?'الإيداع':'Deposits',
  'users'         => $isAr?'المستخدمون':'Users',
  'tickets'       => $isAr?'التذاكر':'Tickets',
  'coupons'       => $isAr?'الكوبونات':'Coupons',
  'reviews'       => $isAr?'التقييمات':'Reviews',
  'notifications' => $isAr?'الإشعارات':'Notifications',
  'banners'       => $isAr?'البانرات':'Banners',
  'reports'       => $isAr?'التقارير':'Reports',
  'visitors'      => $isAr?'الزوار':'Visitors',
  'settings'      => $isAr?'الإعدادات':'Settings',
  'backup'        => $isAr?'النسخ الاحتياطي':'Backup',
  'security'      => $isAr?'الأمان':'Security',
  'seo'           => $isAr?'SEO':'SEO',
  'activity'      => $isAr?'سجل النشاطات':'Activity',
  'pages'         => $isAr?'الصفحات':'Pages',
  'admins'        => $isAr?'إدارة المسؤولين (خطر!)':'Manage Admins (!)',
];

// Get view id
$viewId = Helpers::getInt('id');
$viewAct = Helpers::getStr('action');
$admins = Database::fetchAll("SELECT * FROM ".DB_PREFIX."users WHERE role IN ('admin','moderator') ORDER BY created_at ASC");
$editAdmin = ($viewId && $viewAct==='edit') ? Database::fetch("SELECT * FROM ".DB_PREFIX."users WHERE id=?",[$viewId]) : null;
?>

<!-- Tabs -->
<div style="display:flex;gap:4px;margin-bottom:16px;background:var(--card);border:1px solid var(--border);border-radius:10px;padding:5px">
  <a href="<?= $adUrl ?>?p=admins&tab=list" style="padding:8px 16px;border-radius:7px;font-size:13px;font-weight:700;text-decoration:none;<?= $tab==='list'?'background:var(--primary);color:#fff':'color:var(--muted)' ?>">👑 <?= $isAr?'المسؤولون':'Admins List' ?></a>
  <a href="<?= $adUrl ?>?p=admins&tab=presets" style="padding:8px 16px;border-radius:7px;font-size:13px;font-weight:700;text-decoration:none;<?= $tab==='presets'?'background:var(--primary);color:#fff':'color:var(--muted)' ?>">🛡️ <?= $isAr?'قوالب الصلاحيات':'Role Presets' ?></a>
  <a href="<?= $adUrl ?>?p=admins&tab=perms" style="padding:8px 16px;border-radius:7px;font-size:13px;font-weight:700;text-decoration:none;<?= $tab==='perms'?'background:var(--primary);color:#fff':'color:var(--muted)' ?>">🔑 <?= $isAr?'الصلاحيات':'Permissions' ?></a>
</div>

<?php if($tab === 'list'): ?>

<!-- Edit Admin Panel -->
<?php if($editAdmin): ?>
<div class="frm-card" style="margin-bottom:16px;border:1px solid var(--primary)">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
    <h3>✏️ <?= $isAr?'تعديل المسؤول':'Edit Admin' ?>: <?= htmlspecialchars($editAdmin['name']) ?></h3>
    <a href="<?= $adUrl ?>?p=admins" class="btn btn-sm btn-secondary">✕</a>
  </div>
  <form method="POST" action="<?= $adUrl ?>">
    <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
    <input type="hidden" name="action" value="edit_admin">
    <input type="hidden" name="id" value="<?= $editAdmin['id'] ?>">
    <input type="hidden" name="_redirect" value="?p=admins">
    <div class="grid-2">
      <div class="fg"><label><?= $isAr?'الاسم':'Name' ?></label><input type="text" name="name" value="<?= htmlspecialchars($editAdmin['name']) ?>" required></div>
      <div class="fg"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($editAdmin['email']) ?>" required></div>
      <div class="fg"><label><?= $isAr?'الدور':'Role' ?></label>
        <select name="role">
          <option value="admin" <?= $editAdmin['role']==='admin'?'selected':'' ?>>Admin <?= $isAr?'(مسؤول كامل)':'(Full Access)' ?></option>
          <option value="moderator" <?= $editAdmin['role']==='moderator'?'selected':'' ?>>Moderator <?= $isAr?'(محدود)':'(Limited)' ?></option>
        </select>
      </div>
      <div class="fg"><label><?= $isAr?'الحالة':'Status' ?></label>
        <select name="status">
          <option value="active" <?= $editAdmin['status']==='active'?'selected':'' ?>><?= $isAr?'نشط':'Active' ?></option>
          <option value="banned" <?= $editAdmin['status']==='banned'?'selected':'' ?>><?= $isAr?'محظور':'Banned' ?></option>
        </select>
      </div>
      <div class="fg full"><label><?= $isAr?'كلمة مرور جديدة (اتركها فارغة للإبقاء)':'New Password (leave blank to keep)' ?></label>
        <input type="password" name="password" placeholder="8+ chars">
      </div>
    </div>
    <!-- Permissions (for moderators) -->
    <?php if($editAdmin['role']==='moderator'):
      $curPerms = json_decode($editAdmin['permissions']??'[]',true) ?: [];
    ?>
    <div style="margin-top:14px">
      <label style="font-weight:700;font-size:12px;color:var(--muted);display:block;margin-bottom:10px"><?= $isAr?'الصلاحيات المسموح بها:':'Allowed Permissions:' ?></label>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:8px">
        <?php foreach($allPerms as $pk=>$plbl): ?>
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:8px 10px">
          <input type="checkbox" name="permissions[]" value="<?= $pk ?>" <?= in_array($pk,$curPerms)?'checked':'' ?> style="accent-color:var(--primary)">
          <span style="font-size:12px"><?= $plbl ?></span>
        </label>
        <?php endforeach; ?>
      </div>
      <div style="display:flex;gap:8px;margin-top:10px">
        <button type="button" class="btn btn-sm btn-secondary" onclick="document.querySelectorAll('[name=\'permissions[]\']').forEach(c=>c.checked=true)"><?= $isAr?'تحديد الكل':'Select All' ?></button>
        <button type="button" class="btn btn-sm btn-secondary" onclick="document.querySelectorAll('[name=\'permissions[]\']').forEach(c=>c.checked=false)"><?= $isAr?'إلغاء الكل':'Deselect All' ?></button>
      </div>
    </div>
    <?php endif; ?>
    <div style="margin-top:16px"><button type="submit" class="btn btn-primary">💾 <?= $isAr?'حفظ':'Save' ?></button></div>
  </form>
</div>
<?php endif; ?>

<!-- Admins Table -->
<div style="display:flex;justify-content:flex-end;margin-bottom:12px">
  <button class="btn btn-primary" onclick="openModal('addAdminModal')">➕ <?= $isAr?'إضافة مسؤول':'Add Admin' ?></button>
</div>
<div class="tbl-wrap"><table><thead><tr>
  <th>#</th><th><?= $isAr?'المسؤول':'Admin' ?></th><th>Email</th>
  <th><?= $isAr?'الدور':'Role' ?></th><th><?= $isAr?'الحالة':'Status' ?></th>
  <th><?= $isAr?'آخر دخول':'Last Login' ?></th><th></th>
</tr></thead><tbody>
<?php foreach($admins as $a): ?>
<tr>
  <td style="color:var(--muted);font-size:11px"><?= $a['id'] ?></td>
  <td>
    <div style="display:flex;align-items:center;gap:8px">
      <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:13px"><?= mb_substr($a['name'],0,1) ?></div>
      <div>
        <div style="font-weight:700;font-size:13px"><?= htmlspecialchars($a['name']) ?></div>
        <?php if($a['username']): ?><div style="font-size:10px;color:var(--muted)">@<?= htmlspecialchars($a['username']) ?></div><?php endif; ?>
      </div>
    </div>
  </td>
  <td style="font-size:12px"><?= htmlspecialchars($a['email']) ?></td>
  <td><span class="bpill <?= $a['role']==='admin'?'bp-top':'bp-processing' ?>"><?= $a['role'] ?></span></td>
  <td><span class="bpill bp-<?= $a['status'] ?>"><?= $a['status'] ?></span></td>
  <td style="font-size:11px;color:var(--muted)"><?= $a['last_login']??'-' ?></td>
  <td>
    <div style="display:flex;gap:4px">
      <a href="<?= $adUrl ?>?p=admins&action=edit&id=<?= $a['id'] ?>" class="btn btn-sm btn-info">✏️</a>
      <?php if($a['id'] != Auth::id()): ?>
      <form method="POST" action="<?= $adUrl ?>" style="display:inline">
        <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
        <input type="hidden" name="action" value="toggle_user_status">
        <input type="hidden" name="id" value="<?= $a['id'] ?>">
        <input type="hidden" name="_redirect" value="?p=admins">
        <button type="submit" class="btn btn-sm <?= $a['status']==='active'?'btn-warning':'btn-success' ?>"><?= $a['status']==='active'?'🚫':'✅' ?></button>
      </form>
      <form method="POST" action="<?= $adUrl ?>" style="display:inline" onsubmit="return confirmDel('<?= $isAr?'حذف هذا المسؤول؟':'Delete this admin?' ?>')">
        <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
        <input type="hidden" name="action" value="delete_user">
        <input type="hidden" name="id" value="<?= $a['id'] ?>">
        <input type="hidden" name="_redirect" value="?p=admins">
        <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
      </form>
      <?php endif; ?>
    </div>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>

<!-- Add Admin Modal -->
<div class="modal-ov" id="addAdminModal">
  <div class="modal-box" style="max-width:480px">
    <div class="modal-hdr"><h3>➕ <?= $isAr?'إضافة مسؤول':'Add Admin' ?></h3><button class="modal-close" onclick="closeModal('addAdminModal')">×</button></div>
    <form method="POST" action="<?= $adUrl ?>">
      <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
      <input type="hidden" name="action" value="add_admin">
      <input type="hidden" name="_redirect" value="?p=admins">
      <div class="modal-body">
        <div class="grid-2">
          <div class="fg full"><label><?= $isAr?'الاسم الكامل':'Full Name' ?> *</label><input type="text" name="name" required></div>
          <div class="fg full"><label>Email *</label><input type="email" name="email" required></div>
          <div class="fg"><label><?= $isAr?'كلمة المرور':'Password' ?> * (8+)</label><input type="password" name="password" required minlength="8"></div>
          <div class="fg"><label><?= $isAr?'الدور':'Role' ?></label>
            <select name="role">
              <option value="admin">Admin</option>
              <option value="moderator">Moderator</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('addAdminModal')"><?= $isAr?'إلغاء':'Cancel' ?></button>
        <button type="submit" class="btn btn-primary">💾 <?= $isAr?'إنشاء':'Create' ?></button>
      </div>
    </form>
  </div>
</div>

<?php elseif($tab === 'presets'): ?>
<!-- قوالب الصلاحيات -->
<?php
$rolePresets = json_decode(Setting::get('admin_role_presets','[]'), true) ?: [];
$moderators  = array_filter($admins, fn($a) => $a['role'] === 'moderator');
?>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
<div class="frm-card">
  <h3>➕ <?= $isAr?'إنشاء قالب صلاحيات':'Create Role Preset' ?></h3>
  <p style="font-size:12px;color:var(--muted);margin-bottom:14px"><?= $isAr?'أنشئ قالباً باسم مثل: دعم فني، مبيعات، مدير منتجات وطبّقه على المشرفين':'Create a named preset like: Support, Sales, Product Manager and apply to moderators' ?></p>
  <form method="POST" action="<?= $adUrl ?>">
    <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
    <input type="hidden" name="action" value="save_role_preset">
    <input type="hidden" name="_redirect" value="?p=admins&tab=presets">
    <div class="fg" style="margin-bottom:12px">
      <label><?= $isAr?'اسم القالب':'Preset Name' ?> *</label>
      <input type="text" name="preset_name" required placeholder="<?= $isAr?'مثال: دعم فني':'e.g., Support Team' ?>">
    </div>
    <label style="font-weight:700;font-size:12px;display:block;margin-bottom:8px"><?= $isAr?'الصلاحيات':'Permissions' ?></label>
    <div style="display:flex;gap:6px;margin-bottom:8px">
      <button type="button" class="btn btn-sm btn-secondary" onclick="document.querySelectorAll('[name=\'preset_perms[]\']').forEach(x=>x.checked=true)"><?= $isAr?'تحديد الكل':'All' ?></button>
      <button type="button" class="btn btn-sm btn-secondary" onclick="document.querySelectorAll('[name=\'preset_perms[]\']').forEach(x=>x.checked=false)"><?= $isAr?'إلغاء':'None' ?></button>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:5px;max-height:300px;overflow-y:auto;background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:10px;margin-bottom:12px">
      <?php foreach($allPerms as $pk=>$plbl): ?>
      <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;padding:4px 6px;border-radius:6px">
        <input type="checkbox" name="preset_perms[]" value="<?= $pk ?>">
        <?= $plbl ?>
      </label>
      <?php endforeach; ?>
    </div>
    <button type="submit" class="btn btn-primary">💾 <?= $isAr?'حفظ القالب':'Save Preset' ?></button>
  </form>
</div>
<div class="frm-card">
  <h3>🛡️ <?= $isAr?'القوالب المحفوظة':'Saved Presets' ?></h3>
  <?php if(empty($rolePresets)): ?>
  <p style="color:var(--muted);font-size:13px;text-align:center;padding:30px"><?= $isAr?'لا توجد قوالب بعد. أنشئ قالباً من اليسار.':'No presets yet. Create one from the left.' ?></p>
  <?php else: ?>
  <?php foreach($rolePresets as $pi=>$preset): ?>
  <div style="background:var(--bg);border:1px solid var(--border);border-radius:12px;padding:14px;margin-bottom:10px">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;flex-wrap:wrap">
      <span style="font-weight:800;font-size:14px">🛡️ <?= htmlspecialchars($preset['name']) ?></span>
      <span style="font-size:11px;color:var(--muted)"><?= count($preset['perms']??[]) ?> <?= $isAr?'صلاحية':'perms' ?></span>
      <div style="margin-inline-start:auto;display:flex;gap:5px;flex-wrap:wrap">
        <?php if($moderators): ?>
        <select id="applyMod_<?= $pi ?>" style="font-size:11px;padding:4px 8px;background:var(--card);border:1px solid var(--border);border-radius:7px;color:var(--text)">
          <?php foreach($moderators as $mod): ?><option value="<?= $mod['id'] ?>"><?= htmlspecialchars($mod['name']) ?></option><?php endforeach; ?>
        </select>
        <form method="POST" action="<?= $adUrl ?>" style="display:inline">
          <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
          <input type="hidden" name="action" value="apply_role_preset">
          <input type="hidden" name="preset_idx" value="<?= $pi ?>">
          <input type="hidden" name="_redirect" value="?p=admins&tab=presets">
          <input type="hidden" name="admin_id" id="applyId_<?= $pi ?>" value="">
          <button type="submit" class="btn btn-sm btn-success" onclick="document.getElementById('applyId_<?= $pi ?>').value=document.getElementById('applyMod_<?= $pi ?>').value">✅ <?= $isAr?'تطبيق':'Apply' ?></button>
        </form>
        <?php endif; ?>
        <form method="POST" action="<?= $adUrl ?>" style="display:inline" onsubmit="return confirm('<?= $isAr?'حذف هذا القالب؟':'Delete preset?' ?>')">
          <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
          <input type="hidden" name="action" value="delete_role_preset">
          <input type="hidden" name="preset_idx" value="<?= $pi ?>">
          <input type="hidden" name="_redirect" value="?p=admins&tab=presets">
          <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
        </form>
      </div>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:4px">
      <?php foreach($preset['perms']??[] as $pk): ?>
      <span style="background:rgba(124,58,237,.15);border:1px solid rgba(124,58,237,.3);color:var(--primary);padding:2px 8px;border-radius:6px;font-size:10px"><?= $allPerms[$pk]??$pk ?></span>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>
  <?php endif; ?>
</div>
</div>

<?php elseif($tab === 'perms'): ?>
<!-- تبويب الصلاحيات -->
<div class="frm-card">
  <h3>🔑 <?= $isAr?'الصلاحيات المتاحة':'Available Permissions' ?></h3>
  <p style="font-size:13px;color:var(--muted);margin-bottom:16px"><?= $isAr?'هذه قائمة بجميع الصلاحيات التي يمكن تعيينها للمسؤولين من نوع Moderator. المسؤولون من نوع Admin يملكون جميع الصلاحيات تلقائياً.':'Full list of permissions assignable to Moderators. Admins automatically have all permissions.' ?></p>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px">
    <?php foreach($allPerms as $pk=>$plbl): ?>
    <div style="background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:12px 14px;display:flex;align-items:center;gap:10px">
      <div style="width:8px;height:8px;border-radius:50%;background:var(--primary);flex-shrink:0"></div>
      <div>
        <div style="font-weight:700;font-size:12px"><?= $plbl ?></div>
        <div style="font-size:10px;color:var(--muted);font-family:monospace"><?= $pk ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>
