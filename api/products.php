<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$conn = getDBConnection();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Get all products
        $sql = "SELECT * FROM products ORDER BY created_at DESC";
        $result = $conn->query($sql);
        $products = [];
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        
        echo json_encode(['success' => true, 'data' => $products]);
        break;
        
    case 'POST':
        // Add new product
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $inventory = $_POST['inventory'] ?? 100;
        $background_color = $_POST['background_color'] ?? '';
        
        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Product name is required']);
            exit;
        }
        
        // Handle image upload
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (in_array($_FILES['image']['type'], $allowed_types)) {
                $upload_dir = '../assets/uploads/';
                $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $file_ext;
                $upload_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image = 'assets/uploads/' . $filename;
                }
            }
        }
        
        $stmt = $conn->prepare("INSERT INTO products (name, description, image, inventory, background_color) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $name, $description, $image, $inventory, $background_color);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product added successfully', 'id' => $stmt->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add product']);
        }
        $stmt->close();
        break;
        
    case 'PUT':
        // Update product
        parse_str(file_get_contents("php://input"), $_PUT);
        $id = $_PUT['id'] ?? 0;
        $name = $_PUT['name'] ?? '';
        
        if (empty($id) || empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Product ID and name are required']);
            exit;
        }
        
        $description = $_PUT['description'] ?? '';
        $inventory = $_PUT['inventory'] ?? 100;
        $background_color = $_PUT['background_color'] ?? '';
        
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, inventory=?, background_color=? WHERE id=?");
        $stmt->bind_param("ssisi", $name, $description, $inventory, $background_color, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update product']);
        }
        $stmt->close();
        break;
        
    case 'DELETE':
        parse_str(file_get_contents("php://input"), $_DELETE);
        $id = $_DELETE['id'] ?? 0;
        
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Product ID is required']);
            exit;
        }
        
        $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete product']);
        }
        $stmt->close();
        break;
}

$conn->close();
?>
