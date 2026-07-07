<?php
class Expediente {
    public $id_expediente;
    public $numero_expediente;
    public $id_fallecido;
    public $id_solicitud;
    public $fecha_registro;

    public function __construct($id_expediente = null, $numero_expediente = null, $id_fallecido = null, $id_solicitud = null, $fecha_registro = null) {
        $this->id_expediente = $id_expediente;
        $this->numero_expediente = $numero_expediente;
        $this->id_fallecido = $id_fallecido;
        $this->id_solicitud = $id_solicitud;
        $this->fecha_registro = $fecha_registro;
    }
}
?>
