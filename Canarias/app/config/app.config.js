// Configuración de la aplicación Economía Circular Canarias
window.AppConfig = {
    // Ruta base de la aplicación (ajustar según dónde esté instalada)
    // Para localhost/Canarias/ usar 'Canarias'
    // Para localhost:8080/ usar '' (vacío)
    basePath: 'Canarias',
    
    // Configuración del API
    api: {
        // Cambiar a false cuando el backend esté listo
        useMockMode: false, // Activado para probar el backend PHP
        // URLs del API - usando rutas relativas desde la carpeta actual
        baseUrl: 'api', // Rutas relativas desde donde está el index.html
        // Rutas del API relativas a la carpeta actual
        endpoints: {
            // Autenticación
            register: 'auth/register.php',
            login: 'auth/login.php',
            logout: 'auth/logout.php',
            validate: 'auth/validate.php',
            confirmEmail: 'auth/confirm-email.php',
            requestPasswordReset: 'auth/request-password-reset.php',
            resetPassword: 'auth/reset-password.php',
            // Productos
            createProduct: 'products/create.php',
            listProducts: 'products/list.php',
            getProduct: 'products/get.php',
            updateProduct: 'products/update.php',
            deleteProduct: 'products/delete.php',
            getCategories: 'products/categories.php',
            // Pedidos
            createOrder: 'orders/create.php',
            getOrder: 'orders/get.php',
            myOrders: 'orders/my-orders.php',
            updateOrderStatus: 'orders/update-status.php',
            // Pruebas
            testProducts: 'test-products.php',
            testOrders: 'test-orders.php'
        }
    },
    // Configuración de la aplicación
    app: {
        name: 'Economía Circular Canarias',
        version: '1.0.0',
        environment: 'development'
    },
    // Configuración de debugging
    debug: {
        enableLogs: true,
        enableAuthServiceLogs: true
    },
    
    // Helper para construir rutas correctas
    getPath: function(relativePath) {
        // Si relativePath empieza con /, la quitamos
        const cleanPath = relativePath.startsWith('/') ? relativePath.substring(1) : relativePath;
        // Si basePath está vacío, retornamos la ruta con / al inicio
        // Si basePath tiene valor, retornamos /basePath/ruta
        return this.basePath ? `/${this.basePath}/${cleanPath}` : `/${cleanPath}`;
    }
};

