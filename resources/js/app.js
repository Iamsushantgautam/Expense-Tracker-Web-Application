import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

// Global Theme Management
window.ExpenseTracker = {
    initTheme() {
        const theme = localStorage.getItem('theme_color') || 'blue';
        const darkMode = localStorage.getItem('dark_mode') === 'true';

        this.applyTheme(theme);
        this.applyDarkMode(darkMode);
    },

    applyTheme(color) {
        document.documentElement.setAttribute('data-theme', color);
        localStorage.setItem('theme_color', color);
    },

    applyDarkMode(isDark) {
        if (isDark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        localStorage.setItem('dark_mode', isDark);
    },

    toggleDarkMode() {
        const isDark = !document.documentElement.classList.contains('dark');
        this.applyDarkMode(isDark);
        return isDark;
    }
};

// Initialize theme as early as possible
document.addEventListener('DOMContentLoaded', () => {
    window.ExpenseTracker.initTheme();
});

Alpine.start();
