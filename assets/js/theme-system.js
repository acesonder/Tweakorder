/**
 * Advanced Theme and Animation System
 * Handles theme switching, 3D effects, and interactive elements
 */

// Theme Management
class ThemeManager {
    constructor() {
        this.currentTheme = localStorage.getItem('theme') || 'light';
        this.init();
    }

    init() {
        this.applyTheme(this.currentTheme);
        this.createThemeSwitcher();
    }

    applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        this.currentTheme = theme;
        localStorage.setItem('theme', theme);
        
        // Update active button
        document.querySelectorAll('.theme-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.theme === theme) {
                btn.classList.add('active');
            }
        });
    }

    createThemeSwitcher() {
        const themeSwitcher = document.createElement('div');
        themeSwitcher.className = 'theme-switcher';
        themeSwitcher.innerHTML = `
            <button class="theme-btn" data-theme="light" title="Light Theme"></button>
            <button class="theme-btn" data-theme="dark" title="Dark Theme"></button>
            <button class="theme-btn" data-theme="neon" title="Neon Theme"></button>
            <button class="theme-btn" data-theme="glass" title="Glassmorphism Theme"></button>
            <button class="theme-btn" data-theme="material" title="Material Theme"></button>
            <button class="theme-btn" data-theme="retro" title="Retro Theme"></button>
        `;

        document.body.appendChild(themeSwitcher);

        // Add event listeners
        themeSwitcher.querySelectorAll('.theme-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                this.applyTheme(btn.dataset.theme);
                this.showThemeNotification(btn.title);
            });
        });

        // Mark current theme as active
        this.applyTheme(this.currentTheme);
    }

    showThemeNotification(themeName) {
        const notification = document.createElement('div');
        notification.className = 'theme-notification';
        notification.textContent = `Switched to ${themeName}`;
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--card-bg);
            color: var(--text-primary);
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 5px 20px var(--shadow);
            z-index: 10000;
            animation: slideInRight 0.3s ease-out;
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        }, 2000);
    }
}

// 3D Card Effects
class Card3DEffect {
    constructor(card) {
        this.card = card;
        this.init();
    }

    init() {
        this.card.addEventListener('mousemove', this.handleMouseMove.bind(this));
        this.card.addEventListener('mouseleave', this.handleMouseLeave.bind(this));
    }

    handleMouseMove(e) {
        const rect = this.card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        
        const rotateX = (y - centerY) / 10;
        const rotateY = (centerX - x) / 10;
        
        this.card.style.transform = `
            perspective(1000px)
            rotateX(${rotateX}deg)
            rotateY(${rotateY}deg)
            translateZ(10px)
        `;
    }

    handleMouseLeave() {
        this.card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateZ(0)';
    }
}

// Ripple Effect for Buttons
function addRippleEffect(button, event) {
    const ripple = document.createElement('span');
    const rect = button.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;

    ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.5);
        left: ${x}px;
        top: ${y}px;
        transform: scale(0);
        animation: ripple-animation 0.6s ease-out;
        pointer-events: none;
    `;

    button.appendChild(ripple);

    setTimeout(() => ripple.remove(), 600);
}

// Smooth Scroll
function smoothScroll(target) {
    const element = document.querySelector(target);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// Loading Spinner
class LoadingSpinner {
    static show() {
        const spinner = document.createElement('div');
        spinner.id = 'global-spinner';
        spinner.className = 'spinner-3d';
        spinner.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 10000;
        `;
        document.body.appendChild(spinner);
    }

    static hide() {
        const spinner = document.getElementById('global-spinner');
        if (spinner) {
            spinner.remove();
        }
    }
}

// Notification System
class NotificationManager {
    static show(message, type = 'info', duration = 3000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type} modal-blur-in`;
        
        const colors = {
            success: '#4caf50',
            error: '#f44336',
            warning: '#ff9800',
            info: '#2196f3'
        };

        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${colors[type] || colors.info};
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            z-index: 10000;
            max-width: 300px;
        `;

        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        }, duration);
    }
}

// Page Transition Effect
function pageTransition() {
    const container = document.querySelector('.container');
    if (container) {
        container.classList.add('page-transition');
    }
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', () => {
    // Initialize theme manager
    const themeManager = new ThemeManager();

    // Add 3D effects to nav cards
    document.querySelectorAll('.nav-card').forEach(card => {
        card.classList.add('nav-card-3d');
        new Card3DEffect(card);
    });

    // Add ripple effect to all buttons
    document.querySelectorAll('.btn, button').forEach(button => {
        if (!button.classList.contains('theme-btn')) {
            button.style.position = 'relative';
            button.style.overflow = 'hidden';
            button.addEventListener('click', (e) => {
                addRippleEffect(button, e);
            });
        }
    });

    // Add page transition
    pageTransition();

    // Add floating animation to random cards
    const cards = document.querySelectorAll('.nav-card');
    cards.forEach((card, index) => {
        if (index % 3 === 0) {
            card.classList.add('floating-card');
        }
    });

    // Add CSS for ripple animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    `;
    document.head.appendChild(style);
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        ThemeManager,
        Card3DEffect,
        LoadingSpinner,
        NotificationManager,
        smoothScroll
    };
}
