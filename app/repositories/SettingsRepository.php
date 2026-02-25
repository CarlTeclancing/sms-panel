<?php

class SettingsRepository
{
    public function get(string $name): ?string
    {
        $stmt = db()->prepare('SELECT value FROM settings WHERE name = ? LIMIT 1');
        $stmt->execute([$name]);
        $row = $stmt->fetch();
        return $row['value'] ?? null;
    }

    public function set(string $name, string $value): void
    {
        $stmt = db()->prepare('INSERT INTO settings (name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)');
        $stmt->execute([$name, $value]);
    }
}
