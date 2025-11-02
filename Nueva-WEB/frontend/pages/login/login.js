
// Solo lógica de validación y submit del formulario
function initLoginForm() {
    var form = document.getElementById('login-form');
    if (!form) return;
    if (form.dataset.bound === '1') return;

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        var username = document.getElementById('username').value;
        var password = document.getElementById('password').value;
        var result = await AuthService.login({ username, password });
        if (result.success) {
            var event = new CustomEvent('userLoggedIn', { detail: result.user });
            document.dispatchEvent(event);
            if (window.app && typeof window.app.navigate === 'function') {
                window.app.navigate('home');
            } else {
                window.location.hash = '#home';
            }
        } else {
            showModal(result.message || 'Credenciales incorrectas', 'error');
        }
    });
    form.dataset.bound = '1';
}

window.loginPage = {
    init: initLoginForm
};
