CREATE DATABASE IF NOT EXISTS tailoring_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tailoring_db;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO users (username, password_hash) VALUES
('admin', '$2y$10$L8.4aDJ1e8Wi.kP/7WknAOiyySFY4N3fdAoZ8Kihe7NS0JJFR8NQ.');

CREATE TABLE IF NOT EXISTS records (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    garment_type VARCHAR(80) NOT NULL,
    measurements TEXT NOT NULL,
    order_date DATE NOT NULL,
    due_date DATE NOT NULL,
    price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    status ENUM('Pending', 'Ongoing', 'Completed') NOT NULL DEFAULT 'Pending',
    priority ENUM('High', 'Normal', 'Low') NOT NULL DEFAULT 'Normal',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE records ADD COLUMN IF NOT EXISTS priority ENUM('High', 'Normal', 'Low') NOT NULL DEFAULT 'Normal' AFTER status;

INSERT INTO records (customer_name, phone, garment_type, measurements, order_date, due_date, price, status, priority, notes) VALUES
('Maria Santos', '0917 123 4567', 'School Uniform', 'Chest: 34, Waist: 28, Length: 24', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY), 850.00, 'Ongoing', 'High', 'Navy blue fabric.'),
('James Cruz', '0918 555 0192', 'Formal Trousers', 'Waist: 32, Hips: 38, Length: 40', DATE_SUB(CURDATE(), INTERVAL 2 DAY), DATE_ADD(CURDATE(), INTERVAL 3 DAY), 1200.00, 'Pending', 'Normal', 'Straight cut.'),
('Ana Reyes', '0920 400 8811', 'Blouse', 'Chest: 36, Shoulder: 15, Length: 25', DATE_SUB(CURDATE(), INTERVAL 10 DAY), DATE_SUB(CURDATE(), INTERVAL 2 DAY), 650.00, 'Completed', 'Low', 'Call customer when ready.');

-- Convert an older installation that used the previous four-status list.
ALTER TABLE records MODIFY status ENUM('Pending', 'In Progress', 'Ready', 'Collected', 'Ongoing', 'Completed') NOT NULL DEFAULT 'Pending';
UPDATE records SET status = 'Ongoing' WHERE status = 'In Progress';
UPDATE records SET status = 'Completed' WHERE status IN ('Ready', 'Collected');
ALTER TABLE records MODIFY status ENUM('Pending', 'Ongoing', 'Completed') NOT NULL DEFAULT 'Pending';
