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

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$action = $data['action'] ?? '';

try {
    $pdo = getPDOConnection();

    switch ($action) {
        case 'login':
            handleLogin($pdo, $data);
            break;

        case 'logout':
            handleLogout();
            break;

        case 'check_session':
            checkSession();
            break;

        case 'register':
            handleRegister($pdo, $data);
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}

function handleLogin($pdo, $data) {
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'Username and password required']);
        return;
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
        return;
    }

    // Verify password
    $passwordField = isset($user['password']) ? $user['password'] : $user['password_hash'];
    if (!password_verify($password, $passwordField)) {
        echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
        return;
    }

    // Check if account is active
    if ($user['is_active'] != 1) {
        echo json_encode(['success' => false, 'error' => 'Account is disabled']);
        return;
    }

    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];

    // Update last login
    $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->execute([$user['id']]);

    // Log activity
    logActivity($pdo, $user['id'], 'login', 'User logged in');

    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'full_name' => $user['first_name'] . ' ' . $user['last_name']
        ]
    ]);
}

function handleLogout() {
    if (isset($_SESSION['user_id'])) {
        $pdo = getPDOConnection();
        logActivity($pdo, $_SESSION['user_id'], 'logout', 'User logged out');
    }

    session_destroy();
    echo json_encode(['success' => true]);
}

function checkSession() {
    if (isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role'],
                'full_name' => $_SESSION['full_name']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No active session']);
    }
}

function handleRegister($pdo, $data) {
    // Only admins can register new users
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        return;
    }

    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    $first_name = $data['first_name'] ?? '';
    $last_name = $data['last_name'] ?? '';
    $email = $data['email'] ?? '';
    $role = $data['role'] ?? 'Worker';

    if (empty($username) || empty($password) || empty($first_name) || empty($last_name)) {
        echo json_encode(['success' => false, 'error' => 'Required fields missing']);
        return;
    }

    // Check if username exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Username already exists']);
        return;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $pdo->prepare("
        INSERT INTO users (username, password, first_name, last_name, email, role, is_active)
        VALUES (?, ?, ?, ?, ?, ?, 1)
    ");

    if ($stmt->execute([$username, $hashedPassword, $first_name, $last_name, $email, $role])) {
        $userId = $pdo->lastInsertId();
        logActivity($pdo, $_SESSION['user_id'], 'create_user', "Created user: $username");

        echo json_encode([
            'success' => true,
            'message' => 'User created successfully',
            'user_id' => $userId
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to create user']);
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
        // Fail silently - don't break the main operation
        error_log("Activity log error: " . $e->getMessage());
    }
}

function getPDOConnection() {
    require_once '../config/database.php';
    return getConnection();
}
?>
