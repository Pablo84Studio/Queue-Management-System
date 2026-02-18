<?php
// Queue Management Class

class QueueManager {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    // Add new customer to queue
    public function addToQueue($customer_name, $phone, $service_id) {
        try {
            // Get next ticket number
            $result = $this->conn->query("SELECT MAX(ticket_number) as max_ticket FROM queue WHERE DATE(created_at) = CURDATE()");
            if (!$result) {
                return ['success' => false, 'message' => 'Database error: ' . $this->conn->error];
            }
            $row = $result->fetch_assoc();
            $next_ticket = ($row['max_ticket'] ?? 0) + 1;
            
            $query = "INSERT INTO queue (ticket_number, customer_name, phone, service_id, status) VALUES (?, ?, ?, ?, 'waiting')";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                return ['success' => false, 'message' => 'Prepare failed: ' . $this->conn->error];
            }

            if (!$stmt->bind_param("issi", $next_ticket, $customer_name, $phone, $service_id)) {
                return ['success' => false, 'message' => 'Bind failed: ' . $stmt->error];
            }

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'queue_id' => $this->conn->insert_id,
                    'ticket_number' => $next_ticket,
                    'message' => 'Successfully added to queue'
                ];
            } else {
                return ['success' => false, 'message' => 'Execute failed: ' . $stmt->error];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Get current queue
    public function getCurrentQueue($service_id = null, $limit = 50) {
        $query = "SELECT q.*, s.name as service_name, c.name as counter_name FROM queue q 
                  JOIN services s ON q.service_id = s.id 
                  LEFT JOIN counters c ON q.counter_id = c.id 
                  WHERE q.status IN ('waiting', 'called')";
        
        if ($service_id) {
            $query .= " AND q.service_id = $service_id";
        }
        
        $query .= " ORDER BY q.created_at ASC LIMIT $limit";
        
        $result = $this->conn->query($query);
        if (!$result) {
            // Return empty array on error and include message for debugging
            error_log('QueueManager::getCurrentQueue DB error: ' . $this->conn->error);
            return [];
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Call next customer
    public function callNextCustomer($counter_id) {
        try {
            // Get counter details and service
            $counter_query = "SELECT service_id FROM counters WHERE id = ?";
            $stmt = $this->conn->prepare($counter_query);
            if (!$stmt) {
                return ['success' => false, 'message' => 'Prepare failed (counter query): ' . $this->conn->error];
            }
            if (!$stmt->bind_param("i", $counter_id)) {
                return ['success' => false, 'message' => 'Bind failed (counter query): ' . $stmt->error];
            }
            if (!$stmt->execute()) {
                return ['success' => false, 'message' => 'Execute failed (counter query): ' . $stmt->error];
            }
            $res = $stmt->get_result();
            if (!$res) {
                return ['success' => false, 'message' => 'Get result failed (counter query): ' . $this->conn->error];
            }
            $counter = $res->fetch_assoc();

            if (!$counter) {
                return ['success' => false, 'message' => 'Counter not found'];
            }
            
            // Get next waiting customer for this service
            $queue_query = "SELECT id, ticket_number FROM queue WHERE status = 'waiting' AND service_id = ? ORDER BY created_at ASC LIMIT 1";
            $stmt = $this->conn->prepare($queue_query);
            if (!$stmt) {
                return ['success' => false, 'message' => 'Prepare failed (queue query): ' . $this->conn->error];
            }
            if (!$stmt->bind_param("i", $counter['service_id'])) {
                return ['success' => false, 'message' => 'Bind failed (queue query): ' . $stmt->error];
            }
            if (!$stmt->execute()) {
                return ['success' => false, 'message' => 'Execute failed (queue query): ' . $stmt->error];
            }
            $res = $stmt->get_result();
            if (!$res) {
                return ['success' => false, 'message' => 'Get result failed (queue query): ' . $this->conn->error];
            }
            $queue = $res->fetch_assoc();

            if (!$queue) {
                return ['success' => false, 'message' => 'No customers waiting'];
            }
            
            // Update queue status and assign to counter
            $update_query = "UPDATE queue SET status = 'called', called_at = NOW(), counter_id = ? WHERE id = ?";
            $stmt = $this->conn->prepare($update_query);
            if (!$stmt) {
                return ['success' => false, 'message' => 'Prepare failed (update query): ' . $this->conn->error];
            }
            if (!$stmt->bind_param("ii", $counter_id, $queue['id'])) {
                return ['success' => false, 'message' => 'Bind failed (update query): ' . $stmt->error];
            }
            if ($stmt->execute()) {
                // Log activity
                $this->logActivity($counter_id, $queue['id'], 'called_customer');

                // Optionally fetch full queue row to return more info
                $rowRes = $this->conn->query("SELECT q.*, s.name as service_name FROM queue q JOIN services s ON q.service_id = s.id WHERE q.id = " . intval($queue['id']));
                $full = $rowRes ? $rowRes->fetch_assoc() : null;

                return [
                    'success' => true,
                    'queue_id' => $queue['id'],
                    'ticket_number' => $queue['ticket_number'],
                    'data' => $full,
                    'message' => 'Customer called successfully'
                ];
            } else {
                return ['success' => false, 'message' => 'Execute failed (update query): ' . $stmt->error];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Complete service
    public function completeService($queue_id) {
        try {
            $update_query = "UPDATE queue SET status = 'completed', completed_at = NOW() WHERE id = ?";
            $stmt = $this->conn->prepare($update_query);
            $stmt->bind_param("i", $queue_id);
            
            if ($stmt->execute()) {
                $this->logActivity(null, $queue_id, 'completed_service');
                return ['success' => true, 'message' => 'Service completed'];
            } else {
                return ['success' => false, 'message' => 'Failed to complete service'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Cancel from queue
    public function cancelFromQueue($queue_id) {
        try {
            $update_query = "UPDATE queue SET status = 'cancelled' WHERE id = ?";
            $stmt = $this->conn->prepare($update_query);
            $stmt->bind_param("i", $queue_id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Customer removed from queue'];
            } else {
                return ['success' => false, 'message' => 'Failed to remove customer'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Get queue statistics for date range
    public function getStatistics($start_date, $end_date, $service_id = null) {
        $query = "SELECT 
                    COUNT(*) as total_customers,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_customers,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_customers,
                    AVG(EXTRACT(EPOCH FROM (called_at - created_at))) as avg_wait_time,
                    AVG(EXTRACT(EPOCH FROM (completed_at - called_at))) as avg_service_time
                  FROM queue 
                  WHERE DATE(created_at) BETWEEN ? AND ?";
        
        if ($service_id) {
            $query .= " AND service_id = ?";
        }
        
        $stmt = $this->conn->prepare($query);
        if ($service_id) {
            $stmt->bind_param("ssi", $start_date, $end_date, $service_id);
        } else {
            $stmt->bind_param("ss", $start_date, $end_date);
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // Get queue history
    public function getQueueHistory($start_date, $end_date, $service_id = null, $status = null) {
        $query = "SELECT q.*, s.name as service_name, c.name as counter_name 
                  FROM queue q 
                  JOIN services s ON q.service_id = s.id 
                  LEFT JOIN counters c ON q.counter_id = c.id 
                  WHERE DATE(q.created_at) BETWEEN ? AND ?";
        
        $params = [$start_date, $end_date];
        $types = "ss";
        
        if ($service_id) {
            $query .= " AND q.service_id = ?";
            $params[] = $service_id;
            $types .= "i";
        }
        
        if ($status) {
            $query .= " AND q.status = ?";
            $params[] = $status;
            $types .= "s";
        }
        
        $query .= " ORDER BY q.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    // Log staff activity
    private function logActivity($counter_id = null, $queue_id = null, $activity_type) {
        $staff_id = $_SESSION['user_id'] ?? null;
        if (!$staff_id) return;
        
        $query = "INSERT INTO staff_activity (staff_id, counter_id, queue_id, activity_type) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiis", $staff_id, $counter_id, $queue_id, $activity_type);
        $stmt->execute();
    }
    
    // Get average wait time
    public function getAverageWaitTime($date = null) {
        if (!$date) $date = date('Y-m-d');
        
        $query = "SELECT AVG(TIMESTAMPDIFF(MINUTE, created_at, called_at)) as avg_wait_minutes 
                  FROM queue 
                  WHERE DATE(created_at) = ? AND called_at IS NOT NULL";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $date);
        $stmt->execute();
        
        $result = $stmt->get_result()->fetch_assoc();
        return round($result['avg_wait_minutes'] ?? 0, 2);
    }
}
?>
