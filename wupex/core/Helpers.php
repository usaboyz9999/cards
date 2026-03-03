<?php
class Helpers {
    public static function setting(string $key, $default = ''): string {
        static $cache = [];
        if (!isset($cache[$key])) {
            $row = Database::fetch("SELECT value FROM ".DB_PREFIX."settings WHERE `key`=?", [$key]);
            $cache[$key] = $row ? $row['value'] : $default;
        }
        return $cache[$key];
    }

    public static function settings(array $keys): array {
        $out = [];
        foreach ($keys as $k) $out[$k] = self::setting($k);
        return $out;
    }

    public static function t(string $key, ?string $lang = null): string {
        return Lang::get($key, $lang);
    }

    public static function ip(): string {
        foreach (['HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR','HTTP_CLIENT_IP','REMOTE_ADDR'] as $h) {
            if (!empty($_SERVER[$h])) {
                return explode(',', $_SERVER[$h])[0];
            }
        }
        return '0.0.0.0';
    }

    public static function slug(string $text): string {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[\s\-]+/', '-', $text);
        $text = preg_replace('/[^\p{L}\p{N}\-]/u', '', $text);
        return trim($text, '-') ?: md5($text);
    }

    public static function money(float $amount, ?string $symbol = null): string {
        $s = $symbol ?? self::setting('currency_symbol', 'ر.س');
        return $s . number_format($amount, 2);
    }

    public static function timeAgo(string $datetime, string $lang = 'ar'): string {
        $diff = time() - strtotime($datetime);
        if ($lang === 'ar') {
            if ($diff < 60) return 'منذ لحظات';
            if ($diff < 3600) return 'منذ ' . floor($diff/60) . ' دقيقة';
            if ($diff < 86400) return 'منذ ' . floor($diff/3600) . ' ساعة';
            if ($diff < 2592000) return 'منذ ' . floor($diff/86400) . ' يوم';
            return date('Y-m-d', strtotime($datetime));
        } else {
            if ($diff < 60) return 'just now';
            if ($diff < 3600) return floor($diff/60) . ' min ago';
            if ($diff < 86400) return floor($diff/3600) . ' hr ago';
            return date('Y-m-d', strtotime($datetime));
        }
    }

    public static function redirect(string $url): void {
        header("Location: $url"); exit;
    }

    public static function sanitize(string $str): string {
        return htmlspecialchars(strip_tags(trim($str)), ENT_QUOTES, 'UTF-8');
    }

    public static function randomCode(int $len = 16): string {
        return strtoupper(bin2hex(random_bytes($len / 2)));
    }

    public static function orderNumber(): string {
        return 'WX' . date('Ymd') . strtoupper(Helpers::randomCode(6));
    }

    public static function ticketNumber(): string {
        return 'TK' . date('ymd') . rand(100,999);
    }

    public static function depositNumber(): string {
        return 'DP' . date('YmdHis') . rand(10,99);
    }

    public static function uploadImage(array $file, string $dir = 'products'): ?string {
        $path = UPLOADS_PATH . "/$dir/";
        if (!is_dir($path)) mkdir($path, 0755, true);
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_IMG)) return null;
        if ($file['size'] > MAX_UPLOAD) return null;
        $name = uniqid('img_', true) . '.' . $ext;
        if (move_uploaded_file($file['tmp_name'], $path . $name)) {
            return "uploads/$dir/$name";
        }
        return null;
    }

    public static function deleteFile(string $path): void {
        $full = SITE_PATH . '/' . $path;
        if (file_exists($full) && strpos($full, UPLOADS_PATH) !== false) {
            unlink($full);
        }
    }

    public static function siteUrl(string $path = ''): string {
        return rtrim(SITE_URL, '/') . '/' . ltrim($path, '/');
    }

    public static function assetUrl(string $path): string {
        return self::siteUrl("assets/$path");
    }

    public static function imageUrl(string $path, string $placeholder = 'placeholders/product.svg'): string {
        if (empty($path)) return self::siteUrl("images/$placeholder");
        if (str_starts_with($path, 'http')) return $path;
        return self::siteUrl($path);
    }

    public static function json(array $data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function isAjax(): bool {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }

    public static function postInt(string $k, int $default = 0): int { return (int)($_POST[$k] ?? $default); }
    public static function postStr(string $k, string $default = ''): string { return self::sanitize($_POST[$k] ?? $default); }
    public static function getStr(string $k, string $default = ''): string { return self::sanitize($_GET[$k] ?? $default); }
    public static function getInt(string $k, int $default = 0): int { return (int)($_GET[$k] ?? $default); }

    /**
     * تحويل الأرقام العربية-الهندية إلى أرقام غربية (لاتينية)
     * Convert Arabic-Indic numerals to Western/Latin numerals
     */
    public static function toWesternNums(string $str): string {
        $ar = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
        $en = ['0','1','2','3','4','5','6','7','8','9'];
        return str_replace($ar, $en, $str);
    }

    public static function numFormat(float $num, int $decimals = 2): string {
        return self::toWesternNums(number_format($num, $decimals));
    }
}
