<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'queue_management_system');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Function to execute queries safely
function executeQuery($conn, $query, $params = [], $types = '') {
    if (!empty($params)) {
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt;
    } else {
        return $conn->query($query);
    }
}

// Function to fetch all results as array
function fetchAll($result) {
    if (is_object($result) && method_exists($result, 'fetch_all')) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } elseif (is_object($result) && method_exists($result, 'get_result')) {
        return $result->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

// Function to fetch single row
function fetchOne($result) {
    if (is_object($result) && method_exists($result, 'fetch_assoc')) {
        return $result->fetch_assoc();
    } elseif (is_object($result) && method_exists($result, 'get_result')) {
        return $result->get_result()->fetch_assoc();
    }
    return null;
}

// Session handling
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
