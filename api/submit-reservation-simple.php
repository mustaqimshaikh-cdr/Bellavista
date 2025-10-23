<?php
/**
 * Simplified Restaurant Reservation Handler
 * Basic form processing with minimal dependencies
 */

// Enable error reporting for debugging
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

try {
    // Get input data
    $json_input = file_get_contents('php://input');
    $input_data = json_decode($json_input, true);
    
    // If JSON input is empty, try POST data
    if (empty($input_data)) {
        $input_data = $_POST;
    }
    
    // Log the received data for debugging
    error_log("Received reservation data: " . print_r($input_data, true));
    
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
        'name' => htmlspecialchars(strip_tags(trim($input_data['name']))),
        'email' => filter_var(trim($input_data['email']), FILTER_SANITIZE_EMAIL),
        'phone' => htmlspecialchars(strip_tags(trim($input_data['phone']))),
        'date' => htmlspecialchars(strip_tags(trim($input_data['date']))),
        'time' => htmlspecialchars(strip_tags(trim($input_data['time']))),
        'guests' => (int)$input_data['guests'],
        'message' => isset($input_data['message']) ? htmlspecialchars(strip_tags(trim($input_data['message']))) : ''
    ];
    
    // Basic validation
    $validation_errors = [];
    
    if (strlen($data['name']) < 2) {
        $validation_errors[] = 'Name must be at least 2 characters long';
    }
    
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $validation_errors[] = 'Please enter a valid email address';
    }
    
    if (strlen(preg_replace('/[^0-9]/', '', $data['phone'])) < 10) {
        $validation_errors[] = 'Please enter a valid phone number';
    }
    
    $reservation_date = DateTime::createFromFormat('Y-m-d', $data['date']);
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    
    if (!$reservation_date || $reservation_date < $today) {
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
    
    // Try to save to database (optional - will work without database)
    $saved_to_db = false;
    $reservation_id = rand(100000, 999999); // Generate random ID if no database
    
    try {
        // Database connection
        $pdo = new PDO("mysql:host=localhost;dbname=restaurant_db;charset=utf8mb4", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create table if it doesn't exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS reservations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            reservation_date DATE NOT NULL,
            reservation_time TIME NOT NULL,
            guests INT NOT NULL,
            special_requests TEXT,
            status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        // Insert reservation
        $stmt = $pdo->prepare("INSERT INTO reservations (name, email, phone, reservation_date, reservation_time, guests, special_requests) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['date'],
            $data['time'],
            $data['guests'],
            $data['message']
        ]);
        
        if ($result) {
            $reservation_id = $pdo->lastInsertId();
            $saved_to_db = true;
        }
        
    } catch (PDOException $e) {
        // Database error - log it but don't fail the request
        error_log("Database error: " . $e->getMessage());
        $saved_to_db = false;
    }
    
    // Log reservation to file as backup
    $log_entry = date('Y-m-d H:i:s') . " - Reservation #" . $reservation_id . "\n";
    $log_entry .= "Name: " . $data['name'] . "\n";
    $log_entry .= "Email: " . $data['email'] . "\n";
    $log_entry .= "Phone: " . $data['phone'] . "\n";
    $log_entry .= "Date: " . $data['date'] . " at " . $data['time'] . "\n";
    $log_entry .= "Guests: " . $data['guests'] . "\n";
    if (!empty($data['message'])) {
        $log_entry .= "Special Requests: " . $data['message'] . "\n";
    }
    $log_entry .= "Database Saved: " . ($saved_to_db ? 'Yes' : 'No') . "\n";
    $log_entry .= "---\n\n";
    
    // Save to log file
    file_put_contents(__DIR__ . '/reservations.log', $log_entry, FILE_APPEND | LOCK_EX);
    
    // Send simple email notification (disabled for local development)
    $email_sent = false;
    
    // Check if mail server is configured before attempting to send
    $smtp_configured = !empty(ini_get('SMTP')) && ini_get('SMTP') !== 'localhost';
    
    if ($smtp_configured) {
        try {
            $to = $data['email'];
            $subject = "Reservation Confirmation - Bella Vista Restaurant";
            $message = "Dear " . $data['name'] . ",\n\n";
            $message .= "Thank you for your reservation request!\n\n";
            $message .= "Reservation Details:\n";
            $message .= "Date: " . date('F j, Y', strtotime($data['date'])) . "\n";
            $message .= "Time: " . date('g:i A', strtotime($data['time'])) . "\n";
            $message .= "Guests: " . $data['guests'] . "\n";
            $message .= "Reservation ID: #" . str_pad($reservation_id, 6, '0', STR_PAD_LEFT) . "\n\n";
            $message .= "We will contact you shortly to confirm your reservation.\n\n";
            $message .= "Best regards,\nBella Vista Restaurant\n(555) 123-4567";
            
            $headers = "From: Bella Vista Restaurant <noreply@bellavista.com>\r\n";
            $headers .= "Reply-To: reservations@bellavista.com\r\n";
            
            $email_sent = @mail($to, $subject, $message, $headers);
        } catch (Exception $e) {
            error_log("Email error: " . $e->getMessage());
        }
    } else {
        // Log email content to file for local development
        $email_log = "EMAIL LOG - " . date('Y-m-d H:i:s') . "\n";
        $email_log .= "To: " . $data['email'] . "\n";
        $email_log .= "Subject: Reservation Confirmation - Bella Vista Restaurant\n";
        $email_log .= "Message: Dear " . $data['name'] . ", thank you for your reservation!\n";
        $email_log .= "Reservation ID: #" . str_pad($reservation_id, 6, '0', STR_PAD_LEFT) . "\n";
        $email_log .= "---\n\n";
        
        file_put_contents(__DIR__ . '/email_log.txt', $email_log, FILE_APPEND | LOCK_EX);
        error_log("Email not sent - no SMTP configured. Email logged to file instead.");
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Reservation request submitted successfully! We will contact you shortly to confirm.',
        'reservation_id' => $reservation_id,
        'database_saved' => $saved_to_db,
        'email_sent' => $email_sent,
        'debug_info' => [
            'received_data' => $input_data,
            'processed_data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Reservation form error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred. Please try again or call us directly at (555) 123-4567.',
        'error_details' => $e->getMessage()
    ]);
}
?>
