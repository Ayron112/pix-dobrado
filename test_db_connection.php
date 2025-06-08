<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'pix_dobrado');
define('DB_USER', 'root');
define('DB_PASS', 'root');

try {
    // Try TCP/IP connection
    $dsn = "mysql:host=" . DB_HOST . ";port=3306;dbname=" . DB_NAME;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Successfully connected to MySQL!\n";
    
    // Try to create database and tables
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    echo "Database created or already exists.\n";
    
    $pdo->exec("USE " . DB_NAME);
    
    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        cep VARCHAR(20) NOT NULL,
        cpf VARCHAR(20) NOT NULL,
        address VARCHAR(255) NOT NULL,
        birthdate DATE NOT NULL,
        cell VARCHAR(20) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        defaultPixKey VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Users table created.\n";
    
    // Create deposits table
    $pdo->exec("CREATE TABLE IF NOT EXISTS deposits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        original_amount DECIMAL(10,2) NOT NULL,
        doubled_amount DECIMAL(10,2) NOT NULL,
        method ENUM('manual','qr-code') NOT NULL,
        status ENUM('Em processamento', 'Confirmado', 'Concluído') DEFAULT 'Em processamento',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
    echo "Deposits table created.\n";
    
    // Create withdrawals table
    $pdo->exec("CREATE TABLE IF NOT EXISTS withdrawals (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        pixKey VARCHAR(255) NOT NULL,
        status ENUM('Em processamento', 'Confirmado', 'Concluído') DEFAULT 'Em processamento',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
    echo "Withdrawals table created.\n";
    
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}
?>
