<?php
// En este diseño, la solicitud es procesada junto con el expediente en el flujo único (ECU-04).
// Este controlador puede encargarse de la validación inicial si fuera necesario de forma aislada.

class SolicitudController {
    public function procesarSolicitudInicial($datos) {
        $errores = [];
        if (empty(trim($datos['nombre_deudo']))) $errores[] = "El nombre del deudo es obligatorio.";
        if (empty(trim($datos['dni_deudo'])) || !preg_match('/^\d{8}$/', $datos['dni_deudo'])) $errores[] = "El DNI del deudo debe tener 8 dígitos.";
        if (empty(trim($datos['parentesco']))) $errores[] = "El parentesco es obligatorio.";
        
        return $errores;
    }
}
?>
