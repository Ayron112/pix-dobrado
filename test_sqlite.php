<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Create SQLite database
    $db = new PDO('sqlite:database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Successfully connected to SQLite database!\n";
    
    // Create users table
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL
    )");
    echo "Users table created successfully!\n";
    
    // Insert test user
    $stmt = $db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute(['Test User', 'test@example.com', password_hash('test123', PASSWORD_DEFAULT)]);
    echo "Test user inserted successfully! ID: " . $db->lastInsertId() . "\n";
    
    // Verify the data
    $result = $db->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
    echo "Users in database:\n";
    print_r($result);
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>
