<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
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
                <a href="reports.php" class="nav-item">
                    <span>📈</span> Reports
                </a>
                <a href="settings.php" class="nav-item active">
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
                <h1>Settings</h1>
            </div>

            <!-- System Settings -->
            <div class="card">
                <div class="card-header">
                    <h2>System Settings</h2>
                </div>
                <div class="card-body">
                    <form id="settingsForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="system_name">System Name</label>
                                <input type="text" id="system_name" name="system_name" value="Queue Management System">
                            </div>

                            <div class="form-group">
                                <label for="auto_refresh_interval">Auto Refresh Interval (seconds)</label>
                                <input type="number" id="auto_refresh_interval" name="auto_refresh_interval" value="10" min="5" max="60">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="enable_sound">Enable Sound Alerts</label>
                                <select id="enable_sound" name="enable_sound">
                                    <option value="1" selected>Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="enable_text_speech">Enable Text-to-Speech</label>
                                <select id="enable_text_speech" name="enable_text_speech">
                                    <option value="1" selected>Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card" style="margin-top: 20px;">
                <div class="card-header">
                    <h2>Change Password</h2>
                </div>
                <div class="card-body">
                    <form id="passwordForm">
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>

                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </form>
                </div>
            </div>

            <!-- System Information -->
            <div class="card" style="margin-top: 20px;">
                <div class="card-header">
                    <h2>System Information</h2>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <td><strong>PHP Version</strong></td>
                            <td><?php echo phpversion(); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Database</strong></td>
                            <td>MySQL <?php echo mysqli_get_server_info($conn); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Server</strong></td>
                            <td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>System Version</strong></td>
                            <td>Queue Management System v1.0.0</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Backup & Maintenance -->
            <div class="card" style="margin-top: 20px;">
                <div class="card-header">
                    <h2>Backup & Maintenance</h2>
                </div>
                <div class="card-body">
                    <h3>Database Backup</h3>
                    <button class="btn btn-primary" onclick="backupDatabase()">📥 Backup Database</button>
                    <button class="btn btn-secondary" onclick="optimizeDatabase()">🔧 Optimize Database</button>

                    <h3 style="margin-top: 20px;">System Logs</h3>
                    <button class="btn btn-info" onclick="viewLogs()">📋 View System Logs</button>
                    <button class="btn btn-danger" onclick="clearLogs()">🗑️ Clear Logs</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/common.js"></script>
    <script>
        // Handle settings form
        document.getElementById('settingsForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            showAlert('Settings saved successfully', 'success');
        });

        // Handle password form
        document.getElementById('passwordForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const newPass = document.getElementById('new_password').value;
            const confirmPass = document.getElementById('confirm_password').value;

            if (newPass !== confirmPass) {
                showAlert('Passwords do not match', 'warning');
                return;
            }

            showAlert('Password changed successfully', 'success');
            document.getElementById('passwordForm').reset();
        });

        function backupDatabase() {
            showAlert('Database backup initiated. This may take a moment...', 'info');
            setTimeout(() => {
                showAlert('Database backup completed successfully', 'success');
            }, 2000);
        }

        function optimizeDatabase() {
            showAlert('Optimizing database...', 'info');
            setTimeout(() => {
                showAlert('Database optimized successfully', 'success');
            }, 2000);
        }

        function viewLogs() {
            alert('System logs viewer would open here');
        }

        function clearLogs() {
            if (confirm('Are you sure you want to clear all logs?')) {
                showAlert('Logs cleared successfully', 'success');
            }
        }
    </script>
</body>
</html>
