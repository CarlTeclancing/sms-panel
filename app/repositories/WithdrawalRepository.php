<?php

class WithdrawalRepository
{
    public function create(array $data): int
    {
        $stmt = db()->prepare('INSERT INTO withdrawal_requests (user_id, amount, fee, net_amount, status, note) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['user_id'],
            $data['amount'],
            $data['fee'],
            $data['net_amount'],
            $data['status'] ?? 'pending',
            $data['note'] ?? null,
        ]);
        return (int)db()->lastInsertId();
    }

    public function allByUser(int $userId): array
    {
        $stmt = db()->prepare('SELECT * FROM withdrawal_requests WHERE user_id = ? ORDER BY id DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function allPending(): array
    {
        $stmt = db()->query("SELECT * FROM withdrawal_requests WHERE status = 'pending' ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM withdrawal_requests WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updateStatus(int $id, string $status, ?string $note = null): void
    {
        $stmt = db()->prepare('UPDATE withdrawal_requests SET status = ?, note = ?, processed_at = NOW() WHERE id = ?');
        $stmt->execute([$status, $note, $id]);
    }

    public function pendingTotalNet(): float
    {
        $stmt = db()->query("SELECT SUM(net_amount) AS total FROM withdrawal_requests WHERE status = 'pending'");
        $row = $stmt->fetch();
        return (float)($row['total'] ?? 0);
    }

    public function dailyTotals(int $days = 7): array
    {
        $stmt = db()->prepare('SELECT DATE(created_at) AS day, SUM(net_amount) AS total FROM withdrawal_requests WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY) GROUP BY DATE(created_at) ORDER BY day ASC');
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }
}
