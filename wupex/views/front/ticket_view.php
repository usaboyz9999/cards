<?php
$isAr = Lang::isRtl();
$sym  = htmlspecialchars(Setting::get('currency_symbol','ر.س'));
$S    = Setting::all();

$pColors = ['low'=>'#10b981','medium'=>'#f59e0b','high'=>'#ef4444','urgent'=>'#7c3aed'];
$pColor  = $pColors[$ticket['priority']] ?? '#7c3aed';
$statusMap = ['open'=>$isAr?'مفتوحة':'Open','in_progress'=>$isAr?'قيد المعالجة':'In Progress','waiting'=>$isAr?'انتظار':'Waiting','resolved'=>$isAr?'محلولة':'Resolved','closed'=>$isAr?'مغلقة':'Closed'];
$isClosed = $ticket['status']==='closed';
?>
<style>
.ticket-page{max-width:760px;width:100%;margin:0 auto;padding:24px 20px}
.tk-header{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px 20px;margin-bottom:16px}
.tk-meta{display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-top:10px}
.tk-pill{padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;border:1px solid currentColor}
.chat-box{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:16px;margin-bottom:16px;max-height:480px;overflow-y:auto;display:flex;flex-direction:column;gap:14px}
.msg-row{display:flex;gap:10px}
.msg-row.admin-msg{flex-direction:row-reverse}
.msg-avatar{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
.msg-bubble{max-width:75%;background:var(--bg);border:1px solid var(--border);border-radius:12px;padding:10px 14px;font-size:13px;line-height:1.7;white-space:pre-wrap}
.msg-row.admin-msg .msg-bubble{background:rgba(124,58,237,.1);border-color:rgba(124,58,237,.25)}
.msg-time{font-size:10px;color:var(--muted);margin-top:4px}
.reply-box{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px}
.reply-textarea{width:100%;background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:12px;color:inherit;font-family:inherit;font-size:13px;resize:vertical;min-height:90px;outline:none;transition:.2s;box-sizing:border-box}
.reply-textarea:focus{border-color:var(--primary)}
.tk-actions{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px}
</style>

<div class="ticket-page">
  <?php require VIEWS_PATH.'/partials/flash.php'; ?>

  <!-- Header -->
  <div class="tk-header">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap">
      <div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
          <a href="<?= Helpers::siteUrl('?page=tickets') ?>" style="color:var(--muted);font-size:13px;font-weight:600">← <?= $isAr?'التذاكر':'Tickets' ?></a>
          <span style="color:var(--primary);font-family:monospace;font-weight:700">#<?= htmlspecialchars($ticket['ticket_number']) ?></span>
        </div>
        <h2 style="font-size:16px;font-weight:800;margin:0"><?= htmlspecialchars($ticket['subject']) ?></h2>
      </div>
      <div class="tk-meta">
        <span class="tk-pill" style="color:<?= $pColor ?>;border-color:<?= $pColor ?>20;background:<?= $pColor ?>12"><?= $ticket['priority'] ?></span>
        <span class="tk-pill" style="color:var(--<?= $isClosed?'danger':'success' ?>);border-color:var(--<?= $isClosed?'danger':'success' ?>)20;background:var(--<?= $isClosed?'danger':'success' ?>)08">
          <?= $statusMap[$ticket['status']] ?? $ticket['status'] ?>
        </span>
        <span style="font-size:11px;color:var(--muted)"><?= date('Y-m-d H:i', strtotime($ticket['created_at'])) ?></span>
      </div>
    </div>

    <!-- Action buttons -->
    <div class="tk-actions" style="margin-top:14px;margin-bottom:0">
      <?php if(!$isClosed): ?>
      <form method="POST">
        <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
        <input type="hidden" name="action" value="user_close_ticket">
        <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
        <button type="submit" style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:var(--danger);padding:7px 16px;border-radius:9px;font-weight:700;cursor:pointer;font-family:inherit;font-size:12px">
          🔒 <?= $isAr?'إغلاق التذكرة':'Close Ticket' ?>
        </button>
      </form>
      <?php else: ?>
      <form method="POST">
        <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
        <input type="hidden" name="action" value="user_reopen_ticket">
        <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
        <button type="submit" style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:var(--success);padding:7px 16px;border-radius:9px;font-weight:700;cursor:pointer;font-family:inherit;font-size:12px">
          🔓 <?= $isAr?'إعادة فتح التذكرة':'Reopen Ticket' ?>
        </button>
      </form>
      <?php endif; ?>
    </div>
  </div>

  <!-- Chat -->
  <div class="chat-box" id="chatBox">
    <?php foreach($ticketReplies as $r): ?>
    <div class="msg-row <?= $r['is_admin']?'admin-msg':'' ?>">
      <div class="msg-avatar" style="background:<?= $r['is_admin']?'linear-gradient(135deg,var(--primary),var(--accent))':'var(--card2,var(--bg))' ?>;border:1px solid var(--border)">
        <?= $r['is_admin']?'👑':'👤' ?>
      </div>
      <div>
        <div class="msg-bubble">
          <?php if($r['is_admin']): ?>
          <div style="font-size:10px;font-weight:700;color:var(--primary);margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px"><?= $isAr?'فريق الدعم':'Support Team' ?></div>
          <?php endif; ?>
          <?= nl2br(htmlspecialchars($r['message'])) ?>
        </div>
        <div class="msg-time" style="text-align:<?= $r['is_admin']?'right':'left' ?>">
          <?= date('Y-m-d H:i', strtotime($r['created_at'])) ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <script>document.getElementById('chatBox').scrollTop=9999;</script>

  <!-- Reply Form -->
  <?php if(!$isClosed): ?>
  <div class="reply-box">
    <h3 style="font-size:13px;font-weight:700;margin-bottom:12px">💬 <?= $isAr?'أضف رداً':'Add Reply' ?></h3>
    <form method="POST">
      <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
      <input type="hidden" name="action" value="user_reply_ticket">
      <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
      <textarea name="message" class="reply-textarea" required
                placeholder="<?= $isAr?'اكتب ردك هنا...':'Write your reply here...' ?>"></textarea>
      <div style="display:flex;gap:8px;margin-top:10px">
        <button type="submit" style="background:linear-gradient(135deg,var(--primary),var(--accent));color:#fff;border:none;padding:10px 22px;border-radius:10px;font-weight:700;cursor:pointer;font-family:inherit">
          📤 <?= $isAr?'إرسال':'Send' ?>
        </button>
      </div>
    </form>
  </div>
  <?php else: ?>
  <div style="text-align:center;padding:18px;background:var(--card);border:1px solid var(--border);border-radius:12px;color:var(--muted);font-size:13px">
    🔒 <?= $isAr?'هذه التذكرة مغلقة. أعد فتحها للرد.':'This ticket is closed. Reopen it to reply.' ?>
  </div>
  <?php endif; ?>
</div>
