<?php

class ApiKeyRepository
{
    public function findByUser(int $userId): ?array
    {
        $stmt = db()->prepare('SELECT * FROM api_keys WHERE user_id = ? LIMIT 1');
        $stmt->execute([$userId]);
        $key = $stmt->fetch();
        return $key ?: null;
    }

    public function create(int $userId, string $token): int
    {
        $stmt = db()->prepare('INSERT INTO api_keys (user_id, token) VALUES (?, ?)');
        $stmt->execute([$userId, $token]);
        return (int)db()->lastInsertId();
    }

    public function all(): array
    {
        $stmt = db()->query('SELECT * FROM api_keys ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public function deleteByUser(int $userId): void
    {
        $stmt = db()->prepare('DELETE FROM api_keys WHERE user_id = ?');
        $stmt->execute([$userId]);
    }
}
