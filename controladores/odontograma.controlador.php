<?php

class ControladorOdontograma {

    /*=============================================
    CREAR ODONTOGRAMA
    =============================================*/
    static public function ctrCrearOdontograma() {
        if (isset($_POST["descripcionOdontograma"])) {
            if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["descripcionOdontograma"])) {

                $tabla = "odontograma";

                $datos = array(
                    "descripcion" => $_POST["descripcionOdontograma"],
                    "estado" => "Activo",
                    "idTratamiento" => $_POST["idTratamiento"]
                );

                $respuesta = ModeloOdontograma::mdlIngresarOdontograma($tabla, $datos);

                if ($respuesta == "ok") {
                    echo '<script>
                        swal({
                            type: "success",
                            title: "¡El odontograma ha sido guardado correctamente!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if(result.value){
                                window.location = "tratamientos";
                            }
                        });
                    </script>';
                }

            } else {
                echo '<script>
                    swal({
                        type: "error",
                        title: "¡La descripción no puede ir vacía o contener caracteres no válidos!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if(result.value){
                            window.location = "tratamientos";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    EDITAR ODONTOGRAMA
    =============================================*/
    static public function ctrEditarOdontograma() {
        if (isset($_POST["editarIdOdontograma"])) {
            if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarDescripcionOdontograma"])) {

                $tabla = "odontograma";

                $datos = array(
                    "idOdontograma" => $_POST["editarIdOdontograma"],
                    "descripcion" => $_POST["editarDescripcionOdontograma"],
                    "estado" => $_POST["editarEstadoOdontograma"]
                );

                $respuesta = ModeloOdontograma::mdlEditarOdontograma($tabla, $datos);

                if ($respuesta == "ok") {
                    echo '<script>
                        swal({
                            type: "success",
                            title: "El odontograma ha sido actualizado correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if(result.value){
                                window.location = "tratamientos";
                            }
                        });
                    </script>';
                }
            } else {
                echo '<script>
                    swal({
                        type: "error",
                        title: "¡La descripción no puede contener caracteres no válidos!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if(result.value){
                            window.location = "tratamientos";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    ELIMINAR ODONTOGRAMA
    =============================================*/
    static public function ctrEliminarOdontograma() {
        if (isset($_GET["idOdontograma"])) {
            $tabla = "odontograma";
            $datos = $_GET["idOdontograma"];

            $respuesta = ModeloOdontograma::mdlBorrarOdontograma($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    swal({
                        type: "success",
                        title: "El odontograma ha sido eliminado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if(result.value){
                            window.location = "tratamientos";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    MOSTRAR ODONTOGRAMAS
    =============================================*/
    static public function ctrMostrarOdontograma($item, $valor) {
        $tabla = "odontograma";
        $respuesta = ModeloOdontograma::mdlMostrarOdontograma($tabla, $item, $valor);
        return $respuesta;
    }
}