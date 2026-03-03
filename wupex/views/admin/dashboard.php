<?php
$isAr = Lang::isRtl();
$sym  = htmlspecialchars(Setting::get('currency_symbol','ر.س'));

// إحصائيات
$stats = [
  'total_orders'   => Database::count('orders'),
  'total_revenue'  => Database::fetch("SELECT COALESCE(SUM(total),0) as v FROM ".DB_PREFIX."orders WHERE status='completed'")['v'] ?? 0,
  'total_users'    => Database::count('users',"role='user'"),
  'total_products' => Database::count('products',"status=1"),
  'pending_orders' => Database::count('orders',"status='pending'"),
  'open_tickets'   => Database::count('tickets',"status='open'"),
  'wallet_balance' => Database::fetch("SELECT COALESCE(SUM(wallet_balance),0) as v FROM ".DB_PREFIX."users")['v'] ?? 0,
  'today_visitors' => Visitor::todayCount(),
];

// أحدث الطلبات
$latestOrders = Database::fetchAll("SELECT o.*, u.name as uname FROM ".DB_PREFIX."orders o LEFT JOIN ".DB_PREFIX."users u ON u.id=o.user_id ORDER BY o.created_at DESC LIMIT 8");

// أحدث المستخدمين
$latestUsers = Database::fetchAll("SELECT * FROM ".DB_PREFIX."users WHERE role='user' ORDER BY created_at DESC LIMIT 6");

// أكثر المنتجات مبيعاً
$topProducts = Database::fetchAll("SELECT * FROM ".DB_PREFIX."products ORDER BY sales_count DESC LIMIT 5");
?>

<!-- Stats Row 1 -->
<div class="stats-row">
  <div class="stat-c blue">
    <div class="stat-body"><div class="stat-num"><?= number_format($stats['total_orders']) ?></div>
    <div class="stat-lbl"><?= $isAr?'إجمالي الطلبات':'Total Orders' ?></div></div>
    <div class="stat-ic">📦</div>
  </div>
  <div class="stat-c orange">
    <div class="stat-body"><div class="stat-num"><?= $sym ?><?= number_format($stats['total_revenue'],2) ?></div>
    <div class="stat-lbl"><?= $isAr?'إجمالي الإيرادات':'Total Revenue' ?></div></div>
    <div class="stat-ic">💰</div>
  </div>
  <div class="stat-c green">
    <div class="stat-body"><div class="stat-num"><?= number_format($stats['total_users']) ?></div>
    <div class="stat-lbl"><?= $isAr?'المستخدمون':'Users' ?></div></div>
    <div class="stat-ic">👥</div>
  </div>
  <div class="stat-c pink" style="--after-bg:var(--accent)">
    <div class="stat-body"><div class="stat-num"><?= number_format($stats['total_products']) ?></div>
    <div class="stat-lbl"><?= $isAr?'المنتجات النشطة':'Active Products' ?></div></div>
    <div class="stat-ic">🎮</div>
  </div>
</div>
<!-- Stats Row 2 -->
<div class="stats-row" style="margin-top:-8px">
  <div class="stat-c">
    <div class="stat-body"><div class="stat-num" style="color:var(--warning)"><?= $stats['pending_orders'] ?></div>
    <div class="stat-lbl"><?= $isAr?'طلبات معلقة':'Pending Orders' ?></div></div>
    <div class="stat-ic">⏳</div>
  </div>
  <div class="stat-c">
    <div class="stat-body"><div class="stat-num" style="color:var(--danger)"><?= $stats['open_tickets'] ?></div>
    <div class="stat-lbl"><?= $isAr?'تذاكر مفتوحة':'Open Tickets' ?></div></div>
    <div class="stat-ic">🎫</div>
  </div>
  <div class="stat-c">
    <div class="stat-body"><div class="stat-num" style="color:var(--success)"><?= $sym ?><?= number_format($stats['wallet_balance'],2) ?></div>
    <div class="stat-lbl"><?= $isAr?'أرصدة المحافظ':'Wallet Balances' ?></div></div>
    <div class="stat-ic">💳</div>
  </div>
  <div class="stat-c">
    <div class="stat-body"><div class="stat-num"><?= $stats['today_visitors'] ?></div>
    <div class="stat-lbl"><?= $isAr?'زوار اليوم':'Today Visitors' ?></div></div>
    <div class="stat-ic">👁️</div>
  </div>
</div>

<!-- Quick Actions -->
<div class="frm-card" style="margin-bottom:16px">
  <h3>⚡ <?= $isAr?'إجراءات سريعة':'Quick Actions' ?></h3>
  <div class="qa-grid">
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=products&action=create" class="qa-item"><div class="qa-ico">➕</div><div class="qa-lbl"><?= $isAr?'منتج جديد':'New Product' ?></div></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=categories" class="qa-item"><div class="qa-ico">📂</div><div class="qa-lbl"><?= $isAr?'تصنيف جديد':'New Category' ?></div></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=coupons" class="qa-item"><div class="qa-ico">🏷️</div><div class="qa-lbl"><?= $isAr?'كوبون جديد':'New Coupon' ?></div></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=banners" class="qa-item"><div class="qa-ico">🖼️</div><div class="qa-lbl"><?= $isAr?'بانر جديد':'New Banner' ?></div></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=codes" class="qa-item"><div class="qa-ico">🔑</div><div class="qa-lbl"><?= $isAr?'رفع أكواد':'Upload Codes' ?></div></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=notifications&action=send" class="qa-item"><div class="qa-ico">📢</div><div class="qa-lbl"><?= $isAr?'إشعار جماعي':'Broadcast' ?></div></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=orders" class="qa-item"><div class="qa-ico">📦</div><div class="qa-lbl"><?= $isAr?'الطلبات':'Orders' ?></div></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=users" class="qa-item"><div class="qa-ico">👥</div><div class="qa-lbl"><?= $isAr?'المستخدمون':'Users' ?></div></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=tickets" class="qa-item"><div class="qa-ico">🎫</div><div class="qa-lbl"><?= $isAr?'الدعم':'Support' ?></div></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=reports" class="qa-item"><div class="qa-ico">📈</div><div class="qa-lbl"><?= $isAr?'التقارير':'Reports' ?></div></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=backup" class="qa-item"><div class="qa-ico">💾</div><div class="qa-lbl"><?= $isAr?'نسخة احتياطية':'Backup' ?></div></a>
    <a href="<?= Helpers::siteUrl('admin/') ?>?p=settings&tab=appearance" class="qa-item"><div class="qa-ico">🎨</div><div class="qa-lbl"><?= $isAr?'المظهر':'Appearance' ?></div></a>
  </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:16px">
  <!-- Latest Orders -->
  <div class="tbl-wrap">
    <div class="tbl-hdr">
      <div class="tbl-title">📦 <?= $isAr?'أحدث الطلبات':'Latest Orders' ?></div>
      <a href="<?= Helpers::siteUrl('admin/') ?>?p=orders" class="btn btn-sm btn-secondary"><?= $isAr?'عرض الكل':'View All' ?></a>
    </div>
    <table>
      <thead><tr>
        <th>#</th>
        <th><?= $isAr?'العميل':'Customer' ?></th>
        <th><?= $isAr?'الإجمالي':'Total' ?></th>
        <th><?= $isAr?'الحالة':'Status' ?></th>
        <th><?= $isAr?'التاريخ':'Date' ?></th>
        <th></th>
      </tr></thead>
      <tbody>
      <?php foreach($latestOrders as $o): ?>
      <tr>
        <td style="font-size:11px;color:var(--muted)"><?= htmlspecialchars($o['order_number']) ?></td>
        <td><?= htmlspecialchars($o['uname']??$o['guest_email']??'-') ?></td>
        <td style="font-weight:700;color:var(--success)"><?= $sym ?><?= number_format($o['total'],2) ?></td>
        <td><span class="bpill bp-<?= $o['status'] ?>"><?= $o['status'] ?></span></td>
        <td style="font-size:11px;color:var(--muted)"><?= date('m-d H:i',strtotime($o['created_at'])) ?></td>
        <td><a href="<?= Helpers::siteUrl('admin/') ?>?p=orders&action=view&id=<?= $o['id'] ?>" class="btn btn-sm btn-secondary">👁️</a></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Right Column -->
  <div style="display:flex;flex-direction:column;gap:16px">
    <!-- Top Products -->
    <div class="frm-card" style="margin-bottom:0">
      <h3>🏆 <?= $isAr?'أكثر مبيعاً':'Top Selling' ?></h3>
      <?php foreach($topProducts as $p): ?>
      <div style="display:flex;align-items:center;gap:9px;padding:7px 0;border-bottom:1px solid var(--border)">
        <span style="font-size:20px"><?= $p['icon'] ?></span>
        <div style="flex:1;min-width:0">
          <div style="font-size:12px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars($isAr?$p['name_ar']:$p['name_en']) ?></div>
          <div style="font-size:10px;color:var(--muted)"><?= $p['sales_count'] ?> <?= $isAr?'مبيعة':'sales' ?></div>
        </div>
        <span style="font-size:12px;font-weight:700;color:var(--success)"><?= $sym ?><?= number_format($p['price'],2) ?></span>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Latest Users -->
    <div class="frm-card" style="margin-bottom:0">
      <h3>👥 <?= $isAr?'أحدث المستخدمين':'New Users' ?></h3>
      <?php foreach($latestUsers as $u): ?>
      <div style="display:flex;align-items:center;gap:8px;padding:6px 0;border-bottom:1px solid var(--border)">
        <div style="width:30px;height:30px;background:linear-gradient(135deg,var(--primary),var(--accent));border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#fff;flex-shrink:0"><?= mb_substr($u['name'],0,1) ?></div>
        <div style="flex:1;min-width:0">
          <div style="font-size:12px;font-weight:700"><?= htmlspecialchars($u['name']) ?></div>
          <div style="font-size:10px;color:var(--muted)"><?= date('m-d',strtotime($u['created_at'])) ?></div>
        </div>
        <span class="bpill bp-<?= $u['status'] ?>"><?= $u['status'] ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
