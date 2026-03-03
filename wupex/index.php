<?php
define('WUPEX', true);
require_once __DIR__ . '/core/App.php';
// ── تحويل للمثبت إذا لم يكن المتجر مثبتاً بعد ──
if (!file_exists(__DIR__ . '/storage/installed.lock') && !file_exists(__DIR__ . '/install/.installed')) {
    header('Location: install/');
    exit;
}



// الصيانة
if (Setting::maintenanceMode() && !Auth::isAdmin()) {
    $lang = Lang::current(); $isAr = Lang::isRtl();
    $msg = htmlspecialchars(Setting::get("maintenance_msg_$lang", Setting::get('maintenance_msg_ar','قيد الصيانة...')));
    http_response_code(503);
    echo "<!DOCTYPE html><html lang='$lang' dir='".(Lang::dir())."'><head><meta charset='UTF-8'><title>Maintenance</title><style>body{background:#09071a;color:#f0eaff;font-family:Tajawal,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;text-align:center;padding:20px}.box{max-width:480px}.ico{font-size:64px;margin-bottom:18px}.t{font-size:26px;font-weight:900;margin-bottom:10px}.m{font-size:15px;opacity:.7;line-height:1.7}</style></head><body><div class='box'><div class='ico'>🔧</div><div class='t'>".($isAr?'قيد الصيانة':'Under Maintenance')."</div><div class='m'>$msg</div></div></body></html>";
    exit;
}

// الراوتر البسيط
$page   = Helpers::getStr('page', 'home');
$action = Helpers::getStr('action');

// معالجة الأكشنات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    if (!Session::verifyCsrf($_POST['csrf'] ?? '')) {
        Helpers::json(['success'=>false,'msg'=>'CSRF error'], 403);
    }

    match($postAction) {
        'login'          => (function() {
            $r = Auth::login($_POST['email']??'', $_POST['password']??'', !empty($_POST['remember']));
            if ($r['success']) { $redir=Session::flash('redirect'); Helpers::redirect($redir ?: Helpers::siteUrl()); }
            else { Session::flash('error', $r['msg']); Helpers::redirect(Helpers::siteUrl('?page=login')); }
        })(),
        'register'       => (function() {
            $S=Setting::all();
            if(empty($S['registration_enabled'])) { Session::flash('error','التسجيل مغلق'); Helpers::redirect(Helpers::siteUrl('?page=login')); }
            if(strlen($_POST['password']??'')<8) { Session::flash('error','كلمة المرور قصيرة'); Helpers::redirect(Helpers::siteUrl('?page=register')); return; }
            if(($_POST['password']??'')!==($_POST['confirm_password']??'')) { Session::flash('error','كلمات المرور غير متطابقة'); Helpers::redirect(Helpers::siteUrl('?page=register')); return; }
            $refBy=null;
            if(!empty($_POST['referral_code'])) { $refUser=Database::fetch("SELECT id FROM ".DB_PREFIX."users WHERE referral_code=?",[$_POST['referral_code']]); $refBy=$refUser['id']??null; }
            $r = Auth::register(['name'=>$_POST['name']??'','username'=>$_POST['username']??'','email'=>$_POST['email']??'','password'=>$_POST['password'],'phone'=>$_POST['phone']??'','referred_by'=>$refBy,'lang'=>Lang::current()]);
            if($r['success']) { Session::flash('success',Lang::get('register').' '.Lang::get('success')); Helpers::redirect(Helpers::siteUrl('?page=login')); }
            else { Session::flash('error',$r['msg']); Helpers::redirect(Helpers::siteUrl('?page=register')); }
        })(),
        'update_profile' => (function() {
            Auth::requireLogin();
            $uid=Auth::id();
            Database::update('users',['name'=>Helpers::postStr('name'),'username'=>Helpers::postStr('username'),'phone'=>Helpers::postStr('phone')],'id=?',[$uid]);
            Session::flash('success', Lang::current()==='ar'?'تم تحديث الملف الشخصي':'Profile updated');
            Helpers::redirect(Helpers::siteUrl('?page=account'));
        })(),
        'change_password' => (function() {
            Auth::requireLogin();
            $user=Auth::user();
            if(!password_verify($_POST['current_pass']??'',$user['password'])) { Session::flash('error',Lang::current()==='ar'?'كلمة المرور الحالية خاطئة':'Wrong current password'); Helpers::redirect(Helpers::siteUrl('?page=account')); return; }
            if(($_POST['new_pass']??'')!==($_POST['confirm_pass']??'')) { Session::flash('error',Lang::current()==='ar'?'كلمتا المرور غير متطابقتين':'Passwords do not match'); Helpers::redirect(Helpers::siteUrl('?page=account')); return; }
            Database::update('users',['password'=>Auth::hashPassword($_POST['new_pass'])],'id=?',[Auth::id()]);
            Session::flash('success',Lang::current()==='ar'?'تم تحديث كلمة المرور':'Password updated');
            Helpers::redirect(Helpers::siteUrl('?page=account'));
        })(),
        default => null,
    };
}

// JSON AJAX actions
if (Helpers::isAjax() || !empty($_GET['action'])) {
    $ajaxAction = $_GET['action'] ?? '';
    switch($ajaxAction) {
        case 'cart_add':
            if(!Session::verifyCsrf($_POST['csrf']??'')) Helpers::json(['success'=>false,'msg'=>'error'],403);
            $pid = Helpers::postInt('product_id'); $prid = Helpers::postInt('price_id'); $pr = (float)($_POST['price']??0);
            $prod = Product::find($pid);
            if(!$prod || !$prod['stock']) Helpers::json(['success'=>false,'msg'=>Lang::get('out_of_stock')]);
            Cart::add($pid, $prid, $pr, 1, Auth::id());
            Helpers::json(['success'=>true,'msg'=>Lang::get('added_cart'),'count'=>Cart::count(Auth::id())]);
            break;
        case 'cart_remove':
            if(!Session::verifyCsrf($_POST['csrf']??'')) Helpers::json(['success'=>false,'msg'=>'error'],403);
            Cart::remove(Helpers::postInt('cart_id'));
            Helpers::json(['success'=>true,'msg'=>Lang::get('removed_cart'),'count'=>Cart::count(Auth::id())]);
            break;
        case 'cart_update_qty':
            if(!Session::verifyCsrf($_POST['csrf']??'')) Helpers::json(['success'=>false,'msg'=>'error'],403);
            $cid = Helpers::postInt('cart_id');
            $qty = max(1,(int)($_POST['qty']??1));
            Cart::updateQty($cid,$qty);
            Helpers::json(['success'=>true,'count'=>Cart::count(Auth::id()),'total'=>number_format(Cart::total(Auth::id()),2)]);
            break;
        case 'cart_items':
            Helpers::json(['items'=>Cart::items(Auth::id()),'total'=>number_format(Cart::total(Auth::id()),2),'count'=>Cart::count(Auth::id())]);
            break;
        case 'wishlist_toggle':
            if(!Auth::check()) Helpers::json(['success'=>false,'redirect'=>Helpers::siteUrl('?page=login')],401);
            if(!Session::verifyCsrf($_POST['csrf']??'')) Helpers::json(['success'=>false,'msg'=>'error'],403);
            $added=Wishlist::toggle(Auth::id(), Helpers::postInt('product_id'));
            Helpers::json(['success'=>true,'added'=>$added]);
            break;
        case 'apply_coupon':
            if(!Session::verifyCsrf($_POST['csrf']??'')) Helpers::json(['success'=>false,'msg'=>'error'],403);
            $code=strtoupper(Helpers::postStr('code')); $total=Cart::total(Auth::id());
            $r=Coupon::validate($code,$total,Auth::id());
            if($r['valid']) Helpers::json(['success'=>true,'discount'=>$r['discount'],'new_total'=>number_format($total-$r['discount'],2),'msg'=>Lang::get('coupon_applied')]);
            else Helpers::json(['success'=>false,'msg'=>$r['msg']]);
    }
}

// ── معالجة POST العادية ──
if($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['action'])) {
    if(!Session::verifyCsrf($_POST['csrf']??'')) {
        Session::flash('error','CSRF error');
        Helpers::redirect($_SERVER['HTTP_REFERER']??Helpers::siteUrl());
    }
    $postAction = $_POST['action'];

    // إيداع
    if($postAction === 'submit_deposit') {
        Auth::requireLogin();
        $amount = (float)($_POST['amount']??0);
        $S2 = Setting::all();
        $minD = (float)($S2['min_deposit']??5);
        $maxD = (float)($S2['max_deposit']??2000);
        if($amount < $minD || $amount > $maxD) {
            Session::flash('error', Lang::current()==='ar'?"المبلغ يجب بين $minD و $maxD":"Amount must be between $minD and $maxD");
        } else {
            $ref = strtoupper(substr(md5(uniqid()),0,10));
            Database::insert('deposit_requests',[
                'user_id'        => Auth::id(),
                'ref_number'     => $ref,
                'amount'         => $amount,
                'bonus'          => round($amount * (float)($S2['wallet_bonus_percent']??0) / 100, 2),
                'payment_method' => $_POST['payment_method']??'bank',
                'status'         => 'pending',
            ]);
            ActivityLog::log('deposit_request','user',Auth::id(),"طلب إيداع: $amount");
            Session::flash('success', Lang::current()==='ar'?'تم إرسال طلب الإيداع بنجاح، سيتم مراجعته قريباً.':'Deposit request submitted, will be reviewed soon.');
        }
        Helpers::redirect(Helpers::siteUrl('?page=deposit'));
    }

    // إنشاء طلب شراء
    if($postAction === 'place_order') {
        Auth::requireLogin();
        $cartItems = Cart::items(Auth::id());
        if(empty($cartItems)) { Session::flash('error','السلة فارغة'); Helpers::redirect(Helpers::siteUrl('?page=cart')); }
        $S2 = Setting::all();
        $cartTotal = Cart::total(Auth::id());
        $payMethod = $_POST['payment_method']??'wallet';
        $couponCode = strtoupper(trim($_POST['coupon_code']??''));
        $discount = 0;
        if($couponCode) {
            $cv = Coupon::validate($couponCode,$cartTotal,Auth::id());
            if($cv['valid']) { $discount = $cv['discount']; Coupon::use($couponCode,Auth::id()); }
        }
        $tax      = (float)($_POST['tax']??0);
        $shipping = (float)($_POST['shipping']??0);
        $grand    = $cartTotal - $discount + $tax + $shipping;
        // التحقق من رصيد المحفظة
        if($payMethod==='wallet') {
            $bal = Wallet::balance(Auth::id());
            if($bal < $grand) { Session::flash('error',Lang::current()==='ar'?'رصيد المحفظة غير كافٍ':'Insufficient wallet balance'); Helpers::redirect(Helpers::siteUrl('?page=checkout')); }
        }
        // إنشاء الطلب
        $orderNum = 'WX-'.strtoupper(substr(md5(uniqid()),0,8));
        $orderId = Database::insert('orders',[
            'order_number'   => $orderNum,
            'user_id'        => Auth::id(),
            'subtotal'       => $cartTotal,
            'discount'       => $discount,
            'tax'            => $tax,
            'shipping'       => $shipping,
            'total'          => $grand,
            'coupon_code'    => $couponCode ?: null,
            'payment_method' => $payMethod,
            'status'         => 'pending',
        ]);
        // إضافة عناصر الطلب + توزيع الأكواد
        $allCompleted = true;
        foreach($cartItems as $item) {
            $codes = [];
            if($item['delivery_type']==='instant') {
                $avail = Database::fetchAll("SELECT id,code FROM ".DB_PREFIX."codes WHERE product_id=? AND ".($item['price_id']?"price_id=?":"price_id IS NULL")." AND status='available' LIMIT ?",
                    $item['price_id'] ? [$item['product_id'],$item['price_id'],$item['quantity']] : [$item['product_id'],$item['quantity']]);
                foreach($avail as $cd) {
                    Database::update('codes',['status'=>'sold','order_id'=>$orderId,'sold_at'=>date('Y-m-d H:i:s')],'id=?',[$cd['id']]);
                    $codes[] = $cd['code'];
                }
                if(count($codes) < $item['quantity']) $allCompleted = false;
            }
            Database::insert('order_items',[
                'order_id'     => $orderId,
                'product_id'   => $item['product_id'],
                'price_id'     => $item['price_id'],
                'product_name' => $item['name_ar']??$item['name_en']??'',
                'price_label'  => $item['price_label']??'',
                'price'        => $item['price'],
                'quantity'     => $item['quantity'],
                'codes'        => implode(',', $codes),
            ]);
            // تحديث عداد المبيعات
            Database::update('products',['sales_count' => (int)Database::fetch("SELECT sales_count FROM ".DB_PREFIX."products WHERE id=?",[$item['product_id']])['sales_count'] + $item['quantity']],'id=?',[$item['product_id']]);
        }
        // تحديث حالة الطلب
        $orderStatus = $allCompleted ? 'completed' : 'processing';
        Database::update('orders',['status'=>$orderStatus],'id=?',[$orderId]);
        // خصم من المحفظة
        if($payMethod==='wallet') {
            Wallet::debit(Auth::id(),$grand,'order',Lang::current()==='ar'?"دفع طلب #$orderNum":"Order payment #$orderNum",'',$orderNum);
        }
        // نقاط
        if(!empty($S2['points_enabled']) && !empty($S2['points_per_sar'])) {
            $pts = (int)($grand * (float)$S2['points_per_sar']);
            if($pts>0) {
                Database::update('users',['points'=>(int)(Auth::user()['points']??0)+$pts],'id=?',[Auth::id()]);
                Database::insert('points_transactions',['user_id'=>Auth::id(),'type'=>'order','amount'=>$pts,'ref'=>$orderNum]);
            }
        }
        // تفريغ السلة
        Cart::clear(Auth::id());
        ActivityLog::log('place_order','order',$orderId,"طلب #$orderNum بقيمة $grand");
        Session::flash('success', Lang::current()==='ar'?"✅ تم إنشاء طلبك بنجاح! رقم الطلب: $orderNum":"✅ Order placed! Order #: $orderNum");
        Helpers::redirect(Helpers::siteUrl('?page=orders'));
    }

    // تذكرة دعم
    if($postAction === 'create_ticket') {
        Auth::requireLogin();
        $subject = trim($_POST['subject']??'');
        $message = trim($_POST['message']??'');
        if(empty($subject)||empty($message)) { Session::flash('error','يرجى ملء جميع الحقول'); Helpers::redirect(Helpers::siteUrl('?page=tickets')); }
        $tNum = 'TK-'.strtoupper(substr(md5(uniqid()),0,6));
        $tid = Database::insert('tickets',['ticket_number'=>$tNum,'user_id'=>Auth::id(),'subject'=>$subject,'status'=>'open','priority'=>$_POST['priority']??'medium']);
        Database::insert('ticket_replies',['ticket_id'=>$tid,'user_id'=>Auth::id(),'is_admin'=>0,'message'=>$message]);
        Session::flash('success', Lang::current()==='ar'?"✅ تم فتح التذكرة #$tNum":"✅ Ticket #$tNum opened");
        Helpers::redirect(Helpers::siteUrl('?page=ticket&id='.$tid));
    }
    if($postAction === 'user_reply_ticket') {
        Auth::requireLogin();
        $tid = (int)($_POST['ticket_id']??0);
        $msg = trim($_POST['message']??'');
        $tk  = $tid ? Database::fetch("SELECT * FROM ".DB_PREFIX."tickets WHERE id=? AND user_id=?",[$tid,Auth::id()]) : null;
        if($tk && $msg && $tk['status']!=='closed') {
            Ticket::addReply($tid, Auth::id(), $msg, false);
            Session::flash('success', Lang::current()==='ar'?'تم إرسال الرد':'Reply sent');
        }
        Helpers::redirect(Helpers::siteUrl('?page=ticket&id='.$tid));
    }
    if($postAction === 'user_close_ticket') {
        Auth::requireLogin();
        $tid = (int)($_POST['ticket_id']??0);
        $tk  = $tid ? Database::fetch("SELECT * FROM ".DB_PREFIX."tickets WHERE id=? AND user_id=?",[$tid,Auth::id()]) : null;
        if($tk && $tk['status']!=='closed') {
            Database::update('tickets',['status'=>'closed','updated_at'=>date('Y-m-d H:i:s')],'id=?',[$tid]);
            Session::flash('success', Lang::current()==='ar'?'تم إغلاق التذكرة':'Ticket closed');
        }
        Helpers::redirect(Helpers::siteUrl('?page=ticket&id='.$tid));
    }
    if($postAction === 'user_reopen_ticket') {
        Auth::requireLogin();
        $tid = (int)($_POST['ticket_id']??0);
        $tk  = $tid ? Database::fetch("SELECT * FROM ".DB_PREFIX."tickets WHERE id=? AND user_id=?",[$tid,Auth::id()]) : null;
        if($tk && $tk['status']==='closed') {
            Database::update('tickets',['status'=>'open','updated_at'=>date('Y-m-d H:i:s')],'id=?',[$tid]);
            Session::flash('success', Lang::current()==='ar'?'تم إعادة فتح التذكرة':'Ticket reopened');
        }
        Helpers::redirect(Helpers::siteUrl('?page=ticket&id='.$tid));
    }
}

// تسجيل الخروج
if($page === 'logout') { Auth::logout(); Helpers::redirect(Helpers::siteUrl()); }

// تحضير البيانات
$allCategories = Category::withCount();
$catFilter = Helpers::getInt('cat');
$allProductsData = Product::all($catFilter ? ['category_id'=>$catFilter] : [], 1, 500);
$allProducts = $allProductsData['items'];
$allPrices = [];
foreach($allProducts as $p) {
    $prices = Product::prices($p['id']);
    if($prices) $allPrices[$p['id']] = $prices;
}

// عرض الصفحة
ob_start();
$S = Setting::all();
$lang = Lang::current();

switch($page) {
    case 'home':
    case 'products':
        require VIEWS_PATH.'/front/home.php';
        break;
    case 'account':
    case 'profile':
        Auth::requireLogin();
        $user = Auth::user();
        $transactions = Wallet::transactions(Auth::id(), 10);
        require VIEWS_PATH.'/front/profile.php';
        break;
    case 'orders':
        Auth::requireLogin();
        $orders = Order::userOrders(Auth::id());
        require VIEWS_PATH.'/front/orders.php';
        break;
    case 'wallet':
        Auth::requireLogin();
        $transactions = Wallet::transactions(Auth::id(), 20);
        require VIEWS_PATH.'/front/wallet.php';
        break;
    case 'wishlist':
        Auth::requireLogin();
        $wishItems = Wishlist::items(Auth::id());
        require VIEWS_PATH.'/front/wishlist.php';
        break;
    case 'notifications':
        Auth::requireLogin();
        Notification::markRead(Auth::id());
        $notifs = Database::fetchAll("SELECT * FROM ".DB_PREFIX."notifications WHERE user_id=? OR is_broadcast=1 ORDER BY created_at DESC LIMIT 50",[Auth::id()]);
        require VIEWS_PATH.'/front/notifications.php';
        break;
    case 'tickets':
        Auth::requireLogin();
        $myTickets = Ticket::userTickets(Auth::id());
        require VIEWS_PATH.'/front/tickets.php';
        break;
    case 'ticket':
        Auth::requireLogin();
        $ticketId = Helpers::getInt('id');
        $ticket   = $ticketId ? Database::fetch("SELECT * FROM ".DB_PREFIX."tickets WHERE id=? AND user_id=?",[$ticketId,Auth::id()]) : null;
        if (!$ticket) { Session::flash('error','التذكرة غير موجودة'); Helpers::redirect(Helpers::siteUrl('?page=tickets')); }
        $ticketReplies = Ticket::replies($ticketId);
        require VIEWS_PATH.'/front/ticket_view.php';
        break;
    case 'cart':
        $cartItems = Cart::items(Auth::id());
        require VIEWS_PATH.'/front/cart.php';
        break;
    case 'checkout':
        $cartItems = Cart::items(Auth::id());
        require VIEWS_PATH.'/front/checkout.php';
        break;
    case 'login':
        if(Auth::check()) Helpers::redirect(Helpers::siteUrl());
        require VIEWS_PATH.'/auth/login.php';
        break;
    case 'register':
        if(Auth::check()) Helpers::redirect(Helpers::siteUrl());
        require VIEWS_PATH.'/auth/register.php';
        break;
    case 'about':   require VIEWS_PATH.'/front/about.php'; break;
    case 'contact': require VIEWS_PATH.'/front/contact.php'; break;
    case 'faq':     require VIEWS_PATH.'/front/faq.php'; break;
    case 'deposit':
        Auth::requireLogin();
        require VIEWS_PATH.'/front/deposit.php'; break;
    case 'points':
        Auth::requireLogin();
        require VIEWS_PATH.'/front/points.php'; break;
    case 'referral':
        Auth::requireLogin();
        require VIEWS_PATH.'/front/referral.php'; break;
    case 'check_code':  require VIEWS_PATH.'/front/check_code.php'; break;
    case 'invoices':
        Auth::requireLogin();
        require VIEWS_PATH.'/front/invoices.php'; break;
    case 'invoice':
        Auth::requireLogin();
        require VIEWS_PATH.'/front/invoices.php'; break;
    case 'settings':
        Auth::requireLogin();
        Helpers::redirect(Helpers::siteUrl('?page=account&tab=settings')); break;
    case 'returns':  require VIEWS_PATH.'/front/returns.php'; break;
    case 'privacy':  require VIEWS_PATH.'/front/privacy.php'; break;
    case 'subscriptions':
    case 'offers':
        require VIEWS_PATH.'/front/home.php'; break;
    default:
        $pageRow = Database::fetch("SELECT * FROM ".DB_PREFIX."pages WHERE slug=? AND status=1", [$page]);
        if($pageRow) { require VIEWS_PATH.'/front/page.php'; }
        else { http_response_code(404); require VIEWS_PATH.'/errors/404.php'; }
}

$content = ob_get_clean();
require VIEWS_PATH.'/layouts/front.php';
