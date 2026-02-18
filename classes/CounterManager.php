<?php
// Counter Management Class

class CounterManager {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    // Get all counters
    public function getAllCounters() {
        $query = "SELECT 
                    c.id,
                    c.name,
                    c.status,
                    s.name as service_name,
                    u.name as staff_name,
                    u.id as staff_id,
                    (SELECT COUNT(*) FROM queue WHERE counter_id = c.id AND status IN ('waiting', 'called')) as waiting_count
                  FROM counters c
                  JOIN services s ON c.service_id = s.id
                  LEFT JOIN users u ON c.assigned_staff_id = u.id
                  ORDER BY c.name";
        
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get counter by ID
    public function getCounterById($counter_id) {
        $query = "SELECT 
                    c.*,
                    s.name as service_name,
                    u.name as staff_name,
                    u.id as staff_id
                  FROM counters c
                  JOIN services s ON c.service_id = s.id
                  LEFT JOIN users u ON c.assigned_staff_id = u.id
                  WHERE c.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $counter_id);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }
    
    // Add new counter
    public function addCounter($name, $service_id) {
        try {
            $query = "INSERT INTO counters (name, service_id, status) VALUES (?, ?, 'closed')";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("si", $name, $service_id);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'counter_id' => $stmt->insert_id,
                    'message' => 'Counter created successfully'
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to create counter'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Update counter
    public function updateCounter($counter_id, $name, $service_id, $status) {
        try {
            $query = "UPDATE counters SET name = ?, service_id = ?, status = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sisi", $name, $service_id, $status, $counter_id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Counter updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update counter'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Assign staff to counter
    public function assignStaffToCounter($counter_id, $staff_id) {
        try {
            $query = "UPDATE counters SET assigned_staff_id = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ii", $staff_id, $counter_id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Staff assigned successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to assign staff'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Open/Close counter
    public function setCounterStatus($counter_id, $status) {
        try {
            if (!in_array($status, ['open', 'closed', 'break'])) {
                return ['success' => false, 'message' => 'Invalid status'];
            }
            
            $query = "UPDATE counters SET status = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("si", $status, $counter_id);
            
            if ($stmt->execute()) {
                // Log activity
                $activity_type = $status === 'break' ? 'break' : ($status === 'open' ? 'login' : 'logout');
                $this->logActivity($counter_id, null, $activity_type);
                
                return ['success' => true, 'message' => "Counter status changed to $status"];
            } else {
                return ['success' => false, 'message' => 'Failed to update counter status'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Get active counters
    public function getActiveCounters() {
        $query = "SELECT 
                    c.id,
                    c.name,
                    c.status,
                    s.name as service_name,
                    u.name as staff_name,
                    (SELECT COUNT(*) FROM queue WHERE counter_id = c.id AND status = 'called') as in_service_count
                  FROM counters c
                  JOIN services s ON c.service_id = s.id
                  LEFT JOIN users u ON c.assigned_staff_id = u.id
                  WHERE c.status IN ('open', 'break')
                  ORDER BY c.name";
        
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Delete counter
    public function deleteCounter($counter_id) {
        try {
            // Check if counter has active queue
            $check_query = "SELECT COUNT(*) as count FROM queue WHERE counter_id = ? AND status IN ('waiting', 'called')";
            $stmt = $this->conn->prepare($check_query);
            $stmt->bind_param("i", $counter_id);
            $stmt->execute();
            
            $result = $stmt->get_result()->fetch_assoc();
            if ($result['count'] > 0) {
                return ['success' => false, 'message' => 'Cannot delete counter with active queue items'];
            }
            
            $query = "DELETE FROM counters WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $counter_id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Counter deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete counter'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Log activity
    private function logActivity($counter_id, $queue_id, $activity_type) {
        $staff_id = $_SESSION['user_id'] ?? null;
        if (!$staff_id) return;
        
        $query = "INSERT INTO staff_activity (staff_id, counter_id, queue_id, activity_type) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiis", $staff_id, $counter_id, $queue_id, $activity_type);
        $stmt->execute();
    }
}
?>
