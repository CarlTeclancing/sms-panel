<?php

class TicketRepository
{
    public function create(int $userId, string $subject, string $message): int
    {
        $stmt = db()->prepare('INSERT INTO tickets (user_id, subject, message, status) VALUES (?, ?, ?, ?)');
        $stmt->execute([$userId, $subject, $message, 'open']);
        return (int)db()->lastInsertId();
    }

    public function all(): array
    {
        $stmt = db()->query('SELECT t.*, u.name, u.email FROM tickets t JOIN users u ON u.id = t.user_id ORDER BY t.id DESC');
        return $stmt->fetchAll();
    }

    public function allByUser(int $userId): array
    {
        $stmt = db()->prepare('SELECT * FROM tickets WHERE user_id = ? ORDER BY id DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
