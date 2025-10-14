<?php
include "includes/conn.php";
include "includes/modal-dismiss.html";

if (isset($_POST["invited"])) // Si la cita la reserva el administrador.
{
    $already = false;
    $name = "Consumidor Final";
    $address = "";
    $phone = "";
    $email = $_POST["invited"];
    $pass = "1111";
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $bday = "";
    $date = "";
    $time = "";

    $stmt = $conn->prepare("SELECT id, email FROM client"); // Busco los E-mail.
    $stmt->execute();
    if ($stmt->rowCount() > 0)
    {
        while($row = $stmt->fetch(PDO::FETCH_OBJ))
        {
            if ($email == $row->email) // Si está cuaquiera de los tres.
            {
                $_SESSION["client"] = $row->id;
                $_SESSION["name"] = $name;
                $already = true; // Pongo $already a true.
                break; // Salgo de la busqueda.
            }
        }
    }

    if (!$already) // Si no están en la base de datos ni E-mail ni Teléfono.
    {
        $stmt = $conn->prepare("INSERT INTO client VALUES (:id, :name, :address, :phone, :email, :pass, :bday, :date, :time);");

        $stmt->execute(array(':id' => null, ':name' => $name, ':address' => $address, ':phone' => $phone, ':email' => $email, ':pass' => $hash, ':bday' => $bday, ':date' => $date, ':time' => $time));

        $_SESSION["client"] = $conn->lastInsertId();
        $_SESSION["name"] = $name;
        echo "<script>toast(0, 'Cliente Agregado', 'Se Ha Registrado el Cliente $name.');</script>";
        // Inserto los datos y aviso.
    }
}
$title = "Citas a Clientes - La Peluquería de Javier Borneo";
include "includes/header.php";
include "includes/nav_request.php";
include "includes/nav-mob-request.php";
?>
<section class="container-fluid pt-3">
    <div class="row">
        <div class="col-md-1" style="width: 5%;"></div>
            <div class="col-md-10">
                <div id="view1">
                <br><br><br><br>
                    <?php
                    if (isset($_SESSION["client"])) // Aquí entra si está abierta la sesión client.
                    {
                        $id = $_SESSION["client"];
                        if (!isset($_POST['date'])) // Si no se envió la fecha, vengo del perfil de un cliente.
                        {
                            echo '<h3 class="auto-style1"><strong>Te Damos la Bienvenida a la Peluquería de Javier Borneo.</strong></h3><br>';
                            
                            echo '<h4 class="auto-style1">Por Favor Pide tu Turno Usando el Calendario para Poder Atenderte.</h4><br>';
                            echo '<form method="post">';
                            echo '<label><input type="date" name="date" id="day" required> Selecciona el Día de tu Turno</label>';
                            echo '<br><br>';
                            echo '<input type="submit" value="Tocá aquí para Elegir la Hora." style="height:32px;">';
                            echo '</form>';
                            echo '<br><br>';
                        }
                        else // Si la fecha se envió vengo del script de administración.
                        {
                            $latin = "";
                            $date = $_POST['date'];
                            $preLatin = explode ("-", $date);
                            $reverse = array_reverse ($preLatin);
                            for ($i = 0; $i < count($reverse); $i++)
                            {
                                $latin .= $reverse[$i];
                                if ($i == 2)
                                {
                                    break;
                                }
                                $latin .= "-";
                            }
                            $z = 0;
                            $time_array = [];
                            $stmt = $conn->prepare("SELECT time FROM client WHERE date='$date'");
                            $stmt->execute();
                            if ($stmt->rowCount() > 0)
                            {
                                while ($row = $stmt->fetch(PDO::FETCH_OBJ))
                                {
                                    $time_array[$z] = $row->time;
                                    $z++;
                                }
                            }
                            
                            $index = 0; // Indice correlativo de los turnos.
                            $zero = 0; // Variable a 0 para los segundos.
                            $exist = false; // Booleano para avisar si el turno ya está reservado.
                            echo '<form action="turn.php" method="post" id="turnForm" onsubmit="return validateTurnForm()">';
                            echo '<input type="hidden" name="id" value="' . $id . '">';
                            echo '<input type="text" value="' . $latin . '" readonly style="font-size: 1.2rem; padding: 0.5rem; width: 100%; max-width: 400px; margin-bottom: 1rem;">';
                            echo '<input type="hidden" name="date" value="' . $date . '">';
                            echo '<br>';
                            echo '<h3 style="color: #667eea;">Selecciona la Hora del Turno</h3>';
                            echo '<p style="color: #666; margin-bottom: 1.5rem;">Horario disponible: 9:00 - 13:00 y 15:00 - 19:00</p>';
                            echo '<div style="max-height: 400px; overflow-y: auto; padding: 1rem; border: 2px solid #e0e0e0; border-radius: 10px; background: #f9f9f9;">';
                            for($hours = 9; $hours < 19; $hours++) // Horas de las Citas, de 9 a 19.
                            {
                                if ($hours < 13 || $hours > 14)
                                {
                                    for($mins = 0; $mins < 60; $mins += 30) // Minutos de las Citas, cada 30 minutos.
                                    {
                                        $turn_array[$index] = str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($mins, 2, '0', STR_PAD_LEFT) . ':' . str_pad($zero, 2, '0', STR_PAD_LEFT); // Carga en un array las citas.
                                        for ($j = $index; $j < (count($time_array)) + $index; $j++) // Bucle para verificar si la cita ya está solicitada.
                                        {
                                            if ($turn_array[$index] == $time_array[$j - $index]) // Si la hora de la cita está en la base de datos
                                            {
                                                $exist = true; // Si es así pongo $exist a true, ya está solicitada.
                                                break; // Rompo el bucle.
                                            }
                                        }
                                        if (!$exist) // Si $exist está a false.
                                        {
                                            turns($turn_array[$index], ($index + 1), (($index + 1) . "i")); // Llama a la función turns. // Muestro la hora de la cita en pantalla, para poder seleccionarla.
                                        }
                                        else // Si ya está seleccionada
                                        {
                                            $exist = false; // Cambio el estado de $exist a false.
                                        }
                                        $index++; // Incremento el número de la cita.
                                    }
                                }
                            }
                            echo '</div>'; // Cerrar el contenedor de turnos
                            echo '<br><button type="submit" class="btn btn-primary btn-lg" style="margin-top: 1rem; padding: 0.75rem 2rem;">Confirmar Turno</button>';
                            echo '</form>';
                            echo '<script>
                            function validateTurnForm() {
                                var radios = document.getElementsByName("time");
                                var selected = false;
                                for (var i = 0; i < radios.length; i++) {
                                    if (radios[i].checked) {
                                        selected = true;
                                        break;
                                    }
                                }
                                if (!selected) {
                                    toast(1, "Error", "Por favor, selecciona una hora para tu turno.");
                                    return false;
                                }
                                return true;
                            }
                            </script>';
                        }
                    }
                    else
                    {
                        echo "Donde estoy?";
                    }
                ?>
                </div>
            </div>
        <div class="col-md-1" style="width: 5%;"></div>
    </div>
</section>
<?php
function turns($turn, $id, $idi)
{
    $label_style = 'display: block; padding: 0.75rem 1rem; margin: 0.5rem 0; background: white; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; transition: all 0.3s ease;';
    $input_style = 'margin-right: 0.75rem; cursor: pointer;';
    $time_style = 'font-weight: 600; color: #667eea;';
    
    if ($id > 9)
    {
        echo '<label id="' . $idi . '" style="' . $label_style . '" onmouseover="this.style.borderColor=\'#667eea\'; this.style.background=\'#f0f4ff\';" onmouseout="this.style.borderColor=\'#e0e0e0\'; this.style.background=\'white\';">';
        echo '<input type="radio" value="' . $turn . '" name="time" id="' . $id . '" style="' . $input_style . '" onchange="this.parentElement.style.borderColor=\'#667eea\'; this.parentElement.style.background=\'#e8f0fe\';">';
        echo 'Turno ' . $id . ' - <span style="' . $time_style . '">' . $turn . '</span>';
        echo '</label>';
    }
    else
    {
        echo '<label id="' . $idi . '" style="' . $label_style . '" onmouseover="this.style.borderColor=\'#667eea\'; this.style.background=\'#f0f4ff\';" onmouseout="this.style.borderColor=\'#e0e0e0\'; this.style.background=\'white\';">';
        echo '<input type="radio" value="' . $turn . '" name="time" id="' . $id . '" style="' . $input_style . '" onchange="this.parentElement.style.borderColor=\'#667eea\'; this.parentElement.style.background=\'#e8f0fe\';">';
        echo 'Turno ' . $id . ' - <span style="' . $time_style . '">' . $turn . '</span>';
        echo '</label>';
    }
}
include "includes/footer.html";
?>
<script>screenSize();</script>
<script>screen();</script>
<!-- Script para detectar si la pantalla modifica su tamaño horizontal -->
<script>
    window.addEventListener('resize', screen);
    screen();
</script>
</body>
</html>