<?php
header('Content-Type: application/json');

// Suppress HTML error output
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Capture fatal errors and return JSON
register_shutdown_function(function() {
    $err = error_get_last();
    // Only handle fatal errors (not warnings, notices, etc.)
    if ($err !== null && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        if (!headers_sent()) {
            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'message' => 'Server error: ' . $err['message'],
                'error_type' => $err['type']
            ]);
        }
        exit;
    }
});

require_once('../config/database.php');
require_once('../classes/UserManager.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$userManager = new UserManager($conn);

switch ($action) {
    case 'get':
        $staff_id = $_GET['id'] ?? '';
        if (empty($staff_id)) {
            echo json_encode(['success' => false, 'message' => 'Staff ID required']);
            exit;
        }
        
        $staff = $userManager->getUserById($staff_id);
        if ($staff) {
            echo json_encode(['success' => true, 'data' => $staff]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Staff not found']);
        }
        break;

    case 'add':
        $name = $_POST['name'] ?? '';
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $status = $_POST['status'] ?? 'active';

        if (empty($name) || empty($username) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        $result = $userManager->register($username, $email, $password, $name, 'staff');
        echo json_encode($result);
        break;

    case 'edit':
        $staff_id = $_POST['staff_id'] ?? '';
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $status = $_POST['status'] ?? 'active';

        if (empty($staff_id)) {
            echo json_encode(['success' => false, 'message' => 'Staff ID required']);
            exit;
        }

        $result = $userManager->updateUser($staff_id, $name, $email, 'staff', $status);
        echo json_encode($result);
        break;

    case 'delete':
        $staff_id = $_POST['staff_id'] ?? '';
        
        if (empty($staff_id)) {
            echo json_encode(['success' => false, 'message' => 'Staff ID required']);
            exit;
        }

        $result = $userManager->deleteUser($staff_id);
        echo json_encode($result);
        break;

    case 'get_all':
        $staff_members = $userManager->getAllUsers('staff');
        echo json_encode(['success' => true, 'data' => $staff_members]);
        break;

    case 'change_password':
        $staff_id = $_POST['staff_id'] ?? '';
        $old_password = $_POST['old_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';

        if (empty($staff_id) || empty($old_password) || empty($new_password)) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        $result = $userManager->changePassword($staff_id, $old_password, $new_password);
        echo json_encode($result);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>