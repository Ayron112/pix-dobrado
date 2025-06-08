<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Connect to MySQL
    $pdo = new PDO("mysql:host=localhost;dbname=pix_dobrado", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Test data
    $testUser = [
        'name' => 'Test User',
        'cep' => '12345-678',
        'cpf' => '123.456.789-00',
        'address' => 'Test Address',
        'birthdate' => '1990-01-01',
        'cell' => '(11) 98765-4321',
        'email' => 'test@example.com',
        'password' => password_hash('test123', PASSWORD_DEFAULT),
        'defaultPixKey' => 'test@pix.com'
    ];
    
    // Try to insert test user
    $stmt = $pdo->prepare("INSERT INTO users (name, cep, cpf, address, birthdate, cell, email, password, defaultPixKey) 
        VALUES (:name, :cep, :cpf, :address, :birthdate, :cell, :email, :password, :defaultPixKey)");
    
    $result = $stmt->execute($testUser);
    
    if ($result) {
        echo "Test user created successfully! ID: " . $pdo->lastInsertId();
    } else {
        echo "Failed to create test user";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
