<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$conn = getDBConnection();

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Get schedule availability
        $locationId = $_GET['location_id'] ?? null;
        
        if ($locationId) {
            $sql = "SELECT * FROM schedule_availability WHERE location_id = ? AND is_active = 1 ORDER BY 
                    FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), 
                    start_time";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $locationId);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $sql = "SELECT * FROM schedule_availability WHERE is_active = 1 ORDER BY 
                    FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), 
                    start_time";
            $result = $conn->query($sql);
        }
        
        $schedules = [];
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $schedules[] = $row;
            }
        }
        
        echo json_encode(['success' => true, 'data' => $schedules]);
        
        if (isset($stmt)) {
            $stmt->close();
        }
        break;
        
    case 'POST':
        // Add schedule availability
        $dayOfWeek = $_POST['day_of_week'] ?? '';
        $startTime = $_POST['start_time'] ?? '';
        $endTime = $_POST['end_time'] ?? '';
        $locationId = $_POST['location_id'] ?? null;
        
        if (empty($dayOfWeek) || empty($startTime) || empty($endTime)) {
            echo json_encode(['success' => false, 'message' => 'Day of week, start time, and end time are required']);
            exit;
        }
        
        $stmt = $conn->prepare("INSERT INTO schedule_availability (day_of_week, start_time, end_time, location_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $dayOfWeek, $startTime, $endTime, $locationId);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Schedule added successfully', 'id' => $stmt->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add schedule']);
        }
        $stmt->close();
        break;
}

$conn->close();
?>
