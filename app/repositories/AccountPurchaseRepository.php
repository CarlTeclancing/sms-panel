<?php

class AccountPurchaseRepository
{
    public function create(array $data): int
    {
        $stmt = db()->prepare('INSERT INTO account_purchases (listing_id, buyer_id, seller_id, price, platform_fee, net_amount, details_snapshot) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['listing_id'],
            $data['buyer_id'],
            $data['seller_id'],
            $data['price'],
            $data['platform_fee'],
            $data['net_amount'],
            $data['details_snapshot'],
        ]);
        return (int)db()->lastInsertId();
    }

    public function allByBuyer(int $buyerId): array
    {
        $stmt = db()->prepare('SELECT * FROM account_purchases WHERE buyer_id = ? ORDER BY id DESC');
        $stmt->execute([$buyerId]);
        return $stmt->fetchAll();
    }
}
