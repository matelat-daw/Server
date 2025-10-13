<?php
function result($conn, $row, $where, $how) // Función result recibe la conexión, las filas de la base de datos $row y un 1 o un 0 para saber de donde se llama y cómo se llamó.
{
    global $table_name, $client, $waiter, $price, $partial, $product, $quantity;
    
    // Inicializar variables para evitar conflictos
    $product = "";
    $price = "";
    $quantity = "";
    $partial = "";
    
    // Obtener el ID correcto de la factura que se está procesando
    if ($how == 0) {
        $invoice_id = $row["id"]; // Para export.php (array)
        $table_name = getTable($conn, $row["table_id"]);
        $client = getClient($conn, $row["client_id"]);
        $waiter = getWaiter($conn, $row["waiter_id"]);
    } else {
        $invoice_id = $row->id; // Para otras vistas (objeto)
        $table_name = getTable($conn, $row->table_id);
        $client = getClient($conn, $row->client_id);
        $waiter = getWaiter($conn, $row->waiter_id);
    }

    // Obtener los productos vendidos para ESTA factura específica
    try {
        $sql = "SELECT * FROM sold WHERE invoice_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$invoice_id]);
        
        $products = [];
        $quantities = [];
        $i = 0;
        
        while ($row_sold = $stmt->fetch(PDO::FETCH_OBJ)) {
            $products[$i] = $row_sold->product_id;
            $quantities[$i] = $row_sold->quantity;
            $i++;
        }
        
        // Si no hay productos, establecer valores por defecto
        if (empty($products)) {
            $product = "Sin productos";
            $price = "0.00 $";
            $quantity = "0";
            $partial = "0.00 $";
            return;
        }
    } catch (Exception $e) {
        error_log("Error en función result: " . $e->getMessage());
        $product = "Error";
        $price = "0.00 $";
        $quantity = "0";
        $partial = "0.00 $";
        return;
    }

    // Arrays para almacenar la información de cada producto
    $products_info = [];
    $prices_info = [];
    $quantities_info = [];
    $partials_info = [];
    
    // Procesar cada producto
    for ($i = 0; $i < count($products); $i++) {
        try {
            $sql_product = "SELECT name, price FROM food WHERE id = ?";
            $stmt = $conn->prepare($sql_product);
            $stmt->execute([$products[$i]]);
            $row_product = $stmt->fetch(PDO::FETCH_OBJ);
            
            if ($row_product) {
                $product_name = $row_product->name;
                $product_price = $row_product->price;
                $partial_amount = $product_price * $quantities[$i];
                
                $products_info[] = $product_name;
                $quantities_info[] = $quantities[$i];
                
                // Formatear precios según el contexto
                if ($where == 1) { // HTML
                    $prices_info[] = number_format((float)$product_price, 2, ',', '.') . " $";
                    $partials_info[] = number_format((float)$partial_amount, 2, ',', '.') . " $";
                } else { // Excel - valores numéricos
                    $prices_info[] = (float)$product_price;
                    $partials_info[] = (float)$partial_amount;
                }
            } else {
                // Si no se encuentra el producto, usar valores por defecto
                error_log("Producto no encontrado con ID: " . $products[$i]);
                $products_info[] = "Producto no encontrado";
                $quantities_info[] = $quantities[$i];
                
                if ($where == 1) { // HTML
                    $prices_info[] = "0.00 $";
                    $partials_info[] = "0.00 $";
                } else { // Excel
                    $prices_info[] = 0.00;
                    $partials_info[] = 0.00;
                }
            }
        } catch (Exception $e) {
            error_log("Error procesando producto " . $products[$i] . ": " . $e->getMessage());
            $products_info[] = "Error al cargar producto";
            $quantities_info[] = $quantities[$i];
            
            if ($where == 1) {
                $prices_info[] = "0.00 $";
                $partials_info[] = "0.00 $";
            } else {
                $prices_info[] = 0.00;
                $partials_info[] = 0.00;
            }
        }
    }
    
    // Unir la información con el separador apropiado
    $separator = ($where == 1) ? "<br>" : "\n";
    $product = implode($separator, $products_info);
    $quantity = implode($separator, $quantities_info);
    
    // Para precios y parciales, manejar según el contexto
    if ($where == 1) { // HTML - ya están formateados
        $price = implode($separator, $prices_info);
        $partial = implode($separator, $partials_info);
    } else { // Excel - convertir a texto con separadores
        $price = implode($separator, $prices_info);
        $partial = implode($separator, $partials_info);
    }
}

function getClient($conn, $name)
{
    if ($name != null)
    {
        $sql = "SELECT name FROM client WHERE id=$name;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row->name;
    }
    else
    {
        return "Consumidor Final";
    }
}

function getWaiter($conn, $waiter_id)
{
    if ($waiter_id != null)
    {
        $sql = "SELECT name FROM waiter WHERE id=$waiter_id;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row->name;
    }
    else
    {
        return "Fonda 13";
    }
}

function getTable($conn, $table_id)
{
    $sql = "SELECT name FROM tables WHERE id=$table_id;";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_OBJ);
    return $row->name;
}
?>