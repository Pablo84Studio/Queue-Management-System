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
require_once('../classes/CounterManager.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Actions allowed for staff members
$staff_allowed_actions = ['get_staff_counter', 'get_services'];

// Check if action requires admin role
if (!in_array($action, $staff_allowed_actions) && $_SESSION['role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$counterManager = new CounterManager($conn);

switch ($action) {
    case 'get_all':
        $counters = $counterManager->getAllCounters();
        echo json_encode(['success' => true, 'data' => $counters]);
        break;

    case 'get':
        $counter_id = $_GET['id'] ?? '';
        if (empty($counter_id)) {
            echo json_encode(['success' => false, 'message' => 'Counter ID required']);
            exit;
        }
        
        $counter = $counterManager->getCounterById($counter_id);
        if ($counter) {
            echo json_encode(['success' => true, 'data' => $counter]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Counter not found']);
        }
        break;

    case 'add':
        $name = $_POST['name'] ?? '';
        $service_id = $_POST['service_id'] ?? '';

        if (empty($name) || empty($service_id)) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        $result = $counterManager->addCounter($name, $service_id);
        echo json_encode($result);
        break;

    case 'edit':
        $counter_id = $_POST['counter_id'] ?? '';
        $name = $_POST['name'] ?? '';
        $service_id = $_POST['service_id'] ?? '';
        $status = $_POST['status'] ?? 'closed';

        if (empty($counter_id)) {
            echo json_encode(['success' => false, 'message' => 'Counter ID required']);
            exit;
        }

        $result = $counterManager->updateCounter($counter_id, $name, $service_id, $status);
        echo json_encode($result);
        break;

    case 'delete':
        $counter_id = $_POST['counter_id'] ?? '';
        
        if (empty($counter_id)) {
            echo json_encode(['success' => false, 'message' => 'Counter ID required']);
            exit;
        }

        $result = $counterManager->deleteCounter($counter_id);
        echo json_encode($result);
        break;

    case 'assign_staff':
        $counter_id = $_POST['counter_id'] ?? '';
        $staff_id = $_POST['staff_id'] ?? '';

        if (empty($counter_id)) {
            echo json_encode(['success' => false, 'message' => 'Counter ID required']);
            exit;
        }

        $result = $counterManager->assignStaffToCounter($counter_id, $staff_id);
        echo json_encode($result);
        break;

    case 'set_status':
        $counter_id = $_POST['counter_id'] ?? '';
        $status = $_POST['status'] ?? '';

        if (empty($counter_id) || empty($status)) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        $result = $counterManager->setCounterStatus($counter_id, $status);
        echo json_encode($result);
        break;

    case 'get_active':
        $counters = $counterManager->getActiveCounters();
        echo json_encode(['success' => true, 'data' => $counters]);
        break;

    case 'get_staff_counter':
        // Get counter assigned to current logged-in staff member
        $staff_id = $_SESSION['user_id'] ?? '';
        
        if (empty($staff_id)) {
            echo json_encode(['success' => false, 'message' => 'User ID required']);
            exit;
        }

        $counter = $counterManager->getCounterByStaffId($staff_id);
        if ($counter) {
            echo json_encode(['success' => true, 'data' => $counter]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No counter assigned to this staff member']);
        }
        break;

    case 'get_services':
        // Get all services from database
        $query = "SELECT id, name, status FROM services ORDER BY name ASC";
        $result = $conn->query($query);
        
        if ($result) {
            $services = [];
            while ($row = $result->fetch_assoc()) {
                $services[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $services]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error fetching services']);
        }
        break;

    case 'get_by_service':
        $service_id = $_GET['service_id'] ?? '';
        
        if (empty($service_id)) {
            echo json_encode(['success' => false, 'message' => 'Service ID required']);
            exit;
        }

        $counters = $counterManager->getCountersByService($service_id);
        echo json_encode(['success' => true, 'data' => $counters]);
        break;

    case 'delete_service':
        $service_id = $_POST['service_id'] ?? '';
        
        if (empty($service_id)) {
            echo json_encode(['success' => false, 'message' => 'Service ID required']);
            exit;
        }

        $result = $counterManager->deleteService($service_id);
        echo json_encode($result);
        break;

    case 'assign_admin_service':
        // Assign service to admin
        $admin_id = $_POST['admin_id'] ?? '';
        $service_id = $_POST['service_id'] ?? '';
        
        if (empty($admin_id) || empty($service_id)) {
            echo json_encode(['success' => false, 'message' => 'Admin ID and Service ID required']);
            exit;
        }
        
        $result = $counterManager->assignAdminService($admin_id, $service_id);
        echo json_encode($result);
        break;

    case 'remove_admin_service':
        // Remove service from admin
        $admin_id = $_POST['admin_id'] ?? '';
        $service_id = $_POST['service_id'] ?? '';
        
        if (empty($admin_id) || empty($service_id)) {
            echo json_encode(['success' => false, 'message' => 'Admin ID and Service ID required']);
            exit;
        }
        
        $result = $counterManager->removeAdminService($admin_id, $service_id);
        echo json_encode($result);
        break;

    case 'get_admin_services':
        // Get all services assigned to an admin
        $admin_id = $_GET['admin_id'] ?? $_POST['admin_id'] ?? '';
        
        if (empty($admin_id)) {
            echo json_encode(['success' => false, 'message' => 'Admin ID required']);
            exit;
        }
        
        $services = $counterManager->getServicesByAdmin($admin_id);
        echo json_encode(['success' => true, 'data' => $services]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>