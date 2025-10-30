<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';

// Check if client is authenticated
if (!isset($_SESSION['client_id']) || $_SESSION['user_type'] !== 'client') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$conn = getDBConnection();
$clientId = $_SESSION['client_id'];

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Get client's orders only
        $sql = "SELECT o.* FROM orders o WHERE o.client_id = ? ORDER BY o.created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = [];
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $order_id = $row['id'];
                
                // Get order items
                $items_sql = "SELECT oi.*, p.name, p.image, p.background_color 
                              FROM order_items oi 
                              JOIN products p ON oi.product_id = p.id 
                              WHERE oi.order_id = ?";
                $items_stmt = $conn->prepare($items_sql);
                $items_stmt->bind_param("i", $order_id);
                $items_stmt->execute();
                $items_result = $items_stmt->get_result();
                
                $items = [];
                while($item = $items_result->fetch_assoc()) {
                    $items[] = $item;
                }
                
                $row['items'] = $items;
                $orders[] = $row;
                $items_stmt->close();
            }
        }
        
        echo json_encode(['success' => true, 'data' => $orders]);
        $stmt->close();
        break;
        
    case 'POST':
        // Create new order for client
        $items = json_decode($_POST['items'] ?? '[]', true);
        $pickupOrDropoff = $_POST['pickup_or_dropoff'] ?? 'pickup';
        $locationId = $_POST['location_id'] ?? null;
        $scheduledTime = $_POST['scheduled_time'] ?? null;
        $otherInstructions = $_POST['other_instructions'] ?? '';
        
        if (empty($items)) {
            echo json_encode(['success' => false, 'message' => 'Order items are required']);
            exit;
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Insert order with 'processing' status for client orders
            $status = 'processing';
            $stmt = $conn->prepare("INSERT INTO orders (client_id, status, pickup_or_dropoff, location_id, scheduled_time, other_instructions) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ississ", $clientId, $status, $pickupOrDropoff, $locationId, $scheduledTime, $otherInstructions);
            $stmt->execute();
            $order_id = $stmt->insert_id;
            $stmt->close();
            
            // Insert order items
            $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
            foreach ($items as $item) {
                $product_id = $item['product_id'];
                $quantity = $item['quantity'];
                $item_stmt->bind_param("iii", $order_id, $product_id, $quantity);
                $item_stmt->execute();
            }
            $item_stmt->close();
            
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Order created successfully', 'id' => $order_id]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Failed to create order: ' . $e->getMessage()]);
        }
        break;
}

$conn->close();
?>
