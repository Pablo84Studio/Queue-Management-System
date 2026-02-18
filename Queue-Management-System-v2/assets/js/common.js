// Common JavaScript Functions

// API Helper
async function api(endpoint, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {},
        // ensure session cookie is sent to same-origin API endpoints
        credentials: 'same-origin'
    };

    if (data) {
        // For PHP endpoints expecting form-encoded POST data, send as URL-encoded
        if (method.toUpperCase() === 'GET') {
            // append query parameters for GET requests
            const params = new URLSearchParams(data).toString();
            endpoint += (endpoint.includes('?') ? '&' : '?') + params;
        } else {
            options.headers['Content-Type'] = 'application/x-www-form-urlencoded;charset=UTF-8';
            options.body = new URLSearchParams(data).toString();
        }
    }

    try {
        const response = await fetch(endpoint, options);
        if (!response.ok && response.status !== 404) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        return { success: false, message: error.message };
    }
}

// Show Alert
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    const container = document.querySelector('.main-content') || document.body;
    container.insertBefore(alertDiv, container.firstChild);

    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Format Date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric'
    });
}

// Format Time
function formatTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Format DateTime
function formatDateTime(dateString) {
    return formatDate(dateString) + ' ' + formatTime(dateString);
}

// Calculate time difference in minutes
function getMinutesDifference(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = Math.floor((now - date) / 60000);
    return diff;
}

// Update current time
function updateCurrentTime() {
    const timeElement = document.getElementById('current_time');
    if (timeElement) {
        const now = new Date();
        timeElement.textContent = now.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }
}

// Modal Functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
    }
});

// Format currency
function formatCurrency(amount) {
    return '$' + parseFloat(amount).toFixed(2);
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Play sound
function playSound(soundId) {
    const audio = document.getElementById(soundId);
    if (audio) {
        audio.play().catch(err => console.error('Error playing sound:', err));
    }
}

// Text to speech
function speakText(text) {
    if ('speechSynthesis' in window) {
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.rate = 1;
        utterance.pitch = 1;
        speechSynthesis.speak(utterance);
    }
}

// Get date range
function getDateRange(rangeType) {
    const today = new Date();
    let startDate, endDate;

    switch (rangeType) {
        case 'day':
            startDate = new Date(today);
            endDate = new Date(today);
            break;
        case 'week':
            const day = today.getDay();
            const diff = today.getDate() - day;
            startDate = new Date(today.setDate(diff));
            endDate = new Date();
            break;
        case 'month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = new Date();
            break;
        default:
            startDate = new Date(today);
            endDate = new Date(today);
    }

    return {
        start: formatDateForInput(startDate),
        end: formatDateForInput(endDate)
    };
}

// Format date for input
function formatDateForInput(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Load services into select
async function loadServices(selectId) {
    try {
        // In a real application, you would fetch from API
        const services = [
            { id: 1, name: 'General Inquiry' },
            { id: 2, name: 'Registration' },
            { id: 3, name: 'Payment Processing' },
            { id: 4, name: 'Troubleshooting' },
            { id: 5, name: 'Document Submission' }
        ];

        const select = document.getElementById(selectId);
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

// Load staff into select
async function loadStaff(selectId) {
    try {
        // In a real application, you would fetch from API
        const select = document.getElementById(selectId);
        if (select) {
            // Placeholder - will be populated from database
        }
    } catch (error) {
        console.error('Error loading staff:', error);
    }
}

// Session timeout check
function checkSessionTimeout() {
    const lastActivity = sessionStorage.getItem('lastActivity');
    const timeout = 30 * 60 * 1000; // 30 minutes

    if (lastActivity) {
        const elapsed = Date.now() - parseInt(lastActivity);
        if (elapsed > timeout) {
            window.location.href = '/index.php';
        }
    }

    sessionStorage.setItem('lastActivity', Date.now());
}

// Update time display
setInterval(updateCurrentTime, 1000);

// Check session every minute
setInterval(checkSessionTimeout, 60000);

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCurrentTime();
    checkSessionTimeout();
});
