<?php
class Cart {
    private static function sid(): string { return session_id(); }

    public static function items(?int $userId = null): array {
        $where = $userId ? 'user_id=?' : 'session_id=?';
        $param = $userId ?? self::sid();
        $lang = defined("APP_LANG") ? APP_LANG : "ar";
        return Database::fetchAll("SELECT c.*, p.name_ar, p.name_en, p.icon, p.color1, p.color2, p.image, p.stock, COALESCE(pp.label_ar, pp.label_en, '') as price_label FROM ".DB_PREFIX."carts c JOIN ".DB_PREFIX."products p ON p.id=c.product_id LEFT JOIN ".DB_PREFIX."product_prices pp ON pp.id=c.price_id WHERE $where", [$param]);
    }

    public static function count(?int $userId = null): int {
        $where = $userId ? 'user_id=?' : 'session_id=?';
        $param = $userId ?? self::sid();
        return Database::count('carts', $where, [$param]);
    }

    public static function total(?int $userId = null): float {
        $where = $userId ? 'user_id=?' : 'session_id=?';
        $param = $userId ?? self::sid();
        $row = Database::fetch("SELECT SUM(price*quantity) as t FROM ".DB_PREFIX."carts WHERE $where", [$param]);
        return (float)($row['t'] ?? 0);
    }

    public static function add(int $productId, int $priceId = 0, float $price = 0, int $qty = 1, ?int $userId = null): void {
        $sid = self::sid();
        $exist = Database::fetch("SELECT id,quantity FROM ".DB_PREFIX."carts WHERE session_id=? AND product_id=? AND price_id=?", [$sid,$productId,$priceId]);
        if ($exist) {
            Database::update('carts', ['quantity'=>$exist['quantity']+$qty], 'id=?', [$exist['id']]);
        } else {
            Database::insert('carts', ['session_id'=>$sid,'user_id'=>$userId,'product_id'=>$productId,'price_id'=>$priceId,'quantity'=>$qty,'price'=>$price]);
        }
    }

    public static function remove(int $cartId): void { Database::delete('carts', 'id=?', [$cartId]); }
    public static function clear(?int $userId = null): void {
        $where = $userId ? 'user_id=?' : 'session_id=?';
        Database::delete('carts', $where, [$userId ?? self::sid()]);
    }
    public static function updateQty(int $cartId, int $qty): void { Database::update('carts', ['quantity'=>$qty], 'id=?', [$cartId]); }
}
