<?php
$isAr = Lang::isRtl();
$sym  = htmlspecialchars(Setting::get('currency_symbol','ر.س'));
$page = max(1, Helpers::getInt('page',1));
$f    = ['status'=>Helpers::getStr('status'),'search'=>Helpers::getStr('q')];
$data = Order::adminAll($f, $page, 20);
$orders = $data['items'];
$total  = $data['total'];
$statuses = ['','pending','processing','completed','cancelled','refunded'];
?>
<div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;align-items:center">
  <form method="GET" style="display:flex;gap:6px;flex-wrap:wrap">
    <input type="hidden" name="p" value="orders">
    <input type="text" name="q" value="<?= htmlspecialchars($f['search']) ?>" placeholder="<?= $isAr?'رقم الطلب أو الإيميل':'Order # or email' ?>" style="width:200px">
    <select name="status">
      <option value=""><?= $isAr?'كل الحالات':'All Statuses' ?></option>
      <?php foreach(['pending','processing','completed','cancelled','refunded'] as $st): ?><option value="<?= $st ?>" <?= $f['status']===$st?'selected':'' ?>><?= $st ?></option><?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-secondary">🔍</button>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=orders" class="btn btn-secondary">✕</a>
  </form>
  <span style="font-size:12px;color:var(--muted);margin-right:auto"><?= $total ?> <?= $isAr?'طلب':'orders' ?></span>
</div>
<div class="tbl-wrap">
  <table><thead><tr>
    <th><?= $isAr?'رقم الطلب':'Order #' ?></th><th><?= $isAr?'العميل':'Customer' ?></th>
    <th><?= $isAr?'الإجمالي':'Total' ?></th><th><?= $isAr?'الدفع':'Payment' ?></th>
    <th><?= $isAr?'الحالة':'Status' ?></th><th><?= $isAr?'التاريخ':'Date' ?></th><th></th>
  </tr></thead><tbody>
  <?php foreach($orders as $o): ?>
  <tr>
    <td style="font-weight:700;font-size:12px"><?= htmlspecialchars($o['order_number']) ?></td>
    <td><?= htmlspecialchars($o['uname']??$o['guest_email']??'-') ?></td>
    <td><strong style="color:var(--success)"><?= $sym ?><?= number_format($o['total'],2) ?></strong></td>
    <td><?= htmlspecialchars($o['payment_method']??'-') ?></td>
    <td>
      <form method="POST" style="display:flex;gap:5px;align-items:center">
        <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
        <input type="hidden" name="action" value="update_order_status">
        <input type="hidden" name="id" value="<?= $o['id'] ?>">
        <select name="status" style="font-size:11px;padding:4px 8px;width:auto">
          <?php foreach(['pending','processing','completed','cancelled','refunded'] as $st): ?><option value="<?= $st ?>" <?= $o['status']===$st?'selected':'' ?>><?= $st ?></option><?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-sm btn-success">✓</button>
      </form>
    </td>
    <td style="font-size:11px;color:var(--muted)"><?= date('Y-m-d H:i',strtotime($o['created_at'])) ?></td>
    <td><a href="<?= Helpers::siteUrl('admin/') ?>?p=orders&action=view&id=<?= $o['id'] ?>" class="btn btn-sm btn-info">👁️</a></td>
  </tr>
  <?php endforeach; ?>
  </tbody></table>
</div>
