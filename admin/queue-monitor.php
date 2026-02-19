<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Monitor - Admin Panel</title>
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

        if (!UserManager::isLoggedIn() || !UserManager::isAdmin()) {
            header('Location: ../index.php');
            exit;
        }
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
                <a href="queue-monitor.php" class="nav-item active">
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
                <a href="../logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="top-bar">
                <h1>Queue Monitor</h1>
                <div class="top-bar-actions">
                    <button class="btn btn-primary" id="refresh_btn">
                        🔄 Refresh
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Live Queue Status</h2>
                    <p>Real-time monitoring of all customers in queue</p>
                </div>
                <div class="card-body">
                    <div class="filter-bar">
                        <select id="service_filter" class="form-control">
                            <option value="">All Services</option>
                        </select>
                        <select id="status_filter" class="form-control">
                            <option value="">All Status</option>
                            <option value="waiting">Waiting</option>
                            <option value="called">Called</option>
                            <option value="in_service">In Service</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Ticket #</th>
                                <th>Customer Name</th>
                                <th>Phone</th>
                                <th>Service</th>
                                <th>Counter</th>
                                <th>Status</th>
                                <th>Wait Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="queue_table">
                            <tr>
                                <td colspan="8" style="text-align: center;">Loading queue data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Queue Statistics -->
            <div class="card" style="margin-top: 20px;">
                <div class="card-header">
                    <h2>Queue Statistics</h2>
                </div>
                <div class="card-body">
                    <div class="stats-grid">
                        <div class="stat-box">
                            <h3>Total in Queue</h3>
                            <p id="stat_total" class="stat-number">0</p>
                        </div>
                        <div class="stat-box">
                            <h3>Waiting</h3>
                            <p id="stat_waiting" class="stat-number">0</p>
                        </div>
                        <div class="stat-box">
                            <h3>Being Served</h3>
                            <p id="stat_serving" class="stat-number">0</p>
                        </div>
                        <div class="stat-box">
                            <h3>Avg Wait Time</h3>
                            <p id="stat_avg_wait" class="stat-number">0 min</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/common.js"></script>
    <script src="../assets/js/queue-monitor.js"></script>
</body>
</html>
