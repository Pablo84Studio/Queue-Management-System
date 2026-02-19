<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Counters - Admin Panel</title>
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
                <a href="manage-counters.php" class="nav-item active">
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
                <a href="../logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="top-bar">
                <h1>Manage Counters</h1>
                <div class="top-bar-actions">
                    <button class="btn btn-primary" onclick="showAddCounterModal()">
                        + Add New Counter
                    </button>
                </div>
            </div>

            <!-- Add/Edit Counter Modal -->
            <div id="counterModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Add New Counter</h2>
                        <span class="close" onclick="closeCounterModal()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <form id="counterForm">
                            <input type="hidden" id="counter_id" name="counter_id">
                            
                            <div class="form-group">
                                <label for="counter_name">Counter Name *</label>
                                <input type="text" id="counter_name" name="counter_name" placeholder="e.g., Counter 1" required>
                            </div>

                            <div class="form-group">
                                <label for="service_id">Service *</label>
                                <select id="service_id" name="service_id" required>
                                    <option value="">Select Service</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="staff_id">Assign Staff</label>
                                <select id="staff_id" name="staff_id">
                                    <option value="">None</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Save Counter</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Counter List -->
            <div class="card">
                <div class="card-header">
                    <h2>Active Counters</h2>
                </div>
                <div class="card-body">
                    <div id="counters_container" class="counters-grid">
                        <p>Loading counters...</p>
                    </div>
                </div>
            </div>

            <!-- Services -->
            <div class="card" style="margin-top: 20px;">
                <div class="card-header">
                    <h2>Services</h2>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Service Name</th>
                                <th>Status</th>
                                <th>Active Counters</th>
                            </tr>
                        </thead>
                        <tbody id="services_table">
                            <tr>
                                <td colspan="3" style="text-align: center;">Loading services...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/common.js"></script>
    <script src="../assets/js/manage-counters.js"></script>
</body>
</html>
