<?php
require_once __DIR__ . '/../config/database.php';

class ReporteDAO {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function porSexo($desde, $hasta) {
        $query = "SELECT sexo, COUNT(*) AS total 
                  FROM fallecido 
                  WHERE fecha_fallecimiento BETWEEN :desde AND :hasta 
                  GROUP BY sexo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":desde", $desde);
        $stmt->bindParam(":hasta", $hasta);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function porGrupoEtario($desde, $hasta) {
        $query = "SELECT 
                    CASE 
                      WHEN edad BETWEEN 0 AND 17 THEN 'Menor de edad' 
                      WHEN edad BETWEEN 18 AND 29 THEN 'Joven adulto' 
                      WHEN edad BETWEEN 30 AND 59 THEN 'Adulto' 
                      ELSE 'Adulto mayor' 
                    END AS grupo_etario, 
                    COUNT(*) AS total 
                  FROM fallecido 
                  WHERE fecha_fallecimiento BETWEEN :desde AND :hasta 
                  GROUP BY grupo_etario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":desde", $desde);
        $stmt->bindParam(":hasta", $hasta);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function registrarGeneracion($tipo_reporte, $id_usuario) {
        $query = "INSERT INTO reporte_estadistico (tipo_reporte, fecha_generacion, generado_por) 
                  VALUES (:tipo, :fecha, :usuario)";
        $stmt = $this->conn->prepare($query);
        $fecha = date('Y-m-d');
        $stmt->bindParam(":tipo", $tipo_reporte);
        $stmt->bindParam(":fecha", $fecha);
        $stmt->bindParam(":usuario", $id_usuario);
        $stmt->execute();
    }
}
?>
