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

            $user = $this->users->findById((int)$tx['user_id']);
            if ($user && empty($user['first_deposit_at'])) {
                $this->users->markFirstDeposit((int)$tx['user_id']);
                if (!empty($user['referred_by']) && (int)$user['referral_rewarded'] !== 1) {
                    $referrerId = (int)$user['referred_by'];
                    $this->users->incrementBalance($referrerId, 1.00);
                    $this->transactions->create([
                        'user_id' => $referrerId,
                        'type' => 'adjustment',
                        'amount' => 1.00,
                        'ref' => 'referral-' . $tx['user_id'] . '-' . $tx['id'],
                        'provider' => 'referral',
                        'status' => 'success',
                        'meta' => json_encode(['referred_user_id' => (int)$tx['user_id']]),
                    ]);
                    $this->users->markReferralRewarded((int)$tx['user_id']);
                }
            }
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
