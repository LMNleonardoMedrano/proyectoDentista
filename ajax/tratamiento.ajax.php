<?php
require_once "../controladores/tratamiento.controlador.php";
require_once "../modelos/tratamiento.modelo.php";

class AjaxTratamiento {

    public $idTratamiento;

    // Traer datos del tratamiento
    public function ajaxEditarTratamiento() {
        $item = "idTratamiento";
        $valor = $this->idTratamiento;
        $respuesta = ControladorTratamiento::ctrMostrarTratamientos($item, $valor);
        echo json_encode($respuesta);
    }

    // Traer medicamentos de un tratamiento
    public function ajaxMostrarMedicamentosTratamiento() {
        $item = "idTratamiento";
        $valor = $this->idTratamiento;
        $respuesta = ControladorTratamiento::ctrMostrarMedicamentos($item, $valor);
        echo json_encode($respuesta);
    }
}

// EDITAR TRATAMIENTO
if (isset($_POST["idTratamiento"])) {
    $editar = new AjaxTratamiento();
    $editar->idTratamiento = $_POST["idTratamiento"];
    $editar->ajaxEditarTratamiento();
}

// MOSTRAR MEDICAMENTOS
if (isset($_POST["idTratamientoMedicamentos"])) {
    $medicamentos = new AjaxTratamiento();
    $medicamentos->idTratamiento = $_POST["idTratamientoMedicamentos"];
    $medicamentos->ajaxMostrarMedicamentosTratamiento();
}
