<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../config/database.php';

session_start();

// Check if user is authenticated and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

try {
    $pdo = getPDOConnection();

    switch ($action) {
        case 'import':
            importDemoContent($pdo);
            break;

        case 'remove':
            removeDemoContent($pdo);
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}

function importDemoContent($pdo) {
    try {
        // Start transaction
        $pdo->beginTransaction();

        // Insert sample products
        $stmt = $pdo->prepare("
            INSERT INTO products (name, description, inventory, background_color, category) VALUES
            ('Premium Widget', 'High-quality widget for professional use', 50, 'gradient-1', 'Tools'),
            ('Basic Gadget', 'Essential gadget for everyday tasks', 100, 'gradient-5', 'Accessories'),
            ('Deluxe Kit', 'Complete kit with everything you need', 25, 'gradient-7', 'Kits'),
            ('Standard Tool', 'Reliable tool for general purposes', 75, 'gradient-10', 'Tools'),
            ('Advanced Module', 'Advanced module with extra features', 30, 'gradient-3', 'Modules')
            ON DUPLICATE KEY UPDATE name=name
        ");
        $stmt->execute();

        // Insert sample locations
        $stmt = $pdo->prepare("
            INSERT INTO locations (name, address, type, is_active) VALUES
            ('Main Warehouse', '123 Main St, City, ST 12345', 'both', 1),
            ('Downtown Pickup Point', '456 Downtown Ave, City, ST 12345', 'pickup', 1),
            ('East Side Dropoff', '789 East Rd, City, ST 12345', 'dropoff', 1),
            ('North Branch', '321 North Blvd, City, ST 12345', 'both', 1)
            ON DUPLICATE KEY UPDATE name=name
        ");
        $stmt->execute();

        // Insert sample workers (for backward compatibility)
        $stmt = $pdo->prepare("
            INSERT INTO workers (first_name, last_name) VALUES
            ('Alice', 'Manager'),
            ('Bob', 'Technician'),
            ('Carol', 'Supervisor')
            ON DUPLICATE KEY UPDATE first_name=first_name
        ");
        $stmt->execute();

        // Get location IDs for schedule
        $stmt = $pdo->query("SELECT id FROM locations ORDER BY id LIMIT 2");
        $locations = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (count($locations) >= 2) {
            // Insert sample schedule availability
            $stmt = $pdo->prepare("
                INSERT INTO schedule_availability (day_of_week, start_time, end_time, location_id, is_active) VALUES
                ('Monday', '09:00:00', '17:00:00', ?, 1),
                ('Tuesday', '09:00:00', '17:00:00', ?, 1),
                ('Wednesday', '09:00:00', '17:00:00', ?, 1),
                ('Thursday', '09:00:00', '17:00:00', ?, 1),
                ('Friday', '09:00:00', '17:00:00', ?, 1),
                ('Monday', '10:00:00', '18:00:00', ?, 1),
                ('Wednesday', '10:00:00', '18:00:00', ?, 1),
                ('Friday', '10:00:00', '18:00:00', ?, 1)
                ON DUPLICATE KEY UPDATE day_of_week=day_of_week
            ");
            $stmt->execute([
                $locations[0], $locations[0], $locations[0], $locations[0], $locations[0],
                $locations[1], $locations[1], $locations[1]
            ]);
        }

        // Commit transaction
        $pdo->commit();

        logActivity($pdo, $_SESSION['user_id'], 'import_demo_content', 'Imported demo content');
        echo json_encode(['success' => true, 'message' => 'Demo content imported successfully']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => 'Import failed: ' . $e->getMessage()]);
    }
}

function removeDemoContent($pdo) {
    try {
        // Start transaction
        $pdo->beginTransaction();

        // Remove demo products (keep if referenced in orders)
        $stmt = $pdo->prepare("
            DELETE FROM products 
            WHERE name IN ('Premium Widget', 'Basic Gadget', 'Deluxe Kit', 'Standard Tool', 'Advanced Module')
            AND id NOT IN (SELECT DISTINCT product_id FROM order_items)
        ");
        $stmt->execute();

        // Remove demo locations (keep if referenced in orders or schedules with orders)
        $stmt = $pdo->prepare("
            DELETE FROM locations 
            WHERE name IN ('Main Warehouse', 'Downtown Pickup Point', 'East Side Dropoff', 'North Branch')
            AND id NOT IN (SELECT DISTINCT location_id FROM orders WHERE location_id IS NOT NULL)
        ");
        $stmt->execute();

        // Remove demo workers (keep if created orders)
        $stmt = $pdo->prepare("
            DELETE FROM workers 
            WHERE first_name IN ('Alice', 'Bob', 'Carol')
            AND last_name IN ('Manager', 'Technician', 'Supervisor')
        ");
        $stmt->execute();

        // Remove orphaned schedule availability
        $stmt = $pdo->prepare("
            DELETE FROM schedule_availability 
            WHERE location_id NOT IN (SELECT id FROM locations)
        ");
        $stmt->execute();

        // Commit transaction
        $pdo->commit();

        logActivity($pdo, $_SESSION['user_id'], 'remove_demo_content', 'Removed demo content');
        echo json_encode(['success' => true, 'message' => 'Demo content removed successfully']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => 'Remove failed: ' . $e->getMessage()]);
    }
}

function logActivity($pdo, $userId, $action, $description) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO activity_log (user_id, action, description, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $action,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    } catch (Exception $e) {
        error_log("Activity log error: " . $e->getMessage());
    }
}

function getPDOConnection() {
    require_once '../config/database.php';
    return getConnection();
}
?>
