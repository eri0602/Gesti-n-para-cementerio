<?php
function sanitizar($dato) {
    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}

function generarNumeroExpediente($ultimoId) {
    $anio = date('Y');
    $correlativo = str_pad($ultimoId + 1, 4, '0', STR_PAD_LEFT);
    return "EXP-{$anio}-{$correlativo}";
}
?>
