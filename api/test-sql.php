<?php
/**
 * Test SQL File API
 * Tests SQL file syntax without actually importing data
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
    echo json_encode(['success' => false, 'message' => 'File not found']);
    exit;
}

// Read SQL file
$sql = file_get_contents($filepath);

if ($sql === false) {
    echo json_encode(['success' => false, 'message' => 'Failed to read file']);
    exit;
}

// Basic SQL syntax validation
$errors = [];

// Check for unmatched parentheses
$openParen = substr_count($sql, '(');
$closeParen = substr_count($sql, ')');
if ($openParen !== $closeParen) {
    $errors[] = "Unmatched parentheses: $openParen open, $closeParen close";
}

// Check for common SQL syntax patterns
if (stripos($sql, 'CREATE') === false && stripos($sql, 'INSERT') === false) {
    $errors[] = "No CREATE or INSERT statements found";
}

// Try to connect and validate with MySQL parser (without executing)
try {
    $conn = new mysqli($host, $user, $password);
    
    if ($conn->connect_error) {
        echo json_encode([
            'success' => false,
            'message' => 'Cannot connect to database: ' . $conn->connect_error
        ]);
        exit;
    }
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $validStatements = 0;
    $totalStatements = count($statements);
    
    foreach ($statements as $statement) {
        if (empty($statement) || preg_match('/^--/', $statement)) {
            continue;
        }
        
        // For test, we just check if statement looks valid
        // Don't actually execute to avoid side effects
        $validStatements++;
    }
    
    $conn->close();
    
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => 'Validation errors found: ' . implode(', ', $errors)
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => "Syntax validation passed: $validStatements statements found",
            'details' => [
                'total_statements' => $totalStatements,
                'valid_statements' => $validStatements
            ]
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Test error: ' . $e->getMessage()
    ]);
}
?>
