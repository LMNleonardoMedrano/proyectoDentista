<?php
require_once '../../vendor/autoload.php';
require_once '../../controladores/tratamiento.controlador.php';
require_once '../../modelos/tratamiento.modelo.php';

use Mpdf\Mpdf;

// Captura parámetros
$tipo = $_GET['tipoReporteTratamientos'] ?? null;
$desde = $_GET['desdeTrat'] ?? null;
$hasta = $_GET['hastaTrat'] ?? null;

if (!$tipo) {
    die("Debe seleccionar un tipo de reporte.");
}

$mpdf = new Mpdf();
$mpdf->SetTitle('Reporte de Tratamientos - ' . ucfirst($tipo));

$datos = [];
$tituloReporte = '';

// Obtener datos según tipo de reporte
switch ($tipo) {
    case "completados":
        $datos = ControladorTratamiento::ctrTratamientosCompletados($desde, $hasta);
        $tituloReporte = "Tratamientos Completados";
        break;
    case "parciales":
        $datos = ControladorTratamiento::ctrTratamientosParciales($desde, $hasta);
        $tituloReporte = "Tratamientos Parciales";
        break;
    case "activos":
        $datos = ControladorTratamiento::ctrTratamientosActivos($desde, $hasta);
        $tituloReporte = "Tratamientos Activos";
        break;
    case "noCancelados":
        $datos = ControladorTratamiento::ctrTratamientosNoCancelados($desde, $hasta);
        $tituloReporte = "Tratamientos No Cancelados";
        break;
    case "porOdontologo":
        $datos = ControladorTratamiento::ctrTratamientosPorOdontologo($desde, $hasta);
        $tituloReporte = "Tratamientos por Odontólogo";
        break;
    case "porServicio":
        $datos = ControladorTratamiento::ctrTratamientosPorServicio($desde, $hasta);
        $tituloReporte = "Tratamientos por Servicio";
        break;
    case "porEstado":
        $datos = ControladorTratamiento::ctrTratamientosPorEstado($desde, $hasta);
        $tituloReporte = "Tratamientos por Estado";
        break;
    case "mensual":
        $datos = ControladorTratamiento::ctrTratamientosMensuales();
        $tituloReporte = "Tratamientos Mensuales";
        break;
    default:
        die("Tipo de reporte no válido.");
}

// Verifica si hay resultados
if (empty($datos)) {
    echo "<h3 style='text-align:center;'>No hay datos disponibles para este reporte.</h3>";
    exit;
}

// Cabecera del PDF
$html = '
<h2 style="text-align:center; font-family: Arial;">REPORTE DE ' . strtoupper($tituloReporte) . '</h2>
<p style="text-align:center; font-family: Arial; font-size:12px;">Desde: ' . ($desde ?: '-') . ' | Hasta: ' . ($hasta ?: '-') . '</p>
<br>
<table style="width:100%; border-collapse:collapse; font-family: Arial; font-size:12px;">
<thead style="background-color:#007BFF; color:white;">';

// Genera columnas según tipo
switch ($tipo) {
    case "completados":
        $html .= '
        <tr>
            <th style="border:1px solid #ccc; padding:6px;">Paciente</th>
            <th style="border:1px solid #ccc; padding:6px;">Servicio</th>
            <th style="border:1px solid #ccc; padding:6px;">Fecha Finalización</th>
        </tr></thead><tbody>';
        foreach ($datos as $t) {
            $html .= '<tr>
                <td style="border:1px solid #ccc; padding:6px;">' . $t['nombre_paciente'] . '</td>
                <td style="border:1px solid #ccc; padding:6px;">' . $t['nombre_servicio'] . '</td>
                <td style="border:1px solid #ccc; padding:6px;">' . $t['fecha_fin'] . '</td>
            </tr>';
        }
        break;

    case "parciales":
        $html .= '
        <tr>
            <th style="border:1px solid #ccc; padding:6px;">Paciente</th>
            <th style="border:1px solid #ccc; padding:6px;">Progreso (%)</th>
        </tr></thead><tbody>';
        foreach ($datos as $t) {
            $html .= '<tr>
                <td style="border:1px solid #ccc; padding:6px;">' . $t['nombre_paciente'] . '</td>
                <td style="border:1px solid #ccc; padding:6px; text-align:center;">' . $t['progreso'] . '</td>
            </tr>';
        }
        break;

    case "activos":
        $html .= '
        <tr>
            <th style="border:1px solid #ccc; padding:6px;">Paciente</th>
            <th style="border:1px solid #ccc; padding:6px;">Servicio</th>
        </tr></thead><tbody>';
        foreach ($datos as $t) {
            $html .= '<tr>
                <td style="border:1px solid #ccc; padding:6px;">' . $t['nombre_paciente'] . '</td>
                <td style="border:1px solid #ccc; padding:6px;">' . $t['nombre_servicio'] . '</td>
            </tr>';
        }
        break;

    case "noCancelados":
        $html .= '
        <tr>
            <th style="border:1px solid #ccc; padding:6px;">Paciente</th>
            <th style="border:1px solid #ccc; padding:6px;">Estado</th>
        </tr></thead><tbody>';
        foreach ($datos as $t) {
            $html .= '<tr>
                <td style="border:1px solid #ccc; padding:6px;">' . $t['nombre_paciente'] . '</td>
                <td style="border:1px solid #ccc; padding:6px;">' . $t['estado'] . '</td>
            </tr>';
        }
        break;

    case "porOdontologo":
        $html .= '
        <tr>
            <th style="border:1px solid #ccc; padding:6px;">Odontólogo</th>
            <th style="border:1px solid #ccc; padding:6px;">Cantidad de Tratamientos</th>
        </tr></thead><tbody>';
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
        </tr></thead><tbody>';
        foreach ($datos as $s) {
            $html .= '<tr>
                <td style="border:1px solid #ccc; padding:6px;">' . $s['nombreServicio'] . '</td>
                <td style="border:1px solid #ccc; padding:6px; text-align:center;">' . $s['cantidad'] . '</td>
            </tr>';
        }
        break;

    case "porEstado":
        $html .= '
        <tr>
            <th style="border:1px solid #ccc; padding:6px;">Estado</th>
            <th style="border:1px solid #ccc; padding:6px;">Cantidad</th>
        </tr></thead><tbody>';
        foreach ($datos as $e) {
            $html .= '<tr>
                <td style="border:1px solid #ccc; padding:6px;">' . $e['estado'] . '</td>
                <td style="border:1px solid #ccc; padding:6px; text-align:center;">' . $e['cantidad'] . '</td>
            </tr>';
        }
        break;

    case "mensual":
        $html .= '
        <tr>
            <th style="border:1px solid #ccc; padding:6px;">Mes / Año</th>
            <th style="border:1px solid #ccc; padding:6px;">Cantidad</th>
        </tr></thead><tbody>';
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
$mpdf->Output('reporte_tratamientos_' . $tipo . '.pdf', 'I');
