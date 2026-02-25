<?php

class ServiceRepository
{
    public function allActive(): array
    {
        $stmt = db()->query('SELECT * FROM services WHERE active = 1 ORDER BY name');
        return $stmt->fetchAll();
    }

    public function updatePrice(int $id, float $price): void
    {
        $stmt = db()->prepare('UPDATE services SET price = ? WHERE id = ?');
        $stmt->execute([$price, $id]);
    }

    public function upsertFromSmsMan(array $apps, array $priceMap = []): int
    {
        $count = 0;
        $stmt = db()->prepare('INSERT INTO services (smsman_application_id, name, code, price, active) VALUES (?, ?, ?, ?, 1)
            ON DUPLICATE KEY UPDATE name = VALUES(name), code = VALUES(code), price = VALUES(price), active = 1');

        foreach ($apps as $key => $app) {
            if (!is_array($app)) {
                continue;
            }

            $id = isset($app['id']) ? (int)$app['id'] : (is_numeric($key) ? (int)$key : null);
            $name = $app['name'] ?? $app['title'] ?? null;
            $code = $app['code'] ?? null;

            if (!$id || !$name || !$code) {
                continue;
            }
            $price = $priceMap[$id] ?? 0.00;
            $stmt->execute([
                $id,
                $name,
                $code,
                $price,
            ]);
            $count++;
        }

        return $count;
    }

    public function countAll(): int
    {
        $stmt = db()->query('SELECT COUNT(*) AS total FROM services');
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }
}
