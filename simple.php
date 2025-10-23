<?php
/**
 * Simple Admin Panel - No Authentication Required
 * View reservations from database and log file
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get reservations from database
$reservations = [];
$db_connected = false;

try {
    $pdo = new PDO("mysql:host=localhost;dbname=restaurant_db;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT * FROM reservations ORDER BY created_at DESC LIMIT 50");
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db_connected = true;
    
} catch (PDOException $e) {
    $db_error = "Database connection failed: " . $e->getMessage();
}

// Get reservations from log file if database is not available
$log_reservations = [];
$log_file = __DIR__ . '/../api/reservations.log';
if (file_exists($log_file)) {
    $log_content = file_get_contents($log_file);
    $log_reservations = explode('---', $log_content);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Admin - Bella Vista Restaurant</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: #2c1810;
            color: white;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 8px;
            text-align: center;
        }
        
        .header h1 {
            color: #d4af37;
            margin-bottom: 10px;
        }
        
        .status {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .status.success {
            border-left: 4px solid #4CAF50;
        }
        
        .status.error {
            border-left: 4px solid #f44336;
        }
        
        .section {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .section-header {
            background: #2c1810;
            color: white;
            padding: 15px 20px;
        }
        
        .section-header h3 {
            color: #d4af37;
            margin: 0;
        }
        
        .section-content {
            padding: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .reservation-item {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            border-left: 4px solid #d4af37;
        }
        
        .reservation-item h4 {
            color: #2c1810;
            margin-bottom: 10px;
        }
        
        .reservation-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .detail-item {
            background: white;
            padding: 8px 12px;
            border-radius: 4px;
        }
        
        .detail-label {
            font-weight: 600;
            color: #666;
            font-size: 0.9rem;
        }
        
        .detail-value {
            color: #333;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .refresh-btn {
            background: #d4af37;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .refresh-btn:hover {
            background: #b8941f;
        }
        
        .log-entry {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 0.9rem;
            white-space: pre-line;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .reservation-details {
                grid-template-columns: 1fr;
            }
            
            table {
                font-size: 14px;
            }
            
            th, td {
                padding: 8px 4px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bella Vista Restaurant</h1>
            <p>Simple Admin Panel - Reservation Management</p>
            <a href="?" class="refresh-btn">Refresh Data</a>
        </div>
        
        <!-- Connection Status -->
        <div class="status <?php echo $db_connected ? 'success' : 'error'; ?>">
            <h3><?php echo $db_connected ? '✅ Database Connected' : '❌ Database Not Connected'; ?></h3>
            <?php if (!$db_connected && isset($db_error)): ?>
                <p><strong>Error:</strong> <?php echo htmlspecialchars($db_error); ?></p>
                <p><strong>Note:</strong> Showing data from log file instead.</p>
            <?php endif; ?>
        </div>
        
        <!-- Database Reservations -->
        <?php if ($db_connected): ?>
            <div class="section">
                <div class="section-header">
                    <h3>Database Reservations (<?php echo count($reservations); ?> total)</h3>
                </div>
                <div class="section-content">
                    <?php if (empty($reservations)): ?>
                        <div class="no-data">
                            <p>No reservations found in database.</p>
                            <p>Try submitting a test reservation from the main website.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($reservations as $reservation): ?>
                            <div class="reservation-item">
                                <h4>Reservation #<?php echo str_pad($reservation['id'], 6, '0', STR_PAD_LEFT); ?></h4>
                                <div class="reservation-details">
                                    <div class="detail-item">
                                        <div class="detail-label">Name</div>
                                        <div class="detail-value"><?php echo htmlspecialchars($reservation['name']); ?></div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Email</div>
                                        <div class="detail-value"><?php echo htmlspecialchars($reservation['email']); ?></div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Phone</div>
                                        <div class="detail-value"><?php echo htmlspecialchars($reservation['phone']); ?></div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Date & Time</div>
                                        <div class="detail-value">
                                            <?php echo date('M j, Y', strtotime($reservation['reservation_date'])); ?> 
                                            at <?php echo date('g:i A', strtotime($reservation['reservation_time'])); ?>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Guests</div>
                                        <div class="detail-value"><?php echo $reservation['guests']; ?></div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Status</div>
                                        <div class="detail-value"><?php echo ucfirst($reservation['status'] ?? 'pending'); ?></div>
                                    </div>
                                </div>
                                <?php if (!empty($reservation['special_requests'])): ?>
                                    <div class="detail-item">
                                        <div class="detail-label">Special Requests</div>
                                        <div class="detail-value"><?php echo htmlspecialchars($reservation['special_requests']); ?></div>
                                    </div>
                                <?php endif; ?>
                                <div class="detail-item">
                                    <div class="detail-label">Submitted</div>
                                    <div class="detail-value"><?php echo date('M j, Y g:i A', strtotime($reservation['created_at'])); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Log File Reservations -->
        <div class="section">
            <div class="section-header">
                <h3>Log File Reservations (<?php echo count(array_filter($log_reservations)); ?> entries)</h3>
            </div>
            <div class="section-content">
                <?php if (empty($log_reservations) || count(array_filter($log_reservations)) == 0): ?>
                    <div class="no-data">
                        <p>No reservations found in log file.</p>
                        <p>Log file location: <code><?php echo $log_file; ?></code></p>
                    </div>
                <?php else: ?>
                    <?php foreach (array_reverse(array_filter($log_reservations)) as $index => $log_entry): ?>
                        <?php if (trim($log_entry)): ?>
                            <div class="log-entry">
                                <?php echo htmlspecialchars(trim($log_entry)); ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Instructions -->
        <div class="section">
            <div class="section-header">
                <h3>Instructions</h3>
            </div>
            <div class="section-content">
                <h4>How to test the reservation system:</h4>
                <ol>
                    <li>Go to the main website: <a href="../index.html" target="_blank">../index.html</a></li>
                    <li>Scroll down to the "Reserve Your Table" section</li>
                    <li>Fill out the reservation form with test data</li>
                    <li>Submit the form</li>
                    <li>Come back to this page and refresh to see the new reservation</li>
                </ol>
                
                <h4>Troubleshooting:</h4>
                <ul>
                    <li><strong>Database not connected:</strong> Make sure MySQL is running in XAMPP and the database 'restaurant_db' exists</li>
                    <li><strong>No reservations showing:</strong> Try submitting a test reservation from the main website</li>
                    <li><strong>Form not working:</strong> Check the browser console for JavaScript errors</li>
                </ul>
                
                <h4>Files to check:</h4>
                <ul>
                    <li><strong>Main website:</strong> <a href="../index.html" target="_blank">index.html</a></li>
                    <li><strong>API endpoint:</strong> <a href="../api/submit-reservation-simple.php" target="_blank">submit-reservation-simple.php</a></li>
                    <li><strong>Test API:</strong> <a href="../api/test-simple.php" target="_blank">test-simple.php</a></li>
                    <li><strong>PHP test:</strong> <a href="../test.php" target="_blank">test.php</a></li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
