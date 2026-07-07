<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGS - Sistema de Gestión de Expedientes de Fallecidos</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/estilos.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php if(isset($_SESSION['id_usuario'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="../index.php">SIGS Cementerio</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-toggle="target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="solicitud_form.php">Nueva Solicitud</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="consulta.php">Consultar Expedientes</a>
                </li>
                <?php if($_SESSION['rol'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="reportes.php">Reportes y Estadísticas</a>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <span class="nav-link text-white">Usuario: <?= htmlspecialchars($_SESSION['usuario']) ?> (<?= ucfirst($_SESSION['rol']) ?>)</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="../logout.php">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>

<div class="container">
