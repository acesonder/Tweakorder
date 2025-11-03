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

// Check if user is authenticated (staff only)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

try {
    $pdo = getPDOConnection();

    switch ($action) {
        case 'reset_own_password':
            resetOwnPassword($pdo, $data);
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}

function resetOwnPassword($pdo, $data) {
    $currentPassword = $data['current_password'] ?? '';
    $newPassword = $data['new_password'] ?? '';

    if (empty($currentPassword) || empty($newPassword)) {
        echo json_encode(['success' => false, 'error' => 'Current password and new password required']);
        return;
    }

    try {
        // Verify current password
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['success' => false, 'error' => 'User not found']);
            return;
        }

        $passwordField = $user['password_hash'];
        if (!password_verify($currentPassword, $passwordField)) {
            echo json_encode(['success' => false, 'error' => 'Current password is incorrect']);
            return;
        }

        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        
        if ($stmt->execute([$hashedPassword, $_SESSION['user_id']])) {
            logActivity($pdo, $_SESSION['user_id'], 'reset_own_password', 'User reset their own password');
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
