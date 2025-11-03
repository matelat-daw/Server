# TechStore - E-commerce con Bootstrap

E-commerce moderno desarrollado con **Bootstrap 5** y tonos azules comerciales.

## 🎨 Características de Diseño

- **Framework**: Bootstrap 5
- **Colores**: Tonos azules profesionales (#0d6efd, #0a58ca, #0dcaf0)
- **Iconos**: Font Awesome 6
- **Responsive**: Completamente adaptable a móviles, tablets y desktop
- **Componentes**: Modales, toasts, cards, navbar, forms

## 📁 Estructura del Proyecto

```
Nueva-BS/
├── frontend/
│   ├── js/
│   │   └── bootstrap.min.js
│   ├── style/
│   │   ├── bootstrap.min.css
│   │   └── custom.css
│   ├── utils/
│   │   └── modal.js
│   ├── services/
│   │   ├── api.js
│   │   ├── auth.js
│   │   └── cart.js
│   ├── components/
│   │   ├── header/header.js
│   │   ├── nav/nav.js
│   │   ├── footer/footer.js
│   │   ├── cart/cart.js
│   │   ├── product-card/product-card.js
│   │   └── user-menu/user-menu.js
│   ├── pages/
│   │   ├── home/home.js
│   │   ├── products/products.js
│   │   ├── about/about.js
│   │   ├── contact/contact.js
│   │   ├── login/login.js
│   │   └── register/register.js
│   ├── imgs/
│   │   └── producto-generico.svg
│   └── app.js
└── index.php
```

## 🚀 Funcionalidades

### Implementadas ✅

1. **Sistema de Navegación SPA** (Single Page Application)
   - Router con hash navigation
   - Carga dinámica de páginas sin recargar

2. **Carrito de Compras**
   - Añadir/Eliminar productos
   - Actualizar cantidades
   - Persistencia en localStorage
   - Badge con contador
   - Modal de Bootstrap

3. **Páginas**
   - 🏠 Home: Hero section + productos destacados + features
   - 🛍️ Products: Catálogo con filtros y búsqueda
   - ℹ️ About: Información de la empresa
   - 📧 Contact: Formulario de contacto
   - 🔐 Login: Inicio de sesión
   - 📝 Register: Registro de usuarios

4. **Componentes Reutilizables**
   - Header con navbar responsive
   - Footer con links e información
   - Product Cards con diseño uniforme
   - Modales de confirmación
   - Toasts para notificaciones

5. **Sistema de Notificaciones**
   - Toasts de Bootstrap
   - Modales de éxito/error/warning/info
   - Confirmaciones antes de acciones destructivas

### Conectar con API Backend 🔌

La aplicación está configurada para conectarse a:
```javascript
baseURL: '/Nueva-WEB/api'
```

Endpoints esperados:
- `GET /products` - Lista de productos
- `GET /products/featured` - Productos destacados
- `POST /auth/login` - Iniciar sesión
- `POST /auth/register` - Registrar usuario
- `POST /contact` - Enviar mensaje de contacto

## 🎨 Personalización de Colores

Los colores principales se definen en `custom.css`:

```css
:root {
    --primary-blue: #0d6efd;
    --dark-blue: #0a58ca;
    --light-blue: #cfe2ff;
    --accent-blue: #0dcaf0;
    --gradient-blue: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
    --gradient-dark: linear-gradient(135deg, #0a58ca 0%, #0d6efd 100%);
}
```

## 📱 Responsive Design

- **Desktop**: Grid de 4 columnas para productos
- **Tablet**: Grid de 3 columnas
- **Mobile**: Grid de 1-2 columnas
- Navbar colapsable en móviles
- Modales adaptables

## 🔧 Uso del Carrito

```javascript
// Añadir producto
window.cartService.addItem(product);

// Obtener items
const items = window.cartService.getItems();

// Obtener total
const total = window.cartService.getTotal();

// Limpiar carrito
window.cartService.clear();
```

## 🎯 Próximas Mejoras

- [ ] Página de checkout
- [ ] Perfil de usuario
- [ ] Historial de pedidos
- [ ] Sistema de valoraciones
- [ ] Comparador de productos
- [ ] Lista de deseos

## 🚀 Cómo Usar

1. Accede a: `http://localhost/Nueva-BS/`
2. Navega por las diferentes secciones
3. Añade productos al carrito
4. (Opcional) Regístrate e inicia sesión

## 📝 Notas

- Los productos mostrados son de ejemplo (mock data)
- Para producción, conectar con API backend real
- Las imágenes de productos deben colocarse en `frontend/imgs/`
- El carrito persiste en localStorage del navegador

---

**Desarrollado con ❤️ usando Bootstrap 5**
