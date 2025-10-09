<?php
require_once "../controladores/odontograma.controlador.php";
require_once "../modelos/odontograma.modelo.php";

class AjaxOdontograma {

    public $idTratamiento;

    public function ajaxMostrarOdontograma() {

        $item = "idTratamiento";
        $valor = $this->idTratamiento;

        $respuesta = ControladorOdontograma::ctrMostrarOdontograma($item, $valor);

        echo json_encode($respuesta);
    }
    public function guardarImagen() {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input || !isset($input["imageData"])) {
      echo json_encode(["error" => "Datos inválidos"]);
      return;
    }

    $imageData = $input["imageData"];
    $user = preg_replace('/[^a-zA-Z0-9_-]/', '', $input["user"]);
    $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '', $input["fileName"]);

    $pathDir = "../vistas/img/odontogramas/$user";
    if (!file_exists($pathDir)) mkdir($pathDir, 0755, true);

    $imageData = str_replace('data:image/png;base64,', '', $imageData);
    $imageData = str_replace(' ', '+', $imageData);
    $decodedImage = base64_decode($imageData);

    $filePath = "$pathDir/$fileName.jpg";
    file_put_contents($filePath, $decodedImage);

    echo json_encode([
      "success" => true,
      "filePath" => str_replace("../", "", $filePath)
    ]);
  }
}

/*=============================================
MOSTRAR ODONTOGRAMA
=============================================*/	
if (isset($_POST["idTratamiento"])) {

    $odontograma = new AjaxOdontograma();
    $odontograma->idTratamiento = $_POST["idTratamiento"];
    $odontograma->ajaxMostrarOdontograma();
}
// Ejecutar si es POST
$ajax = new AjaxOdontograma();
$ajax->guardarImagen();