<?php
function getClient($conn, $id)
{
    if ($id == null)
    {
        $id = "Consumidor Final";
        return $id;
    }
    else
    {
        $sql_client = "SELECT name FROM client WHERE id=$id";
        $stmt = $conn->prepare($sql_client);
        $stmt->execute();
        $row_client = $stmt->fetch(PDO::FETCH_OBJ);
        return $row_client->name;
    }
}

function getService($conn, $services, $where)
{
    global $service, $price;
    if (count($services) == count($services, COUNT_RECURSIVE)) 
    {
        for ($i = 0; $i < count($services); $i++)
        {
            $sql = "SELECT service, price FROM service WHERE id='" . $services[$i] . "';";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            if ($stmt->rowCount() > 0)
            {
                $row_service = $stmt->fetch(PDO::FETCH_OBJ);
                if ($where == "html")
                {
                    $service[0][$i] = $row_service->service . "<br>";
                    $price[0][$i] = $row_service->price . " $<br>";
                }
                else
                {
                    $service[0][$i] = $row_service->service . "\n";
                    $price[0][$i] = $row_service->price . " $\n";
                }
            }
        }
    }
    else
    {
        for ($i = 0; $i < count($services); $i++)
        {
            for ($j = 0; $j < count($services[$i]); $j++)
            {
                $sql = "SELECT service, price FROM service WHERE id='" . $services[$i][$j] . "';";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $row_service = $stmt->fetch(PDO::FETCH_OBJ);
                if ($where == "html")
                {
                    $service[$i][$j] = $row_service->service . "<br>";
                    $price[$i][$j] = $row_service->price . " $<br>";
                }
                else
                {
                    $service[$i][$j] = $row_service->service . "\n";
                    $price[$i][$j] = $row_service->price . " $\n";
                }
            }
        }
    }
}

function recursive($index, $service, $qtt, $id, $i) // Recibe $index, es el índice de los servicios o cantidades encontradas en todas las facturas (Empezando de 0), el array $service, el array $qtt (contiene las cantidades de cada servicio), el array con las ID de las facturas $id, $i es el indice de la factura actual ya que esta función se llama en un bucle y $j que siempre es 0.
{
    $j = 0;
    global $index, $array, $qtty; // Hago globales las variables ya declaradas $index, contiene en índice, $array y $qtty.
    $count = count($service); // Asigno a $count el tamaño del array $service
    while ($index < $count - 1 && $id[$index] == $id[$index + 1]) // Mientras $index sea menor que el tamaño del array $service Y las ID de las facturas contiguas sean iguales.
    {
        $array[$i][$j] = $service[$index]; // Asigno a $array[$i][$j] el contenido de la ID del servicio en $service[$index]. 
        $qtty[$i][$j] = $qtt[$index]; // Lo mismo para el array bidemensional $qtty con la Cantidad del servicio anterior.
        $j++; // Incremento $j.
        $index++; // Incremento $index. $index Mantiene su Valor ya que es una Variable Global y Aunque la Función Termine, al ser Llamada de Vuelta $index no se Reinicia.
    }
    $array[$i][$j] = $service[$index];
    $qtty[$i][$j] = $qtt[$index];
}
?>