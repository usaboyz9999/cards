<?php $isAr=Lang::isRtl(); ?>
<div class="stabs">
  <?php $tabs=[['password',$isAr?'كلمة المرور':'Password','🔐'],['blocked_ips',$isAr?'IPs المحظورة':'Blocked IPs','🚫'],['login_log',$isAr?'سجل الدخول':'Login Log','📋']];
  $curTab=$_GET['tab']??'password';
  foreach($tabs as [$id,$lbl,$ic]): ?>
  <button class="stab <?= $curTab===$id?'active':'' ?>" data-tab="<?= $id ?>" onclick="goStab('<?= $id ?>')"><?= $ic ?> <?= $lbl ?></button>
  <?php endforeach; ?>
</div>

<div class="stab-pane <?= $curTab==='password'?'active':'' ?>" id="tab-password">
  <div class="frm-card" style="max-width:480px"><h3>🔐 <?= $isAr?'تغيير كلمة مرور المدير':'Change Admin Password' ?></h3>
    <form method="POST">
      <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
      <input type="hidden" name="action" value="change_admin_pass">
      <div class="fg" style="margin-bottom:12px"><label><?= $isAr?'كلمة المرور الحالية':'Current Password' ?></label><input type="password" name="current" required></div>
      <div class="fg" style="margin-bottom:12px"><label><?= $isAr?'الجديدة':'New Password' ?></label><input type="password" name="new_pass" required minlength="8"></div>
      <div class="fg" style="margin-bottom:14px"><label><?= $isAr?'تأكيد':'Confirm' ?></label><input type="password" name="confirm" required></div>
      <button type="submit" class="btn btn-primary">🔒 <?= $isAr?'تحديث':'Update' ?></button>
    </form>
  </div>
</div>

<div class="stab-pane <?= $curTab==='blocked_ips'?'active':'' ?>" id="tab-blocked_ips">
  <div style="display:flex;justify-content:flex-end;margin-bottom:12px">
    <button class="btn btn-danger btn-sm" onclick="openModal('blockIpModal')">🚫 <?= $isAr?'حظر IP':'Block IP' ?></button>
  </div>
  <?php $blocked=Database::fetchAll("SELECT * FROM ".DB_PREFIX."blocked_ips ORDER BY created_at DESC LIMIT 50"); ?>
  <div class="tbl-wrap"><table><thead><tr>
    <th>IP</th><th><?= $isAr?'السبب':'Reason' ?></th><th><?= $isAr?'حتى':'Until' ?></th><th><?= $isAr?'دائم':'Permanent' ?></th><th></th>
  </tr></thead><tbody>
  <?php foreach($blocked as $b): ?>
  <tr>
    <td style="font-family:monospace;font-weight:700"><?= htmlspecialchars($b['ip_address']) ?></td>
    <td style="font-size:12px;color:var(--muted)"><?= htmlspecialchars($b['reason']) ?></td>
    <td style="font-size:11px"><?= $b['blocked_until'] ? date('Y-m-d H:i',strtotime($b['blocked_until'])) : '-' ?></td>
    <td><?= $b['permanent']?'✅':'-' ?></td>
    <td><form method="POST" style="display:inline" onsubmit="return confirmDel()"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="unblock_ip"><input type="hidden" name="id" value="<?= $b['id'] ?>"><button type="submit" class="btn btn-sm btn-success">✓ <?= $isAr?'رفع':'Unblock' ?></button></form></td>
  </tr>
  <?php endforeach; ?>
  </tbody></table></div>
  <div class="modal-ov" id="blockIpModal"><div class="modal-box" style="max-width:400px">
    <div class="modal-hdr"><h3>🚫 Block IP</h3><button class="modal-close" onclick="closeModal('blockIpModal')">×</button></div>
    <form method="POST"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="block_ip">
    <div class="modal-body">
      <div class="grid-2">
        <div class="fg full"><label>IP Address</label><input type="text" name="ip" required placeholder="0.0.0.0"></div>
        <div class="fg full"><label>Reason</label><input type="text" name="reason" required></div>
        <div class="fg"><label>Duration (minutes)</label><input type="number" name="minutes" value="60"></div>
        <label class="chk-row" style="align-self:flex-end;padding-bottom:9px"><input type="checkbox" name="permanent" value="1"><span>Permanent</span></label>
      </div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="closeModal('blockIpModal')">Cancel</button><button type="submit" class="btn btn-danger">🚫 Block</button></div>
    </form>
  </div></div>
</div>

<div class="stab-pane <?= $curTab==='login_log'?'active':'' ?>" id="tab-login_log">
  <?php $logs=Database::fetchAll("SELECT * FROM ".DB_PREFIX."login_attempts ORDER BY attempted_at DESC LIMIT 100"); ?>
  <div class="tbl-wrap"><table><thead><tr>
    <th>Email</th><th>IP</th><th><?= $isAr?'النتيجة':'Result' ?></th><th><?= $isAr?'الوقت':'Time' ?></th>
  </tr></thead><tbody>
  <?php foreach($logs as $l): ?>
  <tr>
    <td style="font-size:12px"><?= htmlspecialchars($l['email']??'-') ?></td>
    <td style="font-family:monospace;font-size:12px"><?= htmlspecialchars($l['ip_address']) ?></td>
    <td><span class="bpill <?= $l['success']?'bp-active':'bp-out' ?>"><?= $l['success']?'✅ Success':'❌ Failed' ?></span></td>
    <td style="font-size:11px;color:var(--muted)"><?= $l['attempted_at'] ?></td>
  </tr>
  <?php endforeach; ?>
  </tbody></table></div>
</div>
<script>document.addEventListener('DOMContentLoaded',()=>goStab('<?= htmlspecialchars($_GET['tab']??'password') ?>',false));</script>
