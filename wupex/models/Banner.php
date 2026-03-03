<?php
class Banner {
    public static function active(string $position='hero'): array {
        $now=date('Y-m-d H:i:s');
        return Cache::remember("banners_$position", fn() =>
            Database::fetchAll("SELECT * FROM ".DB_PREFIX."banners WHERE status=1 AND position=? AND (starts_at IS NULL OR starts_at<=?) AND (ends_at IS NULL OR ends_at>=?) ORDER BY sort_order ASC", [$position,$now,$now])
        , 300);
    }

    public static function all(): array { return Database::fetchAll("SELECT * FROM ".DB_PREFIX."banners ORDER BY sort_order ASC"); }
    public static function create(array $d): int { Cache::del('banners_hero'); return Database::insert('banners',$d); }
    public static function update(int $id, array $d): void { Cache::flush(); Database::update('banners',$d,'id=?',[$id]); }
    public static function delete(int $id): void { Cache::flush(); Database::delete('banners','id=?',[$id]); }
}
