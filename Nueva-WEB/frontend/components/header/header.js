var headerComponent = {
    init() {
        this.setupThemeToggle();
    },

    setupThemeToggle() {
        var themeToggle = document.getElementById('theme-toggle');
        var themeStylesheet = document.getElementById('theme-stylesheet');
        
        var savedTheme = localStorage.getItem('theme') || 'light';
        this.setTheme(savedTheme);
        
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                var currentTheme = themeStylesheet.getAttribute('href').includes('light') ? 'light' : 'dark';
                var newTheme = currentTheme === 'light' ? 'dark' : 'light';
                this.setTheme(newTheme);
            });
        }
    },

    setTheme(theme) {
        var themeStylesheet = document.getElementById('theme-stylesheet');
        var themeToggle = document.getElementById('theme-toggle');
        
        themeStylesheet.setAttribute('href', '/Nueva-WEB/frontend/styles/themes/' + theme + '.css');
        localStorage.setItem('theme', theme);
        
        if (themeToggle) {
            themeToggle.textContent = theme === 'light' ? '🌙' : '☀️';
        }
    }
};