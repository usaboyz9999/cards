<?php
class Wishlist {
    public static function items(int $userId): array {
        return Database::fetchAll("SELECT w.*,p.name_ar,p.name_en,p.icon,p.price,p.color1,p.color2,p.image,p.slug FROM ".DB_PREFIX."wishlists w JOIN ".DB_PREFIX."products p ON p.id=w.product_id WHERE w.user_id=?", [$userId]);
    }
    public static function toggle(int $userId, int $productId): bool {
        if (Database::exists('wishlists','user_id=? AND product_id=?',[$userId,$productId])) {
            Database::delete('wishlists','user_id=? AND product_id=?',[$userId,$productId]); return false;
        } else {
            Database::insert('wishlists',['user_id'=>$userId,'product_id'=>$productId]); return true;
        }
    }
    public static function has(int $userId, int $productId): bool {
        return Database::exists('wishlists','user_id=? AND product_id=?',[$userId,$productId]);
    }
}
