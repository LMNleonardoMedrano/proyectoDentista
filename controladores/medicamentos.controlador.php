<?php

class ControladorMedicamento {

    /*=============================================
    CREAR MEDICAMENTO
    =============================================*/
    static public function ctrCrearMedicamento() {
        if (isset($_POST["nuevoNombre"])) {
            if (
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoNombre"]) &&
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoTipo"]) &&
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevaMedida"]) &&
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoTiempo"])
            ) {

                $tabla = "medicamento";

                $datos = array(
                    "nombre" => $_POST["nuevoNombre"],
                    "tipo" => $_POST["nuevoTipo"],
                    "medida" => $_POST["nuevaMedida"],
                    "tiempo" => $_POST["nuevoTiempo"]
                );

                $respuesta = ModeloMedicamento::mdlIngresarMedicamento($tabla, $datos);

                if ($respuesta == "ok") {
                    echo '<script>
                        swal({
                            type: "success",
                            title: "¡El medicamento ha sido guardado correctamente!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if(result.value){
                                window.location = "medicamentos";
                            }
                        });
                    </script>';
                }

            } else {
                echo '<script>
                    swal({
                        type: "error",
                        title: "¡Los campos no pueden ir vacíos o contener caracteres no válidos!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if(result.value){
                            window.location = "medicamentos";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    EDITAR MEDICAMENTO
    =============================================*/
    static public function ctrEditarMedicamento() {
        if (isset($_POST["editarCodMedicamento"])) {
            if (
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarNombre"]) &&
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarTipo"]) &&
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarMedida"]) &&
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarTiempo"])
            ) {

                $tabla = "medicamento";

                $datos = array(
                    "codMedicamento" => $_POST["editarCodMedicamento"],
                    "nombre" => $_POST["editarNombre"],
                    "tipo" => $_POST["editarTipo"],
                    "medida" => $_POST["editarMedida"],
                    "tiempo" => $_POST["editarTiempo"]
                );

                $respuesta = ModeloMedicamento::mdlEditarMedicamento($tabla, $datos);

                if ($respuesta == "ok") {
                    echo '<script>
                        swal({
                            type: "success",
                            title: "El medicamento ha sido editado correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if(result.value){
                                window.location = "medicamentos";
                            }
                        });
                    </script>';
                }

            } else {
                echo '<script>
                    swal({
                        type: "error",
                        title: "¡Los campos no pueden ir vacíos o contener caracteres no válidos!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if(result.value){
                            window.location = "medicamentos";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    ELIMINAR MEDICAMENTO
    =============================================*/
    static public function ctrEliminarMedicamento() {
        if (isset($_GET["codMedicamento"])) {

            $tabla = "medicamento";
            $datos = $_GET["codMedicamento"];

            $respuesta = ModeloMedicamento::mdlBorrarMedicamento($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    swal({
                        type: "success",
                        title: "El medicamento ha sido eliminado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if(result.value){
                            window.location = "medicamentos";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    MOSTRAR MEDICAMENTOS
    =============================================*/
    static public function ctrMostrarMedicamentos($item, $valor) {
        $tabla = "medicamento";
        $respuesta = ModeloMedicamento::mdlMostrarMedicamentos($tabla, $item, $valor);
        return $respuesta;
    }
}
