<?php if(!isset($notifs)) { Notification::markRead(Auth::id()); $notifs = Database::fetchAll("SELECT * FROM ".DB_PREFIX."notifications WHERE user_id=? OR is_broadcast=1 ORDER BY created_at DESC LIMIT 50",[Auth::id()]); } $isAr=Lang::isRtl(); ?>
<div class="page-container"><div class="page-container-inner">
  <h2 style="margin-bottom:18px">🔔 <?= $isAr?'الإشعارات':'Notifications' ?></h2>
  <?php if(empty($notifs)): ?>
  <div style="text-align:center;padding:48px;background:var(--card);border:1px solid var(--border);border-radius:16px">
    <div style="font-size:56px;margin-bottom:14px">🔔</div>
    <h3><?= $isAr?'لا توجد إشعارات':'No notifications' ?></h3>
  </div>
  <?php else: foreach($notifs as $n): $isRead=$n['is_read']; ?>
  <div style="background:<?= $isRead?'var(--card2)':'var(--card)' ?>;border:1px solid <?= $isRead?'var(--border)':'rgba(124,58,237,.4)' ?>;border-radius:12px;padding:14px 16px;margin-bottom:10px;display:flex;align-items:flex-start;gap:12px">
    <div style="font-size:22px;flex-shrink:0;margin-top:2px"><?= $n['icon']??'🔔' ?></div>
    <div style="flex:1;min-width:0">
      <div style="font-weight:700;font-size:14px"><?= htmlspecialchars($isAr?$n['title_ar']:$n['title_en']) ?></div>
      <div style="font-size:12px;color:var(--muted);margin-top:4px;line-height:1.6"><?= htmlspecialchars($isAr?$n['message_ar']:$n['message_en']) ?></div>
      <div style="font-size:10px;color:var(--muted);margin-top:6px"><?= Helpers::timeAgo($n['created_at'],$isAr?'ar':'en') ?></div>
    </div>
    <?php if(!$isRead): ?><div style="width:8px;height:8px;background:var(--primary);border-radius:50%;flex-shrink:0;margin-top:6px"></div><?php endif; ?>
  </div>
  <?php endforeach; endif; ?>
</div>
</div></div>