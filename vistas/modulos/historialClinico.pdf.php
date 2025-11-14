<?php
require_once '../../vendor/autoload.php';
require_once '../../modelos/conexion.php';
use Mpdf\Mpdf;

// 🟦 Capturar parámetro de búsqueda
$busqueda = $_GET['idPaciente'] ?? null;
if (!$busqueda) {
    die("Debe ingresar un nombre o CI del paciente.");
}

// 🟦 Buscar paciente
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

// 🟦 Consultas principales
$citas = Conexion::conectar()->prepare("
    SELECT c.*, CONCAT(u.nombre, ' ', u.apellido) AS odontologo
    FROM citas c
    INNER JOIN usuarios u ON u.idUsuarios = c.idUsuarios
    WHERE c.idPaciente = :id
    ORDER BY c.fecha DESC, c.hora DESC
");
$citas->execute(['id' => $idPaciente]);
$citasHistorial = $citas->fetchAll(PDO::FETCH_ASSOC);

$tratamientos = Conexion::conectar()->prepare("
    SELECT t.*, CONCAT(u.nombre, ' ', u.apellido) AS odontologo
    FROM tratamiento t
    INNER JOIN usuarios u ON u.idUsuarios = t.idUsuarios
    WHERE t.idPaciente = :id
    ORDER BY t.fechaRegistro DESC
");
$tratamientos->execute(['id' => $idPaciente]);
$tratamientosHistorial = $tratamientos->fetchAll(PDO::FETCH_ASSOC);

$medicamentos = Conexion::conectar()->prepare("
    SELECT dm.*, m.nombre AS medicamento
    FROM detallemedicamento dm
    INNER JOIN medicamento m ON m.codMedicamento = dm.codMedicamento
    INNER JOIN tratamiento t ON t.idTratamiento = dm.idTratamiento
    WHERE t.idPaciente = :id
");
$medicamentos->execute(['id' => $idPaciente]);
$medicamentosHistorial = $medicamentos->fetchAll(PDO::FETCH_ASSOC);

$pagos = Conexion::conectar()->prepare("
    SELECT pp.*, tp.nombreTipoPago
    FROM planpagotratamiento pp
    INNER JOIN tipopago tp ON tp.codTipoPago = pp.codTipoPago
    INNER JOIN tratamiento t ON t.idTratamiento = pp.idTratamiento
    WHERE t.idPaciente = :id
");
$pagos->execute(['id' => $idPaciente]);
$pagosHistorial = $pagos->fetchAll(PDO::FETCH_ASSOC);

// 🟦 Configuración mPDF
$mpdf = new Mpdf([
    'margin_left' => 15,
    'margin_right' => 15,
    'margin_top' => 35,
    'margin_bottom' => 20,
    'margin_header' => 10,
    'margin_footer' => 10,
]);

$mpdf->SetTitle("Historial Clínico - {$paciente['nombre']}");
$logo = '../../vistas/src/img/logo7.5.png'; // Ruta corregida

// 🟦 Encabezado y pie de página
$mpdf->SetHeader("
<table width='100%'>
    <tr>
        <td width='20%'><img src='$logo' width='90'></td>
        <td width='60%' style='text-align:center; font-size:14px; color:#0059b3;'>
            <b>CLÍNICA DENTAL DENTANI</b><br>
            <span style='font-size:12px; color:#000;'>Historial Clínico del Paciente</span>
        </td>
        <td width='20%' style='text-align:right; font-size:11px;'>
            <b>Fecha:</b> " . date('d/m/Y') . "
        </td>
    </tr>
</table>
<hr style='height:1px; border:none; background-color:#0059b3; margin-top:5px;' />
");

$mpdf->SetFooter("
<hr style='height:1px; border:none; background-color:#ccc;' />
<div style='text-align:center; font-size:10px; color:#555;'>Historial Clínico - Página {PAGENO}</div>
");

// 🟦 Estilos CSS
$css = "
body { font-family: sans-serif; color: #333; font-size: 12px; }
h2 { text-align:center; color:#0059b3; margin-bottom:10px; }
h3 { color:#007BFF; margin-top:18px; border-left:5px solid #007BFF; padding-left:8px; }
table { width:100%; border-collapse: collapse; margin-top:6px; margin-bottom:10px; }
th, td { border:1px solid #ccc; padding:6px; text-align:left; }
th { background-color:#007BFF; color:white; }
.section { margin-bottom:20px; }
.label { color:#0059b3; font-weight:bold; width:30%; }
.summary { background-color:#f8faff; border:1px solid #007BFF; padding:10px; border-radius:8px; }
p { margin:3px 0; }
";

// 🟦 Contenido HTML
$html = "
<h2>Historial Clínico del Paciente</h2>

<div class='section'>
    <h3>Datos del Paciente</h3>
    <table>
        <tr><td class='label'>Nombre:</td><td>{$paciente['nombre']}</td></tr>
        <tr><td class='label'>CI:</td><td>{$paciente['ci']}</td></tr>
        <tr><td class='label'>Fecha de Nacimiento:</td><td>{$paciente['fechaNac']}</td></tr>
        <tr><td class='label'>Teléfono:</td><td>{$paciente['telCel']}</td></tr>
        <tr><td class='label'>Dirección:</td><td>{$paciente['domicilio']}</td></tr>
        <tr><td class='label'>Sexo:</td><td>{$paciente['genero']}</td></tr>";

if ($paciente['tutor']) {
    $html .= "<tr><td class='label'>Tutor:</td><td>{$paciente['tutor']} ({$paciente['relacion']})</td></tr>";
}

$html .= "</table></div>";

// 🟦 Historial de Citas
$html .= "<div class='section'><h3>Historial de Citas</h3>";
if ($citasHistorial) {
    $html .= "<table>
        <thead><tr><th>Fecha</th><th>Hora</th><th>Motivo</th><th>Odontólogo</th><th>Estado</th></tr></thead><tbody>";
    foreach ($citasHistorial as $c) {
        $html .= "<tr>
            <td>{$c['fecha']}</td>
            <td>{$c['hora']}</td>
            <td>{$c['motivoConsulta']}</td>
            <td>{$c['odontologo']}</td>
            <td>{$c['estado']}</td>
        </tr>";
    }
    $html .= "</tbody></table>";
} else {
    $html .= "<p>No hay citas registradas.</p>";
}
$html .= "</div>";

// 🟦 Tratamientos
$html .= "<div class='section'><h3>Tratamientos</h3>";
if ($tratamientosHistorial) {
    foreach ($tratamientosHistorial as $t) {
        $html .= "
        <div style='margin-bottom:10px; border:1px solid #ccc; padding:8px; border-radius:6px; background-color:#fdfdfd;'>
            <p><b>Fecha:</b> {$t['fechaRegistro']} | <b>Odontólogo:</b> {$t['odontologo']}</p>
            <p><b>Total:</b> {$t['totalPago']} | <b>Saldo:</b> {$t['saldo']} | <b>Estado:</b> {$t['estado']}</p>";

        $servs = Conexion::conectar()->prepare("
            SELECT s.nombreServicio
            FROM detalletratamientoservicios dts
            INNER JOIN servicios s ON s.idServicio = dts.idServicio
            WHERE dts.idTratamiento = :idTratamiento
        ");
        $servs->execute(['idTratamiento' => $t['idTratamiento']]);
        $servicios = $servs->fetchAll(PDO::FETCH_ASSOC);
        $servStr = $servicios ? implode(', ', array_column($servicios, 'nombreServicio')) : 'Sin servicios registrados';

        $html .= "<p><b>Servicios realizados:</b> $servStr</p></div>";
    }
} else {
    $html .= "<p>No hay tratamientos registrados.</p>";
}
$html .= "</div>";

// 🟦 Medicamentos
$html .= "<div class='section'><h3>Medicamentos Recetados</h3>";
if ($medicamentosHistorial) {
    $html .= "<table><thead><tr><th>Medicamento</th><th>Dosis</th><th>Inicio</th><th>Fin</th><th>Observación</th></tr></thead><tbody>";
    foreach ($medicamentosHistorial as $m) {
        $html .= "<tr>
            <td>{$m['medicamento']}</td>
            <td>{$m['dosis']}</td>
            <td>{$m['fechaInicio']}</td>
            <td>{$m['fechaFinal']}</td>
            <td>{$m['observacion']}</td>
        </tr>";
    }
    $html .= "</tbody></table>";
} else {
    $html .= "<p>No hay medicamentos recetados.</p>";
}
$html .= "</div>";

// 🟦 Pagos
$html .= "<div class='section'><h3>Pagos Realizados</h3>";
if ($pagosHistorial) {
    $html .= "<table><thead><tr><th>Fecha</th><th>Monto</th><th>Forma de Pago</th><th>Descripción</th></tr></thead><tbody>";
    foreach ($pagosHistorial as $p) {
        $html .= "<tr>
            <td>{$p['fecha']}</td>
            <td>{$p['monto']}</td>
            <td>{$p['nombreTipoPago']}</td>
            <td>{$p['descripcion']}</td>
        </tr>";
    }
    $html .= "</tbody></table>";
} else {
    $html .= "<p>No hay pagos registrados.</p>";
}
$html .= "</div>";

// 🟦 Resumen
$totalTratamientos = count($tratamientosHistorial);
$totalPagado = array_sum(array_column($pagosHistorial, 'monto'));
$saldoPendiente = array_sum(array_column($tratamientosHistorial, 'saldo'));
$ultimaCita = end($citasHistorial)['fecha'] ?? '-';
$ultimoTrat = end($tratamientosHistorial)['fechaRegistro'] ?? '-';

$html .= "
<div class='section summary'>
    <h3>Resumen Final</h3>
    <p><b>Total de tratamientos:</b> $totalTratamientos</p>
    <p><b>Total pagado:</b> Bs. $totalPagado</p>
    <p><b>Saldo pendiente:</b> Bs. $saldoPendiente</p>
    <p><b>Última cita:</b> $ultimaCita</p>
    <p><b>Último tratamiento:</b> $ultimoTrat</p>
</div>
";

// 🟦 Generar PDF
$mpdf->WriteHTML($css, 1);
$mpdf->WriteHTML($html, 2);
$mpdf->Output("Historial_{$paciente['nombre']}.pdf", "I");
//DESCARGA DIRECTA -> $mpdf->Output("Historial_{$paciente['nombre']}.pdf", "D");
?>
