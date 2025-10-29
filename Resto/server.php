<?php
// ========================================
// IMPORTANTE: Este archivo debe guardarse con codificación UTF-8 SIN BOM
// ========================================

// Establecer codificación UTF-8 en todos los niveles
header('Content-Type: application/json; charset=utf-8');
mb_internal_encoding('UTF-8');
ini_set('default_charset', 'UTF-8');

// Limpiar cualquier output buffer que pueda contaminar el JSON
ob_start();

if (json_decode(file_get_contents('php://input'), true))
{
    $_POST = json_decode(file_get_contents('php://input'), true);
}

if (isset($_REQUEST['id']))
{
    include "includes/conn.php";

    $id = $_REQUEST["id"];

    $id = (int)$id;
    $product = array();

    try {
        // Preparar statement con PDO
        $stmt = $conn->prepare("SELECT id, name, price FROM food WHERE kind = :kind ORDER BY id");
        $stmt->bindParam(':kind', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Procesar resultados
        while($row = $stmt->fetch(PDO::FETCH_OBJ))
        {
            $temp = array();
            $temp['id'] = (string)$row->id;
            $temp['food'] = mb_convert_encoding($row->name, 'UTF-8', 'UTF-8'); // Limpiar encoding
            $temp['price'] = (string)$row->price;
            array_push($product, $temp);
        }

        // Limpiar buffer antes de enviar JSON
        ob_end_clean();

        // Enviar JSON con flags apropiadas para UTF-8
        echo json_encode($product, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();

    } catch (PDOException $e) {
        // Limpiar buffer antes de enviar error
        ob_end_clean();
        http_response_code(500);
        echo json_encode([
            "error" => "Fallo al consultar los datos",
            "details" => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }
}

// Limpiar buffer si llegamos aquí
ob_end_clean();

$title = "Enviando Datos a Android";
include "includes/header.php";
?>
<section class="container-fluid pt-3">
<div id="pc"></div>
    <div id="mobile"></div>
    <div class="row">
        <div class="col-md-1"></div>
            <div class="col-md-10">
                <div id="view1">
                </div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>
<?php
include "includes/footer.html";
?>

