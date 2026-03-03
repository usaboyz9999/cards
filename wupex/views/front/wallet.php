<?php $lang=Lang::current();$isAr=Lang::isRtl();$t=fn($k)=>Lang::get($k);$sym=htmlspecialchars(Setting::get('currency_symbol','ر.س'));$user=Auth::user();?>
<div class="page-container"><div class="page-container-inner">
  <div class="wallet-card" style="max-width:400px;margin-bottom:20px">
    <div class="wallet-label">💰 <?= $t('available_balance') ?></div>
    <div class="wallet-balance"><?= $sym ?><?= number_format($user['wallet_balance'],2) ?></div>
    <div style="margin-top:14px;display:flex;gap:10px">
      <a href="?page=deposit" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3)"><?= $t('deposit') ?></a>
      <a href="?page=transactions" class="btn btn-sm" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.2)"><?= $t('transactions') ?></a>
    </div>
  </div>
  <?php if(!empty($transactions)): ?>
  <div class="tbl-wrap">
    <div class="tbl-head"><div class="tbl-title">📊 <?= $isAr?'آخر المعاملات':'Recent Transactions' ?></div></div>
    <table><thead><tr><th><?= $isAr?'النوع':'Type' ?></th><th><?= $isAr?'المبلغ':'Amount' ?></th><th><?= $isAr?'الرصيد بعد':'Balance After' ?></th><th><?= $isAr?'التاريخ':'Date' ?></th></tr></thead><tbody>
    <?php foreach($transactions as $tx): $isCredit=$tx['amount']>0; ?>
    <tr>
      <td><span class="bpill <?= $isCredit?'bp-in':'bp-out' ?>"><?= htmlspecialchars($isAr?$tx['description_ar']:$tx['description_en']) ?></span></td>
      <td style="font-weight:700;color:<?= $isCredit?'#10b981':'#ef4444' ?>"><?= $isCredit?'+':'' ?><?= $sym ?><?= number_format(abs($tx['amount']),2) ?></td>
      <td><?= $sym ?><?= number_format($tx['balance_after'],2) ?></td>
      <td style="color:var(--muted);font-size:11px"><?= $tx['created_at'] ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody></table>
  </div>
  <?php endif; ?>
</div>
</div></div>