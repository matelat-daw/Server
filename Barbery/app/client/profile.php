<?php
include $_SERVER['DOCUMENT_ROOT'] . "/Barbery/includes/conn.php";
include $_SERVER['DOCUMENT_ROOT'] . "/Barbery/api/client/getdata.php";

if (isset($_POST["email"])) // Si se recibe el email del cliente
{
    $ok = false; // Booleano para verificar si los datos son correctos.
    $email = $_POST["email"]; // Lo asigno a la variable $email.
    $pass = $_POST["pass"]; // Asigno la Password a la variable $pass.
    $sql = "SELECT * FROM client WHERE email='$email';"; // Preparo la consulta con el email.
    $stmt = $conn->prepare($sql); // Hago la consulta a la base de datos con la conexión y la consulta recibidas.
    $stmt->execute(); // La ejecuto.
    if ($stmt->rowCount() > 0) // Si hubo resultados.
    {
        $row = $stmt->fetch(PDO::FETCH_OBJ); // Cargo el resultado en $row.
        if (password_verify($pass, $row->pass)) // Verifico la contraseña enviada con la de la base de datos descifrada.
        {
            $id = $row->id; // Si la contraseña es correcta, obtengo la ID del cliente.
            $name = $row->name; // Obtengo el nombre del cliente.
            $ok = true; // Pongo $ok a true.
        }
    }
    if ($ok) // Si $ok esta a true.
    {
        $_SESSION["client"] = $id; // Asigno a la variable de sesión client la id del cliente.
        $_SESSION["name"] = $name; // Asigno a la variable de sesión name el nombre del cliente.
    }
    else // Si $ok es false.
    {
        session_destroy(); // Destruyo la sesión.
    }
}

$title = "La Peluquería de Javier Borneo - Perfil de Cliente";
include $_SERVER['DOCUMENT_ROOT'] . "/Barbery/includes/header.php";

if (isset($_SESSION["client"])) // Verifico si la sesión no está vacia.
{
    include $_SERVER['DOCUMENT_ROOT'] . "/Barbery/includes/modal.html";
    $ok = false; // Booleano para verificar si los datos son correctos.
    $id = $_SESSION["client"]; // Asigno a la variable $id el valor de la sesión client.
    $sql = "SELECT * FROM client WHERE id=$id;"; // Preparo una consulta por la ID.
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_OBJ); // Asigno el resultado a la variable $row.
    $name = $row->name; // Asigno el contenido de $row a variables.
    $address = $row->address;
    $phone = $row->phone;
    $email = $row->email;
    $bday = $row->bday;
    $b_day = strtotime($bday);
    $bday = date("Y-m-d", $b_day);
    include $_SERVER['DOCUMENT_ROOT'] . "/Barbery/includes/nav_profile.php";
    include $_SERVER['DOCUMENT_ROOT'] . "/Barbery/includes/nav-mob-profile.php";
?>
<section class="container-fluid pt-3">
    <div class="row">
        <div class="col-12">
            <div id="view1" class="profile-dashboard">
                <br>
                <!-- Header de Bienvenida -->
                <div class="welcome-header">
                    <h1>Te damos la Bienvenida:</h1>
                    <h2 class="client-name"><?php echo $name; ?></h2>
                </div>
                
                <!-- Grid de Cards -->
                <div class="dashboard-grid">
                    
                    <!-- Card: Próxima Cita -->
                    <div class="dashboard-card appointment-card">
                        <div class="card-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3>Tu Próxima Cita</h3>
                        <?php
                        $sql = "SELECT date, time FROM client WHERE id=$id;";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        if ($stmt->rowCount() > 0) {
                            $row = $stmt->fetch(PDO::FETCH_OBJ);
                            if ($row->date !== null && $row->time !== null) {
                                $my_date = explode("-", $row->date);
                                echo "<div class='appointment-info'>";
                                echo "<p class='appointment-date'>" . $my_date[2] . "/" . $my_date[1] . "/" . $my_date[0] . "</p>";
                                echo "<p class='appointment-time'>" . $row->time . " Hs.</p>";
                                echo "</div>";
                            } else {
                                echo "<p class='no-appointment'>No tienes citas programadas</p>";
                                echo "<a href='/Barbery/app/client/appointments/request.php' class='btn-card'>Solicitar Cita</a>";
                            }
                        }
                        ?>
                    </div>
                    
                    <!-- Card: Mis Datos -->
                    <div class="dashboard-card data-card">
                        <div class="card-icon">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <h3>Mis Datos Personales</h3>
                        <div class="data-list">
                            <div class="data-item">
                                <i class="fas fa-envelope"></i>
                                <span><?php echo $email; ?></span>
                            </div>
                            <div class="data-item">
                                <i class="fas fa-phone"></i>
                                <span><?php echo $phone; ?></span>
                            </div>
                            <div class="data-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo $address; ?></span>
                            </div>
                            <div class="data-item">
                                <i class="fas fa-birthday-cake"></i>
                                <span><?php 
                                    $b_day_display = explode("-", $bday);
                                    echo $b_day_display[2] . "/" . $b_day_display[1] . "/" . $b_day_display[0];
                                ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card: Modificar Datos -->
                    <div class="dashboard-card edit-card">
                        <div class="card-icon">
                            <i class="fas fa-edit"></i>
                        </div>
                        <h3>Modificar Mis Datos</h3>
                        <form action='/Barbery/api/client/modify.php' method='post' onsubmit='return verify()'>
                            <div class="form-group-compact">
                                <input type='text' class="form-control-compact" name='username' value='<?php echo $name; ?>' placeholder="Nombre Completo" required>
                            </div>
                            
                            <div class="form-group-compact">
                                <input type='text' class="form-control-compact" name='address' value='<?php echo $address; ?>' placeholder="Dirección" required>
                            </div>
                            
                            <div class="form-row-compact">
                                <input type='text' class="form-control-compact" name='phone' value='<?php echo $phone; ?>' placeholder="Teléfono" required>
                                <input type='date' class="form-control-compact" name='bday' value='<?php echo $bday; ?>' required>
                            </div>
                            
                            <div class="form-group-compact">
                                <input type='email' class="form-control-compact" name='email' value='<?php echo $email; ?>' placeholder="E-mail" required>
                            </div>
                            
                            <div class="password-section">
                                <p class="password-hint"><i class="fas fa-info-circle"></i> Deja en blanco para mantener tu contraseña actual</p>
                                <div class="form-group-compact password-wrapper">
                                    <input type='password' class="form-control-compact" name='pass' id='pass1' placeholder="Nueva Contraseña" onkeypress="showEye(1)">
                                    <i onclick="spy(1)" class="far fa-eye password-toggle" id="togglePassword1" style="visibility: hidden;"></i>
                                </div>
                                
                                <div class="form-group-compact password-wrapper">
                                    <input type='password' class="form-control-compact" id='pass2' placeholder="Repetir Contraseña" onkeypress="showEye(2)">
                                    <i onclick="spy(2)" class="far fa-eye password-toggle" id="togglePassword2" style="visibility: hidden;"></i>
                                </div>
                            </div>
                            
                            <button type='submit' class="btn-card btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </form>
                    </div>
                    
                    <!-- Card: Acciones Rápidas -->
                    <div class="dashboard-card actions-card">
                        <div class="card-icon danger">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3>Zona Peligrosa</h3>
                        <p class="danger-warning">Esta acción es permanente y no se puede deshacer.</p>
                        <form action="/Barbery/api/client/delete.php" method="post" onsubmit="return confirm('¿Estás seguro de que quieres eliminar tu perfil? Esta acción no se puede deshacer.');">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <button type="submit" class="btn-card btn-danger">
                                <i class="fas fa-trash-alt"></i> Eliminar Mi Perfil
                            </button>
                        </form>
                    </div>
                    
                </div>
                <!-- Fin Grid de Cards -->
                
                <!-- Sección de Historial de Compras -->
                <div class="purchases-section">
                    <h2><i class="fas fa-shopping-bag"></i> Tu Historial de Compras</h2>
                    <div class="purchases-card">
                        <?php
                        $index = 0;
                        $ids = [];
                        $array = [];
                        $qtty = [];
                        $service = [];
                        $service[] = [];
                        $price = [];
                        $price[] = [];

                            $sql = "SELECT invoice_id FROM sold JOIN invoice ON sold.invoice_id=invoice.id WHERE invoice.client_id=$id GROUP BY invoice_id;";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            if ($stmt->rowCount() > 0)
                            {
                                $ok = true;
                                while ($row = $stmt->fetch(PDO::FETCH_OBJ))
                                {
                                    $ids[$index] = $row->invoice_id;
                                    $index++;
                                }
                                $index = 0;
                                $sql = "SELECT invoice_id, invoice.total, invoice.inv_date, invoice.inv_time FROM sold JOIN invoice ON sold.invoice_id=invoice.id WHERE invoice.client_id=$id GROUP BY invoice_id;";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                while ($row = $stmt->fetch(PDO::FETCH_OBJ))
                                {
                                    $total[$index] = $row->total;
                                    $date[$index] = $row->inv_date;
                                    $time[$index] = $row->inv_time;
                                    $index++;
                                }
                                $index = 0;
                                $array[] = [];
                                $qtty[] = [];
                                $sql = "SELECT * FROM sold INNER JOIN invoice WHERE invoice.client_id=$id AND invoice.id=sold.invoice_id;";
                                $stmt_sold = $conn->prepare($sql);
                                $stmt_sold->execute();
                                $ids2 = [];
                                $serv = [];
                                $qtt = [];
                                while ($row_sold = $stmt_sold->fetch(PDO::FETCH_OBJ))
                                {
                                    $ids2[$index] = $row_sold->invoice_id;
                                    $serv[$index] = $row_sold->service_id;
                                    $qtt[$index] = $row_sold->qtty . "<br>";
                                    $index++;
                                }
                                $i = 0;
                                $index = 0;
                                for ($z = 0; $z < count($ids); $z++)
                                {
                                    recursive($index, $serv, $qtt, $ids2, $i);
                                    $i++;
                                    $index++;
                                }
                                getService($conn, $array, "html");
                            }
                            else // Si no hay datos
                            {
                                echo "<script>toast(1, 'Aun sin Datos', 'No Hay Ningúna Factura Tuya Registrada.');</script>"; // No hay Registros.
                            }
                            if ($ok) // Si se encontraron facturas.
                            {
                                echo "<script>var name = '';</script>
                                <script>var invoice = [];</script>
                                <script>var service = [];</script>
                                <script>var price = [];</script>
                                <script>var qtties = [];</script>
                                <script>var total = [];</script>
                                <script>var date = [];</script>
                                <script>var time = [];</script>
                                <script>name = '" . $name . "';</script>"; // Les asigno los datos de PHP.
                                for ($i = 0; $i < count($ids); $i++)
                                {
                                    echo "<script>invoice[" . $i . "] = " . $ids[$i] . ";</script>
                                    <script>total[" . $i . "] = '" . $total[$i] . "';</script>
                                    <script>date[" . $i . "] = '" . $date[$i] . "';</script>
                                    <script>time[" . $i . "] = '" . $time[$i] . "';</script>";
                                }
                                for ($i = 0; $i < count($service); $i++) // Bucle interno desde 0 al tamaño del doble array $service.
                                {
                                    echo "<script> service[" . $i . "] = [];
                                    price[" . $i . "] = [];
                                    qtties[" . $i . "] = [];</script>";
                                    for ($j = 0; $j < count($service[$i]); $j++)
                                    {
                                        echo "<script>qtties[" . $i . "][" . $j . "] = '" . $qtty[$i][$j] . "';</script>
                                        <script>service[" . $i . "][" . $j . "] = '" . $service[$i][$j] . "';</script>
                                        <script>price[" . $i . "][" . $j . "] = '" . $price[$i][$j] . "';</script>";
                                    }
                                }
                                ?>
                                <div id="table"></div>
                                <div class="pagination-controls">
                                    <span id="page"></span>
                                    <button onclick="prev(false)" id="prev" class="btn btn-secondary" style="visibility: hidden;"><i class="fas fa-arrow-left"></i> Anterior</button>
                                    <button onclick="next(false)" id="next" class="btn btn-secondary" style="visibility: hidden;">Siguiente <i class="fas fa-arrow-right"></i></button>
                                </div>
                                <script>change(1, 5, false);</script>
                                <?php
                                // Se muestran las facturas del cliente.
                            }
                            ?>
                        </div>
                    </div>
                    <!-- Fin Sección Compras -->
                </div>
                <!-- Fin Profile Dashboard -->
            </div>
        </div>
</section>
<?php
}
else
{
    include $_SERVER['DOCUMENT_ROOT'] . "/Barbery/includes/modal_index.html";
    echo "<script>toast(1, 'Ha Habido un Error', 'Has Llegado Aquí por Error.');</script>"; // Error, has llegado por el camino equivocado.
}
include $_SERVER['DOCUMENT_ROOT'] . "/Barbery/includes/footer.html";
?>