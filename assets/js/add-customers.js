// Admin Add Customers JavaScript

let adminServices = [];

// Load admin services
async function loadAdminServices() {
    try {
        const response = await api('../api/queue.php?action=get_admin_services', 'GET');
        
        console.log('Admin services response:', response);
        
        if (response.success && response.data && Array.isArray(response.data)) {
            adminServices = response.data;
            
            // Populate service select
            const select = document.getElementById('service_id');
            if (select) {
                select.innerHTML = '<option value="">Select Service</option>';
                adminServices.forEach(service => {
                    const option = document.createElement('option');
                    option.value = service.id;
                    option.textContent = `${service.name} (${service.waiting_count} waiting)`;
                    select.appendChild(option);
                });
            }
            
            // Update services status display
            updateServicesStatus();
        } else {
            console.error('Failed to load admin services:', response.message);
            showAlert('Failed to load your services: ' + (response.message || 'Unknown error'), 'danger');
        }
    } catch (error) {
        console.error('Error loading admin services:', error);
        showAlert('Error loading services', 'danger');
    }
}

// Update services status display
function updateServicesStatus() {
    const container = document.getElementById('services_status');
    if (!container) return;
    
    if (adminServices.length === 0) {
        container.innerHTML = '<p style="color: orange;">No services assigned to you</p>';
        return;
    }
    
    let html = '<div class="services-grid">';
    adminServices.forEach(service => {
        const statusColor = service.waiting_count === 0 ? '#27ae60' : service.waiting_count <= 5 ? '#f39c12' : '#e74c3c';
        html += `
            <div class="service-card" style="padding: 15px; border-left: 4px solid ${statusColor}; margin-bottom: 10px;">
                <h4>${service.name}</h4>
                <p style="margin: 5px 0;"><strong>Waiting:</strong> <span style="color: ${statusColor}; font-size: 1.2em;">${service.waiting_count}</span></p>
                <p style="margin: 5px 0; font-size: 0.9em; color: #666;">${service.description || 'No description'}</p>
            </div>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
}

// Add customer to queue
async function addCustomerToQueue(e) {
    if (e) e.preventDefault();
    
    const serviceId = document.getElementById('service_id')?.value;
    const customerName = document.getElementById('customer_name')?.value;
    const phone = document.getElementById('phone')?.value;
    
    if (!serviceId || !customerName) {
        showAlert('Please fill in required fields', 'warning');
        return;
    }
    
    try {
        const response = await api('../api/queue.php?action=add', 'POST', {
            service_id: serviceId,
            customer_name: customerName,
            phone: phone
        });
        
        console.log('Add customer response:', response);
        
        if (response.success) {
            showAlert(`Customer added successfully - Ticket #${response.ticket_number}`, 'success');
            document.getElementById('addCustomerForm').reset();
            loadAdminServices();
            loadRecentCustomers();
        } else {
            showAlert(response.message || 'Error adding customer', 'danger');
        }
    } catch (error) {
        console.error('Error adding customer:', error);
        showAlert('Error adding customer: ' + error.message, 'danger');
    }
}

// Load recent customers added
async function loadRecentCustomers() {
    try {
        const response = await api('../api/queue.php?action=get_current', 'GET');
        const tbody = document.getElementById('recent_customers');
        
        if (!tbody) return;
        
        if (response.success && response.data && Array.isArray(response.data)) {
            const queue = response.data.slice(0, 10);
            
            if (queue.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">No customers in queue</td></tr>';
                return;
            }
            
            let html = '';
            queue.forEach(item => {
                const timeAdded = getMinutesDifference(item.created_at);
                html += `
                    <tr>
                        <td><strong>#${item.ticket_number}</strong></td>
                        <td>${item.customer_name || 'N/A'}</td>
                        <td>${item.service_name || 'N/A'}</td>
                        <td>${timeAdded} min ago</td>
                        <td><span class="badge badge-${item.status === 'waiting' ? 'warning' : item.status === 'called' ? 'info' : 'success'}">${item.status.toUpperCase()}</span></td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
        } else {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Error loading queue</td></tr>';
        }
    } catch (error) {
        console.error('Error loading recent customers:', error);
    }
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadAdminServices();
    loadRecentCustomers();
    
    // Handle form submission
    const form = document.getElementById('addCustomerForm');
    if (form) {
        form.addEventListener('submit', addCustomerToQueue);
    }
    
    // Auto-refresh every 10 seconds
    setInterval(function() {
        loadAdminServices();
        loadRecentCustomers();
    }, 10000);
});
