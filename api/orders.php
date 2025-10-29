<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$conn = getDBConnection();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Get orders
        $status = $_GET['status'] ?? 'all';
        
        if ($status == 'open') {
            $sql = "SELECT o.*, c.first_name, c.last_name 
                    FROM orders o 
                    JOIN clients c ON o.client_id = c.id 
                    WHERE o.status = 'waiting' 
                    ORDER BY o.created_at DESC";
        } elseif ($status == 'waiting') {
            $sql = "SELECT o.*, c.first_name, c.last_name 
                    FROM orders o 
                    JOIN clients c ON o.client_id = c.id 
                    WHERE o.status = 'waiting' 
                    ORDER BY o.created_at DESC";
        } else {
            $sql = "SELECT o.*, c.first_name, c.last_name 
                    FROM orders o 
                    JOIN clients c ON o.client_id = c.id 
                    ORDER BY o.created_at DESC";
        }
        
        $result = $conn->query($sql);
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
        break;
        
    case 'POST':
        // Create new order
        $client_id = $_POST['client_id'] ?? 0;
        $status = $_POST['status'] ?? 'waiting';
        $items = json_decode($_POST['items'] ?? '[]', true);
        
        if (empty($client_id) || empty($items)) {
            echo json_encode(['success' => false, 'message' => 'Client ID and items are required']);
            exit;
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Insert order
            $stmt = $conn->prepare("INSERT INTO orders (client_id, status) VALUES (?, ?)");
            $stmt->bind_param("is", $client_id, $status);
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
        
    case 'PUT':
        // Update order
        parse_str(file_get_contents("php://input"), $_PUT);
        $id = $_PUT['id'] ?? 0;
        $status = $_PUT['status'] ?? '';
        
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Order ID is required']);
            exit;
        }
        
        if (!empty($status)) {
            $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
            $stmt->bind_param("si", $status, $id);
        } else {
            echo json_encode(['success' => false, 'message' => 'No update data provided']);
            exit;
        }
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update order']);
        }
        $stmt->close();
        break;
}

$conn->close();
?>
