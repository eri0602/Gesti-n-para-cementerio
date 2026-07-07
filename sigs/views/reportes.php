<?php
require_once '../includes/auth_check.php';

// Solo el rol admin puede acceder a los reportes
if ($_SESSION['rol'] !== 'admin') {
    die("Acceso denegado. Se requieren permisos de Jefe de Área.");
}

require_once '../controllers/ReporteController.php';

$controller = new ReporteController();
$estadisticas = null;
$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-t');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['generar'])) {
    $estadisticas = $controller->generarEstadisticas($desde, $hasta);
    // Registramos la generación del reporte
    $controller->registrarGeneracion('Periodo: ' . $desde . ' a ' . $hasta, $_SESSION['id_usuario']);
}
?>

<?php include 'layout/header.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Generar Reportes y Estadísticas</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="reportes.php">
                    <input type="hidden" name="generar" value="1">
                    <div class="row align-items-end">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Desde</label>
                            <input type="date" class="form-control" name="desde" value="<?= htmlspecialchars($desde) ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Hasta</label>
                            <input type="date" class="form-control" name="hasta" value="<?= htmlspecialchars($hasta) ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <button type="submit" class="btn btn-primary w-100">Generar Estadísticas</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($estadisticas): ?>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-light"><h6 class="mb-0">Fallecidos por Sexo</h6></div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead><tr><th>Sexo</th><th>Total</th></tr></thead>
                                <tbody>
                                    <?php 
                                    $labels_sexo = []; $data_sexo = [];
                                    foreach ($estadisticas['por_sexo'] as $row): 
                                        $labels_sexo[] = $row['sexo'];
                                        $data_sexo[] = $row['total'];
                                    ?>
                                        <tr><td><?= htmlspecialchars($row['sexo']) ?></td><td><?= htmlspecialchars($row['total']) ?></td></tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <canvas id="chartSexo" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-light"><h6 class="mb-0">Fallecidos por Grupo Etario</h6></div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead><tr><th>Grupo Etario</th><th>Total</th></tr></thead>
                                <tbody>
                                    <?php 
                                    $labels_edad = []; $data_edad = [];
                                    foreach ($estadisticas['por_grupo_etario'] as $row): 
                                        $labels_edad[] = $row['grupo_etario'];
                                        $data_edad[] = $row['total'];
                                    ?>
                                        <tr><td><?= htmlspecialchars($row['grupo_etario']) ?></td><td><?= htmlspecialchars($row['total']) ?></td></tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <canvas id="chartEdad" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end mb-5">
                <button type="button" class="btn btn-danger" onclick="alert('Exportación PDF en construcción')">Exportar Reporte a PDF</button>
            </div>

            <script>
            document.addEventListener("DOMContentLoaded", function() {
                var ctxSexo = document.getElementById('chartSexo').getContext('2d');
                new Chart(ctxSexo, {
                    type: 'pie',
                    data: {
                        labels: <?= json_encode($labels_sexo) ?>,
                        datasets: [{ data: <?= json_encode($data_sexo) ?>, backgroundColor: ['#36a2eb', '#ff6384'] }]
                    }
                });

                var ctxEdad = document.getElementById('chartEdad').getContext('2d');
                new Chart(ctxEdad, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode($labels_edad) ?>,
                        datasets: [{ label: 'Fallecidos', data: <?= json_encode($data_edad) ?>, backgroundColor: '#4bc0c0' }]
                    },
                    options: { scales: { y: { beginAtZero: true } } }
                });
            });
            </script>
        <?php endif; ?>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
