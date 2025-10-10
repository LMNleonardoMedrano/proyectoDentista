<?php

class ControladorCitas
{

    /*=============================================
    CREAR CITA
    =============================================*/
    static public function ctrCrearCita()
    {
        if (isset($_POST["fechaCita"])) {
            if (
                preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST["fechaCita"]) &&
                preg_match('/^\d{2}:\d{2}$/', $_POST["horaCita"]) &&
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ,.()\/\-]+$/u', $_POST["motivoCita"]) &&
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ,.()\/\-]+$/u', $_POST["estado"]) &&
                preg_match('/^\d+$/', $_POST["pacienteCita"]) &&
                preg_match('/^\d+$/', $_POST["usuarioCita"])
            ) {
                $tabla = "citas";

                // Verificar si la hora está ocupada para el mismo odontólogo antes de registrar la cita
                $citaExistente = ModeloCita::mdlVerificarCitaOdontologo(
                    $tabla,
                    $_POST["fechaCita"],
                    $_POST["horaCita"],
                    $_POST["usuarioCita"]
                );

                if ($citaExistente) {
                    echo '<script>
                    swal({
                        type: "error",
                        title: "¡El odontólogo ya tiene una cita en esta fecha y hora!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if(result.value){
                            window.location = "citas";
                        }
                    });
                </script>';
                    return;
                }

                $datos = array(
                    "fecha" => $_POST["fechaCita"],
                    "hora" => $_POST["horaCita"],
                    "horaFin" => !empty($_POST["horaFinCita"]) ? $_POST["horaFinCita"] : date("H:i:s", strtotime($_POST["horaCita"] . ' +30 minutes')),
                    "motivoConsulta" => $_POST["motivoCita"],
                    "estado" => $_POST["estado"],
                    "idPaciente" => $_POST["pacienteCita"],
                    "idUsuarios" => $_POST["usuarioCita"]
                );

                $respuesta = ModeloCita::mdlIngresarCita($tabla, $datos);

                if ($respuesta == "ok") {
                    echo '<script>
                    swal({
                        type: "success",
                        title: "La cita ha sido guardada correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if(result.value){
                            window.location = "citas";
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
                        window.location = "citas";
                    }
                });
            </script>';
            }
        }
    }
    /*=============================================
    MOSTRAR CITAS
    =============================================*/
    static public function ctrMostrarCitas()
    {
        try {
            $tabla = "citas";
            return ModeloCita::mdlMostrarCitas($tabla);
        } catch (Exception $e) {
            error_log("Error en ctrMostrarCitas: " . $e->getMessage());
            return [];
        }
    }
/*=============================================
MOSTRAR TODAS LAS CITAS CON NOMBRES
=============================================*/
static public function ctrMostrarCitasCompletas()
{
    $tabla = "citas";
    return ModeloCita::mdlMostrarCitasCompletas($tabla);
}

    /*=============================================
    EDITAR CITA
    =============================================*/
    static public function ctrEditarCita()
    {
        if (isset($_POST["editarIdCita"])) {
            if (
                preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST["editarFecha"]) &&
                preg_match('/^\d{2}:\d{2}$/', $_POST["editarHora"]) &&
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ,.()\/\-]+$/u', $_POST["editarMotivo"]) &&
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ,.()\/\-]+$/u', $_POST["editarEstado"]) &&
                preg_match('/^\d+$/', $_POST["editarPaciente"]) &&
                preg_match('/^\d+$/', $_POST["editarUsuarios"])
            ) {

                $tabla = "citas";

                $datos = array(
                    "idCita" => $_POST["editarIdCita"],
                    "fecha" => $_POST["editarFecha"],
                    "hora" => $_POST["editarHora"],
                    "horaFin" => $_POST["editarHoraFin"],
                    "motivoConsulta" => $_POST["editarMotivo"],
                    "estado" => $_POST["editarEstado"],
                    "idPaciente" => $_POST["editarPaciente"],
                    "idUsuarios" => $_POST["editarUsuarios"]
                );

                $respuesta = ModeloCita::mdlEditarCita($tabla, $datos);

                if ($respuesta == "ok") {
                    echo '<script>
                        swal({
                            type: "success",
                            title: "La cita ha sido editada correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if(result.value){
                                window.location = "citas";
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
                            window.location = "citas";
                        }
                    });
                </script>';
            }
        }
    }
    /*=============================================
    ELIMINAR CITA
    =============================================*/
    public function ctrEliminarCita()
    {
        if (isset($_GET["eliminarCita"])) {
            $tabla = "citas";
            $datos = $_GET["eliminarCita"];
            $respuesta = ModeloCita::mdlEliminarCita($tabla, $datos);
            if ($respuesta == "ok") {
                echo '<script>
                    swal({
                        type: "success",
                        title: "la cita ha sido eliminado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if(result.value){
                            window.location = "citas";
                        }
                    });
                </script>';
            }
        }
    }
    static public function ctrMostrarCitasPorFecha($fecha)
    {
        return ModeloCita::mdlMostrarCitasPorFecha("citas", $fecha);
    }
    static public function ctrMostrarCitasPorOdontologo($idUsuarios)
    {
        return ModeloCita::mdlMostrarCitasPorOdontologo("citas", $idUsuarios);
    }

 static public function ctrActualizarEstadoCita($idCita, $estado){
    $tabla = "citas";
    $datos = [
        "idCita" => $idCita,
        "estado" => $estado
    ];

    $respuesta = ModeloCita::mdlActualizarEstadoCita($tabla, $datos);
    return $respuesta;
}


static public function ctrMostrarCitasFiltradas($estado)
{
    $tabla = "citas";
    return ModeloCita::mdlMostrarCitasFiltradas($tabla, $estado);
}












public static function ctrCitasProgramadas($desde=null, $hasta=null){
        return ModeloCita::mdlCitasProgramadas($desde, $hasta);
    }

    public static function ctrCitasConfirmadas($desde=null, $hasta=null){
        return ModeloCita::mdlCitasConfirmadas($desde, $hasta);
    }

    public static function ctrPacientesAtendidos($desde=null, $hasta=null){
        return ModeloCita::mdlPacientesAtendidos($desde, $hasta);
    }

    public static function ctrCitasCanceladas($desde=null, $hasta=null){
        return ModeloCita::mdlCitasCanceladas($desde, $hasta);
    }

    public static function ctrCitasPorDia($desde=null, $hasta=null){
        return ModeloCita::mdlCitasPorDia($desde, $hasta);
    }

    public static function ctrCitasPorOdontologo($desde=null, $hasta=null){
        return ModeloCita::mdlCitasPorOdontologo($desde, $hasta);
    }

    public static function ctrCitasPorServicio($desde=null, $hasta=null){
        return ModeloCita::mdlCitasPorServicio($desde, $hasta);
    }

    public static function ctrCitasMensuales(){
        return ModeloCita::mdlCitasMensuales();
    }

}
