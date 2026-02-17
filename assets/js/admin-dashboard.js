// Admin Dashboard JavaScript

// Load dashboard data
async function loadDashboardData() {
    try {
        // Load today's statistics
        const range = getDateRange('day');
        const statsResponse = await api(`../api/queue.php?action=get_statistics&start_date=${range.start}&end_date=${range.end}`);
        
        if (statsResponse.success && statsResponse.data) {
            const stats = statsResponse.data;
            document.getElementById('total_customers').textContent = stats.total_customers || 0;
            document.getElementById('completed_customers').textContent = stats.completed_customers || 0;
            document.getElementById('waiting_customers').textContent = stats.waiting_customers || 0;
            document.getElementById('avg_wait_time').textContent = (stats.avg_wait_time || 0) + ' min';
        }

        // Load current queue
        const queueResponse = await api('../api/queue.php?action=get_current');
        if (queueResponse.success && queueResponse.data) {
            const queue = queueResponse.data;
            updateCounterStatus(queue);
            updateServicesOverview(queue);
        }

        // Load average wait time
        const waitTimeResponse = await api(`../api/queue.php?action=get_avg_wait_time`);
        if (waitTimeResponse.success) {
            document.getElementById('avg_wait_time').textContent = waitTimeResponse.data + ' minutes';
        }

    } catch (error) {
        console.error('Error loading dashboard data:', error);
    }
}

// Update counter status display
function updateCounterStatus(queue) {
    const container = document.getElementById('counter_status_container');
    if (!container) return;

    // Group by counter
    const counters = {};
    queue.forEach(item => {
        if (item.counter_name) {
            if (!counters[item.counter_name]) {
                counters[item.counter_name] = [];
            }
            counters[item.counter_name].push(item);
        }
    });

    let html = '';
    for (const [counterName, items] of Object.entries(counters)) {
        const status = items[0].status;
        const statusClass = status === 'called' ? 'serving' : status;
        const ticketNumber = items[0].ticket_number;
        const customerName = items[0].customer_name;

        html += `
            <div class="counter-box ${statusClass}">
                <h3>${counterName}</h3>
                <span class="counter-status ${statusClass}">${status.toUpperCase()}</span>
                <div>
                    <p><strong>Ticket:</strong> ${ticketNumber}</p>
                    <p><strong>Customer:</strong> ${customerName}</p>
                </div>
            </div>
        `;
    }

    container.innerHTML = html || '<p>No counters active</p>';
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

// Refresh dashboard
function refreshDashboard() {
    loadDashboardData();
    showAlert('Dashboard refreshed', 'success');
}

// Auto-refresh dashboard every 10 seconds
setInterval(loadDashboardData, 10000);

// Initialize dashboard on page load
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();

    // Add event listener for refresh button if exists
    const refreshBtn = document.getElementById('refresh_btn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', refreshDashboard);
    }
});
