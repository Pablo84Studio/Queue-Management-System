<?php
header('Content-Type: application/json');

require_once('../config/database.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'get_services':
        // Get all services
        $query = "SELECT id, name, status FROM services WHERE status = 'active' ORDER BY name";
        $result = $conn->query($query);
        $services = $result->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $services]);
        break;

    case 'get_staff':
        // Get all active staff
        $query = "SELECT id, name, username FROM users WHERE role = 'staff' AND status = 'active' ORDER BY name";
        $result = $conn->query($query);
        $staff = $result->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $staff]);
        break;

    case 'get_counters':
        // Get all counters
        $query = "SELECT c.id, c.name, s.name as service_name, c.status FROM counters c 
                  JOIN services s ON c.service_id = s.id 
                  ORDER BY c.name";
        $result = $conn->query($query);
        $counters = $result->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $counters]);
        break;

    case 'get_dashboard_stats':
        // Get dashboard statistics
        $today = date('Y-m-d');
        
        // Total customers today
        $query = "SELECT COUNT(*) as count FROM queue WHERE DATE(created_at) = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $totalResult = $stmt->get_result()->fetch_assoc();
        
        // Completed today
        $query = "SELECT COUNT(*) as count FROM queue WHERE DATE(created_at) = ? AND status = 'completed'";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $completedResult = $stmt->get_result()->fetch_assoc();
        
        // Waiting
        $query = "SELECT COUNT(*) as count FROM queue WHERE status = 'waiting'";
        $result = $conn->query($query);
        $waitingResult = $result->fetch_assoc();
        
        // Average wait time
        $query = "SELECT AVG(TIMESTAMPDIFF(MINUTE, created_at, called_at)) as avg_wait 
                  FROM queue WHERE DATE(created_at) = ? AND called_at IS NOT NULL";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $avgWaitResult = $stmt->get_result()->fetch_assoc();
        
        echo json_encode([
            'success' => true,
            'data' => [
                'total_customers' => $totalResult['count'],
                'completed_customers' => $completedResult['count'],
                'waiting_customers' => $waitingResult['count'],
                'avg_wait_time' => round($avgWaitResult['avg_wait'] ?? 0, 1)
            ]
        ]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
