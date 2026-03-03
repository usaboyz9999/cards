<?php
class Product {
    public static function all(array $filters = [], int $page = 1, int $perPage = 24): array {
        $where = ['p.status=1'];
        $params = [];
        if (!empty($filters['category_id'])) { $where[] = 'p.category_id=?'; $params[] = $filters['category_id']; }
        if (!empty($filters['search'])) { $where[] = '(p.name_ar LIKE ? OR p.name_en LIKE ?)'; $params[] = "%{$filters['search']}%"; $params[] = "%{$filters['search']}%"; }
        if (!empty($filters['featured'])) { $where[] = 'p.featured=1'; }
        if (isset($filters['badge']) && $filters['badge']) { $where[] = 'p.badge=?'; $params[] = $filters['badge']; }
        if (isset($filters['in_stock'])) { $where[] = 'p.stock=1'; }
        $orderBy = match($filters['sort'] ?? 'default') {
            'price_low'  => 'p.price ASC',
            'price_high' => 'p.price DESC',
            'name'       => 'p.name_ar ASC',
            'newest'     => 'p.created_at DESC',
            'popular'    => 'p.sales_count DESC',
            default      => 'p.featured DESC, p.sort_order ASC'
        };
        $cond = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT p.*, c.name_ar as cat_name_ar, c.name_en as cat_name_en, c.icon as cat_icon FROM ".DB_PREFIX."products p LEFT JOIN ".DB_PREFIX."categories c ON c.id=p.category_id WHERE $cond ORDER BY $orderBy LIMIT $perPage OFFSET $offset";
        $total = Database::count('products p', $cond, $params);
        return ['items'=>Database::fetchAll($sql, $params),'total'=>$total,'pages'=>ceil($total/$perPage),'page'=>$page];
    }

    public static function find(int $id): ?array {
        return Database::fetch("SELECT p.*, c.name_ar as cat_name_ar, c.name_en as cat_name_en FROM ".DB_PREFIX."products p LEFT JOIN ".DB_PREFIX."categories c ON c.id=p.category_id WHERE p.id=?", [$id]);
    }

    public static function findBySlug(string $slug): ?array {
        return Database::fetch("SELECT p.*, c.name_ar as cat_name_ar, c.name_en as cat_name_en FROM ".DB_PREFIX."products p LEFT JOIN ".DB_PREFIX."categories c ON c.id=p.category_id WHERE p.slug=? AND p.status=1", [$slug]);
    }

    public static function prices(int $productId): array {
        return Database::fetchAll("SELECT * FROM ".DB_PREFIX."product_prices WHERE product_id=? ORDER BY sort_order ASC", [$productId]);
    }

    public static function featured(int $limit = 12): array {
        return Database::fetchAll("SELECT * FROM ".DB_PREFIX."products WHERE featured=1 AND status=1 ORDER BY sort_order ASC LIMIT ?", [$limit]);
    }

    public static function related(int $catId, int $excludeId, int $limit = 6): array {
        return Database::fetchAll("SELECT * FROM ".DB_PREFIX."products WHERE category_id=? AND id!=? AND status=1 ORDER BY RAND() LIMIT ?", [$catId,$excludeId,$limit]);
    }

    public static function incrementView(int $id): void {
        Database::query("UPDATE ".DB_PREFIX."products SET views_count=views_count+1 WHERE id=?", [$id]);
    }

    public static function name(array $p, string $lang = 'ar'): string {
        return $p["name_$lang"] ?? $p['name_ar'] ?? '';
    }

    public static function create(array $d): int { return Database::insert('products', $d); }
    public static function update(int $id, array $d): void { Database::update('products', $d, 'id=?', [$id]); }
    public static function delete(int $id): void { Database::delete('products', 'id=?', [$id]); }
}
