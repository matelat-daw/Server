// File: /Nueva-WEB/Nueva-WEB/frontend/components/nav/nav.js

document.addEventListener("DOMContentLoaded", function() {
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');

    navToggle.addEventListener('click', function() {
        navMenu.classList.toggle('active');
    });

    const userMenuToggle = document.querySelector('.user-menu-toggle');
    const userMenu = document.querySelector('.user-menu');

    if (userMenuToggle) {
        userMenuToggle.addEventListener('click', function() {
            userMenu.classList.toggle('active');
        });
    }
});