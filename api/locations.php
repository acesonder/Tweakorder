<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$conn = getDBConnection();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Get active locations
        $type = $_GET['type'] ?? 'both';
        
        if ($type === 'pickup') {
            $sql = "SELECT * FROM locations WHERE is_active = 1 AND (type = 'pickup' OR type = 'both') ORDER BY name";
        } elseif ($type === 'dropoff') {
            $sql = "SELECT * FROM locations WHERE is_active = 1 AND (type = 'dropoff' OR type = 'both') ORDER BY name";
        } else {
            $sql = "SELECT * FROM locations WHERE is_active = 1 ORDER BY name";
        }
        
        $result = $conn->query($sql);
        $locations = [];
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $locations[] = $row;
            }
        }
        
        echo json_encode(['success' => true, 'data' => $locations]);
        break;
        
    case 'POST':
        // Add new location
        $name = $_POST['name'] ?? '';
        $address = $_POST['address'] ?? '';
        $type = $_POST['type'] ?? 'both';
        
        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Location name is required']);
            exit;
        }
        
        $stmt = $conn->prepare("INSERT INTO locations (name, address, type) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $address, $type);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Location added successfully', 'id' => $stmt->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add location']);
        }
        $stmt->close();
        break;
}

$conn->close();
?>
