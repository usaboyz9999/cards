<?php
class Auth {
    private static ?array $user = null;

    public static function check(): bool { return Session::has('user_id'); }

    // ✅ null safety - user() can return null, prevent TypeError in PHP 8
    public static function isAdmin(): bool { return (self::user()['role'] ?? '') === 'admin'; }
    public static function isModerator(): bool { return in_array((self::user()['role'] ?? ''), ['admin','moderator']); }
    public static function id(): ?int { return Session::get('user_id'); }

    public static function user(): ?array {
        if (self::$user) return self::$user;
        if (!Session::has('user_id')) return null;
        self::$user = Database::fetch(
            "SELECT * FROM ".DB_PREFIX."users WHERE id=? AND status='active'",
            [Session::get('user_id')]
        );
        return self::$user;
    }

    public static function login(string $email, string $pass, bool $remember = false): array {
        $ip = Helpers::ip();
        $attempts = Database::count('login_attempts',
            "ip_address=? AND success=0 AND attempted_at > DATE_SUB(NOW(),INTERVAL 15 MINUTE)", [$ip]);
        if ($attempts >= 5) {
            ActivityLog::log('login_blocked', null, null, "IP محظور مؤقتاً: $ip");
            return ['success'=>false,'msg'=>'تم تجاوز الحد المسموح. انتظر 15 دقيقة.'];
        }
        $user = Database::fetch("SELECT * FROM ".DB_PREFIX."users WHERE email=?", [$email]);
        if (!$user || !password_verify($pass, $user['password'])) {
            Database::insert('login_attempts', ['email'=>$email,'ip_address'=>$ip,'success'=>0]);
            return ['success'=>false,'msg'=>'البريد الإلكتروني أو كلمة المرور غير صحيحة.'];
        }
        if ($user['status'] === 'banned') return ['success'=>false,'msg'=>'تم حظر هذا الحساب.'];

        Database::insert('login_attempts', ['email'=>$email,'ip_address'=>$ip,'success'=>1]);
        Database::update('users', ['last_login'=>date('Y-m-d H:i:s'),'last_ip'=>$ip], 'id=?', [$user['id']]);

        // ✅ Save session data before regenerate to prevent data loss on shared hosting
        $sessionData = $_SESSION;
        session_write_close();
        Session::start();
        session_regenerate_id(false); // false = don't delete old session file (safer on shared hosting)

        // Restore + set new data
        foreach ($sessionData as $k => $v) $_SESSION[$k] = $v;
        Session::set('user_id',   $user['id']);
        Session::set('user_role', $user['role']);
        Session::set('user_name', $user['name']);

        ActivityLog::log('login', 'user', $user['id'], 'تسجيل دخول ناجح');
        return ['success'=>true,'user'=>$user];
    }

    public static function register(array $data): array {
        if (Database::exists('users', 'email=?', [$data['email']])) {
            return ['success'=>false,'msg'=>'البريد الإلكتروني مسجل مسبقاً.'];
        }
        if (!empty($data['username']) && Database::exists('users', 'username=?', [$data['username']])) {
            return ['success'=>false,'msg'=>'اسم المستخدم محجوز.'];
        }
        $referralCode = strtoupper(substr(md5($data['email'].time()),0,8));
        $id = Database::insert('users', [
            'name'          => $data['name'],
            'username'      => $data['username'] ?? null,
            'email'         => $data['email'],
            'password'      => password_hash($data['password'], PASSWORD_BCRYPT, ['cost'=>HASH_COST]),
            'phone'         => $data['phone'] ?? null,
            'referral_code' => $referralCode,
            'referred_by'   => $data['referred_by'] ?? null,
            'locale'        => $data['lang'] ?? 'ar',
            'email_token'   => bin2hex(random_bytes(20)),
        ]);
        Notification::create(null, 'مستخدم جديد', 'New User', "انضم {$data['name']} للمتجر!", "User {$data['name']} joined!", '👤', '#7c3aed');
        ActivityLog::log('register', 'user', $id, 'تسجيل مستخدم جديد');
        return ['success'=>true,'id'=>$id];
    }

    public static function logout(): void {
        self::$user = null;
        ActivityLog::log('logout', 'user', self::id());
        Session::destroy();
        Session::start();
    }

    public static function requireLogin(): void {
        if (!self::check()) {
            Session::flash('redirect', $_SERVER['REQUEST_URI']);
            header('Location: '.Helpers::siteUrl('?page=login')); exit;
        }
    }

    public static function requireAdmin(): void {
        if (!self::check() || !self::isAdmin()) {
            Session::set('admin_redirect', $_SERVER['REQUEST_URI'] ?? '');
            header('Location: '.Helpers::siteUrl('admin/?p=login')); exit;
        }
    }

    public static function hashPassword(string $pass): string {
        return password_hash($pass, PASSWORD_BCRYPT, ['cost'=>HASH_COST]);
    }
}
