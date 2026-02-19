<?php
header('Content-Type: application/json');

// Suppress HTML error output and ensure errors are returned as JSON
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
                'error_type' => $err['type'],
                'file' => $err['file'],
                'line' => $err['line']
            ]);
        }
        exit;
    }
});

require_once('../config/database.php');
require_once('../classes/QueueManager.php');

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
$queueManager = new QueueManager($conn);

switch ($action) {
    case 'add':
        $customer_name = $_POST['customer_name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $service_id = $_POST['service_id'] ?? '';

        if (empty($customer_name) || empty($service_id)) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        // Check permissions: staff can add, admin can add only for their services
        if ($_SESSION['role'] === 'admin') {
            // Check if admin is in charge of this service
            $check_query = "SELECT id FROM admin_services WHERE admin_id = ? AND service_id = ?";
            $stmt = $conn->prepare($check_query);
            if (!$stmt) {
                echo json_encode(['success' => false, 'message' => 'Database error']);
                exit;
            }
            $stmt->bind_param("ii", $_SESSION['user_id'], $service_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'You are not in charge of this service']);
                exit;
            }
        } else if ($_SESSION['role'] !== 'staff') {
            // Only admin and staff can add to queue
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $result = $queueManager->addToQueue($customer_name, $phone, $service_id);
        echo json_encode($result);
        break;

    case 'get_current':
        $service_id = $_GET['service_id'] ?? null;
        $queue = $queueManager->getCurrentQueue($service_id);
        echo json_encode(['success' => true, 'data' => $queue]);
        break;

    case 'call_next':
        $counter_id = $_POST['counter_id'] ?? '';
        
        if (empty($counter_id)) {
            echo json_encode(['success' => false, 'message' => 'Counter ID required']);
            exit;
        }

        $result = $queueManager->callNextCustomer($counter_id);
        echo json_encode($result);
        break;

    case 'complete':
        $queue_id = $_POST['queue_id'] ?? '';
        
        if (empty($queue_id)) {
            echo json_encode(['success' => false, 'message' => 'Queue ID required']);
            exit;
        }

        $result = $queueManager->completeService($queue_id);
        echo json_encode($result);
        break;

    case 'cancel':
        $queue_id = $_POST['queue_id'] ?? '';
        
        if (empty($queue_id)) {
            echo json_encode(['success' => false, 'message' => 'Queue ID required']);
            exit;
        }

        $result = $queueManager->cancelFromQueue($queue_id);
        echo json_encode($result);
        break;

    case 'get_statistics':
        $start_date = $_GET['start_date'] ?? date('Y-m-d');
        $end_date = $_GET['end_date'] ?? date('Y-m-d');
        $service_id = $_GET['service_id'] ?? null;

        $stats = $queueManager->getStatistics($start_date, $end_date, $service_id);
        echo json_encode(['success' => true, 'data' => $stats]);
        break;

    case 'get_avg_wait_time':
        $date = $_GET['date'] ?? date('Y-m-d');
        $avg_wait = $queueManager->getAverageWaitTime($date);
        echo json_encode(['success' => true, 'data' => $avg_wait]);
        break;

    case 'get_admin_services':
        // Get services assigned to current admin
        if ($_SESSION['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Only admins can access this']);
            exit;
        }
        
        $services = $queueManager->getAdminServices($_SESSION['user_id']);
        echo json_encode(['success' => true, 'data' => $services]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>