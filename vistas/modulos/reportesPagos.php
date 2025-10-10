<?php
require_once '../../vendor/autoload.php';
require_once '../../controladores/planPagoTratamiento.controlador.php';
require_once '../../modelos/planPagoTratamiento.modelo.php';

use Mpdf\Mpdf;

$tipo = $_GET['tipoReporte'] ?? null;
$desde = $_GET['desde'] ?? null;
$hasta = $_GET['hasta'] ?? null;

$mpdf = new Mpdf();
$mpdf->SetTitle('Reporte de Pagos');

// Encabezado general
$html = '<h2 style="text-align:center; font-family: Arial, sans-serif;">Reporte de Pagos - Clínica Dental</h2>';

// Subtítulo según el tipo de reporte
$subtitulo = '';
switch ($tipo) {
    case "totales":
        $subtitulo = 'Pagos Totales';
        break;
    case "saldoPacientes":
        $subtitulo = 'Saldos por Paciente';
        break;
    case "pendientes":
        $subtitulo = 'Pagos Pendientes';
        break;
    case "diario":
        $subtitulo = 'Pagos por Día';
        break;
    case "servicios":
        $subtitulo = 'Servicios más solicitados';
        break;
    case "porOdontologo":
        $subtitulo = 'Pagos por Odontólogo';
        break;
    case "porTipoPago":
        $subtitulo = 'Pagos por Tipo de Pago';
        break;
    default:
        $subtitulo = 'Reporte Desconocido';
}
$html .= '<h4 style="text-align:center; font-family: Arial, sans-serif; color:#555;">' . $subtitulo . '</h4>';
$html .= '<hr style="margin-bottom:20px;">';

// Estilo de tarjeta
$cardStyle = "background-color:#d1e7dd; border:1px solid #badbcc; border-radius:8px; padding:12px; margin-bottom:10px; font-family: Arial, sans-serif; box-shadow: 1px 2px 5px rgba(0,0,0,0.1);";

// Generar contenido según el tipo
if (!$tipo) {
    $html .= '<p style="text-align:center;">No se ha seleccionado ningún reporte.</p>';
} else {
    switch ($tipo) {
        case "totales":
            $pagos = ControladorPlanPago::ctrPagosTotales($desde, $hasta);
            $total = 0;
            if (!empty($pagos)) {
                foreach ($pagos as $i => $pago) {
                    $total += $pago["monto"];
                    $html .= '
                    <div style="' . $cardStyle . '">
                        <h4 style="margin:0;">Pago #' . ($i + 1) . '</h4>
                        <p><strong>Fecha:</strong> ' . $pago["fecha"] . '</p>
                        <p><strong>Monto:</strong> ' . number_format($pago["monto"]) . ' Bs</p>
                    </div>';
                }
                $html .= '<p style="text-align:center; font-weight:bold; font-size:16px; margin-top:15px;">TOTAL: ' . number_format($total, 2) . ' Bs</p>';
            } else {
                $html .= '<p style="text-align:center;">No hay pagos registrados en este rango.</p>';
            }
            break;

        case "saldoPacientes":
            $saldos = ControladorPlanPago::ctrSaldosPorPaciente();
            if (!empty($saldos)) {
                foreach ($saldos as $s) {
                    $html .= '<div style="' . $cardStyle . '"><p><strong>' . $s['nombre'] . ':</strong> ' . number_format($s['saldo_total']) . ' Bs</p></div>';
                }
            } else {
                $html .= '<p style="text-align:center;">No hay saldos de pacientes registrados.</p>';
            }
            break;

        case "pendientes":
            $pendientes = ControladorPlanPago::ctrPagosPendientes();
            if (!empty($pendientes)) {
                foreach ($pendientes as $p) {
                    $html .= '<div style="' . $cardStyle . '"><p><strong>' . $p['nombre'] . ':</strong> ' . number_format($p['saldo']) . ' Bs pendiente</p></div>';
                }
            } else {
                $html .= '<p style="text-align:center;">No hay pagos pendientes.</p>';
            }
            break;

        case "diario":
            $pagosDia = ControladorPlanPago::ctrPagosPorDia($desde, $hasta);
            $suma = 0;
            if (!empty($pagosDia)) {
                foreach ($pagosDia as $p) {
                    $html .= '<div style="' . $cardStyle . '"><p><strong>' . $p['fecha'] . ':</strong> ' . number_format($p['recaudacion']) . ' Bs</p></div>';
                    $suma += $p['recaudacion'];
                }
                $html .= '<p style="text-align:center; font-weight:bold; font-size:16px; margin-top:15px;">RECAUDACIÓN TOTAL: ' . number_format($suma) . ' Bs</p>';
            } else {
                $html .= '<p style="text-align:center;">No hay pagos en este rango.</p>';
            }
            break;

        case "servicios":
            $servicios = ControladorPlanPago::ctrServiciosMasSolicitados($desde, $hasta);
            if (!empty($servicios)) {
                foreach ($servicios as $s) {
                    $html .= '<div style="' . $cardStyle . '"><p><strong>' . $s['nombreServicio'] . ':</strong> ' . $s['veces'] . ' veces</p></div>';
                }
            } else {
                $html .= '<p style="text-align:center;">No hay servicios registrados en este rango.</p>';
            }
            break;
        case "porOdontologo":
            $odontologos = ControladorPlanPago::ctrPagosPorOdontologo($desde, $hasta);
            if (!empty($odontologos)) {
                foreach ($odontologos as $o) {
                    $html .= '<div style="' . $cardStyle . '">
                        <p><strong>' . $o['nombre'] . ' ' . $o['apellido'] . ':</strong> ' . number_format($o['totalRecaudado']) . ' Bs</p>
                      </div>';
                }
            } else {
                $html .= '<p style="text-align:center;">No hay pagos registrados por odontólogos.</p>';
            }
            break;

        case "porTipoPago":
            $tipos = ControladorPlanPago::ctrPagosPorTipo($desde, $hasta);
            if (!empty($tipos)) {
                foreach ($tipos as $t) {
                    $html .= '<div style="' . $cardStyle . '">
                        <p><strong>' . $t['nombreTipoPago'] . ':</strong> ' . number_format($t['total']) . ' Bs</p>
                      </div>';
                }
            } else {
                $html .= '<p style="text-align:center;">No hay pagos registrados por tipo.</p>';
            }
            break;
        //         case "porPaciente":
        // $idPaciente = $_GET['idPaciente'] ?? null;
        // if (!$idPaciente) {
        //     $html .= '<p style="text-align:center;">No se ha seleccionado ningún paciente.</p>';
        //     break;
        // }

        // // Obtener nombre del paciente
        // $stmt = Conexion::conectar()->prepare("SELECT nombre FROM pacientes WHERE idPaciente = :id");
        // $stmt->bindParam(":id", $idPaciente, PDO::PARAM_INT);
        // $stmt->execute();
        // $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
        // $nombrePaciente = $paciente['nombre'] ?? 'Desconocido';

        // $html .= '<h4 style="text-align:center; font-family: Arial, sans-serif; color:#555;">Pagos del Paciente: ' . $nombrePaciente . '</h4>';
        // $html .= '<hr style="margin-bottom:20px;">';

        // $pagosPaciente = ControladorPlanPago::ctrPagosPorPaciente($idPaciente);
        // $total = 0;

        // if (!empty($pagosPaciente)) {
        //     foreach ($pagosPaciente as $i => $p) {
        //         $html .= '<div style="' . $cardStyle . '">
        //                     <h4 style="margin:0;">Pago #' . ($i + 1) . '</h4>
        //                     <p><strong>Fecha:</strong> ' . $p["fecha"] . '</p>
        //                     <p><strong>Monto:</strong> ' . number_format($p["monto"]) . ' Bs</p>
        //                     <p><strong>Tipo de Pago:</strong> ' . $p["nombreTipoPago"] . '</p>
        //                     <p><strong>Estado:</strong> ' . $p["estadoPago"] . '</p>
        //                   </div>';
        //         $total += $p["monto"];
        //     }

        //     $html .= '<p style="text-align:center; font-weight:bold; font-size:16px; margin-top:15px;">TOTAL PAGADO: ' . number_format($total, 2) . ' Bs</p>';
        // } else {
        //     $html .= '<p style="text-align:center;">Este paciente no tiene pagos registrados.</p>';
        // }
        // break;
        case "porEstadoTratamiento":
            $html .= '<h4 style="text-align:center; font-family: Arial, sans-serif; color:#555;">Pagos por Estado de Tratamiento</h4>';
            $html .= '<hr style="margin-bottom:20px;">';

            $estados = ControladorPlanPago::ctrPagosPorEstadoTratamiento();
            if (!empty($estados)) {
                foreach ($estados as $e) {
                    $html .= '<div style="' . $cardStyle . '">
                        <p><strong>Estado:</strong> ' . $e['estadoPago'] . '</p>
                        <p><strong>Tratamientos:</strong> ' . $e['cantidad'] . '</p>
                        <p><strong>Total:</strong> ' . number_format($e['total']) . ' Bs</p>
                      </div>';
                }
            } else {
                $html .= '<p style="text-align:center;">No hay pagos registrados por estado.</p>';
            }
            break;
        case "descuentos":
            $html .= '<h4 style="text-align:center; font-family: Arial, sans-serif; color:#555;">Descuentos Aplicados</h4>';
            $html .= '<hr style="margin-bottom:20px;">';

            $descuentos = ControladorPlanPago::ctrDescuentosAplicados();
            if (!empty($descuentos)) {
                foreach ($descuentos as $d) {
                    $html .= '<div style="' . $cardStyle . '">
                        <p><strong>Fecha:</strong> ' . $d['fecha'] . '</p>
                        <p><strong>Monto:</strong> ' . number_format($d['monto']) . ' Bs</p>
                        <p><strong>Descuento:</strong> ' . $d['descuento'] . '%</p>
                        <p><strong>Detalle:</strong> ' . $d['descripcion'] . '</p>
                      </div>';
                }
            } else {
                $html .= '<p style="text-align:center;">No hay descuentos registrados.</p>';
            }
            break;
        case "porServicio":
            $html .= '<h4 style="text-align:center; font-family: Arial, sans-serif; color:#555;">Pagos por Servicio</h4>';
            $html .= '<hr style="margin-bottom:20px;">';

            $servicios = ControladorPlanPago::ctrPagosPorServicio();
            if (!empty($servicios)) {
                foreach ($servicios as $s) {
                    $html .= '<div style="' . $cardStyle . '">
                        <p><strong>Servicio:</strong> ' . $s['nombreServicio'] . '</p>
                        <p><strong>Solicitado:</strong> ' . $s['veces'] . ' veces</p>
                        <p><strong>Total Recaudado:</strong> ' . number_format($s['totalRecaudado']) . ' Bs</p>
                      </div>';
                }
            } else {
                $html .= '<p style="text-align:center;">No hay pagos por servicios registrados.</p>';
            }
            break;
        case "mensual":
            $html .= '<h4 style="text-align:center; font-family: Arial, sans-serif; color:#555;">Pagos Mensuales</h4>';
            $html .= '<hr style="margin-bottom:20px;">';

            $mensuales = ControladorPlanPago::ctrPagosMensuales();
            if (!empty($mensuales)) {
                foreach ($mensuales as $m) {
                    $html .= '<div style="' . $cardStyle . '">
                        <p><strong>Mes:</strong> ' . $m['mes'] . ' / ' . $m['anio'] . '</p>
                        <p><strong>Total:</strong> ' . number_format($m['total']) . ' Bs</p>
                      </div>';
                }
            } else {
                $html .= '<p style="text-align:center;">No hay pagos mensuales registrados.</p>';
            }
            break;
        default:
            $html .= '<p style="text-align:center;">Reporte no disponible.</p>';
    }
}

$mpdf->WriteHTML($html);
$nombreArchivo = 'reporte_pagos.pdf';
$mpdf->Output($nombreArchivo, 'I');
