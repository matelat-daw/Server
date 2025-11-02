
var activatePage = {
    async init() {


        // Obtener token de la URL
        const hashParts = window.location.hash.split('?');

        const urlParams = new URLSearchParams(hashParts[1] || '');
        const token = urlParams.get('token');

        if (!token) {

            this.showError('No se proporcionó un token de activación válido');
            return;
        }
        
        // Activar cuenta

        await this.activateAccount(token);
    },
    
    async activateAccount(token) {
        try {


            const response = await ApiService.post('/activate', { token: token });

            if (response && response.success) {

                this.showSuccess();
            } else {

                this.showError(response.message || 'Error al activar la cuenta');
            }
        } catch (error) {

            this.showError('Error de conexión al activar la cuenta');
        }
    },
    
    showSuccess() {
        const content = document.getElementById('activation-content');
        content.innerHTML = `
            <div class="activation-success">
                <div class="success-icon">✅</div>
                <h2>¡Cuenta Activada!</h2>
                <p>Tu cuenta ha sido activada exitosamente. Ya puedes iniciar sesión y disfrutar de nuestros productos.</p>
                <a href="#login" data-route="login" class="success-button">Ir a Iniciar Sesión</a>
            </div>
        `;
    },
    
    showError(message) {
        const content = document.getElementById('activation-content');
        content.innerHTML = `
            <div class="activation-error">
                <div class="error-icon">❌</div>
                <h2>Error de Activación</h2>
                <p>${message}</p>
                <a href="#home" data-route="home" class="error-button">Volver al Inicio</a>
            </div>
        `;
    }
};

// Exponer como activatePage para compatibilidad con app.js
window.activatePage = activatePage;


