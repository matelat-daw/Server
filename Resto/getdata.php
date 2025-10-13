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
    for ($i = 0; $i < count($services); $i++)
	{
		for ($j = 0; $j < count($services[$i]); $j++)
		{
			$sql = "SELECT name, price FROM food WHERE id='" . $services[$i][$j] . "';";
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			$row_service = $stmt->fetch(PDO::FETCH_OBJ);
			if ($where == "html")
			{
				$service[$i][$j] = $row_service->name . "<br>";
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

function recursive($index, $service, $qtt, $id, $i, $j) // Recibe el $index, la cantidad de servicios encontrados en todas las facturas, el array $service, la cantidad de cada servicio $qtt, el array con las ID de las facturas $id, $i es el indice de la factura actual ya que esta función se llama en un bucle y $j que siempre es 0.
{
    global $index, $array, $qtty; // Hago globales las variables ya declaradas $index, contiene en índice, $array y $qtty.
    $count = count($service); // Asigno a $count el tamaño del array $service
    while ($index < $count - 1 && $id[$index] == $id[$index + 1]) // Mientras $index sea menor que el tamaño del array $service Y las ID de las facturas contiguas sea iguales.
    {
        $array[$i][$j] = $service[$index]; // Asigno a $array[$i][$j] el contenido de la ID del servicio en $service[$index]. 
        $qtty[$i][$j] = $qtt[$index]; // Lo mismo para el array bidemensional $qtty con la Cantidad del servicio anterior.
        $j++; // Incremento $j.
        $index++; // Incremento $index.
    }
    $array[$i][$j] = $service[$index];
    $qtty[$i][$j] = $qtt[$index];
}
?>