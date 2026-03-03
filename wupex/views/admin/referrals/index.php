<?php $isAr=Lang::isRtl(); $sym=htmlspecialchars(Setting::get('currency_symbol','ر.س'));
$refs=Database::fetchAll("SELECT r.*,u.name as rname,u2.name as referred FROM ".DB_PREFIX."referrals r LEFT JOIN ".DB_PREFIX."users u ON u.id=r.referrer_id LEFT JOIN ".DB_PREFIX."users u2 ON u2.id=r.referred_id ORDER BY r.created_at DESC LIMIT 100");
?>
<div class="stats-row" style="margin-bottom:16px">
  <div class="stat-c"><div class="stat-num"><?= Database::count('referrals') ?></div><div class="stat-lbl">🔗 <?= $isAr?'إجمالي الإحالات':'Total Referrals' ?></div></div>
  <div class="stat-c"><div class="stat-num" style="color:var(--success)"><?= $sym ?><?= number_format(Database::fetch("SELECT COALESCE(SUM(commission),0) as v FROM ".DB_PREFIX."referrals")['v']??0, 2) ?></div><div class="stat-lbl">💰 <?= $isAr?'إجمالي العمولات':'Total Commissions' ?></div></div>
</div>
<div class="tbl-wrap"><table><thead><tr>
  <th><?= $isAr?'المُحيل':'Referrer' ?></th><th><?= $isAr?'المُحال':'Referred' ?></th>
  <th><?= $isAr?'العمولة':'Commission' ?></th><th>Date</th>
</tr></thead><tbody>
<?php foreach($refs as $r): ?>
<tr>
  <td style="font-weight:700"><?= htmlspecialchars($r['rname']??'-') ?></td>
  <td><?= htmlspecialchars($r['referred']??'-') ?></td>
  <td style="color:var(--success);font-weight:700"><?= $sym ?><?= number_format($r['commission']??0,2) ?></td>
  <td style="font-size:11px;color:var(--muted)"><?= date('Y-m-d H:i',strtotime($r['created_at'])) ?></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
