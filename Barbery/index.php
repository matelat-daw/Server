<?php
include "includes/conn.php";
$title = "La Peluquería de Javier Borneo - Página Principal";
include "includes/header.php";
include "includes/modal.html";
include "includes/modal-img.html";
if (isset($_SESSION["client"]))
{
    include "includes/nav_client.php";
    include "includes/nav-mob-client.php";
}
else
{
    include "includes/nav_index.html";
    include "includes/nav-mob-index.html";
}
?>
<section class="container-fluid pt-3">
    <div class="row">
        <div class="col-md-1"></div>
            <div class="col-md-10">
                <div id="view1">
                    <br><br><br><br>
					<header>      
                    <!-- Jumbotron -->
                    <!-- Background image -->
                    <div class="mask" style="background-color: rgba(0, 0, 0, 0.6);">
                        <div class="d-flex justify-content-center align-items-center h-100"></div>
                            <div class="text-white">
                                    <h1 class="mb-3">La Peluquería de Javier Borneo</h1>
                            </div>
                    </div>
                    <div id="slide" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="img/corte.jpg" class="d-block w-100" alt="Corte" width="960px" height="600px">
                            </div>
                            <div class="carousel-item">
                                <img src="img/reflex.jpg" class="d-block w-100" alt="Reflejos" width="960px" height="600px">
                            </div>
                            <div class="carousel-item">
                                <img src="img/fix.jpg" class="d-block w-100" alt="Arreglo de Barba" width="960px" height="600px">
                            </div>
                            <div class="carousel-item">
                                <img src="img/brush.jpg" class="d-block w-100" alt="Peinado" width="960px" height="600px">
                            </div>
                            <div class="carousel-item">
                                <img src="img/shave.jpg" class="d-block w-100" alt="Afeitado" width="960px" height="600px">
                            </div>
                            <div class="carousel-item">
                                <img src="img/dye.jpg" class="d-block w-100" alt="Color" width="960px" height="600px">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#slide" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#slide" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                        </button>
                    </div>
                    <div class="mask" style="background-color: rgba(0, 255, 0, 0.6);">
                        <div class="d-flex justify-content-center align-items-center h-100"></div>
                            <div class="text-white">
                                <h2 class="mb-3">Atención a Domicilio</h2>
                            </div>
                    </div>
                    <!-- Jumbotron -->
                    </header>
                    <h3>Todos los Servicios de Peluquería al Mejor Precio.</h3>
                    <br>
                </div>
                <div id="view2">
                    <div class="col-md-7">
                        <br><br><br><br>
                        <h2>Nuestros Precios son los Mejores de la Ciudad</h2>
                        <br>
                        <h3>Lista de Precios:</h3>
                        <br>
                        <?php
                            $i = 0;
                            $ok = false;
                            $sql = "SELECT service, price, img FROM service";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            if ($stmt->rowCount() > 0)
                            {
                                $ok = true;
                                while ($row = $stmt->fetch(PDO::FETCH_OBJ))
                                {
                                    $service[$i] = $row->service;
                                    $price[$i] = $row->price;
                                    $img[$i] = $row->img;
                                    $i++;
                                }
                            }
                            else
                            {
                                echo "<script>toast(1, 'No hay nada aun:', 'Estamos Trabajando en la Tienda Online, Pronto Verás la Lista de Precios.')</script>";
                            }
                            if ($ok)
                            {
                                echo "<script>var service = [];</script>";
                                echo "<script>var price = [];</script>";
                                echo "<script>var img = [];</script>";
                                for ($i = 0; $i < count($service); $i++)
                                {
                                    echo "<script>service[" . $i . "] = '" . $service[$i] . "';</script>";
                                    echo "<script>price[" . $i . "] = '" . $price[$i] . "';</script>";
                                    echo "<script>img[" . $i . "] = '" . $img[$i] . "';</script>";
                                }
                            }
                        ?>
                        <div id="table"></div>
                        <br>
                        <span id="page"></span>&nbsp;&nbsp;&nbsp;&nbsp;
                        <button onclick="prev(true)" id="prev" class="btn btn-danger" style="visibility: hidden;">Anteriores Resultados</button>&nbsp;&nbsp;&nbsp;&nbsp;
                        <button onclick="next(true)" id="next" class="btn btn-primary" style="visibility: hidden;">Siguientes Resultados</button><br>
                        <script>change(1, 5, true);</script>
                        <br>
                        <h4>Puedes Registrarte y Solicitar tu Turno Online.</h4>
                    </div>
                </div>
                <div id="view3">
                    <!-- Contenedor de Login -->
                    <div class="auth-container" id="loginContainer">
                        <div class="auth-card">
                            <h2>Bienvenido/a</h2>
                            <p class="subtitle">Accede a tu cuenta para reservar tu turno</p>
                            
                            <form action="profile.php" method="post">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="loginEmail" name="email" placeholder="tu@email.com" required>
                                    <label for="loginEmail">Correo Electrónico</label>
                                </div>
                                
                                <div class="form-floating password-wrapper">
                                    <input type="password" class="form-control" id="pass3" name="pass" placeholder="Contraseña" required>
                                    <label for="pass3">Contraseña</label>
                                    <i onclick="spy(3)" class="far fa-eye password-toggle" id="togglePassword3"></i>
                                </div>
                                
                                <button type="submit" class="btn btn-login">Iniciar Sesión</button>
                            </form>
                            
                            <a href="recover.php" class="forgot-link">¿Olvidaste tu contraseña?</a>
                        </div>
                        
                        <div class="cta-register">
                            <h3>¿Primera vez aquí?</h3>
                            <p>Crea tu cuenta y disfruta de estos beneficios</p>
                            
                            <div class="benefits">
                                <div class="benefit-item">
                                    <i class="fas fa-calendar-check"></i>
                                    <p>Reserva Online</p>
                                </div>
                                <div class="benefit-item">
                                    <i class="fas fa-clock"></i>
                                    <p>Ahorra Tiempo</p>
                                </div>
                                <div class="benefit-item">
                                    <i class="fas fa-gift"></i>
                                    <p>Promociones Exclusivas</p>
                                </div>
                            </div>
                            
                            <button onclick="showRegisterForm()" class="btn btn-register mt-3">
                                Crear Cuenta Gratis
                            </button>
                        </div>
                    </div>
                    
                    <!-- Contenedor de Registro -->
                    <div class="auth-container" id="registerContainer" style="display: none;">
                        <div class="auth-card" style="max-width: 800px;">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h2>Crear Nueva Cuenta</h2>
                                    <p class="subtitle">Completa tus datos para registrarte</p>
                                </div>
                                <button onclick="showLoginForm()" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver
                                </button>
                            </div>
                            
                            <form action="register.php" method="post" id="registerForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="username" name="username" placeholder="Nombre" required>
                                            <label for="username">Nombre</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="surname" name="surname" placeholder="Apellido" required>
                                            <label for="surname">Primer Apellido</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="surname2" name="surname2" placeholder="Segundo Apellido">
                                    <label for="surname2">Segundo Apellido (opcional)</label>
                                </div>
                                
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="address" name="address" placeholder="Dirección" required>
                                    <label for="address">Dirección</label>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="Teléfono" required>
                                            <label for="phone">Teléfono</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="date" class="form-control" id="bday" name="bday" required>
                                            <label for="bday">Fecha de Nacimiento</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                    <label for="email">Correo Electrónico</label>
                                </div>
                                
                                <div class="form-floating mb-3 password-wrapper">
                                    <input type="password" class="form-control" id="pass1" name="pass" placeholder="Contraseña" required>
                                    <label for="pass1">Contraseña</label>
                                    <i onclick="spy(1)" class="far fa-eye password-toggle" id="togglePassword1"></i>
                                </div>
                                
                                <div class="form-floating mb-3 password-wrapper">
                                    <input type="password" class="form-control" id="pass2" placeholder="Repetir Contraseña" required>
                                    <label for="pass2">Repetir Contraseña</label>
                                    <i onclick="spy(2)" class="far fa-eye password-toggle" id="togglePassword2"></i>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-register" style="padding: 0.75rem;">
                                        Crear Mi Cuenta
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>
<?php
include "includes/footer.html";
?>