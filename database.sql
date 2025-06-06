-- Create the database
CREATE DATABASE IF NOT EXISTS pix_dobrado;

USE pix_dobrado;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
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
);

-- Create deposits table
CREATE TABLE IF NOT EXISTS deposits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    original_amount DECIMAL(10,2) NOT NULL,
    doubled_amount DECIMAL(10,2) NOT NULL,
    method ENUM('manual','qr-code') NOT NULL,
    status ENUM('Em processamento', 'Confirmado', 'Concluído') DEFAULT 'Em processamento',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create withdrawals table
CREATE TABLE IF NOT EXISTS withdrawals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    pixKey VARCHAR(255) NOT NULL,
    status ENUM('Em processamento', 'Confirmado', 'Concluído') DEFAULT 'Em processamento',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
