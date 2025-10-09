<?php

require_once "../controladores/tratamiento.controlador.php";
require_once "../modelos/tratamiento.modelo.php";

class AjaxTratamiento {

    /*=============================================
    EDITAR TRATAMIENTO
    =============================================*/    

    public $idTratamiento;

    public function ajaxEditarTratamiento() {

        $item = "idTratamiento";
        $valor = $this->idTratamiento;

        $respuesta = ControladorTratamiento::ctrMostrarTratamientos($item, $valor);

        echo json_encode($respuesta);
    }
}

/*=============================================
EDITAR TRATAMIENTO
=============================================*/    

if (isset($_POST["idTratamiento"])) {

    $tratamiento = new AjaxTratamiento();
    $tratamiento->idTratamiento = $_POST["idTratamiento"];
    $tratamiento->ajaxEditarTratamiento();
}
