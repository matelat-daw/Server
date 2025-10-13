<?php
include "includes/modal.html";
$title = "Gestión de Facturas y Reportes";
include "includes/header.php";
?>

<main class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-11">
            <!-- Header -->
            <div class="text-center mb-4">
                <h1 class="display-6 fw-bold text-primary mb-3">
                    <i class="bi bi-receipt-cutoff me-3"></i>
                    Gestión de Facturas y Reportes
                </h1>
                <p class="lead text-muted">Consulta informes por trimestre, busca facturas específicas y gestiona respaldos</p>
            </div>

            <!-- Navegación por tabs -->
            <div class="card border-0 shadow">
                <div class="card-header bg-white px-0 pt-4 pb-0">
                    <ul class="nav nav-tabs nav-justified" id="reportTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="quarterly-tab" data-bs-toggle="tab" data-bs-target="#quarterly" type="button" role="tab">
                                <i class="bi bi-calendar3 me-2"></i>Informes Trimestrales
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="search-tab" data-bs-toggle="tab" data-bs-target="#search" type="button" role="tab">
                                <i class="bi bi-search me-2"></i>Búsqueda de Facturas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="annual-tab" data-bs-toggle="tab" data-bs-target="#annual" type="button" role="tab">
                                <i class="bi bi-graph-up me-2"></i>Total Anual
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tools-tab" data-bs-toggle="tab" data-bs-target="#tools" type="button" role="tab">
                                <i class="bi bi-tools me-2"></i>Herramientas
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4 p-lg-5">
                    <div class="tab-content" id="reportTabsContent">
                        
                        <!-- Tab: Informes Trimestrales -->
                        <div class="tab-pane fade show active" id="quarterly" role="tabpanel">
                            <div class="row justify-content-center">
                                <div class="col-12 col-lg-8">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body p-4 p-lg-5">
                                            <h3 class="text-center mb-4">
                                                <i class="bi bi-calendar3 text-primary me-2"></i>
                                                Informes por Trimestre
                                            </h3>
                                            <p class="text-center text-muted mb-4">
                                                Genera y descarga informes detallados de las facturas por trimestre
                                            </p>
                                            
                                            <form action="export.php" method="post" target="_blank" class="needs-validation" novalidate>
                                                <div class="mb-4">
                                                    <label for="quarter-select" class="form-label fw-bold h5">
                                                        <i class="bi bi-calendar-range me-2"></i>Trimestre
                                                    </label>
                                                    <select name="date" id="quarter-select" class="form-select form-select-lg" required>
                                                        <option value="1">1º Trimestre (Enero - Marzo)</option>
                                                        <option value="2">2º Trimestre (Abril - Junio)</option>
                                                        <option value="3">3º Trimestre (Julio - Septiembre)</option>
                                                        <option value="4">4º Trimestre (Octubre - Diciembre)</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="mb-4">
                                                    <label for="year-input" class="form-label fw-bold h5">
                                                        <i class="bi bi-calendar-event me-2"></i>Año
                                                    </label>
                                                    <input type="number" id="year-input" name="year" class="form-control form-control-lg" 
                                                           min="2022" max="3000" step="1" required>
                                                </div>
                                                
                                                <div class="d-grid gap-2">
                                                    <button type="submit" class="btn btn-primary btn-lg">
                                                        <i class="bi bi-download me-2"></i>
                                                        Generar y Descargar Informe
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab: Búsqueda de Facturas -->
                        <div class="tab-pane fade" id="search" role="tabpanel">
                            <div class="row justify-content-center">
                                <div class="col-12 col-lg-10">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body p-4 p-lg-5">
                                            <h3 class="text-center mb-4">
                                                <i class="bi bi-search text-primary me-2"></i>
                                                Búsqueda de Facturas
                                            </h3>
                                            <p class="text-center text-muted mb-4">
                                                Busca facturas por fecha, mesa o combinando ambos criterios
                                            </p>
                                            
                                            <form action="showtable.php" method="post" onsubmit="return verifyShow()" class="needs-validation" novalidate>
                                                <div class="row">
                                                    <div class="col-md-6 mb-4">
                                                        <label for="search-date" class="form-label fw-bold h5">
                                                            <i class="bi bi-calendar-date me-2"></i>Fecha
                                                        </label>
                                                        <input type="date" id="search-date" name="date" class="form-control form-control-lg">
                                                        <div class="form-text">Opcional: deja vacío para buscar por mesa solamente</div>
                                                    </div>
                                                    
                                                    <div class="col-md-6 mb-4">
                                                        <label for="table-select" class="form-label fw-bold h5">
                                                            <i class="bi bi-table me-2"></i>Mesa
                                                        </label>
                                                        <select id="table-select" name="table" class="form-select form-select-lg">
                                                            <option value="">Todas las mesas</option>
                                                            <optgroup label="Zona de Entrada">
                                                                <option value="Entrada 1">Entrada 1</option>
                                                                <option value="Entrada 2">Entrada 2</option>
                                                                <option value="Entrada 3">Entrada 3</option>
                                                                <option value="Entrada 4">Entrada 4</option>
                                                            </optgroup>
                                                            <optgroup label="Zona de Barra">
                                                                <option value="Barra 1">Barra 1</option>
                                                                <option value="Barra 2">Barra 2</option>
                                                                <option value="Barra 3">Barra 3</option>
                                                            </optgroup>
                                                            <optgroup label="Zona de Patio">
                                                                <option value="Patio 1">Patio 1</option>
                                                                <option value="Patio 2">Patio 2</option>
                                                                <option value="Patio 3">Patio 3</option>
                                                                <option value="Patio 4">Patio 4</option>
                                                                <option value="Patio 5">Patio 5</option>
                                                            </optgroup>
                                                            <optgroup label="Zona de Vereda">
                                                                <option value="Vereda 1">Vereda 1</option>
                                                                <option value="Vereda 2">Vereda 2</option>
                                                                <option value="Vereda 3">Vereda 3</option>
                                                            </optgroup>
                                                            <optgroup label="Mesas Principales">
                                                                <option value="Mesa 1">Mesa 1</option>
                                                                <option value="Mesa 2">Mesa 2</option>
                                                                <option value="Mesa 3">Mesa 3</option>
                                                                <option value="Mesa 4">Mesa 4</option>
                                                                <option value="Mesa 5">Mesa 5</option>
                                                                <option value="Mesa 6">Mesa 6</option>
                                                                <option value="Mesa 7">Mesa 7</option>
                                                                <option value="Mesa 8">Mesa 8</option>
                                                                <option value="Mesa 9">Mesa 9</option>
                                                                <option value="Mesa 10">Mesa 10</option>
                                                                <option value="Mesa 11">Mesa 11</option>
                                                                <option value="Mesa 12">Mesa 12</option>
                                                                <option value="Mesa 13">Mesa 13</option>
                                                            </optgroup>
                                                            <optgroup label="Tablones">
                                                                <option value="Tablón 1">Tablón 1</option>
                                                                <option value="Tablón 2">Tablón 2</option>
                                                            </optgroup>
                                                        </select>
                                                        <div class="form-text">Opcional: deja vacío para buscar por fecha solamente</div>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-grid gap-2">
                                                    <button type="submit" class="btn btn-success btn-lg">
                                                        <i class="bi bi-search me-2"></i>
                                                        Buscar Facturas
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab: Total Anual -->
                        <div class="tab-pane fade" id="annual" role="tabpanel">
                            <div class="row justify-content-center">
                                <div class="col-12 col-lg-6">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body p-4 p-lg-5 text-center">
                                            <h3 class="mb-4">
                                                <i class="bi bi-graph-up text-success me-2"></i>
                                                Total de Ventas Anual
                                            </h3>
                                            <p class="text-muted mb-4">
                                                Consulta el resumen completo de todas las ventas del año actual
                                            </p>
                                            
                                            <div class="d-grid gap-3">
                                                <button onclick="window.open('showtotal.php', '_blank')" class="btn btn-success btn-lg">
                                                    <i class="bi bi-bar-chart-line me-2"></i>
                                                    Ver Total de Ventas del Año
                                                </button>
                                            </div>
                                            
                                            <div class="mt-4 p-3 bg-white rounded">
                                                <small class="text-muted">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    Se abrirá en una nueva ventana con el reporte completo
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab: Herramientas -->
                        <div class="tab-pane fade" id="tools" role="tabpanel">
                            <div class="row justify-content-center">
                                <div class="col-12 col-lg-8">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body p-4 p-lg-5">
                                            <h3 class="text-center mb-4">
                                                <i class="bi bi-tools text-warning me-2"></i>
                                                Herramientas del Sistema
                                            </h3>
                                            <p class="text-center text-muted mb-4">
                                                Acceso rápido a herramientas de administración y consulta
                                            </p>
                                            
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="card h-100 border-success">
                                                        <div class="card-body text-center p-4">
                                                            <i class="bi bi-receipt display-6 text-success mb-3"></i>
                                                            <h5 class="card-title">Última Factura</h5>
                                                            <p class="card-text text-muted">
                                                                Consulta los detalles de la factura más reciente
                                                            </p>
                                                            <button onclick="window.open('invoice.php', '_blank')" class="btn btn-success btn-lg w-100">
                                                                <i class="bi bi-eye me-2"></i>
                                                                Ver Última Factura
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div class="card h-100 border-secondary">
                                                        <div class="card-body text-center p-4">
                                                            <i class="bi bi-database display-6 text-secondary mb-3"></i>
                                                            <h5 class="card-title">Respaldo de Base de Datos</h5>
                                                            <p class="card-text text-muted">
                                                                Genera una copia de seguridad completa
                                                            </p>
                                                            <button onclick="window.open('db-backup.php', '_blank')" class="btn btn-secondary btn-lg w-100">
                                                                <i class="bi bi-download me-2"></i>
                                                                Crear Respaldo
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<style>
.nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 500;
    padding: 1rem 1.5rem;
}

.nav-tabs .nav-link.active {
    background-color: #fff;
    color: #0d6efd;
    border-bottom: 3px solid #0d6efd;
}

.nav-tabs .nav-link:hover {
    color: #0d6efd;
    border-bottom: 3px solid transparent;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.display-6 {
    font-size: 2.5rem;
}

@media (max-width: 768px) {
    .nav-tabs .nav-link {
        padding: 0.75rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .display-6 {
        font-size: 2rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Establecer el año actual en el input de año
    const yearInput = document.getElementById('year-input');
    if (yearInput) {
        const currentYear = new Date().getFullYear();
        yearInput.value = currentYear;
    }
    
    // Establecer el trimestre actual
    const quarterSelect = document.getElementById('quarter-select');
    if (quarterSelect) {
        const currentMonth = new Date().getMonth() + 1; // getMonth() devuelve 0-11
        let currentQuarter;
        
        if (currentMonth >= 1 && currentMonth <= 3) {
            currentQuarter = 1;
        } else if (currentMonth >= 4 && currentMonth <= 6) {
            currentQuarter = 2;
        } else if (currentMonth >= 7 && currentMonth <= 9) {
            currentQuarter = 3;
        } else {
            currentQuarter = 4;
        }
        
        quarterSelect.value = currentQuarter;
    }
    
    // Bootstrap form validation
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
    
    // Validación personalizada para búsqueda de facturas
    const searchForm = document.querySelector('form[action="showtable.php"]');
    if (searchForm) {
        searchForm.addEventListener('submit', function(event) {
            const date = document.getElementById('search-date').value;
            const table = document.getElementById('table-select').value;
            
            if (!date && !table) {
                event.preventDefault();
                showToast('error', 'Criterio de búsqueda requerido', 'Debes seleccionar al menos una fecha o una mesa para realizar la búsqueda.');
                return false;
            }
            
            return verifyShow();
        });
    }
    
    // Función para mostrar toasts
    function showToast(type, title, message) {
        const toastContainer = document.getElementById('toast-container') || createToastContainer();
        
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : 'success'} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <strong>${title}</strong><br>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }
    
    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
        return container;
    }
    
    // Hacer las funciones globales para mantener compatibilidad
    window.showToast = showToast;
});

// Función verifyShow para mantener compatibilidad
function verifyShow() {
    const date = document.getElementById('search-date').value;
    const table = document.getElementById('table-select').value;
    
    if (!date && !table) {
        return false;
    }
    
    return true;
}
</script>

<?php
include "includes/footer.html";
?>