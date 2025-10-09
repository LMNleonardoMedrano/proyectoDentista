<?php
require_once "../controladores/paciente.controlador.php";
require_once "../modelos/paciente.modelo.php";

class AjaxPacientes {

    public $idPaciente;
    public $validarCi;

    /*=============================================
    EDITAR PACIENTE (devuelve datos + tutor si aplica)
    =============================================*/
    public function ajaxEditarPaciente() {
        $item = "idPaciente";
        $valor = $this->idPaciente;

        $paciente = ControladorPaciente::ctrMostrarPaciente($item, $valor);

        // Si es array de arrays, tomar la primera fila
        if (isset($paciente[0])) {
            $paciente = $paciente[0];
        }

        // Calcular edad y traer tutor si <18
        if (isset($paciente["fechaNac"])) {
            $fn = new DateTime($paciente["fechaNac"]);
            if ($fn->diff(new DateTime())->y < 18) {
                $paciente["tutor"] = ModeloPaciente::mdlMostrarTutorPorPaciente("pacienteMenor", $valor);
            }
        }

        echo json_encode($paciente);
    }

    /*=============================================
    VALIDAR NO REPETIR CI
    =============================================*/
    public function ajaxValidarCI() {
        $item = "ci";
        $valor = $this->validarCi;

        $respuesta = ControladorPaciente::ctrMostrarPaciente($item, $valor);

        if ($respuesta) {
            echo json_encode(["existe" => true, "mensaje" => "Este CI ya está registrado"]);
        } else {
            echo json_encode(["existe" => false]);
        }
    }
}

/*=============================================
PETICIONES
=============================================*/

// ✅ Editar paciente (flujo principal)
if (isset($_POST["idPaciente"])) {
    $editar = new AjaxPacientes();
    $editar->idPaciente = $_POST["idPaciente"];
    $editar->ajaxEditarPaciente();
    exit;
}

// Validar CI
if (isset($_POST["validarCi"])) {
    $valCi = new AjaxPacientes();
    $valCi->validarCi = $_POST["validarCi"];
    $valCi->ajaxValidarCI();
    exit;
}

// Obtener tutor directo (si lo quieres separado)
if (isset($_GET["accion"]) && $_GET["accion"] == "getTutor" && isset($_GET["idPaciente"])) {
    $id = intval($_GET["idPaciente"]);
    $tutor = ModeloPaciente::mdlMostrarTutorPorPaciente("pacienteMenor", $id);
    echo json_encode($tutor);
    exit;
}
