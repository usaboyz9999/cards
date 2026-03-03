<?php
class Security {
    public static function isBlocked(): bool {
        $ip = Helpers::ip();
        $row = Database::fetch("SELECT * FROM ".DB_PREFIX."blocked_ips WHERE ip_address=?", [$ip]);
        if (!$row) return false;
        if ($row['permanent']) return true;
        if (!empty($row['blocked_until']) && strtotime($row['blocked_until']) > time()) return true;
        return false;
    }

    public static function blockIp(string $ip, string $reason = '', bool $permanent = false, int $minutes = 60): void {
        $until = $permanent ? null : date('Y-m-d H:i:s', time() + $minutes * 60);
        $exist = Database::fetch("SELECT id FROM ".DB_PREFIX."blocked_ips WHERE ip_address=?", [$ip]);
        if ($exist) {
            Database::update('blocked_ips', ['reason'=>$reason,'permanent'=>$permanent,'blocked_until'=>$until], 'ip_address=?', [$ip]);
        } else {
            Database::insert('blocked_ips', ['ip_address'=>$ip,'reason'=>$reason,'permanent'=>(int)$permanent,'blocked_until'=>$until]);
        }
    }

    public static function xss(string $str): string {
        return htmlspecialchars($str, ENT_QUOTES|ENT_HTML5, 'UTF-8');
    }

    public static function validatePassword(string $pass): array {
        $errors = [];
        if (mb_strlen($pass) < 8) $errors[] = 'كلمة المرور قصيرة (8 أحرف على الأقل)';
        if (!preg_match('/[A-Z]/', $pass)) $errors[] = 'يجب أن تحتوي على حرف كبير';
        if (!preg_match('/[0-9]/', $pass)) $errors[] = 'يجب أن تحتوي على رقم';
        return $errors;
    }

    public static function validateEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function generateToken(int $length = 32): string {
        return bin2hex(random_bytes($length));
    }

    public static function sanitizeFilename(string $name): string {
        return preg_replace('/[^a-zA-Z0-9\-_\.]/', '', $name);
    }
}
