<?php
$lang = Lang::current();
$isAr = Lang::isRtl();
$t    = fn($k) => Lang::get($k);
$user = Auth::user();
$sym  = htmlspecialchars(Setting::get('currency_symbol','ر.س'));
$S    = Setting::all();
$tab  = $_GET['tab'] ?? 'profile';
?>
<div class="page-container"><div class="page-container-inner">
  <?php require VIEWS_PATH.'/partials/flash.php'; ?>

  <!-- Header بطاقة المستخدم -->
  <div style="background:linear-gradient(135deg,var(--primary),var(--accent));border-radius:16px;padding:20px 24px;margin-bottom:20px;display:flex;align-items:center;gap:16px;flex-wrap:wrap">
    <div style="width:60px;height:60px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:900;color:#fff;flex-shrink:0">
      <?= mb_substr($user['name'],0,1) ?>
    </div>
    <div style="flex:1">
      <div style="font-weight:900;font-size:18px"><?= htmlspecialchars($user['name']) ?></div>
      <div style="font-size:12px;opacity:.8"><?= htmlspecialchars($user['email']) ?></div>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap">
      <div style="text-align:center;background:rgba(255,255,255,.15);border-radius:10px;padding:8px 14px">
        <div style="font-weight:900;font-size:16px"><?= $sym ?><?= number_format($user['wallet_balance']??0,2) ?></div>
        <div style="font-size:10px;opacity:.8"><?= $isAr?'المحفظة':'Wallet' ?></div>
      </div>
      <div style="text-align:center;background:rgba(255,255,255,.15);border-radius:10px;padding:8px 14px">
        <div style="font-weight:900;font-size:16px"><?= number_format($user['points']??0) ?></div>
        <div style="font-size:10px;opacity:.8"><?= $isAr?'النقاط':'Points' ?></div>
      </div>
    </div>
    <a href="?page=logout" style="background:rgba(239,68,68,.3);border:1px solid rgba(239,68,68,.5);color:#fff;padding:8px 14px;border-radius:9px;font-size:13px;font-weight:700">🚪</a>
  </div>

  <!-- Tabs -->
  <div style="display:flex;gap:4px;flex-wrap:wrap;margin-bottom:16px;background:var(--card);border:1px solid var(--border);border-radius:12px;padding:6px">
    <?php
    $tabs = [
      'profile'      => ['👤', $isAr?'الملف':'Profile'],
      'orders'       => ['📦', $isAr?'الطلبات':'Orders'],
      'wallet'       => ['💰', $isAr?'المحفظة':'Wallet'],
      'deposit'      => ['➕', $isAr?'شحن':'Top Up'],
      'wishlist'     => ['❤️', $isAr?'المفضلة':'Wishlist'],
      'tickets'      => ['🎫', $isAr?'الدعم':'Support'],
      'notifications'=> ['🔔', $isAr?'الإشعارات':'Notifications'],
    ];
    if(!empty($S['points_enabled']))   $tabs['points']   = ['💎', $isAr?'النقاط':'Points'];
    if(!empty($S['referral_enabled'])) $tabs['referral'] = ['🔗', $isAr?'الإحالات':'Referral'];
    $tabs['invoices'] = ['🧾', $isAr?'الفواتير':'Invoices'];
    $tabs['settings'] = ['⚙️', $isAr?'الإعدادات':'Settings'];
    foreach($tabs as $k=>[$ic,$lbl]):
    ?>
    <a href="?page=account&tab=<?= $k ?>"
       style="display:flex;align-items:center;gap:5px;padding:7px 12px;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;transition:all .2s;<?= $tab===$k?'background:var(--primary);color:#fff':'color:var(--muted)' ?>">
      <?= $ic ?> <span class="nav-lbl"><?= $lbl ?></span>
    </a>
    <?php endforeach; ?>
  </div>

  <!-- Tab Content -->
  <?php if($tab === 'profile'): ?>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:22px">
    <h3 style="margin-bottom:16px">👤 <?= $isAr?'تعديل الملف الشخصي':'Edit Profile' ?></h3>
    <form method="POST">
      <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
      <input type="hidden" name="action" value="update_profile">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        <div class="fg"><label><?= $isAr?'الاسم الكامل':'Full Name' ?></label><input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required></div>
        <div class="fg"><label><?= $isAr?'اسم المستخدم':'Username' ?></label><input type="text" name="username" value="<?= htmlspecialchars($user['username']??'') ?>"></div>
        <div class="fg"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></div>
        <div class="fg"><label><?= $isAr?'الهاتف':'Phone' ?></label><input type="text" name="phone" value="<?= htmlspecialchars($user['phone']??'') ?>"></div>
      </div>
      <button type="submit" class="btn btn-primary" style="margin-top:10px">💾 <?= $isAr?'حفظ':'Save' ?></button>
    </form>
    <hr style="border:none;border-top:1px solid var(--border);margin:20px 0">
    <h3 style="margin-bottom:16px">🔐 <?= $isAr?'تغيير كلمة المرور':'Change Password' ?></h3>
    <form method="POST">
      <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
      <input type="hidden" name="action" value="change_password">
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px">
        <div class="fg"><label><?= $isAr?'الحالية':'Current' ?></label><input type="password" name="current_pass" required></div>
        <div class="fg"><label><?= $isAr?'الجديدة':'New' ?></label><input type="password" name="new_pass" required minlength="8"></div>
        <div class="fg"><label><?= $isAr?'تأكيد':'Confirm' ?></label><input type="password" name="confirm_pass" required></div>
      </div>
      <button type="submit" class="btn btn-warning" style="margin-top:10px">🔐 <?= $isAr?'تغيير':'Change' ?></button>
    </form>
  </div>

  <?php elseif($tab === 'orders'):
    $orders = Order::userOrders(Auth::id()); ?>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px">
    <h3 style="margin-bottom:14px">📦 <?= $isAr?'طلباتي':'My Orders' ?></h3>
    <?php if(empty($orders)): ?>
    <div style="text-align:center;padding:30px;color:var(--muted)">📦 <?= $isAr?'لا توجد طلبات':'No orders yet' ?></div>
    <?php else: foreach($orders as $o):
      $items = Database::fetchAll("SELECT * FROM ".DB_PREFIX."order_items WHERE order_id=?",[$o['id']]);
    ?>
    <div style="border:1px solid var(--border);border-radius:10px;padding:14px;margin-bottom:10px">
      <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;margin-bottom:10px">
        <div style="font-family:monospace;font-weight:700;color:var(--primary)"><?= htmlspecialchars($o['order_number']) ?></div>
        <div style="display:flex;gap:8px;align-items:center">
          <span class="bpill bp-<?= $o['status'] ?>"><?= $o['status'] ?></span>
          <span style="font-weight:700;color:var(--success)"><?= $sym ?><?= number_format($o['total'],2) ?></span>
          <span style="font-size:11px;color:var(--muted)"><?= date('Y-m-d',strtotime($o['created_at'])) ?></span>
        </div>
      </div>
      <?php foreach($items as $it): ?>
      <div style="background:var(--bg);border-radius:8px;padding:8px 12px;margin-bottom:6px;font-size:13px">
        <div style="font-weight:700"><?= htmlspecialchars($it['product_name']) ?> <?php if($it['price_label']): ?><span style="color:var(--muted)">(<?= htmlspecialchars($it['price_label']) ?>)</span><?php endif; ?></div>
        <?php if($it['codes']): ?>
        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:6px">
          <?php foreach(explode(',',$it['codes']) as $cd): if(!trim($cd)) continue; ?>
          <div onclick="navigator.clipboard.writeText('<?= htmlspecialchars(trim($cd)) ?>').then(()=>showToast('✅ <?= $isAr?'تم النسخ':'Copied' ?>','success'))"
               style="background:var(--card);border:1px solid var(--primary);border-radius:6px;padding:4px 10px;font-family:monospace;font-size:12px;cursor:pointer;font-weight:700;color:var(--primary)"
               title="<?= $isAr?'انقر للنسخ':'Click to copy' ?>">
            📋 <?= htmlspecialchars(trim($cd)) ?>
          </div>
          <?php endforeach; ?>
        </div>
        <?php elseif($o['status']==='processing'): ?>
        <div style="color:var(--warning);font-size:12px;margin-top:4px">⏳ <?= $isAr?'جارٍ التجهيز...':'Processing...' ?></div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endforeach; endif; ?>
  </div>

  <?php elseif($tab === 'wallet'):
    $txns = Wallet::transactions(Auth::id(), 30);
    $balance = Wallet::balance(Auth::id()); ?>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px">
    <div style="background:linear-gradient(135deg,#10b981,#059669);border-radius:12px;padding:18px;text-align:center;margin-bottom:16px">
      <div style="font-size:12px;opacity:.8"><?= $isAr?'رصيد المحفظة':'Wallet Balance' ?></div>
      <div style="font-size:32px;font-weight:900"><?= $sym ?><?= number_format($balance,2) ?></div>
      <a href="?page=account&tab=deposit" style="display:inline-block;margin-top:10px;background:rgba(255,255,255,.2);padding:6px 16px;border-radius:20px;font-size:13px;font-weight:700;color:#fff">➕ <?= $isAr?'شحن المحفظة':'Top Up' ?></a>
    </div>
    <?php foreach($txns as $tx): ?>
    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.05);font-size:13px">
      <div>
        <span style="font-weight:700;color:<?= $tx['type']==='credit'?'var(--success)':'var(--danger)' ?>"><?= $tx['type']==='credit'?'+':'-' ?><?= $sym ?><?= number_format($tx['amount'],2) ?></span>
        <span style="color:var(--muted);margin-<?= $isAr?'right':'left' ?>:8px"><?= htmlspecialchars($tx['source']??$tx['type']) ?></span>
      </div>
      <span style="font-size:11px;color:var(--muted)"><?= date('Y-m-d H:i',strtotime($tx['created_at'])) ?></span>
    </div>
    <?php endforeach; ?>
    <?php if(empty($txns)): ?><div style="text-align:center;padding:20px;color:var(--muted)"><?= $isAr?'لا توجد معاملات':'No transactions' ?></div><?php endif; ?>
  </div>

  <?php elseif($tab === 'deposit'):
    $_GET['page'] = 'deposit'; require VIEWS_PATH.'/front/deposit.php';

  elseif($tab === 'wishlist'):
    $wishItems = Wishlist::items(Auth::id());
    require VIEWS_PATH.'/front/wishlist.php';

  elseif($tab === 'tickets'):
    $myTickets = Ticket::userTickets(Auth::id());
    require VIEWS_PATH.'/front/tickets.php';

  elseif($tab === 'notifications'):
    Notification::markRead(Auth::id());
    $notifs = Database::fetchAll("SELECT * FROM ".DB_PREFIX."notifications WHERE user_id=? OR is_broadcast=1 ORDER BY created_at DESC LIMIT 50",[Auth::id()]);
    require VIEWS_PATH.'/front/notifications.php';

  elseif($tab === 'points'):
    require VIEWS_PATH.'/front/points.php';

  elseif($tab === 'referral'):
    require VIEWS_PATH.'/front/referral.php';

  elseif($tab === 'invoices'):
    $orders = Order::userOrders(Auth::id());
    require VIEWS_PATH.'/front/invoices.php';

  elseif($tab === 'settings'): ?>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:22px">
    <h3 style="margin-bottom:16px">⚙️ <?= $isAr?'إعدادات الحساب':'Account Settings' ?></h3>
    <form method="POST">
      <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
      <input type="hidden" name="action" value="update_profile">
      <div class="fg" style="margin-bottom:12px"><label><?= $isAr?'اللغة المفضلة':'Preferred Language' ?></label>
        <select name="locale">
          <option value="ar" <?= ($user['locale']??'ar')==='ar'?'selected':'' ?>>🇸🇦 العربية</option>
          <option value="en" <?= ($user['locale']??'ar')==='en'?'selected':'' ?>>🇬🇧 English</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">💾 <?= $isAr?'حفظ':'Save' ?></button>
    </form>
    <hr style="border:none;border-top:1px solid var(--border);margin:20px 0">
    <h3 style="margin-bottom:12px;color:var(--danger)">⚠️ <?= $isAr?'منطقة الخطر':'Danger Zone' ?></h3>
    <a href="?page=logout" style="display:inline-flex;align-items:center;gap:6px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:var(--danger);padding:10px 18px;border-radius:9px;font-weight:700;font-size:13px">🚪 <?= $isAr?'تسجيل الخروج':'Sign Out' ?></a>
  </div>
  <?php endif; ?>
</div>
</div></div>