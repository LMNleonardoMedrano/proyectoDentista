<?php

require_once "../controladores/usuarios.controlador.php";
require_once "../modelos/usuarios.modelo.php";

class AjaxUsuarios
{
  /*=============================================
    EDITAR USUARIO
    =============================================*/
    public $idUsuarios; // Variable pública para almacenar el id del usuario

    public function ajaxEditarUsuario()
    {
        // Definir el item que se va a buscar (en este caso, 'idUsuarios')
        $item = "idUsuarios"; 
        $valor = $this->idUsuarios;  // Asignar el valor del idUsuarios recibido desde el frontend

        // Llamar al controlador para obtener la información del usuario
        $respuesta = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);

        // Verificar si la respuesta contiene datos
        if ($respuesta) {
            // Retornar la respuesta en formato JSON para que sea procesada por el frontend
            echo json_encode($respuesta);
        } else {
            // Si no hay datos, retornar un mensaje de error
            echo json_encode(array("error" => "Usuario no encontrado"));
        }
    }

    /*=============================================
    ACTIVAR USUARIO
    =============================================*/

    public $activarUsuario;
    public $activarId;

    public function ajaxActivarUsuario()
    {
        $tabla = "usuarios";
        $item1 = "estado";
        $valor1 = $this->activarUsuario;
        $item2 = "idUsuarios";
        $valor2 = $this->activarId;

        $respuesta = ModeloUsuarios::mdlActualizarUsuario($tabla, $item1, $valor1, $item2, $valor2);

        if ($respuesta) {
            echo json_encode(array("exito" => true, "mensaje" => "El usuario ha sido actualizado correctamente"));
        } else {
            echo json_encode(array("exito" => false, "mensaje" => "Hubo un error al actualizar el estado del usuario"));
        }
    }

    /*=============================================
    VALIDAR NO REPETIR USUARIO
    =============================================*/

    public $validarUsuario;

    public function ajaxValidarUsuario()
    {
        $item = "usuario";
        $valor = $this->validarUsuario;

        $respuesta = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);

        if (count($respuesta) > 0) {
            echo json_encode(array("existe" => true, "mensaje" => "Este usuario ya existe en la base de datos"));
        } else {
            echo json_encode(array("existe" => false));
        }
    }

    /*=============================================
    VALIDAR NO REPETIR CI
    =============================================*/

    public $validarCi;

    public function ajaxValidarCI()
    {
        $item = "ci";
        $valor = $this->validarCi;

        $respuesta = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);

        if (count($respuesta) > 0) {
            echo json_encode(array("existe" => true, "mensaje" => "Este CI ya está registrado en la base de datos"));
        } else {
            echo json_encode(array("existe" => false));
        }
    }
}

/*=============================================
EDITAR USUARIO
=============================================*/

if (isset($_POST["idUsuarios"])) {
    // Crear una instancia de la clase AjaxUsuarios
    $editar = new AjaxUsuarios();
    
    // Asignar el idUsuarios recibido a la propiedad idUsuarios de la clase
    $editar->idUsuarios = $_POST["idUsuarios"];
    
    // Llamar al método ajaxEditarUsuario() para obtener los datos del usuario
    $editar->ajaxEditarUsuario();
}

/*=============================================
ACTIVAR USUARIO
=============================================*/
if (isset($_POST["activarUsuario"])) {
    $activarUsuario = new AjaxUsuarios();
    $activarUsuario->activarUsuario = $_POST["activarUsuario"];
    $activarUsuario->activarId = $_POST["activarId"];
    $activarUsuario->ajaxActivarUsuario();
}

/*=============================================
VALIDAR NO REPETIR USUARIO
=============================================*/
if (isset($_POST["validarUsuario"])) {
    $valUsuario = new AjaxUsuarios();
    $valUsuario->validarUsuario = $_POST["validarUsuario"];
    $valUsuario->ajaxValidarUsuario();
}

/*=============================================
VALIDAR NO REPETIR CI
=============================================*/
if (isset($_POST["validarCi"])) {
    $valCi = new AjaxUsuarios();
    $valCi->validarCi = $_POST["validarCi"];
    $valCi->ajaxValidarCI();
}
