-- Database setup: create and select the database used by the project.
CREATE DATABASE IF NOT EXISTS student_management;
USE student_management;

-- Student records: this table stores the main student profile information.
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    course VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    status ENUM('Active', 'Inactive', 'Graduated') NOT NULL DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Admin login: this keeps the admin username and hashed password.
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Default admin: username is admin and password is admin123.
INSERT INTO admin (username, password) VALUES ('admin', '$2y$10$rTvzJwQA./uvYiJHZ1vUvOzs.EsqkJXvRrcaSQ2cBqavVVCt/LssS')
ON DUPLICATE KEY UPDATE username = username;
