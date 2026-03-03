<?php
class Lang {
    private static array $strings = [];
    private static string $lang = 'ar';

    public static function load(string $lang): void {
        self::$lang = in_array($lang,['ar','en']) ? $lang : 'ar';
        $file = LANG_PATH . "/{$lang}.php";
        if (file_exists($file)) {
            self::$strings = require $file;
        }
    }

    public static function get(string $key, ?string $lang = null): string {
        $l = $lang ?? self::$lang;
        if ($l !== self::$lang) {
            $file = LANG_PATH . "/{$l}.php";
            $other = file_exists($file) ? require $file : [];
            return $other[$key] ?? $key;
        }
        return self::$strings[$key] ?? $key;
    }

    public static function isRtl(): bool { return self::$lang === 'ar'; }
    public static function current(): string { return self::$lang; }
    public static function dir(): string { return self::isRtl() ? 'rtl' : 'ltr'; }
}
