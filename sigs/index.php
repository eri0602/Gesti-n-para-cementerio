<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: views/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGS - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">SIGS Cementerio</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="views/solicitud_form.php">Nueva Solicitud</a></li>
                <li class="nav-item"><a class="nav-link" href="views/consulta.php">Consultar Expedientes</a></li>
                <?php if($_SESSION['rol'] === 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="views/reportes.php">Reportes y Estadísticas</a></li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item"><span class="nav-link text-white">Usuario: <?= htmlspecialchars($_SESSION['usuario']) ?> (<?= ucfirst($_SESSION['rol']) ?>)</span></li>
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-12 text-center mb-5">
            <h2>Bienvenido al Sistema de Gestión de Expedientes de Fallecidos</h2>
            <p class="lead">Seleccione una opción para comenzar</p>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100 text-center">
                <div class="card-body">
                    <h5 class="card-title">Registrar Expediente</h5>
                    <p class="card-text">Inicie el flujo para registrar una nueva solicitud y expediente de defunción.</p>
                    <a href="views/solicitud_form.php" class="btn btn-primary">Ir a Nueva Solicitud</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100 text-center">
                <div class="card-body">
                    <h5 class="card-title">Consultas</h5>
                    <p class="card-text">Busque expedientes por múltiples criterios y vea sus detalles.</p>
                    <a href="views/consulta.php" class="btn btn-info text-white">Ir a Consultas</a>
                </div>
            </div>
        </div>
        <?php if ($_SESSION['rol'] === 'admin'): ?>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100 text-center">
                <div class="card-body">
                    <h5 class="card-title">Reportes</h5>
                    <p class="card-text">Genere estadísticas trimestrales y anuales en PDF.</p>
                    <a href="views/reportes.php" class="btn btn-warning">Ir a Reportes</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
