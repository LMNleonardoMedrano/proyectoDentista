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
            <th style="border:1px solid #ccc; padding:6px;">Hora Fin</th>
            <th style="border:1px solid #ccc; padding:6px;">Motivo de la Consulta</th>
            <th style="border:1px solid #ccc; padding:6px;">Paciente</th>
            <th style="border:1px solid #ccc; padding:6px;">Odontólogo</th>
        </tr>
        </thead><tbody>';
        foreach ($datos as $c) {
            $html .= '<tr>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['fecha_cita'] . '</td>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['hora'] . '</td>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['horaFin'] . '</td>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['motivoConsulta'] . '</td>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['nombre_paciente'] . '</td>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['nombre_odontologo'] . '</td>
            </tr>';
        }
        break;

    case "confirmadas":
        $html .= '
        <tr>
             <th style="border:1px solid #ccc; padding:6px;">Fecha</th>
            <th style="border:1px solid #ccc; padding:6px;">Hora</th>
            <th style="border:1px solid #ccc; padding:6px;">Hora Fin</th>
            <th style="border:1px solid #ccc; padding:6px;">Motivo de la Consulta</th>
            <th style="border:1px solid #ccc; padding:6px;">Paciente</th>
            <th style="border:1px solid #ccc; padding:6px;">Odontólogo</th>
        </tr>
        </thead><tbody>';
        foreach ($datos as $c) {
            $html .= '<tr>
               <td style="border:1px solid #ccc; padding:6px;">' . $c['fecha_cita'] . '</td>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['hora'] . '</td>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['horaFin'] . '</td>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['motivoConsulta'] . '</td>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['nombre_paciente'] . '</td>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['nombre_odontologo'] . '</td>
            </tr>';
        }
        break;

    case "atendidos":
    $odontologoActual = '';
$totalOdontologo = 0;

$html .= '
<tr>
    <th style="border:1px solid #ccc; padding:6px;">Odontólogo</th>
    <th style="border:1px solid #ccc; padding:6px;">Paciente</th>
    <th style="border:1px solid #ccc; padding:6px;">Fecha</th>
</tr>
</thead><tbody>';

foreach ($datos as $index => $a) {
    // Si es un nuevo odontólogo, mostrar su encabezado
    if ($odontologoActual !== $a['nombre_odontologo']) {
        $odontologoActual = $a['nombre_odontologo'];
        $html .= '<tr style="background-color:#f2f2f2; font-weight:bold;">
            <td colspan="3" style="border:1px solid #ccc; padding:6px;">' . $odontologoActual . '</td>
        </tr>';
    }

    // Fila del paciente
    $html .= '<tr>
        <td style="border:1px solid #ccc; padding:6px;"></td>
        <td style="border:1px solid #ccc; padding:6px;">' . $a['nombre_paciente'] . '</td>
        <td style="border:1px solid #ccc; padding:6px; text-align:center;">' . $a['fecha_cita'] . '</td>
    </tr>';

    $totalOdontologo++;

    // Si es el último registro o cambia el odontólogo en la siguiente iteración, imprime el total
    if (!isset($datos[$index + 1]) || $datos[$index + 1]['nombre_odontologo'] !== $odontologoActual) {
      $html .= '<tr style="background-color:#d1ecf1; font-weight:bold;">
    <td colspan="2" style="border:1px solid #ccc; padding:6px; text-align:left;">Total atendidos:</td>
    <td style="border:1px solid #ccc; padding:6px; text-align:right;">' . $totalOdontologo . '</td>
</tr>';
        $totalOdontologo = 0;
    }
}
    break;


    case "canceladas":
        $html .= '
        <tr>
            <th style="border:1px solid #ccc; padding:6px;">Paciente</th>
            <th style="border:1px solid #ccc; padding:6px;">Motivo de Cancelación</th>
            <th style="border:1px solid #ccc; padding:6px;">Fecha de la cita</th>
            <th style="border:1px solid #ccc; padding:6px;">odontologo</th>
            
        </tr>
        </thead><tbody>';
        foreach ($datos as $c) {
            $html .= '<tr>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['nombre_paciente'] . '</td>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['motivo_cancelacion'] . '</td>
                <td style="border:1px solid #ccc; padding:6px; text-align:center;">' . $c['fecha_cita'] . '</td>
                <td style="border:1px solid #ccc; padding:6px;">' . $c['nombre_odontologo'] . '</td>
            </tr>';
        }
        break;

   case "porDia":
    $html .= '
    <tr>
        <th style="border:1px solid #ccc; padding:6px;">Fecha</th>
        <th style="border:1px solid #ccc; padding:6px;">Cantidad</th>
        <th style="border:1px solid #ccc; padding:6px;">Odontólogos</th>
        <th style="border:1px solid #ccc; padding:6px;">Estados</th>
    </tr>
    </thead><tbody>';

    foreach ($datos as $d) {
        // Preparar listado de odontólogos
        $odontologos = '';
        if (!empty($d['odontologos'])) {
            foreach ($d['odontologos'] as $o) {
                $odontologos .= $o['nombre'] . ' (' . $o['cantidad'] . ')<br>';
            }
        }

        // Preparar listado de estados
        $estados = '';
        if (!empty($d['estados'])) {
            foreach ($d['estados'] as $estado => $cantidad) {
                $estados .= ucfirst($estado) . ' (' . $cantidad . ')<br>';
            }
        }

        $html .= '<tr>
            <td style="border:1px solid #ccc; padding:6px;">' . $d['fecha_cita'] . '</td>
            <td style="border:1px solid #ccc; padding:6px; text-align:center;">' . $d['cantidad'] . '</td>
            <td style="border:1px solid #ccc; padding:6px;">' . $odontologos . '</td>
            <td style="border:1px solid #ccc; padding:6px;">' . $estados . '</td>
        </tr>';
    }
    break;


   case "porOdontologo":
    // Agrupar los datos por odontólogo
    $agrupados = [];

    foreach ($datos as $o) {
        $nombre = $o['nombre_odontologo'];
        $estado = $o['estado'];
        $fecha = $o['fecha_cita'];

        if (!isset($agrupados[$nombre])) {
            $agrupados[$nombre] = [
                'cantidad' => 0,
                'estados' => [],
                'por_fecha' => []
            ];
        }

        $agrupados[$nombre]['cantidad']++;

        // Agrupar por estado
        if (!isset($agrupados[$nombre]['estados'][$estado])) {
            $agrupados[$nombre]['estados'][$estado] = 0;
        }
        $agrupados[$nombre]['estados'][$estado]++;

        // Agrupar por fecha
        if (!isset($agrupados[$nombre]['por_fecha'][$fecha])) {
            $agrupados[$nombre]['por_fecha'][$fecha] = 0;
        }
        $agrupados[$nombre]['por_fecha'][$fecha]++;
    }

    // Encabezado de la tabla
    $html .= '
    <tr>
        <th style="border:1px solid #ccc; padding:6px;">Odontólogo</th>
        <th style="border:1px solid #ccc; padding:6px;">Cantidad</th>
        <th style="border:1px solid #ccc; padding:6px;">Estados</th>
        <th style="border:1px solid #ccc; padding:6px;">Por día</th>
    </tr>
    </thead><tbody>';

    // Filas por odontólogo
    foreach ($agrupados as $nombre => $info) {
        $detalleEstados = '';
        foreach ($info['estados'] as $estado => $cantidad) {
            $detalleEstados .= '<b>' . ucfirst($estado) . ':</b> ' . $cantidad . '<br>';
        }

        $detalleFechas = '';
        foreach ($info['por_fecha'] as $fecha => $cantidad) {
            $detalleFechas .= '<b>' . $fecha . ':</b> ' . $cantidad . ' cita(s)<br>';
        }

        $html .= '<tr>
            <td style="border:1px solid #ccc; padding:6px;">' . $nombre . '</td>
            <td style="border:1px solid #ccc; padding:6px; text-align:center;">' . $info['cantidad'] . '</td>
            <td style="border:1px solid #ccc; padding:6px;">' . $detalleEstados . '</td>
            <td style="border:1px solid #ccc; padding:6px;">' . $detalleFechas . '</td>
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
$html .= '<p style="text-align:right; font-weight:bold;">TOTAL DE ' . strtoupper($tituloReporte) . ': ' . count($datos) . '</p>';
$mpdf->WriteHTML($html);
$mpdf->Output('reporte_citas_' . $tipo . '.pdf', 'I');
?>
