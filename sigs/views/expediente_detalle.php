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

<div class="row mt-4">
    <div class="col-md-10 offset-md-1">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detalle de Expediente: <?= htmlspecialchars($detalle['numero_expediente']) ?></h5>
                <a href="consulta.php" class="btn btn-sm btn-light">Volver a Consulta</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 border-end">
                        <h6 class="text-primary border-bottom pb-2">Información del Fallecido</h6>
                        <table class="table table-sm table-borderless">
                            <tr><th>Nombres:</th><td><?= htmlspecialchars($detalle['nombres']) ?></td></tr>
                            <tr><th>Apellidos:</th><td><?= htmlspecialchars($detalle['apellidos']) ?></td></tr>
                            <tr><th>DNI:</th><td><?= htmlspecialchars($detalle['dni']) ?></td></tr>
                            <tr><th>Nacimiento:</th><td><?= htmlspecialchars($detalle['fecha_nacimiento']) ?></td></tr>
                            <tr><th>Fallecimiento:</th><td><?= htmlspecialchars($detalle['fecha_fallecimiento']) ?></td></tr>
                            <tr><th>Edad:</th><td><?= htmlspecialchars($detalle['edad']) ?> años</td></tr>
                            <tr><th>Sexo:</th><td><?= htmlspecialchars($detalle['sexo']) ?></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary border-bottom pb-2">Información de la Solicitud y Registro</h6>
                        <table class="table table-sm table-borderless">
                            <tr><th>Fecha de Registro:</th><td><?= htmlspecialchars($detalle['fecha_registro']) ?></td></tr>
                            <tr><th>Deudo Solicitante:</th><td><?= htmlspecialchars($detalle['nombre_deudo']) ?></td></tr>
                            <tr><th>DNI Deudo:</th><td><?= htmlspecialchars($detalle['dni_deudo']) ?></td></tr>
                            <tr><th>Parentesco:</th><td><?= htmlspecialchars($detalle['parentesco']) ?></td></tr>
                            <tr><th>Fecha de Solicitud:</th><td><?= htmlspecialchars($detalle['fecha_solicitud']) ?></td></tr>
                            <?php if ($detalle['id_comprobante']): ?>
                            <tr><th>Comprobante Generado:</th><td>Sí (Emitido el <?= htmlspecialchars($detalle['fecha_emision']) ?>)</td></tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
            <?php if ($_SESSION['rol'] === 'admin'): ?>
            <div class="card-footer bg-light text-end">
                <button type="button" class="btn btn-warning" onclick="alert('Funcionalidad de Edición en construcción (FA2 de ECU-08)')">Editar/Corregir Datos</button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
