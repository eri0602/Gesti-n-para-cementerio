<?php
require_once __DIR__ . '/../config/database.php';

class ExpedienteDAO {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function existeNumeroExpediente($numero_expediente) {
        $query = "SELECT id_expediente FROM expediente WHERE numero_expediente = :numero_expediente LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":numero_expediente", $numero_expediente);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function registrar($fallecido, $solicitud, $expediente) {
        try {
            $this->conn->beginTransaction();

            // 1. Insertar Fallecido
            $queryFallecido = "INSERT INTO fallecido (dni, nombres, apellidos, fecha_nacimiento, fecha_fallecimiento, edad, sexo) 
                               VALUES (:dni, :nombres, :apellidos, :fecha_nacimiento, :fecha_fallecimiento, :edad, :sexo)";
            $stmtF = $this->conn->prepare($queryFallecido);
            $stmtF->execute([
                ':dni' => $fallecido->dni,
                ':nombres' => $fallecido->nombres,
                ':apellidos' => $fallecido->apellidos,
                ':fecha_nacimiento' => $fallecido->fecha_nacimiento,
                ':fecha_fallecimiento' => $fallecido->fecha_fallecimiento,
                ':edad' => $fallecido->edad,
                ':sexo' => $fallecido->sexo
            ]);
            $idFallecido = $this->conn->lastInsertId();

            // 2. Insertar Solicitud
            $querySolicitud = "INSERT INTO solicitud_sepultura (nombre_deudo, dni_deudo, parentesco, fecha_solicitud) 
                               VALUES (:nombre, :dni, :parentesco, :fecha)";
            $stmtS = $this->conn->prepare($querySolicitud);
            $stmtS->execute([
                ':nombre' => $solicitud->nombre_deudo,
                ':dni' => $solicitud->dni_deudo,
                ':parentesco' => $solicitud->parentesco,
                ':fecha' => $solicitud->fecha_solicitud
            ]);
            $idSolicitud = $this->conn->lastInsertId();

            // 3. Insertar Expediente
            $queryExpediente = "INSERT INTO expediente (numero_expediente, id_fallecido, id_solicitud, fecha_registro) 
                                VALUES (:numero, :id_fallecido, :id_solicitud, :fecha)";
            $stmtE = $this->conn->prepare($queryExpediente);
            $stmtE->execute([
                ':numero' => $expediente->numero_expediente,
                ':id_fallecido' => $idFallecido,
                ':id_solicitud' => $idSolicitud,
                ':fecha' => $expediente->fecha_registro
            ]);
            $idExpediente = $this->conn->lastInsertId();

            // 4. Insertar Comprobante
            $queryComprobante = "INSERT INTO comprobante_registro (id_expediente, fecha_emision) 
                                 VALUES (:id_expediente, :fecha)";
            $stmtC = $this->conn->prepare($queryComprobante);
            $stmtC->execute([
                ':id_expediente' => $idExpediente,
                ':fecha' => date('Y-m-d')
            ]);

            $this->conn->commit();
            return $idExpediente;

        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function buscar($filtros) {
        $query = "SELECT e.id_expediente, e.numero_expediente, e.fecha_registro, f.nombres, f.apellidos, f.dni, f.edad, f.sexo 
                  FROM expediente e 
                  INNER JOIN fallecido f ON e.id_fallecido = f.id_fallecido 
                  WHERE 1=1";
        
        $params = [];
        
        if (!empty($filtros['numero_expediente'])) {
            $query .= " AND e.numero_expediente = :numero_expediente";
            $params[':numero_expediente'] = $filtros['numero_expediente'];
        }
        if (!empty($filtros['dni'])) {
            $query .= " AND f.dni = :dni";
            $params[':dni'] = $filtros['dni'];
        }
        if (!empty($filtros['nombres'])) {
            $query .= " AND f.nombres LIKE :nombres";
            $params[':nombres'] = "%" . $filtros['nombres'] . "%";
        }
        if (!empty($filtros['apellidos'])) {
            $query .= " AND f.apellidos LIKE :apellidos";
            $params[':apellidos'] = "%" . $filtros['apellidos'] . "%";
        }
        if (!empty($filtros['edad'])) {
            $query .= " AND f.edad = :edad";
            $params[':edad'] = $filtros['edad'];
        }
        if (!empty($filtros['sexo'])) {
            $query .= " AND f.sexo = :sexo";
            $params[':sexo'] = $filtros['sexo'];
        }
        if (!empty($filtros['fecha_registro'])) {
            $query .= " AND e.fecha_registro = :fecha_registro";
            $params[':fecha_registro'] = $filtros['fecha_registro'];
        }

        $stmt = $this->conn->prepare($query);
        foreach($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function obtenerDetalleCompletoPorId($id_expediente) {
        $query = "SELECT e.*, f.*, s.*, c.id_comprobante, c.fecha_emision 
                  FROM expediente e 
                  INNER JOIN fallecido f ON e.id_fallecido = f.id_fallecido
                  INNER JOIN solicitud_sepultura s ON e.id_solicitud = s.id_solicitud
                  LEFT JOIN comprobante_registro c ON e.id_expediente = c.id_expediente
                  WHERE e.id_expediente = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id_expediente);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>
