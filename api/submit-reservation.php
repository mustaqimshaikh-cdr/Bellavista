<?php
/**
 * Restaurant Reservation Form Handler
 * Handles form submissions for table reservations and sends confirmation emails
 */

// Enable error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Database configuration
$db_config = [
    'host' => 'localhost',
    'username' => 'root',  // Change this to your database username
    'password' => '',      // Change this to your database password
    'database' => 'restaurant_db'  // Change this to your database name
];

// Email configuration
$email_config = [
    'restaurant_email' => 'reservations@bellavista.com',  // Restaurant email
    'restaurant_name' => 'Bella Vista Restaurant',
    'smtp_host' => 'localhost',  // Configure your SMTP settings
    'smtp_port' => 587,
    'smtp_username' => '',
    'smtp_password' => '',
    'from_email' => 'noreply@bellavista.com'
];

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Validate email format
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (basic validation)
 */
function isValidPhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return strlen($phone) >= 10;
}

/**
 * Validate date (must be today or future)
 */
function isValidDate($date) {
    $reservation_date = DateTime::createFromFormat('Y-m-d', $date);
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    
    return $reservation_date && $reservation_date >= $today;
}

/**
 * Create database connection
 */
function createDatabaseConnection($config) {
    try {
        $pdo = new PDO(
            "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4",
            $config['username'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Create reservations table if it doesn't exist
 */
function createReservationsTable($pdo) {
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
    
    try {
        $pdo->exec($sql);
        return true;
    } catch (PDOException $e) {
        error_log("Failed to create reservations table: " . $e->getMessage());
        return false;
    }
}

/**
 * Save reservation to database
 */
function saveReservation($pdo, $data) {
    $sql = "INSERT INTO reservations (name, email, phone, reservation_date, reservation_time, guests, special_requests) 
            VALUES (:name, :email, :phone, :reservation_date, :reservation_time, :guests, :special_requests)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':phone' => $data['phone'],
            ':reservation_date' => $data['date'],
            ':reservation_time' => $data['time'],
            ':guests' => $data['guests'],
            ':special_requests' => $data['message'] ?? ''
        ]);
        
        return $result ? $pdo->lastInsertId() : false;
    } catch (PDOException $e) {
        error_log("Failed to save reservation: " . $e->getMessage());
        return false;
    }
}

/**
 * Send confirmation email to customer
 */
function sendCustomerConfirmation($data, $reservation_id, $config) {
    $to = $data['email'];
    $subject = "Reservation Confirmation - {$config['restaurant_name']}";
    
    $message = "
    <html>
    <head>
        <title>Reservation Confirmation</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #d4af37; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f9f9f9; }
            .details { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #d4af37; }
            .footer { text-align: center; padding: 20px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>{$config['restaurant_name']}</h1>
                <p>Reservation Confirmation</p>
            </div>
            <div class='content'>
                <h2>Dear {$data['name']},</h2>
                <p>Thank you for your reservation request! We have received your booking and will confirm availability shortly.</p>
                
                <div class='details'>
                    <h3>Reservation Details:</h3>
                    <p><strong>Reservation ID:</strong> #" . str_pad($reservation_id, 6, '0', STR_PAD_LEFT) . "</p>
                    <p><strong>Name:</strong> {$data['name']}</p>
                    <p><strong>Date:</strong> " . date('F j, Y', strtotime($data['date'])) . "</p>
                    <p><strong>Time:</strong> " . date('g:i A', strtotime($data['time'])) . "</p>
                    <p><strong>Guests:</strong> {$data['guests']}</p>
                    <p><strong>Phone:</strong> {$data['phone']}</p>";
    
    if (!empty($data['message'])) {
        $message .= "<p><strong>Special Requests:</strong> {$data['message']}</p>";
    }
    
    $message .= "
                </div>
                
                <p>We will contact you within 24 hours to confirm your reservation. If you have any questions, please don't hesitate to call us at <strong>(555) 123-4567</strong>.</p>
                
                <p>We look forward to serving you!</p>
            </div>
            <div class='footer'>
                <p>{$config['restaurant_name']}<br>
                123 Main Street, Downtown, CA 90210<br>
                Phone: (555) 123-4567 | Email: {$config['restaurant_email']}</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        "From: {$config['restaurant_name']} <{$config['from_email']}>",
        "Reply-To: {$config['restaurant_email']}",
        'X-Mailer: PHP/' . phpversion()
    ];
    
    return mail($to, $subject, $message, implode("\r\n", $headers));
}

/**
 * Send notification email to restaurant
 */
function sendRestaurantNotification($data, $reservation_id, $config) {
    $to = $config['restaurant_email'];
    $subject = "New Reservation Request - #{$reservation_id}";
    
    $message = "
    <html>
    <head>
        <title>New Reservation Request</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #2c1810; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .details { background: #f9f9f9; padding: 15px; margin: 15px 0; border: 1px solid #ddd; }
            .urgent { color: #d4af37; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>New Reservation Request</h1>
                <p class='urgent'>Requires Confirmation</p>
            </div>
            <div class='content'>
                <div class='details'>
                    <h3>Customer Information:</h3>
                    <p><strong>Reservation ID:</strong> #" . str_pad($reservation_id, 6, '0', STR_PAD_LEFT) . "</p>
                    <p><strong>Name:</strong> {$data['name']}</p>
                    <p><strong>Email:</strong> {$data['email']}</p>
                    <p><strong>Phone:</strong> {$data['phone']}</p>
                    <p><strong>Date:</strong> " . date('F j, Y', strtotime($data['date'])) . "</p>
                    <p><strong>Time:</strong> " . date('g:i A', strtotime($data['time'])) . "</p>
                    <p><strong>Number of Guests:</strong> {$data['guests']}</p>";
    
    if (!empty($data['message'])) {
        $message .= "<p><strong>Special Requests:</strong><br>{$data['message']}</p>";
    }
    
    $message .= "
                    <p><strong>Submitted:</strong> " . date('F j, Y g:i A') . "</p>
                </div>
                
                <p><strong>Action Required:</strong> Please contact the customer to confirm this reservation.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        "From: Website <{$config['from_email']}>",
        'X-Mailer: PHP/' . phpversion()
    ];
    
    return mail($to, $subject, $message, implode("\r\n", $headers));
}

// Main processing logic
try {
    // Get and decode JSON input
    $json_input = file_get_contents('php://input');
    $input_data = json_decode($json_input, true);
    
    // If JSON input is empty, try POST data
    if (empty($input_data)) {
        $input_data = $_POST;
    }
    
    // Validate required fields
    $required_fields = ['name', 'email', 'phone', 'date', 'time', 'guests'];
    $errors = [];
    
    foreach ($required_fields as $field) {
        if (empty($input_data[$field])) {
            $errors[] = ucfirst($field) . ' is required';
        }
    }
    
    // If there are missing fields, return error
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please fill in all required fields',
            'errors' => $errors
        ]);
        exit();
    }
    
    // Sanitize input data
    $data = [
        'name' => sanitizeInput($input_data['name']),
        'email' => sanitizeInput($input_data['email']),
        'phone' => sanitizeInput($input_data['phone']),
        'date' => sanitizeInput($input_data['date']),
        'time' => sanitizeInput($input_data['time']),
        'guests' => (int)$input_data['guests'],
        'message' => isset($input_data['message']) ? sanitizeInput($input_data['message']) : ''
    ];
    
    // Validate data
    $validation_errors = [];
    
    if (strlen($data['name']) < 2) {
        $validation_errors[] = 'Name must be at least 2 characters long';
    }
    
    if (!isValidEmail($data['email'])) {
        $validation_errors[] = 'Please enter a valid email address';
    }
    
    if (!isValidPhone($data['phone'])) {
        $validation_errors[] = 'Please enter a valid phone number';
    }
    
    if (!isValidDate($data['date'])) {
        $validation_errors[] = 'Please select a valid future date';
    }
    
    if ($data['guests'] < 1 || $data['guests'] > 20) {
        $validation_errors[] = 'Number of guests must be between 1 and 20';
    }
    
    // If validation fails, return errors
    if (!empty($validation_errors)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please correct the following errors',
            'errors' => $validation_errors
        ]);
        exit();
    }
    
    // Create database connection
    $pdo = createDatabaseConnection($db_config);
    if (!$pdo) {
        echo json_encode([
            'success' => false,
            'message' => 'Database connection failed. Please try again later.'
        ]);
        exit();
    }
    
    // Create table if it doesn't exist
    if (!createReservationsTable($pdo)) {
        echo json_encode([
            'success' => false,
            'message' => 'Database setup failed. Please contact support.'
        ]);
        exit();
    }
    
    // Save reservation to database
    $reservation_id = saveReservation($pdo, $data);
    if (!$reservation_id) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to save reservation. Please try again.'
        ]);
        exit();
    }
    
    // Send confirmation emails
    $customer_email_sent = sendCustomerConfirmation($data, $reservation_id, $email_config);
    $restaurant_email_sent = sendRestaurantNotification($data, $reservation_id, $email_config);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Reservation request submitted successfully! We will contact you shortly to confirm.',
        'reservation_id' => $reservation_id,
        'emails_sent' => [
            'customer' => $customer_email_sent,
            'restaurant' => $restaurant_email_sent
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Reservation form error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred. Please try again later.'
    ]);
}
?>
