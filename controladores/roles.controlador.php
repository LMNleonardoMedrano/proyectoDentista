<?php

class ControladorRoles {

    /*=============================================
    CREAR ROL
    =============================================*/
    static public function ctrCrearRoles() {
        if (isset($_POST["nuevoRol"])) {
            if (
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoRol"]) 
               
            ) {

                $tabla = "roles";

                $datos = array(
                    "nombreRol" => $_POST["nuevoRol"]
                );

                $respuesta = ModeloRoles::mdlIngresarRoles($tabla, $datos);

                if ($respuesta == "ok") {
                    echo '<script>
                        swal({
                            type: "success",
                            title: "¡El rol ha sido guardado correctamente!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if(result.value){
                                window.location = "roles";
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
                            window.location = "roles";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    EDITAR ROL
    =============================================*/
    static public function ctrEditarRoles() {
        if (isset($_POST["editaridRol"])) {
            if (
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarRol"]) 
            ) {

                $tabla = "roles";

                $datos = array(
                    "idRol" => $_POST["editaridRol"],
                    "nombreRol" => $_POST["editarRol"]
                );

                $respuesta = ModeloRoles::mdlEditarRoles($tabla, $datos);

                if ($respuesta == "ok") {
                    echo '<script>
                        swal({
                            type: "success",
                            title: "El Rol ha sido editado correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if(result.value){
                                window.location = "roles";
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
                            window.location = "roles";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    ELIMINAR ROL
    =============================================*/
    static public function ctrEliminarRoles() {
        if (isset($_GET[""])) {

            $tabla = "roles";
            $datos = $_GET["idRol"];

            $respuesta = ModeloMedicamento::mdlBorrarMedicamento($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    swal({
                        type: "success",
                        title: "El rol ha sido eliminado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if(result.value){
                            window.location = "roles";
                        }
                    });
                </script>';
            }
        }
    }






    static public function ctrMostrarFormulariosPorModulo($idRol, $modulo) {
    return ModeloRoles::mdlMostrarFormulariosPorModulo($idRol, $modulo);
}

static public function ctrMostrarPermisosPorRol($idRol) {
        return ModeloRoles::mdlMostrarPermisosPorRol($idRol);
    }
    /*=============================================
    MOSTRAR ROLES
    =============================================*/
    static public function ctrMostrarRoles($item, $valor) {
        $tabla = "roles";
        $respuesta = ModeloRoles::mdlMostrarRoles($tabla, $item, $valor);
        return $respuesta;
    }
     /*=============================================
    MOSTRAR PERMISOS
    =============================================*/
    static public function ctrMostrarPermisos() {
        return ModeloRoles::mdlMostrarPermisos("permisos");
    }

    /*=============================================
    MOSTRAR PERMISOS DE UN ROL
    =============================================*/
    static public function ctrMostrarPermisosRol($idRol) {
        return ModeloRoles::mdlMostrarPermisosRol($idRol);
    }

    /*=============================================
    ASIGNAR PERMISOS
    =============================================*/
    static public function ctrAsignarPermisos($idRol, $permisos) {
        return ModeloRoles::mdlAsignarPermisos($idRol, $permisos);
    }
}
