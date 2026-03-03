<?php if(!isset($myTickets)) { $myTickets = Ticket::userTickets(Auth::id()); } $isAr=Lang::isRtl(); $t=fn($k)=>Lang::get($k); ?>
<div class="page-container"><div class="page-container-inner">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px">
    <h2>🎫 <?= $isAr?'تذاكر الدعم':'Support Tickets' ?></h2>
    <button onclick="document.getElementById('newTicketBox').style.display=document.getElementById('newTicketBox').style.display==='none'?'block':'none'" style="background:linear-gradient(135deg,var(--primary),var(--accent));color:#fff;border:none;padding:9px 18px;border-radius:9px;font-weight:700;cursor:pointer;font-family:inherit;font-size:13px">
      ➕ <?= $isAr?'تذكرة جديدة':'New Ticket' ?>
    </button>
  </div>

  <div id="newTicketBox" style="display:none;background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px;margin-bottom:18px">
    <h3 style="font-size:14px;font-weight:700;margin-bottom:14px">📝 <?= $isAr?'فتح تذكرة جديدة':'Open New Ticket' ?></h3>
    <form method="POST">
      <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
      <input type="hidden" name="action" value="create_ticket">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
        <div class="fg"><label><?= $isAr?'الموضوع':'Subject' ?></label><input type="text" name="subject" required placeholder="<?= $isAr?'اكتب موضوع التذكرة':'Enter ticket subject' ?>"></div>
        <div class="fg"><label><?= $isAr?'الأولوية':'Priority' ?></label>
          <select name="priority">
            <option value="low"><?= $isAr?'منخفضة':'Low' ?></option>
            <option value="medium" selected><?= $isAr?'متوسطة':'Medium' ?></option>
            <option value="high"><?= $isAr?'عالية':'High' ?></option>
            <option value="urgent"><?= $isAr?'عاجلة':'Urgent' ?></option>
          </select>
        </div>
      </div>
      <div class="fg" style="margin-bottom:14px">
        <label><?= $isAr?'رسالتك':'Your Message' ?></label>
        <textarea name="message" required rows="4" style="min-height:100px" placeholder="<?= $isAr?'اشرح مشكلتك بالتفصيل...':'Describe your issue in detail...' ?>"></textarea>
      </div>
      <button type="submit" style="background:linear-gradient(135deg,var(--primary),var(--accent));color:#fff;border:none;padding:10px 22px;border-radius:9px;font-weight:700;cursor:pointer;font-family:inherit">📤 <?= $isAr?'إرسال':'Submit' ?></button>
    </form>
  </div>

  <?php if(empty($myTickets)): ?>
  <div style="text-align:center;padding:48px;background:var(--card);border:1px solid var(--border);border-radius:16px">
    <div style="font-size:56px;margin-bottom:14px">🎫</div>
    <h3><?= $isAr?'لا توجد تذاكر بعد':'No tickets yet' ?></h3>
    <p style="color:var(--muted);margin-top:8px"><?= $isAr?'افتح تذكرة للتواصل مع فريق الدعم':'Open a ticket to contact support' ?></p>
  </div>
  <?php else: foreach($myTickets as $tk):
    $statusColors = ['open'=>'var(--danger)','in_progress'=>'var(--warning)','waiting'=>'#0ea5e9','resolved'=>'var(--success)','closed'=>'var(--muted)'];
    $sc = $statusColors[$tk['status']] ?? 'var(--muted)'; ?>
  <a href="?page=ticket&id=<?= $tk['id'] ?>" style="display:block;background:var(--card);border:1px solid var(--border);border-radius:12px;padding:15px 18px;margin-bottom:10px;transition:all .2s;text-decoration:none;color:inherit" onmouseover="this.style.borderColor='var(--primary)';this.style.transform='translateY(-1px)'" onmouseout="this.style.borderColor='var(--border)';this.style.transform='none'">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:7px;flex-wrap:wrap;gap:6px">
      <span style="font-size:11px;color:var(--muted);font-family:monospace">#<?= htmlspecialchars($tk['ticket_number']) ?></span>
      <span style="font-size:11px;font-weight:700;color:<?= $sc ?>">● <?= $tk['status'] ?></span>
    </div>
    <div style="font-weight:700;font-size:14px;margin-bottom:5px"><?= htmlspecialchars($tk['subject']) ?></div>
    <div style="font-size:11px;color:var(--muted);display:flex;align-items:center;justify-content:space-between">
      <span><?= Helpers::timeAgo($tk['updated_at'], $isAr?'ar':'en') ?></span>
      <span class="bpill <?= in_array($tk['priority'],['high','urgent'])?'bp-out':'bp-processing' ?>"><?= $tk['priority'] ?></span>
    </div>
  </a>
  <?php endforeach; endif; ?>
</div>
</div></div>