<?php
class Setting {
    private static array $all = [];

    public static function get(string $key, $default = ''): string {
        if (empty(self::$all)) self::loadAll();
        return self::$all[$key] ?? $default;
    }

    public static function set(string $key, $value): void {
        $p = DB_PREFIX;
        Database::query("INSERT INTO `{$p}settings` (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=?", [$key,$value,$value]);
        self::$all[$key] = $value;
        Cache::del('settings_all');
    }

    public static function setMany(array $data): void {
        foreach ($data as $k => $v) self::set($k, $v);
    }

    public static function all(): array {
        if (empty(self::$all)) self::loadAll();
        return self::$all;
    }

    private static function loadAll(): void {
        $rows = Database::fetchAll("SELECT `key`,`value` FROM ".DB_PREFIX."settings");
        foreach ($rows as $r) self::$all[$r['key']] = $r['value'];
    }

    // شorthands
    public static function storeName(string $lang='ar'): string { return self::get("store_name_$lang", 'ووبيكس'); }
    public static function currency(): string { return self::get('currency','SAR'); }
    public static function currencySymbol(): string { return self::get('currency_symbol','ر.س'); }
    public static function maintenanceMode(): bool { return self::get('maintenance_mode','0') === '1'; }
    public static function defaultLang(): string { return self::get('default_lang','ar'); }
    public static function tickerEnabled(): bool { return self::get('ticker_enabled','0') === '1'; }
    public static function popupEnabled(): bool { return self::get('popup_enabled','0') === '1'; }
    public static function walletEnabled(): bool { return self::get('wallet_enabled','0') === '1'; }
    public static function pointsEnabled(): bool { return self::get('points_enabled','0') === '1'; }
    public static function referralEnabled(): bool { return self::get('referral_enabled','0') === '1'; }
}
