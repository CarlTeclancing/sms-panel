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

    public function findByReferralCode(string $code): ?array
    {
        $stmt = db()->prepare('SELECT * FROM users WHERE referral_code = ? LIMIT 1');
        $stmt->execute([$code]);
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
        $stmt = db()->prepare('INSERT INTO users (name, email, password_hash, role, balance, profile_image, referral_code, referred_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['password_hash'],
            $data['role'] ?? 'user',
            $data['balance'] ?? 0,
            $data['profile_image'] ?? null,
            $data['referral_code'] ?? null,
            $data['referred_by'] ?? null,
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

    public function updateProfileInfo(int $userId, string $name, string $email, ?string $profileImage): void
    {
        if ($profileImage !== null) {
            $stmt = db()->prepare('UPDATE users SET name = ?, email = ?, profile_image = ? WHERE id = ?');
            $stmt->execute([$name, $email, $profileImage, $userId]);
            return;
        }

        $stmt = db()->prepare('UPDATE users SET name = ?, email = ? WHERE id = ?');
        $stmt->execute([$name, $email, $userId]);
    }

    public function updatePassword(int $userId, string $passwordHash): void
    {
        $stmt = db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $stmt->execute([$passwordHash, $userId]);
    }

    public function updateActive(int $userId, int $active): void
    {
        $stmt = db()->prepare('UPDATE users SET active = ? WHERE id = ?');
        $stmt->execute([$active, $userId]);
    }

    public function markFirstDeposit(int $userId): void
    {
        $stmt = db()->prepare('UPDATE users SET first_deposit_at = IFNULL(first_deposit_at, NOW()) WHERE id = ?');
        $stmt->execute([$userId]);
    }

    public function markReferralRewarded(int $userId): void
    {
        $stmt = db()->prepare('UPDATE users SET referral_rewarded = 1 WHERE id = ?');
        $stmt->execute([$userId]);
    }

    public function countReferrals(int $userId): int
    {
        $stmt = db()->prepare('SELECT COUNT(*) AS total FROM users WHERE referred_by = ?');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }

    public function updateReferralCode(int $userId, string $code): void
    {
        $stmt = db()->prepare('UPDATE users SET referral_code = ? WHERE id = ?');
        $stmt->execute([$code, $userId]);
    }

    public function countAll(): int
    {
        $stmt = db()->query('SELECT COUNT(*) AS total FROM users');
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }
}
