<?php
include "includes/conn.php";
include "includes/function.php";
include 'vendor/autoload.php';

if (isset($_REQUEST["id"]))
{
    $id = $_REQUEST["id"];
    $product = "";
    $price = "";
    $qtty = "";
    $wait = "";

    $stmt = $conn->prepare("SET lc_time_names = 'es_ES'");
	$stmt->execute();

    $sql = ("SELECT *, DATE_FORMAT(inv_date,'%d %M %Y') as date FROM invoice WHERE id=$id");
	
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_OBJ);

    result($conn, $row, 0, 1); // Llama a la función result, le pasa la conexión, el resultado de la base de datos y un 0.

    $sheet = new PhpOffice\PhpSpreadsheet\Spreadsheet(); // Hay que usarlo así en Wordpress, también funciona en cualquier script de PHP.
    $active_sheet = $sheet->getActiveSheet();

	$active_sheet->setCellValue('A1', 'Nº de factura');
	$active_sheet->setCellValue('B1', 'Mesa');
	$active_sheet->setCellValue('C1', 'Camarero');
    $active_sheet->setCellValue('D1', 'Producto');
	$active_sheet->setCellValue('E1', 'Precio');
	$active_sheet->setCellValue('F1', 'Cantidad');
	$active_sheet->setCellValue('G1', 'Día');
    $active_sheet->setCellValue('H1', 'Hora');
    $active_sheet->setCellValue('I1', 'Base Imponible');
    $active_sheet->setCellValue('J1', 'Pago de I.V.A. 10%');
    $active_sheet->getStyle('J1')->getAlignment()->setHorizontal("center");
	$active_sheet->setCellValue('K1', 'Total + I.V.A.');

    $id = $row->id;
    $table = $row->table_id;
    $table_name = getTable($conn, $table);
    $total = $row->total;
    $mydate = $row->inv_date;
    $time = $row->inv_time;

    $active_sheet->setCellValue('A2', $id);
    $active_sheet->getStyle('A2')->getAlignment()->setHorizontal("left");
    $active_sheet->setCellValue('B2', $table_name);
    $active_sheet->setCellValue('C2', $wait);
    $active_sheet->setCellValue('D2', $product);
    $active_sheet->setCellValue('E2', $price);
    $active_sheet->getStyle('E2')->getNumberFormat()->setFormatCode('#,##0.00 $');
    $active_sheet->getStyle('E2')->getAlignment()->setHorizontal("right"); // Alineación del texto con la cadena 'right', Alinea a la Derecha.
    $active_sheet->setCellValue('F2', $qtty);
    $active_sheet->getStyle('F2')->getAlignment()->setHorizontal("right"); // Alineación del texto con la cadena 'right', Alinea a la Derecha.
    $active_sheet->setCellValue('G2', $mydate);
    $active_sheet->getStyle('G2')->getAlignment()->setHorizontal("right");
    $active_sheet->setCellValue('H2', $time);
    $active_sheet->getStyle('H2')->getAlignment()->setHorizontal("right");
    $active_sheet->setCellValue('I2', $total * 100 / 110);
    $active_sheet->getStyle('I2')->getNumberFormat()->setFormatCode('#,##0.00 $');
    $active_sheet->setCellValue('J2', $total * 100 / 110 * .1);
    $active_sheet->getStyle('J2')->getAlignment()->setHorizontal("center");
    $active_sheet->getStyle('J2')->getNumberFormat()->setFormatCode('#,##0.00 $');
    $active_sheet->setCellValue('K2', $total);
	$active_sheet->getStyle('K2')->getNumberFormat()->setFormatCode('#,##0.00 $');
	$active_sheet->setCellValue('J4', "Total:");
	$active_sheet->setCellValue('K4', $total);
	$active_sheet->getStyle('K4')->getNumberFormat()->setFormatCode('#,##0.00 $');
	$active_sheet->setCellValue('A6', "Fonda 13 - C.U.I.T. 2-25000000-2 Calle Santa María de Oro Nº 47, 7600, Mar del Plata");

    for ($i = 1; $i <= 2; $i++)
    {
        $active_sheet->getRowDimension($i)->setRowHeight(82); // Cambia el tamaño Vertical de las filas usadas en la planilla.

        if ($i == 1)
        {
            $active_sheet->getRowDimension($i)->setRowHeight(20); // Cambia el tamaño Vertical de las filas usadas en la planilla.
            $active_sheet->getColumnDimension(chr(64 + $i))->setWidth(15);
            $active_sheet->getColumnDimension(chr(64 + $i + 1))->setWidth(15); // Si es la Letra C le da el tamaño horizontal 52.
            $active_sheet->getColumnDimension(chr(64 + $i + 2))->setWidth(30); // Si es la Letra C le da el tamaño horizontal 52.
            $active_sheet->getColumnDimension(chr(64 + $i + 3))->setWidth(52); // Si es la Letra C le da el tamaño horizontal 52.
            $active_sheet->getColumnDimension(chr(64 + $i + 4))->setWidth(15);
            $active_sheet->getColumnDimension(chr(64 + $i + 5))->setWidth(15);
            $active_sheet->getColumnDimension(chr(64 + $i + 6))->setWidth(15);
            $active_sheet->getColumnDimension(chr(64 + $i + 7))->setWidth(15);
            $active_sheet->getColumnDimension(chr(64 + $i + 8))->setWidth(15);
            $active_sheet->getColumnDimension(chr(64 + $i + 9))->setWidth(20);
            $active_sheet->getColumnDimension(chr(64 + $i + 10))->setWidth(15);
        }
    }

    if ($i == 3)
    {
        $active_sheet->getRowDimension($i + 1)->setRowHeight(40); // Cambia el tamaño Vertical de las filas usadas en la planilla.
        $active_sheet->getRowDimension($i + 3)->setRowHeight(40); // Cambia el tamaño Vertical de las filas usadas en la planilla.
    }
		
	$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($sheet, "Xlsx");

	$file_name = "Factura a $table_name - $mydate.Xlsx";

	$writer->save($file_name);

	header('Content-Type: application/x-www-form-urlencoded');

	header('Content-Transfer-Encoding: Binary');

	header("Content-disposition: attachment; filename=\"".$file_name."\"");

	readfile($file_name);

	unlink($file_name);

	exit;
}
?>