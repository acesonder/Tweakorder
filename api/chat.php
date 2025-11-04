<?php
// ============================================
// Chat API Endpoint
// File: api/chat.php
// Description: Handles chat operations and real-time messaging
// ============================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

// Start session for authentication
session_start();

$conn = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

// Helper function to get current user/client info
function getCurrentUser() {
    // For demo purposes, we'll use a default user
    // In production, this should check session authentication
    if (isset($_SESSION['user_id'])) {
        return [
            'type' => 'staff',
            'id' => $_SESSION['user_id']
        ];
    } elseif (isset($_SESSION['client_id'])) {
        return [
            'type' => 'client',
            'id' => $_SESSION['client_id']
        ];
    }
    // Default to admin user for demo
    return [
        'type' => 'staff',
        'id' => 1
    ];
}

switch($method) {
    case 'GET':
        handleGet($conn);
        break;
    case 'POST':
        handlePost($conn);
        break;
    case 'PUT':
        handlePut($conn);
        break;
    case 'DELETE':
        handleDelete($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}

function handleGet($conn) {
    $action = $_GET['action'] ?? 'list_conversations';
    $currentUser = getCurrentUser();
    
    switch($action) {
        case 'list_conversations':
            listConversations($conn, $currentUser);
            break;
        case 'get_conversation':
            getConversation($conn, $_GET['id'] ?? 0, $currentUser);
            break;
        case 'get_messages':
            getMessages($conn, $_GET['conversation_id'] ?? 0, $currentUser);
            break;
        case 'unread_count':
            getUnreadCount($conn, $currentUser);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Unknown action']);
    }
}

function handlePost($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    $currentUser = getCurrentUser();
    
    switch($action) {
        case 'create_conversation':
            createConversation($conn, $data, $currentUser);
            break;
        case 'send_message':
            sendMessage($conn, $data, $currentUser);
            break;
        case 'mark_read':
            markAsRead($conn, $data, $currentUser);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Unknown action']);
    }
}

function handlePut($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    
    switch($action) {
        case 'archive_conversation':
            archiveConversation($conn, $data['id'] ?? 0);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Unknown action']);
    }
}

function handleDelete($conn) {
    parse_str(file_get_contents('php://input'), $data);
    $messageId = $data['message_id'] ?? 0;
    deleteMessage($conn, $messageId);
}

// Function implementations

function listConversations($conn, $currentUser) {
    $userField = $currentUser['type'] === 'staff' ? 'user_id' : 'client_id';
    $userId = $currentUser['id'];
    
    $sql = "SELECT DISTINCT c.*, 
            (SELECT COUNT(*) FROM messages WHERE conversation_id = c.id) as message_count,
            (SELECT message_text FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message,
            (SELECT created_at FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message_time
            FROM conversations c
            INNER JOIN conversation_participants cp ON c.id = cp.conversation_id
            WHERE cp.$userField = ? AND cp.is_active = 1 AND c.is_archived = 0
            ORDER BY c.updated_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $conversations = [];
    while($row = $result->fetch_assoc()) {
        // Get participants
        $partSql = "SELECT cp.*, u.first_name as staff_first, u.last_name as staff_last,
                    c.first_name as client_first, c.last_name as client_last
                    FROM conversation_participants cp
                    LEFT JOIN users u ON cp.user_id = u.id
                    LEFT JOIN clients c ON cp.client_id = c.id
                    WHERE cp.conversation_id = ? AND cp.is_active = 1";
        $partStmt = $conn->prepare($partSql);
        $partStmt->bind_param("i", $row['id']);
        $partStmt->execute();
        $partResult = $partStmt->get_result();
        
        $participants = [];
        while($part = $partResult->fetch_assoc()) {
            $participants[] = [
                'id' => $part['participant_type'] === 'staff' ? $part['user_id'] : $part['client_id'],
                'type' => $part['participant_type'],
                'name' => $part['participant_type'] === 'staff' ? 
                    $part['staff_first'] . ' ' . $part['staff_last'] :
                    $part['client_first'] . ' ' . $part['client_last']
            ];
        }
        
        $row['participants'] = $participants;
        $conversations[] = $row;
    }
    
    echo json_encode(['success' => true, 'conversations' => $conversations]);
}

function getConversation($conn, $conversationId, $currentUser) {
    $sql = "SELECT * FROM conversations WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $conversationId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($row = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'conversation' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Conversation not found']);
    }
}

function getMessages($conn, $conversationId, $currentUser) {
    $sql = "SELECT m.*, 
            u.first_name as staff_first, u.last_name as staff_last,
            c.first_name as client_first, c.last_name as client_last
            FROM messages m
            LEFT JOIN users u ON m.sender_id = u.id
            LEFT JOIN clients c ON m.sender_client_id = c.id
            WHERE m.conversation_id = ? AND m.is_deleted = 0
            ORDER BY m.created_at ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $conversationId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while($row = $result->fetch_assoc()) {
        $row['sender_name'] = $row['sender_type'] === 'staff' ?
            $row['staff_first'] . ' ' . $row['staff_last'] :
            $row['client_first'] . ' ' . $row['client_last'];
        
        // Check if current user has read this message
        $readSql = "SELECT id FROM message_read_status WHERE message_id = ? AND " .
                   ($currentUser['type'] === 'staff' ? 'user_id' : 'client_id') . " = ?";
        $readStmt = $conn->prepare($readSql);
        $readStmt->bind_param("ii", $row['id'], $currentUser['id']);
        $readStmt->execute();
        $row['is_read'] = $readStmt->get_result()->num_rows > 0;
        
        $messages[] = $row;
    }
    
    echo json_encode(['success' => true, 'messages' => $messages]);
}

function getUnreadCount($conn, $currentUser) {
    $userField = $currentUser['type'] === 'staff' ? 'user_id' : 'client_id';
    $userId = $currentUser['id'];
    
    $sql = "SELECT COUNT(DISTINCT cn.conversation_id) as unread_conversations,
            COUNT(cn.id) as unread_messages
            FROM chat_notifications cn
            WHERE cn.$userField = ? AND cn.is_read = 0";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    echo json_encode(['success' => true, 'data' => $row]);
}

function createConversation($conn, $data, $currentUser) {
    $title = $data['title'] ?? 'New Conversation';
    $type = $data['type'] ?? 'direct';
    $participants = $data['participants'] ?? [];
    
    $sql = "INSERT INTO conversations (title, type, created_by) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $type, $currentUser['id']);
    
    if($stmt->execute()) {
        $conversationId = $stmt->insert_id;
        
        // Add creator as participant
        $partSql = "INSERT INTO conversation_participants (conversation_id, " . 
                   ($currentUser['type'] === 'staff' ? 'user_id' : 'client_id') . 
                   ", participant_type) VALUES (?, ?, ?)";
        $partStmt = $conn->prepare($partSql);
        $partStmt->bind_param("iis", $conversationId, $currentUser['id'], $currentUser['type']);
        $partStmt->execute();
        
        // Add other participants
        foreach($participants as $participant) {
            $partType = $participant['type'];
            $partId = $participant['id'];
            $partField = $partType === 'staff' ? 'user_id' : 'client_id';
            
            $partSql = "INSERT INTO conversation_participants (conversation_id, $partField, participant_type) VALUES (?, ?, ?)";
            $partStmt = $conn->prepare($partSql);
            $partStmt->bind_param("iis", $conversationId, $partId, $partType);
            $partStmt->execute();
        }
        
        echo json_encode(['success' => true, 'message' => 'Conversation created', 'id' => $conversationId]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create conversation']);
    }
}

function sendMessage($conn, $data, $currentUser) {
    $conversationId = $data['conversation_id'] ?? 0;
    $messageText = $data['message'] ?? '';
    $messageType = $data['type'] ?? 'text';
    
    if(empty($messageText)) {
        echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
        return;
    }
    
    $senderField = $currentUser['type'] === 'staff' ? 'sender_id' : 'sender_client_id';
    
    $sql = "INSERT INTO messages (conversation_id, $senderField, sender_type, message_text, message_type) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisss", $conversationId, $currentUser['id'], $currentUser['type'], $messageText, $messageType);
    
    if($stmt->execute()) {
        $messageId = $stmt->insert_id;
        
        // Create notifications for other participants
        $partSql = "SELECT * FROM conversation_participants 
                    WHERE conversation_id = ? AND is_active = 1 AND NOT (" .
                    ($currentUser['type'] === 'staff' ? 'user_id' : 'client_id') . " = ? AND participant_type = ?)";
        $partStmt = $conn->prepare($partSql);
        $partStmt->bind_param("iis", $conversationId, $currentUser['id'], $currentUser['type']);
        $partStmt->execute();
        $partResult = $partStmt->get_result();
        
        while($part = $partResult->fetch_assoc()) {
            $recipientField = $part['participant_type'] === 'staff' ? 'user_id' : 'client_id';
            $recipientId = $part['participant_type'] === 'staff' ? $part['user_id'] : $part['client_id'];
            
            $notifSql = "INSERT INTO chat_notifications (conversation_id, message_id, $recipientField, recipient_type, notification_type)
                         VALUES (?, ?, ?, ?, 'new_message')";
            $notifStmt = $conn->prepare($notifSql);
            $notifStmt->bind_param("iiis", $conversationId, $messageId, $recipientId, $part['participant_type']);
            $notifStmt->execute();
        }
        
        echo json_encode(['success' => true, 'message' => 'Message sent', 'id' => $messageId]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send message']);
    }
}

function markAsRead($conn, $data, $currentUser) {
    $messageId = $data['message_id'] ?? 0;
    $userField = $currentUser['type'] === 'staff' ? 'user_id' : 'client_id';
    
    $sql = "INSERT IGNORE INTO message_read_status (message_id, $userField, reader_type) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $messageId, $currentUser['id'], $currentUser['type']);
    
    if($stmt->execute()) {
        // Mark notification as read
        $notifSql = "UPDATE chat_notifications SET is_read = 1 
                     WHERE message_id = ? AND $userField = ?";
        $notifStmt = $conn->prepare($notifSql);
        $notifStmt->bind_param("ii", $messageId, $currentUser['id']);
        $notifStmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Marked as read']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to mark as read']);
    }
}

function archiveConversation($conn, $conversationId) {
    $sql = "UPDATE conversations SET is_archived = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $conversationId);
    
    if($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Conversation archived']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to archive conversation']);
    }
}

function deleteMessage($conn, $messageId) {
    $sql = "UPDATE messages SET is_deleted = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $messageId);
    
    if($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Message deleted']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete message']);
    }
}

$conn->close();

// ============================================
// End of File: api/chat.php
// ============================================
?>
