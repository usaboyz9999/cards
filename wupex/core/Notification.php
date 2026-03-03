<?php
class Notification {
    public static function create(?int $userId, string $titleAr, string $titleEn, string $msgAr, string $msgEn, string $icon='🔔', string $color='#7c3aed', ?string $link=null, bool $broadcast=false): void {
        try {
            Database::insert('notifications', [
                'user_id'      => $userId,
                'title_ar'     => $titleAr,
                'title_en'     => $titleEn,
                'message_ar'   => $msgAr,
                'message_en'   => $msgEn,
                'icon'         => $icon,
                'color'        => $color,
                'link'         => $link,
                'is_broadcast' => (int)$broadcast,
                'is_read'      => 0,
            ]);
        } catch(\Exception $e) {}
    }

    public static function broadcast(string $titleAr, string $titleEn, string $msgAr, string $msgEn, string $icon='📢'): void {
        self::create(null, $titleAr, $titleEn, $msgAr, $msgEn, $icon, '#f97316', null, true);
    }

    public static function unreadCount(?int $userId): int {
        if (!$userId) return 0;
        return Database::count('notifications', "(user_id=? OR is_broadcast=1) AND is_read=0", [$userId]);
    }

    public static function markRead(?int $userId): void {
        if (!$userId) return;
        Database::query("UPDATE ".DB_PREFIX."notifications SET is_read=1 WHERE user_id=? OR is_broadcast=1", [$userId]);
    }
}
