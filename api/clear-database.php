<?php
/**
 * Clear Database API
 * Drops all tables from the database
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
    // Connect to MySQL
    $conn = new mysqli($host, $user, $password, $database);
    
    if ($conn->connect_error) {
        echo json_encode([
            'success' => false,
            'message' => 'Connection failed: ' . $conn->connect_error
        ]);
        exit;
    }
    
    // Disable foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS=0");
    
    // Get all tables
    $result = $conn->query("SHOW TABLES");
    
    if (!$result) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to get table list: ' . $conn->error
        ]);
        exit;
    }
    
    $tables = [];
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
    
    if (empty($tables)) {
        echo json_encode([
            'success' => true,
            'message' => 'Database is already empty (no tables found)'
        ]);
        exit;
    }
    
    // Drop all tables
    $droppedCount = 0;
    $errors = [];
    
    foreach ($tables as $table) {
        if ($conn->query("DROP TABLE IF EXISTS `$table`")) {
            $droppedCount++;
        } else {
            $errors[] = "Failed to drop table $table: " . $conn->error;
        }
    }
    
    // Re-enable foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS=1");
    
    $conn->close();
    
    if (empty($errors)) {
        echo json_encode([
            'success' => true,
            'message' => "Successfully dropped $droppedCount tables",
            'tables_dropped' => $droppedCount
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => "Dropped $droppedCount tables with some errors",
            'tables_dropped' => $droppedCount,
            'errors' => $errors
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
