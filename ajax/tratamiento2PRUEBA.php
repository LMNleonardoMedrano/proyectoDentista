<?php
require_once "../controladores/tratamiento.controlador.php";
require_once "../modelos/tratamiento.modelo.php";

class AjaxTratamientos {

  /*=============================================
  EDITAR TRATAMIENTO COMPLETO
  =============================================*/
  public $idTratamiento;

  public function ajaxEditarTratamiento() {
    $id = $this->idTratamiento;

    // Controlador devuelve datos clínicos completos
    $respuesta = ControladorTratamiento::ctrMostrarTratamientoCompleto($id); // método que une tratamiento, medicamento y odontograma

    // Log the response to check data structure
    error_log(print_r($respuesta, true));

    echo json_encode($respuesta);
  }
}

/*=============================================
RECIBIR PETICIÓN
=============================================*/
if (isset($_POST["idTratamiento"])) {
  $editar = new AjaxTratamientos();
  $editar->idTratamiento = $_POST["idTratamiento"];
  $editar->ajaxEditarTratamiento();
}
?>
