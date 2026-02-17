// Staff Dashboard JavaScript

let currentCustomer = null;
let currentCounter = null;

// Load staff counter
async function loadStaffCounter() {
    try {
        // In real app, would fetch from database
        // For demo, assuming first counter is assigned to staff
        currentCounter = {
            id: 1,
            name: 'Counter 1',
            service_id: 1,
            status: 'open'
        };

        const counterInfo = document.getElementById('counter_info');
        const counterActions = document.getElementById('counter_actions');
        if (counterInfo && counterActions) {
            counterInfo.innerHTML = `
                <h3 style="margin: 0 0 10px 0;">${currentCounter.name}</h3>
                <p style="margin: 5px 0;">Status: <strong style="color: green;">OPEN</strong></p>
            `;
            counterActions.style.display = 'block';
        }

        loadServices();
        loadQueueData();
    } catch (error) {
        console.error('Error loading counter:', error);
    }
}

// Load services
async function loadServices() {
    const services = [
        { id: 1, name: 'General Inquiry' },
        { id: 2, name: 'Registration' },
        { id: 3, name: 'Payment Processing' },
        { id: 4, name: 'Troubleshooting' },
        { id: 5, name: 'Document Submission' }
    ];

    const select = document.getElementById('service_id');
    if (select) {
        services.forEach(service => {
            const option = document.createElement('option');
            option.value = service.id;
            option.textContent = service.name;
            select.appendChild(option);
        });
    }
}

// Add customer to queue
async function addCustomerToQueue(e) {
    if (e) e.preventDefault();

    const customerName = document.getElementById('customer_name')?.value || '';
    const phone = document.getElementById('phone')?.value || '';
    const serviceId = document.getElementById('service_id')?.value || '';

    if (!customerName || !serviceId) {
        showAlert('Please fill in required fields', 'warning');
        return;
    }

    try {
        const response = await api('../api/queue.php?action=add', 'POST', {
            customer_name: customerName,
            phone: phone,
            service_id: serviceId
        });

        if (response.success) {
            showAlert(`Customer added to queue - Ticket #${response.ticket_number}`, 'success');
            document.getElementById('addQueueForm').reset();
            loadQueueData();
            
            // Play notification sound
            playSound('notificationAudio');
        } else {
            showAlert(response.message || 'Error adding customer', 'danger');
        }
    } catch (error) {
        console.error('Error adding customer:', error);
        showAlert('Error adding customer', 'danger');
    }
}

// Call next customer
async function callNextCustomer() {
    if (!currentCounter) {
        showAlert('No counter assigned', 'warning');
        return;
    }

    try {
        const response = await api('../api/queue.php?action=call_next', 'POST', {
            counter_id: currentCounter.id
        });

        if (response.success) {
            currentCustomer = response;
            
            // Announce ticket number
            const announcement = `Calling ticket number ${response.ticket_number}`;
            speakText(announcement);
            
            // Play call sound
            playSound('callAudio');
            
            showAlert(announcement, 'success');
            updateCurrentCustomerDisplay();
            loadQueueData();
        } else {
            showAlert(response.message || 'No customers waiting', 'warning');
        }
    } catch (error) {
        console.error('Error calling customer:', error);
        showAlert('Error calling customer', 'danger');
    }
}

// Show current customer
function showCurrentCustomer() {
    if (currentCustomer) {
        updateCurrentCustomerDisplay();
    } else {
        showAlert('No customer currently being served', 'info');
    }
}

// Update current customer display
function updateCurrentCustomerDisplay() {
    const container = document.getElementById('current_customer');
    const serviceActions = document.getElementById('service_actions');

    if (currentCustomer && container && serviceActions) {
        container.innerHTML = `
            <h3 style="color: #27ae60; margin-bottom: 15px;">TICKET #${currentCustomer.ticket_number}</h3>
            <p><strong>Customer:</strong> ${currentCustomer.customer_name || 'N/A'}</p>
            <p><strong>Phone:</strong> ${currentCustomer.phone || 'N/A'}</p>
            <p><strong>Service:</strong> General Inquiry</p>
        `;
        container.classList.remove('placeholder');
        serviceActions.style.display = 'block';
    }
}

// Complete service
async function completeService() {
    if (!currentCustomer) {
        showAlert('No customer to complete', 'warning');
        return;
    }

    try {
        const response = await api('../api/queue.php?action=complete', 'POST', {
            queue_id: currentCustomer.queue_id
        });

        if (response.success) {
            showAlert('Service completed successfully', 'success');
            currentCustomer = null;
            document.getElementById('current_customer').innerHTML = '<p class="placeholder">No customer being served</p>';
            document.getElementById('current_customer').classList.add('placeholder');
            document.getElementById('service_actions').style.display = 'none';
            loadQueueData();
        } else {
            showAlert(response.message || 'Error completing service', 'danger');
        }
    } catch (error) {
        console.error('Error completing service:', error);
        showAlert('Error completing service', 'danger');
    }
}

// Cancel service
async function cancelService() {
    if (!currentCustomer) {
        showAlert('No customer to cancel', 'warning');
        return;
    }

    if (!confirm('Are you sure you want to cancel this customer?')) {
        return;
    }

    try {
        const response = await api('../api/queue.php?action=cancel', 'POST', {
            queue_id: currentCustomer.queue_id
        });

        if (response.success) {
            showAlert('Customer cancelled', 'success');
            currentCustomer = null;
            document.getElementById('current_customer').innerHTML = '<p class="placeholder">No customer being served</p>';
            document.getElementById('current_customer').classList.add('placeholder');
            document.getElementById('service_actions').style.display = 'none';
            loadQueueData();
        } else {
            showAlert(response.message || 'Error cancelling customer', 'danger');
        }
    } catch (error) {
        console.error('Error cancelling customer:', error);
        showAlert('Error cancelling customer', 'danger');
    }
}

// Toggle break
function toggleBreak() {
    const isOnBreak = document.getElementById('call_next_btn')?.disabled;
    if (isOnBreak) {
        // Resume
        document.getElementById('counter_info').innerHTML = `
            <h3 style="margin: 0 0 10px 0;">Counter 1</h3>
            <p style="margin: 5px 0;">Status: <strong style="color: green;">OPEN</strong></p>
        `;
        document.getElementById('call_next_btn').disabled = false;
        showAlert('Resume service', 'success');
    } else {
        // Take break
        document.getElementById('counter_info').innerHTML = `
            <h3 style="margin: 0 0 10px 0;">Counter 1</h3>
            <p style="margin: 5px 0;">Status: <strong style="color: orange;">ON BREAK</strong></p>
        `;
        document.getElementById('call_next_btn').disabled = true;
        showAlert('On break - customers cannot be called', 'warning');
    }
}

// Load queue data
async function loadQueueData() {
    try {
        const response = await api('../api/queue.php?action=get_current');
        
        if (response.success && response.data) {
            const queue = response.data;

            // Update next customers
            updateNextCustomersTable(queue.slice(0, 5));

            // Update statistics
            const waiting = queue.filter(q => q.status === 'waiting').length;
            document.getElementById('waiting_count').textContent = waiting;

            // Calculate average wait time
            const waitingItems = queue.filter(q => q.status === 'waiting');
            if (waitingItems.length > 0) {
                const avgMinutes = waitingItems.reduce((sum, q) => sum + getMinutesDifference(q.created_at), 0) / waitingItems.length;
                document.getElementById('avg_wait').textContent = Math.round(avgMinutes) + 'm';
            }

            // Load served today count
            dailyStatsResponse = 'TBD'; // Would be from database
            document.getElementById('served_today').textContent = queue.filter(q => q.status === 'completed').length;
        }
    } catch (error) {
        console.error('Error loading queue data:', error);
    }
}

// Update next customers table
function updateNextCustomersTable(queue) {
    const tbody = document.getElementById('next_customers_table');
    if (!tbody) return;

    let html = '';
    queue.forEach((item, index) => {
        const waitTime = getMinutesDifference(item.created_at);
        html += `
            <tr>
                <td>${index + 1}</td>
                <td>${item.customer_name || 'N/A'}</td>
                <td>${item.service_name}</td>
                <td>${waitTime} min</td>
            </tr>
        `;
    });

    tbody.innerHTML = html || '<tr><td colspan="4">No customers in queue</td></tr>';
}

// Initialize staff dashboard
document.addEventListener('DOMContentLoaded', function() {
    loadStaffCounter();

    // Handle add queue form
    const addQueueForm = document.getElementById('addQueueForm');
    if (addQueueForm) {
        addQueueForm.addEventListener('submit', addCustomerToQueue);
    }

    // Auto-refresh queue every 5 seconds
    setInterval(loadQueueData, 5000);
});
