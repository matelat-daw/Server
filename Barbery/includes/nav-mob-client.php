<!-- Menú para pantalla de Teléfono -->
<nav class="navbar fixed-top bg-white" id="mobile">
  <div class="container-fluid">
    <div class="row w-100">
      <div class="col-8">
        <select class="form-select" id="change" onchange="goThere()">
            <option value="">Selecciona Tu Opción</option>
            <option value="view1">Inicio</option>
            <option value="view2">Lista de Precios</option>
            <option value="request">Solicitar Cita</option>
            <option value="profile">Perfil de Cliente</option>
            <option value="contact">Contacto</option>
        </select>
      </div>
      <div class="col-4 text-end">
        <button onclick="window.open('/Barbery/api/auth/logout.php', '_self')" class="btn btn-danger btn-sm">Cerrar Sesión</button>
      </div>
    </div>
  </div>
</nav>