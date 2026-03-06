<?php

class UserRepository
{
    private function userColumns(): array
    {
        static $columns = null;
        if ($columns === null) {
            $stmt = db()->query('SHOW COLUMNS FROM users');
            $rows = $stmt->fetchAll();
            $columns = array_map(static fn($row) => $row['Field'] ?? '', $rows);
        }
        return $columns;
    }

    private function hasUserColumn(string $column): bool
    {
        return in_array($column, $this->userColumns(), true);
    }

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

    public function findByStoreSlug(string $slug): ?array
    {
        $stmt = db()->prepare('SELECT * FROM users WHERE store_slug = ? LIMIT 1');
        $stmt->execute([$slug]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(array $data): int
    {
        $balanceTopup = (float)($data['balance_topup'] ?? ($data['balance'] ?? 0));
        $balanceEarnings = (float)($data['balance_earnings'] ?? 0);
        $totalBalance = $balanceTopup + $balanceEarnings;
        $columns = ['name', 'email'];
        $values = [$data['name'], $data['email']];

        if ($this->hasUserColumn('phone_number')) {
            $columns[] = 'phone_number';
            $values[] = $data['phone_number'] ?? null;
        }

        if ($this->hasUserColumn('country')) {
            $columns[] = 'country';
            $values[] = $data['country'] ?? null;
        }

        $columns = array_merge($columns, [
            'password_hash',
            'role',
            'balance',
            'balance_topup',
            'balance_earnings',
            'profile_image',
            'referral_code',
            'referred_by',
        ]);

        $values = array_merge($values, [
            $data['password_hash'],
            $data['role'] ?? 'user',
            $totalBalance,
            $balanceTopup,
            $balanceEarnings,
            $data['profile_image'] ?? null,
            $data['referral_code'] ?? null,
            $data['referred_by'] ?? null,
        ]);

        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $stmt = db()->prepare('INSERT INTO users (' . implode(', ', $columns) . ') VALUES (' . $placeholders . ')');
        $stmt->execute($values);
        return (int)db()->lastInsertId();
    }

    public function all(): array
    {
        $columns = ['id', 'name', 'email'];
        if ($this->hasUserColumn('phone_number')) {
            $columns[] = 'phone_number';
        }
        if ($this->hasUserColumn('country')) {
            $columns[] = 'country';
        }
        $columns = array_merge($columns, ['role', 'balance', 'balance_topup', 'balance_earnings', 'active', 'created_at']);
        $stmt = db()->query('SELECT ' . implode(', ', $columns) . ' FROM users ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public function updateBalance(int $userId, float $balance): void
    {
        $stmt = db()->prepare('UPDATE users SET balance = ? WHERE id = ?');
        $stmt->execute([$balance, $userId]);
    }

    public function updateTopupBalance(int $userId, float $balanceTopup): void
    {
        $stmt = db()->prepare('UPDATE users SET balance_topup = ?, balance = balance_earnings + ? WHERE id = ?');
        $stmt->execute([$balanceTopup, $balanceTopup, $userId]);
    }

    public function updateEarningsBalance(int $userId, float $balanceEarnings): void
    {
        $stmt = db()->prepare('UPDATE users SET balance_earnings = ?, balance = balance_topup + ? WHERE id = ?');
        $stmt->execute([$balanceEarnings, $balanceEarnings, $userId]);
    }

    public function incrementTopupBalance(int $userId, float $amount): void
    {
        $stmt = db()->prepare('UPDATE users SET balance_topup = balance_topup + ?, balance = balance + ? WHERE id = ?');
        $stmt->execute([$amount, $amount, $userId]);
    }

    public function incrementEarningsBalance(int $userId, float $amount): void
    {
        $stmt = db()->prepare('UPDATE users SET balance_earnings = balance_earnings + ?, balance = balance + ? WHERE id = ?');
        $stmt->execute([$amount, $amount, $userId]);
    }

    public function updateProfile(int $userId, string $name, string $email, string $role, ?string $phoneNumber, ?string $country): void
    {
        $columns = ['name = ?', 'email = ?', 'role = ?'];
        $values = [$name, $email, $role];

        if ($this->hasUserColumn('phone_number')) {
            $columns[] = 'phone_number = ?';
            $values[] = $phoneNumber;
        }

        if ($this->hasUserColumn('country')) {
            $columns[] = 'country = ?';
            $values[] = $country;
        }

        $values[] = $userId;
        $stmt = db()->prepare('UPDATE users SET ' . implode(', ', $columns) . ' WHERE id = ?');
        $stmt->execute($values);
    }

    public function updateProfileInfo(int $userId, string $name, string $email, ?string $phoneNumber, ?string $country, ?string $profileImage): void
    {
        $columns = ['name = ?', 'email = ?'];
        $values = [$name, $email];

        if ($this->hasUserColumn('phone_number')) {
            $columns[] = 'phone_number = ?';
            $values[] = $phoneNumber;
        }

        if ($this->hasUserColumn('country')) {
            $columns[] = 'country = ?';
            $values[] = $country;
        }

        if ($profileImage !== null) {
            $columns[] = 'profile_image = ?';
            $values[] = $profileImage;
        }

        $values[] = $userId;
        $stmt = db()->prepare('UPDATE users SET ' . implode(', ', $columns) . ' WHERE id = ?');
        $stmt->execute($values);
    }

    public function updateStoreProfile(int $userId, string $storeName, string $storeSlug, string $storeTagline, string $storeDescription): void
    {
        $stmt = db()->prepare('UPDATE users SET store_name = ?, store_slug = ?, store_tagline = ?, store_description = ? WHERE id = ?');
        $stmt->execute([$storeName, $storeSlug, $storeTagline, $storeDescription, $userId]);
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

    public function walletTotals(): array
    {
        $stmt = db()->query('SELECT SUM(balance) AS total_balance, SUM(balance_topup) AS total_topup, SUM(balance_earnings) AS total_earnings FROM users');
        $row = $stmt->fetch();
        return [
            'total_balance' => (float)($row['total_balance'] ?? 0),
            'total_topup' => (float)($row['total_topup'] ?? 0),
            'total_earnings' => (float)($row['total_earnings'] ?? 0),
        ];
    }

    public function dailySignups(int $days = 7): array
    {
        $stmt = db()->prepare('SELECT DATE(created_at) AS day, COUNT(*) AS total FROM users WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY) GROUP BY DATE(created_at) ORDER BY day ASC');
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }
}
