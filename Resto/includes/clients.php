<?php
/**
 * Clase ClientManager - Gestión centralizada de clientes
 * 
 * Esta clase maneja todas las operaciones CRUD (Create, Read, Update, Delete)
 * para la entidad Cliente de manera segura y eficiente.
 * 
 * Características:
 * - Consultas preparadas para seguridad
 * - Validación de datos integrada
 * - Manejo de errores robusto
 * - Métodos utilitarios
 */

class ClientManager {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Obtener todos los clientes
     * @param string $orderBy Campo por el cual ordenar
     * @return array Lista de clientes
     */
    public function getAllClients($orderBy = 'name') {
        try {
            $sql = "SELECT id, name, surname1, surname2, email, phone, address 
                    FROM client ORDER BY " . $orderBy;
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener clientes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener un cliente por ID
     * @param int $id ID del cliente
     * @return array|null Datos del cliente o null si no existe
     */
    public function getClientById($id) {
        try {
            $sql = "SELECT * FROM client WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener cliente por ID: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Verificar si existe un cliente con email, teléfono o DNI específicos
     * @param string $email Email del cliente
     * @param string $phone Teléfono del cliente
     * @param string $dni DNI del cliente (opcional)
     * @param int $excludeId ID a excluir de la búsqueda (para modificaciones)
     * @return bool True si existe duplicado
     */
    public function checkDuplicateClient($email, $phone, $dni = null, $excludeId = null) {
        try {
            $sql = "SELECT id FROM client WHERE (email = ? OR phone = ?)";
            $params = [$email, $phone];
            
            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar duplicados: " . $e->getMessage());
            return true; // En caso de error, asumimos que hay duplicado para mayor seguridad
        }
    }
    
    /**
     * Agregar un nuevo cliente
     * @param array $data Datos del cliente
     * @return array Resultado de la operación
     */
    public function addClient($data) {
        try {
            // Validar datos requeridos
            $requiredFields = ['name', 'surname1', 'email', 'pass', 'phone', 'address'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return ['success' => false, 'message' => "El campo {$field} es obligatorio"];
                }
            }
            
            // Validar email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Email no válido'];
            }
            
            // Validar contraseña
            if (strlen($data['pass']) < 6) {
                return ['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres'];
            }
            
            // Verificar duplicados
            if ($this->checkDuplicateClient($data['email'], $data['phone'])) {
                return ['success' => false, 'message' => 'Ya existe un cliente con ese email o teléfono'];
            }
            
            // Insertar cliente
            $sql = "INSERT INTO client (name, surname1, surname2, email, pass, phone, address) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $hash = password_hash($data['pass'], PASSWORD_DEFAULT);
            
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                $data['name'],
                $data['surname1'],
                $data['surname2'] ?: null,
                $data['email'],
                $hash,
                $data['phone'],
                $data['address']
            ]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Cliente agregado correctamente', 'id' => $this->conn->lastInsertId()];
            } else {
                return ['success' => false, 'message' => 'Error al agregar el cliente'];
            }
            
        } catch (PDOException $e) {
            error_log("Error al agregar cliente: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error en la base de datos'];
        }
    }
    
    /**
     * Modificar un cliente existente
     * @param int $id ID del cliente
     * @param array $data Nuevos datos del cliente
     * @return array Resultado de la operación
     */
    public function updateClient($id, $data) {
        try {
            // Verificar que el cliente existe
            if (!$this->getClientById($id)) {
                return ['success' => false, 'message' => 'Cliente no encontrado'];
            }
            
            // Validar datos requeridos
            $requiredFields = ['name', 'surname1', 'email', 'phone', 'address'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return ['success' => false, 'message' => "El campo {$field} es obligatorio"];
                }
            }
            
            // Validar email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Email no válido'];
            }
            
            // Verificar duplicados (excluyendo el cliente actual)
            if ($this->checkDuplicateClient($data['email'], $data['phone'], null, $id)) {
                return ['success' => false, 'message' => 'Ya existe otro cliente con ese email o teléfono'];
            }
            
            // Preparar la consulta de actualización
            if (!empty($data['pass'])) {
                // Validar nueva contraseña
                if (strlen($data['pass']) < 6) {
                    return ['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres'];
                }
                
                // Actualizar con nueva contraseña
                $sql = "UPDATE client SET name = ?, surname1 = ?, surname2 = ?, email = ?, 
                        pass = ?, phone = ?, address = ? WHERE id = ?";
                $hash = password_hash($data['pass'], PASSWORD_DEFAULT);
                $params = [
                    $data['name'], $data['surname1'], $data['surname2'] ?: null, 
                    $data['email'], $hash, $data['phone'], 
                    $data['address'], $id
                ];
            } else {
                // Actualizar sin cambiar contraseña
                $sql = "UPDATE client SET name = ?, surname1 = ?, surname2 = ?, email = ?, 
                        phone = ?, address = ? WHERE id = ?";
                $params = [
                    $data['name'], $data['surname1'], $data['surname2'] ?: null, 
                    $data['email'], $data['phone'], 
                    $data['address'], $id
                ];
            }
            
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute($params);
            
            if ($result && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Cliente modificado correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se realizaron cambios en los datos'];
            }
            
        } catch (PDOException $e) {
            error_log("Error al modificar cliente: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error en la base de datos'];
        }
    }
    
    /**
     * Eliminar un cliente
     * @param int $id ID del cliente
     * @return array Resultado de la operación
     */
    public function deleteClient($id) {
        try {
            // Verificar que el cliente existe
            $client = $this->getClientById($id);
            if (!$client) {
                return ['success' => false, 'message' => 'Cliente no encontrado'];
            }
            
            // Verificar si tiene facturas asociadas
            if ($this->hasAssociatedInvoices($id)) {
                return ['success' => false, 'message' => 'No se puede eliminar el cliente porque tiene facturas asociadas'];
            }
            
            // Eliminar cliente
            $sql = "DELETE FROM client WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([$id]);
            
            if ($result && $stmt->rowCount() > 0) {
                $clientName = $client['name'] . ' ' . $client['surname1'];
                return ['success' => true, 'message' => "Cliente {$clientName} eliminado correctamente"];
            } else {
                return ['success' => false, 'message' => 'Error al eliminar el cliente'];
            }
            
        } catch (PDOException $e) {
            error_log("Error al eliminar cliente: " . $e->getMessage());
            
            // Verificar si es un error de integridad referencial
            if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                return ['success' => false, 'message' => 'No se puede eliminar el cliente porque tiene registros asociados'];
            } else {
                return ['success' => false, 'message' => 'Error en la base de datos'];
            }
        }
    }
    
    /**
     * Verificar si un cliente tiene facturas asociadas
     * @param int $clientId ID del cliente
     * @return bool True si tiene facturas
     */
    public function hasAssociatedInvoices($clientId) {
        try {
            $sql = "SELECT COUNT(*) as count FROM invoice WHERE client_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$clientId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar facturas asociadas: " . $e->getMessage());
            return true; // En caso de error, asumimos que tiene facturas para mayor seguridad
        }
    }
    
    /**
     * Buscar clientes por criterio
     * @param string $search Término de búsqueda
     * @param string $field Campo donde buscar (name, email, phone, etc.)
     * @return array Lista de clientes que coinciden
     */
    public function searchClients($search, $field = 'name') {
        try {
            $allowedFields = ['name', 'surname1', 'email', 'phone'];
            if (!in_array($field, $allowedFields)) {
                $field = 'name';
            }
            
            $sql = "SELECT id, name, surname1, surname2, email, phone, address 
                    FROM client WHERE {$field} LIKE ? ORDER BY name";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['%' . $search . '%']);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al buscar clientes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener estadísticas de clientes
     * @return array Estadísticas básicas
     */
    public function getClientStats() {
        try {
            $stats = [];
            
            // Total de clientes
            $sql = "SELECT COUNT(*) as total FROM client";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Clientes con facturas
            $sql = "SELECT COUNT(DISTINCT client_id) as count FROM invoice WHERE client_id IS NOT NULL";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $stats['con_facturas'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return [
                'total' => 0,
                'con_facturas' => 0
            ];
        }
    }
    
    /**
     * Validar datos de cliente
     * @param array $data Datos a validar
     * @param bool $isUpdate Si es una actualización (permite contraseña vacía)
     * @return array Lista de errores encontrados
     */
    public function validateClientData($data, $isUpdate = false) {
        $errors = [];
        
        // Validar nombre
        if (empty($data['name']) || strlen(trim($data['name'])) < 2) {
            $errors[] = 'El nombre debe tener al menos 2 caracteres';
        }
        
        // Validar apellido
        if (empty($data['surname1']) || strlen(trim($data['surname1'])) < 2) {
            $errors[] = 'El apellido debe tener al menos 2 caracteres';
        }
        
        // Validar email
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Debe proporcionar un email válido';
        }
        
        // Validar contraseña
        if (!$isUpdate || !empty($data['pass'])) {
            if (empty($data['pass']) || strlen($data['pass']) < 6) {
                $errors[] = 'La contraseña debe tener al menos 6 caracteres';
            }
        }
        
        // Validar teléfono
        if (empty($data['phone']) || strlen(trim($data['phone'])) < 8) {
            $errors[] = 'Debe proporcionar un número de teléfono válido';
        }
        
        // Validar dirección
        if (empty($data['address']) || strlen(trim($data['address'])) < 10) {
            $errors[] = 'La dirección debe tener al menos 10 caracteres';
        }
        
        return $errors;
    }
}
?>
