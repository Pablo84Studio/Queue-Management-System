<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff - Admin Panel</title>
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

        $userManager = new UserManager($conn);
        $staff_members = $userManager->getAllUsers('staff');
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
                <a href="manage-staff.php" class="nav-item active">
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
                <a href="../logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="top-bar">
                <h1>Manage Staff</h1>
                <div class="top-bar-actions">
                    <button class="btn btn-primary" onclick="showAddStaffModal()">
                        + Add New Staff
                    </button>
                </div>
            </div>

            <!-- Add/Edit Staff Modal -->
            <div id="staffModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Add New Staff Member</h2>
                        <span class="close" onclick="closeStaffModal()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <form id="staffForm">
                            <input type="hidden" id="staff_id" name="staff_id">
                            
                            <div class="form-group">
                                <label for="name">Full Name *</label>
                                <input type="text" id="name" name="name" required>
                            </div>

                            <div class="form-group">
                                <label for="username">Username *</label>
                                <input type="text" id="username" name="username" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" required>
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Save Staff Member</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Staff List -->
            <div class="card">
                <div class="card-header">
                    <h2>Registered Staff Members</h2>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($staff_members)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center;">No staff members found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($staff_members as $staff): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($staff['name']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['username']); ?></td>
                                        <td><?php echo htmlspecialchars($staff['email']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $staff['status'] === 'active' ? 'success' : 'danger'; ?>">
                                                <?php echo ucfirst($staff['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($staff['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="editStaff(<?php echo $staff['id']; ?>)">Edit</button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteStaff(<?php echo $staff['id']; ?>)">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/common.js"></script>
    <script src="../assets/js/manage-staff.js"></script>
</body>
</html>
