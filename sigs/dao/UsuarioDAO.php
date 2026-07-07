<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioDAO {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function obtenerPorUsuario($username) {
        $query = "SELECT * FROM usuario WHERE usuario = :usuario LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario", $username);
        $stmt->execute();

        if ($row = $stmt->fetch()) {
            return new Usuario($row['id_usuario'], $row['usuario'], $row['contrasena'], $row['rol']);
        }
        return null;
    }
}
?>
