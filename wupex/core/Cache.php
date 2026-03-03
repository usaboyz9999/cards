<?php
class Cache {
    private static string $dir;

    public static function init(): void {
        self::$dir = STORAGE_PATH . '/cache/';
        if (!is_dir(self::$dir)) mkdir(self::$dir, 0755, true);
    }

    public static function get(string $key) {
        if (!CACHE_ENABLED) return null;
        $file = self::$dir . md5($key) . '.cache';
        if (!file_exists($file)) return null;
        $data = unserialize(file_get_contents($file));
        if ($data['expires'] < time()) { unlink($file); return null; }
        return $data['value'];
    }

    public static function set(string $key, $value, int $ttl = CACHE_TTL): void {
        if (!CACHE_ENABLED) return;
        if (!isset(self::$dir)) self::init();
        $file = self::$dir . md5($key) . '.cache';
        file_put_contents($file, serialize(['value'=>$value,'expires'=>time()+$ttl]));
    }

    public static function del(string $key): void {
        $file = self::$dir . md5($key) . '.cache';
        if (file_exists($file)) unlink($file);
    }

    public static function flush(): void {
        if (!isset(self::$dir)) self::init();
        foreach (glob(self::$dir . '*.cache') as $f) unlink($f);
    }

    public static function remember(string $key, callable $cb, int $ttl = CACHE_TTL) {
        $val = self::get($key);
        if ($val !== null) return $val;
        $val = $cb();
        self::set($key, $val, $ttl);
        return $val;
    }
}
