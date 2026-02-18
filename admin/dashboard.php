<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Queue Management System</title>
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

        $user_name = $_SESSION['name'] ?? 'Admin';
        ?>

        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>🎫 Queue Manager</h2>
                <p>Admin Panel</p>
            </div>

            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item active">
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
                <h1>Dashboard</h1>
                <div class="top-bar-actions">
                    <button class="btn btn-primary" onclick="location.href='queue-monitor.php'">
                        View Live Queue
                    </button>
                    <button class="btn btn-secondary" onclick="location.href='reports.php'">
                        Generate Report
                    </button>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="dashboard-grid">
                <!-- Stats Cards -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: #3498db;">👥</div>
                        <div class="stat-content">
                            <h3>Total Customers Today</h3>
                            <p id="total_customers" class="stat-value">0</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: #27ae60;">✓</div>
                        <div class="stat-content">
                            <h3>Completed</h3>
                            <p id="completed_customers" class="stat-value">0</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: #e74c3c;">⏳</div>
                        <div class="stat-content">
                            <h3>Waiting in Queue</h3>
                            <p id="waiting_customers" class="stat-value">0</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: #f39c12;">⏱️</div>
                        <div class="stat-content">
                            <h3>Avg Wait Time</h3>
                            <p id="avg_wait_time" class="stat-value">0 min</p>
                        </div>
                    </div>
                </div>

                <!-- Counter Status -->
                <div class="card">
                    <div class="card-header">
                        <h2>Counter Status</h2>
                    </div>
                    <div class="card-body">
                        <div id="counter_status_container" class="counter-grid">
                            <p>Loading...</p>
                        </div>
                    </div>
                </div>

                <!-- Services Overview -->
                <div class="card">
                    <div class="card-header">
                        <h2>Services Overview</h2>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Waiting</th>
                                    <th>In Service</th>
                                    <th>Completed</th>
                                </tr>
                            </thead>
                            <tbody id="services_table">
                                <tr>
                                    <td colspan="4" style="text-align: center;">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="card">
                    <div class="card-header">
                        <h2>Recent Queue Activities</h2>
                    </div>
                    <div class="card-body">
                        <div id="activities_container">
                            <p>Loading recent activities...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/common.js"></script>
    <script src="../assets/js/admin-dashboard.js"></script>
</body>
</html>
