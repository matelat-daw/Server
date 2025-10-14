<!-- Menú para pantalla de Teléfono -->
<nav class="navbar fixed-top bg-white" id="mobile">
    <div class="col-md-6"> <!-- Columnas con el menú de navegación. -->
        <div class="nav nav-tabs" id="nav-tab" role="tablist" ></div>
            <select class="form-select" id="change" onchange="goThere()">
                <option value="">Selecciona Tu Opcion</option>
                <option value="view1">Inicio</option>
                <option value="view2">Lista de Precios</option>
                <option value="request">Solicitar Cita</option>
                <option value="profile">Perfil de Cliente</option>
                <option value="contact">Contacto</option>
            </select>
        </div>
    </div>
    <div class="col-md-3">
    </div>
    <div class="col-md-3 nav-tabs">
      <h3>Te damos la Bienvenida: </h3><span><?php echo $_SESSION["name"]; ?></span>
      <button onclick="window.open('endsession.php', '_self')" class="btn btn-danger">Cerrar Sesión</button>
    </div>
</nav>