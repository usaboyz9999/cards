<?php
class Wallet {
    public static function balance(int $userId): float {
        $row = Database::fetch("SELECT wallet_balance FROM ".DB_PREFIX."users WHERE id=?", [$userId]);
        return (float)($row['wallet_balance'] ?? 0);
    }

    public static function credit(int $userId, float $amount, string $type, string $descAr, string $descEn, ?string $ref = null): bool {
        if ($amount <= 0) return false;
        $before = self::balance($userId);
        $after  = $before + $amount;
        Database::update('users', ['wallet_balance'=>$after], 'id=?', [$userId]);
        Database::insert('wallet_transactions', ['user_id'=>$userId,'type'=>$type,'amount'=>$amount,'balance_before'=>$before,'balance_after'=>$after,'reference'=>$ref,'description_ar'=>$descAr,'description_en'=>$descEn,'status'=>'completed']);
        return true;
    }

    public static function debit(int $userId, float $amount, string $type, string $descAr, string $descEn, ?string $ref = null): bool {
        if ($amount <= 0) return false;
        $before = self::balance($userId);
        if ($before < $amount) return false;
        $after = $before - $amount;
        Database::update('users', ['wallet_balance'=>$after], 'id=?', [$userId]);
        Database::insert('wallet_transactions', ['user_id'=>$userId,'type'=>$type,'amount'=>-$amount,'balance_before'=>$before,'balance_after'=>$after,'reference'=>$ref,'description_ar'=>$descAr,'description_en'=>$descEn,'status'=>'completed']);
        return true;
    }

    public static function transactions(int $userId, int $limit = 20, int $offset = 0): array {
        return Database::fetchAll("SELECT * FROM ".DB_PREFIX."wallet_transactions WHERE user_id=? ORDER BY created_at DESC LIMIT $limit OFFSET $offset", [$userId]);
    }

    public static function createDeposit(int $userId, float $amount, string $method, ?string $proof = null, ?string $notes = null): int {
        $bonus = 0;
        $bonusPct = (float)Setting::get('wallet_bonus', '0');
        if ($bonusPct > 0) $bonus = round($amount * $bonusPct / 100, 2);
        return Database::insert('deposit_requests', [
            'request_number' => Helpers::depositNumber(),
            'user_id'=>$userId,'amount'=>$amount,'bonus'=>$bonus,
            'method'=>$method,'proof_image'=>$proof,'notes'=>$notes,
        ]);
    }
}
