<?php
require_once '../includes/auth_check.php';
require_once '../controllers/ConsultaController.php';

// Verificamos si existe el autoloader de Composer para DomPDF o TCPDF
if (!file_exists('../vendor/autoload.php')) {
    die("Error: No se encontró la librería para generar PDF. Ejecuta 'composer require dompdf/dompdf' en la carpeta raíz del proyecto 'sigs'.");
}

require '../vendor/autoload.php';
use Dompdf\Dompdf;

$id_expediente = $_GET['id'] ?? 0;
$controller = new ConsultaController();
$detalle = $controller->obtenerDetalle($id_expediente);

if (!$detalle) {
    die("Expediente no encontrado.");
}

$html = '
<h2>Comprobante de Registro de Sepultura</h2>
<hr>
<p><strong>N° Expediente:</strong> ' . htmlspecialchars($detalle['numero_expediente']) . '</p>
<p><strong>N° Comprobante:</strong> ' . htmlspecialchars($detalle['id_comprobante']) . '</p>
<p><strong>Fecha Emisión:</strong> ' . htmlspecialchars($detalle['fecha_emision']) . '</p>
<h3>Datos del Fallecido</h3>
<p>Nombres: ' . htmlspecialchars($detalle['nombres'] . ' ' . $detalle['apellidos']) . '</p>
<p>DNI: ' . htmlspecialchars($detalle['dni']) . '</p>
<p>Fecha Defunción: ' . htmlspecialchars($detalle['fecha_fallecimiento']) . '</p>
<h3>Datos del Solicitante</h3>
<p>Deudo: ' . htmlspecialchars($detalle['nombre_deudo']) . '</p>
<p>DNI: ' . htmlspecialchars($detalle['dni_deudo']) . '</p>
<p>Parentesco: ' . htmlspecialchars($detalle['parentesco']) . '</p>
<hr>
<p style="text-align:center;"><em>Municipalidad - Área de Cementerio</em></p>
';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("comprobante_" . $detalle['numero_expediente'] . ".pdf", array("Attachment" => true));
?>
