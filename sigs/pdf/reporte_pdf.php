<?php
require_once '../includes/auth_check.php';

if ($_SESSION['rol'] !== 'admin') {
    die("Acceso denegado.");
}

if (!file_exists('../vendor/autoload.php')) {
    die("Error: No se encontró la librería para generar PDF. Ejecuta 'composer require dompdf/dompdf' en la carpeta raíz del proyecto 'sigs'.");
}

require '../vendor/autoload.php';
use Dompdf\Dompdf;
require_once '../controllers/ReporteController.php';

$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-t');

$controller = new ReporteController();
$estadisticas = $controller->generarEstadisticas($desde, $hasta);

$html = '
<h2>Reporte Estadístico de Fallecidos</h2>
<p><strong>Periodo:</strong> ' . htmlspecialchars($desde) . ' al ' . htmlspecialchars($hasta) . '</p>
<p><strong>Generado por:</strong> ' . htmlspecialchars($_SESSION['usuario']) . ' (Jefe de Área)</p>
<p><strong>Fecha de Generación:</strong> ' . date('Y-m-d H:i:s') . '</p>
<hr>
<h3>Por Sexo</h3>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <tr><th>Sexo</th><th>Total</th></tr>';
foreach ($estadisticas['por_sexo'] as $row) {
    $html .= '<tr><td>' . htmlspecialchars($row['sexo']) . '</td><td>' . htmlspecialchars($row['total']) . '</td></tr>';
}
$html .= '
</table>
<br>
<h3>Por Grupo Etario</h3>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <tr><th>Grupo Etario</th><th>Total</th></tr>';
foreach ($estadisticas['por_grupo_etario'] as $row) {
    $html .= '<tr><td>' . htmlspecialchars($row['grupo_etario']) . '</td><td>' . htmlspecialchars($row['total']) . '</td></tr>';
}
$html .= '
</table>
<hr>
<p style="text-align:center;"><em>Municipalidad - Área de Cementerio</em></p>
';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("reporte_estadistico_" . date('Ymd_His') . ".pdf", array("Attachment" => true));
?>
