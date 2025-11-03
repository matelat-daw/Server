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
            console.error('Error al activar cuenta:', error);
            this.showError('Error de conexión al activar la cuenta');
        }
    },
    
    showSuccess() {
        const content = document.getElementById('activation-content');
        content.innerHTML = `
            <div class="text-center">
                <div class="text-success mb-3" style="font-size: 4rem;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 class="mb-3">¡Cuenta Activada!</h2>
                <p class="text-muted mb-4">Tu cuenta ha sido activada exitosamente. Ya puedes iniciar sesión y disfrutar de nuestros productos.</p>
                <a href="#login" class="btn btn-primary btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i>Ir a Iniciar Sesión
                </a>
            </div>
        `;
    },
    
    showError(message) {
        const content = document.getElementById('activation-content');
        content.innerHTML = `
            <div class="text-center">
                <div class="text-danger mb-3" style="font-size: 4rem;">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h2 class="mb-3">Error de Activación</h2>
                <p class="text-muted mb-4">${message}</p>
                <a href="#home" class="btn btn-secondary btn-lg">
                    <i class="fas fa-home me-2"></i>Volver al Inicio
                </a>
            </div>
        `;
    }
};

// Exponer como activatePage para compatibilidad con app.js
window.activatePage = activatePage;
