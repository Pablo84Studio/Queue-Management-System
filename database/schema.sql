-- Queue Management System Database Schema

-- Create Users table (Admin & Staff)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',
    name VARCHAR(150) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create Services table (Counter types)
CREATE TABLE IF NOT EXISTS services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Counter/Window table
CREATE TABLE IF NOT EXISTS counters (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    service_id INT NOT NULL,
    assigned_staff_id INT,
    status ENUM('open', 'closed', 'break') DEFAULT 'closed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id),
    FOREIGN KEY (assigned_staff_id) REFERENCES users(id)
);

-- Create Queue table
CREATE TABLE IF NOT EXISTS queue (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ticket_number INT NOT NULL UNIQUE,
    customer_name VARCHAR(150),
    phone VARCHAR(20),
    service_id INT NOT NULL,
    counter_id INT,
    status ENUM('waiting', 'called', 'in_service', 'completed', 'cancelled') DEFAULT 'waiting',
    called_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id),
    FOREIGN KEY (counter_id) REFERENCES counters(id),
    INDEX (status),
    INDEX (created_at)
);

-- Create Staff Activity Log
CREATE TABLE IF NOT EXISTS staff_activity (
    id INT PRIMARY KEY AUTO_INCREMENT,
    staff_id INT NOT NULL,
    counter_id INT,
    queue_id INT,
    activity_type ENUM('login', 'logout', 'called_customer', 'completed_service', 'break') NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (staff_id) REFERENCES users(id),
    FOREIGN KEY (counter_id) REFERENCES counters(id),
    FOREIGN KEY (queue_id) REFERENCES queue(id)
);

-- Create Daily Statistics
CREATE TABLE IF NOT EXISTS daily_stats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    date DATE NOT NULL UNIQUE,
    total_customers INT DEFAULT 0,
    completed_customers INT DEFAULT 0,
    cancelled_customers INT DEFAULT 0,
    avg_wait_time INT DEFAULT 0,
    avg_service_time INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Admin Services Assignment (which admin manages which service)
CREATE TABLE IF NOT EXISTS admin_services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT NOT NULL,
    service_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    UNIQUE KEY unique_admin_service (admin_id, service_id),
    INDEX idx_admin_id (admin_id),
    INDEX idx_service_id (service_id)
);

-- Create Indexes for better query performance
CREATE INDEX idx_queue_status_date ON queue(status, created_at);
CREATE INDEX idx_queue_service ON queue(service_id);
CREATE INDEX idx_counter_status ON counters(status);
CREATE INDEX idx_staff_activity_date ON staff_activity(timestamp);

-- Insert sample services
INSERT IGNORE INTO services (id, name, description, status) VALUES
(1, 'General Inquiry', 'General questions and information', 'active'),
(2, 'Registration', 'Customer registration and enrollment', 'active'),
(3, 'Payment Processing', 'Payment and billing services', 'active'),
(4, 'Troubleshooting', 'Technical support and assistance', 'active'),
(5, 'Document Submission', 'Document handling and submission', 'active');

-- Insert sample admin user (password: admin123)
INSERT IGNORE INTO users (id, username, email, password, role, name, status) VALUES
(1, 'admin', 'admin@queuemanagement.com', '$2y$10$Y9Z7.L8v9S4q1P2w3E5r6.kM8n7V9x5B3d4C5f6G7h8I9j0K1L2', 'admin', 'Administrator', 'active');

-- Insert sample staff users (password: staff123)
INSERT IGNORE INTO users (id, username, email, password, role, name, status) VALUES
(2, 'staff1', 'staff1@queuemanagement.com', '$2y$10$Y9Z7.L8v9S4q1P2w3E5r6.kM8n7V9x5B3d4C5f6G7h8I9j0K1L2', 'staff', 'John Smith', 'active'),
(3, 'staff2', 'staff2@queuemanagement.com', '$2y$10$Y9Z7.L8v9S4q1P2w3E5r6.kM8n7V9x5B3d4C5f6G7h8I9j0K1L2', 'staff', 'Jane Doe', 'active'),
(4, 'staff3', 'staff3@queuemanagement.com', '$2y$10$Y9Z7.L8v9S4q1P2w3E5r6.kM8n7V9x5B3d4C5f6G7h8I9j0K1L2', 'staff', 'Mike Johnson', 'active');

-- Insert sample counters
INSERT IGNORE INTO counters (id, name, service_id, assigned_staff_id, status) VALUES
(1, 'Counter 1', 1, 2, 'open'),
(2, 'Counter 2', 2, 3, 'open'),
(3, 'Counter 3', 3, 4, 'closed'),
(4, 'Counter 4', 4, NULL, 'closed'),
(5, 'VIP Counter', 5, NULL, 'closed');
