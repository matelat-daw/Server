<?php
echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Tienda Online</title>
    
    <!-- Global Styles -->
    <link rel="stylesheet" href="frontend/styles/global.css">
    <link rel="stylesheet" href="frontend/styles/themes/light.css" id="theme-stylesheet">
    <link rel="stylesheet" href="frontend/styles/responsive.css">
    
    <!-- Component Styles -->
    <link rel="stylesheet" href="frontend/components/header/header.css">
    <link rel="stylesheet" href="frontend/components/nav/nav.css">
    <link rel="stylesheet" href="frontend/components/user-menu/user-menu.css">
    <link rel="stylesheet" href="frontend/components/footer/footer.css">
</head>
<body>
    <div id="app">
        <!-- Header Component -->
        <div id="header-component"></div>
        
        <!-- Navigation Component -->
        <div id="nav-component"></div>
        
        <!-- Main Content -->
        <main id="main-content">
            <!-- Dynamic content will be loaded here -->
        </main>
        
        <!-- Footer Component -->
        <div id="footer-component"></div>
    </div>

    <!-- Services -->
    <script src="frontend/services/api.js"></script>
    <script src="frontend/services/auth.js"></script>
    
    <!-- Components Scripts -->
    <script src="frontend/components/header/header.js"></script>
    <script src="frontend/components/nav/nav.js"></script>
    <script src="frontend/components/user-menu/user-menu.js"></script>
    <script src="frontend/components/product-card/product-card.js"></script>
    <script src="frontend/components/product-list/product-list.js"></script>
    <script src="frontend/components/login/login.js"></script>
    <script src="frontend/components/register/register.js"></script>
    <script src="frontend/components/cart/cart.js"></script>
    <script src="frontend/components/footer/footer.js"></script>
    
    <!-- Pages Scripts -->
    <script src="frontend/pages/home/home.js"></script>
    <script src="frontend/pages/products/products.js"></script>
    <script src="frontend/pages/about/about.js"></script>
    <script src="frontend/pages/contact/contact.js"></script>
    
    <!-- Main App -->
    <script src="frontend/app.js"></script>
</body>
</html>';
?>