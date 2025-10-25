// This file contains the JavaScript functionality for the footer component.

document.addEventListener("DOMContentLoaded", function() {
    const footer = document.querySelector('footer');
    footer.innerHTML = `
        <div class="footer-content">
            <p>&copy; ${new Date().getFullYear()} Nueva-WEB. Todos los derechos reservados.</p>
            <ul class="footer-links">
                <li><a href="/about.html">Sobre Nosotros</a></li>
                <li><a href="/contact.html">Contacto</a></li>
                <li><a href="/privacy.html">Política de Privacidad</a></li>
            </ul>
        </div>
    `;
});