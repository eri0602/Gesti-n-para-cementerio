<?php
class ReporteEstadistico {
    public $id_reporte;
    public $tipo_reporte;
    public $fecha_generacion;
    public $generado_por;

    public function __construct($id_reporte = null, $tipo_reporte = null, $fecha_generacion = null, $generado_por = null) {
        $this->id_reporte = $id_reporte;
        $this->tipo_reporte = $tipo_reporte;
        $this->fecha_generacion = $fecha_generacion;
        $this->generado_por = $generado_por;
    }
}
?>
