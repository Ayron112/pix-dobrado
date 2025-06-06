<?php
// deposit.php
header('Content-Type: application/json');
require_once 'db.php';

$data = $_POST;

if (empty($data['user_id']) || empty($data['amount']) || empty($data['method'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
    exit;
}

$originalAmount = floatval($data['amount']);
$doubledAmount = $originalAmount * 2;

$stmt = $pdo->prepare("INSERT INTO deposits (user_id, original_amount, doubled_amount, method, status) 
    VALUES (:user_id, :original_amount, :doubled_amount, :method, 'Em processamento')");
try {
    $stmt->execute([
        'user_id' => $data['user_id'],
        'original_amount' => $originalAmount,
        'doubled_amount' => $doubledAmount,
        'method' => $data['method']
    ]);
    $depositId = $pdo->lastInsertId();
    echo json_encode(['status' => 'success', 'message' => 'Deposit recorded successfully', 'deposit_id' => $depositId]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
