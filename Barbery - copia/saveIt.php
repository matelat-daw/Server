<?php
include "includes/conn.php";
include 'vendor/autoload.php';
include "getdata.php";

$service = [];
$services = [];
$prices = [];
$qtty = [];

if(isset($_REQUEST["id"]))
{
    $id = $_REQUEST["id"];

    $stmt = $conn->prepare("SET lc_time_names = 'es_ES'");
	$stmt->execute();

    $sql = ("SELECT *, DATE_FORMAT(inv_date,'%d %M %Y') as date FROM invoice WHERE id=$id");
	
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_OBJ);

    $sheet = new PhpOffice\PhpSpreadsheet\Spreadsheet(); // Hay que usarlo así en Wordpress, también funciona en cualquier script de PHP.
    $active_sheet = $sheet->getActiveSheet();

	$active_sheet->setCellValue('A1', 'Nº de factura');
	$active_sheet->setCellValue('B1', 'Cliente');
	$active_sheet->setCellValue('C1', 'Servicio');
	$active_sheet->setCellValue('D1', 'Precio');
    $active_sheet->setCellValue('E1', 'Cantidad');
    $active_sheet->setCellValue('F1', 'Parcial');
	$active_sheet->setCellValue('G1', 'Día');
	$active_sheet->setCellValue('H1', 'Hora');
    $active_sheet->setCellValue('I1', 'Base Imponible');
	$active_sheet->setCellValue('J1', 'I.V.A.');
    $active_sheet->getStyle('J1')->getAlignment()->setHorizontal("center");
	$active_sheet->setCellValue('K1', 'Total + I.V.A.');

	$count = 2;
    $i = 0;

    // $id = $row->id;
    if ($row->client_id !== null)
    {
        $client = getClient($conn, $row->client_id);
    }
    else
    {
        $client = "Consumidor Final";
    }
    $total = $row->total;
    $mydate = $row->inv_date;
    $time = $row->inv_time;

    $service[0] = [];
    $services[0] = [];
    $price[0] = [];
    $qtty[0] = [];

    $sql = "SELECT * FROM sold WHERE invoice_id=$id;";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_OBJ))
    {
        $services[0][$i] = $row->service_id;
        $qtty[0][$i] = $row->qtty . "\n";
        $i++;
    }
    getService($conn, $services, "excel");

    $servi = "";
    $pric = "";
    $qtti = "";
    $partial = "";
    for ($i = 0; $i < count($service[0]); $i++)
    {
        $servi .= $service[0][$i];
        $pric .= $price[0][$i];
        $qtti .= $qtty[0][$i];
        $partial .= number_format((floatval($price[0][$i]) * $qtty[0][$i]), 2) . " $\n";
    }

    $active_sheet->setCellValue('A' . $count, $id);
    $active_sheet->setCellValue('B' . $count, $client);
    $active_sheet->setCellValue('C' . $count, $servi);
    $active_sheet->setCellValue('D' . $count, $pric);
    $active_sheet->getStyle('D' . $count)->getNumberFormat()->setFormatCode('#,##0.00 $');
    $active_sheet->getStyle('D' . $count)->getAlignment()->setHorizontal("right"); // Alineación del texto con la cadena 'right', Alinea a la Derecha.
    $active_sheet->setCellValue('E' . $count, $qtti);
    $active_sheet->getStyle('E' . $count)->getAlignment()->setHorizontal("center");
    $active_sheet->setCellValue('F' . $count, $partial);
    $active_sheet->getStyle('F' . $count)->getAlignment()->setHorizontal("right");
    $active_sheet->setCellValue('G' . $count, $mydate);
    $active_sheet->getStyle('G' . $count)->getAlignment()->setHorizontal("center");
    $active_sheet->setCellValue('H' . $count, $time);
    $active_sheet->getStyle('H' . $count)->getAlignment()->setHorizontal("center");
    $active_sheet->setCellValue('I' . $count, $total * 100 / 121);
    $active_sheet->getStyle('I' . $count)->getNumberFormat()->setFormatCode('#,##0.00 $');
    $active_sheet->setCellValue('J' . $count, $total * .21 * 100 / 121);
    $active_sheet->getStyle('J' . $count)->getAlignment()->setHorizontal("center");
    $active_sheet->getStyle('J' . $count)->getNumberFormat()->setFormatCode('#,##0.00 $');
    $active_sheet->setCellValue('K' . $count, $total);
    $active_sheet->getStyle('K' . $count)->getNumberFormat()->setFormatCode('#,##0.00 $');

	$active_sheet->setCellValue('J' . ($count + 2), "Total:");
	$active_sheet->setCellValue('K' . ($count + 2), $total);
	$active_sheet->getStyle('K' . ($count + 2))->getNumberFormat()->setFormatCode('#,##0.00 $');
	$active_sheet->setCellValue('A' . ($count + 4), "La Peluquería de Javier Borneo - C.U.I.T 20-22506157-3");

    for ($i = 1; $i < 3; $i++)
    {
        $active_sheet->getRowDimension($i)->setRowHeight(60); // Cambia el tamaño Vertical de las filas usadas en la planilla.

        if ($i == 1)
        {
            $active_sheet->getRowDimension($i)->setRowHeight(20); // Cambia el tamaño Vertical de las filas usadas en la planilla.
            $active_sheet->getColumnDimension(chr(64 + $i))->setWidth(15);
            $active_sheet->getColumnDimension(chr(64 + $i + 1))->setWidth(40); // Si es la Letra C le da el tamaño horizontal 52.
            $active_sheet->getColumnDimension(chr(64 + $i + 2))->setWidth(50); // Si es la Letra C le da el tamaño horizontal 52.
            for ($j = 68; $j < 75; $j++)
            {
                $active_sheet->getColumnDimension(chr($j))->setWidth(15); // Si es la Letra C le da el tamaño horizontal 52.
            }
            $active_sheet->getColumnDimension(chr(64 + $i + 10))->setWidth(20);
        }
    }
    $active_sheet->getRowDimension($i + 1)->setRowHeight(40); // Cambia el tamaño Vertical de las filas usadas en la planilla.
    $active_sheet->getRowDimension($i + 3)->setRowHeight(40); // Cambia el tamaño Vertical de las filas usadas en la planilla.
    $active_sheet->getRowDimension($i + 5)->setRowHeight(40); // Cambia el tamaño Vertical de las filas usadas en la planilla.
		
	$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($sheet, "Xlsx");

	$file_name = "Factura Nº $id - $mydate.Xlsx";

	$writer->save($file_name);

	header('Content-Type: application/x-www-form-urlencoded');

	header('Content-Transfer-Encoding: Binary');

	header("Content-disposition: attachment; filename=\"".$file_name."\"");

	readfile($file_name);

	unlink($file_name);

	exit;
}
?>