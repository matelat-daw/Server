<?php
include 'includes/conn.php';
include 'includes/function.php';
include 'vendor/autoload.php';

// Declarar variables globales
global $table_name, $client, $wait, $price, $partial, $product, $qtty;

// MOVER LA LÓGICA DE EXPORTACIÓN AL INICIO - ANTES DE CUALQUIER SALIDA HTML
if(isset($_POST["export"]))
{
	// Debug temporal - registrar el intento de exportación
	error_log("=== INICIO EXPORTACIÓN ===");
	error_log("Datos POST: " . print_r($_POST, true));
	
	// Limpiar TODOS los buffers de salida antes de hacer CUALQUIER cosa
	while (ob_get_level()) {
		ob_end_clean();
	}
	
	// Obtener datos del POST
	$date = $_POST['date'];
	$year = $_POST['year'];
	
	// Construir la consulta según el trimestre
	switch($date) {
		case 1:
			$query = "SELECT *, DATE_FORMAT(inv_date,'%d %M %Y') date FROM invoice WHERE inv_date BETWEEN CAST('" . $year . "-01-01' AS DATE) AND CAST('" . $year . "-03-31' AS DATE) ORDER BY id";
		break;
		case 2:
			$query = "SELECT *, DATE_FORMAT(inv_date,'%d %M %Y') date FROM invoice WHERE inv_date BETWEEN CAST('" . $year . "-04-01' AS DATE) AND CAST('" . $year . "-06-30' AS DATE) ORDER BY id";
		break;
		case 3:
			$query = "SELECT *, DATE_FORMAT(inv_date,'%d %M %Y') date FROM invoice WHERE inv_date BETWEEN CAST('" . $year . "-07-01' AS DATE) AND CAST('" . $year . "-09-30' AS DATE) ORDER BY id";
		break;
		default:
			$query = "SELECT *, DATE_FORMAT(inv_date,'%d %M %Y') date FROM invoice WHERE inv_date BETWEEN CAST('" . $year . "-10-01' AS DATE) AND CAST('" . $year . "-12-31' AS DATE) ORDER BY id";
		break;
	}
	
	try {
		// Preparar la consulta
		$stmt = $conn->prepare("SET lc_time_names = 'es_ES'");
		$stmt->execute();
		
		$statement = $conn->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		
		error_log("Facturas encontradas: " . count($result));
		
		if (empty($result)) {
			error_log("No hay facturas para exportar");
			header('Content-Type: text/html; charset=utf-8');
			echo '<script>alert("No hay facturas para exportar en el período seleccionado."); window.history.back();</script>';
			exit;
		}
		
		// Crear el spreadsheet
		$file = new PhpOffice\PhpSpreadsheet\Spreadsheet();
		$active_sheet = $file->getActiveSheet();
		
		error_log("Spreadsheet creado exitosamente");
		
		// Configurar propiedades del documento
		$file->getProperties()
			->setCreator("Sistema de Facturación XXXXX")
			->setLastModifiedBy("Sistema de Facturación")
			->setTitle("Informe de Facturas - " . $date . "º Trimestre " . $year)
			->setSubject("Informe trimestral de facturación")
			->setDescription("Reporte detallado de facturas del " . $date . "º trimestre del año " . $year)
			->setKeywords("facturas, trimestre, informe, ventas")
			->setCategory("Informes Financieros");

		// Configurar nombre de la hoja
		$active_sheet->setTitle("Facturas T" . $date . " " . $year);

		// Encabezados con estilo
		$headers = [
			'A1' => 'Nº Factura',
			'B1' => 'Mesa',
			'C1' => 'Cliente', 
			'D1' => 'Camarero',
			'E1' => 'Producto',
			'F1' => 'Precio Unit.',
			'G1' => 'Cantidad',
			'H1' => 'Fecha',
			'I1' => 'Hora',
			'J1' => 'Base Imponible',
			'K1' => 'I.V.A. (10%)',
			'L1' => 'Total + I.V.A.'
		];

		foreach ($headers as $cell => $header) {
			$active_sheet->setCellValue($cell, $header);
		}

		// Estilo para encabezados
		$headerStyle = [
			'font' => [
				'bold' => true,
				'color' => ['rgb' => 'FFFFFF'],
				'size' => 12
			],
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => ['rgb' => '4472C4']
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
			],
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['rgb' => '000000']
				]
			]
		];

		$active_sheet->getStyle('A1:L1')->applyFromArray($headerStyle);

		// Configurar ancho de columnas
		$columnWidths = [
			'A' => 12, 'B' => 15, 'C' => 25, 'D' => 20,
			'E' => 35, 'F' => 15, 'G' => 10, 'H' => 12,
			'I' => 10, 'J' => 18, 'K' => 15, 'L' => 18
		];

		foreach ($columnWidths as $column => $width) {
			$active_sheet->getColumnDimension($column)->setWidth($width);
		}

		$count = 2;
		$totalVentas = 0;
		$totalBaseImponible = 0;
		$totalIVA = 0;

		error_log("Iniciando procesamiento de " . count($result) . " facturas");

		foreach($result as $row) {
			// Inicializar variables para evitar residuos de iteraciones anteriores
			$table_name = "";
			$client = "";
			$wait = "";
			$price = "";
			$partial = "";
			$product = "";
			$qtty = "";
			
			result($conn, $row, 0, 0);
			
			$baseImponible = (float)$row["total"] * 100 / 110;
			$iva = $baseImponible * 0.1;
			$total = (float)$row["total"];
			
			$totalVentas += $total;
			$totalBaseImponible += $baseImponible;
			$totalIVA += $iva;
			
			// Llenar datos
			$active_sheet->setCellValue('A' . $count, $row["id"]);
			$active_sheet->setCellValue('B' . $count, $table_name);
			$active_sheet->setCellValue('C' . $count, $client);
			$active_sheet->setCellValue('D' . $count, $wait);
			$active_sheet->setCellValue('E' . $count, $product);
			
			// Para el precio, manejar múltiples productos mostrando solo el primero como valor numérico
			$firstPrice = 0.00;
			if (!empty($price)) {
				$priceLines = explode("\n", $price);
				$firstPrice = is_numeric($priceLines[0]) ? (float)$priceLines[0] : 0.00;
			}
			$active_sheet->setCellValue('F' . $count, $firstPrice);
			
			// Para cantidad, mostrar la primera cantidad
			$firstQtty = 0;
			if (!empty($qtty)) {
				$qttyLines = explode("\n", $qtty);
				$firstQtty = is_numeric($qttyLines[0]) ? (int)$qttyLines[0] : 0;
			}
			$active_sheet->setCellValue('G' . $count, $firstQtty);
			
			$active_sheet->setCellValue('H' . $count, $row["inv_date"]);
			$active_sheet->setCellValue('I' . $count, $row["inv_time"]);
			$active_sheet->setCellValue('J' . $count, $baseImponible);
			$active_sheet->setCellValue('K' . $count, $iva);
			$active_sheet->setCellValue('L' . $count, $total);

			// Formatear estilos de las celdas
			$active_sheet->getStyle('A' . $count)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$active_sheet->getStyle('F' . $count)->getNumberFormat()->setFormatCode('$#,##0.00');
			$active_sheet->getStyle('G' . $count)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$active_sheet->getStyle('H' . $count)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
			$active_sheet->getStyle('I' . $count)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$active_sheet->getStyle('J' . $count)->getNumberFormat()->setFormatCode('$#,##0.00');
			$active_sheet->getStyle('K' . $count)->getNumberFormat()->setFormatCode('$#,##0.00');
			$active_sheet->getStyle('L' . $count)->getNumberFormat()->setFormatCode('$#,##0.00');

			$count++;
		}

		// Añadir fila de totales
		$totalRow = $count + 1;
		$active_sheet->setCellValue('I' . $totalRow, 'TOTALES:');
		$active_sheet->setCellValue('J' . $totalRow, $totalBaseImponible);
		$active_sheet->setCellValue('K' . $totalRow, $totalIVA);
		$active_sheet->setCellValue('L' . $totalRow, $totalVentas);

		// Estilo para fila de totales
		$totalStyle = [
			'font' => ['bold' => true, 'size' => 12],
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => ['rgb' => 'E7E6E6']
			],
			'borders' => [
				'top' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
					'color' => ['rgb' => '000000']
				]
			]
		];

		$active_sheet->getStyle('I' . $totalRow . ':L' . $totalRow)->applyFromArray($totalStyle);
		$active_sheet->getStyle('J' . $totalRow . ':L' . $totalRow)->getNumberFormat()->setFormatCode('$#,##0.00');

		// Información de la empresa
		$infoRow = $totalRow + 3;
		$active_sheet->setCellValue('A' . $infoRow, 'XXXXX - N.I.F. 20-42000000-3');
		$active_sheet->setCellValue('A' . ($infoRow + 1), 'Informe generado el: ' . date('d/m/Y H:i:s'));
		
		$active_sheet->getStyle('A' . $infoRow . ':A' . ($infoRow + 1))->getFont()->setItalic(true);

		// Configurar altura de filas
		for ($i = 2; $i < $count; $i++) {
			$active_sheet->getRowDimension($i)->setRowHeight(25);
		}
		$active_sheet->getRowDimension(1)->setRowHeight(30);

		// Crear el archivo
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($file, $_POST["file_type"]);
		$fileName = "Facturas_T" . $date . "_" . $year . "_" . date('Ymd_His') . "." . strtolower($_POST["file_type"]);

		error_log("Generando archivo: " . $fileName);

		// Headers para descarga según el tipo de archivo
		if ($_POST["file_type"] == "Xlsx") {
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		} else {
			header('Content-Type: text/csv');
		}
		
		header('Content-Disposition: attachment; filename="' . $fileName . '"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Pragma: public');

		error_log("Headers configurados, enviando archivo...");

		// Enviar el archivo
		$writer->save('php://output');
		
		error_log("=== EXPORTACIÓN COMPLETADA ===");
		exit; // TERMINAR AQUÍ - NO GENERAR HTML

	} catch (Exception $e) {
		error_log("ERROR EN EXPORTACIÓN: " . $e->getMessage());
		error_log("Stack trace: " . $e->getTraceAsString());
		
		header('Content-Type: text/html; charset=utf-8');
		echo '<script>alert("Error al generar el archivo: ' . addslashes($e->getMessage()) . '"); window.history.back();</script>';
		exit;
	}
}

// SOLO SI NO ES EXPORTACIÓN, CONTINUAR CON LA PÁGINA NORMAL
$date = $_POST['date'] ?? 3; // Default para testing
$year = $_POST['year'] ?? 2024; // Default para testing
// SOLO SI NO ES EXPORTACIÓN, CONTINUAR CON LA PÁGINA NORMAL
$date = $_POST['date'] ?? 3; // Default para testing
$year = $_POST['year'] ?? 2024; // Default para testing

    $table_name = "";
	$product = "";
	$price = "";
	$qtty = "";
	$letter = 0;
	
	switch($date)
	{
		case 1:
			$query = "SELECT *, DATE_FORMAT(inv_date,'%d %M %Y') date FROM invoice WHERE inv_date BETWEEN CAST('" . $year . "-01-01' AS DATE) AND CAST('" . $year . "-03-31' AS DATE) ORDER BY id"; // Para el 1º Trimestre desde el 1/1 al 31/3
		break;
		case 2:
			$query = "SELECT *, DATE_FORMAT(inv_date,'%d %M %Y') date FROM invoice WHERE inv_date BETWEEN CAST('" . $year . "-04-01' AS DATE) AND CAST('" . $year . "-06-30' AS DATE) ORDER BY id"; // Para el 2º Trimestre desde el 1/4 al 30/6
		break;
		case 3:
			$query = "SELECT *, DATE_FORMAT(inv_date,'%d %M %Y') date FROM invoice WHERE inv_date BETWEEN CAST('" . $year . "-07-01' AS DATE) AND CAST('" . $year . "-09-30' AS DATE) ORDER BY id"; // Para el 3º Trimestre desde el 1/7 al 30/9
		break;
		default:
			$query = "SELECT *, DATE_FORMAT(inv_date,'%d %M %Y') date FROM invoice WHERE inv_date BETWEEN CAST('" . $year . "-10-01' AS DATE) AND CAST('" . $year . "-12-31' AS DATE) ORDER BY id"; // Para el 4º Trimestre desde el 1/10 al 31/12
		break;
	}
	
	$stmt = $conn->prepare("SET lc_time_names = 'es_ES'");
	$stmt->execute();
	
	$statement = $conn->prepare($query); // Preparo la consulta para obtener todos los datos de la tabla de facturas (invoice), del trimestre seleccionado..
	
	$statement->execute(); // Ejecuto la consulta.
	
	$result = $statement->fetchAll(); // Asigno todos los resultados a la variable $result.

$title = "Informe de Facturas - Trimestre " . $date . " de " . $year;
include "includes/header.php";

// Calcular totales para el resumen
$totalFacturas = count($result);
$totalVentas = 0;
$totalBaseImponible = 0;
$totalIVA = 0;

foreach($result as $row) {
    $totalVentas += (float)$row["total"];
    $totalBaseImponible += (float)$row["total"] * 100 / 110;
    $totalIVA += (float)$row["total"] * 100 / 110 * 0.1;
}

// Nombres de trimestres para mostrar
$trimestres = [
    1 => "Primer Trimestre (Enero - Marzo)",
    2 => "Segundo Trimestre (Abril - Junio)", 
    3 => "Tercer Trimestre (Julio - Septiembre)",
    4 => "Cuarto Trimestre (Octubre - Diciembre)"
];
?>

<style>
.export-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    color: white;
    padding: 2rem;
    margin-bottom: 2rem;
}

.summary-card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
}

.summary-card:hover {
    transform: translateY(-2px);
}

.table-container {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.invoice-table {
    margin: 0;
    font-size: 0.9rem;
}

.invoice-table th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    border: none;
    padding: 1rem 0.75rem;
    text-align: center;
    white-space: nowrap;
}

.invoice-table td {
    padding: 0.75rem;
    border-bottom: 1px solid #e9ecef;
    text-align: center;
    vertical-align: middle;
}

.invoice-table tbody tr:hover {
    background-color: #f8f9fa;
}

.export-controls {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

@media (max-width: 768px) {
    .invoice-table {
        font-size: 0.8rem;
    }
    
    .invoice-table th,
    .invoice-table td {
        padding: 0.5rem 0.25rem;
    }
}
</style>

<main class="container-fluid py-4">
    <!-- Header del informe -->
    <div class="export-header text-center">
        <h1 class="display-6 fw-bold mb-3">
            <i class="bi bi-file-earmark-spreadsheet me-3"></i>
            Informe de Facturas
        </h1>
        <h2 class="h4 mb-2"><?php echo $trimestres[$date]; ?></h2>
        <p class="lead mb-0">Año <?php echo $year; ?> • XXXXX - N.I.F. 20-42000000-3</p>
    </div>

    <!-- Resumen ejecutivo -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card summary-card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-receipt display-6 text-primary mb-3"></i>
                    <h3 class="text-primary fw-bold"><?php echo number_format($totalFacturas); ?></h3>
                    <p class="text-muted mb-0">Total de Facturas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card summary-card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-calculator display-6 text-info mb-3"></i>
                    <h3 class="text-info fw-bold">$<?php echo number_format($totalBaseImponible, 2, ',', '.'); ?></h3>
                    <p class="text-muted mb-0">Base Imponible</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card summary-card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-percent display-6 text-warning mb-3"></i>
                    <h3 class="text-warning fw-bold">$<?php echo number_format($totalIVA, 2, ',', '.'); ?></h3>
                    <p class="text-muted mb-0">I.V.A. (10%)</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card summary-card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cash-stack display-6 text-success mb-3"></i>
                    <h3 class="text-success fw-bold">$<?php echo number_format($totalVentas, 2, ',', '.'); ?></h3>
                    <p class="text-muted mb-0">Total + I.V.A.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Controles de exportación -->
    <div class="export-controls">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-2">
                    <i class="bi bi-download me-2"></i>
                    Exportar a Excel o CSV
                </h4>
                <p class="text-muted mb-0">Descarga el informe completo con todos los detalles de las facturas</p>
            </div>
            <div class="col-md-4">
                <form method="post" class="d-flex gap-2">
                    <input type="hidden" name="date" value="<?php echo $date; ?>">
                    <input type="hidden" name="year" value="<?php echo $year; ?>">
                    <select name="file_type" class="form-select">
                        <option value="Xlsx">📊 Excel (XLSX)</option>
                        <option value="Csv">📄 CSV</option>
                    </select>
                    <button type="submit" name="export" class="btn btn-primary btn-lg">
                        <i class="bi bi-download me-2"></i>
                        Descargar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabla de facturas -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table invoice-table mb-0">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Mesa</th>
                        <th>Cliente</th>
                        <th>Camarero</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cant.</th>
                        <th>Hora</th>
                        <th>Fecha</th>
                        <th>Base Imp.</th>
                        <th>I.V.A.</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (empty($result)) {
                        echo '<tr><td colspan="12" class="text-center py-4">
                            <i class="bi bi-inbox display-6 text-muted d-block mb-3"></i>
                            <h5 class="text-muted">No se encontraron facturas</h5>
                            <p class="text-muted">No hay datos para el período seleccionado</p>
                        </td></tr>';
                    } else {
                        // Declarar variables globales para la visualización HTML
                        global $table_name, $client, $wait, $price, $partial, $product, $qtty;
                        
                        foreach($result as $row) {
                            result($conn, $row, 1, 0);
                            
                            $baseImponible = (float)$row["total"] * 100 / 110;
                            $iva = $baseImponible * 0.1;
                            $total = (float)$row["total"];
                            
                            echo '<tr>
                                <td><span class="badge bg-primary">' . $row["id"] . '</span></td>
                                <td><strong>' . htmlspecialchars($table_name) . '</strong></td>
                                <td>' . htmlspecialchars($client) . '</td>
                                <td>' . htmlspecialchars($wait) . '</td>
                                <td class="text-start">' . htmlspecialchars($product) . '</td>
                                <td class="text-end"><strong>$' . htmlspecialchars($price) . '</strong></td>
                                <td><span class="badge bg-secondary">' . htmlspecialchars($qtty) . '</span></td>
                                <td>' . $row["inv_time"] . '</td>
                                <td>' . date('d/m/Y', strtotime($row["inv_date"])) . '</td>
                                <td class="text-end">$' . number_format($baseImponible, 2, ',', '.') . '</td>
                                <td class="text-end">$' . number_format($iva, 2, ',', '.') . '</td>
                                <td class="text-end"><strong>$' . number_format($total, 2, ',', '.') . '</strong></td>
                            </tr>';
                            
                            // Resetear variables
                            $table_name = "";
                            $product = "";
                            $price = "";
                            $qtty = "";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pie de página con acciones -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <h6 class="text-muted mb-2">
                        <i class="bi bi-info-circle me-2"></i>
                        Información del Informe
                    </h6>
                    <p class="mb-1"><strong>Período:</strong> <?php echo $trimestres[$date] . " de " . $year; ?></p>
                    <p class="mb-1"><strong>Generado:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
                    <p class="mb-0"><strong>Sistema:</strong> Facturación XXXXX</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 d-flex align-items-center">
            <button onclick="window.close()" class="btn btn-outline-danger btn-lg w-100">
                <i class="bi bi-x-circle me-2"></i>
                Cerrar Ventana
            </button>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Export.php JavaScript cargado');
    
    // Auto-imprimir si viene desde un link directo de impresión
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('print') === 'true') {
        window.print();
    }
    
    // SIMPLIFICAR: Solo verificar datos antes de envío, SIN interferir con la descarga
    const exportForm = document.querySelector('form[method="post"]');
    if (exportForm) {
        exportForm.addEventListener('submit', function(e) {
            // Verificar que se hayan seleccionado datos
            const hasData = <?php echo count($result) > 0 ? 'true' : 'false'; ?>;
            if (!hasData) {
                e.preventDefault();
                alert('No hay datos para exportar en el período seleccionado.');
                return false;
            }
            
            console.log('📤 Formulario de exportación enviado');
            console.log('📋 Datos:', {
                date: this.querySelector('[name="date"]').value,
                year: this.querySelector('[name="year"]').value,
                file_type: this.querySelector('[name="file_type"]').value
            });
            
            // NO modificar el botón ni crear elementos que interfieran
            // Dejar que el navegador maneje la descarga naturalmente
        });
    }
});
</script>
<?php
include "includes/footer.html";
?>