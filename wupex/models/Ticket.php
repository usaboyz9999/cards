<?php
class Ticket {
    public static function create(array $d, string $firstMsg): int {
        $id = Database::insert('tickets', array_merge($d, ['ticket_number'=>Helpers::ticketNumber()]));
        Database::insert('ticket_replies', ['ticket_id'=>$id,'user_id'=>$d['user_id'],'message'=>$firstMsg,'is_admin'=>0]);
        Notification::create(null,'تذكرة جديدة','New Ticket',"تذكرة جديدة من المستخدم",'New ticket received','🎫','#f59e0b');
        ActivityLog::log('ticket_created','ticket',$id);
        return $id;
    }

    public static function replies(int $ticketId): array {
        return Database::fetchAll("SELECT r.*,u.name,u.role FROM ".DB_PREFIX."ticket_replies r JOIN ".DB_PREFIX."users u ON u.id=r.user_id WHERE r.ticket_id=? ORDER BY r.created_at ASC", [$ticketId]);
    }

    public static function addReply(int $ticketId, int $userId, string $msg, bool $isAdmin=false): void {
        Database::insert('ticket_replies', ['ticket_id'=>$ticketId,'user_id'=>$userId,'message'=>$msg,'is_admin'=>(int)$isAdmin]);
        $status = $isAdmin ? 'in_progress' : 'waiting';
        Database::update('tickets', ['status'=>$status,'updated_at'=>date('Y-m-d H:i:s')], 'id=?', [$ticketId]);
    }

    public static function userTickets(int $userId): array {
        return Database::fetchAll("SELECT * FROM ".DB_PREFIX."tickets WHERE user_id=? ORDER BY created_at DESC", [$userId]);
    }

    public static function adminAll(string $status='open', int $page=1): array {
        $offset=($page-1)*20;
        $items=Database::fetchAll("SELECT t.*,u.name FROM ".DB_PREFIX."tickets t JOIN ".DB_PREFIX."users u ON u.id=t.user_id WHERE t.status=? ORDER BY t.updated_at DESC LIMIT 20 OFFSET $offset",[$status]);
        return ['items'=>$items,'total'=>Database::count('tickets',"status=?",[$status])];
    }

    public static function update(int $id, array $d): void { Database::update('tickets', $d, 'id=?', [$id]); }
}
