<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

try {
    // Remove existing database file if it exists
    if (file_exists(DB_FILE)) {
        unlink(DB_FILE);
        echo "Removed existing database file.\n";
    }

    // Create new database connection
    $pdo = new PDO('sqlite:' . DB_FILE);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to SQLite database.\n";
    
    // Create users table
    $pdo->exec("CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        cep TEXT NOT NULL,
        cpf TEXT NOT NULL,
        address TEXT NOT NULL,
        birthdate DATE NOT NULL,
        cell TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        defaultPixKey TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Users table created.\n";
    
    // Create deposits table
    $pdo->exec("CREATE TABLE deposits (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        original_amount DECIMAL(10,2) NOT NULL,
        doubled_amount DECIMAL(10,2) NOT NULL,
        method TEXT NOT NULL CHECK(method IN ('manual','qr-code')),
        status TEXT DEFAULT 'Em processamento' CHECK(status IN ('Em processamento', 'Confirmado', 'Concluído')),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
    echo "Deposits table created.\n";
    
    // Create withdrawals table
    $pdo->exec("CREATE TABLE withdrawals (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        pixKey TEXT NOT NULL,
        status TEXT DEFAULT 'Em processamento' CHECK(status IN ('Em processamento', 'Confirmado', 'Concluído')),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
    echo "Withdrawals table created.\n";
    
    echo "Database setup completed successfully!\n";
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>
