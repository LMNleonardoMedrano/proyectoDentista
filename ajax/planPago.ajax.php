<?php
require_once "../controladores/planPagoTratamiento.controlador.php";
require_once "../modelos/planPagoTratamiento.modelo.php";

class AjaxPlanPago {

    public $codPlan;

    public function ajaxEditarPlanPago() {
        $item = "codPlan";
        $valor = $this->codPlan;

        $respuesta = ControladorPlanPago::ctrMostrarPlanesPago($item, $valor);

        echo json_encode($respuesta);
    }
}

if (isset($_POST["codPlan"])) {
    $planPago = new AjaxPlanPago();
    $planPago->codPlan = $_POST["codPlan"];
    $planPago->ajaxEditarPlanPago();
}
