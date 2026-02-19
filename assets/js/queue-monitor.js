// Queue Monitor JavaScript

// Load queue data
async function loadQueueData() {
    try {
        const serviceFilter = document.getElementById('service_filter')?.value || '';
        const statusFilter = document.getElementById('status_filter')?.value || '';

        let endpoint = '../api/queue.php?action=get_current';
        if (serviceFilter) {
            endpoint += `&service_id=${serviceFilter}`;
        }

        const response = await api(endpoint);
        if (response.success && response.data) {
            updateQueueTable(response.data, statusFilter);
            updateQueueStats(response.data);
        }
    } catch (error) {
        console.error('Error loading queue data:', error);
    }
}

// Update queue table
function updateQueueTable(queue, statusFilter) {
    const tbody = document.getElementById('queue_table');
    if (!tbody) return;

    let filteredQueue = queue;
    if (statusFilter) {
        filteredQueue = queue.filter(item => item.status === statusFilter);
    }

    let html = '';
    filteredQueue.forEach((item, index) => {
        const waitTime = getMinutesDifference(item.created_at);
        const statusColor = getStatusColor(item.status);

        html += `
            <tr>
                <td>${item.ticket_number}</td>
                <td>${item.customer_name || 'N/A'}</td>
                <td>${item.phone || 'N/A'}</td>
                <td>${item.service_name}</td>
                <td>${item.counter_name || '-'}</td>
                <td><span class="badge badge-${statusColor}">${item.status.replace('_', ' ').toUpperCase()}</span></td>
                <td>${waitTime} min</td>
                <td>
                    ${item.status === 'waiting' ? `
                        <button class="btn btn-sm btn-info" onclick="callCustomer(${item.id})">Call</button>
                        <button class="btn btn-sm btn-danger" onclick="cancelCustomer(${item.id})">Cancel</button>
                    ` : item.status === 'called' ? `
                        <button class="btn btn-sm btn-success" onclick="completeCustomer(${item.id})">Complete</button>
                    ` : '-'}
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html || '<tr><td colspan="8">No customers in queue</td></tr>';
}

// Update queue statistics
function updateQueueStats(queue) {
    const total = queue.length;
    const waiting = queue.filter(q => q.status === 'waiting').length;
    const serving = queue.filter(q => q.status === 'called').length;

    document.getElementById('stat_total').textContent = total;
    document.getElementById('stat_waiting').textContent = waiting;
    document.getElementById('stat_serving').textContent = serving;

    // Calculate average wait time
    const waitTimes = queue
        .filter(q => q.status === 'waiting' || q.status === 'called')
        .map(q => getMinutesDifference(q.created_at));

    const avgWait = waitTimes.length > 0
        ? Math.round(waitTimes.reduce((a, b) => a + b) / waitTimes.length)
        : 0;

    document.getElementById('stat_avg_wait').textContent = avgWait + ' min';
}

// Get status color
function getStatusColor(status) {
    switch (status) {
        case 'waiting':
            return 'warning';
        case 'called':
        case 'in_service':
            return 'info';
        case 'completed':
            return 'success';
        default:
            return 'danger';
    }
}

// Call customer action
async function callCustomer(queueId) {
    // In real app, would call `call_next` endpoint
    const response = await api('../api/queue.php?action=complete', 'POST', { queue_id: queueId });
    if (response.success) {
        showAlert('Customer called', 'success');
        loadQueueData();
    } else {
        showAlert(response.message || 'Error calling customer', 'danger');
    }
}

// Cancel customer
async function cancelCustomer(queueId) {
    if (confirm('Are you sure you want to cancel this customer?')) {
        const response = await api('../api/queue.php?action=cancel', 'POST', { queue_id: queueId });
        if (response.success) {
            showAlert('Customer cancelled', 'success');
            loadQueueData();
        } else {
            showAlert(response.message || 'Error cancelling customer', 'danger');
        }
    }
}

// Complete customer
async function completeCustomer(queueId) {
    const response = await api('../api/queue.php?action=complete', 'POST', { queue_id: queueId });
    if (response.success) {
        showAlert('Service completed', 'success');
        loadQueueData();
    } else {
        showAlert(response.message || 'Error completing service', 'danger');
    }
}

// Load services for filter
async function loadServicesFilter() {
    try {
        const services = [
            { id: 1, name: 'General Inquiry' },
            { id: 2, name: 'Registration' },
            { id: 3, name: 'Payment Processing' },
            { id: 4, name: 'Troubleshooting' },
            { id: 5, name: 'Document Submission' }
        ];

        const select = document.getElementById('service_filter');
        if (select) {
            services.forEach(service => {
                const option = document.createElement('option');
                option.value = service.id;
                option.textContent = service.name;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading services:', error);
    }
}

// Initialize queue monitor
document.addEventListener('DOMContentLoaded', function() {
    loadServicesFilter();
    loadQueueData();

    // Auto-refresh every 5 seconds
    setInterval(loadQueueData, 5000);

    // Add event listeners
    const serviceFilter = document.getElementById('service_filter');
    const statusFilter = document.getElementById('status_filter');
    const refreshBtn = document.getElementById('refresh_btn');

    if (serviceFilter) serviceFilter.addEventListener('change', loadQueueData);
    if (statusFilter) statusFilter.addEventListener('change', loadQueueData);
    if (refreshBtn) refreshBtn.addEventListener('click', loadQueueData);
});