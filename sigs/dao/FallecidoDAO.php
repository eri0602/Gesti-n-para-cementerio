<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Fallecido.php';

class FallecidoDAO {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function existeDNI($dni) {
        $query = "SELECT id_fallecido FROM fallecido WHERE dni = :dni LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":dni", $dni);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>
