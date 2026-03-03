<?php
$isAr = Lang::isRtl();
$lang = Lang::current();
$sym  = htmlspecialchars(Setting::get('currency_symbol','ر.س'));
$page = max(1, Helpers::getInt('page',1));
$q    = Helpers::getStr('q');
$role = Helpers::getStr('role');
$ustatus = Helpers::getStr('ustatus');
$pp   = 20;
$adUrl = Helpers::siteUrl('admin/');

// ── عرض مستخدم واحد ──
$viewId   = Helpers::getInt('id');
$viewAct  = Helpers::getStr('action');
$viewUser = ($viewId && $viewAct === 'view')
    ? Database::fetch("SELECT * FROM ".DB_PREFIX."users WHERE id=?",[$viewId])
    : null;

if ($viewUser): ?>
<div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;flex-wrap:wrap">
  <a href="<?= $adUrl ?>?p=users" class="btn btn-secondary">← <?= $isAr?'عودة':'Back' ?></a>
  <div style="display:flex;align-items:center;gap:10px">
    <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:900;color:#fff"><?= mb_substr($viewUser['name'],0,1) ?></div>
    <div>
      <div style="font-weight:700"><?= htmlspecialchars($viewUser['name']) ?></div>
      <div style="font-size:11px;color:var(--muted)"><?= htmlspecialchars($viewUser['email']) ?></div>
    </div>
  </div>
  <span class="bpill bp-<?= $viewUser['status'] ?>" style="margin-right:auto"><?= $viewUser['status'] ?></span>
  <span class="bpill <?= $viewUser['role']==='admin'?'bp-top':'bp-active' ?>"><?= $viewUser['role'] ?></span>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
  <!-- معلومات الحساب -->
  <div class="frm-card">
    <h3>👤 <?= $isAr?'تعديل المعلومات':'Edit Profile' ?></h3>
    <form method="POST" action="<?= $adUrl ?>">
      <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
      <input type="hidden" name="action" value="edit_user">
      <input type="hidden" name="id" value="<?= $viewUser['id'] ?>">
      <input type="hidden" name="_redirect" value="?p=users&action=view&id=<?= $viewUser['id'] ?>">
      <div class="fg"><label><?= $isAr?'الاسم':'Name' ?></label><input type="text" name="name" value="<?= htmlspecialchars($viewUser['name']) ?>" required></div>
      <div class="fg"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($viewUser['email']) ?>" required></div>
      <div class="fg"><label><?= $isAr?'اسم المستخدم':'Username' ?></label><input type="text" name="username" value="<?= htmlspecialchars($viewUser['username']??'') ?>"></div>
      <div class="fg"><label><?= $isAr?'الهاتف':'Phone' ?></label><input type="text" name="phone" value="<?= htmlspecialchars($viewUser['phone']??'') ?>"></div>
      <div class="grid-2">
        <div class="fg"><label><?= $isAr?'الدور':'Role' ?></label>
          <select name="role">
            <option value="user" <?= $viewUser['role']==='user'?'selected':'' ?>>User</option>
            <option value="moderator" <?= $viewUser['role']==='moderator'?'selected':'' ?>>Moderator</option>
            <option value="admin" <?= $viewUser['role']==='admin'?'selected':'' ?>>Admin</option>
          </select>
        </div>
        <div class="fg"><label><?= $isAr?'الحالة':'Status' ?></label>
          <select name="status">
            <option value="active" <?= $viewUser['status']==='active'?'selected':'' ?>><?= $isAr?'نشط':'Active' ?></option>
            <option value="banned" <?= $viewUser['status']==='banned'?'selected':'' ?>><?= $isAr?'محظور':'Banned' ?></option>
            <option value="pending" <?= $viewUser['status']==='pending'?'selected':'' ?>>Pending</option>
          </select>
        </div>
      </div>
      <div class="fg"><label><?= $isAr?'كلمة مرور جديدة':'New Password' ?> (<?= $isAr?'اتركه فارغاً للإبقاء':'leave blank to keep' ?>)</label>
        <input type="password" name="password" placeholder="8+ chars">
      </div>
      <button type="submit" class="btn btn-primary btn-full">💾 <?= $isAr?'حفظ التعديلات':'Save Changes' ?></button>
    </form>
  </div>

  <!-- رصيد ونقاط -->
  <div class="frm-card">
    <h3>💰 <?= $isAr?'الرصيد والنقاط':'Balance & Points' ?></h3>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px">
      <div style="background:var(--bg);border-radius:10px;padding:14px;text-align:center">
        <div style="font-size:20px;font-weight:900;color:var(--success)"><?= $sym ?><?= number_format($viewUser['wallet_balance'],2) ?></div>
        <div style="font-size:10px;color:var(--muted);margin-top:4px"><?= $isAr?'رصيد المحفظة':'Wallet' ?></div>
      </div>
      <div style="background:var(--bg);border-radius:10px;padding:14px;text-align:center">
        <div style="font-size:20px;font-weight:900;color:var(--primary)"><?= number_format($viewUser['points']??0) ?> 💎</div>
        <div style="font-size:10px;color:var(--muted);margin-top:4px"><?= $isAr?'النقاط':'Points' ?></div>
      </div>
    </div>
    <form method="POST" action="<?= $adUrl ?>">
      <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
      <input type="hidden" name="action" value="admin_add_balance">
      <input type="hidden" name="user_id" value="<?= $viewUser['id'] ?>">
      <input type="hidden" name="_redirect" value="?p=users&action=view&id=<?= $viewUser['id'] ?>">
      <div class="grid-2">
        <div class="fg"><label><?= $isAr?'النوع':'Type' ?></label>
          <select name="type">
            <option value="credit">➕ <?= $isAr?'إضافة':'Add' ?></option>
            <option value="debit">➖ <?= $isAr?'خصم':'Deduct' ?></option>
          </select>
        </div>
        <div class="fg"><label><?= $isAr?'المبلغ':'Amount' ?></label><input type="number" name="amount" step="0.01" min="0.01" required></div>
        <div class="fg full"><label><?= $isAr?'السبب':'Reason' ?></label><input type="text" name="desc" required placeholder="<?= $isAr?'سبب':'Reason' ?>"></div>
      </div>
      <button type="submit" class="btn btn-warning btn-full">💳 <?= $isAr?'تعديل الرصيد':'Adjust Balance' ?></button>
    </form>
    <hr style="border:none;border-top:1px solid var(--border);margin:14px 0">
    <!-- إجراءات سريعة -->
    <div style="display:flex;gap:8px;flex-wrap:wrap">
      <form method="POST" action="<?= $adUrl ?>" style="flex:1">
        <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
        <input type="hidden" name="action" value="toggle_user_status">
        <input type="hidden" name="id" value="<?= $viewUser['id'] ?>">
        <input type="hidden" name="_redirect" value="?p=users&action=view&id=<?= $viewUser['id'] ?>">
        <button type="submit" class="btn btn-full <?= $viewUser['status']==='active'?'btn-warning':'btn-success' ?>">
          <?= $viewUser['status']==='active'?'🚫 '.($isAr?'حظر':'Ban'):'✅ '.($isAr?'تفعيل':'Activate') ?>
        </button>
      </form>
      <?php if($viewUser['role']!=='admin'): ?>
      <form method="POST" action="<?= $adUrl ?>" style="flex:1" onsubmit="return confirmDel(this.dataset.msg)" data-msg="<?= $isAr?'حذف المستخدم نهائياً؟':'Permanently delete user?' ?>">
        <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
        <input type="hidden" name="action" value="delete_user">
        <input type="hidden" name="id" value="<?= $viewUser['id'] ?>">
        <input type="hidden" name="_redirect" value="?p=users">
        <button type="submit" class="btn btn-danger btn-full">🗑️ <?= $isAr?'حذف':'Delete' ?></button>
      </form>
      <?php endif; ?>
    </div>
    <!-- ارسال إشعار -->
    <hr style="border:none;border-top:1px solid var(--border);margin:14px 0">
    <button class="btn btn-secondary btn-full" onclick="openModal('notifUserModal')">📢 <?= $isAr?'إرسال إشعار':'Send Notification' ?></button>
  </div>
</div>

<!-- الطلبات والمعاملات -->
<?php
$uOrders = Database::fetchAll("SELECT * FROM ".DB_PREFIX."orders WHERE user_id=? ORDER BY created_at DESC LIMIT 10",[$viewUser['id']]);
$uTxns   = Database::fetchAll("SELECT * FROM ".DB_PREFIX."wallet_transactions WHERE user_id=? ORDER BY created_at DESC LIMIT 10",[$viewUser['id']]);
?>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
  <div class="tbl-wrap">
    <div class="tbl-hdr"><div class="tbl-title">📦 <?= $isAr?'آخر الطلبات':'Orders' ?></div>
      <span style="font-size:11px;color:var(--muted)"><?= Database::count('orders','user_id=?',[$viewUser['id']]) ?> <?= $isAr?'طلب':'orders' ?></span>
    </div>
    <table><thead><tr><th>#</th><th><?= $isAr?'المبلغ':'Amount' ?></th><th>Status</th><th>Date</th></tr></thead><tbody>
    <?php foreach($uOrders as $o): ?>
    <tr>
      <td style="font-size:10px;font-family:monospace;color:var(--primary)"><?= $o['order_number'] ?></td>
      <td style="font-weight:700;color:var(--success)"><?= $sym ?><?= number_format($o['total'],2) ?></td>
      <td><span class="bpill bp-<?= $o['status'] ?>"><?= $o['status'] ?></span></td>
      <td style="font-size:11px;color:var(--muted)"><?= date('m-d',strtotime($o['created_at'])) ?></td>
    </tr>
    <?php endforeach; if(empty($uOrders)): ?>
    <tr><td colspan="4" style="text-align:center;padding:16px;color:var(--muted)"><?= $isAr?'لا توجد طلبات':'No orders' ?></td></tr>
    <?php endif; ?>
    </tbody></table>
  </div>
  <div class="tbl-wrap">
    <div class="tbl-hdr"><div class="tbl-title">💰 <?= $isAr?'المعاملات':'Transactions' ?></div></div>
    <table><thead><tr><th>Type</th><th><?= $isAr?'المبلغ':'Amount' ?></th><th>Date</th></tr></thead><tbody>
    <?php foreach($uTxns as $tx): ?>
    <tr>
      <td><span class="bpill <?= $tx['type']==='credit'?'bp-active':'bp-out' ?>"><?= $tx['type'] ?></span></td>
      <td style="font-weight:700;color:<?= $tx['type']==='credit'?'var(--success)':'var(--danger)' ?>"><?= $tx['type']==='credit'?'+':'-' ?><?= $sym ?><?= number_format($tx['amount'],2) ?></td>
      <td style="font-size:11px;color:var(--muted)"><?= date('m-d',strtotime($tx['created_at'])) ?></td>
    </tr>
    <?php endforeach; if(empty($uTxns)): ?>
    <tr><td colspan="3" style="text-align:center;padding:16px;color:var(--muted)"><?= $isAr?'لا توجد معاملات':'No transactions' ?></td></tr>
    <?php endif; ?>
    </tbody></table>
  </div>
</div>

<!-- Notification Modal -->
<div class="modal-ov" id="notifUserModal">
  <div class="modal-box" style="max-width:420px">
    <div class="modal-hdr"><h3>📢 <?= $isAr?'إرسال إشعار':'Send Notification' ?></h3><button class="modal-close" onclick="closeModal('notifUserModal')">×</button></div>
    <form method="POST" action="<?= $adUrl ?>">
      <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
      <input type="hidden" name="action" value="send_user_notification">
      <input type="hidden" name="user_id" value="<?= $viewUser['id'] ?>">
      <input type="hidden" name="_redirect" value="?p=users&action=view&id=<?= $viewUser['id'] ?>">
      <div class="modal-body">
        <div class="fg"><label><?= $isAr?'العنوان (عربي)':'Title AR' ?></label><input type="text" name="title_ar" required></div>
        <div class="fg"><label>Title EN</label><input type="text" name="title_en"></div>
        <div class="fg"><label><?= $isAr?'الرسالة':'Message' ?></label><textarea name="message_ar" required rows="3"></textarea></div>
        <div class="fg"><label>Icon</label><input type="text" name="icon" value="🔔" maxlength="5"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('notifUserModal')"><?= $isAr?'إلغاء':'Cancel' ?></button>
        <button type="submit" class="btn btn-primary">📤 <?= $isAr?'إرسال':'Send' ?></button>
      </div>
    </form>
  </div>
</div>
<?php return; endif; ?>

<!-- ── قائمة المستخدمين ── -->
<?php
$where = ["1=1"]; $params = [];
if($q) { $where[] = "(name LIKE ? OR email LIKE ?)"; $params = ["%$q%","%$q%"]; }
if($role)    { $where[] = "role=?";    $params[] = $role; }
if($ustatus) { $where[] = "status=?";  $params[] = $ustatus; }
$whereStr = implode(' AND ',$where);
$total  = Database::count('users',$whereStr,$params);
$offset = ($page-1)*$pp;
$users  = Database::fetchAll("SELECT * FROM ".DB_PREFIX."users WHERE $whereStr ORDER BY created_at DESC LIMIT $pp OFFSET $offset",$params);
?>

<div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;align-items:center">
  <form method="GET" style="display:flex;gap:6px;flex-wrap:wrap">
    <input type="hidden" name="p" value="users">
    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="<?= $isAr?'بحث...':'Search...' ?>" style="min-width:200px">
    <select name="role">
      <option value=""><?= $isAr?'كل الأدوار':'All Roles' ?></option>
      <option value="user" <?= $role==='user'?'selected':'' ?>>User</option>
      <option value="moderator" <?= $role==='moderator'?'selected':'' ?>>Moderator</option>
      <option value="admin" <?= $role==='admin'?'selected':'' ?>>Admin</option>
    </select>
    <select name="ustatus">
      <option value=""><?= $isAr?'كل الحالات':'All' ?></option>
      <option value="active" <?= $ustatus==='active'?'selected':'' ?>>Active</option>
      <option value="banned" <?= $ustatus==='banned'?'selected':'' ?>>Banned</option>
    </select>
    <button type="submit" class="btn btn-secondary">🔍</button>
    <a href="<?= $adUrl ?>?p=users" class="btn btn-secondary">✕</a>
  </form>
  <span style="color:var(--muted);font-size:12px"><?= $total ?> <?= $isAr?'مستخدم':'users' ?></span>
  <button class="btn btn-primary" onclick="openModal('addUserModal')" style="margin-<?= $isAr?'right':'left' ?>:auto">➕ <?= $isAr?'إضافة':'Add User' ?></button>
</div>

<!-- Bulk Actions Bar -->
<div id="bulkBar" style="display:none;align-items:center;gap:8px;background:rgba(124,58,237,.08);border:1px solid rgba(124,58,237,.2);border-radius:10px;padding:8px 14px;margin-bottom:10px;flex-wrap:wrap">
  <span style="font-size:13px;font-weight:700">✅ <span id="bulkCount">0</span> <?= $isAr?'محدد':'selected' ?></span>
  <form method="POST" action="<?= $adUrl ?>" id="bulkForm" style="display:flex;gap:6px;flex-wrap:wrap">
    <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
    <input type="hidden" name="action" value="bulk_delete_users">
    <input type="hidden" name="_redirect" value="?p=users">
    <input type="hidden" name="ids" id="bulkIds">
    <button type="button" class="btn btn-sm btn-danger" onclick="bulkDelete(document.getElementById('bulkForm'))">🗑️ <?= $isAr?'حذف المحدد':'Delete Selected' ?></button>
  </form>
</div>

<form method="POST" action="<?= $adUrl ?>" id="usrBulkForm"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="bulk_delete_users"><input type="hidden" name="_redirect" value="?p=users">
<div class="tbl-wrap"><table><thead><tr>
  <th style="width:36px"><input type="checkbox" id="usrChkAll" onchange="toggleBulk(this,'usr-chk','usrBulkBtn')"></th>
  <th>#</th><th><?= $isAr?'المستخدم':'User' ?></th><th>Email</th>
  <th><?= $isAr?'الدور':'Role' ?></th><th><?= $isAr?'الرصيد':'Balance' ?></th>
  <th><?= $isAr?'النقاط':'Points' ?></th><th><?= $isAr?'الحالة':'Status' ?></th>
  <th><?= $isAr?'تاريخ التسجيل':'Joined' ?></th><th></th>
</tr></thead><tbody>
<?php foreach($users as $u): ?>
<tr>
  <td><input type="checkbox" class="usr-chk" name="ids[]" value="<?= $u['id'] ?>" onchange="syncBulkBtn('usr-chk','usrBulkBtn')"></td>
  <td style="color:var(--muted);font-size:11px"><?= $u['id'] ?></td>
  <td>
    <div style="display:flex;align-items:center;gap:8px">
      <div style="width:30px;height:30px;background:linear-gradient(135deg,var(--primary),var(--accent));border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:13px;flex-shrink:0"><?= mb_substr($u['name'],0,1) ?></div>
      <div>
        <div style="font-weight:700;font-size:13px"><?= htmlspecialchars($u['name']) ?></div>
        <?php if($u['username']): ?><div style="font-size:10px;color:var(--muted)">@<?= htmlspecialchars($u['username']) ?></div><?php endif; ?>
      </div>
    </div>
  </td>
  <td style="font-size:12px"><?= htmlspecialchars($u['email']) ?></td>
  <td><span class="bpill <?= $u['role']==='admin'?'bp-top':($u['role']==='moderator'?'bp-processing':'bp-active') ?>"><?= $u['role'] ?></span></td>
  <td style="color:var(--success);font-weight:700;font-size:13px"><?= $sym ?><?= number_format($u['wallet_balance'],2) ?></td>
  <td style="color:var(--primary);font-size:13px"><?= number_format($u['points']??0) ?></td>
  <td><span class="bpill bp-<?= $u['status'] ?>"><?= $u['status'] ?></span></td>
  <td style="font-size:11px;color:var(--muted)"><?= date('Y-m-d',strtotime($u['created_at'])) ?></td>
  <td>
    <div style="display:flex;gap:4px">
      <a href="<?= $adUrl ?>?p=users&action=view&id=<?= $u['id'] ?>" class="btn btn-sm btn-info">👁️</a>
      <form method="POST" action="<?= $adUrl ?>" style="display:inline">
        <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
        <input type="hidden" name="action" value="toggle_user_status">
        <input type="hidden" name="id" value="<?= $u['id'] ?>">
        <button type="submit" class="btn btn-sm <?= $u['status']==='active'?'btn-warning':'btn-success' ?>"><?= $u['status']==='active'?'🚫':'✅' ?></button>
      </form>
    </div>
  </td>
</tr>
<?php endforeach; if(empty($users)): ?>
<tr><td colspan="10" style="text-align:center;padding:24px;color:var(--muted)">👥 <?= $isAr?'لا توجد مستخدمون':'No users found' ?></td></tr>
<?php endif; ?>
</tbody></table></div>

<?php if($total > $pp): ?>
<div style="display:flex;gap:6px;justify-content:center;margin-top:14px;flex-wrap:wrap">
  <?php for($i=1;$i<=ceil($total/$pp);$i++): ?>
  <a href="<?= $adUrl ?>?p=users&page=<?= $i ?>&q=<?= urlencode($q) ?>&role=<?= $role ?>&ustatus=<?= $ustatus ?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
  <?php endfor; ?>
</div>
<?php endif; ?>
</form><!-- end usrBulkForm -->

<!-- Add User Modal -->
<div class="modal-ov" id="addUserModal">
  <div class="modal-box" style="max-width:480px">
    <div class="modal-hdr"><h3>➕ <?= $isAr?'إضافة مستخدم':'Add User' ?></h3><button class="modal-close" onclick="closeModal('addUserModal')">×</button></div>
    <form method="POST" action="<?= $adUrl ?>">
      <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
      <input type="hidden" name="action" value="add_user">
      <div class="modal-body">
        <div class="grid-2">
          <div class="fg full"><label><?= $isAr?'الاسم الكامل':'Full Name' ?> *</label><input type="text" name="name" required></div>
          <div class="fg full"><label>Email *</label><input type="email" name="email" required></div>
          <div class="fg"><label><?= $isAr?'كلمة المرور':'Password' ?> * (6+)</label><input type="password" name="password" required minlength="6"></div>
          <div class="fg"><label><?= $isAr?'الدور':'Role' ?></label>
            <select name="role"><option value="user">User</option><option value="moderator">Moderator</option><option value="admin">Admin</option></select>
          </div>
          <div class="fg"><label><?= $isAr?'رصيد ابتدائي':'Initial Balance' ?></label><input type="number" name="wallet" step="0.01" min="0" value="0"></div>
          <div class="fg"><label><?= $isAr?'نقاط ابتدائية':'Initial Points' ?></label><input type="number" name="points" min="0" value="0"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('addUserModal')"><?= $isAr?'إلغاء':'Cancel' ?></button>
        <button type="submit" class="btn btn-primary">💾 <?= $isAr?'إنشاء':'Create' ?></button>
      </div>
    </form>
  </div>
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