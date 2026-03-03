<?php
class Code {
    public static function available(int $productId, ?int $priceId=null): ?array {
        $where='product_id=? AND status=\'available\'';
        $params=[$productId];
        if ($priceId) { $where.=' AND price_id=?'; $params[]=$priceId; }
        return Database::fetch("SELECT * FROM ".DB_PREFIX."codes WHERE $where LIMIT 1", $params);
    }

    public static function availableCount(int $productId, ?int $priceId=null): int {
        $where='product_id=? AND status=\'available\'';
        $params=[$productId];
        if ($priceId) { $where.=' AND price_id=?'; $params[]=$priceId; }
        return Database::count('codes',$where,$params);
    }

    public static function import(int $productId, array $codes, ?int $priceId=null): int {
        $count=0;
        foreach ($codes as $code) {
            $code=trim($code);
            if (!$code) continue;
            Database::insert('codes',['product_id'=>$productId,'price_id'=>$priceId,'code'=>$code]);
            $count++;
        }
        return $count;
    }

    public static function productCodes(int $productId, int $page=1): array {
        $offset=($page-1)*50;
        return ['items'=>Database::fetchAll("SELECT * FROM ".DB_PREFIX."codes WHERE product_id=? ORDER BY created_at DESC LIMIT 50 OFFSET $offset",[$productId]),'total'=>Database::count('codes','product_id=?',[$productId])];
    }
}
