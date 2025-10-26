var contactPage = {
    init() {
        this.setupForm();
    },

    setupForm() {
        const form = document.getElementById('contact-form');
        
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                
                const name = document.getElementById('contact-name').value;
                const email = document.getElementById('contact-email').value;
                const subject = document.getElementById('contact-subject').value;
                const message = document.getElementById('contact-message').value;
                
                console.log('Contact form submitted:', { name, email, subject, message });
                alert('Mensaje enviado correctamente. Nos pondremos en contacto contigo pronto.');
                form.reset();
            });
        }
    }
};