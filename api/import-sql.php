<?php
/**
 * Import SQL File API
 * Imports SQL file into the database
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Increase execution time for large imports
set_time_limit(300); // 5 minutes

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
$filename = $input['file'] ?? '';

if (empty($filename)) {
    echo json_encode(['success' => false, 'message' => 'No filename provided']);
    exit;
}

// Security: only allow specific SQL files
$allowedFiles = [
    'database_schema.sql',
    'harm_reduction_products.sql',
    'demo_data.sql',
    'database.sql',
    'sample_data.sql',
    'case_templates_data.sql',
    'chat_messaging_schema.sql',
    'dashboard_customization.sql',
    'add_favorite_column.sql'
];

if (!in_array($filename, $allowedFiles)) {
    echo json_encode(['success' => false, 'message' => 'File not in allowed list']);
    exit;
}

$filepath = __DIR__ . '/../' . $filename;

if (!file_exists($filepath)) {
    echo json_encode(['success' => false, 'message' => 'File not found: ' . $filename]);
    exit;
}

// Read SQL file
$sql = file_get_contents($filepath);

if ($sql === false) {
    echo json_encode(['success' => false, 'message' => 'Failed to read file']);
    exit;
}

try {
    // Connect to MySQL
    $conn = new mysqli($host, $user, $password);
    
    if ($conn->connect_error) {
        echo json_encode([
            'success' => false,
            'message' => 'Connection failed: ' . $conn->connect_error
        ]);
        exit;
    }
    
    // Set charset
    $conn->set_charset("utf8mb4");
    
    // Disable foreign key checks temporarily for easier import
    $conn->query("SET FOREIGN_KEY_CHECKS=0");
    
    // Split SQL into individual statements
    // Remove comments and empty lines
    $sql = preg_replace('/^--.*$/m', '', $sql); // Remove -- comments
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql); // Remove /* */ comments
    
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $executedCount = 0;
    $errorCount = 0;
    $errors = [];
    
    foreach ($statements as $statement) {
        if (empty($statement)) {
            continue;
        }
        
        // Execute statement
        if ($conn->multi_query($statement . ';')) {
            do {
                // Store first result set
                if ($result = $conn->store_result()) {
                    $result->free();
                }
            } while ($conn->more_results() && $conn->next_result());
            
            $executedCount++;
        } else {
            $errorCount++;
            $errors[] = substr($statement, 0, 100) . '... : ' . $conn->error;
            
            // Stop if too many errors
            if ($errorCount > 10) {
                $errors[] = 'Too many errors, stopping import';
                break;
            }
        }
    }
    
    // Re-enable foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS=1");
    
    $conn->close();
    
    if ($errorCount === 0) {
        echo json_encode([
            'success' => true,
            'message' => "Successfully imported $executedCount statements from $filename",
            'details' => [
                'executed' => $executedCount,
                'errors' => $errorCount
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => "Import completed with errors: $executedCount statements executed, $errorCount errors",
            'details' => [
                'executed' => $executedCount,
                'errors' => $errorCount,
                'error_messages' => $errors
            ]
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Import error: ' . $e->getMessage()
    ]);
}
?>
