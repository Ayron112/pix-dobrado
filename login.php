<?php
// login.php
header('Content-Type: application/json');
require_once 'db.php';

$data = $_POST;

if (empty($data['email']) || empty($data['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Email and password are required.']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $data['email']]);
$user = $stmt->fetch();

if ($user && password_verify($data['password'], $user['password'])) {
    // Remove sensitive field (password)
    unset($user['password']);
    echo json_encode(['status' => 'success', 'message' => 'Login successful', 'user' => $user]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
}
?>
