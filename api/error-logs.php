<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

// GET - Retrieve error logs
if ($method === 'GET') {
    $status = isset($_GET['status']) ? $_GET['status'] : 'unresolved';
    $severity = isset($_GET['severity']) ? $_GET['severity'] : null;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
    
    try {
        $query = "SELECT el.*, 
                         u.username as user_username,
                         c.first_name as client_first_name, 
                         c.last_name as client_last_name
                  FROM error_logs el
                  LEFT JOIN users u ON el.user_id = u.id
                  LEFT JOIN clients c ON el.client_id = c.id
                  WHERE 1=1";
        
        $params = [];
        
        if ($status === 'unresolved') {
            $query .= " AND el.is_resolved = 0";
        } elseif ($status === 'resolved') {
            $query .= " AND el.is_resolved = 1";
        }
        
        if ($severity) {
            $query .= " AND el.severity = ?";
            $params[] = $severity;
        }
        
        $query .= " ORDER BY el.created_at DESC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $errors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $errors
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

// POST - Create new error log
elseif ($method === 'POST') {
    $error_type = isset($_POST['error_type']) ? trim($_POST['error_type']) : null;
    $error_message = isset($_POST['error_message']) ? trim($_POST['error_message']) : null;
    $error_details = isset($_POST['error_details']) ? trim($_POST['error_details']) : null;
    $page_url = isset($_POST['page_url']) ? trim($_POST['page_url']) : null;
    $severity = isset($_POST['severity']) ? trim($_POST['severity']) : 'medium';
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
    $client_id = isset($_POST['client_id']) ? intval($_POST['client_id']) : null;
    
    if (!$error_type || !$error_message) {
        echo json_encode([
            'success' => false,
            'message' => 'Error type and message are required'
        ]);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO error_logs 
            (error_type, error_message, error_details, user_id, client_id, page_url, severity)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $error_type,
            $error_message,
            $error_details,
            $user_id,
            $client_id,
            $page_url,
            $severity
        ]);
        
        echo json_encode([
            'success' => true,
            'id' => $pdo->lastInsertId(),
            'message' => 'Error logged successfully'
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to log error: ' . $e->getMessage()
        ]);
    }
}

// PUT - Mark error as resolved
elseif ($method === 'PUT') {
    parse_str(file_get_contents("php://input"), $_PUT);
    
    $id = isset($_PUT['id']) ? intval($_PUT['id']) : null;
    $resolved_by = isset($_PUT['resolved_by']) ? intval($_PUT['resolved_by']) : null;
    
    if (!$id) {
        echo json_encode([
            'success' => false,
            'message' => 'Error ID is required'
        ]);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("
            UPDATE error_logs 
            SET is_resolved = 1, 
                resolved_at = NOW(), 
                resolved_by = ?
            WHERE id = ?
        ");
        
        $stmt->execute([$resolved_by, $id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Error marked as resolved'
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

else {
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
}
?>
