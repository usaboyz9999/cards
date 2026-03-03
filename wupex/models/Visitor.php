<?php
class Visitor {
    public static function track(): void {
        static $tracked = false;
        if ($tracked) return;
        $tracked = true;
        if (Auth::isAdmin()) return;
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        if (str_contains($uri,'action=')) return;

        $ip = Helpers::ip();
        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);
        $isHome = !str_contains($uri,'page=') || str_contains($uri,'page=home');

        try {
            // ── سجل كل الصفحات (للتبويب الثاني) ──
            Database::insert('visitors', [
                'ip_address' => $ip,
                'page_url'   => substr($uri, 0, 255),
                'referer'    => substr($_SERVER['HTTP_REFERER'] ?? '', 0, 255),
                'user_agent' => $ua,
                'user_id'    => Auth::id(),
                'is_home'    => $isHome ? 1 : 0,
            ]);

            // ── الصفحة الرئيسية فقط: زيارة واحدة لكل IP في اليوم ──
            if ($isHome) {
                $today = date('Y-m-d');
                $exists = Database::fetch(
                    "SELECT id FROM ".DB_PREFIX."visitor_days WHERE ip_address=? AND visit_date=?",
                    [$ip, $today]
                );
                if (!$exists) {
                    Database::insert('visitor_days', [
                        'ip_address'  => $ip,
                        'visit_date'  => $today,
                        'user_agent'  => $ua,
                        'user_id'     => Auth::id(),
                    ]);
                }
            }
        } catch(\Exception $e) {}
    }

    public static function todayCount(): int {
        try {
            return (int)Database::count('visitor_days', "visit_date=CURDATE()");
        } catch(\Exception $e) {
            return (int)Database::count('visitors', "DATE(visited_at)=CURDATE() AND is_home=1");
        }
    }

    public static function monthCount(): int {
        try {
            return (int)Database::count('visitor_days', "MONTH(visit_date)=MONTH(NOW()) AND YEAR(visit_date)=YEAR(NOW())");
        } catch(\Exception $e) {
            return (int)Database::count('visitors', "MONTH(visited_at)=MONTH(NOW()) AND YEAR(visited_at)=YEAR(NOW()) AND is_home=1");
        }
    }
}
