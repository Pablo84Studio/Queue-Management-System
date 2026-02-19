<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Customers to Queue - Queue Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="container">
        <?php
        require_once('../config/database.php');
        require_once('../classes/UserManager.php');

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is admin
        if (!UserManager::isLoggedIn() || !UserManager::isAdmin()) {
            header('Location: ../index.php');
            exit;
        }

        $user_name = $_SESSION['name'] ?? 'Administrator';
        ?>

        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>🎫 Queue Manager</h2>
                <p>Admin Panel</p>
            </div>

            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item">
                    <span>📊</span> Dashboard
                </a>
                <a href="manage-staff.php" class="nav-item">
                    <span>👥</span> Manage Staff
                </a>
                <a href="manage-counters.php" class="nav-item">
                    <span>🟦</span> Manage Counters
                </a>
                <a href="queue-monitor.php" class="nav-item">
                    <span>📋</span> Monitor Queue
                </a>
                <a href="add-customers.php" class="nav-item active">
                    <span>➕</span> Add Customers
                </a>
                <a href="reports.php" class="nav-item">
                    <span>📈</span> Reports
                </a>
                <a href="settings.php" class="nav-item">
                    <span>⚙️</span> Settings
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-info">
                    <p>Welcome, <strong><?php echo htmlspecialchars($user_name); ?></strong></p>
                </div>
                <a href="../logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
                <h1>Add Customers to Queue</h1>
                <div class="top-bar-actions">
                    <button class="btn btn-secondary" onclick="location.href='queue-monitor.php'">
                        View Live Queue
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="dashboard-grid">
                <!-- Add Customer Form -->
                <div class="card">
                    <div class="card-header">
                        <h2>Add Customer to Queue</h2>
                    </div>
                    <div class="card-body">
                        <form id="addCustomerForm">
                            <div class="form-group">
                                <label for="service_id">Service *</label>
                                <select id="service_id" name="service_id" required>
                                    <option value="">Select Service</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="customer_name">Customer Name *</label>
                                <input type="text" id="customer_name" name="customer_name" placeholder="Enter customer name" required>
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" placeholder="Optional">
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Add Customer to Queue</button>
                        </form>
                    </div>
                </div>

                <!-- Queue Status by Service -->
                <div class="card">
                    <div class="card-header">
                        <h2>Service Queue Status</h2>
                    </div>
                    <div class="card-body">
                        <div id="services_status">
                            <p>Loading service information...</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Additions -->
                <div class="card">
                    <div class="card-header">
                        <h2>Recently Added Customers</h2>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Ticket</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="recent_customers">
                                <tr>
                                    <td colspan="5" style="text-align: center;">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/common.js"></script>
    <script src="../assets/js/add-customers.js"></script>
</body>
</html>
