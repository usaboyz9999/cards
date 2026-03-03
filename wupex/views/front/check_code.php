<?php
$isAr = Lang::isRtl();
$result = null;
if($_SERVER['REQUEST_METHOD']==='POST' && Session::verifyCsrf($_POST['csrf']??'')) {
    $code = trim($_POST['code']??'');
    if($code) {
        $row = Database::fetch("SELECT c.*,p.name_ar,p.name_en,p.icon FROM ".DB_PREFIX."codes c LEFT JOIN ".DB_PREFIX."products p ON p.id=c.product_id WHERE c.code=? LIMIT 1",[$code]);
        $result = $row ?: false;
    }
}
?>
<div style="padding:20px;max-width:560px">
  <h2 style="margin-bottom:20px">🔍 <?= $isAr?'التحقق من كود':'Check Code' ?></h2>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:22px;margin-bottom:16px">
    <p style="color:var(--muted);font-size:13px;margin-bottom:16px"><?= $isAr?'أدخل الكود للتحقق من صلاحيته وحالته.':'Enter a code to check its validity and status.' ?></p>
    <form method="POST">
      <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
      <div class="fg" style="margin-bottom:12px">
        <label><?= $isAr?'الكود':'Code' ?></label>
        <input type="text" name="code" required placeholder="XXXX-XXXX-XXXX-XXXX" style="font-family:monospace;font-size:14px;letter-spacing:1px"
               value="<?= htmlspecialchars($_POST['code']??'') ?>">
      </div>
      <button type="submit" class="btn btn-primary btn-full">🔍 <?= $isAr?'تحقق':'Check' ?></button>
    </form>
  </div>
  <?php if($result !== null): ?>
  <div style="background:var(--card);border:1px solid <?= $result?'var(--success)':'var(--danger)' ?>;border-radius:14px;padding:18px">
    <?php if($result): ?>
    <div style="font-size:28px;text-align:center;margin-bottom:12px"><?= $result['icon'] ?? '🔑' ?></div>
    <div style="text-align:center;margin-bottom:12px">
      <div style="font-weight:700;font-size:15px"><?= htmlspecialchars($isAr?$result['name_ar']:$result['name_en']) ?></div>
    </div>
    <div style="display:flex;justify-content:space-between;font-size:13px;padding:7px 0;border-bottom:1px solid var(--border)">
      <span style="color:var(--muted)"><?= $isAr?'الحالة':'Status' ?></span>
      <span class="bpill bp-<?= $result['status']==='available'?'active':'out' ?>"><?= $result['status'] ?></span>
    </div>
    <div style="display:flex;justify-content:space-between;font-size:13px;padding:7px 0">
      <span style="color:var(--muted)"><?= $isAr?'تاريخ الإنشاء':'Created' ?></span>
      <span><?= date('Y-m-d',strtotime($result['created_at'])) ?></span>
    </div>
    <?php else: ?>
    <div style="text-align:center;color:var(--danger)">
      <div style="font-size:36px;margin-bottom:8px">❌</div>
      <div style="font-weight:700"><?= $isAr?'الكود غير موجود':'Code not found' ?></div>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>
