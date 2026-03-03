<?php $isAr=Lang::isRtl();
$txns=Database::fetchAll("SELECT pt.*,u.name as uname FROM ".DB_PREFIX."points_transactions pt LEFT JOIN ".DB_PREFIX."users u ON u.id=pt.user_id ORDER BY pt.created_at DESC LIMIT 100");
$topUsers=Database::fetchAll("SELECT id,name,points FROM ".DB_PREFIX."users WHERE role='user' ORDER BY points DESC LIMIT 10");
?>
<div style="display:grid;grid-template-columns:1fr 300px;gap:16px">
  <div class="tbl-wrap"><table><thead><tr>
    <th><?= $isAr?'المستخدم':'User' ?></th><th>Type</th><th>Points</th><th>Ref</th><th>Date</th>
  </tr></thead><tbody>
  <?php foreach($txns as $t): ?>
  <tr>
    <td style="font-weight:700"><?= htmlspecialchars($t['uname']??'-') ?></td>
    <td><span class="bpill bp-processing"><?= $t['type'] ?></span></td>
    <td style="font-weight:700;color:<?= $t['amount']>0?'var(--success)':'var(--danger)' ?>"><?= $t['amount']>0?'+':'' ?><?= number_format($t['amount']) ?></td>
    <td style="font-size:11px;color:var(--muted)"><?= htmlspecialchars($t['ref']??'') ?></td>
    <td style="font-size:11px;color:var(--muted)"><?= date('Y-m-d H:i',strtotime($t['created_at'])) ?></td>
  </tr>
  <?php endforeach; ?>
  </tbody></table></div>
  <div class="frm-card" style="margin-bottom:0">
    <h3>🏆 Top Users</h3>
    <?php foreach($topUsers as $i=>$u): ?>
    <div style="display:flex;align-items:center;gap:9px;padding:7px 0;border-bottom:1px solid var(--border)">
      <span style="font-size:16px;width:24px;text-align:center"><?= ['🥇','🥈','🥉'][$i]??'#'.($i+1) ?></span>
      <span style="flex:1;font-weight:700;font-size:13px"><?= htmlspecialchars($u['name']) ?></span>
      <span style="color:var(--primary);font-weight:700"><?= number_format($u['points']??0) ?> pts</span>
    </div>
    <?php endforeach; ?>
  </div>
</div>
