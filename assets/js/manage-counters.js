// Manage Counters JavaScript

// Load counters
async function loadCounters() {
    try {
        // Fetch counters data
        const counters = [
            { id: 1, name: 'Counter 1', service_name: 'General Inquiry', staff_name: 'John Smith', status: 'open', waiting_count: 3 },
            { id: 2, name: 'Counter 2', service_name: 'Registration', staff_name: 'Jane Doe', status: 'open', waiting_count: 5 },
            { id: 3, name: 'Counter 3', service_name: 'Payment Processing', staff_name: null, status: 'closed', waiting_count: 0 }
        ];

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
function editCounter(counterId) {
    // Would fetch counter data from API
    openModal('counterModal');
    document.querySelector('#counterModal .modal-header h2').textContent = 'Edit Counter';
}

// Delete counter
function deleteCounter(counterId) {
    if (confirm('Are you sure you want to delete this counter?')) {
        showAlert('Counter deleted', 'success');
        loadCounters();
    }
}

// Handle counter form submission
document.addEventListener('DOMContentLoaded', function() {
    loadCounters();
    loadServicesForCounters();
    loadStaffForAssignment();

    const counterForm = document.getElementById('counterForm');
    if (counterForm) {
        counterForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const counterId = document.getElementById('counter_id').value;
            const counterName = document.getElementById('counter_name').value;
            const serviceId = document.getElementById('service_id').value;
            const staffId = document.getElementById('staff_id').value;

            // Submit form (API call would go here)
            showAlert('Counter saved successfully', 'success');
            setTimeout(() => {
                closeCounterModal();
                loadCounters();
            }, 1000);
        });
    }
});
