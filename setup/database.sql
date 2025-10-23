-- Restaurant Landing Page Database Setup
-- Run this script to create the database and tables

-- Create database
CREATE DATABASE IF NOT EXISTS restaurant_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE restaurant_db;

-- Create reservations table
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
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create newsletter subscribers table (optional)
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'unsubscribed') DEFAULT 'active',
    
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create contact messages table (optional - for general contact form)
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data (optional - remove in production)
INSERT INTO reservations (name, email, phone, reservation_date, reservation_time, guests, special_requests, status) VALUES
('John Smith', 'john@example.com', '(555) 123-4567', '2024-11-01', '19:00:00', 2, 'Anniversary dinner', 'confirmed'),
('Sarah Johnson', 'sarah@example.com', '(555) 987-6543', '2024-11-02', '20:00:00', 4, 'Birthday celebration', 'pending'),
('Michael Chen', 'michael@example.com', '(555) 456-7890', '2024-11-03', '18:30:00', 6, 'Business dinner', 'confirmed');

-- Create admin user table (optional - for reservation management)
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123 - CHANGE THIS!)
-- Password hash for 'admin123' - PLEASE CHANGE THIS IN PRODUCTION
INSERT INTO admin_users (username, email, password_hash, role) VALUES
('admin', 'admin@bellavista.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Create settings table for restaurant configuration
CREATE TABLE IF NOT EXISTS restaurant_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default restaurant settings
INSERT INTO restaurant_settings (setting_key, setting_value, description) VALUES
('restaurant_name', 'Bella Vista Restaurant', 'Restaurant name'),
('restaurant_email', 'reservations@bellavista.com', 'Main restaurant email'),
('restaurant_phone', '(555) 123-4567', 'Restaurant phone number'),
('restaurant_address', '123 Main Street, Downtown, CA 90210', 'Restaurant address'),
('opening_hours', 'Mon-Thu: 11AM-10PM, Fri-Sat: 11AM-11PM, Sun: 12PM-9PM', 'Opening hours'),
('max_guests_per_reservation', '20', 'Maximum guests per reservation'),
('advance_booking_days', '60', 'How many days in advance can customers book'),
('email_notifications', '1', 'Enable email notifications (1=yes, 0=no)'),
('auto_confirm_reservations', '0', 'Auto-confirm reservations (1=yes, 0=no)');

-- Show tables created
SHOW TABLES;

-- Show reservation table structure
DESCRIBE reservations;

-- Display success message
SELECT 'Database setup completed successfully!' as Status;
SELECT 'Default admin username: admin, password: admin123 (PLEASE CHANGE!)' as Important_Note;
