<?php
class ComprobanteRegistro {
    public $id_comprobante;
    public $id_expediente;
    public $fecha_emision;

    public function __construct($id_comprobante = null, $id_expediente = null, $fecha_emision = null) {
        $this->id_comprobante = $id_comprobante;
        $this->id_expediente = $id_expediente;
        $this->fecha_emision = $fecha_emision;
    }
}
?>
