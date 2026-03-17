# Instalación y Configuración de Exportación de Reportes

## Descripción General

Se ha implementado funcionalidad de exportación de contratos a Excel y PDF en el panel de administración de Energy App. Solo los usuarios con rol de **administrador** pueden acceder a estos reportes.

## Librerías Agregadas

### 1. PHPOffice/PhpSpreadsheet
- **Versión:** ^1.29
- **Propósito:** Generar archivos Excel (.xlsx) con formato profesional
- **Descripción:** Permite crear hojas de cálculo con estilos, colores, bordes y más

### 2. mPDF
- **Versión:** ^8.2
- **Propósito:** Generar archivos PDF desde HTML
- **Descripción:** Realiza conversión de HTML a PDF de forma confiable y rápida

## Pasos de Instalación

### 1. Instalar Dependencias (Composer)

Ejecuta el siguiente comando en la carpeta `/api`:

```bash
cd c:\Server\html\Project-Energy\api
composer install
```

O si necesitas actualizar:

```bash
composer update
```

**Nota:** Las dependencias ya están declaradas en `composer.json`. Si tienes problemas, asegúrate de:
- Tener Composer instalado en tu sistema
- Tener PHP 7.4+ instalado
- Tener conexión a internet para descargar los paquetes

### 2. Verificar Instalación

Verifica que los paquetes se instalaron correctamente:

```bash
ls api/vendor
```

Deberías ver carpetas para:
- `phpoffice/` (PhpSpreadsheet)
- `mpdf/` (mPDF)
- Otras dependencias

## Archivos Modificados

### Backend (API)

1. **`api/composer.json`**
   - Agregadas dependencias: `phpoffice/phpspreadsheet` y `mpdf/mpdf`

2. **`api/controllers/ContractController.php`**
   - Nuevo método: `exportToExcel()` - Genera archivo Excel con todos los contratos
   - Nuevo método: `exportToPdf()` - Genera archivo PDF con todos los contratos
   - Ambos métodos verifican que el usuario sea administrador

3. **`api/routes/api.php`**
   - Nueva ruta: `GET /admin/contracts/export/excel`
   - Nueva ruta: `GET /admin/contracts/export/pdf`

### Frontend

1. **`frontend/pages/profile/profile.html`**
   - Agregados botones de descarga en la sección "Administración"
   - Botón "Descargar Excel" (📊)
   - Botón "Descargar PDF" (📄)

2. **`frontend/pages/profile/profile.js`**
   - Nuevo método: `loadAdminContracts()` - Carga contratos desde la API
   - Nuevo método: `exportToExcel()` - Dispara descarga de Excel
   - Nuevo método: `exportToPdf()` - Dispara descarga de PDF
   - Nuevos métodos: `renderSellerContractsTable()` y `renderDirectContractsTable()` - Renderizan tablas

3. **`frontend/services/api.js`**
   - Nuevo método: `downloadFile()` - Maneja descargas de archivos binarios
   - Modificado método: `get()` - Soporta parámetro `isDownload` para descargas

## Cómo Usar

### Para Administradores

1. Inicia sesión con una cuenta de administrador
2. Ve a tu perfil (botón de usuario en la esquina superior derecha)
3. Selecciona la pestaña "📊 Administración"
4. Se cargarán automáticamente dos tablas:
   - **Contratos gestionados por vendedores**
   - **Contratos realizados directamente por usuarios**
5. Haz clic en uno de los botones de descarga:
   - **"📊 Descargar Excel"** - Descarga todos los contratos en formato Excel
   - **"📄 Descargar PDF"** - Descarga todos los contratos en formato PDF

### Contenido de los Reportes

#### Excel (.xlsx)
- Dos hojas con datos organizados:
  1. Contratos por vendedor (con detalles de vendedor)
  2. Contratos directos (sin vendedor)
- Información incluida por contrato:
  - ID del contrato
  - Nombre y email del cliente
  - Nombre y email del vendedor (si aplica)
  - Plan y proveedor
  - Fechas de inicio y fin
  - Estado del contrato
  - Monto total y comisión
  - Fecha de creación
- Estilos profesionales:
  - Encabezados con fondo azul
  - Bordes en todas las celdas
  - Ancho automático de columnas

#### PDF
- Reporte con orientación horizontal (landscape)
- Tabla clara y legible
- Información de fecha y hora de generación
- Dos secciones:
  1. Contratos por vendedor
  2. Contratos directos
- Formato profesional con colores y bordes

## Seguridad

### Control de Acceso
- Solo usuarios con rol `admin` pueden:
  - Ver la pestaña de administración
  - Acceder a los endpoints de exportación
  - Descargar los reportes

### Validación
- Cada solicitud verifica:
  - Presencia del token JWT
  - Validez del token
  - Rol de administrador

## Estructura del Código

### Flujo de Exportación a Excel

```
Frontend (Botón clic)
  ↓
exportToExcel() en profile.js
  ↓
downloadFile('/admin/contracts/export/excel')
  ↓
API GET /admin/contracts/export/excel
  ↓
ContractController::exportToExcel()
  ↓
  - Valida token y rol
  - Obtiene contratos de BD
  - Crea archivo Excel con PhpOffice
  - Envía como descarga
```

### Flujo similar para PDF

```
Frontend (Botón clic)
  ↓
exportToPdf() en profile.js
  ↓
downloadFile('/admin/contracts/export/pdf')
  ↓
API GET /admin/contracts/export/pdf
  ↓
ContractController::exportToPdf()
  ↓
  - Valida token y rol
  - Obtiene contratos de BD
  - Crea HTML formateado
  - Convierte a PDF con mPDF
  - Envía como descarga
```

## Solución de Problemas

### Error: "No se encontró PHPOffice"
**Solución:** Ejecuta `composer install` en la carpeta `/api`

### Error: "No se encontró mPDF"
**Solución:** Ejecuta `composer install` en la carpeta `/api`

### Los botones no aparecen
**Solución:** Asegúrate de haber iniciado sesión como administrador

### La descarga no inicia
**Solución:** 
- Verifica la consola del navegador (F12 → Console)
- Comprueba que el token es válido
- Asegúrate de que hay contratos en la base de datos

### El archivo Excel/PDF está vacío
**Solución:**
- Verifica que hay contratos en la base de datos
- Comprueba en las tablas que se carguen correctamente

## Personalización

### Modificar estilos de Excel

En `api/controllers/ContractController.php`, método `exportToExcel()`:

```php
// Cambiar color de encabezado
'startColor' => ['rgb' => '366092'], // Cambia este hexadecimal

// Cambiar color alternado de filas
'startColor' => new \PhpOffice\PhpSpreadsheet\Style\Color('E7E6E6')
```

### Modificar estilos de PDF

En `api/controllers/ContractController.php`, método `exportToPdf()`:

```php
// Cambiar colores HTML/CSS en la sección $stylesheet
'background-color: #366092;' // Color títulos
'background-color: #f9f9f9;' // Color filas alternadas
```

### Agregar más columnas

1. En el método `exportToExcel()` o `exportToPdf()`
2. Agregar el campo en el arreglo de encabezados
3. Agregar el valor en el bucle que completa las filas

## Notas Importantes

- Los reportes incluyen TODOS los contratos, sin filtros
- Los archivos se generan en tiempo real al descargar
- Los nombres de archivo incluyen la fecha actual
- La descarga requiere que el navegador permita descargas
- Se recomienda ejecutar estas operaciones durante horas de bajo tráfico si hay muchos contratos

## Próximas Mejoras (Opcionales)

- Agregar filtros por fecha o estado
- Permitir descargar solo vendedores o clientes específicos
- Agregar gráficos en los reportes
- Permitir que administradores personalicen el formato de los reportes
- Agregar opción de enviar reportes por email

## Soporte

Para problemas o preguntas adicionales:
1. Verifica el archivo de log del servidor
2. Revisa la consola del navegador (F12)
3. Consulta la documentación de PHPOffice: https://phpspreadsheet.readthedocs.io/
4. Consulta la documentación de mPDF: https://mpdf.github.io/
