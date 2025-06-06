<?php
// withdraw.php
header('Content-Type: application/json');
require_once 'db.php';

$data = $_POST;

if (empty($data['user_id']) || empty($data['amount']) || empty($data['pixKey'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO withdrawals (user_id, amount, pixKey, status) 
    VALUES (:user_id, :amount, :pixKey, 'Em processamento')");
try {
    $stmt->execute([
        'user_id' => $data['user_id'],
        'amount' => floatval($data['amount']),
        'pixKey' => $data['pixKey']
    ]);
    $withdrawalId = $pdo->lastInsertId();
    echo json_encode(['status' => 'success', 'message' => 'Withdrawal request submitted', 'withdrawal_id' => $withdrawalId]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
