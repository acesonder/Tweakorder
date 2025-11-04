<?php
// ============================================
// Chat & Messaging System Setup Script
// File: setup_chat_system.php
// Description: Sets up the database tables for the chat system
// ============================================

require_once 'config/database.php';

echo "Chat & Messaging System Setup\n";
echo "=============================\n\n";

try {
    $conn = getDBConnection();
    
    // Read the SQL file
    $sql = file_get_contents('chat_messaging_schema.sql');
    
    // Split into individual queries
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    
    $success = 0;
    $errors = 0;
    
    foreach ($queries as $query) {
        // Skip empty queries and comments
        if (empty($query) || strpos($query, '--') === 0 || strpos($query, '/*') === 0) {
            continue;
        }
        
        try {
            if ($conn->query($query)) {
                $success++;
                echo "✓ Query executed successfully\n";
            } else {
                $errors++;
                echo "✗ Query failed: " . $conn->error . "\n";
            }
        } catch (Exception $e) {
            $errors++;
            echo "✗ Error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n=============================\n";
    echo "Setup Complete!\n";
    echo "Successful queries: $success\n";
    echo "Failed queries: $errors\n";
    echo "\n";
    
    // Verify tables
    echo "Verifying tables...\n";
    $tables = ['conversations', 'conversation_participants', 'messages', 'message_read_status', 'chat_notifications'];
    
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "✓ Table '$table' exists\n";
        } else {
            echo "✗ Table '$table' not found\n";
        }
    }
    
    echo "\nChat system setup completed successfully!\n";
    echo "You can now access the chat at: chat.html\n";
    echo "Messaging dashboard at: messaging-dashboard.html\n";
    
} catch (Exception $e) {
    echo "Error during setup: " . $e->getMessage() . "\n";
    exit(1);
}

$conn->close();

// ============================================
// End of File: setup_chat_system.php
// ============================================
?>
