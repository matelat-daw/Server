<?php
// Controlador de Contratos
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/jwt.php';
require_once __DIR__ . '/../models/Contract.php';
require_once __DIR__ . '/../vendor/autoload.php';

class ContractController {
    private $db;
    private $contract;

    public function __construct() {
        global $conn;
        $this->db = $conn;
        $this->contract = new Contract($this->db);
    }

    /**
     * Obtener todos los contratos para el administrador (separados por origen)
     */
    public function adminContracts() {
        $token = $this->getTokenFromRequest();

        if (!$token) {
            return $this->sendResponse(401, false, "No autorizado");
        }

        $decoded = JWT::decode($token);

        if (!$decoded) {
            return $this->sendResponse(401, false, "Token inválido");
        }

        if (!in_array('admin', $decoded['roles'])) {
            return $this->sendResponse(403, false, "Acceso denegado. Se requiere rol de administrador");
        }

        // Contratos gestionados por vendedores
        $stmtSeller = $this->contract->getAllSellerContracts();
        $sellerContracts = [];
        while ($row = $stmtSeller->fetch(PDO::FETCH_ASSOC)) {
            $sellerContracts[] = $row;
        }

        // Contratos realizados directamente por usuarios
        $stmtDirect = $this->contract->getAllDirectContracts();
        $directContracts = [];
        while ($row = $stmtDirect->fetch(PDO::FETCH_ASSOC)) {
            $directContracts[] = $row;
        }

        return $this->sendResponse(200, true, "Contratos obtenidos exitosamente", [
            'seller_contracts' => $sellerContracts,
            'direct_contracts' => $directContracts
        ]);
    }

    /**
     * Listar todos los contratos
     */
    public function index() {
        $stmt = $this->contract->readAll();
        $contracts = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $contracts[] = $row;
        }

        return $this->sendResponse(200, true, "Contratos obtenidos exitosamente", $contracts);
    }

    /**
     * Obtener contratos del usuario autenticado
     */
    public function myContracts() {
        $token = $this->getTokenFromRequest();
        
        if (!$token) {
            return $this->sendResponse(401, false, "No autorizado");
        }

        $decoded = JWT::decode($token);
        
        if (!$decoded) {
            return $this->sendResponse(401, false, "Token inválido");
        }

        $user_id = $decoded['user_id'];
        $roles = $decoded['roles'];

        // Si es vendedor, obtener contratos como vendedor
        if (in_array('seller', $roles)) {
            $stmt = $this->contract->getContractsBySeller($user_id);
        } else {
            // Si es cliente, obtener contratos como cliente
            $stmt = $this->contract->getContractsByClient($user_id);
        }

        $contracts = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $contracts[] = $row;
        }

        return $this->sendResponse(200, true, "Contratos obtenidos exitosamente", $contracts);
    }

    /**
     * Obtener estadísticas de vendedor
     */
    public function sellerStats() {
        $token = $this->getTokenFromRequest();
        
        if (!$token) {
            return $this->sendResponse(401, false, "No autorizado");
        }

        $decoded = JWT::decode($token);
        
        if (!$decoded) {
            return $this->sendResponse(401, false, "Token inválido");
        }

        // Verificar que sea vendedor
        if (!in_array('seller', $decoded['roles'])) {
            return $this->sendResponse(403, false, "No tienes permisos de vendedor");
        }

        $user_id = $decoded['user_id'];
        $stats = $this->contract->getSellerStats($user_id);

        return $this->sendResponse(200, true, "Estadísticas obtenidas exitosamente", $stats);
    }

    /**
     * Obtener un contrato específico
     */
    public function show($id) {
        $this->contract->id = $id;

        if ($this->contract->readOne()) {
            $contract_data = [
                'id' => $this->contract->id,
                'client_id' => $this->contract->client_id,
                'seller_id' => $this->contract->seller_id,
                'plan_id' => $this->contract->plan_id,
                'start_date' => $this->contract->start_date,
                'end_date' => $this->contract->end_date,
                'status' => $this->contract->status,
                'total_amount' => $this->contract->total_amount,
                'commission_amount' => $this->contract->commission_amount,
                'notes' => $this->contract->notes,
                'created_at' => $this->contract->created_at,
                'updated_at' => $this->contract->updated_at
            ];

            return $this->sendResponse(200, true, "Contrato encontrado", $contract_data);
        }

        return $this->sendResponse(404, false, "Contrato no encontrado");
    }

    /**
     * Crear nuevo contrato
     */
    public function store($data) {
        // Validar datos requeridos
        if (empty($data['client_id']) || empty($data['plan_id']) || empty($data['start_date'])) {
            return $this->sendResponse(400, false, "Cliente, plan y fecha de inicio son requeridos");
        }

        $this->contract->client_id = $data['client_id'];
        $this->contract->plan_id = $data['plan_id'];
        
        // Si no se proporciona seller_id, obtenerlo del plan
        if (!isset($data['seller_id']) || empty($data['seller_id'])) {
            // Obtener el plan para conseguir el seller_id
            require_once __DIR__ . '/../models/Plan.php';
            $plan = new Plan($this->db);
            $plan->id = $data['plan_id'];
            if ($plan->readOne() && !empty($plan->seller_id)) {
                $this->contract->seller_id = $plan->seller_id;
            } else {
                $this->contract->seller_id = null;
            }
        } else {
            $this->contract->seller_id = $data['seller_id'];
        }
        
        $this->contract->start_date = $data['start_date'];
        $this->contract->end_date = isset($data['end_date']) ? $data['end_date'] : null;
        $this->contract->status = isset($data['status']) ? $data['status'] : 'pending';
        $this->contract->total_amount = isset($data['total_amount']) ? $data['total_amount'] : 0.00;
        $this->contract->commission_amount = isset($data['commission_amount']) ? $data['commission_amount'] : 0.00;
        $this->contract->notes = isset($data['notes']) ? $data['notes'] : null;

        if ($this->contract->create()) {
            $contract_data = [
                'id' => $this->contract->id,
                'seller_id' => $this->contract->seller_id,
                'status' => $this->contract->status
            ];

            return $this->sendResponse(201, true, "Contrato creado exitosamente", $contract_data);
        }

        return $this->sendResponse(500, false, "Error al crear contrato");
    }

    /**
     * Actualizar contrato
     */
    public function update($data, $id) {
        $this->contract->id = $id;

        if (!$this->contract->readOne()) {
            return $this->sendResponse(404, false, "Contrato no encontrado");
        }

        // Actualizar campos
        if (isset($data['status'])) {
            $this->contract->status = $data['status'];
        }
        if (isset($data['end_date'])) {
            $this->contract->end_date = $data['end_date'];
        }
        if (isset($data['total_amount'])) {
            $this->contract->total_amount = $data['total_amount'];
        }
        if (isset($data['commission_amount'])) {
            $this->contract->commission_amount = $data['commission_amount'];
        }
        if (isset($data['notes'])) {
            $this->contract->notes = $data['notes'];
        }

        if ($this->contract->update()) {
            return $this->sendResponse(200, true, "Contrato actualizado exitosamente");
        }

        return $this->sendResponse(500, false, "Error al actualizar contrato");
    }

    /**
     * Eliminar contrato
     */
    public function destroy($id) {
        $this->contract->id = $id;

        if (!$this->contract->readOne()) {
            return $this->sendResponse(404, false, "Contrato no encontrado");
        }

        if ($this->contract->delete()) {
            return $this->sendResponse(200, true, "Contrato eliminado exitosamente");
        }

        return $this->sendResponse(500, false, "Error al eliminar contrato");
    }

    /**
     * Obtener token del request
     */
    private function getTokenFromRequest() {
        // Intentar obtener de cookie primero
        if (isset($_COOKIE['auth_token'])) {
            return $_COOKIE['auth_token'];
        }

        // Intentar obtener de header Authorization
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        if (isset($headers['Authorization'])) {
            if (preg_match('/Bearer\s+(.*)$/i', $headers['Authorization'], $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Exportar contratos a Excel
     */
    public function exportToExcel() {
        $token = $this->getTokenFromRequest();

        if (!$token) {
            return $this->sendResponse(401, false, "No autorizado");
        }

        $decoded = JWT::decode($token);

        if (!$decoded) {
            return $this->sendResponse(401, false, "Token inválido");
        }

        if (!in_array('admin', $decoded['roles'])) {
            return $this->sendResponse(403, false, "Acceso denegado. Se requiere rol de administrador");
        }

        try {
            // Obtener contratos
            $stmtSeller = $this->contract->getAllSellerContracts();
            $sellerContracts = [];
            while ($row = $stmtSeller->fetch(PDO::FETCH_ASSOC)) {
                $sellerContracts[] = $row;
            }

            $stmtDirect = $this->contract->getAllDirectContracts();
            $directContracts = [];
            while ($row = $stmtDirect->fetch(PDO::FETCH_ASSOC)) {
                $directContracts[] = $row;
            }

            // Crear nuevo documento Excel
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Contratos');

            // Definir estilos
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '366092']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'border' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
            ];

            // Encabezados
            $headers = ['ID', 'Cliente', 'Email Cliente', 'Vendedor', 'Email Vendedor', 'Plan', 'Proveedor', 
                       'Fecha Inicio', 'Fecha Fin', 'Estado', 'Monto Total', 'Comisión', 'Notas', 'Creado'];
            
            $col = 1;
            foreach ($headers as $header) {
                $cell = $sheet->getCellByColumnAndRow($col, 1);
                $cell->setValue($header);
                $cell->getStyle()->applyFromArray($headerStyle);
                $col++;
            }

            // Datos de contratos por vendedor
            $row = 2;
            $sheet->insertNewRowBefore($row, 1);
            $sheet->getCellByColumnAndRow(1, $row)->setValue('CONTRATOS POR VENDEDOR');
            $sheet->getCellByColumnAndRow(1, $row)->getStyle()->getFont()->setBold(true);
            $sheet->getCellByColumnAndRow(1, $row)->getStyle()->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $sheet->getCellByColumnAndRow(1, $row)->getStyle()->getFill()->setStartColor(new \PhpOffice\PhpSpreadsheet\Style\Color('E7E6E6'));
            $row += 2;

            foreach ($sellerContracts as $contract) {
                $col = 1;
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['id'] ?? '');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['client_name'] ?? '');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['client_email'] ?? '');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['seller_name'] ?? '');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['seller_email'] ?? '');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['plan_name'] ?? '');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['provider_name'] ?? '');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['start_date'] ?? '');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['end_date'] ?? '');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['status'] ?? '');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['total_amount'] ?? '0.00');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['commission_amount'] ?? '0.00');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['notes'] ?? '');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['created_at'] ?? '');
                $row++;
            }

            // Datos de contratos directos
            $row += 2;
            $sheet->insertNewRowBefore($row, 1);
            $sheet->getCellByColumnAndRow(1, $row)->setValue('CONTRATOS DIRECTOS');
            $sheet->getCellByColumnAndRow(1, $row)->getStyle()->getFont()->setBold(true);
            $sheet->getCellByColumnAndRow(1, $row)->getStyle()->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $sheet->getCellByColumnAndRow(1, $row)->getStyle()->getFill()->setStartColor(new \PhpOffice\PhpSpreadsheet\Style\Color('E7E6E6'));
            $row += 2;

            foreach ($directContracts as $contract) {
                $col = 1;
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['id'] ?? '');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['client_name'] ?? '');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['client_email'] ?? '');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['seller_name'] ?? '');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['seller_email'] ?? '');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['plan_name'] ?? '');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['provider_name'] ?? '');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['start_date'] ?? '');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['end_date'] ?? '');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['status'] ?? '');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['total_amount'] ?? '0.00');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['commission_amount'] ?? '0.00');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['notes'] ?? '');
                $sheet->getCellByColumnAndRow($col++, $row)->setValue($contract['created_at'] ?? '');
                $row++;
            }

            // Ajustar ancho de columnas
            foreach (range('A', 'N') as $column) {
                $spreadsheet->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
            }

            // Crear escritor
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = 'Contratos_' . date('Y-m-d_His') . '.xlsx';

            // Enviar como descarga
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');

            $writer->save('php://output');
            exit;

        } catch (Exception $e) {
            return $this->sendResponse(500, false, "Error al exportar a Excel: " . $e->getMessage());
        }
    }

    /**
     * Exportar contratos a PDF
     */
    public function exportToPdf() {
        $token = $this->getTokenFromRequest();

        if (!$token) {
            return $this->sendResponse(401, false, "No autorizado");
        }

        $decoded = JWT::decode($token);

        if (!$decoded) {
            return $this->sendResponse(401, false, "Token inválido");
        }

        if (!in_array('admin', $decoded['roles'])) {
            return $this->sendResponse(403, false, "Acceso denegado. Se requiere rol de administrador");
        }

        try {
            // Obtener contratos
            $stmtSeller = $this->contract->getAllSellerContracts();
            $sellerContracts = [];
            while ($row = $stmtSeller->fetch(PDO::FETCH_ASSOC)) {
                $sellerContracts[] = $row;
            }

            $stmtDirect = $this->contract->getAllDirectContracts();
            $directContracts = [];
            while ($row = $stmtDirect->fetch(PDO::FETCH_ASSOC)) {
                $directContracts[] = $row;
            }

            // Crear instancia de mPDF
            $mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);

            // Estilos CSS
            $stylesheet = '
                <style>
                    body { font-family: Arial, sans-serif; font-size: 10px; }
                    h2 { color: #366092; margin-top: 20px; margin-bottom: 10px; }
                    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                    th { background-color: #366092; color: white; padding: 8px; text-align: left; border: 1px solid #ddd; font-weight: bold; }
                    td { padding: 6px; border: 1px solid #ddd; }
                    tr:nth-child(even) { background-color: #f9f9f9; }
                    .header { text-align: center; margin-bottom: 30px; }
                    .footer { text-align: center; font-size: 9px; color: #666; margin-top: 20px; }
                </style>
            ';

            // Construir HTML
            $html = $stylesheet . '
                <div class="header">
                    <h1>Reporte de Contratos</h1>
                    <p>Generado: ' . date('d/m/Y H:i:s') . '</p>
                </div>
            ';

            // Contratos por vendedor
            $html .= '<h2>Contratos por Vendedor</h2>';
            $html .= '<table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Plan</th>
                        <th>Proveedor</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Estado</th>
                        <th>Monto</th>
                        <th>Comisión</th>
                    </tr>
                </thead>
                <tbody>';

            foreach ($sellerContracts as $contract) {
                $html .= '<tr>
                    <td>' . ($contract['id'] ?? '') . '</td>
                    <td>' . ($contract['client_name'] ?? '') . '</td>
                    <td>' . ($contract['seller_name'] ?? '') . '</td>
                    <td>' . ($contract['plan_name'] ?? '') . '</td>
                    <td>' . ($contract['provider_name'] ?? '') . '</td>
                    <td>' . ($contract['start_date'] ?? '') . '</td>
                    <td>' . ($contract['end_date'] ?? '') . '</td>
                    <td>' . ($contract['status'] ?? '') . '</td>
                    <td align="right">$' . number_format($contract['total_amount'] ?? 0, 2) . '</td>
                    <td align="right">$' . number_format($contract['commission_amount'] ?? 0, 2) . '</td>
                </tr>';
            }

            $html .= '</tbody></table>';

            // Contratos directos
            $html .= '<h2>Contratos Directos</h2>';
            $html .= '<table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Plan</th>
                        <th>Proveedor</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Estado</th>
                        <th>Monto</th>
                        <th>Comisión</th>
                    </tr>
                </thead>
                <tbody>';

            foreach ($directContracts as $contract) {
                $html .= '<tr>
                    <td>' . ($contract['id'] ?? '') . '</td>
                    <td>' . ($contract['client_name'] ?? '') . '</td>
                    <td>' . ($contract['seller_name'] ?? '') . '</td>
                    <td>' . ($contract['plan_name'] ?? '') . '</td>
                    <td>' . ($contract['provider_name'] ?? '') . '</td>
                    <td>' . ($contract['start_date'] ?? '') . '</td>
                    <td>' . ($contract['end_date'] ?? '') . '</td>
                    <td>' . ($contract['status'] ?? '') . '</td>
                    <td align="right">$' . number_format($contract['total_amount'] ?? 0, 2) . '</td>
                    <td align="right">$' . number_format($contract['commission_amount'] ?? 0, 2) . '</td>
                </tr>';
            }

            $html .= '</tbody></table>';

            $html .= '<div class="footer">
                <p>Este documento fue generado automáticamente y contiene información confidencial.</p>
            </div>';

            // Generar PDF
            $mpdf->WriteHTML($html);
            $filename = 'Contratos_' . date('Y-m-d_His') . '.pdf';

            // Descargar PDF
            $mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
            exit;

        } catch (Exception $e) {
            return $this->sendResponse(500, false, "Error al exportar a PDF: " . $e->getMessage());
        }
    }

    /**
     * Enviar respuesta JSON
     */
    private function sendResponse($code, $success, $message, $data = null) {
        http_response_code($code);
        $response = [
            'success' => $success,
            'message' => $message
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        echo json_encode($response);
    }
}
?>
