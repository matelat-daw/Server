// About Page
var aboutPage = {
    init: function() {
        console.log('⚡ Página Acerca de cargada');
        this.scrollToTop();
    },
    
    scrollToTop: function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

window.aboutPage = aboutPage;
