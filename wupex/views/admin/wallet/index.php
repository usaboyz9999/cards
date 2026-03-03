<?php $isAr=Lang::isRtl(); $sym=htmlspecialchars(Setting::get('currency_symbol','ر.س'));
$txns=Database::fetchAll("SELECT wt.*,u.name as uname FROM ".DB_PREFIX."wallet_transactions wt LEFT JOIN ".DB_PREFIX."users u ON u.id=wt.user_id ORDER BY wt.created_at DESC LIMIT 100");
$totalBalance=Database::fetch("SELECT COALESCE(SUM(wallet_balance),0) as v FROM ".DB_PREFIX."users")['v']??0;
$totalCredits=Database::fetch("SELECT COALESCE(SUM(amount),0) as v FROM ".DB_PREFIX."wallet_transactions WHERE type='credit'")['v']??0;
$totalDebits=Database::fetch("SELECT COALESCE(SUM(amount),0) as v FROM ".DB_PREFIX."wallet_transactions WHERE type='debit'")['v']??0;
?>
<div class="stats-row" style="margin-bottom:16px">
  <div class="stat-c"><div class="stat-num" style="color:var(--success)"><?= $sym ?><?= number_format($totalBalance,2) ?></div><div class="stat-lbl">💰 <?= $isAr?'إجمالي الأرصدة':'Total Balances' ?></div></div>
  <div class="stat-c"><div class="stat-num" style="color:var(--success)"><?= $sym ?><?= number_format($totalCredits,2) ?></div><div class="stat-lbl">⬆️ <?= $isAr?'إجمالي الإيداعات':'Total Credits' ?></div></div>
  <div class="stat-c"><div class="stat-num" style="color:var(--danger)"><?= $sym ?><?= number_format($totalDebits,2) ?></div><div class="stat-lbl">⬇️ <?= $isAr?'إجمالي السحوبات':'Total Debits' ?></div></div>
  <div class="stat-c"><div class="stat-num"><?= Database::count('wallet_transactions') ?></div><div class="stat-lbl">📋 <?= $isAr?'المعاملات':'Transactions' ?></div></div>
</div>
<div class="tbl-wrap"><table><thead><tr>
  <th><?= $isAr?'المستخدم':'User' ?></th><th>Type</th><th>Amount</th><th>Balance After</th><th>Note</th><th>Date</th>
</tr></thead><tbody>
<?php foreach($txns as $t): ?>
<tr>
  <td style="font-weight:700"><?= htmlspecialchars($t['uname']??'-') ?></td>
  <td><span class="bpill <?= $t['type']==='credit'?'bp-active':'bp-out' ?>"><?= $t['type'] ?></span></td>
  <td style="font-weight:700;color:<?= $t['type']==='credit'?'var(--success)':'var(--danger)' ?>"><?= $t['type']==='credit'?'+':'-' ?><?= $sym ?><?= number_format($t['amount'],2) ?></td>
  <td style="font-size:12px"><?= $sym ?><?= number_format($t['balance_after']??0,2) ?></td>
  <td style="font-size:11px;color:var(--muted)"><?= htmlspecialchars(mb_substr($t['description']??'',0,40)) ?></td>
  <td style="font-size:11px;color:var(--muted)"><?= date('Y-m-d H:i',strtotime($t['created_at'])) ?></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
