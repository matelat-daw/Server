

var headerComponent = {
    init: function() {
        var btn = document.getElementById('theme-toggle');
        if (!btn) return;

        function setTheme(theme) {
            if (theme === 'dark') {
                document.body.classList.add('dark-theme');
                btn.textContent = '☀️';
            } else {
                document.body.classList.remove('dark-theme');
                btn.textContent = '🌙';
            }
            localStorage.setItem('theme', theme);
        }

        // Estado inicial
        var savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            setTheme('dark');
        } else {
            setTheme('light');
        }

        btn.onclick = function() {
            var currentTheme = localStorage.getItem('theme') === 'dark' ? 'dark' : 'light';
            setTheme(currentTheme === 'dark' ? 'light' : 'dark');
        };
    }
};
window.headerComponent = headerComponent;