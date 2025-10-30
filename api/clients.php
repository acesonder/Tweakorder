<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$conn = getDBConnection();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Get all clients
        $sql = "SELECT * FROM clients ORDER BY created_at DESC";
        $result = $conn->query($sql);
        $clients = [];
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $clients[] = $row;
            }
        }
        
        echo json_encode(['success' => true, 'data' => $clients]);
        break;
        
    case 'POST':
        // Add new client
        $first_name = $_POST['first_name'] ?? '';
        $last_name = $_POST['last_name'] ?? '';
        $phone = $_POST['phone'] ?? null;
        $email = $_POST['email'] ?? null;
        $address = $_POST['address'] ?? null;
        $emergency_contact = $_POST['emergency_contact'] ?? null;
        
        if (empty($first_name) || empty($last_name)) {
            echo json_encode(['success' => false, 'message' => 'First name and last name are required']);
            exit;
        }
        
        $stmt = $conn->prepare("INSERT INTO clients (first_name, last_name, phone, email, address, emergency_contact) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $first_name, $last_name, $phone, $email, $address, $emergency_contact);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Client added successfully', 'id' => $stmt->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add client']);
        }
        $stmt->close();
        break;
}

$conn->close();
?>
