<nav class="navbar fixed-top bg-white" id="pc">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-link" aria-current="page" href="/Barbery/app/auth/index.php#view1" aria-selected="false" role="tab" aria-controls="nav-contact">Inicio</a>
        <a class="nav-link" aria-current="page" href="/Barbery/app/auth/index.php#view2" aria-selected="false" role="tab" aria-controls="nav-contact">Lista de Precios</a>
        <a class="nav-link" aria-current="page" href="/Barbery/app/client/profile.php" aria-selected="false" role="tab" aria-controls="nav-contact">Perfil de Cliente</a>
        <a class="nav-link" aria-current="page" href="/Barbery/app/contact.php" target="_blank" aria-selected="false" role="tab" aria-controls="nav-contact">Contacto</a>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="text-muted">Bienvenido/a: <strong><?php echo $_SESSION["name"]; ?></strong></span>
      <button onclick="window.open('/Barbery/api/auth/logout.php', '_self')" class="btn btn-danger btn-sm">Cerrar Sesión</button>
    </div>
  </div>
</nav>