-- Quick Database Setup for Restaurant Landing Page
-- Run this in phpMyAdmin to create the database and table

-- Create the database
CREATE DATABASE IF NOT EXISTS restaurant_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE restaurant_db;

-- Create the reservations table
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    guests INT NOT NULL,
    special_requests TEXT,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for better performance
    INDEX idx_date_time (reservation_date, reservation_time),
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert a sample reservation for testing
INSERT INTO reservations (name, email, phone, reservation_date, reservation_time, guests, special_requests, status) VALUES
('John Smith', 'john@example.com', '(555) 123-4567', CURDATE() + INTERVAL 1 DAY, '19:00:00', 2, 'Window table please', 'pending');

-- Show success message
SELECT 'Database and table created successfully!' as Status;
SELECT 'You can now use the reservation system!' as Message;
