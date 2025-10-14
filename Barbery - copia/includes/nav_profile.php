<nav class="navbar fixed-top bg-white" id="pc">
    <div class="col-md-8"> <!-- Columnas con el menú de navegación. -->
      <div class="nav nav-tabs" id="nav-tab" role="tablist" >
          <a class="nav-link" aria-current="page" href="index.php#view1" aria-selected="false" role="tab" aria-controls="nav-contact">Inicio</a></li>
          <a class="nav-link" aria-current="page" href="index.php#view2" aria-selected="false" role="tab" aria-controls="nav-contact">Lista de Precios</a></li>
          <a class="nav-link" aria-current="page" href="request.php" aria-selected="false" role="tab" aria-controls="nav-contact">Solicitar Cita</a></li>
          <a class="nav-link" aria-current="page" href="contact.php" target="_blank" aria-selected="false" role="tab" aria-controls="nav-contact">Contacto</a>
        </div>
        </div>
      <div class="col-md-4 nav-tabs">
        <h3>Te damos la Bienvenida: </h3><span><?php echo $_SESSION["name"]; ?></span>
        <button onclick="window.open('endsession.php', '_self')" class="btn btn-danger">Cerrar Sesión</button>
      </div>
  </nav>
  <!-- NAV/Menú con el carro de la compra cuando no está logueado un espectador. -->