<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin Panel</title>
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
                <a href="queue-monitor.php" class="nav-item">
                    <span>📋</span> Monitor Queue
                </a>
                <a href="reports.php" class="nav-item active">
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
                <h1>Reports</h1>
            </div>

            <!-- Queue Report -->
            <div class="card">
                <div class="card-header">
                    <h2>Queue Report</h2>
                    <p>Generate queue statistics and customer details</p>
                </div>
                <div class="card-body">
                    <form id="queueReportForm" class="report-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="queue_period">Report Period</label>
                                <select id="queue_period" name="queue_period">
                                    <option value="day">Today</option>
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                    <option value="custom">Custom Range</option>
                                </select>
                            </div>

                            <div class="form-group" id="custom_date_group" style="display: none;">
                                <label for="queue_start_date">Start Date</label>
                                <input type="date" id="queue_start_date" name="queue_start_date">
                            </div>

                            <div class="form-group" id="custom_date_group2" style="display: none;">
                                <label for="queue_end_date">End Date</label>
                                <input type="date" id="queue_end_date" name="queue_end_date">
                            </div>

                            <div class="form-group">
                                <label for="queue_service">Service</label>
                                <select id="queue_service" name="queue_service">
                                    <option value="">All Services</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">
                                    📊 Generate PDF Report
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Staff Performance Report -->
            <div class="card" style="margin-top: 20px;">
                <div class="card-header">
                    <h2>Staff Performance Report</h2>
                    <p>Monitor staff efficiency and performance metrics</p>
                </div>
                <div class="card-body">
                    <form id="staffReportForm" class="report-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="staff_period">Report Period</label>
                                <select id="staff_period" name="staff_period">
                                    <option value="day">Today</option>
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                    <option value="custom">Custom Range</option>
                                </select>
                            </div>

                            <div class="form-group" id="staff_custom_date_group" style="display: none;">
                                <label for="staff_start_date">Start Date</label>
                                <input type="date" id="staff_start_date" name="staff_start_date">
                            </div>

                            <div class="form-group" id="staff_custom_date_group2" style="display: none;">
                                <label for="staff_end_date">End Date</label>
                                <input type="date" id="staff_end_date" name="staff_end_date">
                            </div>

                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">
                                    👥 Generate Staff Report
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent Reports -->
            <div class="card" style="margin-top: 20px;">
                <div class="card-header">
                    <h2>Quick Statistics</h2>
                </div>
                <div class="card-body">
                    <div class="stats-grid">
                        <div class="stat-box">
                            <h3>Today's Total</h3>
                            <p id="today_total" class="stat-number">0</p>
                        </div>
                        <div class="stat-box">
                            <h3>This Week</h3>
                            <p id="week_total" class="stat-number">0</p>
                        </div>
                        <div class="stat-box">
                            <h3>This Month</h3>
                            <p id="month_total" class="stat-number">0</p>
                        </div>
                        <div class="stat-box">
                            <h3>Avg Completion Rate</h3>
                            <p id="completion_rate" class="stat-number">0%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/common.js"></script>
    <script src="../assets/js/reports.js"></script>
</body>
</html>
