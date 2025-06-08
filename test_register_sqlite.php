<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db.php';

try {
    // Test user data
    $userData = [
        'name' => 'Test User',
        'cep' => '12345-678',
        'cpf' => '123.456.789-00',
        'address' => 'Test Address 123',
        'birthdate' => '1990-01-01',
        'cell' => '(11) 98765-4321',
        'email' => 'test@example.com',
        'password' => password_hash('test123', PASSWORD_DEFAULT),
        'defaultPixKey' => 'test@pix.com'
    ];

    // Prepare insert statement
    $stmt = $pdo->prepare("INSERT INTO users (name, cep, cpf, address, birthdate, cell, email, password, defaultPixKey) 
        VALUES (:name, :cep, :cpf, :address, :birthdate, :cell, :email, :password, :defaultPixKey)");
    
    // Execute insert
    $stmt->execute($userData);
    
    echo "Test user created successfully! ID: " . $pdo->lastInsertId() . "\n";
    
    // Verify the data
    $result = $pdo->query("SELECT * FROM users")->fetchAll();
    echo "Users in database:\n";
    print_r($result);
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>
