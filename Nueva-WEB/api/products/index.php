<?php
// Endpoint legacy deshabilitado. Usa rutas del Router principal.
http_response_code(410);
header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Endpoint legacy eliminado. Usa /Nueva-WEB/api rutas principales.']);
exit;