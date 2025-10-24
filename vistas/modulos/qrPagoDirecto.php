<?php
include_once '../../modelos/conexion.php';
include_once 'phpqrcode/qrlib.php';

$idTratamiento = $_GET['idTratamiento'] ?? '';
$monto = $_GET['monto'] ?? '0.00';
$tipoApp = $_GET['app'] ?? 'qr'; // 'yape' o 'qr'

$conexion = Conexion::conectar();
$stmt = $conexion->prepare("
    SELECT CONCAT(p.nombre, ' (CI: ', p.ci, ')') AS pacienteNombre
    FROM tratamiento t
    INNER JOIN pacientes p ON t.idPaciente = p.idPaciente
    WHERE t.idTratamiento = ?
");
$stmt->bindParam(1, $idTratamiento, PDO::PARAM_INT);
$stmt->execute();
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$resultado) {
    header('Content-Type: image/png');
    QRcode::png("Tratamiento no encontrado");
    exit;
}

$nit = "123456789"; // NIT real
$nombreComercio = "Clínica Dental Dentani";
$concepto = "Tratamiento dental";
$referencia = "Tratamiento #" . str_pad($idTratamiento, 5, "0", STR_PAD_LEFT);
$montoFormateado = number_format(floatval($monto), 2, '.', '');

if ($tipoApp === 'yape') {
    // QR para Yape (el paciente escribe el monto manualmente)
    $contenidoQR = "yape://pay?alias=dentani.bolivia"; // reemplazá con tu alias Yape real
} else {
    // QR bancario estándar
    $contenidoQR = "$nit|$nombreComercio|$montoFormateado|$concepto: {$resultado['pacienteNombre']}|$referencia";
}

header('Content-Type: image/png');
QRcode::png($contenidoQR);
exit;
?>