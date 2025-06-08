<?php
// test_db.php
require_once 'config.php';

try {
    // First connect without specifying a database
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Successfully connected to MySQL server\n";
    
    // Try to create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    echo "Database " . DB_NAME . " created or already exists\n";
    
    // Connect to the specific database
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    echo "Successfully connected to database " . DB_NAME . "\n";
    
    // Test creating a simple table
    $pdo->exec("CREATE TABLE IF NOT EXISTS test (id INT)");
    echo "Test table created successfully\n";
    
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}
?>
