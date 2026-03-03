<?php
class ActivityLog {
    public static function log(string $action, ?string $model = null, ?int $modelId = null, ?string $desc = null): void {
        try {
            Database::insert('activity_logs', [
                'user_id'    => Auth::id(),
                'action'     => $action,
                'model'      => $model,
                'model_id'   => $modelId,
                'description'=> $desc,
                'ip_address' => Helpers::ip(),
                'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            ]);
        } catch (Exception $e) { /* silent fail */ }
    }
}
