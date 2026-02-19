<?php
// User/Authentication Management Class

class UserManager {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    // Login user
    public function login($username, $password) {
        try {
            $query = "SELECT id, username, email, password, role, name, status FROM users WHERE username = ? AND status = 'active'";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                return ['success' => false, 'message' => 'Invalid username or password'];
            }
            
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];
                
                return ['success' => true, 'message' => 'Login successful', 'user' => $user];
            } else {
                return ['success' => false, 'message' => 'Invalid username or password'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Logout user
    public function logout() {
        session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully'];
    }
    
    // Register new user
    public function register($username, $email, $password, $name, $role = 'staff') {
        try {
            // Check if user exists
            $check_query = "SELECT id FROM users WHERE username = ? OR email = ?";
            $stmt = $this->conn->prepare($check_query);
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows > 0) {
                return ['success' => false, 'message' => 'Username or email already exists'];
            }
            
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            $insert_query = "INSERT INTO users (username, email, password, role, name, status) VALUES (?, ?, ?, ?, ?, 'active')";
            $stmt = $this->conn->prepare($insert_query);
            $stmt->bind_param("sssss", $username, $email, $hashed_password, $role, $name);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'User created successfully',
                    'user_id' => $stmt->insert_id
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to create user'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Get all users
    public function getAllUsers($role = null) {
        $query = "SELECT id, username, email, role, name, status, created_at FROM users";
        
        if ($role) {
            $query .= " WHERE role = '$role'";
        }
        
        $query .= " ORDER BY created_at DESC";
        
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get user by ID
    public function getUserById($user_id) {
        $query = "SELECT id, username, email, role, name, status, created_at FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }
    
    // Update user
    public function updateUser($user_id, $name, $email, $role, $status) {
        try {
            $query = "UPDATE users SET name = ?, email = ?, role = ?, status = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssssi", $name, $email, $role, $status, $user_id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'User updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update user'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Delete user
    public function deleteUser($user_id) {
        try {
            // Check if admin
            if ($user_id == 1) {
                return ['success' => false, 'message' => 'Cannot delete administrator'];
            }
            
            $query = "DELETE FROM users WHERE id = ? AND id != 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'User deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete user'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Change password
    public function changePassword($user_id, $old_password, $new_password) {
        try {
            $query = "SELECT password FROM users WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            
            $user = $stmt->get_result()->fetch_assoc();
            
            if (!password_verify($old_password, $user['password'])) {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }
            
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            
            $update_query = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $this->conn->prepare($update_query);
            $stmt->bind_param("si", $hashed_password, $user_id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Password changed successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to change password'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Check if user is logged in
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    // Check if user is admin
    public static function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    
    // Get staff statistics
    public function getStaffStatistics($start_date, $end_date) {
        $query = "SELECT 
                    u.id,
                    u.username,
                    u.name,
                    COUNT(DISTINCT sa.id) as total_activities,
                    SUM(CASE WHEN sa.activity_type = 'called_customer' THEN 1 ELSE 0 END) as customers_called,
                    SUM(CASE WHEN sa.activity_type = 'completed_service' THEN 1 ELSE 0 END) as services_completed
                  FROM users u
                  LEFT JOIN staff_activity sa ON u.id = sa.staff_id AND DATE(sa.timestamp) BETWEEN ? AND ?
                  WHERE u.role = 'staff'
                  GROUP BY u.id
                  ORDER BY services_completed DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $start_date, $end_date);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>