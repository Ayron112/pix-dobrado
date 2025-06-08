<?php
// db_sqlite.php
class Database {
    private static $db = null;
    private static $dbFile = 'database.sqlite';

    public static function getInstance() {
        if (self::$db === null) {
            try {
                self::$db = new PDO('sqlite:' . self::$dbFile);
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::initializeTables();
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$db;
    }

    private static function initializeTables() {
        $queries = [
            // Users table
            "CREATE TABLE IF NOT EXISTS users (
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
            )",
            // Deposits table
            "CREATE TABLE IF NOT EXISTS deposits (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                original_amount DECIMAL(10,2) NOT NULL,
                doubled_amount DECIMAL(10,2) NOT NULL,
                method TEXT NOT NULL CHECK(method IN ('manual','qr-code')),
                status TEXT DEFAULT 'Em processamento' CHECK(status IN ('Em processamento', 'Confirmado', 'Concluído')),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )",
            // Withdrawals table
            "CREATE TABLE IF NOT EXISTS withdrawals (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                pixKey TEXT NOT NULL,
                status TEXT DEFAULT 'Em processamento' CHECK(status IN ('Em processamento', 'Confirmado', 'Concluído')),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )"
        ];

        foreach ($queries as $query) {
            self::$db->exec($query);
        }
    }
}
?>
