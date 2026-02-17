<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Display - Queue Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/queue-display.css">
</head>
<body>
    <div class="queue-display-container">
        <!-- Header -->
        <div class="display-header">
            <h1>🎫 Queue Management System</h1>
            <p>Current Queue Status</p>
            <div class="header-time" id="current_time"></div>
        </div>

        <!-- Main Content -->
        <div class="display-content">
            <!-- Now Serving -->
            <div class="now-serving-section">
                <h2>NOW SERVING</h2>
                <div class="counter-display-grid" id="now_serving_grid">
                    <div class="counter-display placeholder">
                        <p>Loading...</p>
                    </div>
                </div>
            </div>

            <!-- Queue List -->
            <div class="queue-list-section">
                <h2>WAITING IN QUEUE</h2>
                
                <div class="service-tabs">
                    <button class="service-tab active" onclick="filterByService('')">All</button>
                </div>

                <div class="queue-list" id="queue_list">
                    <div class="queue-item placeholder">
                        <p>Loading queue...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Footer -->
        <div class="display-footer">
            <div class="stat-item">
                <span class="stat-icon">👥</span>
                <span class="stat-label">Total in Queue</span>
                <span class="stat-value" id="total_in_queue">0</span>
            </div>
            <div class="stat-item">
                <span class="stat-icon">✓</span>
                <span class="stat-label">Average Wait</span>
                <span class="stat-value" id="avg_wait_time">0 min</span>
            </div>
            <div class="stat-item">
                <span class="stat-icon">🏪</span>
                <span class="stat-label">Active Counters</span>
                <span class="stat-value" id="active_counters">0</span>
            </div>
            <div class="stat-item">
                <span class="stat-icon">⏰</span>
                <span class="stat-label">Update</span>
                <span class="stat-value" id="last_update">Just now</span>
            </div>
        </div>
    </div>

    <script src="assets/js/common.js"></script>
    <script src="assets/js/queue-display.js"></script>
</body>
</html>
