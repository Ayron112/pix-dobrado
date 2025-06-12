<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db.php';

// Get POST data
$data = $_POST;

// Debug: Log received data
error_log("Received POST data: " . print_r($data, true));

// Validate required fields including confirmPassword
$required = ['name', 'cep', 'cpf', 'address', 'birthdate', 'cell', 'email', 'password', 'confirmPassword', 'defaultPixKey'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        echo json_encode(['status' => 'error', 'message' => "Field {$field} is required."]);
        exit;
    }
}

// Validate password and confirmPassword match
if ($data['password'] !== $data['confirmPassword']) {
    echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
    exit;
}

try {
    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $data['email']]);
    if($stmt->fetch()){
        echo json_encode(['status' => 'error', 'message' => 'Email already registered.']);
        exit;
    }

    // Hash the password securely
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (name, cep, cpf, address, birthdate, cell, email, password, defaultPixKey) 
        VALUES (:name, :cep, :cpf, :address, :birthdate, :cell, :email, :password, :defaultPixKey)");
    
    $result = $stmt->execute([
        'name' => $data['name'],
        'cep' => $data['cep'],
        'cpf' => $data['cpf'],
        'address' => $data['address'],
        'birthdate' => $data['birthdate'],
        'cell' => $data['cell'],
        'email' => $data['email'],
        'password' => $hashedPassword,
        'defaultPixKey' => $data['defaultPixKey']
    ]);

    if ($result) {
        $userId = $pdo->lastInsertId();
        echo json_encode(['status' => 'success', 'message' => 'User registered successfully', 'user_id' => $userId]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to insert user']);
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
