<?php
// filepath: c:\Server\html\Nueva-WEB\index.php
echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Tienda Online</title>
    <link rel="icon" href="data:,">
    
    <!-- Global Styles -->
    <link rel="stylesheet" href="/Nueva-WEB/frontend/styles/global.css">
    <link rel="stylesheet" href="/Nueva-WEB/frontend/styles/responsive.css">
    
    <!-- Component Styles -->
    <link rel="stylesheet" href="/Nueva-WEB/frontend/components/header/header.css">
    <link rel="stylesheet" href="/Nueva-WEB/frontend/components/nav/nav.css">
    <link rel="stylesheet" href="/Nueva-WEB/frontend/components/user-menu/user-menu.css">
    <link rel="stylesheet" href="/Nueva-WEB/frontend/components/footer/footer.css">
    <link rel="stylesheet" href="/Nueva-WEB/frontend/components/product-card/product-card.css">
    
    <!-- Page Styles -->
    <link rel="stylesheet" href="/Nueva-WEB/frontend/pages/home/home.css">
    <link rel="stylesheet" href="/Nueva-WEB/frontend/pages/products/products.css">
    <link rel="stylesheet" href="/Nueva-WEB/frontend/pages/about/about.css">
    <link rel="stylesheet" href="/Nueva-WEB/frontend/pages/contact/contact.css">
    <link rel="stylesheet" href="/Nueva-WEB/frontend/pages/login/login.css">
    <link rel="stylesheet" href="/Nueva-WEB/frontend/pages/register/register.css">
    <link rel="stylesheet" href="/Nueva-WEB/frontend/pages/activate/activate.css">
</head>
<body>
    <div id="app">
        <!-- Header Component -->
        <div id="header-component"></div>

        <!-- Navigation Component -->
        <div id="nav-component"></div>
        <div id="navbar" style="display:none;"></div>

        <!-- Main Content -->
        <main id="main-content">
            <div style="text-align: center; padding: 3rem; color: #718096;">
                <h2>Iniciando aplicación...</h2>
                <div style="margin-top: 1rem;">Por favor espera.</div>
            </div>
        </main>

        <!-- Footer Component -->
        <div id="footer-component"></div>
    </div>

    <!-- Services (Load FIRST) -->
    <script src="/Nueva-WEB/frontend/services/api.js"></script>
    <script src="/Nueva-WEB/frontend/services/auth.js"></script>
    
    <!-- Components Scripts -->
    <script src="/Nueva-WEB/frontend/components/header/header.js"></script>
    <script src="/Nueva-WEB/frontend/components/nav/nav.js"></script>
    <script src="/Nueva-WEB/frontend/components/user-menu/user-menu.js"></script>
    <script src="/Nueva-WEB/frontend/components/product-card/product-card.js"></script>
    <script src="/Nueva-WEB/frontend/components/product-list/product-list.js"></script>
    <script src="/Nueva-WEB/frontend/components/cart/cart.js"></script>
    <script src="/Nueva-WEB/frontend/components/footer/footer.js"></script>
    
    <!-- Pages Scripts (Load BEFORE app.js) -->
    <script src="/Nueva-WEB/frontend/pages/home/home.js"></script>
    <script src="/Nueva-WEB/frontend/pages/products/products.js"></script>
    <script src="/Nueva-WEB/frontend/pages/about/about.js"></script>
    <script src="/Nueva-WEB/frontend/pages/contact/contact.js"></script>
    <script src="/Nueva-WEB/frontend/pages/login/login.js"></script>
    <script src="/Nueva-WEB/frontend/pages/register/register.js"></script>
    <script src="/Nueva-WEB/frontend/pages/activate/activate.js"></script>
    
    <!-- Main App (Load LAST) -->
    <script src="/Nueva-WEB/frontend/app.js"></script>
    
</body>
</html>';
?>