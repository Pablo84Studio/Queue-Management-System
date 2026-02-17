<?php
header('Content-Type: application/json');

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

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
