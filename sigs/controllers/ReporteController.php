<?php
require_once __DIR__ . '/../dao/ReporteDAO.php';

class ReporteController {
    private $reporteDAO;

    public function __construct() {
        $this->reporteDAO = new ReporteDAO();
    }

    public function generarEstadisticas($desde, $hasta) {
        return [
            'por_sexo' => $this->reporteDAO->porSexo($desde, $hasta),
            'por_grupo_etario' => $this->reporteDAO->porGrupoEtario($desde, $hasta)
        ];
    }

    public function registrarGeneracion($tipo, $id_usuario) {
        $this->reporteDAO->registrarGeneracion($tipo, $id_usuario);
    }
}
?>
