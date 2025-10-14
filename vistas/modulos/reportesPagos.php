<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/dentista/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dentista/controladores/planPagoTratamiento.controlador.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dentista/modelos/planPagoTratamiento.modelo.php';

use Mpdf\Mpdf;

$tipo = $_GET['tipoReporte'] ?? null;
$desde = $_GET['desde'] ?? null;
$hasta = $_GET['hasta'] ?? null;

if (!$tipo) {
    die("Seleccione un tipo de reporte.");
}

$mpdf = new Mpdf();
$mpdf->SetTitle('Reporte de Pagos - ' . ucfirst($tipo));

$html = "<h2 style='text-align:center; font-family: Arial;'>REPORTE DE PAGOS</h2>
<p style='text-align:center; font-family: Arial; font-size:12px;'>Desde: " . ($desde ?: '-') . " | Hasta: " . ($hasta ?: '-') . "</p><br>";

switch ($tipo) {
    case "totales":
        $pagos = ControladorPlanPago::ctrPagosTotales($desde, $hasta);
        if (!empty($pagos)) {
            $html .= "<table style='width:100%; border-collapse: collapse; font-family: Arial; font-size:12px;'>
                        <thead style='background-color:#007BFF; color:white;'>
                          <tr>
                            <th style='border:1px solid #ccc; padding:6px;'>#</th>
                            <th style='border:1px solid #ccc; padding:6px;'>Fecha</th>
                            <th style='border:1px solid #ccc; padding:6px;'>Paciente</th>
                            <th style='border:1px solid #ccc; padding:6px;'>Monto (Bs)</th>
                          </tr>
                        </thead><tbody>";
            $total = 0;
            foreach ($pagos as $i => $pago) {
                $nombrePaciente = $pago['nombrePaciente'] ?? 'Desconocido';
                $total += $pago['monto'];
                $html .= "<tr>
                            <td style='border:1px solid #ccc; padding:6px; text-align:center;'>" . ($i + 1) . "</td>
                            <td style='border:1px solid #ccc; padding:6px;'>" . $pago['fecha'] . "</td>
                            <td style='border:1px solid #ccc; padding:6px;'>" . $nombrePaciente . "</td>
                            <td style='border:1px solid #ccc; padding:6px; text-align:right;'>" . number_format($pago['monto'], 2) . "</td>
                          </tr>";
            }
            $html .= "<tr>
                        <td colspan='3' style='border:1px solid #ccc; padding:6px; text-align:right; font-weight:bold;'>TOTAL</td>
                        <td style='border:1px solid #ccc; padding:6px; text-align:right; font-weight:bold;'>" . number_format($total, 2) . "</td>
                      </tr>";
            $html .= "</tbody></table>";
        } else {
            $html .= "<p style='text-align:center;'>No hay pagos registrados.</p>";
        }
        break;

    case "saldoPacientes":
        $saldos = ControladorPlanPago::ctrSaldosPorPaciente();
        if (!empty($saldos)) {
            $html .= "<table style='width:100%; border-collapse: collapse; font-family: Arial; font-size:12px;'>
                        <thead style='background-color:#007BFF; color:white;'>
                          <tr>
                            <th style='border:1px solid #ccc; padding:6px;'>Paciente</th>
                            <th style='border:1px solid #ccc; padding:6px;'>Saldo Total (Bs)</th>
                          </tr>
                        </thead><tbody>";
            foreach ($saldos as $s) {
                $html .= "<tr>
                            <td style='border:1px solid #ccc; padding:6px;'>" . $s['nombre'] . "</td>
                            <td style='border:1px solid #ccc; padding:6px; text-align:right;'>" . number_format($s['saldo_total'], 2) . "</td>
                          </tr>";
            }
            $html .= "</tbody></table>";
        } else {
            $html .= "<p style='text-align:center;'>No hay saldos de pacientes registrados.</p>";
        }
        break;

    case "pendientes":
        $pendientes = ControladorPlanPago::ctrPagosPendientes();
        if (!empty($pendientes)) {
            $html .= "<table style='width:100%; border-collapse: collapse; font-family: Arial; font-size:12px;'>
                        <thead style='background-color:#007BFF; color:white;'>
                          <tr>
                            <th style='border:1px solid #ccc; padding:6px;'>Paciente</th>
                            <th style='border:1px solid #ccc; padding:6px;'>Monto Pendiente (Bs)</th>
                          </tr>
                        </thead><tbody>";
            foreach ($pendientes as $p) {
                $html .= "<tr>
                            <td style='border:1px solid #ccc; padding:6px;'>" . $p['nombre'] . "</td>
                            <td style='border:1px solid #ccc; padding:6px; text-align:right;'>" . number_format($p['saldo'], 2) . "</td>
                          </tr>";
            }
            $html .= "</tbody></table>";
        } else {
            $html .= "<p style='text-align:center;'>No hay pagos pendientes.</p>";
        }
        break;

    case "diario":
        $pagosDia = ControladorPlanPago::ctrPagosPorDia($desde, $hasta);
        if (!empty($pagosDia)) {
            $html .= "<table style='width:100%; border-collapse: collapse; font-family: Arial; font-size:12px;'>
                        <thead style='background-color:#007BFF; color:white;'>
                          <tr>
                            <th style='border:1px solid #ccc; padding:6px;'>Fecha</th>
                            <th style='border:1px solid #ccc; padding:6px;'>Recaudación (Bs)</th>
                          </tr>
                        </thead><tbody>";
            $totalDia = 0;
            foreach ($pagosDia as $p) {
                $html .= "<tr>
                            <td style='border:1px solid #ccc; padding:6px;'>" . $p['fecha'] . "</td>
                            <td style='border:1px solid #ccc; padding:6px; text-align:right;'>" . number_format($p['recaudacion'], 2) . "</td>
                          </tr>";
                $totalDia += $p['recaudacion'];
            }
            $html .= "<tr>
                        <td style='border:1px solid #ccc; padding:6px; text-align:right; font-weight:bold;'>TOTAL</td>
                        <td style='border:1px solid #ccc; padding:6px; text-align:right; font-weight:bold;'>" . number_format($totalDia, 2) . "</td>
                      </tr>";
            $html .= "</tbody></table>";
        } else {
            $html .= "<p style='text-align:center;'>No hay pagos en este rango.</p>";
        }
        break;

    case "servicios":
        $servicios = ControladorPlanPago::ctrServiciosMasSolicitados($desde, $hasta);
        if (!empty($servicios)) {
            $html .= "<table style='width:100%; border-collapse: collapse; font-family: Arial; font-size:12px;'>
                        <thead style='background-color:#007BFF; color:white;'>
                          <tr>
                            <th style='border:1px solid #ccc; padding:6px;'>Servicio</th>
                            <th style='border:1px solid #ccc; padding:6px;'>Veces Solicitado</th>
                          </tr>
                        </thead><tbody>";
            foreach ($servicios as $s) {
                $html .= "<tr>
                            <td style='border:1px solid #ccc; padding:6px;'>" . $s['nombreServicio'] . "</td>
                            <td style='border:1px solid #ccc; padding:6px; text-align:right;'>" . $s['veces'] . "</td>
                          </tr>";
            }
            $html .= "</tbody></table>";
        } else {
            $html .= "<p style='text-align:center;'>No hay servicios registrados en este rango.</p>";
        }
        break;

    case "porOdontologo":
        $odontologos = ControladorPlanPago::ctrPagosPorOdontologo($desde, $hasta);
        if (!empty($odontologos)) {
            $html .= "<table style='width:100%; border-collapse: collapse; font-family: Arial; font-size:12px;'>
                        <thead style='background-color:#007BFF; color:white;'>
                          <tr>
                            <th style='border:1px solid #ccc; padding:6px;'>Odontólogo</th>
                            <th style='border:1px solid #ccc; padding:6px;'>Total Recaudado (Bs)</th>
                          </tr>
                        </thead><tbody>";
            foreach ($odontologos as $o) {
                $html .= "<tr>
                            <td style='border:1px solid #ccc; padding:6px;'>" . $o['nombre'] . " " . $o['apellido'] . "</td>
                            <td style='border:1px solid #ccc; padding:6px; text-align:right;'>" . number_format($o['totalRecaudado'], 2) . "</td>
                          </tr>";
            }
            $html .= "</tbody></table>";
        } else {
            $html .= "<p style='text-align:center;'>No hay pagos registrados por odontólogos.</p>";
        }
        break;

    case "porTipoPago":
        $tipos = ControladorPlanPago::ctrPagosPorTipo($desde, $hasta);
        if (!empty($tipos)) {
            $html .= "<table style='width:100%; border-collapse: collapse; font-family: Arial; font-size:12px;'>
                        <thead style='background-color:#007BFF; color:white;'>
                          <tr>
                            <th style='border:1px solid #ccc; padding:6px;'>Tipo de Pago</th>
                            <th style='border:1px solid #ccc; padding:6px;'>Total (Bs)</th>
                          </tr>
                        </thead><tbody>";
            foreach ($tipos as $t) {
                $html .= "<tr>
                            <td style='border:1px solid #ccc; padding:6px;'>" . $t['nombreTipoPago'] . "</td>
                            <td style='border:1px solid #ccc; padding:6px; text-align:right;'>" . number_format($t['total'], 2) . "</td>
                          </tr>";
            }
            $html .= "</tbody></table>";
        } else {
            $html .= "<p style='text-align:center;'>No hay pagos registrados por tipo.</p>";
        }
        break;

    case "porEstadoTratamiento":
        $estados = ControladorPlanPago::ctrPagosPorEstadoTratamiento();
        if (!empty($estados)) {
            $html .= "<table style='width:100%; border-collapse: collapse; font-family: Arial; font-size:12px;'>
                        <thead style='background-color:#007BFF; color:white;'>
                          <tr>
                            <th style='border:1px solid #ccc; padding:6px;'>Estado</th>
                            <th style='border:1px solid #ccc; padding:6px;'>Cantidad Tratamientos</th>
                            <th style='border:1px solid #ccc; padding:6px;'>Total Recaudado (Bs)</th>
                          </tr>
                        </thead><tbody>";
            foreach ($estados as $e) {
                $html .= "<tr>
                            <td style='border:1px solid #ccc; padding:6px;'>" . $e['estadoPago'] . "</td>
                            <td style='border:1px solid #ccc; padding:6px; text-align:right;'>" . $e['cantidad'] . "</td>
                            <td style='border:1px solid #ccc; padding:6px; text-align:right;'>" . number_format($e['total'], 2) . "</td>
                          </tr>";
            }
            $html .= "</tbody></table>";
        } else {
            $html .= "<p style='text-align:center;'>No hay pagos registrados por estado.</p>";
        }
        break;

    case "descuentos":
        $descuentos = ControladorPlanPago::ctrDescuentosAplicados();
        if (!empty($descuentos)) {
            $html .= "<table style='width:100%; border-collapse: collapse; font-family: Arial; font-size:12px;'>
                        <thead style='background-color:#007BFF; color:white;'>
                          <tr>
                            <th style='border:1px solid #ccc; padding:6px;'>Fecha</th>
                            <th style='border:1px solid #ccc; padding:6px;'>Monto (Bs)</th>
                            <th style='border:1px solid #ccc; padding:6px;'>Descuento (%)</th>
                            <th style='border:1px solid #ccc; padding:6px;'>Detalle</th>
                          </tr>
                        </thead><tbody>";
            foreach ($descuentos as $d) {
                $html .= "<tr>
                            <td style='border:1px solid #ccc; padding:6px;'>" . $d['fecha'] . "</td>
                            <td style='border:1px solid #ccc; padding:6px; text-align:right;'>" . number_format($d['monto'], 2) . "</td>
                            <td style='border:1px solid #ccc; padding:6px; text-align:right;'>" . $d['descuento'] . "</td>
                            <td style='border:1px solid #ccc; padding:6px;'>" . $d['descripcion'] . "</td>
                          </tr>";
            }
            $html .= "</tbody></table>";
        } else {
            $html .= "<p style='text-align:center;'>No hay descuentos registrados.</p>";
        }
        break;

    case "porServicio":
        $servicios = ControladorPlanPago::ctrPagosPorServicio();
        if (!empty($servicios)) {
            $html .= "<table style='width:100%; border-collapse: collapse; font-family: Arial; font-size:12px;'>
                        <thead style='background-color:#007BFF; color:white;'>
                          <tr>
                            <th style='border:1px solid #ccc; padding:6px;'>Servicio</th>
                            <th style='border:1px solid #ccc; padding:6px;'>Veces Solicitado</th>
                            <th style='border:1px solid #ccc; padding:6px;'>Total Recaudado (Bs)</th>
                          </tr>
                        </thead><tbody>";
            foreach ($servicios as $s) {
                $html .= "<tr>
                            <td style='border:1px solid #ccc; padding:6px;'>" . $s['nombreServicio'] . "</td>
                            <td style='border:1px solid #ccc; padding:6px; text-align:right;'>" . $s['veces'] . "</td>
                            <td style='border:1px solid #ccc; padding:6px; text-align:right;'>" . number_format($s['totalRecaudado'], 2) . "</td>
                          </tr>";
            }
            $html .= "</tbody></table>";
        } else {
            $html .= "<p style='text-align:center;'>No hay pagos por servicios registrados.</p>";
        }
        break;

    case "mensual":
        $mensuales = ControladorPlanPago::ctrPagosMensuales();
        if (!empty($mensuales)) {
            $html .= "<table style='width:100%; border-collapse: collapse; font-family: Arial; font-size:12px;'>
                        <thead style='background-color:#007BFF; color:white;'>
                          <tr>
                            <th style='border:1px solid #ccc; padding:6px;'>Mes / Año</th>
                            <th style='border:1px solid #ccc; padding:6px;'>Total Recaudado (Bs)</th>
                          </tr>
                        </thead><tbody>";
            foreach ($mensuales as $m) {
                $html .= "<tr>
                            <td style='border:1px solid #ccc; padding:6px;'>" . $m['mes'] . " / " . $m['anio'] . "</td>
                            <td style='border:1px solid #ccc; padding:6px; text-align:right;'>" . number_format($m['total'], 2) . "</td>
                          </tr>";
            }
            $html .= "</tbody></table>";
        } else {
            $html .= "<p style='text-align:center;'>No hay pagos mensuales registrados.</p>";
        }
        break;

    default:
        $html .= "<p style='text-align:center;'>Reporte no disponible.</p>";
        break;
}

$html .= "<p style='text-align:right; font-size:11px; color:#555;'>Generado el " . date("d/m/Y H:i") . "</p>";

$mpdf->WriteHTML($html);
$mpdf->Output('reporte_pagos_' . $tipo . '.pdf', 'I');
