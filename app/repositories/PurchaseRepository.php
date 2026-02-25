<?php

class PurchaseRepository
{
    public function create(array $data): int
    {
        $stmt = db()->prepare('INSERT INTO purchases (user_id, request_id, country_id, application_id, number, status, purchase_type, rental_hours, rental_end_at, cost) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['user_id'],
            $data['request_id'],
            $data['country_id'],
            $data['application_id'],
            $data['number'],
            $data['status'],
            $data['purchase_type'] ?? 'buy',
            $data['rental_hours'] ?? null,
            $data['rental_end_at'] ?? null,
            $data['cost'],
        ]);
        return (int)db()->lastInsertId();
    }

    public function allByUser(int $userId): array
    {
        $stmt = db()->prepare('SELECT * FROM purchases WHERE user_id = ? ORDER BY id DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM purchases WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $purchase = $stmt->fetch();
        return $purchase ?: null;
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = db()->prepare('UPDATE purchases SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
    }
}
