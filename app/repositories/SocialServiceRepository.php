<?php

class SocialServiceRepository
{
    public function allActive(): array
    {
        $stmt = db()->query('SELECT * FROM social_services WHERE active = 1 ORDER BY category, name');
        return $stmt->fetchAll();
    }

    public function topActive(int $limit): array
    {
        $stmt = db()->prepare('SELECT * FROM social_services WHERE active = 1 ORDER BY category, name LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM social_services WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function upsertFromPeakerr(array $services): int
    {
        $count = 0;
        $stmt = db()->prepare('INSERT INTO social_services (peakerr_service_id, name, type, category, rate, min_qty, max_qty, refill, cancel, active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
            ON DUPLICATE KEY UPDATE name = VALUES(name), type = VALUES(type), category = VALUES(category), rate = VALUES(rate), min_qty = VALUES(min_qty), max_qty = VALUES(max_qty), refill = VALUES(refill), cancel = VALUES(cancel), active = 1');

        foreach ($services as $service) {
            if (!is_array($service)) {
                continue;
            }
            $serviceId = isset($service['service']) ? (int)$service['service'] : 0;
            $name = $service['name'] ?? null;
            $type = $service['type'] ?? null;
            $category = $service['category'] ?? null;
            $rate = isset($service['rate']) ? (float)$service['rate'] : null;
            $min = isset($service['min']) ? (int)$service['min'] : null;
            $max = isset($service['max']) ? (int)$service['max'] : null;
            $refill = !empty($service['refill']) ? 1 : 0;
            $cancel = !empty($service['cancel']) ? 1 : 0;

            if (!$serviceId || !$name || $rate === null || $min === null || $max === null) {
                continue;
            }

            $stmt->execute([
                $serviceId,
                $name,
                $type,
                $category,
                $rate,
                $min,
                $max,
                $refill,
                $cancel,
            ]);
            $count++;
        }

        return $count;
    }

    public function countAll(): int
    {
        $stmt = db()->query('SELECT COUNT(*) AS total FROM social_services');
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }
}
