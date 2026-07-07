<?php
class SolicitudSepultura {
    public $id_solicitud;
    public $nombre_deudo;
    public $dni_deudo;
    public $parentesco;
    public $fecha_solicitud;

    public function __construct($id_solicitud = null, $nombre_deudo = null, $dni_deudo = null, $parentesco = null, $fecha_solicitud = null) {
        $this->id_solicitud = $id_solicitud;
        $this->nombre_deudo = $nombre_deudo;
        $this->dni_deudo = $dni_deudo;
        $this->parentesco = $parentesco;
        $this->fecha_solicitud = $fecha_solicitud;
    }
}
?>
