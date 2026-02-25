// Privacy Page
var privacyPage = {
    init: function() {
        console.log('🔒 Página de privacidad cargada');
        this.scrollToTop();
    },
    
    scrollToTop: function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

window.privacyPage = privacyPage;
