<?php
require_once '../includes/auth_check.php';
require_once '../controllers/ConsultaController.php';

$id_expediente = $_GET['id'] ?? 0;
$controller = new ConsultaController();
$detalle = $controller->obtenerDetalle($id_expediente);

if (!$detalle) {
    die("Expediente no encontrado.");
}
?>

<?php include 'layout/header.php'; ?>

<div class="row mt-4 justify-content-center">
    <div class="col-md-8">
        <div class="alert alert-success">
            <h5>¡Expediente registrado correctamente!</h5>
            <p class="mb-0">N° de expediente: <strong><?= htmlspecialchars($detalle['numero_expediente']) ?></strong></p>
        </div>

        <div class="card shadow-sm border-success">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Comprobante de Registro</h5>
            </div>
            <div class="card-body">
                <h4 class="text-center mb-4">MUNICIPALIDAD - ÁREA DE CEMENTERIO</h4>
                <div class="row mb-3">
                    <div class="col-6"><strong>N° Comprobante:</strong> <?= htmlspecialchars($detalle['id_comprobante']) ?></div>
                    <div class="col-6 text-end"><strong>Fecha Emisión:</strong> <?= htmlspecialchars($detalle['fecha_emision']) ?></div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-12"><strong>N° Expediente:</strong> <?= htmlspecialchars($detalle['numero_expediente']) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>Datos del Fallecido</h6>
                        <p class="mb-1"><strong>Nombres:</strong> <?= htmlspecialchars($detalle['nombres'] . ' ' . $detalle['apellidos']) ?></p>
                        <p class="mb-1"><strong>DNI:</strong> <?= htmlspecialchars($detalle['dni']) ?></p>
                        <p class="mb-1"><strong>Fec. Defunción:</strong> <?= htmlspecialchars($detalle['fecha_fallecimiento']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Datos del Solicitante</h6>
                        <p class="mb-1"><strong>Deudo:</strong> <?= htmlspecialchars($detalle['nombre_deudo']) ?></p>
                        <p class="mb-1"><strong>DNI:</strong> <?= htmlspecialchars($detalle['dni_deudo']) ?></p>
                        <p class="mb-1"><strong>Parentesco:</strong> <?= htmlspecialchars($detalle['parentesco']) ?></p>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <p><em>Este documento certifica el registro exitoso del servicio de sepultura.</em></p>
                </div>
            </div>
            <div class="card-footer bg-light text-end">
                <a href="consulta.php" class="btn btn-secondary">Volver al listado</a>
                <!-- En un entorno real apuntaría al PDF, por ahora alertaremos que está pendiente -->
                <button type="button" class="btn btn-danger" onclick="alert('Funcionalidad de PDF en construcción')">Descargar PDF</button>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
