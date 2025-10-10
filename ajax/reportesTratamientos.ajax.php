<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/dentista/controladores/tratamiento.controlador.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dentista/modelos/tratamiento.modelo.php';

$tipo = $_GET['tipoReporteTratamientos'] ?? null;
$desde = $_GET['desdeTrat'] ?? null;
$hasta = $_GET['hastaTrat'] ?? null;

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
    case "completados":
        $completados = ControladorTratamiento::ctrTratamientosCompletados($desde, $hasta);
        if (!empty($completados)) {
            foreach ($completados as $t) {
                $contenido = "<b>Paciente:</b> {$t['nombre_paciente']}<br>
                              <b>Servicio:</b> {$t['nombre_servicio']}<br>
                              <b>Finalizado:</b> {$t['fecha_fin']}";
                echo generarTarjeta("Tratamiento Completado", $contenido);
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay tratamientos completados.</div></div>";
        }
        break;

    case "parciales":
        $parciales = ControladorTratamiento::ctrTratamientosParciales($desde, $hasta);
        if (!empty($parciales)) {
            foreach ($parciales as $t) {
                $contenido = "<b>Paciente:</b> {$t['nombre_paciente']}<br>
                              <b>Progreso:</b> {$t['progreso']}%";
                echo generarTarjeta("Tratamiento Parcial", $contenido);
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay tratamientos parciales.</div></div>";
        }
        break;

    case "activos":
        $activos = ControladorTratamiento::ctrTratamientosActivos($desde, $hasta);
        if (!empty($activos)) {
            foreach ($activos as $t) {
                $contenido = "<b>Paciente:</b> {$t['nombre_paciente']}<br>
                              <b>Servicio:</b> {$t['nombre_servicio']}";
                echo generarTarjeta("Tratamiento Activo", $contenido);
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay tratamientos activos.</div></div>";
        }
        break;

    case "noCancelados":
        $noCancelados = ControladorTratamiento::ctrTratamientosNoCancelados($desde, $hasta);
        if (!empty($noCancelados)) {
            foreach ($noCancelados as $t) {
                $contenido = "<b>Paciente:</b> {$t['nombre_paciente']}<br>
                              <b>Estado:</b> {$t['estado']}";
                echo generarTarjeta("Tratamiento Vigente", $contenido);
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay tratamientos no cancelados.</div></div>";
        }
        break;

    case "porOdontologo":
        $odontologos = ControladorTratamiento::ctrTratamientosPorOdontologo($desde, $hasta);
        if (!empty($odontologos)) {
            foreach ($odontologos as $o) {
                echo generarTarjeta($o['nombre_odontologo'], "Tratamientos: {$o['cantidad']}");
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No se registran tratamientos por odontólogo.</div></div>";
        }
        break;

    case "porServicio":
        $servicios = ControladorTratamiento::ctrTratamientosPorServicio($desde, $hasta);
        if (!empty($servicios)) {
            foreach ($servicios as $s) {
                echo generarTarjeta($s['nombreServicio'], "Realizados: {$s['cantidad']}");
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No se registran tratamientos por servicio.</div></div>";
        }
        break;

    case "porEstado":
        $estados = ControladorTratamiento::ctrTratamientosPorEstado($desde, $hasta);
        if (!empty($estados)) {
            foreach ($estados as $e) {
                echo generarTarjeta($e['estado'], "Cantidad: {$e['cantidad']}");
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay tratamientos por estado registrados.</div></div>";
        }
        break;

    case "mensual":
        $mensuales = ControladorTratamiento::ctrTratamientosMensuales();
        if (!empty($mensuales)) {
            foreach ($mensuales as $m) {
                echo generarTarjeta("{$m['mes']}/{$m['anio']}", "Tratamientos: {$m['cantidad']}");
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay tratamientos mensuales registrados.</div></div>";
        }
        break;

    default:
        echo "<div class='col-12'><div class='alert alert-warning text-center'>Reporte no disponible.</div></div>";
}
