<?php
date_default_timezone_set('America/La_Paz');
include_once '../../modelos/conexion.php';
$conexion = Conexion::conectar();
$tokenRecibido = $_GET['token'] ?? '';
$claveSecreta = "TuClaveUltraPrivada2025";
$tokenEsperado = hash('sha256', $_GET['codPlan'] . $claveSecreta);

if ($tokenRecibido !== $tokenEsperado) {
  die("<h2 style='color:red;'>❌ Acceso no autorizado. Token inválido.</h2>");
}
if (isset($_GET['codPlan'])) {
  $codPlan = $_GET['codPlan'];

  $stmt = $conexion->prepare("
    SELECT pp.*, tp.nombreTipoPago, t.idPaciente, t.totalPago, t.idUsuarios,
           CONCAT(p.nombre, ' (CI: ', p.ci, ')') AS pacienteNombre,
           CONCAT(u.nombre, ' ', u.apellido) AS nombreDoctor
    FROM planPagoTratamiento pp
    INNER JOIN tipoPago tp ON pp.codTipoPago = tp.codTipoPago
    INNER JOIN tratamiento t ON pp.idTratamiento = t.idTratamiento
    INNER JOIN pacientes p ON t.idPaciente = p.idPaciente
    INNER JOIN usuarios u ON t.idUsuarios = u.idUsuarios
    WHERE pp.codPlan = ?
  ");
  $stmt->bindParam(1, $codPlan, PDO::PARAM_INT);
  $stmt->execute();
  $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($resultado) {
    $stmtPagos = $conexion->prepare("SELECT SUM(monto) FROM planPagoTratamiento WHERE idTratamiento = ?");
    $stmtPagos->bindParam(1, $resultado["idTratamiento"], PDO::PARAM_INT);
    $stmtPagos->execute();
    $totalAbonado = floatval($stmtPagos->fetchColumn());

    $totalTratamiento = floatval($resultado["totalPago"]);

    if ($totalAbonado == 0) {
      $estadoPago = "PENDIENTE";
    } elseif ($totalAbonado >= $totalTratamiento) {
      $estadoPago = "PAGADO";
    } else {
      $estadoPago = "PARCIAL";
    }
    $colorEstado = $estadoPago === "PAGADO" ? "#28a745" : ($estadoPago === "PARCIAL" ? "#ffc107" : "#dc3545");

    $stmtHistorial = $conexion->prepare("
      SELECT fecha, monto, codTipoPago, descripcion,
             (SELECT nombreTipoPago FROM tipoPago WHERE codTipoPago = pp.codTipoPago) AS nombreTipoPago
      FROM planPagoTratamiento pp
      WHERE idTratamiento = ?
      ORDER BY fecha ASC
    ");
    $stmtHistorial->bindParam(1, $resultado["idTratamiento"], PDO::PARAM_INT);
    $stmtHistorial->execute();
    $historialPagos = $stmtHistorial->fetchAll(PDO::FETCH_ASSOC);

    ob_start();
    include_once '../modulos/phpqrcode/qrlib.php';
    $claveSecreta = "TuClaveUltraPrivada2025"; // misma clave que en reciboQR.php
    $token = hash('sha256', $codPlan . $claveSecreta);
    $linkQR = "http://192.168.100.73/dentista/vistas/modulos/reciboQR.php?codPlan=$codPlan&token=$token";

    // $linkQR = "http://localhost/dentista/vistas/modulos/reciboQR.php?codPlan=$codPlan&token=$token";

    QRcode::png($linkQR, null, QR_ECLEVEL_L, 3);
    $qrImage = base64_encode(ob_get_clean());

    echo '
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recibo de Pago</title>
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
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px dashed #ccc;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }
    .header h1 {
      margin: 0;
      color: #20c997;
      font-size: 24px;
    }
    .header p {
      margin: 0;
      font-size: 12px;
      color: #666;
    }
    .row {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
    }
    .col {
      width: 48%;
      margin-bottom: 10px;
    }
    .section {
      margin-bottom: 25px;
    }
    .section-title {
      font-weight: bold;
      color: #007bff;
      margin-bottom: 8px;
      border-bottom: 1px solid #ddd;
    }
    .dato {
      margin: 4px 0;
    }
    .estado {
      font-weight: bold;
      color: ' . $colorEstado . ';
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
      <div>
        <h1>CLÍNICA DENTAL DENTANI</h1>
        <p>Av. Principal #123 | Tel: 78912345</p>
      </div>
      <div style="text-align:right;">
        <p><strong>Recibo N°:</strong> ' . str_pad($codPlan, 5, '0', STR_PAD_LEFT) . '</p>
        <p><strong>Impreso:</strong> ' . date("d/m/Y H:i") . '</p>
      </div>
    </div>

    <div class="section row">
      <div class="col">
        <div class="section-title">Datos del Paciente</div>
        <div class="dato"><strong>Nombre:</strong> ' . $resultado["pacienteNombre"] . '</div>
      </div>
      <div class="col">
        <div class="section-title">Datos del Tratamiento</div>
        <div class="dato"><strong>ID Tratamiento:</strong> ' . $resultado["idTratamiento"] . '</div>
        <div class="dato"><strong>Doctor:</strong> Dr. ' . $resultado["nombreDoctor"] . '</div>
      </div>
    </div>

    <div class="section row">
      <div class="col">
        <div class="section-title">Detalles del Pago</div>
        <div class="dato"><strong>Fecha:</strong> ' . date("d/m/Y", strtotime($resultado["fecha"])) . '</div>
        <div class="dato"><strong>Tipo de Pago:</strong> ' . $resultado["nombreTipoPago"] . '</div>
        <div class="dato"><strong>Descripción:</strong> ' . $resultado["descripcion"] . '</div>
      </div>
      <div class="col">
        <div class="section-title">Monto Abonado</div>
        <div class="dato"><strong>Monto Actual:</strong> ' . number_format($resultado["monto"], 2) . ' Bs.</div>
        <div class="dato"><strong>Descuento:</strong> ' . $resultado["descuento"] . '%</div>
      </div>
    </div>

    <div class="section row">
      <div class="col">
        <div class="section-title">Resumen de Cuenta</div>
        <div class="dato"><strong>Total del Tratamiento:</strong> ' . number_format($totalTratamiento, 2) . ' Bs.</div>
        <div class="dato"><strong>Total Abonado:</strong> ' . number_format($totalAbonado, 2) . ' Bs.</div>
        <div class="dato"><strong>Estado:</strong> <span class="estado">' . $estadoPago . '</span></div>
      </div>
      <div class="col" style="text-align:center;">
        <div class="section-title">Código QR</div>
        <img src="data:image/png;base64,' . $qrImage . '" alt="Código QR" style="max-width: 150px;">
        <p style="font-size:12px;">Escanéa para ver el recibo</p>
      </div>
    </div>

    <div class="section">
      <div class="section-title">Historial de Pagos</div>
      <table>
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Monto</th>
            <th>Tipo</th>
            <th>Descripción</th>
          </tr>
        </thead>
        <tbody>';
    foreach ($historialPagos as $pago) {
      echo '<tr>
            <td>' . date("d/m/Y", strtotime($pago["fecha"])) . '</td>
            <td>' . number_format($pago["monto"], 2) . ' Bs.</td>
            <td>' . $pago["nombreTipoPago"] . '</td>
            <td>' . $pago["descripcion"] . '</td>
          </tr>';
    }
    echo '
        </tbody>
      </table>
    </div>

    <div class="footer">
      <p>Firmado por: Dr. ' . $resultado["nombreDoctor"] . '</p>
      <p>Emitido automáticamente por el sistema odontológico institucional</p>
    </div>
  </div>
</body>
</html>';
  } else {
    echo "<p style='color:red;'>Recibo no encontrado para el código proporcionado.</p>";
  }
} else {
  echo "<p style='color:red;'>Parámetro <code>codPlan</code> no proporcionado.</p>";
}
