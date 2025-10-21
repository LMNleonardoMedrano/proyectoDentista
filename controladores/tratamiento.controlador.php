<?php

class ControladorTratamiento
{
/*=============================================
CREAR TRATAMIENTO
=============================================*/
static public function ctrCrearTratamiento()
{
    if (isset($_POST["nuevoFechaRegistro"])) {

        if (
            preg_match('/^[0-9-]+$/', $_POST["nuevoFechaRegistro"]) &&
            is_numeric($_POST["nuevoSaldo"]) &&
            is_numeric($_POST["nuevoTotalPago"]) &&
            preg_match('/^[a-zA-Z ]+$/', $_POST["nuevoEstado"]) &&
            preg_match('/^[a-zA-Z ]+$/', $_POST["nuevoEstadoPago"]) &&
            is_numeric($_POST["nuevoIdPaciente"]) &&
            is_numeric($_POST["nuevoIdUsuarios"])
        ) {

            $datosTratamiento = array(
                "fechaRegistro" => $_POST["nuevoFechaRegistro"],
                "saldo" => $_POST["nuevoSaldo"],
                "totalPago" => $_POST["nuevoTotalPago"],
                "estado" => $_POST["nuevoEstado"],
                "estadoPago" => $_POST["nuevoEstadoPago"],
                "idPaciente" => $_POST["nuevoIdPaciente"],
                "idUsuarios" => $_POST["nuevoIdUsuarios"]
            );

            $idTratamiento = ModeloTratamiento::mdlIngresarTratamiento('tratamiento', $datosTratamiento);

            if ($idTratamiento) {

                /* =============================================
                GUARDAR SERVICIOS
                ============================================= */
                if (isset($_POST['servicios']) && is_array($_POST['servicios'])) {
                    foreach ($_POST['servicios'] as $idServicio) {
                        $servicio = ModeloServicios::mdlMostrarServicios('servicios', 'idServicio', $idServicio);

                        ModeloTratamiento::mdlIngresarDetalleServicio('detalleTratamientoServicios', [
                            'idTratamiento' => $idTratamiento,
                            'idServicio' => $idServicio,
                            'cantidad' => 1,
                            'precio' => $servicio['precio']
                        ]);
                    }
                }

                /* =============================================
                GUARDAR MEDICAMENTOS
                ============================================= */
                if (
                    isset($_POST['codMedicamento']) &&
                    isset($_POST['dosis']) &&
                    isset($_POST['fechaInicio']) &&
                    isset($_POST['fechaFinal'])
                ) {
                    $totalMedicamentos = count($_POST['codMedicamento']);
                    for ($i = 0; $i < $totalMedicamentos; $i++) {
                        $datosMedicamento = array(
                            'idTratamiento' => $idTratamiento,
                            'codMedicamento' => $_POST['codMedicamento'][$i],
                            'dosis' => $_POST['dosis'][$i],
                            'fechaInicio' => $_POST['fechaInicio'][$i],
                            'fechaFinal' => $_POST['fechaFinal'][$i],
                            'observacion' => $_POST['observacion'][$i] ?? null,
                            'tiempo' => $_POST['tiempo'][$i] ?? null
                        );

                        ModeloTratamiento::mdlIngresarDetalleMedicamento('detallemedicamento', $datosMedicamento);
                    }
                }

                /* =============================================
                ACTUALIZAR ESTADO DE CITA (si existe seleccionada)
                ============================================= */
                if (!empty($_POST["idCitaSeleccionada"])) {
                    $datosCita = array(
                        "idCita" => $_POST["idCitaSeleccionada"],
                        "estado" => "atendida"
                    );
                    ModeloCita::mdlActualizarEstadoCita("citas", $datosCita);

                    // Enviar idCita al frontend con JS
                    echo '<script>
                        document.addEventListener("DOMContentLoaded", function() {
                            let citaItem = document.querySelector("[data-idcita=\'' . $_POST["idCitaSeleccionada"] . '\']");
                            if(citaItem){
                                citaItem.remove();
                            }
                        });
                    </script>';
                }

                /* =============================================
                RESPUESTA OK
                ============================================= */
                echo '<script>
                    swal({
                        type: "success",
                        title: "¡El tratamiento se guardó correctamente!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if(result.value){
                            window.location = "tratamiento";
                        }
                    });
                </script>';
            } else {
                echo '<script>
                    swal({
                        type: "error",
                        title: "¡Error al guardar tratamiento!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    });
                </script>';
            }
        } else {
            /* =============================================
            ALERTA POR DATOS INCOMPLETOS O INVÁLIDOS
            ============================================= */
            echo '<script>
                swal({
                    type: "warning",
                    title: "¡Faltan datos !",
                    text: "Por favor, revisa que todos los campos estén completos y correctamente llenados.",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                });
            </script>';
        }
    }
}

    /*=============================================
GUARDAR NUEVOS MEDICAMENTOS (ACUMULANDO)
=============================================*/
    public function ctrGuardarMedicamentos()
    {
        if (isset($_POST['idTratamientoMedicamentos']) && !empty($_POST['idTratamientoMedicamentos'])) {

            $idTratamiento = (int)$_POST['idTratamientoMedicamentos'];

            // Medicamentos Nuevos
            if (isset($_POST['codMedicamento']) && is_array($_POST['codMedicamento'])) {
                foreach ($_POST['codMedicamento'] as $i => $cod) {
                    $datosMedicamento = array(
                        'idTratamiento' => $idTratamiento,
                        'codMedicamento' => $cod,
                        'dosis' => $_POST['dosis'][$i] ?? null,
                        'fechaInicio' => $_POST['fechaInicio'][$i] ?? null,
                        'fechaFinal' => $_POST['fechaFinal'][$i] ?? null,
                        'observacion' => $_POST['observacion'][$i] ?? null,
                        'tiempo' => $_POST['tiempo'][$i] ?? null
                    );

                    ModeloTratamiento::mdlIngresarDetalleMedicamento('detallemedicamento', $datosMedicamento);
                }
            }

            echo '<script>
            swal({
                type: "success",
                title: "Medicamentos guardados correctamente",
                showConfirmButton: true,
                confirmButtonText: "Cerrar"
            }).then(function(result){
                if(result.value){ window.location = "tratamiento"; }
            });
        </script>';
        }
    }


    /*=============================================
    EDITAR TRATAMIENTO
    =============================================*/
    static public function ctrEditarTratamiento()
    {
        if (isset($_POST["editarIdTratamiento"])) {
            $tabla = "tratamiento";
            $idUsuario = $_SESSION["idUsuarios"];

            $datos = array(
                "idTratamiento" => $_POST["editarIdTratamiento"],
                "fechaRegistro" => $_POST["editarFechaRegistro"],
                "saldo" => $_POST["editarSaldo"],
                "totalPago" => $_POST["editarTotalPago"],
                "estado" => $_POST["editarEstado"],
                "estadoPago" => $_POST["editarEstadoPago"],
                "idPaciente" => $_POST["editarIdPaciente"],
                "idUsuarios" => $idUsuario
            );

            $respuesta = ModeloTratamiento::mdlEditarTratamiento($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    swal({
                        type: "success",
                        title: "El tratamiento ha sido editado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if(result.value){
                            window.location = "tratamiento";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    ELIMINAR TRATAMIENTO
    =============================================*/
    static public function ctrEliminarTratamiento()
    {
        if (isset($_GET["idTratamiento"])) {
            $tabla = "tratamiento";
            $datos = $_GET["idTratamiento"];
            $respuesta = ModeloTratamiento::mdlBorrarTratamiento($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    swal({
                        type: "success",
                        title: "El tratamiento ha sido eliminado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if(result.value){
                            window.location = "tratamiento";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    MOSTRAR TRATAMIENTOS
    =============================================*/
    static public function ctrMostrarTratamientos($item = null, $valor = null)
    {
        $tabla = "tratamiento";
        return ModeloTratamiento::mdlMostrarTratamientos($tabla, $item, $valor);
    }

    /*=============================================
    MOSTRAR DETALLE MEDICAMENTOS
    =============================================*/
    static public function ctrMostrarDetalleMedicamento($item = null, $valor = null)
    {
        $tabla = "detallemedicamento";
        return ModeloTratamiento::mdlMostrarDetalleMedicamento($tabla, $item, $valor);
    }

    /*=============================================
    MOSTRAR DETALLE SERVICIOS
    =============================================*/
    static public function ctrMostrarDetalleServicios($item = null, $valor = null)
    {
        $tabla = "detalleTratamientoServicios";
        return ModeloTratamiento::mdlMostrarDetalleServicios($tabla, $item, $valor);
    }

    static public function ctrActualizarSaldoTratamiento($idTratamiento, $nuevoSaldo)
    {
        return ModeloTratamiento::mdlActualizarSaldo($idTratamiento, $nuevoSaldo);
    }

    /*=============================================
    ACTUALIZAR ESTADO DE PAGO
    =============================================*/
    static public function ctrActualizarEstadoPago($idTratamiento, $estadoPago)
    {
        return ModeloTratamiento::mdlActualizarEstadoPago($idTratamiento, $estadoPago);
    }
    /*=============================================
OBTENER SERVICIOS POR TRATAMIENTO
=============================================*/
    static public function ctrObtenerServiciosPorTratamiento($idTratamiento)
    {
        if (!empty($idTratamiento) && is_numeric($idTratamiento)) {
            return ModeloTratamiento::mdlObtenerServiciosPorTratamiento($idTratamiento);
        }
        return [];
    }
    /*=============================================
    MOSTRAR TRATAMIENTOS PENDIENTES
    =============================================*/
    static public function ctrMostrarTratamientosPendientes()
    {

        $tabla = "tratamiento";
        $respuesta = ModeloTratamiento::mdlMostrarTratamientosPendientes($tabla);

        return $respuesta;
    }










    static public function ctrTratamientosCompletados($desde = null, $hasta = null)
    {
        return ModeloTratamiento::mdlTratamientosCompletados($desde, $hasta);
    }

    static public function ctrTratamientosParciales($desde = null, $hasta = null)
    {
        return ModeloTratamiento::mdlTratamientosParciales($desde, $hasta);
    }

    static public function ctrTratamientosActivos($desde = null, $hasta = null)
    {
        return ModeloTratamiento::mdlTratamientosActivos($desde, $hasta);
    }

    static public function ctrTratamientosNoCancelados($desde = null, $hasta = null)
    {
        return ModeloTratamiento::mdlTratamientosNoCancelados($desde, $hasta);
    }

    static public function ctrTratamientosPorOdontologo($desde = null, $hasta = null)
    {
        return ModeloTratamiento::mdlTratamientosPorOdontologo($desde, $hasta);
    }

    static public function ctrTratamientosPorServicio($desde = null, $hasta = null)
    {
        return ModeloTratamiento::mdlTratamientosPorServicio($desde, $hasta);
    }

    static public function ctrTratamientosPorEstado($desde = null, $hasta = null)
    {
        return ModeloTratamiento::mdlTratamientosPorEstado($desde, $hasta);
    }

    static public function ctrTratamientosMensuales()
    {
        return ModeloTratamiento::mdlTratamientosMensuales();
    }
}
