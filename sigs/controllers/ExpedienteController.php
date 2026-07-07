<?php
require_once __DIR__ . '/../dao/ExpedienteDAO.php';
require_once __DIR__ . '/../dao/FallecidoDAO.php';
require_once __DIR__ . '/../models/Fallecido.php';
require_once __DIR__ . '/../models/SolicitudSepultura.php';
require_once __DIR__ . '/../models/Expediente.php';

class ExpedienteController {
    private $expedienteDAO;
    private $fallecidoDAO;

    public function __construct() {
        $this->expedienteDAO = new ExpedienteDAO();
        $this->fallecidoDAO = new FallecidoDAO();
    }

    public function registrar($datos) {
        $errores = $this->validarDatos($datos);
        if (!empty($errores)) {
            return ['status' => 'error', 'mensajes' => $errores];
        }

        if ($this->fallecidoDAO->existeDNI($datos['dni'])) {
            return ['status' => 'duplicado', 'mensajes' => ['Ya existe un expediente registrado con este DNI. Verifique con el Técnico Informático si considera que se trata de un error.']];
        }

        if ($this->expedienteDAO->existeNumeroExpediente($datos['numero_expediente'])) {
            return ['status' => 'duplicado', 'mensajes' => ['Ya existe un expediente registrado con este número de expediente.']];
        }

        $fallecido = new Fallecido(
            null, 
            trim($datos['dni']), 
            trim($datos['nombres']), 
            trim($datos['apellidos']), 
            $datos['fecha_nacimiento'], 
            $datos['fecha_fallecimiento'], 
            $this->calcularEdad($datos['fecha_nacimiento'], $datos['fecha_fallecimiento']), 
            $datos['sexo']
        );

        $solicitud = new SolicitudSepultura(
            null, 
            trim($datos['nombre_deudo']), 
            trim($datos['dni_deudo']), 
            trim($datos['parentesco']), 
            date('Y-m-d')
        );

        $expediente = new Expediente(
            null, 
            trim($datos['numero_expediente']), 
            null, 
            null, 
            date('Y-m-d')
        );

        try {
            $idExpediente = $this->expedienteDAO->registrar($fallecido, $solicitud, $expediente);
            return ['status' => 'exito', 'id_expediente' => $idExpediente, 'numero_expediente' => $expediente->numero_expediente];
        } catch (Exception $e) {
            return ['status' => 'error', 'mensajes' => ['Ocurrió un error al registrar el expediente: ' . $e->getMessage()]];
        }
    }

    private function validarDatos($datos) {
        $errores = [];
        
        if (empty(trim($datos['nombres'])) || !preg_match('/^[A-Za-zÁÉÍÓÚÑáéíóúñ ]+$/', $datos['nombres'])) {
            $errores[] = "Nombres inválidos.";
        }
        if (empty(trim($datos['apellidos'])) || !preg_match('/^[A-Za-zÁÉÍÓÚÑáéíóúñ ]+$/', $datos['apellidos'])) {
            $errores[] = "Apellidos inválidos.";
        }
        if (!preg_match('/^\d{8}$/', $datos['dni'])) {
            $errores[] = "El DNI del fallecido debe tener 8 dígitos.";
        }
        if (empty($datos['fecha_nacimiento']) || empty($datos['fecha_fallecimiento'])) {
            $errores[] = "Las fechas son obligatorias.";
        } elseif ($datos['fecha_fallecimiento'] < $datos['fecha_nacimiento']) {
            $errores[] = "La fecha de fallecimiento no puede ser anterior a la de nacimiento.";
        }
        if (empty($datos['sexo']) || !in_array($datos['sexo'], ['Masculino', 'Femenino'])) {
            $errores[] = "Debe seleccionar un sexo válido.";
        }
        if (empty(trim($datos['numero_expediente']))) {
            $errores[] = "El número de expediente es obligatorio.";
        }
        
        return $errores;
    }

    private function calcularEdad($nacimiento, $fallecimiento) {
        $nac = new DateTime($nacimiento);
        $fall = new DateTime($fallecimiento);
        return $fall->diff($nac)->y;
    }
}
?>
