<?php $isAr=Lang::isRtl();
$status=Helpers::getStr('status','pending');
$page=max(1,Helpers::getInt('page',1));
$data=Review::adminAll($status,$page);
$reviews=$data['items']; $total=$data['total'];
?>
<div style="display:flex;gap:7px;margin-bottom:14px;flex-wrap:wrap">
  <?php foreach(['pending','approved','rejected'] as $st): ?>
  <a href="<?= Helpers::siteUrl('admin/') ?>?p=reviews&status=<?= $st ?>" class="btn btn-sm <?= $status===$st?'btn-primary':'btn-secondary' ?>"><?= $st ?> <span class="adm-badge"><?= Database::count('reviews',"status=?",[$st]) ?></span></a>
  <?php endforeach; ?>
</div>
<div class="tbl-wrap"><table><thead><tr>
  <th><?= $isAr?'المنتج':'Product' ?></th><th><?= $isAr?'المستخدم':'User' ?></th>
  <th><?= $isAr?'التقييم':'Rating' ?></th><th><?= $isAr?'التعليق':'Comment' ?></th>
  <th><?= $isAr?'التاريخ':'Date' ?></th><th></th>
</tr></thead><tbody>
<?php foreach($reviews as $r): ?>
<tr>
  <td style="font-size:12px"><?= htmlspecialchars($r['pname']) ?></td>
  <td><?= htmlspecialchars($r['name']) ?></td>
  <td><span style="color:#f59e0b">{'⭐'.str_repeat('⭐',$r['rating'])}</span> <?= $r['rating'] ?>/5</td>
  <td style="font-size:12px;color:var(--muted);max-width:240px"><?= htmlspecialchars(mb_substr($r['comment'],0,80)) ?>...</td>
  <td style="font-size:11px;color:var(--muted)"><?= date('Y-m-d',strtotime($r['created_at'])) ?></td>
  <td>
    <div style="display:flex;gap:5px">
      <?php if($r['status']!=='approved'): ?>
      <form method="POST" style="display:inline"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="review_action"><input type="hidden" name="id" value="<?= $r['id'] ?>"><input type="hidden" name="status" value="approved"><button type="submit" class="btn btn-sm btn-success">✅</button></form>
      <?php endif; ?>
      <?php if($r['status']!=='rejected'): ?>
      <form method="POST" style="display:inline"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="review_action"><input type="hidden" name="id" value="<?= $r['id'] ?>"><input type="hidden" name="status" value="rejected"><button type="submit" class="btn btn-sm btn-warning">❌</button></form>
      <?php endif; ?>
      <form method="POST" style="display:inline" onsubmit="return confirmDel()"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="delete_review"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button type="submit" class="btn btn-sm btn-danger">🗑️</button></form>
    </div>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
