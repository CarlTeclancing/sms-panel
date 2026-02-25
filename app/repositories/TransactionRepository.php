<?php

class TransactionRepository
{
    public function create(array $data): int
    {
        $stmt = db()->prepare('INSERT INTO transactions (user_id, type, amount, ref, provider, status, meta) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['user_id'],
            $data['type'],
            $data['amount'],
            $data['ref'],
            $data['provider'],
            $data['status'],
            $data['meta'] ?? null,
        ]);
        return (int)db()->lastInsertId();
    }

    public function allByUser(int $userId): array
    {
        $stmt = db()->prepare('SELECT * FROM transactions WHERE user_id = ? ORDER BY id DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function all(): array
    {
        $stmt = db()->query('SELECT * FROM transactions ORDER BY id DESC LIMIT 100');
        return $stmt->fetchAll();
    }

    public function findByRef(string $ref): ?array
    {
        $stmt = db()->prepare('SELECT * FROM transactions WHERE ref = ? LIMIT 1');
        $stmt->execute([$ref]);
        $tx = $stmt->fetch();
        return $tx ?: null;
    }

    public function updateStatus(int $id, string $status, ?string $meta = null): void
    {
        $stmt = db()->prepare('UPDATE transactions SET status = ?, meta = ? WHERE id = ?');
        $stmt->execute([$status, $meta, $id]);
    }

    public function allFiltered(array $filters): array
    {
        $sql = 'SELECT * FROM transactions WHERE 1=1';
        $params = [];

        if (!empty($filters['type'])) {
            $sql .= ' AND type = ?';
            $params[] = $filters['type'];
        }
        if (!empty($filters['status'])) {
            $sql .= ' AND status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['user_id'])) {
            $sql .= ' AND user_id = ?';
            $params[] = (int)$filters['user_id'];
        }
        if (!empty($filters['from'])) {
            $sql .= ' AND created_at >= ?';
            $params[] = $filters['from'];
        }
        if (!empty($filters['to'])) {
            $sql .= ' AND created_at <= ?';
            $params[] = $filters['to'];
        }

        $sql .= ' ORDER BY id DESC LIMIT 200';
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function totalRevenue(): float
    {
        $stmt = db()->query("SELECT SUM(amount) AS total FROM transactions WHERE status = 'success'");
        $row = $stmt->fetch();
        return (float)($row['total'] ?? 0);
    }

    public function dailyRevenue(int $days = 7): array
    {
        $stmt = db()->prepare("SELECT DATE(created_at) AS day, SUM(amount) AS total FROM transactions WHERE status = 'success' AND created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY) GROUP BY DATE(created_at) ORDER BY day ASC");
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }
}
