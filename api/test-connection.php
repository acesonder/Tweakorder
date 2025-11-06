<?php
/**
 * Test Database Connection API
 * Tests if the provided database credentials are valid
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

$host = $input['host'] ?? 'localhost';
$user = $input['user'] ?? 'root';
$password = $input['password'] ?? '';
$database = $input['database'] ?? 'tweakorder';

try {
    // Try to connect to MySQL
    $conn = new mysqli($host, $user, $password);
    
    if ($conn->connect_error) {
        echo json_encode([
            'success' => false,
            'message' => 'Connection failed: ' . $conn->connect_error
        ]);
        exit;
    }
    
    // Check if database exists
    $result = $conn->query("SHOW DATABASES LIKE '$database'");
    $dbExists = $result && $result->num_rows > 0;
    
    if ($dbExists) {
        // Try to select the database
        if (!$conn->select_db($database)) {
            echo json_encode([
                'success' => false,
                'message' => 'Database exists but cannot be selected: ' . $conn->error
            ]);
            exit;
        }
        
        $message = "Connected successfully to database '$database'";
    } else {
        $message = "Connected successfully. Database '$database' does not exist yet (will be created on import)";
    }
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'database_exists' => $dbExists
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Connection error: ' . $e->getMessage()
    ]);
}
?>
