<?php

class AccountListingRepository
{
    public function create(array $data): int
    {
        $stmt = db()->prepare('INSERT INTO account_listings (seller_id, title, category, platform, year, price, description, account_details, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['seller_id'],
            $data['title'],
            $data['category'],
            $data['platform'],
            $data['year'],
            $data['price'],
            $data['description'],
            $data['account_details'],
            $data['status'] ?? 'pending',
        ]);
        return (int)db()->lastInsertId();
    }

    public function allApproved(array $filters = []): array
    {
        $sql = "SELECT * FROM account_listings WHERE status = 'approved'";
        $params = [];

        if (!empty($filters['category'])) {
            $sql .= ' AND category = ?';
            $params[] = $filters['category'];
        }
        if (!empty($filters['platform'])) {
            $sql .= ' AND platform = ?';
            $params[] = $filters['platform'];
        }
        if (!empty($filters['year'])) {
            $sql .= ' AND year = ?';
            $params[] = (int)$filters['year'];
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND (title LIKE ? OR description LIKE ?)';
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }

        $sql .= ' ORDER BY id DESC';
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function allApprovedBySellerId(int $sellerId): array
    {
        $stmt = db()->prepare("SELECT * FROM account_listings WHERE status = 'approved' AND seller_id = ? ORDER BY id DESC");
        $stmt->execute([$sellerId]);
        return $stmt->fetchAll();
    }

    public function allBySeller(int $sellerId): array
    {
        $stmt = db()->prepare('SELECT * FROM account_listings WHERE seller_id = ? ORDER BY id DESC');
        $stmt->execute([$sellerId]);
        return $stmt->fetchAll();
    }

    public function allPending(): array
    {
        $stmt = db()->query("SELECT * FROM account_listings WHERE status = 'pending' ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM account_listings WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = db()->prepare('UPDATE account_listings SET status = ?, approved_at = CASE WHEN ? = "approved" THEN NOW() ELSE approved_at END WHERE id = ?');
        $stmt->execute([$status, $status, $id]);
    }

    public function markSold(int $id, int $buyerId): void
    {
        $stmt = db()->prepare('UPDATE account_listings SET status = "sold", sold_at = NOW(), buyer_id = ? WHERE id = ?');
        $stmt->execute([$buyerId, $id]);
    }
}
