// Google Translate Service
(function() {
    'use strict';
    
    /**
     * Limpiar cookies de traducción
     */
    function clearTranslationCookies() {
        document.cookie = 'googtrans=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT';
        document.cookie = 'googtrans=; path=/; domain=' + window.location.hostname + '; expires=Thu, 01 Jan 1970 00:00:00 GMT';
    }
    
    /**
     * Obtener el idioma actual
     * @returns {string} 'es' o 'en'
     */
    function getCurrentLanguage() {
        const hash = window.location.hash;
        const cookie = document.cookie;
        
        if (hash.includes('/en') || cookie.includes('googtrans=/es/en')) {
            return 'en';
        }
        return 'es';
    }
    
    /**
     * Inicializar widget de Google Translate
     */
    window.googleTranslateElementInit = function() {
        new google.translate.TranslateElement({
            pageLanguage: 'es',
            includedLanguages: 'es,en',
            layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
            autoDisplay: false
        }, 'google_translate_element');
        
        // Actualizar UI después de inicializar
        setTimeout(updateLanguageButton, 500);
    };
    
    /**
     * Cambiar idioma
     */
    window.changeLanguage = function() {
        const currentLang = getCurrentLanguage();
        const targetLang = currentLang === 'es' ? 'en' : 'es';
        
        // Guardar la página actual antes de cambiar idioma
        const currentPage = window.location.hash || '#home';
        sessionStorage.setItem('lastPage', currentPage);
        
        if (targetLang === 'es') {
            // Volver al español
            clearTranslationCookies();
            // NO borrar el hash - mantener la página actual
            window.location.reload();
        } else {
            // Cambiar a inglés
            const select = document.querySelector('.goog-te-combo');
            if (select) {
                select.value = 'en';
                select.dispatchEvent(new Event('change'));
                setTimeout(updateLanguageButton, 500);
            } else {
                document.cookie = 'googtrans=/es/en; path=/';
                document.cookie = 'googtrans=/es/en; path=/; domain=' + window.location.hostname;
                window.location.reload();
            }
        }
    };
    
    /**
     * Actualizar el botón de idioma
     */
    function updateLanguageButton() {
        const currentLang = getCurrentLanguage();
        const button = document.querySelector('.language-btn');
        
        if (!button) return;
        
        if (currentLang === 'es') {
            // App en español, mostrar SOLO botón para cambiar a inglés
            button.innerHTML = '<span class="flag">EN</span><span class="lang-text">English</span>';
            button.setAttribute('title', 'Switch to English');
            button.setAttribute('data-target-lang', 'en');
        } else {
            // App en inglés, mostrar SOLO botón para cambiar a español
            button.innerHTML = '<span class="flag">ES</span><span class="lang-text">Español</span>';
            button.setAttribute('title', 'Cambiar a Español');
            button.setAttribute('data-target-lang', 'es');
        }
    }
    
    /**
     * Ocultar elementos de Google Translate
     */
    function hideGoogleTranslateElements() {
        const elementsToHide = [
            '.goog-te-banner-frame',
            '.skiptranslate iframe',
            'body > .skiptranslate',
            '#goog-gt-tt',
            '.goog-te-balloon-frame'
        ];
        
        elementsToHide.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            elements.forEach(el => {
                if (el && el.style) {
                    el.style.display = 'none';
                    el.style.visibility = 'hidden';
                    el.style.opacity = '0';
                }
            });
        });
    }
    
    /**
     * Inicialización
     */
    function init() {
        // Restaurar la página anterior si existe
        const lastPage = sessionStorage.getItem('lastPage');
        if (lastPage && lastPage !== window.location.hash) {
            window.location.hash = lastPage;
            sessionStorage.removeItem('lastPage');
        }
        
        // Actualizar botón de idioma
        updateLanguageButton();
        
        // Ocultar elementos de Google Translate
        hideGoogleTranslateElements();
    }
    
    // Inicializar al cargar
    window.addEventListener('load', init);
    
    // Actualizar periódicamente por si Google Translate cambia el idioma
    setInterval(updateLanguageButton, 1000);
    
    // Ejecutar ocultación repetidamente
    setInterval(hideGoogleTranslateElements, 100);
    
    // Observador de mutaciones para ocultar elementos dinámicos
    const observer = new MutationObserver(() => {
        hideGoogleTranslateElements();
    });
    
    // Iniciar observador cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', () => {
        if (document.body) {
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
            hideGoogleTranslateElements();
        }
    });
    
    // Ejecutar también inmediatamente
    if (document.body) {
        hideGoogleTranslateElements();
    }
})();
