<?php
require_once '../includes/auth_check.php';
require_once '../controllers/ConsultaController.php';

$controller = new ConsultaController();
$resultados = [];
$busqueda_realizada = false;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['buscar'])) {
    $resultados = $controller->buscar($_GET);
    $busqueda_realizada = true;
}
?>

<?php include 'layout/header.php'; ?>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Consultar Expedientes</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="consulta.php">
                    <input type="hidden" name="buscar" value="1">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">N° de Expediente</label>
                            <input type="text" class="form-control" name="numero_expediente" value="<?= htmlspecialchars($_GET['numero_expediente'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">DNI Fallecido</label>
                            <input type="text" class="form-control" name="dni" value="<?= htmlspecialchars($_GET['dni'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Nombres</label>
                            <input type="text" class="form-control" name="nombres" value="<?= htmlspecialchars($_GET['nombres'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Apellidos</label>
                            <input type="text" class="form-control" name="apellidos" value="<?= htmlspecialchars($_GET['apellidos'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="consulta.php" class="btn btn-secondary">Limpiar</a>
                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($busqueda_realizada): ?>
            <?php if (count($resultados) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover border">
                        <thead class="table-dark">
                            <tr>
                                <th>N° Expediente</th>
                                <th>DNI</th>
                                <th>Fallecido</th>
                                <th>Edad</th>
                                <th>Sexo</th>
                                <th>Fecha Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultados as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['numero_expediente']) ?></td>
                                    <td><?= htmlspecialchars($row['dni']) ?></td>
                                    <td><?= htmlspecialchars($row['nombres'] . ' ' . $row['apellidos']) ?></td>
                                    <td><?= htmlspecialchars($row['edad']) ?></td>
                                    <td><?= htmlspecialchars($row['sexo']) ?></td>
                                    <td><?= htmlspecialchars($row['fecha_registro']) ?></td>
                                    <td>
                                        <a href="expediente_detalle.php?id=<?= $row['id_expediente'] ?>" class="btn btn-sm btn-info">Ver</a>
                                        <?php if ($_SESSION['rol'] === 'admin'): ?>
                                            <a href="#" class="btn btn-sm btn-warning">Editar</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No se encontraron expedientes con los criterios indicados.</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
