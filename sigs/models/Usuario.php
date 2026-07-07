<?php
class Usuario {
    public $id_usuario;
    public $usuario;
    public $contrasena;
    public $rol;

    public function __construct($id_usuario = null, $usuario = null, $contrasena = null, $rol = null) {
        $this->id_usuario = $id_usuario;
        $this->usuario = $usuario;
        $this->contrasena = $contrasena;
        $this->rol = $rol;
    }
}
?>
