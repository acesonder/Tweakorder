<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

// Start session to check authentication
session_start();

// Check if user is authenticated
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'staff') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

try {
    switch ($action) {
        case 'get_layout':
            getLayout($conn, $user_id);
            break;
        
        case 'save_layout':
            saveLayout($conn, $user_id);
            break;
        
        case 'save_widgets':
            saveWidgets($conn, $user_id);
            break;
        
        case 'reset_layout':
            resetLayout($conn, $user_id);
            break;
        
        case 'get_widget_data':
            getWidgetData($conn, $user_id);
            break;
        
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function getLayout($conn, $user_id) {
    // Get active layout for user
    $stmt = $conn->prepare("SELECT * FROM dashboard_layouts WHERE user_id = ? AND is_active = 1 LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $layout = $result->fetch_assoc();
    
    if (!$layout) {
        // Create default layout if none exists
        $stmt = $conn->prepare("INSERT INTO dashboard_layouts (user_id, layout_name, is_active, grid_columns) VALUES (?, 'My Dashboard', 1, 12)");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $layout_id = $conn->insert_id;
        
        // Create default widgets
        createDefaultWidgets($conn, $layout_id);
        
        // Fetch the newly created layout
        $stmt = $conn->prepare("SELECT * FROM dashboard_layouts WHERE id = ?");
        $stmt->bind_param("i", $layout_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $layout = $result->fetch_assoc();
    }
    
    // Get widgets for this layout
    $stmt = $conn->prepare("SELECT * FROM dashboard_widgets WHERE layout_id = ? AND is_visible = 1 ORDER BY widget_order");
    $stmt->bind_param("i", $layout['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $widgets = [];
    while ($row = $result->fetch_assoc()) {
        $row['settings'] = json_decode($row['settings'], true);
        $widgets[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'layout' => $layout,
        'widgets' => $widgets
    ]);
}

function saveLayout($conn, $user_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $layout_id = $data['layout_id'] ?? null;
    $layout_name = $data['layout_name'] ?? 'My Dashboard';
    $grid_columns = $data['grid_columns'] ?? 12;
    
    if ($layout_id) {
        // Update existing layout
        $stmt = $conn->prepare("UPDATE dashboard_layouts SET layout_name = ?, grid_columns = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?");
        $stmt->bind_param("siii", $layout_name, $grid_columns, $layout_id, $user_id);
        $stmt->execute();
    }
    
    echo json_encode(['success' => true]);
}

function saveWidgets($conn, $user_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    $layout_id = $data['layout_id'] ?? null;
    $widgets = $data['widgets'] ?? [];
    
    if (!$layout_id) {
        echo json_encode(['success' => false, 'error' => 'Layout ID required']);
        return;
    }
    
    // Verify layout belongs to user
    $stmt = $conn->prepare("SELECT id FROM dashboard_layouts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $layout_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid layout']);
        return;
    }
    
    // Update widgets
    foreach ($widgets as $widget) {
        $widget_id = $widget['id'] ?? null;
        $settings_json = json_encode($widget['settings'] ?? []);
        
        if ($widget_id) {
            // Update existing widget
            $stmt = $conn->prepare("UPDATE dashboard_widgets SET position_x = ?, position_y = ?, width = ?, height = ?, widget_order = ?, settings = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND layout_id = ?");
            $stmt->bind_param("iiiissii", 
                $widget['position_x'], 
                $widget['position_y'], 
                $widget['width'], 
                $widget['height'], 
                $widget['widget_order'],
                $settings_json,
                $widget_id,
                $layout_id
            );
            $stmt->execute();
        } else {
            // Insert new widget
            $stmt = $conn->prepare("INSERT INTO dashboard_widgets (layout_id, widget_type, widget_title, position_x, position_y, width, height, widget_order, settings) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issiiiiis",
                $layout_id,
                $widget['widget_type'],
                $widget['widget_title'],
                $widget['position_x'],
                $widget['position_y'],
                $widget['width'],
                $widget['height'],
                $widget['widget_order'],
                $settings_json
            );
            $stmt->execute();
        }
    }
    
    echo json_encode(['success' => true]);
}

function resetLayout($conn, $user_id) {
    // Get active layout
    $stmt = $conn->prepare("SELECT id FROM dashboard_layouts WHERE user_id = ? AND is_active = 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $layout = $result->fetch_assoc();
    
    if ($layout) {
        // Delete all widgets
        $stmt = $conn->prepare("DELETE FROM dashboard_widgets WHERE layout_id = ?");
        $stmt->bind_param("i", $layout['id']);
        $stmt->execute();
        
        // Recreate default widgets
        createDefaultWidgets($conn, $layout['id']);
    }
    
    echo json_encode(['success' => true]);
}

function createDefaultWidgets($conn, $layout_id) {
    $defaultWidgets = [
        ['quick_stats', 'Quick Statistics', 0, 0, 12, 2, 1, '{"show_orders": true, "show_products": true, "show_clients": true}'],
        ['recent_orders', 'Recent Orders', 0, 2, 6, 4, 2, '{"limit": 5}'],
        ['quick_actions', 'Quick Actions', 6, 2, 6, 4, 3, '{}'],
        ['analytics_summary', 'Analytics Summary', 0, 6, 8, 3, 4, '{"chart_type": "bar"}'],
        ['activity_feed', 'Recent Activity', 8, 6, 4, 3, 5, '{"limit": 10}']
    ];
    
    $stmt = $conn->prepare("INSERT INTO dashboard_widgets (layout_id, widget_type, widget_title, position_x, position_y, width, height, widget_order, settings) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($defaultWidgets as $widget) {
        $stmt->bind_param("issiiiiis", $layout_id, ...$widget);
        $stmt->execute();
    }
}

function getWidgetData($conn, $user_id) {
    $widget_type = $_GET['widget_type'] ?? '';
    
    $data = [];
    
    switch ($widget_type) {
        case 'quick_stats':
            $data = getQuickStats($conn);
            break;
        
        case 'recent_orders':
            $limit = $_GET['limit'] ?? 5;
            $data = getRecentOrders($conn, $limit);
            break;
        
        case 'activity_feed':
            $limit = $_GET['limit'] ?? 10;
            $data = getActivityFeed($conn, $limit);
            break;
        
        case 'analytics_summary':
            $data = getAnalyticsSummary($conn);
            break;
    }
    
    echo json_encode(['success' => true, 'data' => $data]);
}

function getQuickStats($conn) {
    $stats = [];
    
    // Total orders
    $result = $conn->query("SELECT COUNT(*) as total FROM orders");
    $stats['total_orders'] = $result->fetch_assoc()['total'];
    
    // Today's orders
    $result = $conn->query("SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) = CURDATE()");
    $stats['today_orders'] = $result->fetch_assoc()['total'];
    
    // Total products
    $result = $conn->query("SELECT COUNT(*) as total FROM products");
    $stats['total_products'] = $result->fetch_assoc()['total'];
    
    // Total clients
    $result = $conn->query("SELECT COUNT(*) as total FROM clients");
    $stats['total_clients'] = $result->fetch_assoc()['total'];
    
    // Pending orders
    $result = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status IN ('processing', 'accepted', 'waiting_for_supplies')");
    $stats['pending_orders'] = $result->fetch_assoc()['total'];
    
    return $stats;
}

function getRecentOrders($conn, $limit) {
    $stmt = $conn->prepare("
        SELECT o.id, o.status, o.created_at, 
               CONCAT(c.first_name, ' ', c.last_name) as client_name,
               COUNT(oi.id) as item_count
        FROM orders o
        LEFT JOIN clients c ON o.client_id = c.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        GROUP BY o.id
        ORDER BY o.created_at DESC
        LIMIT ?
    ");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    
    return $orders;
}

function getActivityFeed($conn, $limit) {
    // Get recent orders as activity
    $stmt = $conn->prepare("
        SELECT 'order' as type, o.id, o.status, o.created_at,
               CONCAT(c.first_name, ' ', c.last_name) as client_name
        FROM orders o
        LEFT JOIN clients c ON o.client_id = c.id
        ORDER BY o.created_at DESC
        LIMIT ?
    ");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
    
    return $activities;
}

function getAnalyticsSummary($conn) {
    $summary = [];
    
    // Orders by status
    $result = $conn->query("
        SELECT status, COUNT(*) as count 
        FROM orders 
        GROUP BY status
    ");
    
    $summary['by_status'] = [];
    while ($row = $result->fetch_assoc()) {
        $summary['by_status'][] = $row;
    }
    
    // Orders over last 7 days
    $result = $conn->query("
        SELECT DATE(created_at) as date, COUNT(*) as count
        FROM orders
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date
    ");
    
    $summary['last_7_days'] = [];
    while ($row = $result->fetch_assoc()) {
        $summary['last_7_days'][] = $row;
    }
    
    return $summary;
}

$conn->close();
?>
