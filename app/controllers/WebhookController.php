<?php

class WebhookController
{
    private TransactionRepository $transactions;
    private UserRepository $users;

    public function __construct()
    {
        $this->transactions = new TransactionRepository();
        $this->users = new UserRepository();
    }

    public function fapshi(): void
    {
        $payload = json_decode(file_get_contents('php://input'), true) ?? [];
        $externalId = $payload['externalId'] ?? $payload['external_id'] ?? null;
        $status = strtolower((string)($payload['status'] ?? ''));
        $amount = (float)($payload['amount'] ?? 0);

        if (!$externalId) {
            $this->json(['success' => false, 'message' => 'Missing externalId'], 400);
        }

        $tx = $this->transactions->findByRef($externalId);
        if (!$tx) {
            $this->json(['success' => false, 'message' => 'Transaction not found'], 404);
        }

        if ($tx['status'] === 'success') {
            $this->json(['success' => true, 'message' => 'Already processed']);
        }

        $isSuccess = in_array($status, ['success', 'successful', 'paid', 'completed'], true);
        $newStatus = $isSuccess ? 'success' : 'failed';

        $this->transactions->updateStatus((int)$tx['id'], $newStatus, json_encode($payload));

        if ($isSuccess) {
            $creditAmount = $amount > 0 ? $amount : (float)$tx['amount'];
            $this->users->incrementBalance((int)$tx['user_id'], $creditAmount);
        }

        $this->json(['success' => true]);
    }

    private function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
