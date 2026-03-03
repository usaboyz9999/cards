<?php
require_once dirname(__DIR__) . '/core/App.php';
// ── تحويل للمثبت إذا لم يكن المتجر مثبتاً ──
if (!file_exists(dirname(__DIR__) . '/storage/installed.lock') && !file_exists(dirname(__DIR__) . '/install/.installed')) {
    header('Location: ../install/');
    exit;
}


require_once dirname(__DIR__) . '/core/AdminHandlers.php';

// ── صفحة تسجيل الدخول للوحة التحكم ──
// قراءة الصفحة المطلوبة هنا فقط لفحص ما إذا كانت صفحة login
$_pg_check = Helpers::getStr('p', 'dashboard');

if ($_pg_check === 'login') {
    // إذا كان مسجلاً بالفعل كمدير، انتقل للداشبورد
    if (Auth::check() && Auth::isAdmin()) {
        $redir = Session::get('admin_redirect') ?: Helpers::siteUrl('admin/');
        Session::del('admin_redirect');
        header('Location: '.$redir); exit;
    }

    $isAr = Lang::isRtl();
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!Session::verifyCsrf($_POST['csrf'] ?? '')) {
            $error = 'خطأ في الجلسة، أعد المحاولة.';
        } else {
            $result = Auth::login($_POST['email'] ?? '', $_POST['password'] ?? '');
            if ($result['success']) {
                if (Auth::isAdmin()) {
                    $redir = Session::get('admin_redirect') ?: Helpers::siteUrl('admin/');
                    Session::del('admin_redirect');
                    header('Location: '.$redir); exit;
                } else {
                    Auth::logout();
                    $error = $isAr ? 'ليس لديك صلاحية الوصول للوحة التحكم.' : 'You do not have admin access.';
                }
            } else {
                $error = $result['msg'];
            }
        }
    }
    ?>
<!DOCTYPE html>
<html lang="<?= $isAr ? 'ar-u-nu-latn' : 'en' ?>" dir="<?= $isAr?'rtl':'ltr' ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $isAr?'دخول لوحة التحكم':'Admin Login' ?></title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&family=Exo+2:wght@900&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Tajawal',Tahoma,sans-serif;background:#09071a;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background-image:radial-gradient(ellipse at 50% 0%,rgba(124,58,237,.18) 0%,transparent 60%)}
.box{width:100%;max-width:420px}
.logo{text-align:center;margin-bottom:28px}
.logo h1{font-family:'Exo 2',sans-serif;font-size:34px;font-weight:900;background:linear-gradient(135deg,#7c3aed,#ec4899);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.logo p{color:#7a6fa0;font-size:13px;margin-top:5px}
.card{background:#14102a;border:1px solid rgba(124,58,237,.22);border-radius:18px;padding:30px}
.fg{display:flex;flex-direction:column;gap:5px;margin-bottom:16px}
.fg label{font-size:11px;font-weight:700;color:#7a6fa0;text-transform:uppercase;letter-spacing:.8px}
.fg input{background:#09071a;border:1px solid rgba(124,58,237,.22);border-radius:10px;padding:12px 14px;color:#f0eaff;font-size:14px;outline:none;transition:all .2s;font-family:'Tajawal',sans-serif;width:100%}
.fg input:focus{border-color:#7c3aed;box-shadow:0 0 0 3px rgba(124,58,237,.2)}
.btn{width:100%;background:linear-gradient(135deg,#7c3aed,#ec4899);color:#fff;border:none;padding:13px;border-radius:11px;font-size:15px;font-weight:700;cursor:pointer;font-family:'Tajawal',sans-serif;margin-top:6px;transition:all .2s}
.btn:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(124,58,237,.35)}
.err{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.4);border-radius:10px;padding:12px 14px;color:#ef4444;font-size:13px;margin-bottom:16px;font-weight:600}
.back{text-align:center;margin-top:16px;font-size:12px;color:#7a6fa0}
.back a{color:#7c3aed;font-weight:700}
</style>
</head>
<body>
<div class="box">
  <div class="logo">
    <h1>⚙️ Wupex</h1>
    <p><?= $isAr?'لوحة تحكم المدير':'Admin Control Panel' ?></p>
  </div>
  <div class="card">
    <?php if($error): ?><div class="err">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST">
      <input type="hidden" name="csrf" value="<?= Session::csrf() ?>">
      <input type="hidden" name="p" value="login">
      <div class="fg">
        <label><?= $isAr?'البريد الإلكتروني':'Email Address' ?></label>
        <input type="email" name="email" required autofocus placeholder="admin@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="fg">
        <label><?= $isAr?'كلمة المرور':'Password' ?></label>
        <input type="password" name="password" required placeholder="••••••••">
      </div>
      <button type="submit" class="btn">🔐 <?= $isAr?'دخول':'Login' ?></button>
    </form>
    <div class="back">
      <a href="<?= SITE_URL ?>">🏠 <?= $isAr?'العودة للمتجر':'Back to Store' ?></a>
    </div>
  </div>
</div>
</body>
</html>
    <?php
    exit;
}

// ── باقي لوحة التحكم تتطلب صلاحية المدير ──
Auth::requireAdmin();

$pg  = Helpers::getStr('p', 'dashboard');
$tab = Helpers::getStr('tab');
$action = Helpers::getStr('action');
$isAr = Lang::isRtl();
$lang = Lang::current();
$sym  = htmlspecialchars(Setting::get('currency_symbol','ر.س'));

// POST Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Session::verifyCsrf($_POST['csrf'] ?? '')) {
        Session::flash('error','CSRF error'); Helpers::redirect($_SERVER['HTTP_REFERER']??Helpers::siteUrl('admin/'));
    }
    $pa = $_POST['action'] ?? '';

    // ── Products ──
    if ($pa === 'add_product') {
        $slug = Helpers::slug(($_POST['name_en']??$_POST['name_ar']??'product').'-'.time());
        $img  = !empty($_FILES['image']['tmp_name']) ? Helpers::uploadImage($_FILES['image'],'products') : null;
        $pid  = Product::create(['name_ar'=>$_POST['name_ar'],'name_en'=>$_POST['name_en'],'slug'=>$slug,'category_id'=>(int)$_POST['category_id'],'icon'=>$_POST['icon']??'🎮','image'=>$img,'color1'=>$_POST['color1']??'#1a1a2e','color2'=>$_POST['color2']??'#16213e','price'=>(float)$_POST['price'],'price_max'=>(float)($_POST['price_max']??0),'delivery_type'=>$_POST['delivery_type']??'instant','badge'=>$_POST['badge']??'','countries'=>$_POST['countries']??'','description_ar'=>$_POST['description_ar']??'','description_en'=>$_POST['description_en']??'','featured'=>isset($_POST['featured'])?1:0,'stock'=>isset($_POST['stock'])?1:0,'status'=>isset($_POST['status'])?1:0,'sort_order'=>(int)($_POST['sort_order']??99)]);
        // أسعار
        if(!empty($_POST['pr_price'])) { foreach($_POST['pr_price'] as $i=>$pr) { if($pr!='') Database::insert('product_prices',['product_id'=>$pid,'label_ar'=>$_POST['pr_ar'][$i]??'','label_en'=>$_POST['pr_en'][$i]??'','price'=>(float)$pr,'sort_order'=>$i]); } }
        ActivityLog::log('product_created','product',$pid);
        Cache::flush();
        Session::flash('success', $isAr?'تم إضافة المنتج':'Product added');
    }
    if ($pa === 'edit_product') {
        $img = null;
        if (!empty($_FILES['image']['tmp_name'])) $img = Helpers::uploadImage($_FILES['image'],'products');
        $upd = ['name_ar'=>$_POST['name_ar'],'name_en'=>$_POST['name_en'],'category_id'=>(int)$_POST['category_id'],'icon'=>$_POST['icon']??'🎮','color1'=>$_POST['color1']??'#1a1a2e','color2'=>$_POST['color2']??'#16213e','price'=>(float)$_POST['price'],'price_max'=>(float)($_POST['price_max']??0),'delivery_type'=>$_POST['delivery_type']??'instant','badge'=>$_POST['badge']??'','countries'=>$_POST['countries']??'','description_ar'=>$_POST['description_ar']??'','description_en'=>$_POST['description_en']??'','featured'=>isset($_POST['featured'])?1:0,'stock'=>isset($_POST['stock'])?1:0,'status'=>isset($_POST['status'])?1:0,'sort_order'=>(int)($_POST['sort_order']??99)];
        if($img) $upd['image']=$img;
        Product::update((int)$_POST['id'], $upd);
        Cache::flush();
        Session::flash('success', $isAr?'تم التحديث':'Updated');
    }
    if ($pa === 'delete_product') { Product::delete((int)$_POST['id']); Cache::flush(); Session::flash('success',$isAr?'تم الحذف':'Deleted'); }

    // ── Categories ──
    if ($pa === 'add_category') {
        $slug = Helpers::slug($_POST['name_en']??$_POST['name_ar']??'cat');
        Category::create(['name_ar'=>$_POST['name_ar'],'name_en'=>$_POST['name_en'],'slug'=>$slug.'-'.time(),'icon'=>$_POST['icon']??'📦','color1'=>$_POST['color1']??'#1a1a2e','color2'=>$_POST['color2']??'#7c3aed','description_ar'=>$_POST['description_ar']??'','description_en'=>$_POST['description_en']??'','featured'=>isset($_POST['featured'])?1:0,'status'=>isset($_POST['status'])?1:0,'sort_order'=>(int)($_POST['sort_order']??99)]);
        Session::flash('success', $isAr?'تم إضافة التصنيف':'Category added');
    }
    if ($pa === 'edit_category') { Category::update((int)$_POST['id'],['name_ar'=>$_POST['name_ar'],'name_en'=>$_POST['name_en'],'icon'=>$_POST['icon']??'📦','color1'=>$_POST['color1']??'#1a1a2e','color2'=>$_POST['color2']??'#7c3aed','status'=>isset($_POST['status'])?1:0,'sort_order'=>(int)($_POST['sort_order']??99)]); Session::flash('success',$isAr?'تم التحديث':'Updated'); }
    if ($pa === 'delete_category') { Category::delete((int)$_POST['id']); Session::flash('success',$isAr?'تم الحذف':'Deleted'); }

    // ── Orders ──
    if ($pa === 'update_order_status') { Order::updateStatus((int)$_POST['id'], $_POST['status']??'pending'); Session::flash('success',$isAr?'تم تحديث الحالة':'Status updated'); }

    // ── Users ──
    if ($pa === 'toggle_user_status') {
        $u=Database::fetch("SELECT status FROM ".DB_PREFIX."users WHERE id=?",[(int)$_POST['id']]);
        $ns = $u['status']==='active'?'banned':'active';
        Database::update('users',['status'=>$ns],'id=?',[(int)$_POST['id']]);
        Session::flash('success',$isAr?'تم تحديث الحالة':'Status updated');
    }
    if ($pa === 'admin_add_balance') {
        $uid=(int)$_POST['user_id']; $amt=(float)$_POST['amount']; $type=$_POST['type']??'credit'; $desc=$_POST['desc']??'Admin adjustment';
        if($type==='credit') Wallet::credit($uid,$amt,'admin',$desc,$desc,"admin-".Auth::id());
        else Wallet::debit($uid,$amt,'admin',$desc,$desc,"admin-".Auth::id());
        Session::flash('success',$isAr?'تم تحديث الرصيد':'Balance updated');
    }

    // ── Coupons ──
    if ($pa === 'add_coupon') { Coupon::create(['code'=>strtoupper($_POST['code']),'type'=>$_POST['type']??'percent','value'=>(float)$_POST['value'],'min_order'=>(float)($_POST['min_order']??0),'max_discount'=>(float)($_POST['max_discount']??0),'max_uses'=>(int)($_POST['max_uses']??0),'starts_at'=>$_POST['starts_at']?$_POST['starts_at']:null,'expires_at'=>$_POST['expires_at']?$_POST['expires_at']:null,'status'=>1]); Session::flash('success',$isAr?'تم إضافة الكوبون':'Coupon added'); }
    if ($pa === 'delete_coupon') { Coupon::delete((int)$_POST['id']); Session::flash('success',$isAr?'تم الحذف':'Deleted'); }
    if ($pa === 'toggle_coupon') { $c=Database::fetch("SELECT status FROM ".DB_PREFIX."coupons WHERE id=?",[(int)$_POST['id']]); Database::update('coupons',['status'=>$c['status']?0:1],'id=?',[(int)$_POST['id']]); Session::flash('success',$isAr?'تم التحديث':'Updated'); }

    // ── Reviews ──
    if ($pa === 'review_action') { Review::update((int)$_POST['id'],['status'=>$_POST['status']??'approved']); if($_POST['status']==='approved'||$_POST['status']==='rejected') Review::updateProductRating((int)(Database::fetch("SELECT product_id FROM ".DB_PREFIX."reviews WHERE id=?",[int($_POST['id'])])['product_id']??0)); Session::flash('success',$isAr?'تم التحديث':'Updated'); }
    if ($pa === 'delete_review') { Review::delete((int)$_POST['id']); Session::flash('success',$isAr?'تم الحذف':'Deleted'); }

    // ── Security ──
    if ($pa === 'change_admin_pass') {
        $user=Auth::user();
        if(!password_verify($_POST['current']??'',$user['password'])) { Session::flash('error',$isAr?'كلمة المرور الحالية خاطئة':'Wrong current password'); }
        elseif(strlen($_POST['new_pass']??'')<8) { Session::flash('error',$isAr?'كلمة المرور قصيرة':'Password too short'); }
        elseif(($_POST['new_pass']??'')!==($_POST['confirm']??'')) { Session::flash('error',$isAr?'كلمتا المرور غير متطابقتين':'Passwords do not match'); }
        else { Database::update('users',['password'=>Auth::hashPassword($_POST['new_pass'])],'id=?',[Auth::id()]); Session::flash('success',$isAr?'تم تحديث كلمة المرور':'Password updated'); }
    }
    if ($pa === 'block_ip') { Security::blockIp($_POST['ip']??'0.0.0.0',$_POST['reason']??'',(bool)($_POST['permanent']??false),(int)($_POST['minutes']??60)); Session::flash('success',$isAr?'تم حظر IP':'IP blocked'); }
    if ($pa === 'unblock_ip') { Database::delete('blocked_ips','id=?',[(int)$_POST['id']]); Session::flash('success',$isAr?'تم رفع الحظر':'IP unblocked'); }

    // ── Settings ──
    if ($pa === 'save_settings') {
        $allowed=['store_name_ar','store_name_en','store_tagline_ar','store_tagline_en','currency','currency_symbol','default_lang','products_per_row','show_prices','show_flags','registration_enabled','guest_checkout','reviews_enabled','logo_url','favicon_url','contact_email','primary_color','secondary_color','accent_color','bg_dark','bg_sidebar','bg_card','hero_text_left','hero_text_right','hero_character','hero_subtext_ar','hero_subtext_en','hero_bg_start','hero_bg_mid','hero_bg_end','ticker_enabled','ticker_text_ar','ticker_text_en','ticker_speed','ticker_bg','ticker_direction','ticker_pause_hover','popup_enabled','popup_title_ar','popup_title_en','popup_message_ar','popup_message_en','popup_btn_ar','popup_btn_en','popup_emoji','popup_delay','popup_bg','popup_show_once','payment_wallet','payment_bank','payment_card','payment_paypal','bank_info','tax_enabled','tax_percent','shipping_enabled','shipping_cost','shipping_free_above','shipping_note','wallet_enabled','wallet_min_deposit','wallet_max_deposit','wallet_bonus','wallet_terms','whatsapp','telegram','snapchat','instagram','twitter','facebook','tiktok','youtube','mail_host','mail_port','mail_user','mail_from','mail_from_name','notify_new_order','notify_new_user','points_enabled','points_per_sar','points_redeem_rate','referral_enabled','referral_commission','maintenance_mode','maintenance_msg_ar','maintenance_msg_en','custom_css','meta_description_ar','meta_description_en','footer_text_ar','footer_text_en','mail_enabled','auto_backup_enabled','auto_backup_freq','auto_backup_keep','toast_enabled','toast_position','toast_duration','toast_autohide','footer_bg','footer_color','footer_font_size','footer_padding','footer_direction','footer_logo_position','footer_copyright_position','footer_copyright','footer_links'];
        $save = [];
        foreach($allowed as $k) {
            if(isset($_POST[$k])) $save[$k]=$_POST[$k];
            elseif(in_array($k,['ticker_enabled','popup_enabled','show_prices','show_flags','registration_enabled','guest_checkout','reviews_enabled','payment_wallet','payment_bank','payment_card','payment_paypal','tax_enabled','shipping_enabled','wallet_enabled','points_enabled','referral_enabled','maintenance_mode','ticker_pause_hover','popup_show_once','notify_new_order','notify_new_user','mail_enabled','auto_backup_enabled','toast_enabled','toast_autohide'])) $save[$k]='0';
        }
        Setting::setMany($save);
        Cache::flush();
        Session::flash('success', $isAr?'تم حفظ الإعدادات':'Settings saved');
    }


    // ── Tickets (Admin) ──
    if ($pa === 'admin_reply_ticket') {
        $tid   = (int)$_POST['ticket_id'];
        $msg   = trim($_POST['message'] ?? '');
        $close = !empty($_POST['close_after']);
        if ($tid && $msg) {
            Ticket::addReply($tid, Auth::id(), $msg, true);
            if ($close) Database::update('tickets', ['status'=>'closed','updated_at'=>date('Y-m-d H:i:s')], 'id=?', [$tid]);
            Session::flash('success', $isAr ? 'تم إرسال الرد' : 'Reply sent');
        }
    }
    if ($pa === 'update_ticket_status') {
        $tid = (int)$_POST['id'];
        $st  = $_POST['status'] ?? 'open';
        $allowed_st = ['open','in_progress','waiting','resolved','closed'];
        if ($tid && in_array($st, $allowed_st)) {
            Database::update('tickets', ['status'=>$st,'updated_at'=>date('Y-m-d H:i:s')], 'id=?', [$tid]);
            Session::flash('success', $isAr ? 'تم تحديث الحالة' : 'Status updated');
        }
    }
    if ($pa === 'delete_ticket') {
        $tid = (int)$_POST['id'];
        if ($tid) {
            Database::delete('ticket_replies', 'ticket_id=?', [$tid]);
            Database::delete('tickets', 'id=?', [$tid]);
            Session::flash('success', $isAr ? 'تم حذف التذكرة' : 'Ticket deleted');
        }
    }

    // ── Coupons (Edit) ──
    if ($pa === 'edit_coupon') {
        $id = (int)$_POST['id'];
        if ($id) {
            Database::update('coupons', [
                'code'         => strtoupper(trim($_POST['code'] ?? '')),
                'type'         => $_POST['type'] ?? 'percent',
                'value'        => (float)($_POST['value'] ?? 0),
                'min_order'    => (float)($_POST['min_order'] ?? 0),
                'max_discount' => (float)($_POST['max_discount'] ?? 0),
                'max_uses'     => (int)($_POST['max_uses'] ?? 0),
                'starts_at'    => $_POST['starts_at'] ?: null,
                'expires_at'   => $_POST['expires_at'] ?: null,
                'status'       => (int)($_POST['status'] ?? 1),
                'notes'        => $_POST['notes'] ?? '',
            ], 'id=?', [$id]);
            Session::flash('success', $isAr ? 'تم تحديث الكوبون' : 'Coupon updated');
        }
    }

    // ── Visitors ──
    if ($pa === 'clear_visitors') {
        Database::pdo()->exec("DELETE FROM ".DB_PREFIX."visitor_days");
        Session::flash('success', $isAr ? 'تم مسح سجل الزوار' : 'Visitor log cleared');
    }
    if ($pa === 'clear_all_visitors') {
        Database::pdo()->exec("DELETE FROM ".DB_PREFIX."visitors");
        Database::pdo()->exec("DELETE FROM ".DB_PREFIX."visitor_days");
        Session::flash('success', $isAr ? 'تم مسح كل السجلات' : 'All visitor records cleared');
    }
    if ($pa === 'add_ip_device') {
        $ip   = trim($_POST['ip_address'] ?? '');
        $name = trim($_POST['device_name'] ?? '');
        if ($ip && $name) {
            $ex = Database::fetch("SELECT id FROM ".DB_PREFIX."ip_devices WHERE ip_address=?", [$ip]);
            if ($ex) {
                Database::update('ip_devices', ['device_name'=>$name,'notes'=>$_POST['notes']??''], 'id=?', [$ex['id']]);
            } else {
                Database::insert('ip_devices', ['ip_address'=>$ip,'device_name'=>$name,'notes'=>$_POST['notes']??'']);
            }
            // Also update visitor_days device_name
            Database::pdo()->prepare("UPDATE ".DB_PREFIX."visitor_days SET device_name=? WHERE ip_address=?")->execute([$name,$ip]);
            Session::flash('success', $isAr ? 'تم حفظ اسم الجهاز' : 'Device name saved');
        }
    }
    if ($pa === 'edit_ip_device') {
        $id   = (int)$_POST['device_id'];
        $name = trim($_POST['device_name'] ?? '');
        if ($id && $name) {
            $dev = Database::fetch("SELECT ip_address FROM ".DB_PREFIX."ip_devices WHERE id=?", [$id]);
            Database::update('ip_devices', ['device_name'=>$name,'notes'=>$_POST['notes']??''], 'id=?', [$id]);
            if ($dev) Database::pdo()->prepare("UPDATE ".DB_PREFIX."visitor_days SET device_name=? WHERE ip_address=?")->execute([$name,$dev['ip_address']]);
            Session::flash('success', $isAr ? 'تم التحديث' : 'Updated');
        }
    }
    if ($pa === 'delete_ip_device') {
        Database::delete('ip_devices', 'id=?', [(int)$_POST['id']]);
        Session::flash('success', $isAr ? 'تم الحذف' : 'Deleted');
    }

    // ── Admins Management ──
    if ($pa === 'add_admin') {
        $email = trim($_POST['email'] ?? '');
        $name  = trim($_POST['name'] ?? '');
        $pass  = $_POST['password'] ?? '';
        $role  = in_array($_POST['role']??'admin', ['admin','moderator']) ? $_POST['role'] : 'admin';
        if ($name && $email && strlen($pass) >= 8) {
            if (!Database::exists('users', 'email=?', [$email])) {
                Database::insert('users', [
                    'name'     => $name,
                    'email'    => $email,
                    'password' => Auth::hashPassword($pass),
                    'role'     => $role,
                    'status'   => 'active',
                ]);
                Session::flash('success', $isAr ? 'تم إضافة المسؤول' : 'Admin added');
            } else {
                Session::flash('error', $isAr ? 'الإيميل مستخدم' : 'Email already exists');
            }
        } else {
            Session::flash('error', $isAr ? 'بيانات غير مكتملة' : 'Incomplete data');
        }
    }
    if ($pa === 'edit_admin') {
        $id   = (int)$_POST['id'];
        $role = in_array($_POST['role']??'admin', ['admin','moderator']) ? $_POST['role'] : 'admin';
        $upd  = [
            'name'        => trim($_POST['name'] ?? ''),
            'email'       => trim($_POST['email'] ?? ''),
            'role'        => $role,
            'status'      => $_POST['status'] ?? 'active',
            'permissions' => json_encode(array_values($_POST['permissions'] ?? [])),
        ];
        if (!empty($_POST['password']) && strlen($_POST['password']) >= 8) {
            $upd['password'] = Auth::hashPassword($_POST['password']);
        }
        if ($id && $upd['name']) {
            Database::update('users', $upd, 'id=?', [$id]);
            Session::flash('success', $isAr ? 'تم التحديث' : 'Updated');
        }
    }

    // ── Notifications (Admin → User) ──
    if ($pa === 'send_user_notification') {
        $uid = (int)$_POST['user_id'];
        $ta  = trim($_POST['title_ar'] ?? '');
        $te  = trim($_POST['title_en'] ?? '');
        $msg = trim($_POST['message_ar'] ?? '');
        $ic  = $_POST['icon'] ?? '🔔';
        if ($uid && $ta) {
            Notification::create($uid, $ta, $te ?: $ta, $msg, $msg, $ic, '#7c3aed');
            Session::flash('success', $isAr ? 'تم إرسال الإشعار' : 'Notification sent');
        }
    }

    // ── Bulk Delete Users ──
    if ($pa === 'bulk_delete_users') {
        $ids = array_filter(array_map('intval', explode(',', $_POST['ids'] ?? '')));
        $myId = Auth::id();
        $del = 0;
        foreach ($ids as $uid) {
            if ($uid !== $myId) {
                $u = Database::fetch("SELECT role FROM ".DB_PREFIX."users WHERE id=?", [$uid]);
                if ($u && $u['role'] !== 'admin') {
                    Database::delete('users', 'id=?', [$uid]);
                    $del++;
                }
            }
        }
        Session::flash('success', $isAr ? "تم حذف $del مستخدم" : "Deleted $del users");
    }

    // ── Backup (Selective + Delete) ──
    if ($pa === 'delete_backup') {
        $fn = basename($_POST['filename'] ?? '');
        $fp = STORAGE_PATH.'/backups/'.$fn;
        if ($fn && file_exists($fp) && str_ends_with($fn, '.sql')) {
            unlink($fp);
            Session::flash('success', $isAr ? 'تم الحذف' : 'Deleted');
        }
    }
    if ($pa === 'delete_all_backups') {
        foreach (glob(STORAGE_PATH.'/backups/*.sql') ?: [] as $f) { @unlink($f); }
        Session::flash('success', $isAr ? 'تم حذف كل النسخ' : 'All backups deleted');
    }

    // ── Backup ──
    if ($pa === 'create_backup') {
        @set_time_limit(120);
        $file  = STORAGE_PATH.'/backups/backup_'.date('Y-m-d_His').'.sql';
        $pdo   = Database::pdo();
        $scope = $_POST['backup_scope'] ?? 'full';
        $sql   = "-- Wupex Backup ".date('Y-m-d H:i:s')."\n-- Scope: $scope\n";
        if ($scope === 'selective' && !empty($_POST['tables'])) {
            $selTables = array_map(fn($t) => DB_PREFIX.preg_replace('/[^a-z0-9_]/','',$t), $_POST['tables']);
        } else {
            $selTables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        }
        foreach ($selTables as $tbl) {
            try {
                $cr = $pdo->query("SHOW CREATE TABLE `$tbl`")->fetch();
                if (!$cr) continue;
                $sql .= "\n\nDROP TABLE IF EXISTS `$tbl`;\n".$cr['Create Table'].";\n\n";
                $rows = $pdo->query("SELECT * FROM `$tbl`")->fetchAll();
                foreach ($rows as $r) {
                    $vals = array_map(fn($v) => $v===null ? 'NULL' : ("'".$pdo->quote($v)."'"), $r);
                    $sql .= "INSERT INTO `$tbl` VALUES (".implode(',',$vals).");\n";
                }
            } catch (\Exception $e) { $sql .= "-- Error on $tbl: ".$e->getMessage()."\n"; }
        }
        @mkdir(STORAGE_PATH.'/backups', 0755, true);
        file_put_contents($file, $sql);
        Session::flash('success', $isAr ? 'تم إنشاء النسخة الاحتياطية' : 'Backup created: '.basename($file));
    }

    // تسجيل الخروج
    if ($pa === 'logout') { Auth::logout(); header('Location: '.Helpers::siteUrl('admin/?p=login')); exit; }

    // معالجات إضافية
    AdminHandlers::handle($pa, $isAr);
    // معالجات المستخدمين
    if(AdminHandlers::handleUsers($pa, $isAr)) {
        $ref = $_POST['_redirect'] ?? "?p=users";
        Helpers::redirect(Helpers::siteUrl('admin/').$ref);
    }

    // ── Bulk Delete Products ──
    if ($pa === 'bulk_delete_products') {
        $ids = array_filter(array_map('intval', $_POST['ids'] ?? []));
        foreach ($ids as $id) {
            Database::delete('product_prices', 'product_id=?', [$id]);
            Database::delete('codes', 'product_id=?', [$id]);
            Product::delete($id);
        }
        Cache::flush();
        Session::flash('success', $isAr ? 'تم حذف '.count($ids).' منتج' : 'Deleted '.count($ids).' products');
    }

    // ── Bulk Delete Categories ──
    if ($pa === 'bulk_delete_categories') {
        $ids = array_filter(array_map('intval', $_POST['ids'] ?? []));
        foreach ($ids as $id) {
            Category::delete($id);
        }
        Cache::flush();
        Session::flash('success', $isAr ? 'تم حذف '.count($ids).' تصنيف' : 'Deleted '.count($ids).' categories');
    }

    // ── Bulk Delete Coupons ──
    if ($pa === 'bulk_delete_coupons') {
        $ids = array_filter(array_map('intval', $_POST['ids'] ?? []));
        foreach ($ids as $id) {
            Database::delete('coupons', 'id=?', [$id]);
        }
        Session::flash('success', $isAr ? 'تم حذف '.count($ids).' كوبون' : 'Deleted '.count($ids).' coupons');
    }

    // ── Bulk Delete Codes ──
    if ($pa === 'bulk_delete_codes') {
        $ids = array_filter(array_map('intval', $_POST['ids'] ?? []));
        foreach ($ids as $id) {
            Database::delete('codes', 'id=?', [$id]);
        }
        Session::flash('success', $isAr ? 'تم حذف '.count($ids).' كود' : 'Deleted '.count($ids).' codes');
    }

    // ── Bulk Delete Users ──
    if ($pa === 'bulk_delete_users') {
        $ids = array_filter(array_map('intval', $_POST['ids'] ?? []));
        $myId = Auth::id();
        foreach ($ids as $id) {
            if ($id == $myId) continue; // can't delete yourself
            Database::delete('users', 'id=?', [$id]);
        }
        Session::flash('success', $isAr ? 'تم حذف المستخدمين المحددين' : 'Selected users deleted');
    }

    // ── Save Role Preset ──
    if ($pa === 'save_role_preset') {
        $presets = json_decode(Setting::get('admin_role_presets','[]'), true) ?: [];
        $name = trim($_POST['preset_name'] ?? '');
        $perms = array_filter($_POST['preset_perms'] ?? []);
        if ($name) {
            $presets[] = ['name' => $name, 'perms' => array_values($perms)];
            Setting::set('admin_role_presets', json_encode($presets, JSON_UNESCAPED_UNICODE));
            Session::flash('success', $isAr ? 'تم حفظ الصلاحية' : 'Role preset saved');
        }
    }

    // ── Delete Role Preset ──
    if ($pa === 'delete_role_preset') {
        $idx = (int)($_POST['preset_idx'] ?? -1);
        $presets = json_decode(Setting::get('admin_role_presets','[]'), true) ?: [];
        if (isset($presets[$idx])) {
            array_splice($presets, $idx, 1);
            Setting::set('admin_role_presets', json_encode($presets, JSON_UNESCAPED_UNICODE));
            Session::flash('success', $isAr ? 'تم الحذف' : 'Deleted');
        }
    }

    // ── Apply Role Preset to Admin ──
    if ($pa === 'apply_role_preset') {
        $adminId = (int)($_POST['admin_id'] ?? 0);
        $presets = json_decode(Setting::get('admin_role_presets','[]'), true) ?: [];
        $idx = (int)($_POST['preset_idx'] ?? -1);
        if ($adminId && isset($presets[$idx])) {
            Database::update('users', ['permissions' => json_encode($presets[$idx]['perms'])], 'id=?', [$adminId]);
            Session::flash('success', $isAr ? 'تم تطبيق الصلاحية' : 'Preset applied');
        }
    }


    // إعادة التوجيه بعد POST
    $ref = $_POST['_redirect'] ?? "?p=$pg".($tab?"&tab=$tab":'');
    Helpers::redirect(Helpers::siteUrl('admin/').$ref);
}

// تسجيل الخروج GET
if ($pg === 'logout') { Auth::logout(); header('Location: '.Helpers::siteUrl()); exit; }

// render الصفحة
ob_start();

switch($pg) {
    case 'dashboard':
        $pageTitle = $isAr?'لوحة التحكم':'Dashboard';
        require VIEWS_PATH.'/admin/dashboard.php';
        break;
    case 'products':
        $pageTitle = $isAr?'إدارة المنتجات':'Products';
        require VIEWS_PATH.'/admin/products/index.php';
        break;
    case 'categories':
        $pageTitle = $isAr?'إدارة التصنيفات':'Categories';
        require VIEWS_PATH.'/admin/categories/index.php';
        break;
    case 'orders':
        $pageTitle = $isAr?'إدارة الطلبات':'Orders';
        require VIEWS_PATH.'/admin/orders/index.php';
        break;
    case 'users':
        $pageTitle = $isAr?'إدارة المستخدمين':'Users';
        require VIEWS_PATH.'/admin/users/index.php';
        break;
    case 'coupons':
        $pageTitle = $isAr?'إدارة الكوبونات':'Coupons';
        require VIEWS_PATH.'/admin/coupons/index.php';
        break;
    case 'reviews':
        $pageTitle = $isAr?'إدارة التقييمات':'Reviews';
        require VIEWS_PATH.'/admin/reviews/index.php';
        break;
    case 'tickets':
        $pageTitle = $isAr?'تذاكر الدعم الفني':'Support Tickets';
        require VIEWS_PATH.'/admin/tickets/index.php';
        break;
    case 'security':
        $pageTitle = $isAr?'الأمان المتقدم':'Security';
        require VIEWS_PATH.'/admin/security/index.php';
        break;
    case 'settings':
        $pageTitle = $isAr?'الإعدادات':'Settings';
        require VIEWS_PATH.'/admin/settings/general.php';
        break;
    case 'backup':
        $pageTitle = $isAr?'النسخ الاحتياطي':'Backup';
        require VIEWS_PATH.'/admin/backup/index.php';
        break;
    case 'reports':
        $pageTitle = $isAr?'التقارير':'Reports';
        $rStats = ['total_orders'=>Database::count('orders'),'completed'=>Database::count('orders',"status='completed'"),'revenue'=>Database::fetch("SELECT COALESCE(SUM(total),0) as v FROM ".DB_PREFIX."orders WHERE status='completed'")['v']??0,'users'=>Database::count('users',"role='user'"),'products'=>Database::count('products','status=1'),'reviews'=>Database::count('reviews'),'tickets'=>Database::count('tickets'),'visitors'=>Visitor::monthCount()];
        $monthly = Database::fetchAll("SELECT DATE_FORMAT(created_at,'%Y-%m') as mo, COUNT(*) as cnt, SUM(total) as rev FROM ".DB_PREFIX."orders GROUP BY mo ORDER BY mo DESC LIMIT 6");
        ob_start(); ?>
        <div class="stats-row">
          <?php foreach([['total_orders','📦',$isAr?'إجمالي الطلبات':'Total Orders','blue'],['completed','✅',$isAr?'مكتملة':'Completed','green'],['revenue',$sym,$isAr?'الإيرادات':'Revenue','orange'],['users','👥',$isAr?'المستخدمون':'Users','']] as [$k,$ic,$l,$cls]): ?>
          <div class="stat-c <?= $cls ?>"><div class="stat-num"><?= $k==='revenue'?$sym:'' ?><?= number_format($rStats[$k],is_float($rStats[$k])?2:0) ?></div><div class="stat-lbl"><?= $ic ?> <?= $l ?></div></div>
          <?php endforeach; ?>
        </div>
        <div class="frm-card"><h3>📅 <?= $isAr?'المبيعات الشهرية':'Monthly Sales' ?></h3>
          <table><thead><tr><th><?= $isAr?'الشهر':'Month' ?></th><th><?= $isAr?'الطلبات':'Orders' ?></th><th><?= $isAr?'الإيرادات':'Revenue' ?></th></tr></thead><tbody>
          <?php foreach($monthly as $m): ?><tr><td style="font-weight:700"><?= $m['mo'] ?></td><td><?= $m['cnt'] ?></td><td style="color:var(--success);font-weight:700"><?= $sym ?><?= number_format($m['rev'],2) ?></td></tr><?php endforeach; ?>
          </tbody></table>
        </div>
        <?php $repContent=ob_get_clean(); echo $repContent;
        break;
    case 'activity':
        $pageTitle = $isAr?'سجل النشاطات':'Activity Log';
        $logs = Database::fetchAll("SELECT al.*, u.name as uname FROM ".DB_PREFIX."activity_logs al LEFT JOIN ".DB_PREFIX."users u ON u.id=al.user_id ORDER BY al.created_at DESC LIMIT 100");
        ob_start(); ?>
        <div class="tbl-wrap"><table><thead><tr>
          <th><?= $isAr?'الإجراء':'Action' ?></th><th><?= $isAr?'المستخدم':'User' ?></th>
          <th>IP</th><th><?= $isAr?'الوقت':'Time' ?></th>
        </tr></thead><tbody>
        <?php foreach($logs as $l): ?><tr>
          <td><span class="bpill bp-processing"><?= htmlspecialchars($l['action']) ?></span> <?= htmlspecialchars($l['description']??'') ?></td>
          <td><?= htmlspecialchars($l['uname']??'-') ?></td>
          <td style="font-family:monospace;font-size:11px"><?= htmlspecialchars($l['ip_address']??'') ?></td>
          <td style="font-size:11px;color:var(--muted)"><?= $l['created_at'] ?></td>
        </tr><?php endforeach; ?>
        </tbody></table></div>
        <?php $actContent=ob_get_clean(); echo $actContent;
        break;
    case 'deposits':
        $pageTitle = $isAr?'طلبات الإيداع':'Deposits';
        require VIEWS_PATH.'/admin/deposits/index.php'; break;
    case 'transactions':
        $pageTitle = $isAr?'المعاملات المالية':'Transactions';
        require VIEWS_PATH.'/admin/wallet/index.php'; break;
    case 'wallet':
        $pageTitle = $isAr?'المحفظة':'Wallet';
        require VIEWS_PATH.'/admin/wallet/index.php'; break;
    case 'banners':
        $pageTitle = $isAr?'البانرات':'Banners';
        require VIEWS_PATH.'/admin/banners/index.php'; break;
    case 'codes':
        $pageTitle = $isAr?'الأكواد':'Codes';
        require VIEWS_PATH.'/admin/codes/index.php'; break;
    case 'notifications':
        $pageTitle = $isAr?'الإشعارات':'Notifications';
        require VIEWS_PATH.'/admin/notifications/index.php'; break;
    case 'points':
        $pageTitle = $isAr?'النقاط':'Points';
        require VIEWS_PATH.'/admin/points/index.php'; break;
    case 'referrals':
        $pageTitle = $isAr?'الإحالات':'Referrals';
        require VIEWS_PATH.'/admin/referrals/index.php'; break;
    case 'visitors':
        $pageTitle = $isAr?'الزوار':'Visitors';
        require VIEWS_PATH.'/admin/visitors/index.php'; break;
    case 'seo':
        $pageTitle = 'SEO';
        require VIEWS_PATH.'/admin/seo/index.php'; break;
    case 'pages':
        $pageTitle = $isAr?'الصفحات':'Pages';
        require VIEWS_PATH.'/admin/pages/index.php'; break;
    case 'faqs':
        $pageTitle = $isAr?'الأسئلة الشائعة':'FAQs';
        require VIEWS_PATH.'/admin/faqs/index.php'; break;
    case 'admins':
        $pageTitle = $isAr?'إدارة المسؤولين':'Admin Management';
        require VIEWS_PATH.'/admin/admins/index.php'; break;
    default:
        $pageTitle = $pageTitle ?? ($isAr?'قريباً':'Coming Soon');
        echo '<div class="frm-card" style="text-align:center;padding:40px"><div style="font-size:48px">🚧</div><h3 style="margin-top:12px">'.htmlspecialchars($pageTitle).'</h3><p style="color:var(--muted);margin-top:8px">'.($isAr?'هذه الصفحة قيد التطوير':'This page is under development').'</p></div>';
}

$content = ob_get_clean();
require VIEWS_PATH.'/layouts/admin.php';
// NOTE: هذا الملف يحتاج دمج handlers إضافية
// تمت إضافتها في نهاية الملف كـ patch


// Extra page routes (appended)

