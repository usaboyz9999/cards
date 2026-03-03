<?php
$isAr  = Lang::isRtl();
$sym   = htmlspecialchars(Setting::get('currency_symbol','ر.س'));
$status = Helpers::getStr('status','pending');
$allowed_status = ['pending','approved','rejected'];
if (!in_array($status, $allowed_status)) $status = 'pending';
$deposits = Database::fetchAll(
    "SELECT dr.*,u.name as uname,u.email FROM ".DB_PREFIX."deposit_requests dr 
     LEFT JOIN ".DB_PREFIX."users u ON u.id=dr.user_id 
     WHERE dr.status=? ORDER BY dr.created_at DESC LIMIT 50",
    [$status]
);
?>
<div style="display:flex;gap:7px;margin-bottom:14px;flex-wrap:wrap">
  <?php foreach(['pending','approved','rejected'] as $st): ?>
  <a href="<?= Helpers::siteUrl('admin/') ?>?p=deposits&status=<?= $st ?>" class="btn btn-sm <?= $status===$st?'btn-primary':'btn-secondary' ?>"><?= $st ?> (<?= Database::count('deposit_requests',"status='$st'") ?>)</a>
  <?php endforeach; ?>
</div>
<div class="tbl-wrap"><table><thead><tr>
  <th>Ref</th><th><?= $isAr?'المستخدم':'User' ?></th><th><?= $isAr?'المبلغ':'Amount' ?></th>
  <th>Bonus</th><th>Status</th><th>Date</th><th></th>
</tr></thead><tbody>
<?php foreach($deposits as $d): ?>
<tr>
  <td style="font-family:monospace;font-size:11px;color:var(--primary)"><?= htmlspecialchars($d['ref_number']??'#'.$d['id']) ?></td>
  <td><div style="font-weight:700"><?= htmlspecialchars($d['uname']??'-') ?></div><div style="font-size:10px;color:var(--muted)"><?= htmlspecialchars($d['email']??'') ?></div></td>
  <td style="font-weight:700;color:var(--success)"><?= $sym ?><?= number_format($d['amount'],2) ?></td>
  <td style="color:var(--warning)"><?= ($d['bonus']??0)>0?'+'.$sym.number_format($d['bonus'],2):'—' ?></td>
  <td><span class="bpill bp-<?= $d['status'] ?>"><?= $d['status'] ?></span></td>
  <td style="font-size:11px;color:var(--muted)"><?= date('Y-m-d H:i',strtotime($d['created_at'])) ?></td>
  <td>
    <?php if($d['status']==='pending'): ?>
    <div style="display:flex;gap:5px">
      <form method="POST" action="<?= Helpers::siteUrl('admin/') ?>" style="display:inline"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="approve_deposit"><input type="hidden" name="_redirect" value="?p=deposits&status=pending"><input type="hidden" name="id" value="<?= $d['id'] ?>"><button type="submit" class="btn btn-sm btn-success">✅</button></form>
      <form method="POST" action="<?= Helpers::siteUrl('admin/') ?>" style="display:inline"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="reject_deposit"><input type="hidden" name="_redirect" value="?p=deposits&status=pending"><input type="hidden" name="id" value="<?= $d['id'] ?>"><button type="submit" class="btn btn-sm btn-danger">❌</button></form>
    </div>
    <?php endif; ?>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
