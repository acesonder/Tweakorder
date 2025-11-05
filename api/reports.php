<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

// Get database connection
$conn = getDBConnection();

session_start();

// Check if user is authenticated and has admin/manager role
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_type'], ['Admin', 'Manager'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized - Admin or Manager role required']);
    exit;
}

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

try {
    switch ($action) {
        case 'get_sales_report':
            getSalesReport($conn);
            break;
        
        case 'get_inventory_report':
            getInventoryReport($conn);
            break;
        
        case 'get_client_activity_report':
            getClientActivityReport($conn);
            break;
        
        case 'get_staff_performance_report':
            getStaffPerformanceReport($conn);
            break;
        
        case 'get_comprehensive_report':
            getComprehensiveReport($conn);
            break;
        
        case 'export_report_data':
            exportReportData($conn);
            break;
        
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function getSalesReport($conn) {
    $startDate = $_GET['start_date'] ?? date('Y-m-01');
    $endDate = $_GET['end_date'] ?? date('Y-m-d');
    
    // Get order statistics
    $stmt = $conn->prepare("
        SELECT 
            DATE(created_at) as order_date,
            COUNT(*) as order_count,
            status,
            COUNT(CASE WHEN status = 'Fulfilled' THEN 1 END) as fulfilled_count,
            COUNT(CASE WHEN status = 'Pending' THEN 1 END) as pending_count,
            COUNT(CASE WHEN status = 'In Progress' THEN 1 END) as in_progress_count
        FROM orders 
        WHERE created_at BETWEEN ? AND ?
        GROUP BY DATE(created_at), status
        ORDER BY order_date DESC
    ");
    
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $dailyStats = [];
    
    while ($row = $result->fetch_assoc()) {
        $date = $row['order_date'];
        if (!isset($dailyStats[$date])) {
            $dailyStats[$date] = [
                'date' => $date,
                'total_orders' => 0,
                'fulfilled' => 0,
                'pending' => 0,
                'in_progress' => 0
            ];
        }
        $dailyStats[$date]['total_orders'] += $row['order_count'];
        $dailyStats[$date]['fulfilled'] += $row['fulfilled_count'];
        $dailyStats[$date]['pending'] += $row['pending_count'];
        $dailyStats[$date]['in_progress'] += $row['in_progress_count'];
    }
    
    // Get top products
    $stmt = $conn->prepare("
        SELECT 
            o.products,
            COUNT(*) as order_count
        FROM orders o
        WHERE o.created_at BETWEEN ? AND ?
        GROUP BY o.products
        ORDER BY order_count DESC
        LIMIT 10
    ");
    
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $topProducts = [];
    
    while ($row = $result->fetch_assoc()) {
        $topProducts[] = $row;
    }
    
    // Get summary statistics
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_orders,
            COUNT(CASE WHEN status = 'Fulfilled' THEN 1 END) as fulfilled_orders,
            COUNT(CASE WHEN status = 'Pending' THEN 1 END) as pending_orders,
            COUNT(DISTINCT client_id) as unique_clients
        FROM orders
        WHERE created_at BETWEEN ? AND ?
    ");
    
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $summary = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'report_type' => 'sales',
        'date_range' => ['start' => $startDate, 'end' => $endDate],
        'summary' => $summary,
        'daily_stats' => array_values($dailyStats),
        'top_products' => $topProducts,
        'generated_at' => date('Y-m-d H:i:s')
    ]);
}

function getInventoryReport($conn) {
    // Get all products with inventory levels
    // Note: This uses FIND_IN_SET for product matching which is a temporary solution.
    // For production use, consider implementing a proper order_items junction table
    // for better performance and reliability.
    $stmt = $conn->prepare("
        SELECT 
            p.id,
            p.name,
            p.sku,
            p.category,
            p.inventory,
            COUNT(DISTINCT o.id) as times_ordered,
            COALESCE(SUM(CASE WHEN o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END), 0) as orders_last_30_days
        FROM products p
        LEFT JOIN orders o ON FIND_IN_SET(p.name, REPLACE(o.products, ', ', ','))
        GROUP BY p.id, p.name, p.sku, p.category, p.inventory
        ORDER BY p.inventory ASC
    ");
    
    $stmt->execute();
    $result = $stmt->get_result();
    $products = [];
    $lowStock = [];
    $outOfStock = [];
    
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
        
        if ($row['inventory'] <= 0) {
            $outOfStock[] = $row;
        } elseif ($row['inventory'] <= 20) {
            $lowStock[] = $row;
        }
    }
    
    // Get inventory summary
    $summary = [
        'total_products' => count($products),
        'low_stock_items' => count($lowStock),
        'out_of_stock_items' => count($outOfStock),
        'total_inventory_value' => array_sum(array_column($products, 'inventory'))
    ];
    
    echo json_encode([
        'success' => true,
        'report_type' => 'inventory',
        'summary' => $summary,
        'products' => $products,
        'low_stock' => $lowStock,
        'out_of_stock' => $outOfStock,
        'generated_at' => date('Y-m-d H:i:s')
    ]);
}

function getClientActivityReport($conn) {
    $startDate = $_GET['start_date'] ?? date('Y-m-01');
    $endDate = $_GET['end_date'] ?? date('Y-m-d');
    
    // Get client activity statistics
    $stmt = $conn->prepare("
        SELECT 
            c.id,
            c.first_name,
            c.last_name,
            c.email,
            c.phone,
            COUNT(o.id) as total_orders,
            COUNT(CASE WHEN o.status = 'Fulfilled' THEN 1 END) as fulfilled_orders,
            MAX(o.created_at) as last_order_date,
            MIN(o.created_at) as first_order_date
        FROM clients c
        LEFT JOIN orders o ON c.id = o.client_id
        WHERE o.created_at BETWEEN ? AND ?
        GROUP BY c.id, c.first_name, c.last_name, c.email, c.phone
        ORDER BY total_orders DESC
    ");
    
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $clients = [];
    
    while ($row = $result->fetch_assoc()) {
        $clients[] = $row;
    }
    
    // Get new clients in date range
    $stmt = $conn->prepare("
        SELECT COUNT(*) as new_clients
        FROM clients
        WHERE created_at BETWEEN ? AND ?
    ");
    
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $newClientsData = $result->fetch_assoc();
    
    $summary = [
        'active_clients' => count($clients),
        'new_clients' => $newClientsData['new_clients'],
        'total_orders_by_clients' => array_sum(array_column($clients, 'total_orders')),
        'avg_orders_per_client' => count($clients) > 0 ? round(array_sum(array_column($clients, 'total_orders')) / count($clients), 2) : 0
    ];
    
    echo json_encode([
        'success' => true,
        'report_type' => 'client_activity',
        'date_range' => ['start' => $startDate, 'end' => $endDate],
        'summary' => $summary,
        'clients' => $clients,
        'generated_at' => date('Y-m-d H:i:s')
    ]);
}

function getStaffPerformanceReport($conn) {
    $startDate = $_GET['start_date'] ?? date('Y-m-01');
    $endDate = $_GET['end_date'] ?? date('Y-m-d');
    
    // Get staff activity from activity_log if it exists
    $stmt = $conn->prepare("
        SELECT 
            u.id,
            u.username,
            u.first_name,
            u.last_name,
            u.role,
            COUNT(CASE WHEN al.action = 'create_order' THEN 1 END) as orders_created,
            COUNT(CASE WHEN al.action = 'update_order' THEN 1 END) as orders_updated,
            COUNT(CASE WHEN al.action LIKE '%client%' THEN 1 END) as client_actions,
            COUNT(al.id) as total_actions,
            MAX(al.created_at) as last_activity
        FROM users u
        LEFT JOIN activity_log al ON u.id = al.user_id AND al.created_at BETWEEN ? AND ?
        WHERE u.role IN ('Admin', 'Manager', 'Worker')
        GROUP BY u.id, u.username, u.first_name, u.last_name, u.role
        ORDER BY total_actions DESC
    ");
    
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $staff = [];
    
    while ($row = $result->fetch_assoc()) {
        $staff[] = $row;
    }
    
    // If activity_log doesn't exist or is empty, use basic user info
    if (empty($staff)) {
        $stmt = $conn->prepare("
            SELECT 
                id,
                username,
                first_name,
                last_name,
                role
            FROM users
            WHERE role IN ('Admin', 'Manager', 'Worker')
            ORDER BY username
        ");
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $row['orders_created'] = 0;
            $row['orders_updated'] = 0;
            $row['client_actions'] = 0;
            $row['total_actions'] = 0;
            $row['last_activity'] = null;
            $staff[] = $row;
        }
    }
    
    $summary = [
        'total_staff' => count($staff),
        'total_actions' => array_sum(array_column($staff, 'total_actions')),
        'total_orders_created' => array_sum(array_column($staff, 'orders_created')),
        'total_orders_updated' => array_sum(array_column($staff, 'orders_updated'))
    ];
    
    echo json_encode([
        'success' => true,
        'report_type' => 'staff_performance',
        'date_range' => ['start' => $startDate, 'end' => $endDate],
        'summary' => $summary,
        'staff' => $staff,
        'generated_at' => date('Y-m-d H:i:s')
    ]);
}

function getComprehensiveReport($conn) {
    $startDate = $_GET['start_date'] ?? date('Y-m-01');
    $endDate = $_GET['end_date'] ?? date('Y-m-d');
    
    // This combines all reports into one comprehensive view
    $_GET['start_date'] = $startDate;
    $_GET['end_date'] = $endDate;
    
    // Capture output from each report function
    ob_start();
    getSalesReport($conn);
    $salesReport = json_decode(ob_get_clean(), true);
    
    ob_start();
    getInventoryReport($conn);
    $inventoryReport = json_decode(ob_get_clean(), true);
    
    ob_start();
    getClientActivityReport($conn);
    $clientReport = json_decode(ob_get_clean(), true);
    
    ob_start();
    getStaffPerformanceReport($conn);
    $staffReport = json_decode(ob_get_clean(), true);
    
    echo json_encode([
        'success' => true,
        'report_type' => 'comprehensive',
        'date_range' => ['start' => $startDate, 'end' => $endDate],
        'sales' => $salesReport,
        'inventory' => $inventoryReport,
        'clients' => $clientReport,
        'staff' => $staffReport,
        'generated_at' => date('Y-m-d H:i:s')
    ]);
}

function exportReportData($conn) {
    $reportType = $_GET['report_type'] ?? 'sales';
    $format = $_GET['format'] ?? 'csv';
    
    // Get the report data
    $_GET['action'] = 'get_' . $reportType . '_report';
    
    ob_start();
    switch ($reportType) {
        case 'sales':
            getSalesReport($conn);
            break;
        case 'inventory':
            getInventoryReport($conn);
            break;
        case 'client_activity':
            getClientActivityReport($conn);
            break;
        case 'staff_performance':
            getStaffPerformanceReport($conn);
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid report type']);
            return;
    }
    
    $reportData = json_decode(ob_get_clean(), true);
    
    if ($format === 'csv') {
        echo json_encode([
            'success' => true,
            'data' => $reportData,
            'format' => 'csv'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'data' => $reportData,
            'format' => $format
        ]);
    }
}

$conn->close();
