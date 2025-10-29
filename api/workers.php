<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$conn = getDBConnection();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Get all workers
        $sql = "SELECT * FROM workers ORDER BY created_at DESC";
        $result = $conn->query($sql);
        $workers = [];
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $workers[] = $row;
            }
        }
        
        echo json_encode(['success' => true, 'data' => $workers]);
        break;
        
    case 'POST':
        // Add new worker
        $first_name = $_POST['first_name'] ?? '';
        $last_name = $_POST['last_name'] ?? '';
        
        if (empty($first_name) || empty($last_name)) {
            echo json_encode(['success' => false, 'message' => 'First name and last name are required']);
            exit;
        }
        
        $stmt = $conn->prepare("INSERT INTO workers (first_name, last_name) VALUES (?, ?)");
        $stmt->bind_param("ss", $first_name, $last_name);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Worker added successfully', 'id' => $stmt->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add worker']);
        }
        $stmt->close();
        break;
}

$conn->close();
?>
