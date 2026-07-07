<?php
require_once __DIR__ . '/../config/database.php';

class SolicitudDAO {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // El registro de la solicitud se realizará de manera transaccional 
    // en ExpedienteDAO como indica el flujo de ECU-04 y RF08.
}
?>
