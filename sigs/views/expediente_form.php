<?php
require_once '../includes/auth_check.php';
require_once '../controllers/ExpedienteController.php';

$mensaje = '';
$tipo_mensaje = '';
$datos_post = $_POST;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ExpedienteController();
    $resultado = $controller->registrar($_POST);

    if ($resultado['status'] === 'exito') {
        header("Location: comprobante_ver.php?id=" . $resultado['id_expediente']);
        exit();
    } else {
        $tipo_mensaje = $resultado['status'] === 'duplicado' ? 'warning' : 'danger';
        $mensaje = '<ul>';
        foreach ($resultado['mensajes'] as $error) {
            $mensaje .= "<li>" . htmlspecialchars($error) . "</li>";
        }
        $mensaje .= '</ul>';
    }
}
?>

<?php include 'layout/header.php'; ?>

<div class="row mt-4">
    <div class="col-md-10 offset-md-1">
        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $tipo_mensaje ?>">
                <?= $mensaje ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Paso 2: Registrar Expediente de Fallecido</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="expediente_form.php">
                    <!-- Datos precargados de la solicitud (ocultos o solo lectura) -->
                    <input type="hidden" name="id_solicitud" value="<?= htmlspecialchars($_GET['id_solicitud'] ?? $_POST['id_solicitud'] ?? '') ?>">
                    <input type="hidden" name="nombre_deudo" value="<?= htmlspecialchars($_GET['nombre_deudo'] ?? $_POST['nombre_deudo'] ?? '') ?>">
                    <input type="hidden" name="dni_deudo" value="<?= htmlspecialchars($_GET['dni_deudo'] ?? $_POST['dni_deudo'] ?? '') ?>">
                    <input type="hidden" name="parentesco" value="<?= htmlspecialchars($_GET['parentesco'] ?? $_POST['parentesco'] ?? '') ?>">
                    
                    <h6 class="border-bottom pb-2 mb-3">Datos del Fallecido</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">DNI</label>
                            <input type="text" class="form-control" name="dni" required pattern="\d{8}" value="<?= htmlspecialchars($datos_post['dni'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nombres</label>
                            <input type="text" class="form-control" name="nombres" required value="<?= htmlspecialchars($datos_post['nombres'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Apellidos</label>
                            <input type="text" class="form-control" name="apellidos" required value="<?= htmlspecialchars($datos_post['apellidos'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" name="fecha_nacimiento" required value="<?= htmlspecialchars($datos_post['fecha_nacimiento'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha de Fallecimiento</label>
                            <input type="date" class="form-control" name="fecha_fallecimiento" required value="<?= htmlspecialchars($datos_post['fecha_fallecimiento'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sexo</label>
                            <select class="form-select" name="sexo" required>
                                <option value="">Seleccione...</option>
                                <option value="Masculino" <?= (isset($datos_post['sexo']) && $datos_post['sexo'] === 'Masculino') ? 'selected' : '' ?>>Masculino</option>
                                <option value="Femenino" <?= (isset($datos_post['sexo']) && $datos_post['sexo'] === 'Femenino') ? 'selected' : '' ?>>Femenino</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 mt-4">Datos del Expediente</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Número de Expediente</label>
                            <input type="text" class="form-control" name="numero_expediente" required placeholder="EXP-2026-0001" value="<?= htmlspecialchars($datos_post['numero_expediente'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">Guardar Expediente y Generar Comprobante</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
