var contactPage = {
    init() {
        this.setupForm();
    },

    setupForm() {
        const form = document.getElementById('contact-form');
        
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                
                const nameEl = document.getElementById('name');
                const emailEl = document.getElementById('email');
                const messageEl = document.getElementById('message');

                const name = nameEl ? nameEl.value : '';
                const email = emailEl ? emailEl.value : '';
                const message = messageEl ? messageEl.value : '';
                
                console.log('Contact form submitted:', { name, email, message });
                alert('Mensaje enviado correctamente. Nos pondremos en contacto contigo pronto.');
                form.reset();
            });
        }
    }
};