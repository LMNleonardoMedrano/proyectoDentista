<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/dentista/controladores/planPagoTratamiento.controlador.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dentista/modelos/planPagoTratamiento.modelo.php';

$tipo = $_GET['tipoReporte'] ?? null;
$desde = $_GET['desde'] ?? null;
$hasta = $_GET['hasta'] ?? null;

if (!$tipo) {
    echo '<div class="col-12"><div class="alert alert-info text-center">Seleccione un reporte.</div></div>';
    exit;
}

// Función para generar tarjeta blanca
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
    case "totales":
        $pagos = ControladorPlanPago::ctrPagosTotales($desde, $hasta);
        $total = 0;

        if (!empty($pagos)) {
            foreach ($pagos as $i => $pago) {
                $total += $pago["monto"];

                // Mostrar paciente si está disponible
                $nombrePaciente = $pago['nombrePaciente'] ?? 'Desconocido';

                $contenido = "<b>Fecha:</b> {$pago['fecha']}<br>
                          <b>Paciente:</b> {$nombrePaciente}<br>
                          <b>Monto:</b> " . number_format($pago["monto"]) . " Bs";

                echo generarTarjeta("Pago #" . ($i + 1), $contenido);
            }

            echo "<div class='col-12'>
                <div class='alert alert-success text-center fw-bold'>
                    TOTAL: " . number_format($total, 2) . " Bs
                </div>
              </div>";
        } else {
            echo "<div class='col-12'>
                <div class='alert alert-info text-center'>No hay pagos registrados.</div>
              </div>";
        }
        break;


    case "saldoPacientes":
        $saldos = ControladorPlanPago::ctrSaldosPorPaciente();
        if (!empty($saldos)) {
            foreach ($saldos as $s) {
                $contenido = number_format($s['saldo_total']) . " Bs";
                echo generarTarjeta($s['nombre'], $contenido);
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay saldos de pacientes registrados.</div></div>";
        }
        break;

    case "pendientes":
        $pendientes = ControladorPlanPago::ctrPagosPendientes();
        if (!empty($pendientes)) {
            foreach ($pendientes as $p) {
                $contenido = number_format($p['saldo']) . " Bs pendiente";
                echo generarTarjeta($p['nombre'], $contenido);
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay pagos pendientes.</div></div>";
        }
        break;

    case "diario":
        $pagosDia = ControladorPlanPago::ctrPagosPorDia($desde, $hasta);
        $suma = 0;
        if (!empty($pagosDia)) {
            foreach ($pagosDia as $p) {
                $contenido = number_format($p['recaudacion']) . " Bs";
                echo generarTarjeta($p['fecha'], $contenido);
                $suma += $p['recaudacion'];
            }
            echo "<div class='col-12'><div class='alert alert-success text-center fw-bold'>RECAUDACIÓN TOTAL: " . number_format($suma) . " Bs</div></div>";
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay pagos en este rango.</div></div>";
        }
        break;

    case "servicios":
        $servicios = ControladorPlanPago::ctrServiciosMasSolicitados($desde, $hasta);
        if (!empty($servicios)) {
            foreach ($servicios as $s) {
                $contenido = $s['veces'] . " veces";
                echo generarTarjeta($s['nombreServicio'], $contenido);
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay servicios registrados en este rango.</div></div>";
        }
        break;
    case "porOdontologo":
        $odontologos = ControladorPlanPago::ctrPagosPorOdontologo($desde, $hasta);
        if (!empty($odontologos)) {
            foreach ($odontologos as $o) {
                $contenido = "<b>Recaudado:</b> " . number_format($o['totalRecaudado']) . " Bs";
                echo generarTarjeta($o['nombre'] . " " . $o['apellido'], $contenido);
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay pagos registrados por odontólogos.</div></div>";
        }
        break;
    case "porTipoPago":
        $tipos = ControladorPlanPago::ctrPagosPorTipo($desde, $hasta);
        if (!empty($tipos)) {
            foreach ($tipos as $t) {
                $contenido = "<b>Total:</b> " . number_format($t['total']) . " Bs";
                echo generarTarjeta($t['nombreTipoPago'], $contenido);
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay pagos registrados por tipo.</div></div>";
        }
        break;
    // case "porPaciente":
    //     $idPaciente = $_GET['idPaciente'] ?? null;
    //     if (!$idPaciente) {
    //         echo "<div class='col-12'><div class='alert alert-info text-center'>Seleccione un paciente.</div></div>";
    //         break;
    //     }

    //     $pagosPaciente = ControladorPlanPago::ctrPagosPorPaciente($idPaciente);
    //     if (!empty($pagosPaciente)) {
    //         foreach ($pagosPaciente as $i => $p) {
    //             $contenido = "<b>Fecha:</b> {$p['fecha']}<br>
    //                           <b>Monto:</b> " . number_format($p['monto']) . " Bs<br>
    //                           <b>Tipo de Pago:</b> {$p['nombreTipoPago']}<br>
    //                           <b>Estado:</b> {$p['estadoPago']}";
    //             echo generarTarjeta("Pago #" . ($i + 1), $contenido);
    //         }
    //     } else {
    //         echo "<div class='col-12'><div class='alert alert-info text-center'>Este paciente no tiene pagos registrados.</div></div>";
    //     }
    //     break;
    case "porEstadoTratamiento":
        $estados = ControladorPlanPago::ctrPagosPorEstadoTratamiento();
        if (!empty($estados)) {
            foreach ($estados as $e) {
                $contenido = "<b>Tratamientos:</b> {$e['cantidad']}<br>
                          <b>Total:</b> " . number_format($e['total']) . " Bs";
                echo generarTarjeta($e['estadoPago'], $contenido);
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay pagos registrados por estado.</div></div>";
        }
        break;
    case "descuentos":
        $descuentos = ControladorPlanPago::ctrDescuentosAplicados();
        if (!empty($descuentos)) {
            foreach ($descuentos as $d) {
                $contenido = "<b>Fecha:</b> {$d['fecha']}<br>
                          <b>Monto:</b> " . number_format($d['monto']) . " Bs<br>
                          <b>Descuento:</b> {$d['descuento']}%<br>
                          <b>Detalle:</b> {$d['descripcion']}";
                echo generarTarjeta("Descuento aplicado", $contenido);
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay descuentos registrados.</div></div>";
        }
        break;
    case "porServicio":
        $servicios = ControladorPlanPago::ctrPagosPorServicio();
        if (!empty($servicios)) {
            foreach ($servicios as $s) {
                $contenido = "<b>Solicitado:</b> {$s['veces']} veces<br>
                          <b>Total Recaudado:</b> " . number_format($s['totalRecaudado']) . " Bs";
                echo generarTarjeta($s['nombreServicio'], $contenido);
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay pagos por servicios registrados.</div></div>";
        }
        break;
    case "mensual":
        $mensuales = ControladorPlanPago::ctrPagosMensuales();
        if (!empty($mensuales)) {
            foreach ($mensuales as $m) {
                $contenido = "<b>Mes:</b> {$m['mes']} / {$m['anio']}<br>
                          <b>Total:</b> " . number_format($m['total']) . " Bs";
                echo generarTarjeta("Recaudación Mensual", $contenido);
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'>No hay pagos mensuales registrados.</div></div>";
        }
        break;
    default:
        echo "<div class='col-12'><div class='alert alert-info text-center'>Reporte no disponible.</div></div>";
}
