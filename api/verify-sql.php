<?php
/**
 * Verify SQL File API
 * Verifies that SQL file exists and is readable
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$filename = $_GET['file'] ?? '';

if (empty($filename)) {
    echo json_encode(['success' => false, 'message' => 'No filename provided']);
    exit;
}

// Security: only allow specific SQL files in root directory
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

// Check if file exists
if (!file_exists($filepath)) {
    echo json_encode([
        'success' => false,
        'message' => 'File not found: ' . $filename
    ]);
    exit;
}

// Check if file is readable
if (!is_readable($filepath)) {
    echo json_encode([
        'success' => false,
        'message' => 'File is not readable: ' . $filename
    ]);
    exit;
}

// Get file size
$filesize = filesize($filepath);
$filesizeFormatted = $filesize < 1024 ? $filesize . ' B' : round($filesize / 1024, 2) . ' KB';

// Count SQL statements (rough estimate)
$content = file_get_contents($filepath);
$statementCount = substr_count($content, ';');

// Check for CREATE TABLE statements
$createTableCount = preg_match_all('/CREATE\s+TABLE/i', $content);

// Check for INSERT statements
$insertCount = preg_match_all('/INSERT\s+INTO/i', $content);

echo json_encode([
    'success' => true,
    'message' => "File exists and is readable ($filesizeFormatted)",
    'details' => [
        'filename' => $filename,
        'size' => $filesize,
        'size_formatted' => $filesizeFormatted,
        'statement_count' => $statementCount,
        'create_table_count' => $createTableCount,
        'insert_count' => $insertCount
    ]
]);
?>
