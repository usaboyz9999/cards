<?php
class Category {
    public static function all(bool $onlyActive = true): array {
        $where = $onlyActive ? 'WHERE status=1' : '';
        return Cache::remember("cats_all_$onlyActive", fn() =>
            Database::fetchAll("SELECT * FROM ".DB_PREFIX."categories $where ORDER BY sort_order ASC")
        , 600);
    }

    public static function find(int $id): ?array {
        return Database::fetch("SELECT * FROM ".DB_PREFIX."categories WHERE id=?", [$id]);
    }

    public static function findBySlug(string $slug): ?array {
        return Database::fetch("SELECT * FROM ".DB_PREFIX."categories WHERE slug=?", [$slug]);
    }

    public static function withCount(): array {
        return Cache::remember('cats_count', fn() =>
            Database::fetchAll("SELECT c.*, COUNT(p.id) as products_count FROM ".DB_PREFIX."categories c LEFT JOIN ".DB_PREFIX."products p ON p.category_id=c.id AND p.status=1 WHERE c.status=1 GROUP BY c.id ORDER BY c.sort_order ASC")
        , 600);
    }

    public static function name(array $cat, string $lang = 'ar'): string {
        return $cat["name_$lang"] ?? $cat['name_ar'] ?? '';
    }

    public static function create(array $d): int {
        Cache::del('cats_all_1'); Cache::del('cats_count');
        return Database::insert('categories', $d);
    }

    public static function update(int $id, array $d): void {
        Cache::del('cats_all_1'); Cache::del('cats_count');
        Database::update('categories', $d, 'id=?', [$id]);
    }

    public static function delete(int $id): void {
        Cache::del('cats_all_1'); Cache::del('cats_count');
        Database::delete('categories', 'id=?', [$id]);
    }
}
