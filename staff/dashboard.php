<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Queue Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/staff.css">
</head>
<body>
    <div class="staff-container">
        <?php
        require_once('../config/database.php');
        require_once('../classes/UserManager.php');

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!UserManager::isLoggedIn() || $_SESSION['role'] !== 'staff') {
            header('Location: ../index.php');
            exit;
        }

        $user_name = $_SESSION['name'] ?? 'Staff';
        $user_id = $_SESSION['user_id'];
        ?>

        <!-- Header -->
        <div class="staff-header">
            <div class="header-content">
                <h1>🎫 Queue Management System</h1>
                <p>Staff Interface</p>
            </div>
            <div class="header-info">
                <span>Welcome, <strong><?php echo htmlspecialchars($user_name); ?></strong></span>
                <a href="../logout.php" class="logout-link">Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="staff-content">
            <div class="staff-grid">
                <!-- Add Customer to Queue -->
                <div class="card">
                    <div class="card-header">
                        <h2>Add to Queue</h2>
                    </div>
                    <div class="card-body">
                        <form id="addQueueForm">
                            <div class="form-group">
                                <label for="customer_name">Customer Name</label>
                                <input type="text" id="customer_name" name="customer_name" placeholder="Enter customer name" required>
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" placeholder="Optional">
                            </div>

                            <div class="form-group">
                                <label for="service_id">Service</label>
                                <select id="service_id" name="service_id" required>
                                    <option value="">Select Service</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Add Customer</button>
                        </form>
                    </div>
                </div>

                <!-- Counter Control -->
                <div class="card">
                    <div class="card-header">
                        <h2>Counter Control</h2>
                    </div>
                    <div class="card-body">
                        <div id="counter_info">
                            <p>Loading counter information...</p>
                        </div>

                        <div id="counter_actions" style="display: none;">
                            <button class="btn btn-success btn-block" id="call_next_btn" onclick="callNextCustomer()">
                                📢 Call Next Customer
                            </button>
                            <button class="btn btn-info btn-block" onclick="showCurrentCustomer()">
                                👁️ Show Current Queue
                            </button>
                            <button class="btn btn-warning btn-block" onclick="toggleBreak()">
                                ☕ Take Break
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Current Customer -->
                <div class="card">
                    <div class="card-header">
                        <h2>Current Customer</h2>
                    </div>
                    <div class="card-body">
                        <div id="current_customer" class="current-customer">
                            <p class="placeholder">No customer being served</p>
                        </div>
                        <div id="service_actions" style="display: none;">
                            <button class="btn btn-success btn-block" onclick="completeService()">
                                ✓ Complete Service
                            </button>
                            <button class="btn btn-danger btn-block" onclick="cancelService()">
                                ✗ Cancel/No Show
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Queue Statistics -->
                <div class="card">
                    <div class="card-header">
                        <h2>Queue Status</h2>
                    </div>
                    <div class="card-body">
                        <div class="queue-stats">
                            <div class="stat">
                                <span class="stat-label">Waiting</span>
                                <span class="stat-value" id="waiting_count">0</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Average Wait</span>
                                <span class="stat-value" id="avg_wait">0m</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Served Today</span>
                                <span class="stat-value" id="served_today">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Next 5 Customers -->
            <div class="card">
                <div class="card-header">
                    <h2>Next 5 Customers in Queue</h2>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Position</th>
                                <th>Customer Name</th>
                                <th>Service</th>
                                <th>Wait Time</th>
                            </tr>
                        </thead>
                        <tbody id="next_customers_table">
                            <tr>
                                <td colspan="4" style="text-align: center;">Loading queue...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Audio for alerts -->
    <audio id="callAudio" preload="auto">
        <source src="../assets/sounds/call.mp3" type="audio/mpeg">
    </audio>
    <audio id="notificationAudio" preload="auto">
        <source src="../assets/sounds/notification.mp3" type="audio/mpeg">
    </audio>

    <script src="../assets/js/common.js"></script>
    <script src="../assets/js/staff-dashboard.js"></script>
</body>
</html>
