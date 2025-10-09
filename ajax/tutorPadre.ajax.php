<?php

require_once "../controladores/tutorPadre.controlador.php";
require_once "../modelos/tutorPadre.modelo.php";

class ajaxTutorPadre
{

	/*=============================================
	EDITAR TutorPadre
	=============================================*/

	public $IdTutorPadre;

	public function ajaxEditarTutorPadre()
	{

		$item = "IdTutorPadre";
		$valor = $this->IdTutorPadre;

		$respuesta = ControladorTutorPadre::ctrMostrarTutorPadre($item, $valor);

		echo json_encode($respuesta);
	}
}

/*=============================================
EDITAR TutorPadre
=============================================*/

if (isset($_POST["IdTutorPadre"])) {

	$tutorPadre = new ajaxTutorPadre();
	$tutorPadre->IdTutorPadre = $_POST["IdTutorPadre"];
	$tutorPadre->ajaxEditarTutorPadre();
}
