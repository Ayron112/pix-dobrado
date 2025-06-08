<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Try different connection methods
$methods = [
    [
        'dsn' => "mysql:host=localhost;port=3306",
        'user' => 'root',
        'pass' => 'root'
    ],
    [
        'dsn' => "mysql:host=127.0.0.1;port=3306",
        'user' => 'root',
        'pass' => 'root'
    ],
    [
        'dsn' => "mysql:unix_socket=/var/run/mysqld/mysqld.sock",
        'user' => 'root',
        'pass' => 'root'
    ]
];

foreach ($methods as $method) {
    try {
        echo "Trying connection with DSN: " . $method['dsn'] . "\n";
        $pdo = new PDO($method['dsn'], $method['user'], $method['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "Successfully connected to MySQL!\n";
        
        // Try to create and use database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS pix_dobrado");
        $pdo->exec("USE pix_dobrado");
        echo "Database selected successfully!\n";
        
        // If we get here, we've found a working connection
        echo "Connection method works! You can use these settings:\n";
        print_r($method);
        exit(0);
    } catch (PDOException $e) {
        echo "Failed: " . $e->getMessage() . "\n\n";
        continue;
    }
}

die("All connection methods failed.\n");
?>
