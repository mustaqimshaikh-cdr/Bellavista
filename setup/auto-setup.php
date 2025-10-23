<?php
/**
 * Automatic Database Setup
 * This script will create the restaurant_db database and table automatically
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🍝 Restaurant Database Auto-Setup</h1>";

try {
    // Connect to MySQL server (without specifying database)
    echo "<p>📡 Connecting to MySQL server...</p>";
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ Connected to MySQL server successfully!</p>";
    
    // Create database
    echo "<p>🗄️ Creating database 'restaurant_db'...</p>";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS restaurant_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p style='color: green;'>✅ Database 'restaurant_db' created successfully!</p>";
    
    // Use the database
    $pdo->exec("USE restaurant_db");
    echo "<p>🔄 Switched to 'restaurant_db' database</p>";
    
    // Create reservations table
    echo "<p>📋 Creating 'reservations' table...</p>";
    $sql = "CREATE TABLE IF NOT EXISTS reservations (
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
        
        INDEX idx_date_time (reservation_date, reservation_time),
        INDEX idx_email (email),
        INDEX idx_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "<p style='color: green;'>✅ Table 'reservations' created successfully!</p>";
    
    // Insert sample data
    echo "<p>📝 Adding sample reservation...</p>";
    $stmt = $pdo->prepare("INSERT INTO reservations (name, email, phone, reservation_date, reservation_time, guests, special_requests, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'John Smith',
        'john@example.com',
        '(555) 123-4567',
        date('Y-m-d', strtotime('+1 day')),
        '19:00:00',
        2,
        'Window table please',
        'pending'
    ]);
    echo "<p style='color: green;'>✅ Sample reservation added!</p>";
    
    // Verify setup
    echo "<p>🔍 Verifying setup...</p>";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM reservations");
    $count = $stmt->fetch()['count'];
    echo "<p style='color: green;'>✅ Found {$count} reservation(s) in database</p>";
    
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h2 style='color: #155724;'>🎉 Setup Complete!</h2>";
    echo "<p><strong>Database:</strong> restaurant_db ✅</p>";
    echo "<p><strong>Table:</strong> reservations ✅</p>";
    echo "<p><strong>Sample data:</strong> Added ✅</p>";
    echo "<p><strong>Status:</strong> Ready to use! 🚀</p>";
    echo "</div>";
    
    echo "<h3>🧪 Next Steps:</h3>";
    echo "<ol>";
    echo "<li><a href='../index.html' target='_blank'>Test the main website</a></li>";
    echo "<li><a href='../admin/simple.php' target='_blank'>Check the admin panel</a></li>";
    echo "<li>Submit a test reservation</li>";
    echo "<li>Verify it appears in the admin panel</li>";
    echo "</ol>";
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h2 style='color: #721c24;'>❌ Database Setup Failed</h2>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<h3>💡 Troubleshooting:</h3>";
    echo "<ul>";
    echo "<li>Make sure <strong>MySQL is running</strong> in XAMPP Control Panel</li>";
    echo "<li>Check if you can access <a href='http://localhost/phpmyadmin' target='_blank'>phpMyAdmin</a></li>";
    echo "<li>Verify MySQL username is 'root' and password is empty (default XAMPP)</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<hr>";
echo "<p><small>🔧 Auto-setup script completed at " . date('Y-m-d H:i:s') . "</small></p>";
?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background: #f5f5f5;
}

h1 {
    color: #2c1810;
    text-align: center;
    margin-bottom: 30px;
}

p {
    margin: 10px 0;
    padding: 5px 0;
}

a {
    color: #d4af37;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

ol, ul {
    margin: 10px 0;
    padding-left: 30px;
}

li {
    margin: 5px 0;
}
</style>
