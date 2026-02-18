// Reports JavaScript

// Show/hide custom date fields
document.addEventListener('DOMContentLoaded', function() {
    const queuePeriod = document.getElementById('queue_period');
    const staffPeriod = document.getElementById('staff_period');

    if (queuePeriod) {
        queuePeriod.addEventListener('change', function() {
            const customGroup = document.getElementById('custom_date_group');
            const customGroup2 = document.getElementById('custom_date_group2');
            if (this.value === 'custom') {
                customGroup.style.display = 'block';
                customGroup2.style.display = 'block';
            } else {
                customGroup.style.display = 'none';
                customGroup2.style.display = 'none';
            }
        });
    }

    if (staffPeriod) {
        staffPeriod.addEventListener('change', function() {
            const customGroup = document.getElementById('staff_custom_date_group');
            const customGroup2 = document.getElementById('staff_custom_date_group2');
            if (this.value === 'custom') {
                customGroup.style.display = 'block';
                customGroup2.style.display = 'block';
            } else {
                customGroup.style.display = 'none';
                customGroup2.style.display = 'none';
            }
        });
    }

    // Load services
    loadServicesForReports();
    loadReportStatistics();

    // Handle queue report form
    const queueReportForm = document.getElementById('queueReportForm');
    if (queueReportForm) {
        queueReportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            generateQueueReport();
        });
    }

    // Handle staff report form
    const staffReportForm = document.getElementById('staffReportForm');
    if (staffReportForm) {
        staffReportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            generateStaffReport();
        });
    }
});

// Load services for reports
function loadServicesForReports() {
    const services = [
        { id: 1, name: 'General Inquiry' },
        { id: 2, name: 'Registration' },
        { id: 3, name: 'Payment Processing' },
        { id: 4, name: 'Troubleshooting' },
        { id: 5, name: 'Document Submission' }
    ];

    const select = document.getElementById('queue_service');
    if (select) {
        services.forEach(service => {
            const option = document.createElement('option');
            option.value = service.id;
            option.textContent = service.name;
            select.appendChild(option);
        });
    }
}

// Generate queue report
async function generateQueueReport() {
    const period = document.getElementById('queue_period')?.value || 'day';
    let startDate, endDate;

    if (period === 'custom') {
        startDate = document.getElementById('queue_start_date')?.value;
        endDate = document.getElementById('queue_end_date')?.value;

        if (!startDate || !endDate) {
            showAlert('Please select both start and end dates', 'warning');
            return;
        }
    } else {
        const range = getDateRange(period);
        startDate = range.start;
        endDate = range.end;
    }

    const serviceId = document.getElementById('queue_service')?.value || '';

    try {
        // Simulate PDF generation
        showAlert('Generating PDF report...', 'info');

        // In real implementation:
        // window.location.href = `../api/report.php?action=queue&start=${startDate}&end=${endDate}&service=${serviceId}`;

        // For demo
        setTimeout(() => {
            showAlert('Report generated successfully: queue_report_' + startDate + '_to_' + endDate + '.pdf', 'success');
        }, 2000);

    } catch (error) {
        console.error('Error generating report:', error);
        showAlert('Error generating report', 'danger');
    }
}

// Generate staff report
async function generateStaffReport() {
    const period = document.getElementById('staff_period')?.value || 'day';
    let startDate, endDate;

    if (period === 'custom') {
        startDate = document.getElementById('staff_start_date')?.value;
        endDate = document.getElementById('staff_end_date')?.value;

        if (!startDate || !endDate) {
            showAlert('Please select both start and end dates', 'warning');
            return;
        }
    } else {
        const range = getDateRange(period);
        startDate = range.start;
        endDate = range.end;
    }

    try {
        showAlert('Generating staff report...', 'info');

        // In real implementation:
        // window.location.href = `../api/report.php?action=staff&start=${startDate}&end=${endDate}`;

        // For demo
        setTimeout(() => {
            showAlert('Report generated successfully: staff_report_' + startDate + '_to_' + endDate + '.pdf', 'success');
        }, 2000);

    } catch (error) {
        console.error('Error generating report:', error);
        showAlert('Error generating report', 'danger');
    }
}

// Load report statistics
async function loadReportStatistics() {
    try {
        // Today
        const todayRange = getDateRange('day');
        updateStatElement('today_total', 45);

        // This week
        const weekRange = getDateRange('week');
        updateStatElement('week_total', 287);

        // This month
        const monthRange = getDateRange('month');
        updateStatElement('month_total', 1250);

        // Completion rate
        updateStatElement('completion_rate', 92 + '%');

    } catch (error) {
        console.error('Error loading statistics:', error);
    }
}

// Update stat element
function updateStatElement(elementId, value) {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = value;
    }
}
