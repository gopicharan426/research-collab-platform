// Research Collaboration Platform JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    });
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
    
    // Character counter for textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        const maxLength = textarea.getAttribute('maxlength');
        if (maxLength) {
            addCharacterCounter(textarea, maxLength);
        }
    });
});

// Form validation function
function validateForm(form) {
    const inputs = form.querySelectorAll('input[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            showFieldError(input, 'This field is required');
            isValid = false;
        } else {
            clearFieldError(input);
        }
        
        // Email validation
        if (input.type === 'email' && input.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                showFieldError(input, 'Please enter a valid email address');
                isValid = false;
            }
        }
        
        // Password validation
        if (input.type === 'password' && input.value && input.value.length < 6) {
            showFieldError(input, 'Password must be at least 6 characters');
            isValid = false;
        }
    });
    
    return isValid;
}

// Show field error
function showFieldError(input, message) {
    clearFieldError(input);
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.style.color = '#dc3545';
    errorDiv.style.fontSize = '0.875rem';
    errorDiv.style.marginTop = '0.25rem';
    errorDiv.textContent = message;
    
    input.style.borderColor = '#dc3545';
    input.parentNode.appendChild(errorDiv);
}

// Clear field error
function clearFieldError(input) {
    const existingError = input.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
    input.style.borderColor = '#e9ecef';
}

// Add character counter to textarea
function addCharacterCounter(textarea, maxLength) {
    const counter = document.createElement('div');
    counter.className = 'character-counter';
    counter.style.textAlign = 'right';
    counter.style.fontSize = '0.875rem';
    counter.style.color = '#6c757d';
    counter.style.marginTop = '0.25rem';
    
    function updateCounter() {
        const remaining = maxLength - textarea.value.length;
        counter.textContent = `${remaining} characters remaining`;
        
        if (remaining < 50) {
            counter.style.color = '#dc3545';
        } else {
            counter.style.color = '#6c757d';
        }
    }
    
    textarea.addEventListener('input', updateCounter);
    textarea.parentNode.appendChild(counter);
    updateCounter();
}

// Smooth scrolling for anchor links
function smoothScroll(target) {
    document.querySelector(target).scrollIntoView({
        behavior: 'smooth'
    });
}

// Confirm before logout
function confirmLogout() {
    return confirm('Are you sure you want to logout?');
}

// Auto-expand textarea
function autoExpandTextarea(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
}

// Add auto-expand to all textareas
document.addEventListener('DOMContentLoaded', function() {
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            autoExpandTextarea(this);
        });
    });
});


// ── Notification System ──

const NOTIF_ICONS = { follow: '\uD83D\uDC64', like: '\u2764\uFE0F', comment: '\uD83D\uDCAC', new_post: '\uD83D\uDCDD' };
const NOTIF_URL = '/notifications.php';
let notifPollInterval = null;
let lastUnreadCount = 0;

// Toggle dropdown open/close
function toggleNotifPanel() {
    const panel = document.getElementById('notifPanel');
    if (!panel) return;
    const isOpen = panel.classList.contains('open');
    panel.classList.toggle('open');
    if (!isOpen) loadNotifications();
}

// Load and render all notifications
function loadNotifications() {
    fetch(NOTIF_URL + '?action=get')
        .then(r => { if (!r.ok) throw new Error(); return r.json(); })
        .then(data => {
            if (data.error) throw new Error(data.error);
            updateBadge(data.unread);
            renderNotifications(data.notifications);
        })
        .catch(() => {
            const list = document.getElementById('notifList');
            if (list) list.innerHTML = '<p class="notif-empty">No notifications yet.</p>';
        });
}

// Render notification items
function renderNotifications(notifications) {
    const list = document.getElementById('notifList');
    if (!list) return;
    if (!notifications || notifications.length === 0) {
        list.innerHTML = '<p class="notif-empty">No notifications yet.</p>';
        return;
    }
    list.innerHTML = notifications.map(n => {
        const unreadClass = n.is_read == 0 ? 'unread' : '';
        const icon = NOTIF_ICONS[n.type] || '\uD83D\uDD14';
        return `
        <div class="notif-item ${unreadClass}" 
             data-id="${n.notification_id}" 
             data-link="${n.link || ''}" 
             onclick="handleNotifClick(this)">
            <div class="notif-icon ${n.type}">${icon}</div>
            <div class="notif-content">
                <div class="notif-message">${escapeHtml(n.message)}</div>
                <div class="notif-time">${timeAgo(n.created_at)}</div>
            </div>
            ${n.is_read == 0 ? '<div class="notif-dot"></div>' : ''}
        </div>`;
    }).join('');
}

// Handle click: mark as read then redirect
function handleNotifClick(el) {
    const notifId = el.dataset.id;
    const link    = el.dataset.link;

    // Mark as read visually immediately
    el.classList.remove('unread');
    const dot = el.querySelector('.notif-dot');
    if (dot) dot.remove();

    // Send mark-as-read to backend
    const fd = new FormData();
    fd.append('action', 'mark_one_read');
    fd.append('notification_id', notifId);
    fetch(NOTIF_URL, { method: 'POST', body: fd })
        .then(() => pollUnreadCount())
        .catch(() => {});

    // Redirect if link exists
    if (link) {
        setTimeout(() => { window.location.href = link; }, 150);
    }
}

// Mark all as read
function markAllRead() {
    fetch(NOTIF_URL + '?action=mark_all_read', { method: 'POST' })
        .then(r => r.json())
        .then(() => {
            updateBadge(0);
            document.querySelectorAll('.notif-item.unread').forEach(el => {
                el.classList.remove('unread');
                const dot = el.querySelector('.notif-dot');
                if (dot) dot.remove();
            });
        })
        .catch(() => {});
}

// Update bell badge count
function updateBadge(count) {
    const badge = document.getElementById('notifBadge');
    if (!badge) return;
    const n = parseInt(count) || 0;
    if (n > 0) {
        badge.textContent = n > 99 ? '99+' : n;
        badge.style.display = 'flex';
        // Pulse animation on new notifications
        if (n > lastUnreadCount) {
            badge.classList.remove('pulse');
            void badge.offsetWidth; // reflow
            badge.classList.add('pulse');
        }
    } else {
        badge.style.display = 'none';
    }
    lastUnreadCount = n;
}

// Poll only unread count (lightweight)
function pollUnreadCount() {
    fetch(NOTIF_URL + '?action=unread_count')
        .then(r => r.json())
        .then(data => {
            const newCount = parseInt(data.count) || 0;
            updateBadge(newCount);
            // If panel is open and new notifications arrived, refresh list
            const panel = document.getElementById('notifPanel');
            if (panel && panel.classList.contains('open') && newCount > lastUnreadCount) {
                loadNotifications();
            }
        })
        .catch(() => {});
}

function timeAgo(dateStr) {
    const diff = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000);
    if (diff < 60)    return diff + 's ago';
    if (diff < 3600)  return Math.floor(diff / 60) + 'm ago';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
    return Math.floor(diff / 86400) + 'd ago';
}

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

// Close panel when clicking outside
document.addEventListener('click', function(e) {
    const wrapper = document.querySelector('.notif-wrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        const panel = document.getElementById('notifPanel');
        if (panel) panel.classList.remove('open');
    }
});

// Start polling every 5 seconds on page load
document.addEventListener('DOMContentLoaded', function() {
    if (!document.getElementById('notifBadge')) return;
    pollUnreadCount();
    notifPollInterval = setInterval(pollUnreadCount, 5000);
});
