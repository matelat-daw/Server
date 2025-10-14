<?php
include "includes/conn.php";
$i = 0;
$id = 3;
$serviceString = "";
$service_id = "";
$firstsql = "SELECT service_id FROM invoice WHERE client_id='$id'";
$stmt = $conn->prepare($firstsql);
$stmt->execute();
if ($stmt->rowCount() > 0) // Si hay resultados declaro las variables.
{
    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) // Cargo los datos en $row.
    {
        $service_id = $row->service_id; // Obtengo el campo service_id que contiene los id de los servicios separados por ",".
        $serviceArray[$i] = explode(",", $service_id); // Los exploto en un array[], en el primer array $array[0] pondrá todos los números separados en el segundo array: $array[0][0], $array[0][1], $array[0][2], etc.;
        $i++; // Incremento el indice del array y arriba continua: $array[1][0], $array[1][1], $array[1][2], etc.
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testing Things</title>
</head>
<body>
    <h1>El Resutado Es:</h1>
    <?php
    $service = [];
    $price = [];
    for ($i = 0; $i < count($serviceArray); $i++) // Hago un doble bucle cuento el tamaño del array por fuera.
    {
        $arrayCount[$i] = count($serviceArray[$i]) - 1; // Guardo en el array arrayCount el tamaño de cada array, se descuenta 1 ya que siempre hay una coma al final de la cadena y la cuetna como un valor más.
        // for ($j = 0; $j < count($serviceArray[$i]); $j++) // Cuento el tamaño del array por dentro.
        // {
            // echo "<p>" . $serviceArray[$i][$j] . "</p>"; // Muestro el resultado del array de dentro y de fuera: $array[0][0], $array[0][1], $array[0][2], $array[1][0], $array[1][1], $array[1][2], etc.
            switch ($arrayCount[$i])
            {
                case 1: // Si En el array $arrayCount[$i], hay un 1, solo se ha comprado un servicio.
                    for ($k = 0; $k < 1; $k++) // El bucle se usa para pasar por parametro a la función getIt(), además de la conexión con la base de datos, los índices y el array $serviceArray.
                    {
                        getIt($conn, $i, $k, $serviceArray); // Llama a la función getIt() y le pasa los parametros necesarios.
                    }
                    break;
                case 2: // Si En el array $arrayCount[$i], hay un 2, se han comprado dos servicios.
                    for ($k = 0; $k < 2; $k++)
                    {
                        getIt($conn, $i, $k, $serviceArray);
                    }
                    break;
                case 3: // Si En el array $arrayCount[$i], hay un 3, se han comprado tres servicios.
                    for ($k = 0; $k < 3; $k++)
                    {
                        getIt($conn, $i, $k, $serviceArray);
                    }
                    break;
                case 4:
                    for ($k = 0; $k < 4; $k++)
                    {
                        getIt($conn, $i, $k, $serviceArray);
                    }
                    break;
                case 5:
                    for ($k = 0; $k < 5; $k++)
                    {
                        getIt($conn, $i, $k, $serviceArray);
                    }
                    break;
                case 6:
                    for ($k = 0; $k < 6; $k++)
                    {
                        getIt($conn, $i, $k, $serviceArray);
                    }
                    break;
                case 7:
                    for ($k = 0; $k < 7; $k++)
                    {
                        getIt($conn, $i, $k, $serviceArray);
                    }
                    break;
                case 8:
                    for ($k = 0; $k < 8; $k++)
                    {
                        getIt($conn, $i, $k, $serviceArray);
                    }
                    break;
                case 9:
                    for ($k = 0; $k < 9; $k++)
                    {
                        getIt($conn, $i, $k, $serviceArray);
                    }
                    break;
                case 10:
                    for ($k = 0; $k < 10; $k++)
                    {
                        getIt($conn, $i, $k, $serviceArray);
                    }
                    break;
                case 11:
                    for ($k = 0; $k < 11; $k++)
                    {
                        getIt($conn, $i, $k, $serviceArray);
                    }
                    break;
                case 12:
                    for ($k = 0; $k < 12; $k++)
                    {
                        getIt($conn, $i, $k, $serviceArray);
                    }
                    break;
            }
        // }
    }

    function getIt($conn, $i, $k, $serviceArray) // Función getIt(), recibe los parametros necesarios, la conexión con la base de datos $conn, los índices $i y $k y el array $serviceArray.
    {
        global $service; // Hago glabales las variables $service y $price para poder usarlas sin pasarlas como referencia.
        global $price;
        $service[] = []; // Al array $service le asigno un array (doble array).
        $price[] = [];
        $sql = "SELECT service, price FROM services WHERE " . $serviceArray[$i][$k] . "=id"; // Hago una consulta a la base de datos para obtener el nombre de los servicios y los precios, comparando las id de los servicios con las id almacenadas en el array $serviceArray usando los índices que también llegan como parametro.
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() > 0) // Si hay resultados.
        {
            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) // Cargo los datos en $row.
            {
                array_push($service[$i], $row->service . "<br>"); // Hago un push del contenido del campo service, el nombre del servicio, en el array $service en el índice que corresponda, $i.
                array_push($price[$i], $row->price . "<br>"); // Hago un push del contenido del campo price, el precio del servicio, en el array $price. en el índice que corresponda, $i.
            }
        }
    }

    for ($i = 0; $i < count($service); $i++) // Bucle interno desde 0 al tamaño del doble array $service.
    {
        echo "<h2>Array: " . ($i + 1) . "</h2>"; // Muestro en que posición del array están los servicios que se muestran a continuación.
        for ($j = 0; $j < count($service[$i]); $j++) // Bucle interno desde 0 al tamaño del array $service[$i] en el indice $i.
        {
            echo "<h3>" . $service[$i][$j] . "</h3>"; // Muestro el contenido del doble array $service.
            echo "<h3>" . $price[$i][$j] . "</h3>";
        }
    }
    ?>
</body>
</html>