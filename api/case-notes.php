<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

// GET - Retrieve case notes
if ($method === 'GET') {
    $client_id = isset($_GET['client_id']) ? intval($_GET['client_id']) : null;
    $category = isset($_GET['category']) ? $_GET['category'] : null;
    
    try {
        $query = "SELECT cn.*, 
                         c.first_name, c.last_name,
                         ct.name as template_name,
                         u.username as created_by_username
                  FROM case_notes cn
                  LEFT JOIN clients c ON cn.client_id = c.id
                  LEFT JOIN case_templates ct ON cn.template_id = ct.id
                  LEFT JOIN users u ON cn.created_by = u.id
                  WHERE 1=1";
        
        $params = [];
        
        if ($client_id) {
            $query .= " AND cn.client_id = ?";
            $params[] = $client_id;
        }
        
        if ($category) {
            $query .= " AND cn.category = ?";
            $params[] = $category;
        }
        
        $query .= " ORDER BY cn.created_at DESC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $notes
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

// POST - Create new case note
elseif ($method === 'POST') {
    $client_id = isset($_POST['client_id']) ? intval($_POST['client_id']) : null;
    $template_id = isset($_POST['template_id']) ? intval($_POST['template_id']) : null;
    $note_content = isset($_POST['note_content']) ? trim($_POST['note_content']) : null;
    $category = isset($_POST['category']) ? trim($_POST['category']) : 'general';
    $created_by = isset($_POST['created_by']) ? intval($_POST['created_by']) : null;
    $follow_up_date = isset($_POST['follow_up_date']) ? $_POST['follow_up_date'] : null;
    $is_confidential = isset($_POST['is_confidential']) ? intval($_POST['is_confidential']) : 0;
    
    if (!$client_id || !$note_content) {
        echo json_encode([
            'success' => false,
            'message' => 'Client ID and note content are required'
        ]);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO case_notes 
            (client_id, template_id, note_content, category, created_by, follow_up_date, is_confidential)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $client_id,
            $template_id,
            $note_content,
            $category,
            $created_by,
            $follow_up_date,
            $is_confidential
        ]);
        
        echo json_encode([
            'success' => true,
            'id' => $pdo->lastInsertId(),
            'message' => 'Case note created successfully'
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create case note: ' . $e->getMessage()
        ]);
    }
}

// PUT - Update case note
elseif ($method === 'PUT') {
    parse_str(file_get_contents("php://input"), $_PUT);
    
    $id = isset($_PUT['id']) ? intval($_PUT['id']) : null;
    $note_content = isset($_PUT['note_content']) ? trim($_PUT['note_content']) : null;
    $follow_up_date = isset($_PUT['follow_up_date']) ? $_PUT['follow_up_date'] : null;
    
    if (!$id || !$note_content) {
        echo json_encode([
            'success' => false,
            'message' => 'Note ID and content are required'
        ]);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("
            UPDATE case_notes 
            SET note_content = ?, 
                follow_up_date = ?
            WHERE id = ?
        ");
        
        $stmt->execute([$note_content, $follow_up_date, $id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Case note updated successfully'
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
