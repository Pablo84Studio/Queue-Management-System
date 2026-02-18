// Manage Counters JavaScript

// Load counters
async function loadCounters() {
    try {
        const response = await api('../api/counters.php?action=get_all', 'GET');
        const counters = response.success ? response.data : [];

        const container = document.getElementById('counters_container');
        if (!container) return;

        let html = '';
        counters.forEach(counter => {
            const statusClass = counter.status;
            html += `
                <div class="counter-box ${statusClass}">
                    <h3>${counter.name}</h3>
                    <p>${counter.service_name}</p>
                    <span class="counter-status ${statusClass}">${counter.status.toUpperCase()}</span>
                    <p><small>Staff: ${counter.staff_name || 'Unassigned'}</small></p>
                    <p><small>Waiting: ${counter.waiting_count}</small></p>
                    <div style="margin-top: 10px;">
                        <button class="btn btn-sm btn-info" onclick="editCounter(${counter.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteCounter(${counter.id})">Delete</button>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html || '<p>No counters found</p>';
    } catch (error) {
        console.error('Error loading counters:', error);
    }
}

// Load services
async function loadServicesForCounters() {
    try {
        // Hardcoded services for now (could be fetched from API)
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

        // Load services table
        loadServicesTable();
    } catch (error) {
        console.error('Error loading services:', error);
    }
}

// Load services table
function loadServicesTable() {
    const tbody = document.getElementById('services_table');
    if (!tbody) return;

    const services = [
        { id: 1, name: 'General Inquiry', status: 'active', counters: 2 },
        { id: 2, name: 'Registration', status: 'active', counters: 1 },
        { id: 3, name: 'Payment Processing', status: 'active', counters: 1 },
        { id: 4, name: 'Troubleshooting', status: 'active', counters: 1 },
        { id: 5, name: 'Document Submission', status: 'active', counters: 0 }
    ];

    let html = '';
    services.forEach(service => {
        html += `
            <tr>
                <td>${service.name}</td>
                <td><span class="badge badge-success">${service.status.toUpperCase()}</span></td>
                <td>${service.counters}</td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
}

// Load staff for assignment
async function loadStaffForAssignment() {
    try {
        const staff = [
            { id: 2, name: 'John Smith' },
            { id: 3, name: 'Jane Doe' },
            { id: 4, name: 'Mike Johnson' }
        ];

        const select = document.getElementById('staff_id');
        if (select) {
            staff.forEach(member => {
                const option = document.createElement('option');
                option.value = member.id;
                option.textContent = member.name;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading staff:', error);
    }
}

// Show add counter modal
function showAddCounterModal() {
    document.getElementById('counterForm').reset();
    document.getElementById('counter_id').value = '';
    openModal('counterModal');
    const heading = document.querySelector('#counterModal .modal-header h2');
    heading.textContent = 'Add New Counter';
}

// Close counter modal
function closeCounterModal() {
    closeModal('counterModal');
}

// Edit counter
async function editCounter(counterId) {
    try {
        const response = await api(`../api/counters.php?action=get&id=${counterId}`, 'GET');
        if (response.success && response.data) {
            const counter = response.data;
            document.getElementById('counter_id').value = counter.id;
            document.getElementById('counter_name').value = counter.name;
            document.getElementById('service_id').value = counter.service_id;
            document.getElementById('staff_id').value = counter.staff_id || '';
            openModal('counterModal');
            document.querySelector('#counterModal .modal-header h2').textContent = 'Edit Counter';
        } else {
            showAlert('Counter not found', 'danger');
        }
    } catch (error) {
        console.error('Error loading counter:', error);
        showAlert('Error loading counter', 'danger');
    }
}

// Delete counter
async function deleteCounter(counterId) {
    if (!confirm('Are you sure you want to delete this counter?')) {
        return;
    }

    try {
        const response = await api('../api/counters.php?action=delete', 'POST', {
            counter_id: counterId
        });

        if (response.success) {
            showAlert('Counter deleted successfully', 'success');
            loadCounters();
        } else {
            showAlert(response.message || 'Error deleting counter', 'danger');
        }
    } catch (error) {
        console.error('Error deleting counter:', error);
        showAlert('Error deleting counter', 'danger');
    }
}

// Handle counter form submission
document.addEventListener('DOMContentLoaded', function() {
    loadCounters();
    loadServicesForCounters();
    loadStaffForAssignment();

    const counterForm = document.getElementById('counterForm');
    if (counterForm) {
        counterForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const counterId = document.getElementById('counter_id').value;
            const counterName = document.getElementById('counter_name').value;
            const serviceId = document.getElementById('service_id').value;
            const staffId = document.getElementById('staff_id').value;

            if (!counterName || !serviceId) {
                showAlert('Please fill in required fields', 'warning');
                return;
            }

            try {
                let response;
                if (counterId) {
                    // Edit existing counter
                    response = await api('../api/counters.php?action=edit', 'POST', {
                        counter_id: counterId,
                        name: counterName,
                        service_id: serviceId,
                        status: 'closed'
                    });
                } else {
                    // Add new counter
                    response = await api('../api/counters.php?action=add', 'POST', {
                        name: counterName,
                        service_id: serviceId
                    });
                }

                if (response.success) {
                    showAlert(counterId ? 'Counter updated successfully' : 'Counter added successfully', 'success');
                    setTimeout(() => {
                        closeCounterModal();
                        loadCounters();
                    }, 1000);
                } else {
                    showAlert(response.message || 'Error saving counter', 'danger');
                }
            } catch (error) {
                console.error('Error saving counter:', error);
                showAlert('Error saving counter', 'danger');
            }
        });
    }
});
