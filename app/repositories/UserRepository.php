<?php

class UserRepository
{
    public function findByEmail(string $email): ?array
    {
        $stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(array $data): int
    {
        $stmt = db()->prepare('INSERT INTO users (name, email, password_hash, role, balance) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['password_hash'],
            $data['role'] ?? 'user',
            $data['balance'] ?? 0,
        ]);
        return (int)db()->lastInsertId();
    }

    public function all(): array
    {
        $stmt = db()->query('SELECT id, name, email, role, balance, active, created_at FROM users ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public function updateBalance(int $userId, float $balance): void
    {
        $stmt = db()->prepare('UPDATE users SET balance = ? WHERE id = ?');
        $stmt->execute([$balance, $userId]);
    }

    public function incrementBalance(int $userId, float $amount): void
    {
        $stmt = db()->prepare('UPDATE users SET balance = balance + ? WHERE id = ?');
        $stmt->execute([$amount, $userId]);
    }

    public function updateProfile(int $userId, string $name, string $email, string $role): void
    {
        $stmt = db()->prepare('UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?');
        $stmt->execute([$name, $email, $role, $userId]);
    }

    public function updateActive(int $userId, int $active): void
    {
        $stmt = db()->prepare('UPDATE users SET active = ? WHERE id = ?');
        $stmt->execute([$active, $userId]);
    }

    public function countAll(): int
    {
        $stmt = db()->query('SELECT COUNT(*) AS total FROM users');
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }
}
