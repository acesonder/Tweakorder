<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$conn = getDBConnection();

// Get current user from session (in production, implement proper auth)
session_start();
$user_id = $_SESSION['user_id'] ?? 1; // Default to admin for demo

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $action = $_GET['action'] ?? 'get_preferences';
        
        switch($action) {
            case 'get_preferences':
                // Get user's workflow preferences
                $sql = "SELECT uwp.*, owt.name as template_name, owt.description as template_description
                        FROM user_workflow_preferences uwp
                        LEFT JOIN order_workflow_templates owt ON uwp.template_id = owt.id
                        WHERE uwp.user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $prefs = $result->fetch_assoc();
                    echo json_encode(['success' => true, 'data' => $prefs]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No preferences found']);
                }
                $stmt->close();
                break;
                
            case 'get_templates':
                // Get all available workflow templates
                $sql = "SELECT * FROM order_workflow_templates ORDER BY is_default DESC, name ASC";
                $result = $conn->query($sql);
                $templates = [];
                
                while($row = $result->fetch_assoc()) {
                    $templates[] = $row;
                }
                
                echo json_encode(['success' => true, 'data' => $templates]);
                break;
                
            case 'get_template_steps':
                // Get steps for a specific template
                $template_id = $_GET['template_id'] ?? 0;
                
                if (empty($template_id)) {
                    echo json_encode(['success' => false, 'message' => 'Template ID required']);
                    exit;
                }
                
                $sql = "SELECT * FROM order_workflow_steps WHERE template_id = ? ORDER BY step_order ASC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $template_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $steps = [];
                
                while($row = $result->fetch_assoc()) {
                    $steps[] = $row;
                }
                
                echo json_encode(['success' => true, 'data' => $steps]);
                $stmt->close();
                break;
                
            case 'get_product_display':
                // Get user's product display customization
                $sql = "SELECT upd.*, p.name as product_name, p.image, p.category
                        FROM user_product_display upd
                        JOIN products p ON upd.product_id = p.id
                        WHERE upd.user_id = ? AND upd.is_hidden = 0
                        ORDER BY upd.is_pinned DESC, upd.display_order ASC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $products = [];
                
                while($row = $result->fetch_assoc()) {
                    $products[] = $row;
                }
                
                echo json_encode(['success' => true, 'data' => $products]);
                $stmt->close();
                break;
                
            case 'get_quick_templates':
                // Get user's quick order templates
                $sql = "SELECT * FROM order_quick_templates 
                        WHERE user_id = ? 
                        ORDER BY use_count DESC, last_used_at DESC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $templates = [];
                
                while($row = $result->fetch_assoc()) {
                    $templates[] = $row;
                }
                
                echo json_encode(['success' => true, 'data' => $templates]);
                $stmt->close();
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Unknown action']);
                break;
        }
        break;
        
    case 'POST':
        $action = $_POST['action'] ?? '';
        
        switch($action) {
            case 'update_preferences':
                // Update user workflow preferences
                $template_id = $_POST['template_id'] ?? null;
                $product_display_mode = $_POST['product_display_mode'] ?? 'grid';
                $product_sort_order = $_POST['product_sort_order'] ?? 'name';
                $show_product_images = isset($_POST['show_product_images']) ? (int)$_POST['show_product_images'] : 1;
                $show_product_descriptions = isset($_POST['show_product_descriptions']) ? (int)$_POST['show_product_descriptions'] : 1;
                $quick_order_enabled = isset($_POST['quick_order_enabled']) ? (int)$_POST['quick_order_enabled'] : 0;
                $auto_advance_steps = isset($_POST['auto_advance_steps']) ? (int)$_POST['auto_advance_steps'] : 0;
                $default_order_status = $_POST['default_order_status'] ?? 'processing';
                $custom_settings = $_POST['custom_settings'] ?? null;
                
                // Check if preferences exist
                $check_sql = "SELECT id FROM user_workflow_preferences WHERE user_id = ?";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->bind_param("i", $user_id);
                $check_stmt->execute();
                $exists = $check_stmt->get_result()->num_rows > 0;
                $check_stmt->close();
                
                if ($exists) {
                    // Update existing preferences
                    $sql = "UPDATE user_workflow_preferences SET 
                            template_id = ?, product_display_mode = ?, product_sort_order = ?,
                            show_product_images = ?, show_product_descriptions = ?,
                            quick_order_enabled = ?, auto_advance_steps = ?, default_order_status = ?,
                            custom_settings = ?
                            WHERE user_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("issiiisssi", $template_id, $product_display_mode, $product_sort_order,
                                     $show_product_images, $show_product_descriptions, $quick_order_enabled,
                                     $auto_advance_steps, $default_order_status, $custom_settings, $user_id);
                } else {
                    // Insert new preferences
                    $sql = "INSERT INTO user_workflow_preferences 
                            (user_id, template_id, product_display_mode, product_sort_order,
                             show_product_images, show_product_descriptions, quick_order_enabled,
                             auto_advance_steps, default_order_status, custom_settings)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iissiiisss", $user_id, $template_id, $product_display_mode, $product_sort_order,
                                     $show_product_images, $show_product_descriptions, $quick_order_enabled,
                                     $auto_advance_steps, $default_order_status, $custom_settings);
                }
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Preferences updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update preferences']);
                }
                $stmt->close();
                break;
                
            case 'update_product_display':
                // Update product display order and visibility
                $product_id = $_POST['product_id'] ?? 0;
                $display_order = $_POST['display_order'] ?? 0;
                $is_pinned = isset($_POST['is_pinned']) ? (int)$_POST['is_pinned'] : 0;
                $is_hidden = isset($_POST['is_hidden']) ? (int)$_POST['is_hidden'] : 0;
                $custom_label = $_POST['custom_label'] ?? null;
                
                if (empty($product_id)) {
                    echo json_encode(['success' => false, 'message' => 'Product ID required']);
                    exit;
                }
                
                // Check if entry exists
                $check_sql = "SELECT id FROM user_product_display WHERE user_id = ? AND product_id = ?";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->bind_param("ii", $user_id, $product_id);
                $check_stmt->execute();
                $exists = $check_stmt->get_result()->num_rows > 0;
                $check_stmt->close();
                
                if ($exists) {
                    $sql = "UPDATE user_product_display SET display_order = ?, is_pinned = ?, 
                            is_hidden = ?, custom_label = ? WHERE user_id = ? AND product_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iiisii", $display_order, $is_pinned, $is_hidden, $custom_label, $user_id, $product_id);
                } else {
                    $sql = "INSERT INTO user_product_display (user_id, product_id, display_order, is_pinned, is_hidden, custom_label)
                            VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iiiiss", $user_id, $product_id, $display_order, $is_pinned, $is_hidden, $custom_label);
                }
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Product display updated']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update product display']);
                }
                $stmt->close();
                break;
                
            case 'save_quick_template':
                // Save a quick order template
                $template_name = $_POST['template_name'] ?? '';
                $description = $_POST['description'] ?? '';
                $client_id = $_POST['client_id'] ?? null;
                $product_items = $_POST['product_items'] ?? '[]';
                $default_status = $_POST['default_status'] ?? 'processing';
                $default_location_id = $_POST['default_location_id'] ?? null;
                $other_settings = $_POST['other_settings'] ?? null;
                
                if (empty($template_name)) {
                    echo json_encode(['success' => false, 'message' => 'Template name required']);
                    exit;
                }
                
                $sql = "INSERT INTO order_quick_templates 
                        (user_id, template_name, description, client_id, product_items, 
                         default_status, default_location_id, other_settings)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ississis", $user_id, $template_name, $description, $client_id, 
                                 $product_items, $default_status, $default_location_id, $other_settings);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Quick template saved', 'id' => $stmt->insert_id]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to save template']);
                }
                $stmt->close();
                break;
                
            case 'use_quick_template':
                // Increment usage counter for quick template
                $template_id = $_POST['template_id'] ?? 0;
                
                if (empty($template_id)) {
                    echo json_encode(['success' => false, 'message' => 'Template ID required']);
                    exit;
                }
                
                $sql = "UPDATE order_quick_templates 
                        SET use_count = use_count + 1, last_used_at = CURRENT_TIMESTAMP 
                        WHERE id = ? AND user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $template_id, $user_id);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update template']);
                }
                $stmt->close();
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Unknown action']);
                break;
        }
        break;
        
    case 'DELETE':
        parse_str(file_get_contents("php://input"), $_DELETE);
        $action = $_DELETE['action'] ?? '';
        
        switch($action) {
            case 'delete_quick_template':
                $template_id = $_DELETE['template_id'] ?? 0;
                
                if (empty($template_id)) {
                    echo json_encode(['success' => false, 'message' => 'Template ID required']);
                    exit;
                }
                
                $sql = "DELETE FROM order_quick_templates WHERE id = ? AND user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $template_id, $user_id);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Template deleted']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete template']);
                }
                $stmt->close();
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Unknown action']);
                break;
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}

$conn->close();
?>
