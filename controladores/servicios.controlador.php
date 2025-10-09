<?php

class ControladorServicios {

    /*=============================================
    CREAR SERVICIO
    =============================================*/
    static public function ctrCrearServicio() {
        if (isset($_POST["nuevoNombreServicio"])) {
            if (
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoNombreServicio"]) &&
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ .,()]+$/', $_POST["nuevaDescripcion"]) &&
                is_numeric($_POST["nuevoPrecio"])
            ) {

                $tabla = "servicios";

                $datos = array(
                    "nombreServicio" => $_POST["nuevoNombreServicio"],
                    "descripcion" => $_POST["nuevaDescripcion"],
                    "precio" => $_POST["nuevoPrecio"]
                );

                $respuesta = ModeloServicios::mdlIngresarServicio($tabla, $datos);

                if ($respuesta == "ok") {
                    echo '<script>
                        swal({
                            type: "success",
                            title: "¡El servicio ha sido guardado correctamente!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if(result.value){
                                window.location = "servicios";
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
                            window.location = "servicios";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    EDITAR SERVICIO
    =============================================*/
    static public function ctrEditarServicio() {
        if (isset($_POST["editarIdServicio"])) {
            if (
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarNombreServicio"]) &&
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ .,()]+$/', $_POST["editarDescripcion"]) &&
                is_numeric($_POST["editarPrecio"])
            ) {

                $tabla = "servicios";

                $datos = array(
                    "idServicio" => $_POST["editarIdServicio"],
                    "nombreServicio" => $_POST["editarNombreServicio"],
                    "descripcion" => $_POST["editarDescripcion"],
                    "precio" => $_POST["editarPrecio"]
                );

                $respuesta = ModeloServicios::mdlEditarServicio($tabla, $datos);

                if ($respuesta == "ok") {
                    echo '<script>
                        swal({
                            type: "success",
                            title: "¡El servicio ha sido editado correctamente!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if(result.value){
                                window.location = "servicios";
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
                            window.location = "servicios";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    ELIMINAR SERVICIO
    =============================================*/
    static public function ctrEliminarServicio() {
        if (isset($_GET["idServicio"])) {

            $tabla = "servicios";
            $datos = $_GET["idServicio"];

            $respuesta = ModeloServicios::mdlBorrarServicio($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    swal({
                        type: "success",
                        title: "¡El servicio ha sido eliminado correctamente!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if(result.value){
                            window.location = "servicios";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    MOSTRAR SERVICIOS
    =============================================*/
    static public function ctrMostrarServicios($item, $valor) {
        $tabla = "servicios";
        $respuesta = ModeloServicios::mdlMostrarServicios($tabla, $item, $valor);
        return $respuesta;
    }

}
