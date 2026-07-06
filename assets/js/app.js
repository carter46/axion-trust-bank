// SecureBank Online - Main JavaScript

const App = {
    init() {
        console.log('[App] App.init() called');
        this.setupNavigation();
        this.setupFormValidation();
        this.setupAlerts();
        this.setupModals();
        this.setupAJAX();
        this.setupAnimations();
        this.setupCharts();
        console.log('[App] All initializations complete');
    },

    // Navigation
    setupNavigation() {
        // Skip mobile menu setup - it's handled by inline script in header.php
        // This prevents duplicate event listeners and conflicts
        
        // Only handle old navbar menu if it exists (for backward compatibility)
        const toggle = document.querySelector('.navbar-toggle');
        const menu = document.querySelector('.navbar-menu');

        if (toggle && menu && !document.getElementById('hamburger')) {
            // Only setup if new mobile menu doesn't exist
            toggle.addEventListener('click', () => {
                menu.classList.toggle('active');
            });

            // Close menu on link click (mobile)
            const menuLinks = document.querySelectorAll('.navbar-menu a');
            menuLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 768) {
                        menu.classList.remove('active');
                    }
                });
            });
        }
    },

    // Form Validation
    setupFormValidation() {
        const forms = document.querySelectorAll('form[data-validate]');

        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
            });

            // Real-time validation
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    this.validateField(input);
                });

                input.addEventListener('input', () => {
                    if (input.classList.contains('is-invalid')) {
                        this.validateField(input);
                    }
                });
            });
        });

        // Password strength meter
        const passwordInputs = document.querySelectorAll('input[type="password"][data-strength]');
        passwordInputs.forEach(input => {
            const meter = document.createElement('div');
            meter.className = 'password-strength-meter';
            input.parentNode.appendChild(meter);

            input.addEventListener('input', () => {
                this.updatePasswordStrength(input, meter);
            });
        });
    },

    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        return isValid;
    },

    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';

        // Required check
        if (field.hasAttribute('required') && value === '') {
            isValid = false;
            errorMessage = 'This field is required';
        }

        // Email validation
        if (field.type === 'email' && value !== '') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid email address';
            }
        }

        // Password validation
        if (field.type === 'password' && field.hasAttribute('data-validate-password') && value !== '') {
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            if (!passwordRegex.test(value)) {
                isValid = false;
                errorMessage = 'Password must be at least 8 characters with uppercase, lowercase, number and special character';
            }
        }

        // Confirm password
        if (field.hasAttribute('data-confirm')) {
            const originalField = document.querySelector(`[name="${field.getAttribute('data-confirm')}"]`);
            if (originalField && value !== originalField.value) {
                isValid = false;
                errorMessage = 'Passwords do not match';
            }
        }

        // Min/Max length
        if (field.hasAttribute('minlength') && value.length < field.getAttribute('minlength')) {
            isValid = false;
            errorMessage = `Minimum ${field.getAttribute('minlength')} characters required`;
        }

        if (field.hasAttribute('maxlength') && value.length > field.getAttribute('maxlength')) {
            isValid = false;
            errorMessage = `Maximum ${field.getAttribute('maxlength')} characters allowed`;
        }

        // Number validation
        if (field.type === 'number' && value !== '') {
            const num = parseFloat(value);
            if (field.hasAttribute('min') && num < parseFloat(field.getAttribute('min'))) {
                isValid = false;
                errorMessage = `Minimum value is ${field.getAttribute('min')}`;
            }
            if (field.hasAttribute('max') && num > parseFloat(field.getAttribute('max'))) {
                isValid = false;
                errorMessage = `Maximum value is ${field.getAttribute('max')}`;
            }
        }

        // Update UI
        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            this.removeError(field);
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            this.showError(field, errorMessage);
        }

        return isValid;
    },

    showError(field, message) {
        this.removeError(field);
        const error = document.createElement('div');
        error.className = 'form-error';
        error.textContent = message;
        field.parentNode.appendChild(error);
    },

    removeError(field) {
        const error = field.parentNode.querySelector('.form-error');
        if (error) {
            error.remove();
        }
    },

    updatePasswordStrength(input, meter) {
        const value = input.value;
        let strength = 0;

        if (value.length >= 8) strength++;
        if (/[a-z]/.test(value)) strength++;
        if (/[A-Z]/.test(value)) strength++;
        if (/\d/.test(value)) strength++;
        if (/[@$!%*?&]/.test(value)) strength++;

        const strengthLevels = ['weak', 'fair', 'good', 'strong', 'very-strong'];
        const strengthTexts = ['Weak', 'Fair', 'Good', 'Strong', 'Very Strong'];

        meter.className = 'password-strength-meter ' + (strengthLevels[strength - 1] || '');
        meter.textContent = value.length > 0 ? strengthTexts[strength - 1] || 'Weak' : '';
    },

    // Alerts
    setupAlerts() {
        const closeButtons = document.querySelectorAll('.alert-close');
        closeButtons.forEach(button => {
            button.addEventListener('click', () => {
                button.parentElement.remove();
            });
        });

        // Auto-dismiss alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert[data-auto-dismiss]');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });
    },

    showAlert(message, type = 'info') {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.innerHTML = `
            ${message}
            <button class="alert-close">&times;</button>
        `;

        const container = document.querySelector('.container');
        if (container) {
            container.insertBefore(alert, container.firstChild);
            this.setupAlerts();
        }
    },

    // Modals
    setupModals() {
        const modalTriggers = document.querySelectorAll('[data-modal]');
        modalTriggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const modalId = trigger.getAttribute('data-modal');
                this.openModal(modalId);
            });
        });

        const modalCloses = document.querySelectorAll('[data-modal-close]');
        modalCloses.forEach(close => {
            close.addEventListener('click', () => {
                this.closeModal();
            });
        });

        // Close on backdrop click
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-backdrop')) {
                this.closeModal();
            }
        });
    },

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    },

    closeModal() {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.style.display = 'none';
        });
        document.body.style.overflow = '';
    },

    // AJAX
    setupAJAX() {
        // Card freeze/unfreeze
        const cardToggleButtons = document.querySelectorAll('[data-card-toggle]');
        cardToggleButtons.forEach(button => {
            button.addEventListener('click', async () => {
                const cardId = button.getAttribute('data-card-id');
                const action = button.getAttribute('data-card-toggle');
                
                try {
                    const response = await fetch(`/card/${action}/${cardId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.showAlert(data.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        this.showAlert(data.message, 'error');
                    }
                } catch (error) {
                    this.showAlert('An error occurred. Please try again.', 'error');
                }
            });
        });

        // Show card details
        const showCardButtons = document.querySelectorAll('[data-show-card]');
        showCardButtons.forEach(button => {
            button.addEventListener('click', async () => {
                const cardId = button.getAttribute('data-show-card');
                
                try {
                    const response = await fetch(`/card/show-details/${cardId}`);
                    const data = await response.json();

                    if (data.success) {
                        alert(`Card Number: ${data.card_number}\nCVV: ${data.cvv}\nExpiry: ${data.expiry}`);
                    } else {
                        this.showAlert(data.message, 'error');
                    }
                } catch (error) {
                    this.showAlert('An error occurred. Please try again.', 'error');
                }
            });
        });

        // Mark notifications as read
        const notificationItems = document.querySelectorAll('[data-notification-id]');
        notificationItems.forEach(item => {
            item.addEventListener('click', async () => {
                const notificationId = item.getAttribute('data-notification-id');
                await fetch(`/api/notification/mark-read/${notificationId}`, { method: 'POST' });
            });
        });
    },

    // Animations
    setupAnimations() {
        // Fade in on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        const animatedElements = document.querySelectorAll('.animate-on-scroll');
        animatedElements.forEach(el => observer.observe(el));

        // Number counter animation
        const counters = document.querySelectorAll('[data-counter]');
        counters.forEach(counter => {
            const target = parseFloat(counter.getAttribute('data-counter'));
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;

            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    counter.textContent = Math.floor(current).toLocaleString();
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target.toLocaleString();
                }
            };

            // Start animation when element is visible
            const counterObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        updateCounter();
                        counterObserver.unobserve(entry.target);
                    }
                });
            });

            counterObserver.observe(counter);
        });
    },

    // Charts (using Chart.js if available)
    setupCharts() {
        if (typeof Chart === 'undefined') return;

        // Spending chart
        const spendingCanvas = document.getElementById('spendingChart');
        if (spendingCanvas) {
            const ctx = spendingCanvas.getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: window.chartData?.categories || [],
                    datasets: [{
                        data: window.chartData?.amounts || [],
                        backgroundColor: [
                            '#032B44', '#ADD8E6', '#5CB85C', '#F0AD4E', '#D9534F'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Transaction trend chart
        const trendCanvas = document.getElementById('trendChart');
        if (trendCanvas) {
            const ctx = trendCanvas.getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: window.trendData?.dates || [],
                    datasets: [{
                        label: 'Income',
                        data: window.trendData?.income || [],
                        borderColor: '#5CB85C',
                        backgroundColor: 'rgba(92, 184, 92, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'Expenses',
                        data: window.trendData?.expenses || [],
                        borderColor: '#D9534F',
                        backgroundColor: 'rgba(217, 83, 79, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    },

    // Utility Functions
    formatCurrency(amount, currency = 'USD') {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: currency
        }).format(amount);
    },

    formatDate(date) {
        return new Intl.DateTimeFormat('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        }).format(new Date(date));
    },

    copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            this.showAlert('Copied to clipboard!', 'success');
        });
    }
};

// Initialize on DOM ready
console.log('[App] Initializing application...');
console.log('[App] Document ready state:', document.readyState);

if (document.readyState === 'loading') {
    console.log('[App] Document still loading, waiting for DOMContentLoaded...');
    document.addEventListener('DOMContentLoaded', () => {
        console.log('[App] DOMContentLoaded fired, initializing...');
        App.init();
    });
} else {
    console.log('[App] Document already loaded, initializing immediately...');
    App.init();
}

// Export for external use
window.App = App;
