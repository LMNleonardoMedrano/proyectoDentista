<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/dentista/controladores/citas.controlador.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dentista/modelos/citas.modelo.php';

$tipo = $_GET['tipoReporteCitas'] ?? null;
$desde = $_GET['desdeCitas'] ?? null;
$hasta = $_GET['hastaCitas'] ?? null;

if (!$tipo) {
    echo '<div class="col-12"><div class="alert alert-info text-center">Seleccione un reporte.</div></div>';
    exit;
}

function generarTarjeta($titulo, $contenido, $fecha = null)
{
    $fechaHTML = $fecha ? "<small class='text-muted d-block mb-2'><i class='fas fa-calendar-alt'></i> {$fecha}</small>" : "";
    return "
    <div class='col-12 col-md-6 col-lg-4'>
        <div class='card shadow-sm mb-3 bg-white border-start border-primary border-3'>
            <div class='card-body'>
                {$fechaHTML}
                <h5 class='card-title fw-bold text-primary'>{$titulo}</h5>
                <hr>
                <p class='card-text'>{$contenido}</p>
            </div>
        </div>
    </div>";
}

switch ($tipo) {

    case "programadas":
        $citas = ControladorCitas::ctrCitasProgramadas($desde, $hasta);
        if (!empty($citas)) {
            foreach ($citas as $c) {
                $contenido = "
                    <b>Hora:</b> {$c['hora']}<br>
                    <b>Hora de Fin:</b> {$c['horaFin']}<br>
                    <b>Paciente:</b> {$c['nombre_paciente']}<br>
                    <b>Odontólogo:</b> {$c['nombre_odontologo']}<br>
                    
                    <b>Estado:</b> Programada
                ";
                echo generarTarjeta("Cita Programada", $contenido, $c['fecha_cita']);
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay citas programadas en este rango.</div></div>";
        }
        break;

    case "confirmadas":
        $confirmadas = ControladorCitas::ctrCitasConfirmadas($desde, $hasta);
        if (!empty($confirmadas)) {
            foreach ($confirmadas as $c) {
                $contenido = "
                    <b>Hora:</b> {$c['hora']}<br>
                    <b>Hora de fin:</b> {$c['horaFin']}<br>
                    <b>Paciente:</b> {$c['nombre_paciente']}<br>
                    <b>Odontólogo:</b> {$c['nombre_odontologo']}<br>

                    <b>Estado:</b> Confirmada
                ";
                echo generarTarjeta("Cita Confirmada", $contenido, $c['fecha_cita']);
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay pacientes confirmados.</div></div>";
        }
        break;

    case "atendidos":
    $atendidos = ControladorCitas::ctrPacientesAtendidos($desde, $hasta);

    if (!empty($atendidos)) {
        // Contador de pacientes por odontólogo
        $totales = [];
        foreach ($atendidos as $a) {
            $odontologo = $a['nombre_odontologo'];
            if (!isset($totales[$odontologo])) {
                $totales[$odontologo] = 0;
            }
            $totales[$odontologo]++;
        }

        // Mostrar tarjetas
        foreach ($totales as $odontologo => $total) {
            $contenido = "
                <b>Odontólogo:</b> {$odontologo}<br>
                <b>Total pacientes atendidos:</b> {$total}
            ";
            echo generarTarjeta("Atención Odontológica", $contenido);
        }

    } else {
        echo "<div class='col-12'><div class='alert alert-info text-center'>No se registran pacientes atendidos.</div></div>";
    }
    break;

    case "canceladas":
        $canceladas = ControladorCitas::ctrCitasCanceladas($desde, $hasta);
        if (!empty($canceladas)) {
            foreach ($canceladas as $c) {
                $contenido = "
                    <b>Paciente:</b> {$c['nombre_paciente']}<br>
                    <b>Odontólogo:</b> {$c['nombre_odontologo']}<br>
                    <b>Motivo:</b> {$c['motivo_cancelacion']}<br>
                    <b>Estado:</b> Cancelada
                ";
                echo generarTarjeta("Cita Cancelada", $contenido, $c['fecha_cita']);
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay citas canceladas.</div></div>";
        }
        break;

   case "porDia":
    $porDia = ControladorCitas::ctrCitasPorDia($desde, $hasta);

    if (!empty($porDia)) {
        foreach ($porDia as $d) {
            // Detalle por odontólogo
            $detalleOdontologos = '';
            if (!empty($d['odontologos'])) {
                foreach ($d['odontologos'] as $o) {
                    $detalleOdontologos .= "<b>{$o['nombre']}:</b> {$o['cantidad']}<br>";
                }
            }

            // Detalle por estado
            $detalleEstados = '';
            if (!empty($d['estados'])) {
                foreach ($d['estados'] as $estado => $cantidad) {
                    $detalleEstados .= "<b>" . ucfirst($estado) . ":</b> {$cantidad}<br>";
                }
            }

            $contenido = "
                <b>Total citas:</b> {$d['cantidad']}<br><br>
                <b>Por Odontólogo:</b><br>{$detalleOdontologos}<br>
                <b>Por Estado:</b><br>{$detalleEstados}
            ";

            echo generarTarjeta("Resumen Diario", $contenido, $d['fecha_cita']);
        }
    } else {
        echo "<div class='col-12'><div class='alert alert-info text-center'>No se registran citas en este rango.</div></div>";
    }
    break;

   case "porOdontologo":
    $odontologos = ControladorCitas::ctrCitasPorOdontologo($desde, $hasta);

    if (!empty($odontologos)) {
        // Agrupar por odontólogo
        $resumen = [];

        foreach ($odontologos as $o) {
            $nombre = $o['nombre_odontologo'];
            $fecha = $o['fecha_cita'];
            $estado = $o['estado'];

            if (!isset($resumen[$nombre])) {
                $resumen[$nombre] = [
                    'cantidad' => 0,
                    'por_estado' => [],
                    'por_fecha' => []
                ];
            }

            $resumen[$nombre]['cantidad']++;

            // Agrupar por estado
            if (!isset($resumen[$nombre]['por_estado'][$estado])) {
                $resumen[$nombre]['por_estado'][$estado] = 0;
            }
            $resumen[$nombre]['por_estado'][$estado]++;

            // Agrupar por fecha
            if (!isset($resumen[$nombre]['por_fecha'][$fecha])) {
                $resumen[$nombre]['por_fecha'][$fecha] = 0;
            }
            $resumen[$nombre]['por_fecha'][$fecha]++;
        }

        // Mostrar tarjetas
        foreach ($resumen as $nombre => $info) {
            $detalleEstados = '';
            foreach ($info['por_estado'] as $estado => $cantidad) {
                $detalleEstados .= '<b>' . ucfirst($estado) . ':</b> ' . $cantidad . '<br>';
            }

            $detalleFechas = '';
            foreach ($info['por_fecha'] as $fecha => $cantidad) {
                $detalleFechas .= '<b>' . $fecha . ':</b> ' . $cantidad . ' cita(s)<br>';
            }

            $contenido = "
                <b>Citas asignadas:</b> {$info['cantidad']}<br><br>
                <b>Por Estado:</b><br>{$detalleEstados}<br>
                <b>Por Día:</b><br>{$detalleFechas}
            ";

            echo generarTarjeta($nombre, $contenido);
        }

    } else {
        echo "<div class='col-12'><div class='alert alert-info text-center'>Sin registros de citas por odontólogo.</div></div>";
    }
    break;

    case "porServicio":
        $servicios = ControladorCitas::ctrCitasPorServicio($desde, $hasta);
        if (!empty($servicios)) {
            foreach ($servicios as $s) {
                echo generarTarjeta("{$s['nombre_servicio']}", "<b>Solicitado:</b> {$s['cantidad']} veces");
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay citas por servicio registradas.</div></div>";
        }
        break;

    case "mensual":
        $mensuales = ControladorCitas::ctrCitasMensuales();
        if (!empty($mensuales)) {
            foreach ($mensuales as $m) {
                echo generarTarjeta("{$m['mes']}/{$m['anio']}", "<b>Total citas:</b> {$m['cantidad']}");
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay citas mensuales registradas.</div></div>";
        }
        break;

    default:
        echo "<div class='col-12'><div class='alert alert-warning text-center'>Reporte no disponible.</div></div>";
}
?>
