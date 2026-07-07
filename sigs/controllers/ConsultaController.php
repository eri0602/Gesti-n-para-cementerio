<?php
require_once __DIR__ . '/../dao/ExpedienteDAO.php';

class ConsultaController {
    private $expedienteDAO;

    public function __construct() {
        $this->expedienteDAO = new ExpedienteDAO();
    }

    public function buscar($filtros) {
        return $this->expedienteDAO->buscar($filtros);
    }
    
    public function obtenerDetalle($id) {
        return $this->expedienteDAO->obtenerDetalleCompletoPorId($id);
    }
}
?>
