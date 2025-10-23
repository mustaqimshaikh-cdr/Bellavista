<?php
/**
 * Simple Admin Panel for Restaurant Reservations
 * Basic authentication and reservation management
 */

session_start();

// Simple authentication (in production, use proper authentication)
$admin_username = 'admin';
$admin_password = 'admin123'; // Change this!

// Handle login
if (isset($_POST['login'])) {
    if ($_POST['username'] === $admin_username && $_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $login_error = 'Invalid credentials';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Check if logged in
$is_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];

// Database configuration
$db_config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'restaurant_db'
];

// Get reservations if logged in
$reservations = [];
$stats = [];

if ($is_logged_in) {
    try {
        $pdo = new PDO(
            "mysql:host={$db_config['host']};dbname={$db_config['database']};charset=utf8mb4",
            $db_config['username'],
            $db_config['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // Get reservations
        $stmt = $pdo->query("SELECT * FROM reservations ORDER BY reservation_date DESC, reservation_time DESC LIMIT 50");
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get stats
        $stats_queries = [
            'total' => "SELECT COUNT(*) as count FROM reservations",
            'pending' => "SELECT COUNT(*) as count FROM reservations WHERE status = 'pending'",
            'confirmed' => "SELECT COUNT(*) as count FROM reservations WHERE status = 'confirmed'",
            'today' => "SELECT COUNT(*) as count FROM reservations WHERE reservation_date = CURDATE()"
        ];
        
        foreach ($stats_queries as $key => $query) {
            $stmt = $pdo->query($query);
            $stats[$key] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        }
        
    } catch (PDOException $e) {
        $db_error = "Database connection failed: " . $e->getMessage();
    }
}

// Handle status updates
if ($is_logged_in && isset($_POST['update_status'])) {
    try {
        $stmt = $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ?");
        $stmt->execute([$_POST['status'], $_POST['reservation_id']]);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        $update_error = "Failed to update status: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Bella Vista Restaurant</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            color: #d4af37;
        }
        
        .logout-btn {
            background: #d4af37;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: #b8941f;
        }
        
        .login-form {
            max-width: 400px;
            margin: 100px auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .login-form h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c1810;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #d4af37;
        }
        
        .btn {
            background: #d4af37;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #b8941f;
        }
        
        .error {
            color: #e74c3c;
            margin-top: 10px;
            text-align: center;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #d4af37;
        }
        
        .stat-label {
            color: #666;
            margin-top: 5px;
        }
        
        .reservations-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .table-header {
            background: #2c1810;
            color: white;
            padding: 20px;
        }
        
        .table-header h3 {
            color: #d4af37;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
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
        
        .status {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status.pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status.confirmed {
            background: #d4edda;
            color: #155724;
        }
        
        .status.cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-form {
            display: inline-block;
        }
        
        .status-select {
            padding: 4px 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .update-btn {
            background: #28a745;
            color: white;
            padding: 4px 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 5px;
        }
        
        .update-btn:hover {
            background: #218838;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .header {
                flex-direction: column;
                gap: 10px;
                text-align: center;
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
        <?php if (!$is_logged_in): ?>
            <!-- Login Form -->
            <div class="login-form">
                <h2>Admin Login</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" name="login" class="btn">Login</button>
                    <?php if (isset($login_error)): ?>
                        <div class="error"><?php echo $login_error; ?></div>
                    <?php endif; ?>
                </form>
            </div>
        <?php else: ?>
            <!-- Admin Dashboard -->
            <div class="header">
                <div>
                    <h1>Bella Vista Restaurant</h1>
                    <p>Reservation Management</p>
                </div>
                <a href="?logout=1" class="logout-btn">Logout</a>
            </div>
            
            <?php if (isset($db_error)): ?>
                <div class="error"><?php echo $db_error; ?></div>
            <?php else: ?>
                <!-- Statistics -->
                <div class="stats">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['total']; ?></div>
                        <div class="stat-label">Total Reservations</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['pending']; ?></div>
                        <div class="stat-label">Pending</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['confirmed']; ?></div>
                        <div class="stat-label">Confirmed</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['today']; ?></div>
                        <div class="stat-label">Today's Reservations</div>
                    </div>
                </div>
                
                <!-- Reservations Table -->
                <div class="reservations-table">
                    <div class="table-header">
                        <h3>Recent Reservations</h3>
                    </div>
                    
                    <?php if (empty($reservations)): ?>
                        <div class="no-data">
                            <p>No reservations found.</p>
                        </div>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Guests</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $reservation): ?>
                                    <tr>
                                        <td>#<?php echo str_pad($reservation['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                        <td><?php echo htmlspecialchars($reservation['name']); ?></td>
                                        <td><?php echo htmlspecialchars($reservation['email']); ?></td>
                                        <td><?php echo htmlspecialchars($reservation['phone']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($reservation['reservation_date'])); ?></td>
                                        <td><?php echo date('g:i A', strtotime($reservation['reservation_time'])); ?></td>
                                        <td><?php echo $reservation['guests']; ?></td>
                                        <td>
                                            <span class="status <?php echo $reservation['status']; ?>">
                                                <?php echo ucfirst($reservation['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST" class="status-form">
                                                <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                                <select name="status" class="status-select">
                                                    <option value="pending" <?php echo $reservation['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="confirmed" <?php echo $reservation['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                    <option value="cancelled" <?php echo $reservation['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                </select>
                                                <button type="submit" name="update_status" class="update-btn">Update</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php if (!empty($reservation['special_requests'])): ?>
                                        <tr>
                                            <td colspan="9" style="background: #f8f9fa; font-style: italic; padding-left: 40px;">
                                                <strong>Special Requests:</strong> <?php echo htmlspecialchars($reservation['special_requests']); ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                
                <?php if (isset($update_error)): ?>
                    <div class="error"><?php echo $update_error; ?></div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
