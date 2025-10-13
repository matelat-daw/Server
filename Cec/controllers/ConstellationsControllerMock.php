<?php
/**
 * ConstellationsController con datos MOCK para pruebas
 * Usar cuando no hay conexión a base de datos
 */
require_once 'BaseController.php';

class ConstellationsControllerMock extends BaseController {
    private $mockData;
    
    public function __construct() {
        parent::__construct();
        $this->initializeMockData();
    }
    
    private function initializeMockData() {
        $this->mockData = [
            [
                'id' => 1,
                'code' => 'And',
                'latin_name' => 'Andromeda',
                'english_name' => 'Andromeda',
                'spanish_name' => 'Andrómeda',
                'mythology' => 'Princesa etíope en la mitología griega',
                'area_degrees' => 722.28,
                'declination' => 37.0,
                'celestial_zone' => 'Norte',
                'ecliptic_zone' => 'No',
                'brightest_star' => 'Alpheratz',
                'discovery' => 'Antiguo',
                'image_name' => 'andromeda.jpg',
                'image_url' => '/assets/images/constellations/andromeda.jpg'
            ],
            [
                'id' => 2,
                'code' => 'Aqr',
                'latin_name' => 'Aquarius',
                'english_name' => 'Aquarius',
                'spanish_name' => 'Acuario',
                'mythology' => 'El aguador en la mitología griega',
                'area_degrees' => 979.85,
                'declination' => -15.0,
                'celestial_zone' => 'Ecuatorial',
                'ecliptic_zone' => 'Sí',
                'brightest_star' => 'Sadalsuud',
                'discovery' => 'Antiguo',
                'image_name' => 'aquarius.jpg',
                'image_url' => '/assets/images/constellations/aquarius.jpg'
            ],
            [
                'id' => 3,
                'code' => 'Aql',
                'latin_name' => 'Aquila',
                'english_name' => 'Aquila',
                'spanish_name' => 'Águila',
                'mythology' => 'El águila de Zeus en la mitología griega',
                'area_degrees' => 652.47,
                'declination' => 5.0,
                'celestial_zone' => 'Ecuatorial',
                'ecliptic_zone' => 'No',
                'brightest_star' => 'Altair',
                'discovery' => 'Antiguo',
                'image_name' => 'aquila.jpg',
                'image_url' => '/assets/images/constellations/aquila.jpg'
            ],
            [
                'id' => 4,
                'code' => 'Ari',
                'latin_name' => 'Aries',
                'english_name' => 'Aries',
                'spanish_name' => 'Aries',
                'mythology' => 'El carnero del vellocino de oro',
                'area_degrees' => 441.39,
                'declination' => 20.0,
                'celestial_zone' => 'Norte',
                'ecliptic_zone' => 'Sí',
                'brightest_star' => 'Hamal',
                'discovery' => 'Antiguo',
                'image_name' => 'aries.jpg',
                'image_url' => '/assets/images/constellations/aries.jpg'
            ],
            [
                'id' => 5,
                'code' => 'Aur',
                'latin_name' => 'Auriga',
                'english_name' => 'Auriga',
                'spanish_name' => 'Auriga',
                'mythology' => 'El cochero en la mitología griega',
                'area_degrees' => 657.44,
                'declination' => 42.0,
                'celestial_zone' => 'Norte',
                'ecliptic_zone' => 'No',
                'brightest_star' => 'Capella',
                'discovery' => 'Antiguo',
                'image_name' => 'auriga.jpg',
                'image_url' => '/assets/images/constellations/auriga.jpg'
            ],
            [
                'id' => 6,
                'code' => 'Cnc',
                'latin_name' => 'Cancer',
                'english_name' => 'Cancer',
                'spanish_name' => 'Cáncer',
                'mythology' => 'El cangrejo que atacó a Hércules',
                'area_degrees' => 505.87,
                'declination' => 20.0,
                'celestial_zone' => 'Norte',
                'ecliptic_zone' => 'Sí',
                'brightest_star' => 'Altarf',
                'discovery' => 'Antiguo',
                'image_name' => 'cancer.jpg',
                'image_url' => '/assets/images/constellations/cancer.jpg'
            ],
            [
                'id' => 7,
                'code' => 'CMa',
                'latin_name' => 'Canis Major',
                'english_name' => 'Canis Major',
                'spanish_name' => 'Can Mayor',
                'mythology' => 'Uno de los perros de Orión',
                'area_degrees' => 380.12,
                'declination' => -20.0,
                'celestial_zone' => 'Sur',
                'ecliptic_zone' => 'No',
                'brightest_star' => 'Sirius',
                'discovery' => 'Antiguo',
                'image_name' => 'canis_major.jpg',
                'image_url' => '/assets/images/constellations/canis_major.jpg'
            ],
            [
                'id' => 8,
                'code' => 'Cap',
                'latin_name' => 'Capricornus',
                'english_name' => 'Capricornus',
                'spanish_name' => 'Capricornio',
                'mythology' => 'La cabra marina en la mitología griega',
                'area_degrees' => 413.95,
                'declination' => -20.0,
                'celestial_zone' => 'Sur',
                'ecliptic_zone' => 'Sí',
                'brightest_star' => 'Deneb Algedi',
                'discovery' => 'Antiguo',
                'image_name' => 'capricornus.jpg',
                'image_url' => '/assets/images/constellations/capricornus.jpg'
            ],
            [
                'id' => 9,
                'code' => 'Gem',
                'latin_name' => 'Gemini',
                'english_name' => 'Gemini',
                'spanish_name' => 'Géminis',
                'mythology' => 'Los gemelos Cástor y Pólux',
                'area_degrees' => 513.76,
                'declination' => 20.0,
                'celestial_zone' => 'Norte',
                'ecliptic_zone' => 'Sí',
                'brightest_star' => 'Pollux',
                'discovery' => 'Antiguo',
                'image_name' => 'gemini.jpg',
                'image_url' => '/assets/images/constellations/gemini.jpg'
            ],
            [
                'id' => 10,
                'code' => 'Leo',
                'latin_name' => 'Leo',
                'english_name' => 'Leo',
                'spanish_name' => 'Leo',
                'mythology' => 'El león de Nemea vencido por Hércules',
                'area_degrees' => 946.96,
                'declination' => 15.0,
                'celestial_zone' => 'Norte',
                'ecliptic_zone' => 'Sí',
                'brightest_star' => 'Regulus',
                'discovery' => 'Antiguo',
                'image_name' => 'leo.jpg',
                'image_url' => '/assets/images/constellations/leo.jpg'
            ]
        ];
    }
    
    /**
     * GET /api/Constellations
     * Obtiene todas las constelaciones (datos mock)
     */
    public function getAll($params = []) {
        try {
            error_log("ConstellationsControllerMock::getAll - Devolviendo datos mock");
            
            // Configurar headers de respuesta
            header('Content-Type: application/json; charset=UTF-8');
            
            // Devolver los datos mock
            echo json_encode($this->mockData);
            
        } catch (Exception $e) {
            error_log("Error en getAll constellations mock: " . $e->getMessage());
            $this->sendResponse(500, "Error interno del servidor: " . $e->getMessage(), null, false);
        }
    }
    
    /**
     * GET /api/Constellations/{id}
     * Obtiene una constelación específica por ID (datos mock)
     */
    public function getById($params = []) {
        try {
            if (!isset($params['id']) || !is_numeric($params['id'])) {
                $this->sendResponse(400, "ID de constelación requerido y debe ser numérico", null, false);
                return;
            }
            
            $id = intval($params['id']);
            $constellation = null;
            
            foreach ($this->mockData as $item) {
                if ($item['id'] == $id) {
                    $constellation = $item;
                    break;
                }
            }
            
            if (!$constellation) {
                $this->sendResponse(404, "Constelación no encontrada", null, false);
                return;
            }
            
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode($constellation);
            
        } catch (Exception $e) {
            error_log("Error en getById constellations mock: " . $e->getMessage());
            $this->sendResponse(500, "Error interno del servidor: " . $e->getMessage(), null, false);
        }
    }
}
?>
