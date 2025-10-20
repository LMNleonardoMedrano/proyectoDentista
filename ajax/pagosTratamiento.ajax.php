<?php
require_once "../controladores/planPagoTratamiento.controlador.php";
require_once "../modelos/planPagoTratamiento.modelo.php";
require_once "../modelos/tratamiento.modelo.php";
require_once "../controladores/tratamiento.controlador.php";

if (!isset($_POST["idTratamiento"]) || !is_numeric($_POST["idTratamiento"])) {
  echo "<p class='text-warning'>⚠️ No se recibió un tratamiento válido.</p>";
  return;
}

$id = $_POST["idTratamiento"];
$pagos = ControladorPlanPago::ctrMostrarPagosPorTratamiento($id);

if (count($pagos) == 0) {
  echo "<p class='text-danger'>🚫 Este tratamiento no tiene pagos registrados.</p>";
  return;
}

// 🧠 Total del tratamiento
$tratamiento = ModeloTratamiento::mdlObtenerTratamiento("tratamiento", $id);
$totalTratamiento = isset($tratamiento["totalPago"]) ? floatval($tratamiento["totalPago"]) : 0;

// 🧮 Cálculos
$totalPagado    = array_sum(array_column($pagos, "monto"));
$montoPendiente = max(0, $totalTratamiento - $totalPagado);
$excesoPago     = max(0, $totalPagado - $totalTratamiento);

// 🩺 Estado de Pago
if ($totalPagado == 0) {
  $estadoPago = "pendiente";
} elseif ($totalPagado >= $totalTratamiento) {
  $estadoPago = "pagado";
} else {
  $estadoPago = "parcial";
}

// 🔁 Actualizar base de datos
ControladorTratamiento::ctrActualizarSaldoTratamiento($id, $montoPendiente);
ControladorTratamiento::ctrActualizarEstadoPago($id, $estadoPago);

// 📋 Render de pagos
echo "<table class='table table-bordered table-sm'>
  <thead class='thead-dark'>
    <tr>
      <th>#</th>
      <th>Fecha</th>
      <th>Descripción</th>
      <th>Monto</th>
      <th>Tipo</th>
    </tr>
  </thead>
  <tbody>";

// Listado de pagos
foreach ($pagos as $i => $p) {
  echo "<tr>
    <td>" . ($i + 1) . "</td>
    <td>" . $p['fecha'] . "</td>
    <td style='white-space: normal; word-wrap: break-word; word-break: break-word;'>" . htmlspecialchars($p['descripcion']) . "</td>
    <td>" . number_format($p['monto'], 2) . " Bs</td>
    <td>" . $p['nombreTipoPago'] . "</td>
  </tr>";
}


// 🔹 Fila con resumen financiero ordenado
echo "<tr class='text-center align-middle'>
        <td class='table-success'><strong>Total Pagado</strong>; " . number_format($totalPagado, 2) . " Bs</td>
        <td class='table-warning'><strong>Total Tratamiento</strong>: " . number_format($totalTratamiento, 2) . " Bs</td>
        <td colspan='3' class='";
if ($excesoPago > 0) {
    echo "table-warning";
} elseif ($montoPendiente > 0) {
    echo "table-danger";
} else {
    echo "table-secondary";
}
echo " text-center'><strong>";
if ($excesoPago > 0) {
    echo "Pagó de más</strong>: " . number_format($excesoPago, 2) . " Bs";
} elseif ($montoPendiente > 0) {
    echo "Pendiente por pagar</strong>: " . number_format($montoPendiente, 2) . " Bs";
} else {
    echo "Sin saldo pendiente</strong>";
}
echo "</td></tr>";

// 🔹 Fila con Estado de Pago
echo "<tr class='table-info text-center align-middle'>
        <td colspan='5'><strong>Estado de Pago:</strong> <span class='font-weight-bold text-uppercase'>" . ucfirst($estadoPago) . "</span></td>
      </tr>";

// 📦 Datos ocultos para JS
echo "<tr style='display:none'>
        <td colspan='5'>
          <div id='estadoPagoReal'>" . strtolower($estadoPago) . "</div>
          <div id='saldoActualizado'>" . number_format($montoPendiente, 2) . "</div>
        </td>
      </tr>";

echo "</tbody></table>";

?>