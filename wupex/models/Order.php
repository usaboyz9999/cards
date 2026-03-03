<?php
class Order {
    public static function create(array $data, array $items): int {
        $orderId = Database::insert('orders', array_merge($data, ['order_number'=>Helpers::orderNumber()]));
        foreach ($items as $item) {
            Database::insert('order_items', array_merge($item, ['order_id'=>$orderId]));
            // خصم الكود
            if (!empty($item['code_id'])) {
                Database::update('codes', ['status'=>'sold','order_id'=>$orderId,'sold_at'=>date('Y-m-d H:i:s')], 'id=?', [$item['code_id']]);
            }
            // تحديث المبيعات
            Database::query("UPDATE ".DB_PREFIX."products SET sales_count=sales_count+? WHERE id=?", [$item['quantity'],$item['product_id']]);
        }
        // نقاط
        if (Setting::pointsEnabled() && !empty($data['user_id'])) {
            $pts = (int)floor($data['total'] * (float)Setting::get('points_per_sar','1'));
            if ($pts > 0) {
                Database::query("UPDATE ".DB_PREFIX."users SET points=points+? WHERE id=?", [$pts,$data['user_id']]);
                Database::insert('points_transactions', ['user_id'=>$data['user_id'],'type'=>'earn','points'=>$pts,'reference'=>"order_$orderId",'description_ar'=>'نقاط الطلب','description_en'=>'Order points']);
            }
        }
        ActivityLog::log('order_created','order',$orderId);
        Notification::create($data['user_id']??null,'طلب جديد','New Order',"تم استلام طلبك #$orderId",'Order #'.$orderId.' received','📦','#10b981');
        return $orderId;
    }

    public static function find(int $id): ?array {
        return Database::fetch("SELECT o.*, u.name as user_name, u.email as user_email FROM ".DB_PREFIX."orders o LEFT JOIN ".DB_PREFIX."users u ON u.id=o.user_id WHERE o.id=?", [$id]);
    }

    public static function findByNumber(string $num): ?array {
        return Database::fetch("SELECT * FROM ".DB_PREFIX."orders WHERE order_number=?", [$num]);
    }

    public static function items(int $orderId): array {
        return Database::fetchAll("SELECT oi.*, p.icon, p.color1, p.color2 FROM ".DB_PREFIX."order_items oi LEFT JOIN ".DB_PREFIX."products p ON p.id=oi.product_id WHERE oi.order_id=?", [$orderId]);
    }

    public static function userOrders(int $userId, int $limit=20, int $offset=0): array {
        return Database::fetchAll("SELECT * FROM ".DB_PREFIX."orders WHERE user_id=? ORDER BY created_at DESC LIMIT $limit OFFSET $offset", [$userId]);
    }

    public static function updateStatus(int $id, string $status): void {
        Database::update('orders', ['status'=>$status,'updated_at'=>date('Y-m-d H:i:s')], 'id=?', [$id]);
        ActivityLog::log('order_status_changed','order',$id,"Status: $status");
    }

    public static function adminAll(array $f = [], int $page=1, int $pp=20): array {
        $where=['1']; $params=[];
        if (!empty($f['status'])) { $where[]='o.status=?'; $params[]=$f['status']; }
        if (!empty($f['search'])) { $where[]='(o.order_number LIKE ? OR u.email LIKE ?)'; $params[]="%{$f['search']}%"; $params[]="%{$f['search']}%"; }
        $cond=implode(' AND ',$where);
        $offset=($page-1)*$pp;
        $sql="SELECT o.*,u.name as uname,u.email as uemail FROM ".DB_PREFIX."orders o LEFT JOIN ".DB_PREFIX."users u ON u.id=o.user_id WHERE $cond ORDER BY o.created_at DESC LIMIT $pp OFFSET $offset";
        return ['items'=>Database::fetchAll($sql,$params),'total'=>Database::fetch("SELECT COUNT(*) as c FROM ".DB_PREFIX."orders o LEFT JOIN ".DB_PREFIX."users u ON u.id=o.user_id WHERE $cond",$params)['c']??0];
    }
}
