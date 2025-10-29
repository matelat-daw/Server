var headerComponent = {
    init: function() {
        var btn = document.getElementById('theme-toggle');
        var icon = document.getElementById('theme-icon');
        if (!btn) return;

        // Estado inicial
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-theme');
            if (icon) icon.textContent = '☀️';
        } else {
            document.body.classList.remove('dark-theme');
            if (icon) icon.textContent = '🌙';
        }

        btn.onclick = function() {
            document.body.classList.toggle('dark-theme');
            var isDark = document.body.classList.contains('dark-theme');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            if (icon) icon.textContent = isDark ? '☀️' : '🌙';
        };
    }
};
window.headerComponent = headerComponent;