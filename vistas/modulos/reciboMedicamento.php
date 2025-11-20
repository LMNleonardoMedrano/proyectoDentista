<?php
date_default_timezone_set('America/La_Paz');
include_once '../../modelos/conexion.php';
$conexion = Conexion::conectar();

$idTratamiento = $_GET['idTratamiento'] ?? null;
$claveSecreta = "TuClaveUltraPrivada2025";
$tokenRecibido = $_GET['token'] ?? '';
$tokenEsperado = hash('sha256', $idTratamiento . $claveSecreta);

if ($tokenRecibido !== $tokenEsperado) {
  die("<h2 style='color:red;'>❌ Acceso denegado. Token inválido.</h2>");
}

$stmt = $conexion->prepare("
  SELECT dm.*, 
         m.nombre AS nombreMedicamento,
         t.idTratamiento, 
         CONCAT(p.nombre, ' (CI: ', p.ci, ')') AS pacienteNombre,
         CONCAT(u.nombre, ' ', u.apellido) AS nombreDoctor
  FROM detalleMedicamento dm
  INNER JOIN medicamento m ON dm.codMedicamento = m.codMedicamento
  INNER JOIN tratamiento t ON dm.idTratamiento = t.idTratamiento
  INNER JOIN pacientes p ON t.idPaciente = p.idPaciente
  INNER JOIN usuarios u ON t.idUsuarios = u.idUsuarios
  WHERE dm.idTratamiento = ?
");
$stmt->bindParam(1, $idTratamiento, PDO::PARAM_INT);
$stmt->execute();
$medicamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$medicamentos) {
  die("<h2 style='color:red;'>No se encontraron medicamentos recetados para este tratamiento.</h2>");
}

// Generar QR
ob_start();
include_once '../modulos/phpqrcode/qrlib.php';
$linkQR = "http://192.168.100.73/dentista/vistas/modulos/reciboMedicamento.php?idTratamiento=$idTratamiento&token=$tokenEsperado";
QRcode::png($linkQR, null, QR_ECLEVEL_L, 3);
$qrImage = base64_encode(ob_get_clean());

// Render HTML
echo '
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recibo de Medicamentos</title>
  <style>
    body {
      font-family: "Segoe UI", sans-serif;
      background-color: #f8f9fa;
      padding: 30px;
      max-width: 800px;
      margin: auto;
      color: #212529;
    }
    .recibo {
      background: white;
      padding: 30px;
      border: 1px solid #dee2e6;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .header {
      display: flex;
      align-items: center;
      border-bottom: 1px dashed #ccc;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }
    .header-left {
      margin-right: 15px;
    }
    .header h1 {
      margin: 0;
      color: #17a2b8;
      font-size: 22px;
    }
    .section-title {
      font-weight: bold;
      color: #007bff;
      margin-bottom: 8px;
      border-bottom: 1px solid #ddd;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 13px;
      margin-top: 10px;
    }
    th, td {
      border: 1px solid #dee2e6;
      padding: 6px;
    }
    th {
      background-color: #e9ecef;
    }
    .footer {
      font-size: 12px;
      text-align: center;
      color: #6c757d;
      margin-top: 30px;
      border-top: 1px dashed #ccc;
      padding-top: 10px;
    }
    .qr {
      text-align: center;
      margin-top: 20px;
    }
    .no-print {
      text-align: center;
      margin: 20px 0;
    }
    .btn-print {
      background-color: #007bff;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }
    @media print {
      .no-print {
        display: none;
      }
    }
  </style>
</head>
<body>
 <div class="no-print">
    <button onclick="window.print()" class="btn-print">Imprimir Recibo</button>
  </div>
  <div class="recibo">
    <div class="header">
      <div class="header-left">
        <img src="/dentista/vistas/src/img/logo7.5.png" alt="Logo" style="width:120px; height:auto;">
      </div>
      <div>
        <h1>Recibo de Medicamentos Recetados</h1>
        <p><strong>Impreso:</strong> ' . date("d/m/Y H:i") . '</p>
      </div>
      <div style="margin-left:auto; text-align:right;">
        <p><strong>Paciente:</strong> ' . $medicamentos[0]["pacienteNombre"] . '</p>
        <p><strong>Doctor:</strong> Dr. ' . $medicamentos[0]["nombreDoctor"] . '</p>
      </div>
    </div>

    <div class="section-title">Detalle de Medicamentos</div>
    <table>
      <thead>
        <tr>
          <th>Medicamento</th>
          <th>Dosis</th>
          <th>Inicio</th>
          <th>Final</th>
          <th>Aplicación</th>
          <th>Observación</th>
        </tr>
      </thead>
      <tbody>';
foreach ($medicamentos as $med) {
  echo '<tr>
          <td>' . $med["nombreMedicamento"] . '</td>
          <td>' . $med["dosis"] . '</td>
          <td>' . date("d/m/Y", strtotime($med["fechaInicio"])) . '</td>
          <td>' . date("d/m/Y", strtotime($med["fechaFinal"])) . '</td>
          <td>' . $med["tiempo"] . '</td>
          <td>' . $med["observacion"] . '</td>
        </tr>';
}
echo '
      </tbody>
    </table>

    <div class="qr">
      <img src="data:image/png;base64,' . $qrImage . '" alt="QR" style="max-width: 150px;">
      <p style="font-size:12px;">Escanéa para verificar este recibo</p>
    </div>

    <div class="footer">
      <p>Firmado por: Dr. ' . $medicamentos[0]["nombreDoctor"] . '</p>
      <p>Este documento forma parte del historial clínico institucional</p>
    </div>
  </div>
</body>
</html>';
?>
