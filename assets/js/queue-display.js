// Queue Display JavaScript

// Load queue data
async function loadQueueData() {
    try {
        const response = await api('./api/queue.php?action=get_current');
        
        if (response.success && response.data) {
            const queue = response.data;
            updateQueueDisplay(queue);
            updateStatistics(queue);
            updateLastUpdateTime();
        }
    } catch (error) {
        console.error('Error loading queue data:', error);
    }
}

// Update queue display
function updateQueueDisplay(queue) {
    // Separate called and waiting
    const called = queue.filter(q => q.status === 'called');
    const waiting = queue.filter(q => q.status === 'waiting');

    // Update now serving section
    const nowServingGrid = document.getElementById('now_serving_grid');
    if (nowServingGrid) {
        let html = '';
        if (called.length > 0) {
            called.forEach(item => {
                html += `
                    <div class="counter-display">
                        <h3>${item.counter_name || 'Counter'}</h3>
                        <p class="ticket-number">${item.ticket_number}</p>
                        <p class="customer-name">${item.customer_name || 'Customer'}</p>
                        <p class="counter-name">Service: ${item.service_name}</p>
                    </div>
                `;
            });
        } else {
            html = '<div class="counter-display placeholder"><p>No customers being served</p></div>';
        }
        nowServingGrid.innerHTML = html;
    }

    // Update queue list
    const queueList = document.getElementById('queue_list');
    if (queueList) {
        let html = '';
        if (waiting.length > 0) {
            waiting.slice(0, 20).forEach((item, index) => {
                const waitTime = getMinutesDifference(item.created_at);
                html += `
                    <div class="queue-item">
                        <h3>Position</h3>
                        <p class="position">${index + 1}</p>
                        <p class="customer-name">${item.customer_name || 'Customer'}</p>
                        <p class="service-name">${item.service_name}</p>
                    </div>
                `;
            });
        } else {
            html = '<div class="queue-item placeholder"><p>No customers waiting</p></div>';
        }
        queueList.innerHTML = html;
    }
}

// Update statistics
function updateStatistics(queue) {
    const totalInQueue = queue.length;
    const avgWaitTime = queue.length > 0
        ? Math.round(queue.reduce((sum, q) => sum + getMinutesDifference(q.created_at), 0) / queue.length)
        : 0;
    
    const activeCounters = queue.filter(q => q.status === 'called').length;

    document.getElementById('total_in_queue').textContent = totalInQueue;
    document.getElementById('avg_wait_time').textContent = avgWaitTime + ' min';
    document.getElementById('active_counters').textContent = activeCounters;
}

// Update last update time
function updateLastUpdateTime() {
    const lastUpdateElement = document.getElementById('last_update');
    if (lastUpdateElement) {
        lastUpdateElement.textContent = 'Just now';
    }
}

// Filter by service
function filterByService(serviceId) {
    // In real app, would filter by service
    // For demo, just reload all
    loadQueueData();
}

// Load service tabs
async function loadServiceTabs() {
    // In real app, would fetch from database
    const services = [
        { id: 1, name: 'General Inquiry' },
        { id: 2, name: 'Registration' },
        { id: 3, name: 'Payment Processing' },
        { id: 4, name: 'Troubleshooting' },
        { id: 5, name: 'Document Submission' }
    ];

    const tabsContainer = document.querySelector('.service-tabs');
    if (!tabsContainer) return;

    // Add tabs
    services.forEach(service => {
        const button = document.createElement('button');
        button.className = 'service-tab';
        button.textContent = service.name;
        button.onclick = () => filterByService(service.id);
        tabsContainer.appendChild(button);
    });
}

// Initialize display
document.addEventListener('DOMContentLoaded', function() {
    loadQueueData();
    loadServiceTabs();

    // Auto-refresh every 5 seconds
    setInterval(loadQueueData, 5000);

    // Update current time
    updateCurrentTime();
    setInterval(updateCurrentTime, 1000);
});