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

// ---------------------------------------------
//  OPCIÓN YAPE: MOSTRAR UNA IMAGEN ESTÁTICA
// ---------------------------------------------
if ($tipoApp === 'yape') {

    // Ruta de tu imagen Yape (ajústala según tu proyecto)
    $rutaImagenYape = '../img/QR/yape.jpeg';

    if (file_exists($rutaImagenYape)) {
        header('Content-Type: image/png');
        readfile($rutaImagenYape);
        exit;
    } else {
        header('Content-Type: image/png');
        QRcode::png("Imagen Yape no encontrada");
        exit;
    }
}

// ---------------------------------------------
//  SI NO ES YAPE → GENERAR QR NORMAL
// ---------------------------------------------

$nit = "123456789"; 
$nombreComercio = "Clínica Dental Dentani";
$concepto = "Tratamiento dental";
$referencia = "Tratamiento #" . str_pad($idTratamiento, 5, "0", STR_PAD_LEFT);
$montoFormateado = number_format(floatval($monto), 2, '.', '');

$contenidoQR = "$nit|$nombreComercio|$montoFormateado|$concepto: {$resultado['pacienteNombre']}|$referencia";

header('Content-Type: image/png');
QRcode::png($contenidoQR);
exit;
?>
