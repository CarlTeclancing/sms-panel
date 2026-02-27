<?php

class SocialOrderRepository
{
    public function create(array $data): int
    {
        $stmt = db()->prepare('INSERT INTO social_orders (user_id, service_id, peakerr_order_id, link, quantity, runs, interval_minutes, status, charge, remains, currency) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['user_id'],
            $data['service_id'],
            $data['peakerr_order_id'],
            $data['link'],
            $data['quantity'],
            $data['runs'] ?? null,
            $data['interval_minutes'] ?? null,
            $data['status'] ?? 'pending',
            $data['charge'] ?? null,
            $data['remains'] ?? null,
            $data['currency'] ?? 'USD',
        ]);
        return (int)db()->lastInsertId();
    }

    public function allByUser(int $userId): array
    {
        $stmt = db()->prepare('SELECT so.*, ss.name AS service_name, ss.category AS service_category FROM social_orders so JOIN social_services ss ON ss.id = so.service_id WHERE so.user_id = ? ORDER BY so.id DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
