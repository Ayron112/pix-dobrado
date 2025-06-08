<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

// Get POST data
$data = $_POST;

// Debug: Log received data
error_log("Received POST data: " . print_r($data, true));

// Validate required fields
if (empty($data['amount']) || empty($data['pixKey'])) {
    echo json_encode(['status' => 'error', 'message' => 'Amount and PIX key are required.']);
    exit;
}

// Validate amount is numeric and positive
if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid amount']);
    exit;
}

try {
    // Check available balance
    $stmt = $pdo->prepare("
        SELECT 
            COALESCE(SUM(doubled_amount), 0) as total_deposits,
            COALESCE((SELECT SUM(amount) FROM withdrawals WHERE user_id = :user_id), 0) as total_withdrawals
        FROM deposits 
        WHERE user_id = :user_id AND status = 'Confirmado'
    ");
    
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $balance = $stmt->fetch();
    
    $availableBalance = $balance['total_deposits'] - $balance['total_withdrawals'];
    $withdrawAmount = floatval($data['amount']);

    if ($withdrawAmount > $availableBalance) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Insufficient balance',
            'available_balance' => $availableBalance,
            'requested_amount' => $withdrawAmount
        ]);
        exit;
    }

    // Insert withdrawal record
    $stmt = $pdo->prepare("INSERT INTO withdrawals (user_id, amount, pixKey, status) 
        VALUES (:user_id, :amount, :pixKey, 'Em processamento')");
    
    $result = $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'amount' => $withdrawAmount,
        'pixKey' => $data['pixKey']
    ]);

    if ($result) {
        $withdrawalId = $pdo->lastInsertId();
        echo json_encode([
            'status' => 'success',
            'message' => 'Withdrawal request registered successfully',
            'withdrawal_id' => $withdrawalId,
            'amount' => $withdrawAmount,
            'remaining_balance' => $availableBalance - $withdrawAmount
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to register withdrawal']);
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
