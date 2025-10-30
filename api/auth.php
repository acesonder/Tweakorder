<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';

$conn = getDBConnection();

// Helper function to generate unique username
function generateUsername($firstName, $lastName, $dob) {
    // Format: MICBRO050684 (Michael Brown, May 6, 1984)
    $firstInitial = strtoupper(substr($firstName, 0, 3));
    $lastInitial = strtoupper(substr($lastName, 0, 3));
    
    $date = new DateTime($dob);
    $month = $date->format('m');
    $day = $date->format('d');
    $year = $date->format('y');
    
    return $firstInitial . $lastInitial . $month . $day . $year;
}

switch($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $action = $_POST['action'] ?? '';
        
        switch($action) {
            case 'register':
                // Client registration
                $firstName = trim($_POST['first_name'] ?? '');
                $lastName = trim($_POST['last_name'] ?? '');
                $dob = $_POST['date_of_birth'] ?? '';
                $securityQuestion = trim($_POST['security_question'] ?? '');
                $securityAnswer = trim($_POST['security_answer'] ?? '');
                $password = $_POST['password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';
                
                // Validation
                if (empty($firstName) || empty($lastName) || empty($dob) || 
                    empty($securityQuestion) || empty($securityAnswer) || 
                    empty($password) || empty($confirmPassword)) {
                    echo json_encode(['success' => false, 'message' => 'All fields are required']);
                    exit;
                }
                
                if ($password !== $confirmPassword) {
                    echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
                    exit;
                }
                
                if (strlen($password) < 6) {
                    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
                    exit;
                }
                
                // Generate username
                $username = generateUsername($firstName, $lastName, $dob);
                
                // Check if username already exists
                $checkStmt = $conn->prepare("SELECT id FROM clients WHERE username = ?");
                $checkStmt->bind_param("s", $username);
                $checkStmt->execute();
                if ($checkStmt->get_result()->num_rows > 0) {
                    echo json_encode(['success' => false, 'message' => 'A client with this name and date of birth already exists']);
                    exit;
                }
                $checkStmt->close();
                
                // Hash password and security answer
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $securityAnswerHash = password_hash(strtolower($securityAnswer), PASSWORD_DEFAULT);
                
                // Insert client
                $stmt = $conn->prepare("INSERT INTO clients (first_name, last_name, date_of_birth, username, password_hash, security_question, security_answer_hash) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $firstName, $lastName, $dob, $username, $passwordHash, $securityQuestion, $securityAnswerHash);
                
                if ($stmt->execute()) {
                    $clientId = $stmt->insert_id;
                    
                    // Auto-login
                    $_SESSION['client_id'] = $clientId;
                    $_SESSION['client_username'] = $username;
                    $_SESSION['client_name'] = $firstName . ' ' . $lastName;
                    $_SESSION['user_type'] = 'client';
                    
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Registration successful',
                        'username' => $username,
                        'client_id' => $clientId
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $stmt->error]);
                }
                $stmt->close();
                break;
                
            case 'login':
                // Client login
                $username = trim($_POST['username'] ?? '');
                $password = $_POST['password'] ?? '';
                
                if (empty($username) || empty($password)) {
                    echo json_encode(['success' => false, 'message' => 'Username and password are required']);
                    exit;
                }
                
                $stmt = $conn->prepare("SELECT id, first_name, last_name, username, password_hash FROM clients WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
                    exit;
                }
                
                $client = $result->fetch_assoc();
                
                if (password_verify($password, $client['password_hash'])) {
                    $_SESSION['client_id'] = $client['id'];
                    $_SESSION['client_username'] = $client['username'];
                    $_SESSION['client_name'] = $client['first_name'] . ' ' . $client['last_name'];
                    $_SESSION['user_type'] = 'client';
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Login successful',
                        'client_id' => $client['id'],
                        'username' => $client['username'],
                        'name' => $client['first_name'] . ' ' . $client['last_name']
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
                }
                $stmt->close();
                break;
                
            case 'forgot_password_verify':
                // Step 1: Verify identity
                $firstName = trim($_POST['first_name'] ?? '');
                $lastName = trim($_POST['last_name'] ?? '');
                $dob = $_POST['date_of_birth'] ?? '';
                
                if (empty($firstName) || empty($lastName) || empty($dob)) {
                    echo json_encode(['success' => false, 'message' => 'All fields are required']);
                    exit;
                }
                
                $username = generateUsername($firstName, $lastName, $dob);
                
                $stmt = $conn->prepare("SELECT id, security_question FROM clients WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    echo json_encode(['success' => false, 'message' => 'No account found with these details']);
                    exit;
                }
                
                $client = $result->fetch_assoc();
                echo json_encode([
                    'success' => true,
                    'username' => $username,
                    'security_question' => $client['security_question']
                ]);
                $stmt->close();
                break;
                
            case 'forgot_password_reset':
                // Step 2: Verify security answer and reset password
                $username = trim($_POST['username'] ?? '');
                $securityAnswer = trim($_POST['security_answer'] ?? '');
                $newPassword = $_POST['new_password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';
                
                if (empty($username) || empty($securityAnswer) || empty($newPassword) || empty($confirmPassword)) {
                    echo json_encode(['success' => false, 'message' => 'All fields are required']);
                    exit;
                }
                
                if ($newPassword !== $confirmPassword) {
                    echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
                    exit;
                }
                
                if (strlen($newPassword) < 6) {
                    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
                    exit;
                }
                
                $stmt = $conn->prepare("SELECT id, security_answer_hash FROM clients WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    echo json_encode(['success' => false, 'message' => 'Invalid username']);
                    exit;
                }
                
                $client = $result->fetch_assoc();
                
                if (password_verify(strtolower($securityAnswer), $client['security_answer_hash'])) {
                    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                    $updateStmt = $conn->prepare("UPDATE clients SET password_hash = ? WHERE id = ?");
                    $updateStmt->bind_param("si", $newPasswordHash, $client['id']);
                    
                    if ($updateStmt->execute()) {
                        echo json_encode(['success' => true, 'message' => 'Password reset successful']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to reset password']);
                    }
                    $updateStmt->close();
                } else {
                    echo json_encode(['success' => false, 'message' => 'Incorrect security answer']);
                }
                $stmt->close();
                break;
                
            case 'logout':
                session_destroy();
                echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
                break;
                
            case 'check_session':
                if (isset($_SESSION['client_id']) && $_SESSION['user_type'] === 'client') {
                    echo json_encode([
                        'success' => true,
                        'logged_in' => true,
                        'client_id' => $_SESSION['client_id'],
                        'username' => $_SESSION['client_username'],
                        'name' => $_SESSION['client_name']
                    ]);
                } else {
                    echo json_encode([
                        'success' => true,
                        'logged_in' => false
                    ]);
                }
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
                break;
        }
        break;
        
    case 'GET':
        // Check session status
        if (isset($_SESSION['client_id']) && $_SESSION['user_type'] === 'client') {
            echo json_encode([
                'success' => true,
                'logged_in' => true,
                'client_id' => $_SESSION['client_id'],
                'username' => $_SESSION['client_username'],
                'name' => $_SESSION['client_name']
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'logged_in' => false
            ]);
        }
        break;
}

$conn->close();
?>
