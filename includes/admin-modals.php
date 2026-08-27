<?php
// Reusable Modal System for Admin Pages
?>

<!-- Modal System -->
<div id="modalOverlay" class="modal-overlay" style="display: none;">
    <div class="modal-container">
        <div class="modal-header">
            <h3 id="modalTitle">Confirm Action</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p id="modalMessage">Are you sure you want to perform this action?</p>
            <div id="modalForm" style="display: none;">
                <input type="text" id="modalInput" placeholder="Enter value..." style="width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px;">
                <textarea id="modalTextarea" placeholder="Enter details..." style="width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; min-height: 80px; resize: vertical;"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            <button class="btn btn-primary" id="modalConfirmBtn" onclick="confirmModalAction()">Confirm</button>
        </div>
    </div>
</div>

<!-- Toast Notification System -->
<div id="toastContainer" class="toast-container"></div>

<style>
/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 10040; /* Below toast (20050), above page chrome */
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
}

@media (max-width: 768px) {
    .modal-overlay {
        z-index: 10040;
    }
}

.modal-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    animation: slideIn 0.3s ease;
}

.modal-header {
    padding: 20px 24px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 20px;
}

.modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #6b7280;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: background 0.2s;
}

.modal-close:hover {
    background: #f3f4f6;
    color: #374151;
}

.modal-body {
    padding: 0 24px;
    margin-bottom: 20px;
}

.modal-body p {
    margin: 0;
    color: #374151;
    line-height: 1.5;
}

.modal-footer {
    padding: 0 24px 24px;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 14px;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-warning:hover {
    background: #d97706;
    transform: translateY(-1px);
}

/* Toast Styles */
.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 20050; /* Above all admin modals / overlays */
    display: flex;
    flex-direction: column;
    gap: 10px;
    pointer-events: none;
}

.toast-container .toast {
    pointer-events: auto;
}

.toast {
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    padding: 16px 20px;
    min-width: 300px;
    max-width: 400px;
    border-left: 4px solid #3b82f6;
    animation: slideInRight 0.3s ease;
    position: relative;
    overflow: hidden;
}

.toast.success {
    border-left-color: #10b981;
}

.toast.error {
    border-left-color: #ef4444;
}

.toast.warning {
    border-left-color: #f59e0b;
}

.toast-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
}

.toast-icon {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
    color: white;
}

.toast.success .toast-icon {
    background: #10b981;
}

.toast.error .toast-icon {
    background: #ef4444;
}

.toast.warning .toast-icon {
    background: #f59e0b;
}

.toast-title {
    font-weight: 600;
    color: #1f2937;
    font-size: 14px;
}

.toast-message {
    color: #6b7280;
    font-size: 14px;
    line-height: 1.4;
}

.toast-close {
    position: absolute;
    top: 8px;
    right: 8px;
    background: none;
    border: none;
    font-size: 16px;
    cursor: pointer;
    color: #9ca3af;
    padding: 4px;
    border-radius: 4px;
    transition: all 0.2s;
}

.toast-close:hover {
    background: #f3f4f6;
    color: #374151;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from { 
        opacity: 0;
        transform: translateY(-20px) scale(0.95);
    }
    to { 
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideOutRight {
    from {
        opacity: 1;
        transform: translateX(0);
    }
    to {
        opacity: 0;
        transform: translateX(100%);
    }
}

/* Responsive */
@media (max-width: 640px) {
    .modal-container {
        width: 95%;
        margin: 20px;
    }
    
    .modal-header {
        padding: 16px 20px 0;
    }
    
    .modal-body {
        padding: 0 20px;
    }
    
    .modal-footer {
        padding: 0 20px 20px;
        flex-direction: column;
    }
    
    .toast {
        min-width: 280px;
        max-width: 90vw;
    }
    
    .toast-container {
        right: 10px;
        left: 10px;
    }
}
</style>

<script>
// Modal System
let modalCallback = null;
let modalData = {};

function showModal(title, message, type = 'confirm', callback = null, data = {}) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    document.getElementById('modalOverlay').style.display = 'flex';
    
    const form = document.getElementById('modalForm');
    const input = document.getElementById('modalInput');
    const textarea = document.getElementById('modalTextarea');
    const confirmBtn = document.getElementById('modalConfirmBtn');
    
    // Reset form
    form.style.display = 'none';
    input.style.display = 'none';
    textarea.style.display = 'none';
    
    // Set button style based on type
    confirmBtn.className = 'btn';
    if (type === 'danger') {
        confirmBtn.classList.add('btn-danger');
        confirmBtn.textContent = 'Delete';
    } else if (type === 'warning') {
        confirmBtn.classList.add('btn-warning');
        confirmBtn.textContent = 'Confirm';
    } else {
        confirmBtn.classList.add('btn-primary');
        confirmBtn.textContent = 'Confirm';
    }
    
    // Show form if needed
    if (data.input) {
        form.style.display = 'block';
        input.style.display = 'block';
        input.placeholder = data.input.placeholder || 'Enter value...';
        input.value = data.input.value || '';
        input.focus();
    } else if (data.textarea) {
        form.style.display = 'block';
        textarea.style.display = 'block';
        textarea.placeholder = data.textarea.placeholder || 'Enter details...';
        textarea.value = data.textarea.value || '';
        textarea.focus();
    }
    
    modalCallback = callback;
    modalData = data;
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('modalOverlay').style.display = 'none';
    document.body.style.overflow = '';
    modalCallback = null;
    modalData = {};
}

function confirmModalAction() {
    const input = document.getElementById('modalInput');
    const textarea = document.getElementById('modalTextarea');
    
    let inputValue = null;
    if (input.style.display !== 'none') {
        inputValue = input.value.trim();
    } else if (textarea.style.display !== 'none') {
        inputValue = textarea.value.trim();
    }
    
    if (modalCallback) {
        modalCallback(inputValue);
    }
    
    closeModal();
}

// Toast System
function showToast(message, type = 'info', title = null) {
    let container = document.getElementById('toastContainer');
    
    // Always mount toasts on document.body so they sit above modals/overlays
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container';
        (document.body || document.documentElement).appendChild(container);
    } else if (container.parentElement !== document.body && document.body) {
        document.body.appendChild(container);
    }
    
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    const icon = type === 'success' ? '✓' : type === 'error' ? '✕' : type === 'warning' ? '⚠' : 'ℹ';
    const defaultTitle = type === 'success' ? 'Success' : type === 'error' ? 'Error' : type === 'warning' ? 'Warning' : 'Info';
    
    toast.innerHTML = `
        <div class="toast-header">
            <div class="toast-icon">${icon}</div>
            <div class="toast-title">${title || defaultTitle}</div>
        </div>
        <div class="toast-message">${message}</div>
        <button class="toast-close" onclick="removeToast(this)">&times;</button>
    `;
    
    if (container) {
        container.appendChild(toast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                removeToast(toast.querySelector('.toast-close'));
            }
        }, 5000);
    }
}

function removeToast(closeBtn) {
    const toast = closeBtn.closest('.toast');
    toast.style.animation = 'slideOutRight 0.3s ease';
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 300);
}

// Keep toast container on body so it never stacks under modals
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('toastContainer');
    if (container && document.body && container.parentElement !== document.body) {
        document.body.appendChild(container);
    }
});

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.id === 'modalOverlay') {
        closeModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

// Surface PHP flash messages as toasts on all admin pages
document.addEventListener('DOMContentLoaded', function () {
    <?php if (!empty($_SESSION['success'])): ?>
    if (typeof showToast === 'function') {
        showToast(<?php echo json_encode((string)$_SESSION['success']); ?>, 'success');
    }
    <?php unset($_SESSION['success']); endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
    if (typeof showToast === 'function') {
        showToast(<?php echo json_encode((string)$_SESSION['error']); ?>, 'error');
    }
    <?php unset($_SESSION['error']); endif; ?>
});
</script>
