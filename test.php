<?php
// Simple PHP test file to check if PHP is working
echo "<h1>PHP Test</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Current Time: " . date('Y-m-d H:i:s') . "</p>";

// Test database connection
try {
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", "root", "");
    echo "<p style='color: green;'>✅ Database connection successful</p>";
    
    // Check if restaurant_db exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'restaurant_db'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ Database 'restaurant_db' exists</p>";
        
        // Check if reservations table exists
        $pdo->exec("USE restaurant_db");
        $stmt = $pdo->query("SHOW TABLES LIKE 'reservations'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✅ Table 'reservations' exists</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Table 'reservations' does not exist</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ Database 'restaurant_db' does not exist</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

// Display PHP configuration
echo "<h2>PHP Configuration</h2>";
echo "<p>Error Reporting: " . (error_reporting() ? "Enabled" : "Disabled") . "</p>";
echo "<p>Display Errors: " . (ini_get('display_errors') ? "On" : "Off") . "</p>";
echo "<p>MySQL Extension: " . (extension_loaded('pdo_mysql') ? "Loaded" : "Not Loaded") . "</p>";

phpinfo();
?>
