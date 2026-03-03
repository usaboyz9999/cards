<?php
/**
 * معالجات إضافية للوحة التحكم
 * يتم استدعاؤها من admin.php
 */
class AdminHandlers
{
    public static function handle(string $action, bool $isAr): void
    {
        switch ($action) {
            // ── Deposits ──
            case 'approve_deposit':
                $dep = Database::fetch("SELECT * FROM ".DB_PREFIX."deposit_requests WHERE id=?", [(int)$_POST['id']]);
                if ($dep && $dep['status'] === 'pending') {
                    Wallet::credit($dep['user_id'], $dep['amount'] + $dep['bonus'], 'deposit',
                        $isAr ? 'شحن محفظة معتمد' : 'Deposit approved', '', 'dep-'.$dep['id']);
                    Database::update('deposit_requests', ['status'=>'approved','processed_at'=>date('Y-m-d H:i:s')], 'id=?', [$dep['id']]);
                    Notification::create($dep['user_id'],
                        $isAr?'تم قبول الإيداع':'Deposit Approved',
                        'Deposit Approved',
                        $isAr?'تم شحن محفظتك بمبلغ '.number_format($dep['amount'],2):'Your wallet was credited',
                        'Your wallet was credited',
                        '✅','#10b981','?page=wallet'
                    );
                    Session::flash('success', $isAr?'تم قبول الإيداع':'Deposit approved');
                }
                break;

            case 'reject_deposit':
                Database::update('deposit_requests', ['status'=>'rejected','processed_at'=>date('Y-m-d H:i:s')], 'id=?', [(int)$_POST['id']]);
                Session::flash('success', $isAr?'تم رفض الإيداع':'Deposit rejected');
                break;

            // ── Banners ──
            case 'add_banner':
                $img = !empty($_FILES['image']['tmp_name']) ? Helpers::uploadImage($_FILES['image'], 'banners') : null;
                Database::insert('banners', [
                    'title_ar'   => $_POST['title_ar'],
                    'title_en'   => $_POST['title_en'] ?? '',
                    'image'      => $img,
                    'link_url'   => $_POST['link_url'] ?? '',
                    'position'   => $_POST['position'] ?? 'hero',
                    'start_date' => $_POST['start_date'] ?: null,
                    'end_date'   => $_POST['end_date'] ?: null,
                    'status'     => isset($_POST['status']) ? 1 : 0,
                    'sort_order' => (int)($_POST['sort_order'] ?? 99),
                ]);
                Cache::flush();
                Session::flash('success', $isAr?'تمت الإضافة':'Banner added');
                break;

            case 'toggle_banner':
                $b = Database::fetch("SELECT status FROM ".DB_PREFIX."banners WHERE id=?", [(int)$_POST['id']]);
                Database::update('banners', ['status' => $b['status'] ? 0 : 1], 'id=?', [(int)$_POST['id']]);
                Cache::flush();
                Session::flash('success', $isAr?'تم التحديث':'Updated');
                break;

            case 'delete_banner':
                Database::delete('banners', 'id=?', [(int)$_POST['id']]);
                Cache::flush();
                Session::flash('success', $isAr?'تم الحذف':'Deleted');
                break;

            // ── Codes ──
            case 'import_codes':
                $pid    = (int)$_POST['product_id'];
                $priceId= (int)($_POST['price_id'] ?? 0) ?: null;
                $raw    = trim($_POST['codes'] ?? '');
                $lines  = array_filter(array_map('trim', explode("\n", $raw)));
                $count  = Code::import($pid, $priceId, $lines);
                Session::flash('success', $isAr ? "تم رفع $count كود" : "$count codes imported");
                break;

            case 'delete_code':
                Database::delete('codes', 'id=? AND status=?', [(int)$_POST['id'], 'available']);
                Session::flash('success', $isAr?'تم الحذف':'Deleted');
                break;

            // ── Broadcast Notification ──
            case 'broadcast_notification':
                Notification::broadcast(
                    $_POST['title_ar'] ?? '',
                    $_POST['title_en'] ?? '',
                    $_POST['message_ar'] ?? '',
                    $_POST['message_en'] ?? '',
                    $_POST['icon'] ?? '📢'
                );
                Session::flash('success', $isAr?'تم إرسال الإشعار للجميع':'Notification broadcast to all');
                break;

            // ── FAQs ──
            case 'add_faq':
                Database::insert('faqs', ['question_ar'=>$_POST['question_ar'],'question_en'=>$_POST['question_en']??'','answer_ar'=>$_POST['answer_ar'],'answer_en'=>$_POST['answer_en']??'','sort_order'=>(int)($_POST['sort_order']??99),'status'=>isset($_POST['status'])?1:0]);
                Session::flash('success', $isAr?'تمت الإضافة':'Added');
                break;
            case 'edit_faq':
                Database::update('faqs',['question_ar'=>$_POST['question_ar'],'question_en'=>$_POST['question_en']??'','answer_ar'=>$_POST['answer_ar'],'answer_en'=>$_POST['answer_en']??'','sort_order'=>(int)($_POST['sort_order']??99),'status'=>isset($_POST['status'])?1:0],'id=?',[(int)$_POST['id']]);
                Session::flash('success', $isAr?'تم التحديث':'Updated');
                break;
            case 'delete_faq':
                Database::delete('faqs','id=?',[(int)$_POST['id']]);
                Session::flash('success', $isAr?'تم الحذف':'Deleted');
                break;

            // ── Pages ──
            case 'add_page':
                Database::insert('pages',['slug'=>Helpers::slug($_POST['slug']??'page'),'title_ar'=>$_POST['title_ar'],'title_en'=>$_POST['title_en']??'','content_ar'=>$_POST['content_ar']??'','content_en'=>$_POST['content_en']??'','sort_order'=>(int)($_POST['sort_order']??99),'status'=>isset($_POST['status'])?1:0]);
                Session::flash('success', $isAr?'تمت الإضافة':'Page added');
                break;
            case 'edit_page':
                Database::update('pages',['title_ar'=>$_POST['title_ar'],'title_en'=>$_POST['title_en']??'','content_ar'=>$_POST['content_ar']??'','content_en'=>$_POST['content_en']??'','status'=>isset($_POST['status'])?1:0],'id=?',[(int)$_POST['id']]);
                Session::flash('success', $isAr?'تم التحديث':'Updated');
                break;
            case 'delete_page':
                Database::delete('pages','id=?',[(int)$_POST['id']]);
                Session::flash('success', $isAr?'تم الحذف':'Deleted');
                break;

            // ── SEO ──
            case 'save_seo_meta':
                $slug = Helpers::slug($_POST['page_slug']??'');
                $ex = Database::fetch("SELECT id FROM ".DB_PREFIX."seo_metas WHERE page_slug=?",[$slug]);
                if($ex) { Database::update('seo_metas',['meta_title_ar'=>$_POST['meta_title_ar']??'','meta_title_en'=>$_POST['meta_title_en']??'','meta_desc_ar'=>$_POST['meta_desc_ar']??'','meta_desc_en'=>$_POST['meta_desc_en']??''],'id=?',[$ex['id']]); }
                else { Database::insert('seo_metas',['page_slug'=>$slug,'meta_title_ar'=>$_POST['meta_title_ar']??'','meta_title_en'=>$_POST['meta_title_en']??'','meta_desc_ar'=>$_POST['meta_desc_ar']??'','meta_desc_en'=>$_POST['meta_desc_en']??'']); }
                Session::flash('success', $isAr?'تم الحفظ':'Saved');
                break;
            case 'delete_seo':
                Database::delete('seo_metas','id=?',[(int)$_POST['id']]);
                Session::flash('success', $isAr?'تم الحذف':'Deleted');
                break;
        }
    }

    // ===== User Management =====
    public static function handleUsers(string $action, bool $isAr): bool {
        switch($action) {
            case 'toggle_user_status':
                $uid = (int)$_POST['id'];
                $u = Database::fetch("SELECT status FROM ".DB_PREFIX."users WHERE id=?",[$uid]);
                if($u) {
                    $new = $u['status']==='active' ? 'banned' : 'active';
                    Database::update('users',['status'=>$new],'id=?',[$uid]);
                    Session::flash('success',$isAr?($new==='active'?'تم تفعيل المستخدم':'تم إيقاف المستخدم'):($new==='active'?'User activated':'User banned'));
                }
                return true;
            case 'admin_add_balance':
                $uid = (int)$_POST['user_id'];
                $amt = (float)$_POST['amount'];
                $type = $_POST['type'] ?? 'credit';
                $desc = htmlspecialchars($_POST['desc'] ?? '');
                if($amt > 0) {
                    if($type === 'credit') Wallet::credit($uid,$amt,'admin',$desc,$desc);
                    else Wallet::debit($uid,$amt,'admin',$desc,$desc);
                    Session::flash('success',$isAr?'تم تعديل الرصيد':'Balance updated');
                }
                return true;
            case 'add_user':
                $email = $_POST['email'] ?? '';
                $name  = $_POST['name'] ?? '';
                $pass  = $_POST['password'] ?? '';
                $role  = in_array($_POST['role']??'user',['user','moderator','admin']) ? $_POST['role'] : 'user';
                if($email && $name && strlen($pass)>=6) {
                    if(Database::exists('users','email=?',[$email])) {
                        Session::flash('error',$isAr?'البريد مسجل مسبقاً':'Email already registered');
                    } else {
                        $refCode = strtoupper(substr(md5($email.time()),0,8));
                        Database::insert('users',[
                            'name'=>$name,'email'=>$email,
                            'password'=>password_hash($pass,PASSWORD_BCRYPT,['cost'=>12]),
                            'role'=>$role,'status'=>'active','email_verified'=>1,
                            'referral_code'=>$refCode,
                            'wallet_balance'=>(float)($_POST['wallet']??0),
                            'points'=>(int)($_POST['points']??0),
                        ]);
                        Session::flash('success',$isAr?'تم إنشاء الحساب':'User created');
                    }
                } else {
                    Session::flash('error',$isAr?'بيانات غير مكتملة (كلمة المرور 6+ أحرف)':'Incomplete data (password 6+ chars)');
                }
                return true;
            case 'edit_user':
                $uid = (int)$_POST['id'];
                $updates = ['name'=>$_POST['name']??'','role'=>$_POST['role']??'user','status'=>$_POST['status']??'active'];
                if(!empty($_POST['password']) && strlen($_POST['password'])>=6)
                    $updates['password'] = password_hash($_POST['password'],PASSWORD_BCRYPT,['cost'=>12]);
                if(!empty($_POST['email'])) $updates['email'] = $_POST['email'];
                Database::update('users',$updates,'id=?',[$uid]);
                Session::flash('success',$isAr?'تم التحديث':'User updated');
                return true;
            case 'delete_user':
                $uid = (int)$_POST['id'];
                if($uid !== 1) {
                    Database::query("DELETE FROM ".DB_PREFIX."users WHERE id=? AND role!='admin'",[$uid]);
                    Session::flash('success',$isAr?'تم الحذف':'User deleted');
                }
                return true;
            case 'bulk_delete_users':
                $ids = array_filter(array_map('intval', explode(',', $_POST['ids']??'')));
                foreach($ids as $uid) {
                    if($uid > 1) Database::query("DELETE FROM ".DB_PREFIX."users WHERE id=? AND role!='admin'",[$uid]);
                }
                Session::flash('success',$isAr?'تم حذف '.count($ids).' مستخدم':'Deleted '.count($ids).' users');
                return true;
            case 'send_user_notification':
                $uid = (int)$_POST['user_id'];
                Notification::create($uid, $_POST['title_ar']??'', $_POST['title_en']??$_POST['title_ar']??'', $_POST['message_ar']??'', $_POST['message_ar']??'', $_POST['icon']??'🔔');
                Session::flash('success',$isAr?'تم إرسال الإشعار':'Notification sent');
                return true;
        }
        return false;
    }
}
