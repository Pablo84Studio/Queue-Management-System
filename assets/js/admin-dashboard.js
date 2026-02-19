// Admin Dashboard JavaScript

// Load dashboard data
async function loadDashboardData() {
    try {
        // Load today's statistics
        const range = getDateRange('day');
        const statsResponse = await api(`../api/queue.php?action=get_statistics&start_date=${range.start}&end_date=${range.end}`, 'GET');
        
        console.log('Stats Response:', statsResponse);
        
        if (statsResponse.success && statsResponse.data) {
            const stats = statsResponse.data;
            document.getElementById('total_customers').textContent = (stats.total_customers || 0);
            document.getElementById('completed_customers').textContent = (stats.completed_customers || 0);
            document.getElementById('waiting_customers').textContent = (stats.waiting_customers || 0);
            document.getElementById('avg_wait_time').textContent = (parseFloat(stats.avg_wait_time) || 0).toFixed(1) + ' min';
        } else {
            console.error('Stats API error:', statsResponse.message);
        }

        // Load current queue
        const queueResponse = await api('../api/queue.php?action=get_current', 'GET');
        console.log('Queue Response:', queueResponse);
        
        if (queueResponse.success && queueResponse.data && Array.isArray(queueResponse.data)) {
            const queue = queueResponse.data;
            updateCounterStatus(queue);
            updateServicesOverview(queue);
        } else {
            console.error('Queue API error:', queueResponse.message);
            document.getElementById('counter_status_container').innerHTML = '<p>No data available</p>';
            document.getElementById('services_table').innerHTML = '<tr><td colspan="4">No data available</td></tr>';
        }

        // Load average wait time (optional, already included in stats)
        // This is for real-time update if needed
        const waitTimeResponse = await api(`../api/queue.php?action=get_avg_wait_time`, 'GET');
        if (waitTimeResponse.success && waitTimeResponse.data) {
            const avgMinutes = (parseFloat(waitTimeResponse.data) || 0).toFixed(1);
            document.getElementById('avg_wait_time').textContent = avgMinutes + ' min';
        }

    } catch (error) {
        console.error('Error loading dashboard data:', error);
    }
}

// Update counter status display
function updateCounterStatus(queue) {
    const container = document.getElementById('counter_status_container');
    if (!container) return;

    // Filter only 'called' status items to show active counters
    const activeCalls = queue.filter(item => item.status === 'called');
    
    if (activeCalls.length === 0) {
        container.innerHTML = '<p>No customers being served</p>';
        return;
    }

    let html = '';
    activeCalls.forEach(item => {
        html += `
            <div class="counter-box called">
                <h3>${item.counter_name || 'Counter'}</h3>
                <span class="counter-status called">CALLED</span>
                <div>
                    <p><strong>Ticket:</strong> ${item.ticket_number}</p>
                    <p><strong>Customer:</strong> ${item.customer_name || 'N/A'}</p>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

// Update services overview
function updateServicesOverview(queue) {
    const tbody = document.getElementById('services_table');
    if (!tbody) return;

    // Group by service
    const services = {};
    queue.forEach(item => {
        if (!services[item.service_name]) {
            services[item.service_name] = {
                waiting: 0,
                in_service: 0,
                completed: 0
            };
        }

        if (item.status === 'waiting') services[item.service_name].waiting++;
        if (item.status === 'called') services[item.service_name].in_service++;
        if (item.status === 'completed') services[item.service_name].completed++;
    });

    let html = '';
    for (const [serviceName, counts] of Object.entries(services)) {
        html += `
            <tr>
                <td>${serviceName}</td>
                <td>${counts.waiting}</td>
                <td>${counts.in_service}</td>
                <td>${counts.completed}</td>
            </tr>
        `;
    }

    tbody.innerHTML = html || '<tr><td colspan="4">No activity</td></tr>';
}

// Load recent activities
async function loadRecentActivities() {
    try {
        // For now, this will show recent queue items
        const response = await api('../api/queue.php?action=get_current', 'GET');
        const container = document.getElementById('activities_container');
        
        if (!container) return;
        
        if (response.success && response.data && Array.isArray(response.data)) {
            const activities = response.data.slice(0, 10); // Last 10 items
            
            if (activities.length === 0) {
                container.innerHTML = '<p>No recent activities</p>';
                return;
            }
            
            let html = '<ul style="list-style: none; padding: 0;">';
            activities.forEach(activity => {
                html += `
                    <li style="padding: 8px; border-bottom: 1px solid #eee;">
                        <strong>${activity.ticket_number}</strong> - ${activity.customer_name} 
                        <span style="color: #888;"> (${activity.service_name})</span>
                        <span style="color: #666; float: right;">${activity.status}</span>
                    </li>
                `;
            });
            html += '</ul>';
            container.innerHTML = html;
        } else {
            container.innerHTML = '<p>No recent activities available</p>';
        }
    } catch (error) {
        console.error('Error loading recent activities:', error);
        const container = document.getElementById('activities_container');
        if (container) {
            container.innerHTML = '<p>Error loading activities</p>';
        }
    }
}

// Refresh dashboard
function refreshDashboard() {
    loadDashboardData();
    loadRecentActivities();
    showAlert('Dashboard refreshed', 'success');
}

// Auto-refresh dashboard every 10 seconds
setInterval(loadDashboardData, 10000);

// Load recent activities every 15 seconds
setInterval(loadRecentActivities, 15000);

// Initialize dashboard on page load
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
    loadRecentActivities();

    // Add event listener for refresh button if exists
    const refreshBtn = document.getElementById('refresh_btn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', refreshDashboard);
    }
});