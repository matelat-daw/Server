// frontend/app.js
document.addEventListener("DOMContentLoaded", function() {
    // Initialize the application
    initApp();
});

function initApp() {
    // Load the initial page
    loadPage('home');
    
    // Set up event listeners for navigation
    setupNavigation();
}

function loadPage(page) {
    const content = document.getElementById('content');
    fetch(`frontend/pages/${page}/${page}.html`)
        .then(response => response.text())
        .then(html => {
            content.innerHTML = html;
            loadPageScripts(page);
        })
        .catch(error => console.error('Error loading page:', error));
}

function loadPageScripts(page) {
    const script = document.createElement('script');
    script.src = `frontend/pages/${page}/${page}.js`;
    document.body.appendChild(script);
}

function setupNavigation() {
    const navLinks = document.querySelectorAll('nav a');
    navLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            const page = this.getAttribute('href').substring(1); // Remove the leading '#'
            loadPage(page);
        });
    });
}