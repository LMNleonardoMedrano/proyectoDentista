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

function generarTarjeta($titulo, $contenido)
{
    return "<div class='col-12 col-md-6 col-lg-4'>
                <div class='card shadow-sm mb-3 bg-white'>
                    <div class='card-body'>
                        <h5 class='card-title'>{$titulo}</h5>
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
                $contenido = "<b>Fecha:</b> {$c['fecha_cita']}<br>
                              <b>Hora:</b> {$c['hora']}<br>
                              <b>Paciente:</b> {$c['nombre_paciente']}<br>
                              <b>Odontólogo:</b> {$c['nombre_odontologo']}";
                echo generarTarjeta("Cita Programada", $contenido);
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay citas programadas en este rango.</div></div>";
        }
        break;

    case "confirmadas":
        $confirmadas = ControladorCitas::ctrCitasConfirmadas($desde, $hasta);
        if (!empty($confirmadas)) {
            foreach ($confirmadas as $c) {
                $contenido = "<b>Paciente:</b> {$c['nombre_paciente']}<br>
                              <b>Fecha:</b> {$c['fecha_cita']}<br>
                              <b>Odontólogo:</b> {$c['nombre_odontologo']}";
                echo generarTarjeta("Cita Confirmada", $contenido);
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay pacientes confirmados.</div></div>";
        }
        break;

    case "atendidos":
        $atendidos = ControladorCitas::ctrPacientesAtendidos($desde, $hasta);
        if (!empty($atendidos)) {
            foreach ($atendidos as $a) {
                $contenido = "<b>Odontólogo:</b> {$a['nombre_odontologo']}<br>
                              <b>Total atendidos:</b> {$a['total_atendidos']}";
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
                $contenido = "<b>Paciente:</b> {$c['nombre_paciente']}<br>
                              <b>Motivo:</b> {$c['motivo_cancelacion']}";
                echo generarTarjeta("Cita Cancelada", $contenido);
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay citas canceladas.</div></div>";
        }
        break;

    case "porDia":
        $porDia = ControladorCitas::ctrCitasPorDia($desde, $hasta);
        if (!empty($porDia)) {
            foreach ($porDia as $d) {
                echo generarTarjeta("{$d['fecha_cita']}", "Total citas: {$d['cantidad']}");
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No se registran citas en este rango.</div></div>";
        }
        break;

    case "porOdontologo":
        $odontologos = ControladorCitas::ctrCitasPorOdontologo($desde, $hasta);
        if (!empty($odontologos)) {
            foreach ($odontologos as $o) {
                echo generarTarjeta("{$o['nombre_odontologo']}", "Citas asignadas: {$o['cantidad']}");
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>Sin registros de citas por odontólogo.</div></div>";
        }
        break;

    case "porServicio":
        $servicios = ControladorCitas::ctrCitasPorServicio($desde, $hasta);
        if (!empty($servicios)) {
            foreach ($servicios as $s) {
                echo generarTarjeta("{$s['nombre_servicio']}", "Solicitado {$s['cantidad']} veces");
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay citas por servicio registradas.</div></div>";
        }
        break;

    case "mensual":
        $mensuales = ControladorCitas::ctrCitasMensuales();
        if (!empty($mensuales)) {
            foreach ($mensuales as $m) {
                echo generarTarjeta("{$m['mes']}/{$m['anio']}", "Total citas: {$m['cantidad']}");
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay citas mensuales registradas.</div></div>";
        }
        break;

    default:
        echo "<div class='col-12'><div class='alert alert-warning text-center'>Reporte no disponible.</div></div>";
}
