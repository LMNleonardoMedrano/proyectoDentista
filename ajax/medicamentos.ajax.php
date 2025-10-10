<?php

require_once "../controladores/medicamentos.controlador.php";
require_once "../modelos/medicamentos.modelo.php";

class AjaxMedicamento {

    /*=============================================
    EDITAR MEDICAMENTO
    =============================================*/	

    public $codMedicamento;

    public function ajaxEditarMedicamento() {

        $item = "codMedicamento";
        $valor = $this->codMedicamento;

        $respuesta = ControladorMedicamento::ctrMostrarMedicamentos($item, $valor);

        echo json_encode($respuesta);
    }
}

/*=============================================
EDITAR MEDICAMENTO
=============================================*/	

if (isset($_POST["codMedicamento"])) {

    $medicamento = new AjaxMedicamento();
    $medicamento->codMedicamento = $_POST["codMedicamento"];
    $medicamento->ajaxEditarMedicamento();
}
