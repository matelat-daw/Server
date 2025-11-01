var contactPage = {
    init() {
        this.setupForm();
    },

    setupForm() {
        const form = document.getElementById('contact-form');
        const messageDiv = document.getElementById('form-message');
        
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const nameEl = document.getElementById('name');
                const emailEl = document.getElementById('email');
                const messageEl = document.getElementById('message');
                const submitBtn = form.querySelector('.submit-btn');

                const name = nameEl ? nameEl.value : '';
                const email = emailEl ? emailEl.value : '';
                const message = messageEl ? messageEl.value : '';
                
                // Deshabilitar botón durante el envío
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span>Enviando...</span> <span class="btn-icon">⏳</span>';
                
                try {
                    // Aquí puedes agregar la llamada a la API cuando esté lista
                    // const response = await ApiService.post('/contact', { name, email, message });
                    
                    // Simulación de envío exitoso
                    await new Promise(resolve => setTimeout(resolve, 1000));
                    
                    console.log('Contact form submitted:', { name, email, message });
                    
                    // Mostrar mensaje de éxito
                    messageDiv.className = 'form-message success';
                    messageDiv.textContent = '✓ Mensaje enviado correctamente. Nos pondremos en contacto contigo pronto.';
                    messageDiv.style.display = 'block';
                    
                    // Limpiar formulario
                    form.reset();
                    
                    // Ocultar mensaje después de 5 segundos
                    setTimeout(() => {
                        messageDiv.style.display = 'none';
                    }, 5000);
                    
                } catch (error) {
                    console.error('Error al enviar formulario:', error);
                    
                    // Mostrar mensaje de error
                    messageDiv.className = 'form-message error';
                    messageDiv.textContent = '✗ Error al enviar el mensaje. Por favor, inténtalo de nuevo.';
                    messageDiv.style.display = 'block';
                } finally {
                    // Rehabilitar botón
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<span>Enviar Mensaje</span> <span class="btn-icon">📨</span>';
                }
            });
        }
    }
};