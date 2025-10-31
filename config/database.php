<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'tweakorder');

// Create MySQLi connection (for legacy code)
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        // Log error server-side (in production, use proper logging)
        error_log('Database connection failed: ' . $conn->connect_error);
        die(json_encode(['success' => false, 'message' => 'Database connection failed. Please contact support.']));
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

// Create PDO connection (for new code)
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    // Log error server-side (in production, use proper logging)
    error_log('PDO connection failed: ' . $e->getMessage());
    die(json_encode(['success' => false, 'message' => 'Database connection failed. Please contact support.']));
}
?>
