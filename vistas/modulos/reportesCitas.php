<?php

require_once '../../vendor/autoload.php';
require_once '../../controladores/citas.controlador.php';
require_once '../../controladores/paciente.controlador.php';
require_once '../../controladores/usuarios.controlador.php';

require_once '../../modelos/citas.modelo.php';
require_once '../../modelos/paciente.modelo.php';
require_once '../../modelos/usuarios.modelo.php';

use Mpdf\Mpdf;

$mpdf = new Mpdf();
$mpdf->SetTitle('Reporte de Citas');

$citas = ControladorCitas::ctrMostrarCitas();

if (!empty($citas)) {
    foreach ($citas as $i => $cita) {
        $paciente = ControladorPaciente::ctrMostrarPaciente("idPaciente", $cita["idPaciente"]);
        $nombrePaciente = $paciente ? $paciente["nombre"] : "Desconocido";

        $odontologo = ControladorUsuarios::ctrMostrarUsuarios("idUsuarios", $cita["idUsuarios"]);
        $nombreOdontologo = $odontologo ? $odontologo["nombre"] . " " . $odontologo["apellido"] : "Desconocido";

        // Cada cita en una página diferente
        if ($i > 0) {
            $mpdf->AddPage();
        }

        $html = '
        <h2 style="text-align:center;">Ficha de Cita</h2>
        <div style="border:1px solid #888; border-radius:8px; padding:18px; margin:30px auto; width:80%; font-family: Arial, sans-serif; font-size:14px;">
            <h3 style="text-align:center; margin-top:0;">Cita #' . ($i + 1) . '</h3>
            <p><strong>Fecha:</strong> ' . $cita["fecha"] . '</p>
            <p><strong>Hora Inicio:</strong> ' . $cita["hora"] . '</p>
            <p><strong>Hora Fin:</strong> ' . $cita["horaFin"] . '</p>
            <p><strong>Motivo de la Consulta:</strong> ' . $cita["motivoConsulta"] . '</p>
            <p><strong>Paciente:</strong> ' . $nombrePaciente . '</p>
            <p><strong>Odontólogo:</strong> ' . $nombreOdontologo . '</p>
        </div>
        ';

        $mpdf->WriteHTML($html);
    }

    $nombreArchivo = 'reporte_citas.pdf';
    $mpdf->Output($nombreArchivo, 'I');
} else {
    echo "No hay citas registradas.";
}
?>
