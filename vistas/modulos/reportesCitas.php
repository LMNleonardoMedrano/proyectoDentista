<?php
require_once '../../vendor/autoload.php';
require_once '../../controladores/citas.controlador.php';
require_once '../../modelos/citas.modelo.php';

use Mpdf\Mpdf;

// Captura parámetros
$tipo = $_GET['tipoReporteCitas'] ?? null;
$desde = $_GET['desdeCitas'] ?? null;
$hasta = $_GET['hastaCitas'] ?? null;

if (!$tipo) {
    die("Debe seleccionar un tipo de reporte.");
}

$mpdf = new Mpdf();
$mpdf->SetTitle('Reporte de Citas - ' . ucfirst($tipo));

$datos = [];
$tituloReporte = '';

// Según tipo de reporte, obtiene los datos desde el controlador
switch ($tipo) {
    case "programadas":
        $datos = ControladorCitas::ctrCitasProgramadas($desde, $hasta);
        $tituloReporte = "Citas Programadas";
        break;

    case "confirmadas":
        $datos = ControladorCitas::ctrCitasConfirmadas($desde, $hasta);
        $tituloReporte = "Citas Confirmadas";
        break;

    case "atendidos":
        $datos = ControladorCitas::ctrPacientesAtendidos($desde, $hasta);
        $tituloReporte = "Pacientes Atendidos";
        break;

    case "canceladas":
        $datos = ControladorCitas::ctrCitasCanceladas($desde, $hasta);
        $tituloReporte = "Citas Canceladas";
        break;

    case "porDia":
        $datos = ControladorCitas::ctrCitasPorDia($desde, $hasta);
        $tituloReporte = "Citas por Día";
        break;

    case "porOdontologo":
        $datos = ControladorCitas::ctrCitasPorOdontologo($desde, $hasta);
        $tituloReporte = "Citas por Odontólogo";
        break;

    case "porServicio":
        $datos = ControladorCitas::ctrCitasPorServicio($desde, $hasta);
        $tituloReporte = "Citas por Servicio";
        break;

    case "mensual":
        $datos = ControladorCitas::ctrCitasMensuales();
        $tituloReporte = "Citas Mensuales";
        break;

    default:
        die("Tipo de reporte no válido.");
}

// Verifica si hay resultados
if (empty($datos)) {
    echo "<h3 style='text-align:center;'>No hay datos disponibles para este reporte.</h3>";
    exit;
}

// Encabezado general del PDF
$html = '
<h2 style="text-align:center; font-family: Arial;">REPORTE DE ' . strtoupper($tituloReporte) . '</h2>
<p style="text-align:center; font-family: Arial; font-size:12px;">Desde: ' . ($desde ?: '-') . ' | Hasta: ' . ($hasta ?: '-') . '</p>
<br>
<table style="width:100%; border-collapse:collapse; font-family: Arial; font-size:12px;">
<thead style="background-color:#007BFF; color:white;">';

// Genera columnas dinámicas según tipo
switch ($tipo) {
    case "programadas":
        $html .= '
        <tr>
            <th style="border:1px solid #ccc; padding:6px;">Fecha</th>
            <th style="border:1px solid #ccc; padding:6px;">Hora</th>
            <th style="border:1px solid #ccc; padding:6px;">Paciente</th>
            <th style="border:1px solid #ccc; padding:6px;">Odontólogo</th>
        </tr>
        </thead><tbody>';
        foreach ($datos as $c) {
            $html .= '<tr>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['fecha_cita'] . '</td>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['hora'] . '</td>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['nombre_paciente'] . '</td>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['nombre_odontologo'] . '</td>
            </tr>';
        }
        break;

    case "confirmadas":
        $html .= '
        <tr>
            <th style="border:1px solid #ccc; padding:6px;">Paciente</th>
            <th style="border:1px solid #ccc; padding:6px;">Fecha</th>
            <th style="border:1px solid #ccc; padding:6px;">Odontólogo</th>
        </tr>
        </thead><tbody>';
        foreach ($datos as $c) {
            $html .= '<tr>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['nombre_paciente'] . '</td>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['fecha_cita'] . '</td>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['nombre_odontologo'] . '</td>
            </tr>';
        }
        break;

    case "atendidos":
        $html .= '
        <tr>
            <th style="border:1px solid #ccc; padding:6px;">Odontólogo</th>
            <th style="border:1px solid #ccc; padding:6px;">Total Atendidos</th>
        </tr>
        </thead><tbody>';
        foreach ($datos as $a) {
            $html .= '<tr>
                <td style="border:1px solid #ccc; padding:6px;">' . $a['nombre_odontologo'] . '</td>
                <td style="border:1px solid #ccc; padding:6px; text-align:center;">' . $a['total_atendidos'] . '</td>
            </tr>';
        }
        break;

    case "canceladas":
        $html .= '
        <tr>
            <th style="border:1px solid #ccc; padding:6px;">Paciente</th>
            <th style="border:1px solid #ccc; padding:6px;">Motivo de Cancelación</th>
        </tr>
        </thead><tbody>';
        foreach ($datos as $c) {
            $html .= '<tr>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['nombre_paciente'] . '</td>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['motivo_cancelacion'] . '</td>
            </tr>';
        }
        break;

    case "porDia":
        $html .= '
        <tr>
            <th style="border:1px solid #ccc; padding:6px;">Fecha</th>
            <th style="border:1px solid #ccc; padding:6px;">Cantidad</th>
        </tr>
        </thead><tbody>';
        foreach ($datos as $d) {
            $html .= '<tr>
                <td style="border:1px solid #ccc; padding:6px;">' . $d['fecha_cita'] . '</td>
                <td style="border:1px solid #ccc; padding:6px; text-align:center;">' . $d['cantidad'] . '</td>
            </tr>';
        }
        break;

    case "porOdontologo":
        $html .= '
        <tr>
            <th style="border:1px solid #ccc; padding:6px;">Odontólogo</th>
            <th style="border:1px solid #ccc; padding:6px;">Cantidad</th>
        </tr>
        </thead><tbody>';
        foreach ($datos as $o) {
            $html .= '<tr>
                <td style="border:1px solid #ccc; padding:6px;">' . $o['nombre_odontologo'] . '</td>
                <td style="border:1px solid #ccc; padding:6px; text-align:center;">' . $o['cantidad'] . '</td>
            </tr>';
        }
        break;

    case "porServicio":
        $html .= '
        <tr>
            <th style="border:1px solid #ccc; padding:6px;">Servicio</th>
            <th style="border:1px solid #ccc; padding:6px;">Cantidad</th>
        </tr>
        </thead><tbody>';
        foreach ($datos as $s) {
            $html .= '<tr>
                <td style="border:1px solid #ccc; padding:6px;">' . $s['nombre_servicio'] . '</td>
                <td style="border:1px solid #ccc; padding:6px; text-align:center;">' . $s['cantidad'] . '</td>
            </tr>';
        }
        break;

    case "mensual":
        $html .= '
        <tr>
            <th style="border:1px solid #ccc; padding:6px;">Mes / Año</th>
            <th style="border:1px solid #ccc; padding:6px;">Cantidad</th>
        </tr>
        </thead><tbody>';
        foreach ($datos as $m) {
            $html .= '<tr>
                <td style="border:1px solid #ccc; padding:6px;">' . $m['mes'] . '/' . $m['anio'] . '</td>
                <td style="border:1px solid #ccc; padding:6px; text-align:center;">' . $m['cantidad'] . '</td>
            </tr>';
        }
        break;
}

$html .= '
</tbody></table>
<br><p style="text-align:right; font-size:11px; color:#555;">
Generado el ' . date("d/m/Y H:i") . '
</p>';

$mpdf->WriteHTML($html);
$mpdf->Output('reporte_citas_' . $tipo . '.pdf', 'I');
?>
