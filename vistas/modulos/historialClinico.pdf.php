<?php
require_once '../../vendor/autoload.php';
require_once '../../modelos/conexion.php'; // Ajusta la ruta según tu estructura
use Mpdf\Mpdf;

// Captura el parámetro de búsqueda (nombre o CI)
$busqueda = $_GET['idPaciente'] ?? null;

if (!$busqueda) {
    die("Debe ingresar un nombre o CI del paciente.");
}

// 1. Buscar paciente por nombre o CI
$datosPaciente = Conexion::conectar()->prepare("
    SELECT p.*, pm.nombrePT AS tutor, pm.relacion
    FROM pacientes p
    LEFT JOIN pacientemenor pm ON pm.idPaciente = p.idPaciente
    WHERE p.nombre LIKE :busqueda OR p.ci LIKE :busqueda
    LIMIT 1
");
$datosPaciente->execute(['busqueda' => "%$busqueda%"]);
$paciente = $datosPaciente->fetch(PDO::FETCH_ASSOC);

if (!$paciente) {
    die("No se encontró ningún paciente con ese nombre o CI.");
}

$idPaciente = $paciente['idPaciente'];

// 2. Historial de citas
$citas = Conexion::conectar()->prepare("
    SELECT c.*, u.nombre AS odontologo
    FROM citas c
    INNER JOIN usuarios u ON u.idUsuarios = c.idUsuarios
    WHERE c.idPaciente = :id
    ORDER BY c.fecha DESC, c.hora DESC
");
$citas->execute(['id' => $idPaciente]);
$citasHistorial = $citas->fetchAll(PDO::FETCH_ASSOC);

// 3. Tratamientos y servicios
$tratamientos = Conexion::conectar()->prepare("
    SELECT t.*, u.nombre AS odontologo
    FROM tratamiento t
    INNER JOIN usuarios u ON u.idUsuarios = t.idUsuarios
    WHERE t.idPaciente = :id
    ORDER BY t.fechaRegistro DESC
");
$tratamientos->execute(['id' => $idPaciente]);
$tratamientosHistorial = $tratamientos->fetchAll(PDO::FETCH_ASSOC);

// 4. Medicamentos por tratamiento
$medicamentos = Conexion::conectar()->prepare("
    SELECT dm.*, m.nombre AS medicamento
    FROM detallemedicamento dm
    INNER JOIN medicamento m ON m.codMedicamento = dm.codMedicamento
    INNER JOIN tratamiento t ON t.idTratamiento = dm.idTratamiento
    WHERE t.idPaciente = :id
");
$medicamentos->execute(['id' => $idPaciente]);
$medicamentosHistorial = $medicamentos->fetchAll(PDO::FETCH_ASSOC);

// 5. Pagos realizados
$pagos = Conexion::conectar()->prepare("
    SELECT pp.*, tp.nombreTipoPago
    FROM planpagotratamiento pp
    INNER JOIN tipopago tp ON tp.codTipoPago = pp.codTipoPago
    INNER JOIN tratamiento t ON t.idTratamiento = pp.idTratamiento
    WHERE t.idPaciente = :id
");
$pagos->execute(['id' => $idPaciente]);
$pagosHistorial = $pagos->fetchAll(PDO::FETCH_ASSOC);

// 6. Generar PDF con mPDF
$mpdf = new Mpdf();
$mpdf->SetTitle("Historial Clínico - {$paciente['nombre']}");

// Contenido HTML
$html = "<h2 style='text-align:center;'>Historial Clínico de {$paciente['nombre']}</h2>";
$html .= "<p><b>DNI:</b> {$paciente['ci']} | <b>Fecha Nac:</b> {$paciente['fechaNac']} | <b>Tel:</b> {$paciente['telCel']}</p>";
$html .= "<p><b>Dirección:</b> {$paciente['domicilio']} | <b>Sexo:</b> {$paciente['genero']}</p>";
if ($paciente['tutor']) {
    $html .= "<p><b>Tutor:</b> {$paciente['tutor']} ({$paciente['relacion']})</p>";
}

// Historial de citas
$html .= "<h3>📅 Historial de Citas</h3>";
$html .= "<table style='width:100%; border-collapse:collapse; font-size:12px;'>";
$html .= "<thead style='background:#007BFF; color:white;'>
<tr>
<th style='border:1px solid #ccc; padding:5px;'>Fecha</th>
<th style='border:1px solid #ccc; padding:5px;'>Hora</th>
<th style='border:1px solid #ccc; padding:5px;'>Motivo</th>
<th style='border:1px solid #ccc; padding:5px;'>Odontólogo</th>
<th style='border:1px solid #ccc; padding:5px;'>Estado</th>
</tr></thead><tbody>";
foreach ($citasHistorial as $c) {
    $html .= "<tr>
    <td style='border:1px solid #ccc; padding:5px;'>{$c['fecha']}</td>
    <td style='border:1px solid #ccc; padding:5px;'>{$c['hora']}</td>
    <td style='border:1px solid #ccc; padding:5px;'>{$c['motivoConsulta']}</td>
    <td style='border:1px solid #ccc; padding:5px;'>{$c['odontologo']}</td>
    <td style='border:1px solid #ccc; padding:5px;'>{$c['estado']}</td>
    </tr>";
}
$html .= "</tbody></table>";

// Tratamientos
$html .= "<h3>🦷 Tratamientos</h3>";
foreach ($tratamientosHistorial as $t) {
    $html .= "<p><b>Fecha:</b> {$t['fechaRegistro']} | Odontólogo: {$t['odontologo']} | Total: {$t['totalPago']} | Saldo: {$t['saldo']} | Estado: {$t['estado']}</p>";

    $servs = Conexion::conectar()->prepare("
        SELECT s.nombreServicio
        FROM detalletratamientoservicios dts
        INNER JOIN servicios s ON s.idServicio = dts.idServicio
        WHERE dts.idTratamiento = :idTratamiento
    ");
    $servs->execute(['idTratamiento' => $t['idTratamiento']]);
    $servicios = $servs->fetchAll(PDO::FETCH_ASSOC);
    $servStr = implode(", ", array_column($servicios, 'nombreServicio'));
    $html .= "<p><b>Servicios realizados:</b> $servStr</p>";
}

// Medicamentos
$html .= "<h3>💊 Medicamentos Recetados</h3>";
foreach ($medicamentosHistorial as $m) {
    $html .= "<p>{$m['medicamento']} | Dosis: {$m['dosis']} | Inicio: {$m['fechaInicio']} | Fin: {$m['fechaFinal']} | Obs: {$m['observacion']}</p>";
}

// Pagos
$html .= "<h3>💰 Pagos Realizados</h3>";
foreach ($pagosHistorial as $p) {
    $html .= "<p>{$p['fecha']} | Monto: {$p['monto']} | Forma: {$p['nombreTipoPago']} | Obs: {$p['descripcion']}</p>";
}

// Resumen final
$totalTratamientos = count($tratamientosHistorial);
$totalPagado = array_sum(array_column($pagosHistorial, 'monto'));
$saldoPendiente = array_sum(array_column($tratamientosHistorial, 'saldo'));
$ultimaCita = end($citasHistorial)['fecha'] ?? '-';
$ultimoTrat = end($tratamientosHistorial)['fechaRegistro'] ?? '-';

$html .= "<h3>🧩 Resumen Final</h3>";
$html .= "<p>Total de tratamientos: $totalTratamientos</p>";
$html .= "<p>Total pagado: $totalPagado</p>";
$html .= "<p>Saldo pendiente: $saldoPendiente</p>";
$html .= "<p>Última cita: $ultimaCita</p>";
$html .= "<p>Último tratamiento completado: $ultimoTrat</p>";

$mpdf->WriteHTML($html);
$mpdf->Output("historial_{$paciente['nombre']}.pdf", "I");
?>
