<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

// GET - Retrieve case templates
if ($method === 'GET') {
    $category = isset($_GET['category']) ? $_GET['category'] : null;
    $active_only = isset($_GET['active_only']) ? $_GET['active_only'] : true;
    
    try {
        $query = "SELECT * FROM case_templates WHERE 1=1";
        $params = [];
        
        if ($category) {
            $query .= " AND category = ?";
            $params[] = $category;
        }
        
        if ($active_only) {
            $query .= " AND is_active = 1";
        }
        
        $query .= " ORDER BY category, name";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $templates
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

// POST - Create new template (admin only)
elseif ($method === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : null;
    $category = isset($_POST['category']) ? trim($_POST['category']) : 'general';
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $template_content = isset($_POST['template_content']) ? trim($_POST['template_content']) : null;
    
    if (!$name || !$template_content) {
        echo json_encode([
            'success' => false,
            'message' => 'Name and template content are required'
        ]);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO case_templates 
            (name, category, description, template_content)
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->execute([$name, $category, $description, $template_content]);
        
        echo json_encode([
            'success' => true,
            'id' => $pdo->lastInsertId(),
            'message' => 'Template created successfully'
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create template: ' . $e->getMessage()
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
