<?php
define('WUPEX_INSTALL', true);
session_start();
// تحويل الأرقام العربية إلى أرقام غربية
ob_start(function($b) {
    return str_replace(['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'],
                       ['0','1','2','3','4','5','6','7','8','9'], $b);
});


$isInstalled = file_exists(__DIR__ . '/.installed');

// ── إعادة تثبيت ──
if ($isInstalled) {
    // طلب تأكيد إعادة التثبيت
    if (isset($_POST['confirm_reinstall']) && $_POST['confirm_reinstall'] === 'yes') {
        // حذف ملف القفل
        @unlink(__DIR__ . '/.installed');
        // مسح الجلسة وبدء من جديد
        session_destroy();
        session_start();
        header('Location: index.php'); exit;
    }
    // عرض صفحة التأكيد
    $lang2 = $_GET['lang'] ?? 'ar';
    $isAr2 = $lang2 === 'ar';
    ?>
<!DOCTYPE html>
<html lang="<?= $isAr2 ? 'ar-u-nu-latn' : 'en' ?>" dir="<?= $isAr2?'rtl':'ltr' ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= $isAr2?'إعادة التثبيت':'Reinstall' ?></title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&family=Exo+2:wght@900&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Tajawal',sans-serif;background:#09071a;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background-image:radial-gradient(ellipse at 50% 0%,rgba(124,58,237,.18) 0%,transparent 60%)}
.box{width:100%;max-width:480px}
.logo{text-align:center;margin-bottom:24px}
.logo h1{font-family:'Exo 2',sans-serif;font-size:30px;font-weight:900;background:linear-gradient(135deg,#7c3aed,#ec4899);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.card{background:#14102a;border:1px solid rgba(124,58,237,.22);border-radius:18px;padding:28px}
.warn{background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.3);border-radius:12px;padding:14px 16px;margin-bottom:18px}
.warn h3{color:#f59e0b;font-size:14px;margin-bottom:6px}
.warn p{color:#92768a;font-size:13px;line-height:1.8}
.info{background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.2);border-radius:10px;padding:10px 14px;margin-bottom:16px;font-size:12px;color:#10b981}
.btn{width:100%;padding:13px;border-radius:11px;font-size:14px;font-weight:700;cursor:pointer;font-family:'Tajawal',sans-serif;border:none;transition:all .2s}
.btn-danger{background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff;margin-bottom:10px}
.btn-danger:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(220,38,38,.3)}
.btn-sec{background:#1e1a38;color:#7a6fa0;border:1px solid rgba(124,58,237,.2)}
.btn-sec:hover{border-color:#7c3aed;color:#f0eaff}
.check-wrap{display:flex;align-items:flex-start;gap:10px;margin-bottom:18px;cursor:pointer}
.check-wrap input{width:18px;height:18px;accent-color:#ef4444;flex-shrink:0;margin-top:2px;cursor:pointer}
.check-wrap span{font-size:13px;color:#7a6fa0;line-height:1.6}
.installed-info{display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid rgba(255,255,255,.05);font-size:12px;color:#7a6fa0}
.installed-info span:last-child{color:#f0eaff;font-weight:700}
</style>
</head>
<body>
<div class="box">
  <div class="logo">
    <h1>🔄 Wupex</h1>
  </div>
  <div class="card">
    <div class="warn">
      <h3>⚠️ <?= $isAr2?'المتجر مثبت مسبقاً':'Store Already Installed' ?></h3>
      <p><?= $isAr2?'يوجد تثبيت سابق لهذا المتجر. إعادة التثبيت ستقوم بإعادة إنشاء قاعدة البيانات وستحذف جميع البيانات الحالية.':'A previous installation exists. Reinstalling will recreate the database and delete all current data.' ?></p>
    </div>
    <?php
    $installedDate = trim(@file_get_contents(__DIR__ . '/.installed'));
    if ($installedDate): ?>
    <div style="background:rgba(255,255,255,.03);border-radius:10px;padding:12px 14px;margin-bottom:16px">
      <div class="installed-info"><span><?= $isAr2?'تاريخ التثبيت':'Installed on' ?></span><span><?= htmlspecialchars(substr($installedDate,0,19)) ?></span></div>
      <div class="installed-info" style="border:none"><span><?= $isAr2?'الرابط':'URL' ?></span><span><?= htmlspecialchars(substr($installedDate,20,60)) ?></span></div>
    </div>
    <?php endif; ?>
    <form method="POST" id="reinstallForm">
      <label class="check-wrap" id="confirmCheck">
        <input type="checkbox" id="chk" onchange="document.getElementById('reinstallBtn').disabled=!this.checked">
        <span><?= $isAr2?'نعم، أفهم أن هذا سيحذف جميع البيانات وأريد إعادة التثبيت من البداية.':'Yes, I understand this will delete all data and I want to reinstall from scratch.' ?></span>
      </label>
      <input type="hidden" name="confirm_reinstall" value="yes">
      <button type="submit" id="reinstallBtn" class="btn btn-danger" disabled>
        🗑️ <?= $isAr2?'إعادة التثبيت من البداية':'Reinstall from Scratch' ?>
      </button>
    </form>
    <a href="../" class="btn btn-sec" style="display:block;text-align:center;text-decoration:none;padding:13px">
      ← <?= $isAr2?'العودة للمتجر':'Back to Store' ?>
    </a>
  </div>
  <div style="text-align:center;margin-top:16px;font-size:11px;color:#3d3560">
    <a href="?lang=<?= $isAr2?'en':'ar' ?>" style="color:#7c3aed;text-decoration:none">🌐 <?= $isAr2?'English':'عربي' ?></a>
  </div>
</div>
</body>
</html>
    <?php
    exit;
}

// ── إعادة تعيين كاملة إذا طُلب ذلك (يعمل دائماً) ──
if (isset($_GET['reset'])) {
    @unlink(__DIR__ . '/.installed');
    session_unset();
    session_destroy();
    session_start();
    header('Location: index.php'); exit;
}

// ── إذا كانت الجلسة على الخطوة الأخيرة ولا يوجد ملف قفل → بدء من جديد ──
if (!file_exists(__DIR__ . '/.installed') && (int)($_SESSION['install_step'] ?? 1) === 7) {
    session_unset();
}

$step = (int)($_SESSION['install_step'] ?? 1);
$lang = $_SESSION['install_lang'] ?? 'ar';
$isAr = $lang === 'ar';

// معالجة POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'set_lang') {
        $_SESSION['install_lang'] = in_array($_POST['lang'],['ar','en']) ? $_POST['lang'] : 'ar';
        $_SESSION['install_step'] = 2;
        header('Location: index.php'); exit;
    }

    if ($action === 'step2_next') {
        $_SESSION['install_step'] = 3;
        header('Location: index.php'); exit;
    }

    if ($action === 'test_db') {
        $h = $_POST['db_host']??'localhost';
        $n = $_POST['db_name']??'';
        $u = $_POST['db_user']??'';
        $p = $_POST['db_pass']??'';
        try {
            new PDO("mysql:host=$h;dbname=$n;charset=utf8mb4", $u, $p, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
            $_SESSION['db'] = compact('h','n','u','p');
            echo json_encode(['ok'=>true,'msg'=>$isAr?'الاتصال ناجح! ✅':'Connection successful! ✅']);
        } catch(PDOException $e) {
            echo json_encode(['ok'=>false,'msg'=>$e->getMessage()]);
        }
        exit;
    }

    if ($action === 'save_db') {
        $_SESSION['db'] = ['h'=>$_POST['db_host'],'n'=>$_POST['db_name'],'u'=>$_POST['db_user'],'p'=>$_POST['db_pass']];
        $_SESSION['install_step'] = 4;
        header('Location: index.php'); exit;
    }

    if ($action === 'save_store') {
        $_SESSION['store'] = [
            'name_ar'    => $_POST['store_name_ar']??'ووبيكس',
            'name_en'    => $_POST['store_name_en']??'Wupex',
            'url'        => rtrim($_POST['site_url']??'', '/'),
            'maintenance'=> isset($_POST['maintenance'])?'1':'0',
        ];
        $_SESSION['install_step'] = 5;
        header('Location: index.php'); exit;
    }

    if ($action === 'save_admin') {
        $err = [];
        if (empty($_POST['admin_name'])) $err[] = $isAr?'الاسم مطلوب':'Name required';
        if (empty($_POST['admin_email']) || !filter_var($_POST['admin_email'],FILTER_VALIDATE_EMAIL)) $err[] = $isAr?'إيميل غير صالح':'Invalid email';
        if (strlen($_POST['admin_pass']??'')<8) $err[] = $isAr?'كلمة المرور قصيرة (8 أحرف)':'Password too short (8 chars)';
        if ($_POST['admin_pass'] !== $_POST['admin_pass2']) $err[] = $isAr?'كلمتا المرور غير متطابقتين':'Passwords do not match';

        if (empty($err)) {
            $_SESSION['admin'] = [
                'name'       => $_POST['admin_name'],
                'email'      => $_POST['admin_email'],
                'pass'       => $_POST['admin_pass'],
                'admin_path' => preg_replace('/[^a-z0-9_-]/', '', strtolower($_POST['admin_path']??'admin')) ?: 'admin',
            ];
            $_SESSION['install_step'] = 6;
            header('Location: index.php'); exit;
        }
        $_SESSION['install_errors'] = $err;
        header('Location: index.php'); exit;
    }

    if ($action === 'save_features') {
        $_SESSION['features'] = $_POST;
        // تثبيت مباشر - بدون خطوة JS وسيطة
        $result = doInstall($_SESSION, $isAr);
        $_SESSION['install_result']     = $result;
        $_SESSION['install_admin_email'] = $_SESSION['admin']['email'] ?? '';
        $_SESSION['install_step']        = 7; // صفحة النتيجة
        header('Location: index.php'); exit;
    }
}

// دالة التثبيت الفعلي - مستقلة كلياً بدون الاعتماد على classes التطبيق
function doInstall(array $sess, bool $isAr): array {
    @set_time_limit(120); // وقت كافٍ لإنشاء الجداول
    @ini_set('display_errors', '0');
    $log    = [];
    $db_cfg = $sess['db']    ?? [];
    $store  = $sess['store'] ?? [];
    $admin  = $sess['admin'] ?? [];
    $feats  = $sess['features'] ?? [];

    // ── 1. اتصال PDO ──
    try {
        $pdo = new PDO(
            "mysql:host={$db_cfg['h']};dbname={$db_cfg['n']};charset=utf8mb4",
            $db_cfg['u'], $db_cfg['p'],
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ]
        );
    } catch (PDOException $e) {
        return ['success'=>false, 'error'=>'DB Error: '.$e->getMessage()];
    }

    // ── 2. كتابة config.php ──
    $appKey    = bin2hex(random_bytes(32));
    $adminPath = preg_replace('/[^a-z0-9_-]/', '', strtolower($admin['admin_path'] ?? 'admin')) ?: 'admin';
    $siteUrl   = rtrim($store['url'] ?? '', '/');
    $dbH = addslashes($db_cfg['h']); $dbN = addslashes($db_cfg['n']);
    $dbU = addslashes($db_cfg['u']); $dbP = addslashes($db_cfg['p']);

    $cfg = "<?php\n"
         . "define('WUPEX',       true);\n"
         . "define('DB_HOST',     '$dbH');\n"
         . "define('DB_NAME',     '$dbN');\n"
         . "define('DB_USER',     '$dbU');\n"
         . "define('DB_PASS',     '$dbP');\n"
         . "define('DB_CHARSET',  'utf8mb4');\n"
         . "define('DB_PREFIX',   'wx_');\n"
         . "define('SITE_URL',    '$siteUrl');\n"
         . "define('SITE_PATH',   __DIR__);\n"
         . "define('ADMIN_PATH',  '$adminPath');\n"
         . "define('VIEWS_PATH',   SITE_PATH . '/views');\n"
         . "define('UPLOADS_PATH', SITE_PATH . '/uploads');\n"
         . "define('STORAGE_PATH', SITE_PATH . '/storage');\n"
         . "define('LANG_PATH',    SITE_PATH . '/lang');\n"
         . "define('IMAGES_PATH',  SITE_PATH . '/images');\n"
         . "define('SESSION_NAME',    'wupex_session');\n"
         . "define('SESSION_LIFETIME', 86400 * 7);\n"
         . "define('CSRF_TOKEN_NAME', 'wupex_csrf');\n"
         . "define('APP_KEY',    '$appKey');\n"
         . "define('HASH_COST',   12);\n"
         . "define('APP_DEBUG',   false);\n"
         . "define('APP_VERSION', '1.0.0');\n"
         . "define('MAX_UPLOAD',  5 * 1024 * 1024);\n"
         . "define('ALLOWED_IMG', ['jpg','jpeg','png','gif','webp','svg']);\n"
         . "define('CACHE_ENABLED', true);\n"
         . "define('CACHE_TTL',     3600);\n";

    if (file_put_contents(__DIR__.'/../config.php', $cfg) === false) {
        return ['success'=>false, 'error'=>'Cannot write config.php — check folder permissions (chmod 755)'];
    }
    $log[] = '✅ config.php';

    // ── 3. إنشاء الجداول مباشرةً بـ PDO (بدون require classes) ──
    $px = 'wx_';
    $tables = [
        "CREATE TABLE IF NOT EXISTS `{$px}settings` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `key` VARCHAR(100) NOT NULL UNIQUE,
            `value` TEXT,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `username` VARCHAR(50) NULL UNIQUE,
            `email` VARCHAR(150) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `phone` VARCHAR(30) NULL,
            `role` ENUM('user','moderator','admin') DEFAULT 'user',
            `status` ENUM('active','banned','pending') DEFAULT 'active',
            `wallet_balance` DECIMAL(12,2) DEFAULT 0.00,
            `points` INT DEFAULT 0,
            `referral_code` VARCHAR(20) NULL UNIQUE,
            `referred_by` INT NULL,
            `locale` VARCHAR(5) DEFAULT 'ar',
            `email_verified` TINYINT DEFAULT 0,
            `email_token` VARCHAR(60) NULL,
            `last_login` DATETIME NULL,
            `last_ip` VARCHAR(45) NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}categories` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name_ar` VARCHAR(100) NOT NULL,
            `name_en` VARCHAR(100) NOT NULL,
            `slug` VARCHAR(120) NOT NULL UNIQUE,
            `icon` VARCHAR(10) DEFAULT '📦',
            `color1` VARCHAR(20) DEFAULT '#1a1a2e',
            `color2` VARCHAR(20) DEFAULT '#7c3aed',
            `description_ar` TEXT NULL,
            `description_en` TEXT NULL,
            `featured` TINYINT DEFAULT 0,
            `status` TINYINT DEFAULT 1,
            `sort_order` INT DEFAULT 99,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}products` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `category_id` INT NOT NULL,
            `name_ar` VARCHAR(200) NOT NULL,
            `name_en` VARCHAR(200) NOT NULL,
            `slug` VARCHAR(220) NOT NULL UNIQUE,
            `icon` VARCHAR(10) DEFAULT '🎮',
            `image` VARCHAR(255) NULL,
            `color1` VARCHAR(20) DEFAULT '#1a1a2e',
            `color2` VARCHAR(20) DEFAULT '#16213e',
            `price` DECIMAL(12,2) DEFAULT 0.00,
            `price_max` DECIMAL(12,2) DEFAULT 0.00,
            `delivery_type` ENUM('instant','manual') DEFAULT 'instant',
            `badge` VARCHAR(10) DEFAULT '',
            `countries` VARCHAR(255) DEFAULT '',
            `description_ar` TEXT NULL,
            `description_en` TEXT NULL,
            `featured` TINYINT DEFAULT 0,
            `stock` TINYINT DEFAULT 1,
            `status` TINYINT DEFAULT 1,
            `sort_order` INT DEFAULT 99,
            `views_count` INT DEFAULT 0,
            `sales_count` INT DEFAULT 0,
            `rating` DECIMAL(3,2) DEFAULT 0.00,
            `ratings_count` INT DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}product_prices` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `product_id` INT NOT NULL,
            `label_ar` VARCHAR(100) DEFAULT '',
            `label_en` VARCHAR(100) DEFAULT '',
            `price` DECIMAL(12,2) NOT NULL,
            `sort_order` INT DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}codes` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `product_id` INT NOT NULL,
            `price_id` INT NULL,
            `code` TEXT NOT NULL,
            `status` ENUM('available','sold','reserved') DEFAULT 'available',
            `order_id` INT NULL,
            `sold_at` DATETIME NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}orders` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `order_number` VARCHAR(30) NOT NULL UNIQUE,
            `user_id` INT NULL,
            `guest_email` VARCHAR(150) NULL,
            `subtotal` DECIMAL(12,2) DEFAULT 0.00,
            `discount` DECIMAL(12,2) DEFAULT 0.00,
            `tax` DECIMAL(12,2) DEFAULT 0.00,
            `shipping` DECIMAL(12,2) DEFAULT 0.00,
            `total` DECIMAL(12,2) NOT NULL,
            `coupon_code` VARCHAR(50) NULL,
            `payment_method` VARCHAR(50) DEFAULT 'wallet',
            `status` ENUM('pending','processing','completed','cancelled','refunded') DEFAULT 'pending',
            `notes` TEXT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}order_items` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `order_id` INT NOT NULL,
            `product_id` INT NOT NULL,
            `price_id` INT NULL,
            `product_name` VARCHAR(200) NOT NULL,
            `price_label` VARCHAR(100) DEFAULT '',
            `price` DECIMAL(12,2) NOT NULL,
            `quantity` INT DEFAULT 1,
            `codes` TEXT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}carts` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NULL,
            `session_id` VARCHAR(100) NULL,
            `product_id` INT NOT NULL,
            `price_id` INT NULL,
            `price` DECIMAL(12,2) NOT NULL,
            `quantity` INT DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}wallet_transactions` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `type` ENUM('credit','debit') NOT NULL,
            `amount` DECIMAL(12,2) NOT NULL,
            `balance_after` DECIMAL(12,2) NOT NULL,
            `source` VARCHAR(50) DEFAULT 'manual',
            `description` VARCHAR(255) NULL,
            `ref` VARCHAR(100) NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}deposit_requests` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `ref_number` VARCHAR(30) NULL,
            `amount` DECIMAL(12,2) NOT NULL,
            `bonus` DECIMAL(12,2) DEFAULT 0.00,
            `payment_method` VARCHAR(50) DEFAULT 'bank',
            `attachment` VARCHAR(255) NULL,
            `status` ENUM('pending','approved','rejected') DEFAULT 'pending',
            `notes` TEXT NULL,
            `processed_at` DATETIME NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}coupons` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `code` VARCHAR(50) NOT NULL UNIQUE,
            `type` ENUM('percent','fixed','free_shipping') DEFAULT 'percent',
            `value` DECIMAL(12,2) NOT NULL,
            `min_order` DECIMAL(12,2) DEFAULT 0.00,
            `max_discount` DECIMAL(12,2) DEFAULT 0.00,
            `max_uses` INT DEFAULT 0,
            `used_count` INT DEFAULT 0,
            `starts_at` DATETIME NULL,
            `expires_at` DATETIME NULL,
            `status` TINYINT DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}reviews` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `product_id` INT NOT NULL,
            `user_id` INT NOT NULL,
            `order_id` INT NULL,
            `rating` TINYINT NOT NULL DEFAULT 5,
            `comment` TEXT NULL,
            `status` ENUM('pending','approved','rejected') DEFAULT 'pending',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}tickets` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `ticket_number` VARCHAR(20) NOT NULL UNIQUE,
            `user_id` INT NOT NULL,
            `subject` VARCHAR(200) NOT NULL,
            `status` ENUM('open','in_progress','waiting','resolved','closed') DEFAULT 'open',
            `priority` ENUM('low','medium','high','urgent') DEFAULT 'medium',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}ticket_replies` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `ticket_id` INT NOT NULL,
            `user_id` INT NOT NULL,
            `is_admin` TINYINT DEFAULT 0,
            `message` TEXT NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}notifications` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NULL,
            `is_broadcast` TINYINT DEFAULT 0,
            `title_ar` VARCHAR(200) NOT NULL,
            `title_en` VARCHAR(200) NOT NULL,
            `message_ar` TEXT NOT NULL,
            `message_en` TEXT NOT NULL,
            `icon` VARCHAR(10) DEFAULT '🔔',
            `color` VARCHAR(20) DEFAULT '#7c3aed',
            `link` VARCHAR(255) NULL,
            `is_read` TINYINT DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}ip_devices` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `ip_address` VARCHAR(45) NOT NULL UNIQUE,
            `device_name` VARCHAR(100) NOT NULL,
            `notes` TEXT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}wishlists` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `product_id` INT NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY `unique_wish` (`user_id`,`product_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}banners` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title_ar` VARCHAR(200) NOT NULL,
            `title_en` VARCHAR(200) DEFAULT '',
            `image` VARCHAR(255) NULL,
            `link_url` VARCHAR(255) NULL,
            `position` VARCHAR(30) DEFAULT 'hero',
            `start_date` DATETIME NULL,
            `end_date` DATETIME NULL,
            `status` TINYINT DEFAULT 1,
            `sort_order` INT DEFAULT 99,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}pages` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `slug` VARCHAR(100) NOT NULL UNIQUE,
            `title_ar` VARCHAR(200) NOT NULL,
            `title_en` VARCHAR(200) DEFAULT '',
            `content_ar` LONGTEXT NULL,
            `content_en` LONGTEXT NULL,
            `status` TINYINT DEFAULT 1,
            `sort_order` INT DEFAULT 99,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}faqs` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `question_ar` TEXT NOT NULL,
            `question_en` TEXT DEFAULT '',
            `answer_ar` TEXT NOT NULL,
            `answer_en` TEXT DEFAULT '',
            `status` TINYINT DEFAULT 1,
            `sort_order` INT DEFAULT 99,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}activity_logs` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NULL,
            `action` VARCHAR(100) NOT NULL,
            `model` VARCHAR(50) NULL,
            `model_id` INT NULL,
            `description` TEXT NULL,
            `ip_address` VARCHAR(45) NULL,
            `user_agent` VARCHAR(255) NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}login_attempts` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `email` VARCHAR(150) NULL,
            `ip_address` VARCHAR(45) NOT NULL,
            `success` TINYINT DEFAULT 0,
            `attempted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}blocked_ips` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `ip_address` VARCHAR(45) NOT NULL UNIQUE,
            `reason` VARCHAR(255) NULL,
            `permanent` TINYINT DEFAULT 0,
            `blocked_until` DATETIME NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}visitors` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NULL,
            `ip_address` VARCHAR(45) NOT NULL,
            `page_url` VARCHAR(255) NULL,
            `referer` VARCHAR(255) NULL,
            `user_agent` VARCHAR(255) NULL,
            `is_home` TINYINT DEFAULT 0,
            `visited_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}visitor_days` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `ip_address` VARCHAR(45) NOT NULL,
            `visit_date` DATE NOT NULL,
            `user_agent` VARCHAR(500),
            `user_id` INT NULL,
            `device_name` VARCHAR(100) NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY `unique_ip_day` (`ip_address`,`visit_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}seo_metas` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `page_slug` VARCHAR(100) NOT NULL UNIQUE,
            `meta_title_ar` VARCHAR(200) NULL,
            `meta_title_en` VARCHAR(200) NULL,
            `meta_desc_ar` TEXT NULL,
            `meta_desc_en` TEXT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}points_transactions` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `type` VARCHAR(50) NOT NULL,
            `amount` INT NOT NULL,
            `ref` VARCHAR(100) NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS `{$px}referrals` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `referrer_id` INT NOT NULL,
            `referred_id` INT NOT NULL,
            `commission` DECIMAL(12,2) DEFAULT 0.00,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ];

    // ── حذف الجداول القديمة أولاً لضمان تثبيت نظيف ──
    $dropOrder = [
        'referrals','points_transactions','seo_metas','visitors','blocked_ips',
        'login_attempts','activity_logs','faqs','pages','banners','wishlists',
        'notifications','ticket_replies','tickets','reviews','coupons',
        'deposit_requests','wallet_transactions','carts','order_items','orders',
        'codes','product_prices','products','categories','users','settings',
    ];
    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
        foreach ($dropOrder as $tbl) {
            $pdo->exec("DROP TABLE IF EXISTS `{$px}{$tbl}`");
        }
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    } catch (PDOException $e) { /* تجاهل أخطاء الحذف */ }

    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
        foreach ($tables as $sql) {
            $pdo->exec($sql);
        }
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    } catch (PDOException $e) {
        return ['success'=>false, 'error'=>'Table creation failed: '.$e->getMessage()];
    }
    $log[] = '✅ '.($isAr ? 'الجداول أُنشئت ('  .count($tables).' جدول)' : count($tables).' tables created');

    // ── 4. إنشاء حساب المدير ──
    try {
        $hashedPass = password_hash($admin['pass'], PASSWORD_BCRYPT, ['cost'=>12]);
        // حذف أي مدير بنفس الإيميل أولاً لتجنب تعارض UNIQUE
        $pdo->prepare("DELETE FROM `{$px}users` WHERE email=?")->execute([$admin['email']]);
        $pdo->prepare("INSERT INTO `{$px}users` (name,email,password,role,status,email_verified,created_at)
                       VALUES (?,?,?,'admin','active',1,NOW())")
            ->execute([$admin['name'], $admin['email'], $hashedPass]);
    } catch (PDOException $e) {
        return ['success'=>false, 'error'=>'Admin account error: '.$e->getMessage()];
    }
    $log[] = '✅ '.($isAr?'حساب المدير':'Admin account created');

    // ── 5. حفظ الإعدادات ──
    try {
        $settings = [
            'store_name_ar'      => $store['name_ar'] ?? 'ووبيكس',
            'store_name_en'      => $store['name_en'] ?? 'Wupex',
            'maintenance_mode'   => $store['maintenance'] ?? '0',
            'wallet_enabled'     => isset($feats['wallet'])   ? '1' : '0',
            'points_enabled'     => isset($feats['points'])   ? '1' : '0',
            'referral_enabled'   => isset($feats['referral']) ? '1' : '0',
            'reviews_enabled'    => isset($feats['reviews'])  ? '1' : '0',
            'registration_enabled' => '1',
            'show_prices'        => '1',
            'show_flags'         => '1',
            'currency'           => 'SAR',
            'currency_symbol'    => 'ر.س',
            'default_lang'       => 'ar',
            'products_per_row'   => '6',
            'payment_wallet'     => '1',
        ];
        $stmt = $pdo->prepare("INSERT INTO `{$px}settings` (`key`,`value`) VALUES (?,?)
                               ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)");
        foreach ($settings as $k => $v) {
            $stmt->execute([$k, $v]);
        }
    } catch (PDOException $e) {
        return ['success'=>false, 'error'=>'Settings error: '.$e->getMessage()];
    }
    $log[] = '✅ '.($isAr?'الإعدادات الافتراضية':'Default settings saved');

    // ── 6. بيانات أولية كاملة ──
    try {
        // التصنيفات
        $pdo->exec("INSERT IGNORE INTO `{$px}categories`
            (name_ar,name_en,slug,icon,color1,color2,featured,status,sort_order) VALUES
            ('بطاقات شحن الألعاب','Game Top-up Cards','game-topup','🎮','#0f0c29','#302b63',1,1,1),
            ('اشتراكات الفيديو','Streaming Services','streaming','🎬','#000000','#e50914',1,1,2),
            ('متاجر التطبيقات','App Stores','app-stores','📱','#0d0d0d','#2563eb',1,1,3),
            ('شبكات التواصل الاجتماعي','Social Media','social','💬','#075e54','#128c7e',1,1,4),
            ('برامج وأدوات','Software & Tools','software','💻','#1a1a2e','#16213e',0,1,5),
            ('اشتراكات الموسيقى','Music Streaming','music','🎵','#1DB954','#191414',0,1,6),
            ('العاب وترفيه','Games & Entertainment','games','🕹️','#7c3aed','#4c1d95',0,1,7)
        ");

        // المنتجات
        $pdo->exec("INSERT IGNORE INTO `{$px}products`
            (category_id,name_ar,name_en,slug,icon,color1,color2,price,price_max,delivery_type,badge,featured,stock,status,sort_order) VALUES
            (1,'شدات ببجي موبايل','PUBG Mobile UC','pubg-mobile-uc','🔫','#1a0a2e','#4a1464',5.00,100.00,'instant','🔥',1,1,1,1),
            (1,'فري فاير ذهب','Free Fire Diamonds','free-fire-diamonds','🔥','#1a0a00','#8b4513',3.00,80.00,'instant','⚡',1,1,1,2),
            (1,'فورت نايت V-Bucks','Fortnite V-Bucks','fortnite-vbucks','🏗️','#1a3a5c','#0066cc',8.00,120.00,'instant','',1,1,1,3),
            (1,'كود موبايل','Call of Duty Mobile CP','codm-cp','🎖️','#0d1117','#1f6f3c',6.00,90.00,'instant','جديد',0,1,1,4),
            (1,'ليغ أوف ليجيندز','League of Legends RP','lol-rp','⚔️','#0a1628','#c89b3c',5.00,100.00,'instant','',0,1,1,5),
            (2,'نتفليكس اشتراك','Netflix Subscription','netflix','🎬','#141414','#e50914',25.00,75.00,'manual','',1,1,1,6),
            (2,'شاهد VIP','Shahid VIP','shahid-vip','📺','#0a0a0a','#e8b84b',15.00,60.00,'manual','',1,1,1,7),
            (2,'ديزني بلس','Disney Plus','disney-plus','✨','#0f1923','#1137be',18.00,50.00,'manual','شعبي',0,1,1,8),
            (2,'سبوتيفاي بريميوم','Spotify Premium','spotify','🎵','#000000','#1db954',12.00,45.00,'manual','',0,1,1,9),
            (3,'ايتونز بطاقة','iTunes Gift Card','itunes','🍎','#1c1c1e','#2c2c2e',15.00,200.00,'instant','',1,1,1,10),
            (3,'جوجل بلاي','Google Play Card','google-play','🎯','#0d1117','#34a853',10.00,150.00,'instant','🔥',1,1,1,11),
            (3,'بلايستيشن','PlayStation Store','psn','🎮','#003087','#003791',20.00,200.00,'instant','',1,1,1,12),
            (3,'إكس بوكس','Xbox Gift Card','xbox','🎮','#107c10','#0e6b0e',20.00,200.00,'instant','',0,1,1,13),
            (4,'واتساب بيزنس','WhatsApp Business API','whatsapp-business','💬','#075e54','#128c7e',30.00,30.00,'manual','',0,1,1,14),
            (4,'تويتر بلو','Twitter Blue','twitter-blue','🐦','#1a1a2e','#1da1f2',8.00,8.00,'manual','',0,1,1,15),
            (5,'ويندوز 11 أصلي','Windows 11 Pro Key','windows-11','💻','#001748','#0078d4',35.00,35.00,'instant','',1,1,1,16),
            (5,'أوفيس 365','Office 365','office-365','📄','#d83b01','#ff5c00',25.00,80.00,'manual','جديد',0,1,1,17),
            (6,'سبوتيفاي شهر','Spotify 1 Month','spotify-1m','🎵','#000000','#1db954',12.00,12.00,'manual','',0,1,1,18),
            (6,'أبل ميوزك','Apple Music','apple-music','🎵','#fc3c44','#fc3c44',13.00,40.00,'manual','',0,1,1,19),
            (7,'رولبوكس','Robux Roblox','robux','🤖','#e60012','#ff0000',5.00,100.00,'instant','شعبي',1,1,1,20)
        ");

        // أسعار متعددة لمنتج PUBG
        $pubg = $pdo->query("SELECT id FROM `{$px}products` WHERE slug='pubg-mobile-uc' LIMIT 1")->fetch();
        if ($pubg) {
            $pid = $pubg['id'];
            $pdo->exec("INSERT IGNORE INTO `{$px}product_prices` (product_id,label_ar,label_en,price,sort_order) VALUES
                ($pid,'60 شدة','60 UC',5.00,1),
                ($pid,'325 شدة','325 UC',20.00,2),
                ($pid,'660 شدة','660 UC',38.00,3),
                ($pid,'1800 شدة','1800 UC',95.00,4),
                ($pid,'3850 شدة','3850 UC',185.00,5)
            ");
        }

        // أسعار متعددة لمنتج Free Fire
        $ff = $pdo->query("SELECT id FROM `{$px}products` WHERE slug='free-fire-diamonds' LIMIT 1")->fetch();
        if ($ff) {
            $pid = $ff['id'];
            $pdo->exec("INSERT IGNORE INTO `{$px}product_prices` (product_id,label_ar,label_en,price,sort_order) VALUES
                ($pid,'100 ذهب','100 Diamonds',3.00,1),
                ($pid,'310 ذهب','310 Diamonds',8.00,2),
                ($pid,'520 ذهب','520 Diamonds',13.00,3),
                ($pid,'1060 ذهب','1060 Diamonds',25.00,4),
                ($pid,'2180 ذهب','2180 Diamonds',48.00,5)
            ");
        }

        // أسعار نتفليكس
        $nflx = $pdo->query("SELECT id FROM `{$px}products` WHERE slug='netflix' LIMIT 1")->fetch();
        if ($nflx) {
            $pid = $nflx['id'];
            $pdo->exec("INSERT IGNORE INTO `{$px}product_prices` (product_id,label_ar,label_en,price,sort_order) VALUES
                ($pid,'شهر 1','1 Month',25.00,1),
                ($pid,'3 أشهر','3 Months',65.00,2),
                ($pid,'6 أشهر','6 Months',120.00,3),
                ($pid,'سنة كاملة','1 Year',220.00,4)
            ");
        }

        // أكواد تجريبية لـ iTunes
        $itunes = $pdo->query("SELECT id FROM `{$px}products` WHERE slug='itunes' LIMIT 1")->fetch();
        if ($itunes) {
            $pid = $itunes['id'];
            $pdo->exec("INSERT IGNORE INTO `{$px}codes` (product_id,code,status) VALUES
                ($pid,'ITNS-DEMO-1111-AAAA','available'),
                ($pid,'ITNS-DEMO-2222-BBBB','available'),
                ($pid,'ITNS-DEMO-3333-CCCC','available'),
                ($pid,'ITNS-DEMO-4444-DDDD','available'),
                ($pid,'ITNS-DEMO-5555-EEEE','available')
            ");
        }

        // أكواد تجريبية لـ Google Play
        $gplay = $pdo->query("SELECT id FROM `{$px}products` WHERE slug='google-play' LIMIT 1")->fetch();
        if ($gplay) {
            $pid = $gplay['id'];
            $pdo->exec("INSERT IGNORE INTO `{$px}codes` (product_id,code,status) VALUES
                ($pid,'GPLY-DEMO-XXXX-1111','available'),
                ($pid,'GPLY-DEMO-XXXX-2222','available'),
                ($pid,'GPLY-DEMO-XXXX-3333','available')
            ");
        }

        // كوبونات تجريبية
        $pdo->exec("INSERT IGNORE INTO `{$px}coupons` (code,type,value,min_order,max_uses,status) VALUES
            ('WELCOME10','percent',10.00,0.00,100,1),
            ('SAVE20','fixed',20.00,50.00,50,1),
            ('VIP50','percent',50.00,100.00,10,1)
        ");

        // أسئلة شائعة
        $pdo->exec("INSERT IGNORE INTO `{$px}faqs` (question_ar,question_en,answer_ar,answer_en,sort_order,status) VALUES
            ('كيف أشتري من المتجر؟','How do I purchase?','أضف المنتج للسلة ثم أكمل الدفع من رصيد المحفظة.','Add to cart and checkout using your wallet balance.',1,1),
            ('كيف أشحن محفظتي؟','How do I top up my wallet?','اذهب لصفحة الإيداع وأرسل الطلب مع إيصال الدفع.','Visit the deposit page and submit with payment proof.',2,1),
            ('هل التسليم فوري؟','Is delivery instant?','نعم، معظم منتجات الأكواد تُسلَّم فورياً بعد إتمام الدفع.','Yes, code products are delivered instantly after payment.',3,1),
            ('ماهي طرق الدفع المتاحة؟','What payment methods are available?','المحفظة الإلكترونية والتحويل البنكي.','E-Wallet and bank transfer.',4,1),
            ('كيف أتواصل مع الدعم؟','How do I contact support?','افتح تذكرة دعم من قسم الدعم الفني في حسابك.','Open a support ticket from your account dashboard.',5,1)
        ");

        // بانر ترحيبي
        $pdo->exec("INSERT IGNORE INTO `{$px}banners` (title_ar,title_en,position,status,sort_order) VALUES
            ('مرحباً بك في ووبيكس! تسوق الآن واحصل على أفضل الأسعار','Welcome to Wupex! Shop now for the best prices','hero',1,1)
        ");

    } catch (PDOException $e) {
        // البيانات التجريبية اختيارية - لا تُفشل التثبيت
        $log[] = '⚠️ Seed warning: '.$e->getMessage();
    }
    $log[] = '✅ '.($isAr?'البيانات التجريبية (7 تصنيفات، 20 منتجاً)':'Demo data (7 categories, 20 products)');

    // ── 7. إنشاء المجلدات المطلوبة ──
    $basePath = dirname(__DIR__);
    $dirs = ['uploads/products','uploads/categories','uploads/banners','uploads/users',
             'storage/logs','storage/cache','storage/sessions','storage/backups','images/gift-cards'];
    foreach ($dirs as $d) {
        $p = $basePath.'/'.$d;
        if (!is_dir($p)) @mkdir($p, 0755, true);
    }
    if (!file_exists($basePath.'/storage/.htaccess')) {
        @file_put_contents($basePath.'/storage/.htaccess', "Deny from all\n");
    }
    $log[] = '✅ '.($isAr?'المجلدات':'Directories created');

    // ── 8. ملف القفل ──
    $lockWritten = @file_put_contents(__DIR__.'/.installed', date('Y-m-d H:i:s')."\n".$siteUrl);
    // Also write to storage/
    @file_put_contents(dirname(__DIR__).'/storage/installed.lock', date('Y-m-d H:i:s')."\n".$siteUrl);
    $log[] = '✅ '.($isAr?'ملف القفل (install/.installed)':'Lock file created').($lockWritten===false?' ⚠️ (تحذير: فشلت الكتابة - اضغط رابط المتجر)':'');

    return [
        'success'    => true,
        'log'        => $log,
        'admin_path' => $adminPath,
        'site_url'   => $siteUrl,
        'admin_email'=> $admin['email'],
    ];
}

$errors = $_SESSION['install_errors'] ?? [];
unset($_SESSION['install_errors']);
?>
<!DOCTYPE html>
<html lang="<?= $lang==='ar' ? 'ar-u-nu-latn' : 'en' ?>" dir="<?= $isAr?'rtl':'ltr' ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $isAr?'تثبيت ووبيكس':'Install Wupex' ?></title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&family=Exo+2:wght@700;900&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--p:#7c3aed;--a:#ec4899;--s:#f97316;--bg:#0a071c;--card:#16122e;--card2:#1e1a38;--text:#f0eaff;--muted:#8b7fa0;--border:rgba(124,58,237,.22);--glow:rgba(124,58,237,.25)}
body{font-family:'Tajawal',Tahoma,sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background-image:radial-gradient(ellipse at 50% 0%,rgba(124,58,237,.12) 0%,transparent 60%)}
.box{width:100%;max-width:640px}
.hdr{text-align:center;margin-bottom:28px}
.hdr h1{font-family:'Exo 2',sans-serif;font-size:32px;font-weight:900;background:linear-gradient(135deg,var(--p),var(--a));-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:5px}
.hdr p{font-size:13px;color:var(--muted)}
.steps{display:flex;gap:4px;margin-bottom:24px;justify-content:center;flex-wrap:wrap}
.step{width:32px;height:32px;border-radius:50%;background:var(--card2);border:2px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:900;color:var(--muted);transition:all .3s;position:relative}
.step.done{background:var(--p);border-color:var(--p);color:#fff}
.step.active{background:linear-gradient(135deg,var(--p),var(--a));border-color:var(--a);color:#fff;box-shadow:0 0 16px var(--glow)}
.step-line{width:20px;height:2px;background:var(--border);align-self:center;margin-top:-2px}
.step-line.done{background:var(--p)}
.card{background:var(--card);border:1px solid var(--border);border-radius:18px;padding:28px}
.card h2{font-size:20px;font-weight:900;margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:9px}
.fg{display:flex;flex-direction:column;gap:5px;margin-bottom:14px}
.fg label{font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.8px}
.fg input,.fg select{background:var(--bg);border:1px solid var(--border);border-radius:9px;padding:11px 14px;color:var(--text);font-size:14px;outline:none;transition:all .2s;font-family:'Tajawal',sans-serif;width:100%}
.fg input:focus,.fg select:focus{border-color:var(--p);box-shadow:0 0 0 3px var(--glow)}
.fg input::placeholder{color:var(--muted)}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.chk{display:flex;align-items:center;gap:9px;padding:10px 14px;background:var(--card2);border:1px solid var(--border);border-radius:10px;cursor:pointer;margin-bottom:8px}
.chk input{width:18px;height:18px;accent-color:var(--p);flex-shrink:0;cursor:pointer}
.chk span{font-size:14px;font-weight:600}
.btn{width:100%;background:linear-gradient(135deg,var(--p),var(--a));color:#fff;border:none;padding:14px;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;margin-top:8px;font-family:'Tajawal',sans-serif;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px}
.btn:hover{transform:translateY(-2px);box-shadow:0 6px 24px var(--glow)}
.btn-sec{background:var(--card2);border:1px solid var(--border);color:var(--text)}
.btn-sec:hover{border-color:var(--p)}
.lang-cards{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.lang-card{background:var(--card2);border:2px solid var(--border);border-radius:16px;padding:24px;text-align:center;cursor:pointer;transition:all .25s}
.lang-card:hover{border-color:var(--p);transform:translateY(-3px)}
.lang-card h3{font-size:22px;font-weight:900;margin:12px 0 6px}
.lang-card p{font-size:12px;color:var(--muted)}
.req-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:16px}
.req-item{padding:10px 14px;border-radius:10px;font-size:13px;font-weight:700;display:flex;align-items:center;gap:8px}
.req-ok{background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:#10b981}
.req-fail{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#ef4444}
.err-box{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);border-radius:10px;padding:12px 16px;color:#ef4444;font-size:13px;margin-bottom:14px}
.err-box li{margin:3px 0 3px 16px}
.log-item{padding:7px 12px;background:var(--card2);border-radius:8px;font-size:13px;margin-bottom:6px;display:flex;align-items:center;gap:8px}
.success-badge{font-size:48px;margin-bottom:12px}
.success-info{background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.25);border-radius:12px;padding:16px;margin-bottom:16px}
.info-row{display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.06);font-size:13px}
.info-row:last-child{border-bottom:none}
.info-lbl{color:var(--muted);font-weight:600}
.info-val{font-weight:700;font-family:monospace;color:var(--p)}
@media(max-width:500px){.grid2,.lang-cards,.req-grid{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="box">
  <div class="hdr">
    <h1>🛒 Wupex</h1>
    <p><?= $isAr?'معالج التثبيت - الخطوة':'Installation Wizard - Step' ?> <?= min($step, 8) ?>/7</p>
  </div>

  <!-- Steps Indicator -->
  <div class="steps">
    <?php $stepLabels=$isAr?['اللغة','ترحيب','قاعدة البيانات','المتجر','المدير','المميزات','إتمام']:['Language','Welcome','Database','Store','Admin','Features','Finish'];
    for($i=1;$i<=7;$i++): ?>
    <div class="step <?= $i<$step?'done':($i==$step?'active':'') ?>" title="<?= $stepLabels[$i-1]??'' ?>"><?= $i<$step?'✓':$i ?></div>
    <?php if($i<7): ?><div class="step-line <?= $i<$step?'done':'' ?>"></div><?php endif; ?>
    <?php endfor; ?>
  </div>

  <!-- STEP 1: Language -->
  <?php if($step === 1): ?>
  <div class="card">
    <h2>🌐 <?= 'Select Language / اختر اللغة' ?></h2>
    <div class="lang-cards">
      <form method="POST">
        <input type="hidden" name="action" value="set_lang">
        <input type="hidden" name="lang" value="ar">
        <button type="submit" style="border:none;width:100%;background:none;cursor:pointer">
          <div class="lang-card" style="border-color:var(--p)">
            <div style="font-size:36px">🇸🇦</div>
            <h3>عربي</h3><p>العربية - RTL</p>
          </div>
        </button>
      </form>
      <form method="POST">
        <input type="hidden" name="action" value="set_lang">
        <input type="hidden" name="lang" value="en">
        <button type="submit" style="border:none;width:100%;background:none;cursor:pointer">
          <div class="lang-card">
            <div style="font-size:36px">🇺🇸</div>
            <h3>English</h3><p>English - LTR</p>
          </div>
        </button>
      </form>
    </div>
  </div>

  <!-- STEP 2: Welcome -->
  <?php elseif($step === 2): ?>
  <div class="card">
    <h2>👋 <?= $isAr?'مرحباً بك في ووبيكس!':'Welcome to Wupex!' ?></h2>
    <p style="color:var(--muted);margin-bottom:18px;line-height:1.7"><?= $isAr?'سيساعدك هذا المعالج على تثبيت متجرك الرقمي الكامل خلال دقائق.':'This wizard will help you install your complete digital store in minutes.' ?></p>
    <h3 style="font-size:14px;margin-bottom:12px;color:var(--muted)"><?= $isAr?'متطلبات النظام:':'System Requirements:' ?></h3>
    <div class="req-grid">
      <?php
      $reqs = [
        ['PHP >= 8.0', version_compare(PHP_VERSION,'8.0','>=')],
        ['PDO + MySQL',extension_loaded('pdo_mysql')],
        ['mbstring', extension_loaded('mbstring')],
        ['json', extension_loaded('json')],
        ['uploads/ writable', is_writable(dirname(__DIR__).'/uploads') || @mkdir(dirname(__DIR__).'/uploads',0755,true)],
        ['storage/ writable', is_writable(dirname(__DIR__).'/storage') || @mkdir(dirname(__DIR__).'/storage',0755,true)],
        ['config writable', is_writable(dirname(__DIR__).'/config.php') || !file_exists(dirname(__DIR__).'/config.php')],
        ['cURL', function_exists('curl_init')],
      ];
      foreach($reqs as [$label,$ok]): ?>
      <div class="req-item <?= $ok?'req-ok':'req-fail' ?>"><?= $ok?'✅':'❌' ?> <?= $label ?></div>
      <?php endforeach; ?>
    </div>
    <form method="POST">
      <input type="hidden" name="action" value="step2_next">
      <button type="submit" class="btn">➡️ <?= $isAr?'متابعة':'Continue' ?></button>
    </form>
  </div>

  <!-- STEP 3: Database -->
  <?php elseif($step === 3): ?>
  <div class="card">
    <h2>🗄️ <?= $isAr?'إعداد قاعدة البيانات':'Database Setup' ?></h2>
    <p style="color:var(--muted);font-size:13px;margin-bottom:18px"><?= $isAr?'أدخل بيانات قاعدة بيانات MySQL.':'Enter your MySQL database credentials.' ?></p>
    <form method="POST">
      <input type="hidden" name="action" value="save_db">
      <div class="grid2">
        <div class="fg"><label><?= $isAr?'المضيف':'DB Host' ?></label><input type="text" name="db_host" value="<?= htmlspecialchars($_SESSION['db']['h']??'localhost') ?>" required></div>
        <div class="fg"><label><?= $isAr?'اسم القاعدة':'DB Name' ?></label><input type="text" name="db_name" value="<?= htmlspecialchars($_SESSION['db']['n']??'') ?>" required></div>
        <div class="fg"><label><?= $isAr?'اسم المستخدم':'DB Username' ?></label><input type="text" name="db_user" value="<?= htmlspecialchars($_SESSION['db']['u']??'') ?>" required></div>
        <div class="fg"><label><?= $isAr?'كلمة المرور':'DB Password' ?></label><input type="password" name="db_pass" value="<?= htmlspecialchars($_SESSION['db']['p']??'') ?>"></div>
      </div>
      <div id="testMsg" style="margin-bottom:10px;font-size:13px;font-weight:700"></div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
        <button type="button" class="btn btn-sec" onclick="testDB()">🔌 <?= $isAr?'اختبار الاتصال':'Test Connection' ?></button>
        <button type="submit" class="btn">✅ <?= $isAr?'حفظ والمتابعة':'Save & Continue' ?></button>
      </div>
    </form>
  </div>
  <script>
  async function testDB() {
    const f = document.querySelector('form');
    const data = new FormData(f);
    data.set('action','test_db');
    const btn = document.querySelector('.btn-sec');
    btn.disabled=true; btn.textContent='...';
    const r = await fetch('index.php',{method:'POST',body:data});
    const d = await r.json();
    const el = document.getElementById('testMsg');
    el.style.color = d.ok ? '#10b981' : '#ef4444';
    el.textContent = d.msg;
    btn.disabled=false; btn.textContent='🔌 <?= $isAr?'اختبار الاتصال':'Test Connection' ?>';
  }
  </script>

  <!-- STEP 4: Store Settings -->
  <?php elseif($step === 4): ?>
  <div class="card">
    <h2>🏪 <?= $isAr?'إعدادات المتجر':'Store Settings' ?></h2>
    <form method="POST">
      <input type="hidden" name="action" value="save_store">
      <div class="grid2">
        <div class="fg"><label><?= $isAr?'اسم المتجر عربي':'Store Name AR' ?></label><input type="text" name="store_name_ar" value="<?= htmlspecialchars($_SESSION['store']['name_ar']??'ووبيكس') ?>" required></div>
        <div class="fg"><label>Store Name EN</label><input type="text" name="store_name_en" value="<?= htmlspecialchars($_SESSION['store']['name_en']??'Wupex') ?>" required></div>
      </div>
      <div class="fg"><label><?= $isAr?'رابط الموقع (URL)':'Site URL' ?></label><input type="url" name="site_url" value="<?= htmlspecialchars($_SESSION['store']['url']??'http://localhost/wupex') ?>" required placeholder="https://example.com/wupex"></div>
      <label class="chk" style="margin-top:6px;margin-bottom:0">
        <input type="checkbox" name="maintenance" value="1" <?= !empty($_SESSION['store']['maintenance'])?'checked':'' ?>>
        <span>🔧 <?= $isAr?'تفعيل وضع الصيانة عند التثبيت':'Enable Maintenance Mode after install' ?></span>
      </label>
      <p style="font-size:11px;color:var(--muted);margin:7px 0 16px"><?= $isAr?'المدير فقط يرى المتجر مع شريط تحذيري.':'Only admin can see the store with a warning bar.' ?></p>
      <button type="submit" class="btn">➡️ <?= $isAr?'متابعة':'Continue' ?></button>
    </form>
  </div>

  <!-- STEP 5: Admin Credentials -->
  <?php elseif($step === 5): ?>
  <div class="card">
    <h2>👤 <?= $isAr?'بيانات المدير':'Admin Credentials' ?></h2>
    <?php if($errors): ?><div class="err-box"><ul><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="POST">
      <input type="hidden" name="action" value="save_admin">
      <div class="grid2">
        <div class="fg"><label><?= $isAr?'الاسم الكامل':'Full Name' ?></label><input type="text" name="admin_name" value="<?= htmlspecialchars($_SESSION['admin']['name']??'Admin') ?>" required></div>
        <div class="fg"><label>Email</label><input type="email" name="admin_email" value="<?= htmlspecialchars($_SESSION['admin']['email']??'') ?>" required></div>
        <div class="fg"><label><?= $isAr?'كلمة المرور (8+ أحرف)':'Password (8+ chars)' ?></label><input type="password" name="admin_pass" required minlength="8" placeholder="••••••••"></div>
        <div class="fg"><label><?= $isAr?'تأكيد كلمة المرور':'Confirm Password' ?></label><input type="password" name="admin_pass2" required placeholder="••••••••"></div>
      </div>
      <div class="fg"><label><?= $isAr?'مسار لوحة التحكم (URL Path)':'Admin Path (URL)' ?></label>
        <input type="text" name="admin_path" value="<?= htmlspecialchars($_SESSION['admin']['admin_path']??'admin') ?>" placeholder="admin" pattern="[a-z0-9_-]+" required>
        <small style="color:var(--muted);font-size:11px"><?= $isAr?'مثال: admin → yoursite.com/wupex/admin':'e.g. admin → yoursite.com/wupex/admin' ?></small>
      </div>
      <button type="submit" class="btn">➡️ <?= $isAr?'متابعة':'Continue' ?></button>
    </form>
  </div>

  <!-- STEP 6: Features -->
  <?php elseif($step === 6): ?>
  <div class="card">
    <h2>✨ <?= $isAr?'تفعيل المميزات':'Enable Features' ?></h2>
    <form method="POST">
      <input type="hidden" name="action" value="save_features">
      <?php $featsOpts=[
        ['wallet',$isAr?'المحفظة الإلكترونية':'E-Wallet','💰',true],
        ['points',$isAr?'نقاط المكافآت':'Reward Points','💎',true],
        ['referral',$isAr?'نظام الإحالات':'Referral System','🔗',true],
        ['reviews',$isAr?'التقييمات والمراجعات':'Reviews & Ratings','⭐',true],
        ['wishlist',$isAr?'قائمة الأمنيات':'Wishlist','❤️',true],
        ['tickets',$isAr?'الدعم الفني':'Support Tickets','🎫',true],
        ['coupons',$isAr?'الكوبونات والخصومات':'Coupons & Discounts','🏷️',true],
        ['maintenance',$isAr?'وضع الصيانة (تفعيل الآن)':'Maintenance Mode (enable now)','🔧',false],
      ];
      foreach($featsOpts as [$k,$l,$ic,$def]): ?>
      <label class="chk">
        <input type="checkbox" name="<?= $k ?>" value="1" <?= $def?'checked':'' ?>>
        <span><?= $ic ?> <?= $l ?></span>
      </label>
      <?php endforeach; ?>
      <button type="submit" class="btn" style="margin-top:16px">🚀 <?= $isAr?'تثبيت المتجر الآن!':'Install Store Now!' ?></button>
    </form>
  </div>

  <!-- STEP 7: Result (success or failure) -->
  <?php elseif($step >= 7):
    $result   = $_SESSION['install_result'] ?? [];
    $success  = $result['success'] ?? false;
    $adminPath = $result['admin_path'] ?? 'admin';
    $siteUrl   = $result['site_url']  ?? '';
    $adminEmail = $_SESSION['install_admin_email'] ?? ($result['admin_email'] ?? '');
    // مسح بيانات الجلسة بعد قراءتها
    unset($_SESSION['db'], $_SESSION['store'], $_SESSION['admin'], $_SESSION['features'], $_SESSION['install_result']);
  ?>
  <div class="card" style="text-align:center">
    <?php if($success): ?>
    <div class="success-badge">🎉</div>
    <h2 style="justify-content:center;color:#10b981;margin-bottom:16px"><?= $isAr?'تم التثبيت بنجاح!':'Installation Successful!' ?></h2>
    <div class="success-info" style="text-align:<?= $isAr?'right':'left' ?>">
      <div class="info-row">
        <span class="info-lbl"><?= $isAr?'رابط المتجر':'Store URL' ?></span>
        <span class="info-val"><?= htmlspecialchars($siteUrl) ?>/</span>
      </div>
      <div class="info-row">
        <span class="info-lbl"><?= $isAr?'لوحة التحكم':'Admin Panel' ?></span>
        <span class="info-val"><?= htmlspecialchars($siteUrl) ?>/admin.php</span>
      </div>
      <div class="info-row">
        <span class="info-lbl"><?= $isAr?'الإيميل':'Email' ?></span>
        <span class="info-val"><?= htmlspecialchars($adminEmail) ?></span>
      </div>
    </div>
    <div style="background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.3);color:#f59e0b;border-radius:10px;padding:12px 16px;font-size:13px;margin-bottom:18px">
      ⚠️ <?= $isAr?'احذف مجلد install/ من الاستضافة الآن لأسباب أمنية.':'Delete the install/ folder from your server now for security.' ?>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <a href="<?= htmlspecialchars($siteUrl) ?>/" target="_blank" style="text-decoration:none">
        <button class="btn btn-sec" style="width:100%">🏪 <?= $isAr?'فتح المتجر':'Open Store' ?></button>
      </a>
      <a href="<?= htmlspecialchars($siteUrl) ?>/admin.php" target="_blank" style="text-decoration:none">
        <button class="btn" style="width:100%">⚙️ <?= $isAr?'لوحة التحكم':'Admin Panel' ?></button>
      </a>
    </div>
    <?php else: ?>
    <div style="font-size:48px;margin-bottom:12px">❌</div>
    <h2 style="justify-content:center;color:#ef4444;margin-bottom:14px"><?= $isAr?'فشل التثبيت':'Installation Failed' ?></h2>
    <div class="err-box" style="text-align:right"><?= nl2br(htmlspecialchars($result['error'] ?? 'Unknown error')) ?></div>
    <?php if(!empty($result['log'])): ?>
    <div style="margin:14px 0;text-align:right">
      <?php foreach($result['log'] as $l): ?>
      <div class="log-item"><?= htmlspecialchars($l) ?></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:14px">
      <button onclick="$_SESSION['install_step']=6;location='index.php'" class="btn btn-sec" style="width:100%">↩️ <?= $isAr?'العودة':'Go Back' ?></button>
      <a href="index.php?reset=1" style="text-decoration:none"><button class="btn btn-sec" style="width:100%;background:rgba(239,68,68,.15);border-color:#ef4444;color:#ef4444">🔄 <?= $isAr?'إعادة المحاولة':'Retry' ?></button></a>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
