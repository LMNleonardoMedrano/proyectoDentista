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

// Obtiene los datos según el tipo
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

if (empty($datos)) {
    echo "<h3 style='text-align:center;'>No hay datos disponibles para este reporte.</h3>";
    exit;
}

// -------------------------
// ESTILOS GLOBALES
// -------------------------
$stylesheet = "
    body { font-family: Arial; font-size: 12px; }
    .header-table { width: 100%; border-bottom: 2px solid #007BFF; margin-bottom: 15px; }
    .header-table td { vertical-align: middle; }
    .logo { width: 100px; }
    .header-title { text-align: center; font-size: 16px; font-weight: bold; color: #007BFF; }
    .header-fechas { text-align: right; font-size: 11px; color: #555; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 11px; }
    thead { background: #007BFF; color: white; }
    th, td { border: 1px solid #ccc; padding: 7px; }
    tbody tr:nth-child(even) { background: #f7f7f7; }
    .total { font-weight: bold; text-align: right; margin-top: 10px; font-size: 13px; }
";

$mpdf->WriteHTML($stylesheet, 1);

// -------------------------
// ENCABEZADO CON TABLA (LOGO | TÍTULO | FECHAS)
// -------------------------
$html = '
<table class="header-table">
    <tr>
        <td style="width:100px;"><img src="../../vistas/src/img/logo7.5.png" class="logo"></td>
        <td class="header-title">SISTEMA DE GESTIÓN ODONTOLÓGICA</td>
        <td class="header-fechas">Desde: ' . ($desde ?: '-') . ' | Hasta: ' . ($hasta ?: '-') . '</td>
    </tr>
</table>

<h2 style="text-align:center; color:#333;">REPORTE DE ' . strtoupper($tituloReporte) . '</h2>

<table>
<thead>';

// -------------------------------------------------
//  COLUMNAS Y TABLAS SEGÚN TIPO
// -------------------------------------------------
switch ($tipo) {

    case "programadas":
        $html .= '
        <tr>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Hora Fin</th>
            <th>Motivo de la Consulta</th>
            <th>Paciente</th>
            <th>Odontólogo</th>
        </tr>
        </thead><tbody>';

        foreach ($datos as $c) {
            $html .= "
            <tr>
                <td>{$c['fecha_cita']}</td>
                <td>{$c['hora']}</td>
                <td>{$c['horaFin']}</td>
                <td>{$c['motivoConsulta']}</td>
                <td>{$c['nombre_paciente']}</td>
                <td>{$c['nombre_odontologo']}</td>
            </tr>";
        }
        break;

    case "confirmadas":
        $html .= '
        <tr>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Hora Fin</th>
            <th>Motivo</th>
            <th>Paciente</th>
            <th>Odontólogo</th>
        </tr>
        </thead><tbody>';
        
        foreach ($datos as $c) {
            $html .= "
            <tr>
                <td>{$c['fecha_cita']}</td>
                <td>{$c['hora']}</td>
                <td>{$c['horaFin']}</td>
                <td>{$c['motivoConsulta']}</td>
                <td>{$c['nombre_paciente']}</td>
                <td>{$c['nombre_odontologo']}</td>
            </tr>";
        }
        break;

    case "atendidos":
        $odontologoActual = '';
        $totalOdontologo = 0;

        $html .= '
        <tr>
            <th>Odontólogo</th>
            <th>Paciente</th>
            <th>Fecha</th>
        </tr>
        </thead><tbody>';

        foreach ($datos as $index => $a) {
            if ($odontologoActual !== $a['nombre_odontologo']) {
                $odontologoActual = $a['nombre_odontologo'];

                $html .= '
                <tr style="background-color:#f2f2f2; font-weight:bold;">
                    <td colspan="3">' . $odontologoActual . '</td>
                </tr>';
            }

            $html .= '
            <tr>
                <td></td>
                <td>' . $a['nombre_paciente'] . '</td>
                <td style="text-align:center;">' . $a['fecha_cita'] . '</td>
            </tr>';

            $totalOdontologo++;

            if (!isset($datos[$index + 1]) || $datos[$index + 1]['nombre_odontologo'] !== $odontologoActual) {
                $html .= '
                <tr style="background-color:#d1ecf1; font-weight:bold;">
                    <td colspan="2">Total atendidos:</td>
                    <td style="text-align:right;">' . $totalOdontologo . '</td>
                </tr>';
                $totalOdontologo = 0;
            }
        }
        break;

    case "canceladas":
        $html .= '
        <tr>
            <th>Paciente</th>
            <th>Motivo de Cancelación</th>
            <th>Fecha</th>
            <th>Odontólogo</th>
        </tr>
        </thead><tbody>';

        foreach ($datos as $c) {
            $html .= "
            <tr>
                <td>{$c['nombre_paciente']}</td>
                <td>{$c['motivo_cancelacion']}</td>
                <td style='text-align:center;'>{$c['fecha_cita']}</td>
                <td>{$c['nombre_odontologo']}</td>
            </tr>";
        }
        break;

    case "porDia":
        $html .= '
        <tr>
            <th>Fecha</th>
            <th>Cantidad</th>
            <th>Odontólogos</th>
            <th>Estados</th>
        </tr>
        </thead><tbody>';

        foreach ($datos as $d) {
            $odontologos = '';
            if (!empty($d['odontologos'])) {
                foreach ($d['odontologos'] as $o) {
                    $odontologos .= $o['nombre'] . " ({$o['cantidad']})<br>";
                }
            }

            $estados = '';
            if (!empty($d['estados'])) {
                foreach ($d['estados'] as $estado => $cant) {
                    $estados .= ucfirst($estado) . " ($cant)<br>";
                }
            }

            $html .= "
            <tr>
                <td>{$d['fecha_cita']}</td>
                <td style='text-align:center;'>{$d['cantidad']}</td>
                <td>{$odontologos}</td>
                <td>{$estados}</td>
            </tr>";
        }
        break;

    case "porOdontologo":
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
            if (!isset($agrupados[$nombre]['estados'][$estado])) $agrupados[$nombre]['estados'][$estado] = 0;
            $agrupados[$nombre]['estados'][$estado]++;

            if (!isset($agrupados[$nombre]['por_fecha'][$fecha])) $agrupados[$nombre]['por_fecha'][$fecha] = 0;
            $agrupados[$nombre]['por_fecha'][$fecha]++;
        }

        $html .= '
        <tr>
            <th>Odontólogo</th>
            <th>Cantidad</th>
            <th>Estados</th>
            <th>Por día</th>
        </tr>
        </thead><tbody>';

        foreach ($agrupados as $nombre => $info) {
            $detalleEstados = '';
            foreach ($info['estados'] as $estado => $cant) {
                $detalleEstados .= "<b>" . ucfirst($estado) . ":</b> $cant<br>";
            }

            $detalleFechas = '';
            foreach ($info['por_fecha'] as $fecha => $cant) {
                $detalleFechas .= "<b>$fecha:</b> $cant cita(s)<br>";
            }

            $html .= "
            <tr>
                <td>$nombre</td>
                <td style='text-align:center;'>{$info['cantidad']}</td>
                <td>$detalleEstados</td>
                <td>$detalleFechas</td>
            </tr>";
        }
        break;

    case "porServicio":
        $html .= '
        <tr>
            <th>Servicio</th>
            <th>Cantidad</th>
        </tr>
        </thead><tbody>';

        foreach ($datos as $s) {
            $html .= "
            <tr>
                <td>{$s['nombre_servicio']}</td>
                <td style='text-align:center;'>{$s['cantidad']}</td>
            </tr>";
        }
        break;

    case "mensual":
        $html .= '
        <tr>
            <th>Mes / Año</th>
            <th>Cantidad</th>
        </tr>
        </thead><tbody>';

        foreach ($datos as $m) {
            $html .= "
            <tr>
                <td>{$m['mes']}/{$m['anio']}</td>
                <td style='text-align:center;'>{$m['cantidad']}</td>
            </tr>";
        }
        break;
}

$html .= "
</tbody>
</table>

<p class='total'>
    TOTAL DE " . strtoupper($tituloReporte) . ": " . count($datos) . "
</p>
";

$mpdf->WriteHTML($html);
$mpdf->Output('reporte_citas_' . $tipo . '.pdf', 'I');
?>
