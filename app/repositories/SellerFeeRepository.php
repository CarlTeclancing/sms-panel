<?php

class SellerFeeRepository
{
    public function hasPaid(int $userId): bool
    {
        $stmt = db()->prepare('SELECT id FROM seller_fees WHERE user_id = ? LIMIT 1');
        $stmt->execute([$userId]);
        return (bool)$stmt->fetch();
    }

    public function create(int $userId, float $amount, string $ref): int
    {
        $stmt = db()->prepare('INSERT INTO seller_fees (user_id, amount, ref, paid_at) VALUES (?, ?, ?, NOW())');
        $stmt->execute([$userId, $amount, $ref]);
        return (int)db()->lastInsertId();
    }

    public function totalCollected(): float
    {
        $stmt = db()->query('SELECT SUM(amount) AS total FROM seller_fees');
        $row = $stmt->fetch();
        return (float)($row['total'] ?? 0);
    }
}
