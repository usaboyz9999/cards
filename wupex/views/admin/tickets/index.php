<?php
$isAr  = Lang::isRtl();
$adUrl = Helpers::siteUrl('admin/');
$viewId  = Helpers::getInt('id');
$viewAct = Helpers::getStr('action');

// ── عرض تذكرة واحدة ──
if ($viewId && $viewAct === 'view'):
    $tk = Database::fetch("SELECT t.*,u.name as uname,u.email FROM ".DB_PREFIX."tickets t JOIN ".DB_PREFIX."users u ON u.id=t.user_id WHERE t.id=?",[$viewId]);
    if (!$tk) { echo '<div class="frm-card">'.($isAr?'تذكرة غير موجودة':'Ticket not found').'</div>'; return; }
    $replies = Ticket::replies($viewId);
    $pColors = ['low'=>'bp-active','medium'=>'bp-processing','high'=>'bp-pending','urgent'=>'bp-out'];
?>
<div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;flex-wrap:wrap">
  <a href="<?= $adUrl ?>?p=tickets" class="btn btn-secondary">← <?= $isAr?'عودة':'Back' ?></a>
  <div style="font-family:monospace;font-weight:700;color:var(--primary);font-size:15px">#<?= htmlspecialchars($tk['ticket_number']) ?></div>
  <div style="font-weight:700"><?= htmlspecialchars(mb_substr($tk['subject'],0,60)) ?></div>
  <span class="bpill <?= $pColors[$tk['priority']]??'bp-active' ?>"><?= $tk['priority'] ?></span>
  <span class="bpill bp-<?= str_replace(['in_progress','waiting'],['processing','pending'],$tk['status']) ?>"><?= $tk['status'] ?></span>
  <span style="margin-<?= $isAr?'right':'left' ?>:auto;font-size:12px;color:var(--muted)">👤 <?= htmlspecialchars($tk['uname']) ?> · <?= htmlspecialchars($tk['email']) ?></span>
</div>

<!-- Quick Actions -->
<div style="display:flex;gap:7px;margin-bottom:16px;flex-wrap:wrap">
  <?php foreach(['open'=>'🔵','in_progress'=>'🟡','waiting'=>'🟠','resolved'=>'✅','closed'=>'🔴'] as $st=>$ic): ?>
  <?php if($tk['status']!==$st): ?>
  <form method="POST" action="<?= $adUrl ?>" style="display:inline">
    <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
    <input type="hidden" name="action" value="update_ticket_status">
    <input type="hidden" name="id" value="<?= $viewId ?>">
    <input type="hidden" name="status" value="<?= $st ?>">
    <input type="hidden" name="_redirect" value="?p=tickets&action=view&id=<?= $viewId ?>">
    <button type="submit" class="btn btn-sm btn-secondary"><?= $ic ?> <?= $st ?></button>
  </form>
  <?php endif; endforeach; ?>
  <form method="POST" action="<?= $adUrl ?>" style="display:inline;margin-<?= $isAr?'right':'left' ?>:auto" onsubmit="return confirmDel()">
    <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
    <input type="hidden" name="action" value="delete_ticket">
    <input type="hidden" name="id" value="<?= $viewId ?>">
    <input type="hidden" name="_redirect" value="?p=tickets">
    <button type="submit" class="btn btn-sm btn-danger">🗑️ <?= $isAr?'حذف':'Delete' ?></button>
  </form>
</div>

<!-- المحادثة -->
<div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px;margin-bottom:16px;max-height:500px;overflow-y:auto" id="chatBox">
  <?php foreach($replies as $r): ?>
  <div style="display:flex;gap:10px;margin-bottom:14px;<?= $r['is_admin']?'flex-direction:row-reverse':'' ?>">
    <div style="width:34px;height:34px;border-radius:50%;background:<?= $r['is_admin']?'var(--primary)':'var(--card2)' ?>;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0">
      <?= $r['is_admin']?'👑':'👤' ?>
    </div>
    <div style="max-width:75%">
      <div style="font-size:11px;color:var(--muted);margin-bottom:4px;text-align:<?= $r['is_admin']?'right':'left' ?>">
        <?= htmlspecialchars($r['name']) ?> · <?= date('Y-m-d H:i',strtotime($r['created_at'])) ?>
        <?php if($r['is_admin']): ?><span class="bpill bp-top" style="font-size:9px;margin-right:4px"><?= $isAr?'مسؤول':'Admin' ?></span><?php endif; ?>
      </div>
      <div style="background:<?= $r['is_admin']?'rgba(124,58,237,.12)':'var(--bg)' ?>;border:1px solid <?= $r['is_admin']?'rgba(124,58,237,.3)':'var(--border)' ?>;border-radius:10px;padding:10px 14px;font-size:13px;line-height:1.7;white-space:pre-wrap">
        <?= htmlspecialchars($r['message']) ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<script>document.getElementById('chatBox').scrollTop = 9999;</script>

<!-- رد جديد -->
<?php if($tk['status'] !== 'closed'): ?>
<div class="frm-card">
  <h3>💬 <?= $isAr?'الرد على التذكرة':'Reply' ?></h3>
  <form method="POST" action="<?= $adUrl ?>">
    <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
    <input type="hidden" name="action" value="admin_reply_ticket">
    <input type="hidden" name="ticket_id" value="<?= $viewId ?>">
    <input type="hidden" name="_redirect" value="?p=tickets&action=view&id=<?= $viewId ?>">
    <div class="fg">
      <textarea name="message" required rows="4" placeholder="<?= $isAr?'اكتب ردك هنا...':'Type your reply here...' ?>" style="min-height:100px"></textarea>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
      <button type="submit" class="btn btn-primary">📤 <?= $isAr?'إرسال الرد':'Send Reply' ?></button>
      <button type="submit" name="close_after" value="1" class="btn btn-secondary">✅ <?= $isAr?'رد وإغلاق':'Reply & Close' ?></button>
    </div>
  </form>
</div>
<?php endif; ?>
<?php return; endif; ?>

<!-- ── قائمة التذاكر ── -->
<?php
$status = Helpers::getStr('status','open');
$page   = max(1, Helpers::getInt('page',1));
$data   = Ticket::adminAll($status, $page);
$tickets = $data['items']; $total = $data['total'];
?>
<div style="display:flex;gap:7px;margin-bottom:14px;flex-wrap:wrap">
  <?php foreach(['open'=>'🔵','in_progress'=>'🟡','waiting'=>'🟠','resolved'=>'✅','closed'=>'🔴'] as $st=>$ic): ?>
  <a href="<?= $adUrl ?>?p=tickets&status=<?= $st ?>" class="btn btn-sm <?= $status===$st?'btn-primary':'btn-secondary' ?>">
    <?= $ic ?> <?= $st ?> <span class="adm-badge"><?= Database::count('tickets',"status=?",[$st]) ?></span>
  </a>
  <?php endforeach; ?>
</div>

<div class="tbl-wrap"><table><thead><tr>
  <th><?= $isAr?'رقم التذكرة':'Ticket #' ?></th>
  <th><?= $isAr?'المستخدم':'User' ?></th>
  <th><?= $isAr?'الموضوع':'Subject' ?></th>
  <th><?= $isAr?'الأولوية':'Priority' ?></th>
  <th><?= $isAr?'الحالة':'Status' ?></th>
  <th><?= $isAr?'آخر تحديث':'Updated' ?></th>
  <th></th>
</tr></thead><tbody>
<?php foreach($tickets as $tk): ?>
<tr>
  <td style="font-family:monospace;font-size:11px;font-weight:700;color:var(--primary)">#<?= htmlspecialchars($tk['ticket_number']) ?></td>
  <td style="font-size:13px;font-weight:600"><?= htmlspecialchars($tk['name']) ?></td>
  <td style="font-size:12px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars(mb_substr($tk['subject'],0,50)) ?></td>
  <td><span class="bpill bp-<?= $tk['priority']==='urgent'?'out':($tk['priority']==='high'?'pending':'processing') ?>"><?= $tk['priority'] ?></span></td>
  <td><span class="bpill bp-<?= str_replace(['in_progress','waiting'],['processing','pending'],$tk['status']) ?>"><?= $tk['status'] ?></span></td>
  <td style="font-size:11px;color:var(--muted)"><?= date('Y-m-d H:i',strtotime($tk['updated_at'])) ?></td>
  <td>
    <div style="display:flex;gap:4px">
      <a href="<?= $adUrl ?>?p=tickets&action=view&id=<?= $tk['id'] ?>" class="btn btn-sm btn-info">💬 <?= $isAr?'عرض':'View' ?></a>
      <form method="POST" action="<?= $adUrl ?>" style="display:inline" onsubmit="return confirmDel()">
        <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
        <input type="hidden" name="action" value="delete_ticket">
        <input type="hidden" name="id" value="<?= $tk['id'] ?>">
        <input type="hidden" name="_redirect" value="?p=tickets&status=<?= $status ?>">
        <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
      </form>
    </div>
  </td>
</tr>
<?php endforeach; if(empty($tickets)): ?>
<tr><td colspan="7" style="text-align:center;padding:24px;color:var(--muted)">🎫 <?= $isAr?'لا توجد تذاكر في هذه الحالة':'No tickets in this status' ?></td></tr>
<?php endif; ?>
</tbody></table></div>

<?php if($total > 20): ?>
<div style="display:flex;gap:6px;justify-content:center;margin-top:14px;flex-wrap:wrap">
  <?php for($i=1;$i<=ceil($total/20);$i++): ?>
  <a href="<?= $adUrl ?>?p=tickets&status=<?= $status ?>&page=<?= $i ?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
  <?php endfor; ?>
</div>
<?php endif; ?>
