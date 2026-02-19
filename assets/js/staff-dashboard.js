// Staff Dashboard JavaScript

let currentCustomer = null;
let currentCounter = null;

// Load staff counter
async function loadStaffCounter() {
    try {
        // Fetch current staff counter from database
        const response = await api('../api/counters.php?action=get_staff_counter', 'GET');
        
        if (response.success && response.data) {
            currentCounter = response.data;

            const counterInfo = document.getElementById('counter_info');
            const counterActions = document.getElementById('counter_actions');
            if (counterInfo && counterActions) {
                const statusColor = currentCounter.status === 'open' ? 'green' : currentCounter.status === 'closed' ? 'red' : 'orange';
                counterInfo.innerHTML = `
                    <h3 style="margin: 0 0 10px 0;">${currentCounter.name}</h3>
                    <p style="margin: 5px 0;">Service: <strong>${currentCounter.service_name || 'N/A'}</strong></p>
                    <p style="margin: 5px 0;">Status: <strong style="color: ${statusColor};">${currentCounter.status.toUpperCase()}</strong></p>
                `;
                counterActions.style.display = 'block';
            }
        } else {
            const counterInfo = document.getElementById('counter_info');
            if (counterInfo) {
                counterInfo.innerHTML = '<p style="color: red;">No counter assigned to you. Please contact admin.</p>';
            }
        }

        loadServices();
        loadQueueData();
    } catch (error) {
        console.error('Error loading counter:', error);
        const counterInfo = document.getElementById('counter_info');
        if (counterInfo) {
            counterInfo.innerHTML = '<p style="color: red;">Error loading counter information</p>';
        }
    }
}

// Load services
async function loadServices() {
    try {
        // Fetch services from API
        const response = await api('../api/counters.php?action=get_services', 'GET');
        const services = response.success ? response.data : [];

        const select = document.getElementById('service_id');
        if (select) {
            // Clear existing options except the first one
            while (select.options.length > 1) {
                select.remove(1);
            }
            
            services.forEach(service => {
                const option = document.createElement('option');
                option.value = service.id;
                option.textContent = service.name;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading services:', error);
        // Fallback to default services if API fails
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

        console.log('Add to queue response:', response);

        if (response.success) {
            showAlert(`Customer added to queue - Ticket #${response.ticket_number}`, 'success');
            document.getElementById('addQueueForm').reset();
            loadQueueData();
            
            // Play notification sound
            playSound('notificationAudio');
        } else {
            const errorMsg = response.message || response.error || 'Error adding customer';
            console.error('API returned error:', errorMsg);
            showAlert(errorMsg, 'danger');
        }
    } catch (error) {
        console.error('Error adding customer:', error);
        showAlert('Error adding customer: ' + error.message, 'danger');
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

        console.log('Call next customer response:', response);

        if (response.success) {
            // Store both top-level data and nested data
            currentCustomer = {
                ticket_number: response.ticket_number,
                queue_id: response.queue_id,
                data: response.data
            };
            
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
        // Get customer data from either top-level or nested data property
        const customerData = currentCustomer.data || currentCustomer;
        
        const ticketNumber = currentCustomer.ticket_number || customerData.ticket_number || 'N/A';
        const customerName = customerData.customer_name || 'N/A';
        const phone = customerData.phone || 'N/A';
        const serviceName = customerData.service_name || 'General Inquiry';
        
        console.log('Customer data:', customerData);
        
        container.innerHTML = `
            <h3 style="color: #27ae60; margin-bottom: 15px;">TICKET #${ticketNumber}</h3>
            <p><strong>Customer:</strong> ${customerName}</p>
            <p><strong>Phone:</strong> ${phone}</p>
            <p><strong>Service:</strong> ${serviceName}</p>
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
    if (!currentCounter) {
        showAlert('No counter assigned', 'warning');
        return;
    }

    const isOnBreak = document.getElementById('call_next_btn')?.disabled;
    if (isOnBreak) {
        // Resume
        document.getElementById('counter_info').innerHTML = `
            <h3 style="margin: 0 0 10px 0;">${currentCounter.name}</h3>
            <p style="margin: 5px 0;">Service: <strong>${currentCounter.service_name || 'N/A'}</strong></p>
            <p style="margin: 5px 0;">Status: <strong style="color: green;">OPEN</strong></p>
        `;
        document.getElementById('call_next_btn').disabled = false;
        showAlert('Resume service', 'success');
    } else {
        // Take break
        document.getElementById('counter_info').innerHTML = `
            <h3 style="margin: 0 0 10px 0;">${currentCounter.name}</h3>
            <p style="margin: 5px 0;">Service: <strong>${currentCounter.service_name || 'N/A'}</strong></p>
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