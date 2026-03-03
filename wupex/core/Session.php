<?php
class Session {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_samesite', 'Lax');
            session_name(SESSION_NAME);
            session_set_cookie_params(['lifetime'=>SESSION_LIFETIME,'httponly'=>true,'samesite'=>'Lax']);
            session_start();
        }
    }
    public static function set(string $k, $v): void { $_SESSION[$k] = $v; }
    public static function get(string $k, $default = null) { return $_SESSION[$k] ?? $default; }
    public static function has(string $k): bool { return isset($_SESSION[$k]); }
    public static function del(string $k): void { unset($_SESSION[$k]); }
    public static function flash(string $k, $v = null) {
        if ($v !== null) { $_SESSION['_flash'][$k] = $v; return; }
        $val = $_SESSION['_flash'][$k] ?? null;
        unset($_SESSION['_flash'][$k]);
        return $val;
    }
    public static function hasFlash(string $k): bool { return isset($_SESSION['_flash'][$k]); }
    public static function destroy(): void { session_destroy(); $_SESSION = []; }
    public static function regenerate(): void { session_regenerate_id(true); }
    public static function csrf(): string {
        if (!self::has(CSRF_TOKEN_NAME)) {
            self::set(CSRF_TOKEN_NAME, bin2hex(random_bytes(32)));
        }
        return self::get(CSRF_TOKEN_NAME);
    }
    public static function verifyCsrf(string $token): bool {
        return hash_equals(self::get(CSRF_TOKEN_NAME,''), $token);
    }
    public static function setLang(string $lang): void { self::set('lang', in_array($lang,['ar','en'])?$lang:'ar'); }
    public static function getLang(): string { return self::get('lang', 'ar'); }
}
