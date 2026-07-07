<?php
class Fallecido {
    public $id_fallecido;
    public $dni;
    public $nombres;
    public $apellidos;
    public $fecha_nacimiento;
    public $fecha_fallecimiento;
    public $edad;
    public $sexo;

    public function __construct($id_fallecido = null, $dni = null, $nombres = null, $apellidos = null, $fecha_nacimiento = null, $fecha_fallecimiento = null, $edad = null, $sexo = null) {
        $this->id_fallecido = $id_fallecido;
        $this->dni = $dni;
        $this->nombres = $nombres;
        $this->apellidos = $apellidos;
        $this->fecha_nacimiento = $fecha_nacimiento;
        $this->fecha_fallecimiento = $fecha_fallecimiento;
        $this->edad = $edad;
        $this->sexo = $sexo;
    }
}
?>
