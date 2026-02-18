<?php
header('Content-Type: application/json');

require_once('../config/database.php');
require_once('../classes/CounterManager.php');

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

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
