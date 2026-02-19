// Manage Staff JavaScript

// Show add staff modal
function showAddStaffModal() {
    document.getElementById('staffForm').reset();
    document.getElementById('staff_id').value = '';
    openModal('staffModal');
    const heading = document.querySelector('#staffModal .modal-header h2');
    heading.textContent = 'Add New Staff Member';
}

// Close staff modal
function closeStaffModal() {
    closeModal('staffModal');
}

// Edit staff
async function editStaff(staffId) {
    try {
        // Fetch staff data
        const response = await api(`../api/staff.php?action=get&id=${staffId}`);
        if (response.success && response.data) {
            const staff = response.data;
            document.getElementById('staff_id').value = staff.id;
            document.getElementById('name').value = staff.name;
            document.getElementById('username').value = staff.username;
            document.getElementById('email').value = staff.email;
            document.getElementById('status').value = staff.status;

            const heading = document.querySelector('#staffModal .modal-header h2');
            heading.textContent = 'Edit Staff Member';

            openModal('staffModal');
        }
    } catch (error) {
        console.error('Error fetching staff:', error);
        showAlert('Error loading staff data', 'danger');
    }
}

// Delete staff
async function deleteStaff(staffId) {
    if (confirm('Are you sure you want to delete this staff member?')) {
        try {
            const response = await api('../api/staff.php?action=delete', 'POST', { staff_id: staffId });
            if (response.success) {
                showAlert('Staff member deleted successfully', 'success');
                location.reload();
            } else {
                showAlert(response.message || 'Error deleting staff', 'danger');
            }
        } catch (error) {
            console.error('Error deleting staff:', error);
            showAlert('Error deleting staff', 'danger');
        }
    }
}

// Handle form submission
document.addEventListener('DOMContentLoaded', function() {
    const staffForm = document.getElementById('staffForm');
    if (staffForm) {
        staffForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const staffId = document.getElementById('staff_id').value;
            const name = document.getElementById('name').value;
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const status = document.getElementById('status').value;

            const data = {
                name,
                username,
                email,
                status
            };

            if (password) {
                data.password = password;
            }

            if (staffId) {
                data.staff_id = staffId;
            }

            try {
                const action = staffId ? 'edit' : 'add';
                const response = await api('../api/staff.php?action=' + action, 'POST', data);

                if (response.success) {
                    showAlert(response.message || 'Staff member saved successfully', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert(response.message || 'Error saving staff', 'danger');
                }
            } catch (error) {
                console.error('Error saving staff:', error);
                showAlert('Error saving staff', 'danger');
            }
        });
    }
});