<?php
class Review {
    public static function product(int $productId, string $status='approved', int $limit=10): array {
        return Database::fetchAll("SELECT r.*,u.name,u.avatar FROM ".DB_PREFIX."reviews r JOIN ".DB_PREFIX."users u ON u.id=r.user_id WHERE r.product_id=? AND r.status=? ORDER BY r.created_at DESC LIMIT $limit", [$productId,$status]);
    }

    public static function canReview(int $userId, int $productId): bool {
        $ordered = Database::exists('order_items oi JOIN '.DB_PREFIX.'orders o ON o.id=oi.order_id', "oi.product_id=? AND o.user_id=? AND o.status='completed'", [$productId,$userId]);
        $reviewed = Database::exists('reviews', 'product_id=? AND user_id=?', [$productId,$userId]);
        return $ordered && !$reviewed;
    }

    public static function create(array $d): int {
        $id = Database::insert('reviews', $d);
        self::updateProductRating($d['product_id']);
        return $id;
    }

    public static function updateProductRating(int $productId): void {
        $row = Database::fetch("SELECT AVG(rating) as avg, COUNT(*) as cnt FROM ".DB_PREFIX."reviews WHERE product_id=? AND status='approved'", [$productId]);
        Database::update('products', ['rating'=>round($row['avg'],2),'reviews_count'=>$row['cnt']], 'id=?', [$productId]);
    }

    public static function adminAll(string $status='pending', int $page=1): array {
        $offset=($page-1)*20;
        $items=Database::fetchAll("SELECT r.*,u.name,p.name_ar as pname FROM ".DB_PREFIX."reviews r JOIN ".DB_PREFIX."users u ON u.id=r.user_id JOIN ".DB_PREFIX."products p ON p.id=r.product_id WHERE r.status=? ORDER BY r.created_at DESC LIMIT 20 OFFSET $offset",[$status]);
        return ['items'=>$items,'total'=>Database::count('reviews',"status=?",[$status])];
    }

    public static function update(int $id, array $d): void { Database::update('reviews', $d, 'id=?', [$id]); }
    public static function delete(int $id): void { Database::delete('reviews', 'id=?', [$id]); }
}
