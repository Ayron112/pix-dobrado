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
if (empty($data['amount']) || empty($data['method'])) {
    echo json_encode(['status' => 'error', 'message' => 'Amount and method are required.']);
    exit;
}

// Validate amount is numeric and positive
if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid amount']);
    exit;
}

// Validate method
if (!in_array($data['method'], ['manual', 'qr-code'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid deposit method']);
    exit;
}

try {
    // Calculate doubled amount
    $originalAmount = floatval($data['amount']);
    $doubledAmount = $originalAmount * 2;

    // Insert deposit record
    $stmt = $pdo->prepare("INSERT INTO deposits (user_id, original_amount, doubled_amount, method, status) 
        VALUES (:user_id, :original_amount, :doubled_amount, :method, 'Em processamento')");
    
    $result = $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'original_amount' => $originalAmount,
        'doubled_amount' => $doubledAmount,
        'method' => $data['method']
    ]);

    if ($result) {
        $depositId = $pdo->lastInsertId();
        echo json_encode([
            'status' => 'success',
            'message' => 'Deposit registered successfully',
            'deposit_id' => $depositId,
            'original_amount' => $originalAmount,
            'doubled_amount' => $doubledAmount
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to register deposit']);
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
