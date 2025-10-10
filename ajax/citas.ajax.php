<?php

require_once "../controladores/citas.controlador.php";
require_once "../modelos/citas.modelo.php";

class AjaxCita
{
    public $idCita;

    /*=============================================
    EDITAR CITA
    =============================================*/
    public function ajaxEditarCita()
    {
        $item = "idCita";
        $valor = $this->idCita;
        $respuesta = ControladorCitas::ctrMostrarCitas($item, $valor);
        echo json_encode($respuesta);
    }

    /*=============================================
    LISTAR TODAS LAS CITAS CON COLOR POR ODONTÓLOGO
    =============================================*/
    public function ajaxListarCitas()
    {
        try {
            $citas = ControladorCitas::ctrMostrarCitas();

            $coloresOdontologos = [
                2 => '#4caf50',
                3 => '#2196f3',
                4 => '#ff9800',
                6 => '#e91e63',
                7 => '#dce91eff',
                8 => '#290630ff',
            ];

            $eventos = [];

            foreach ($citas as $cita) {
                $end = strlen($cita["horaFin"]) <= 8 ? $cita["fecha"] . "T" . $cita["horaFin"] : str_replace(' ', 'T', $cita["horaFin"]);
                $color = $coloresOdontologos[$cita["idUsuarios"]] ?? '#607d8b';

                $eventos[] = [
                    "id" => $cita["idCita"],
                    "title" => $cita["motivoConsulta"],
                    "start" => $cita["fecha"] . "T" . $cita["hora"],
                    "end" => $end,
                    "allDay" => false,
                    "backgroundColor" => $color,
                    "borderColor" => $color,
                    "textColor" => "#ffffff",
                    "extendedProps" => [
                        "idPaciente" => $cita["idPaciente"],
                        "idUsuarios" => $cita["idUsuarios"],
                        "motivoConsulta" => $cita["motivoConsulta"]
                    ]
                ];
            }

            echo json_encode($eventos);
        } catch (Exception $e) {
            error_log("Error en ajaxListarCitas: " . $e->getMessage());
            echo json_encode([]);
        }
    }

    /*=============================================
    LISTAR CITAS POR ODONTÓLOGO (CALENDARIO FILTRADO)
    =============================================*/
    public function ajaxListarPorOdontologo($idUsuarios)
    {
        $citas = ControladorCitas::ctrMostrarCitasPorOdontologo($idUsuarios);

        $coloresOdontologos = [
            2 => '#4caf50',
            3 => '#2196f3',
            4 => '#ff9800',
            6 => '#e91e63',
            7 => '#dce91eff',
            8 => '#290630ff',
        ];

        $eventos = [];

        foreach ($citas as $cita) {
            $end = strlen($cita["horaFin"]) <= 8 ? $cita["fecha"] . "T" . $cita["horaFin"] : str_replace(' ', 'T', $cita["horaFin"]);
            $color = $coloresOdontologos[$cita["idUsuarios"]] ?? '#607d8b';

            $eventos[] = [
                "id" => $cita["idCita"],
                "title" => $cita["motivoConsulta"],
                "start" => $cita["fecha"] . "T" . $cita["hora"],
                "end" => $end,
                "allDay" => false,
                "backgroundColor" => $color,
                "borderColor" => $color,
                "textColor" => "#ffffff",
                "extendedProps" => [
                    "idPaciente" => $cita["idPaciente"],
                    "idUsuarios" => $cita["idUsuarios"],
                    "motivoConsulta" => $cita["motivoConsulta"]
                ]
            ];
        }

        echo json_encode($eventos);
    }
    
}

/*=============================================
SOLICITUDES INDIVIDUALES
=============================================*/
if (isset($_POST["idCita"]) && !isset($_POST["accion"]) && !isset($_POST['estado'])) {
    $cita = new AjaxCita();
    $cita->idCita = $_POST["idCita"];
    $cita->ajaxEditarCita();
}

/*=============================================
MANEJO DE ACCIONES AJAX
=============================================*/
if (isset($_POST["accion"])) {
    $cita = new AjaxCita();

    switch ($_POST["accion"]) {
        case "editar":
            $cita->idCita = $_POST["idCita"];
            $cita->ajaxEditarCita();
            break;

        case "listar":
            $cita->ajaxListarCitas();
            break;

        case "listarPorOdontologo":
            if (isset($_POST["idUsuarios"])) {
                $cita->ajaxListarPorOdontologo($_POST["idUsuarios"]);
            } else {
                echo json_encode([]);
            }
            break;

        case "listarPorFecha":
            $fecha = $_POST["fecha"];
            $respuesta = ControladorCitas::ctrMostrarCitasPorFecha($fecha);
            echo json_encode($respuesta);
            break;
    }
}

/*=============================================
ACTUALIZAR ESTADO DE CITA
=============================================*/
if (isset($_POST['idCita'], $_POST['estado'])) {
    $idCita = $_POST['idCita'];
    $estado = $_POST['estado'];

    $resultado = ControladorCitas::ctrActualizarEstadoCita($idCita, $estado);

    if ($resultado === "ok") {
        // Retornamos además el estado actualizado para que el JS lo use
        echo json_encode([
            'success' => true,
            'estado' => $estado
        ]);
    } else {
        echo json_encode(['success' => false]);
    }

    exit; // <--- MUY IMPORTANTE
}
