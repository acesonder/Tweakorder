// Main application JavaScript with Ajax functionality

// Utility function for Ajax requests
function ajax(url, method, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open(method, url, true);
    
    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            try {
                const response = JSON.parse(xhr.responseText);
                callback(null, response);
            } catch (e) {
                callback(e, null);
            }
        } else {
            callback(new Error('Request failed'), null);
        }
    };
    
    xhr.onerror = function() {
        callback(new Error('Network error'), null);
    };
    
    if (method === 'GET') {
        xhr.send();
    } else if (data instanceof FormData) {
        xhr.send(data);
    } else {
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send(data);
    }
}

// Show alert message
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    const container = document.querySelector('.container');
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => {
        alertDiv.style.animation = 'fadeOut 0.5s ease-out';
        setTimeout(() => alertDiv.remove(), 500);
    }, 3000);
}

// Navigate to page
function navigateTo(page) {
    window.location.href = page;
}

// Modal functions
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
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
    }
});

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
}
