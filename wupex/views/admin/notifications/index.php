<?php $isAr=Lang::isRtl(); ?>
<div class="frm-card" style="max-width:600px;margin-bottom:20px">
  <h3>📢 <?= $isAr?'إرسال إشعار جماعي':'Broadcast Notification' ?></h3>
  <form method="POST"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="broadcast_notification">
    <div class="grid-2">
      <div class="fg"><label>Title AR</label><input type="text" name="title_ar" required></div>
      <div class="fg"><label>Title EN</label><input type="text" name="title_en" required></div>
      <div class="fg full"><label>Message AR</label><textarea name="message_ar" required></textarea></div>
      <div class="fg full"><label>Message EN</label><textarea name="message_en" required></textarea></div>
      <div class="fg"><label>Icon</label><input type="text" name="icon" value="📢"></div>
      <div class="fg"><label>Link (optional)</label><input type="url" name="link"></div>
    </div>
    <button type="submit" class="btn btn-primary" style="margin-top:10px">📢 <?= $isAr?'إرسال للجميع':'Send to All' ?></button>
  </form>
</div>
<?php $notifs=Database::fetchAll("SELECT * FROM ".DB_PREFIX."notifications WHERE is_broadcast=1 ORDER BY created_at DESC LIMIT 20"); ?>
<div class="tbl-wrap"><table><thead><tr><th>Icon</th><th>Title</th><th>Date</th></tr></thead><tbody>
<?php foreach($notifs as $n): ?>
<tr><td style="font-size:20px"><?= $n['icon'] ?></td><td><strong><?= htmlspecialchars($isAr?$n['title_ar']:$n['title_en']) ?></strong></td><td style="font-size:11px;color:var(--muted)"><?= date('Y-m-d H:i',strtotime($n['created_at'])) ?></td></tr>
<?php endforeach; ?>
</tbody></table></div>
