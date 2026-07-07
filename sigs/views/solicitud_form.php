<?php
require_once '../includes/auth_check.php';
include 'layout/header.php';

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Al guardar la solicitud simulamos generar un número de solicitud y pasamos al siguiente paso
    $id_solicitud = rand(1000, 9999); // Simulación rápida de un insert (ECU-01)
    
    // Guardamos en sesión o pasamos por GET
    $parametros = http_build_query([
        'id_solicitud' => $id_solicitud,
        'nombre_deudo' => $_POST['nombre_deudo'],
        'dni_deudo' => $_POST['dni_deudo'],
        'parentesco' => $_POST['parentesco']
    ]);
    
    header("Location: expediente_form.php?$parametros");
    exit();
}
?>

<div class="row mt-4">
    <div class="col-md-8 offset-md-2">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Paso 1: Datos de la Solicitud</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="solicitud_form.php">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">DNI del Deudo / Solicitante</label>
                            <input type="text" class="form-control" name="dni_deudo" required pattern="\d{8}" title="Debe contener 8 números">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre Completo del Deudo</label>
                            <input type="text" class="form-control" name="nombre_deudo" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Parentesco con el Fallecido</label>
                            <input type="text" class="form-control" name="parentesco" required>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Continuar a registrar expediente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
