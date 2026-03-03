<?php
class Coupon {
    public static function validate(string $code, float $total, ?int $userId = null): array {
        $c = Database::fetch("SELECT * FROM ".DB_PREFIX."coupons WHERE code=? AND status=1", [strtoupper($code)]);
        if (!$c) return ['valid'=>false,'msg'=>'الكوبون غير موجود'];
        if ($c['max_uses'] > 0 && $c['used_count'] >= $c['max_uses']) return ['valid'=>false,'msg'=>'تم استنفاد الكوبون'];
        if ($c['starts_at'] && strtotime($c['starts_at']) > time()) return ['valid'=>false,'msg'=>'الكوبون لم يبدأ بعد'];
        if ($c['expires_at'] && strtotime($c['expires_at']) < time()) return ['valid'=>false,'msg'=>'انتهت صلاحية الكوبون'];
        if ($total < $c['min_order']) return ['valid'=>false,'msg'=>"الحد الأدنى للطلب {$c['min_order']}"];
        $discount = $c['type']==='percent' ? $total*$c['value']/100 : $c['value'];
        if ($c['max_discount'] > 0) $discount = min($discount,$c['max_discount']);
        return ['valid'=>true,'coupon'=>$c,'discount'=>round($discount,2)];
    }

    public static function use(int $id): void {
        Database::query("UPDATE ".DB_PREFIX."coupons SET used_count=used_count+1 WHERE id=?", [$id]);
    }

    public static function all(int $page=1, int $pp=20): array {
        $offset=($page-1)*$pp;
        return ['items'=>Database::fetchAll("SELECT * FROM ".DB_PREFIX."coupons ORDER BY created_at DESC LIMIT $pp OFFSET $offset"),'total'=>Database::count('coupons')];
    }

    public static function create(array $d): int { return Database::insert('coupons', $d); }
    public static function update(int $id, array $d): void { Database::update('coupons', $d, 'id=?', [$id]); }
    public static function delete(int $id): void { Database::delete('coupons', 'id=?', [$id]); }
}
