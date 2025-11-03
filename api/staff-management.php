<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../config/database.php';

session_start();

// Check if user is authenticated and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? $_GET['action'] ?? '';

try {
    $pdo = getPDOConnection();

    switch ($action) {
        case 'list':
            listStaff($pdo);
            break;

        case 'update':
            updateStaff($pdo, $data);
            break;

        case 'delete':
            deleteStaff($pdo, $data);
            break;

        case 'reset_password':
            resetStaffPassword($pdo, $data);
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}

function listStaff($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT id, username, first_name, last_name, email, role, is_active, created_at, last_login FROM users ORDER BY created_at DESC");
        $stmt->execute();
        $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $staff]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Failed to fetch staff: ' . $e->getMessage()]);
    }
}

function updateStaff($pdo, $data) {
    $userId = $data['user_id'] ?? null;
    $firstName = $data['first_name'] ?? '';
    $lastName = $data['last_name'] ?? '';
    $email = $data['email'] ?? null;
    $role = $data['role'] ?? 'Worker';
    $isActive = $data['is_active'] ?? 1;

    if (!$userId || empty($firstName) || empty($lastName)) {
        echo json_encode(['success' => false, 'error' => 'Required fields missing']);
        return;
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE users 
            SET first_name = ?, last_name = ?, email = ?, role = ?, is_active = ?
            WHERE id = ?
        ");

        if ($stmt->execute([$firstName, $lastName, $email, $role, $isActive, $userId])) {
            logActivity($pdo, $_SESSION['user_id'], 'update_user', "Updated user ID: $userId");
            echo json_encode(['success' => true, 'message' => 'Staff member updated successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update staff member']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Update error: ' . $e->getMessage()]);
    }
}

function deleteStaff($pdo, $data) {
    $userId = $data['user_id'] ?? null;

    if (!$userId) {
        echo json_encode(['success' => false, 'error' => 'User ID required']);
        return;
    }

    // Prevent deleting yourself
    if ($userId == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'error' => 'Cannot delete your own account']);
        return;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        
        if ($stmt->execute([$userId])) {
            logActivity($pdo, $_SESSION['user_id'], 'delete_user', "Deleted user ID: $userId");
            echo json_encode(['success' => true, 'message' => 'Staff member deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete staff member']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Delete error: ' . $e->getMessage()]);
    }
}

function resetStaffPassword($pdo, $data) {
    $userId = $data['user_id'] ?? null;
    $newPassword = $data['new_password'] ?? '';

    if (!$userId || empty($newPassword)) {
        echo json_encode(['success' => false, 'error' => 'User ID and new password required']);
        return;
    }

    try {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        
        if ($stmt->execute([$hashedPassword, $userId])) {
            logActivity($pdo, $_SESSION['user_id'], 'reset_password', "Reset password for user ID: $userId");
            echo json_encode(['success' => true, 'message' => 'Password reset successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to reset password']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Password reset error: ' . $e->getMessage()]);
    }
}

function logActivity($pdo, $userId, $action, $description) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO activity_log (user_id, action, description, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $action,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    } catch (Exception $e) {
        error_log("Activity log error: " . $e->getMessage());
    }
}

function getPDOConnection() {
    require_once '../config/database.php';
    return getConnection();
}
?>
