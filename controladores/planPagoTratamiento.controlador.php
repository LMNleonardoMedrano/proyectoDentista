<?php
class ControladorPlanPago {

    /*=============================================
CREAR PLAN DE PAGO
=============================================*/
static public function ctrCrearPlanPago() {
    if (isset($_POST["nuevoDescripcion"])) {

        // Validaciones de los campos
        if (
            preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoDescripcion"]) &&
            is_numeric($_POST["nuevoDescuento"]) &&
            preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST["nuevoFecha"]) &&
            is_numeric($_POST["nuevoMonto"]) &&
            is_numeric($_POST["nuevoTratamiento"]) &&
            is_numeric($_POST["nuevoTipoPago"])
        ) {

            $tabla = "planPagoTratamiento";

            $datos = array(
                "descripcion" => $_POST["nuevoDescripcion"],
                "descuento" => $_POST["nuevoDescuento"],
                "fecha" => $_POST["nuevoFecha"],
                "monto" => $_POST["nuevoMonto"],
                "idTratamiento" => $_POST["nuevoTratamiento"],
                "codTipoPago" => $_POST["nuevoTipoPago"]
            );

            // Guardar plan de pago y obtener el codPlan
            $codPlan = ModeloPlanPago::mdlIngresarPlanPago($tabla, $datos);

            if ($codPlan) {

                // 🔁 ACTUALIZAR SALDO DEL TRATAMIENTO
                $monto = $_POST["nuevoMonto"];
                $idTratamiento = $_POST["nuevoTratamiento"];
                ModeloPlanPago::mdlActualizarSaldoTratamiento($idTratamiento, $monto);

                // 🔎 Verificar saldo actual y actualizar estados
                $tratamiento = ModeloTratamiento::mdlMostrarTratamientos("tratamiento", "idTratamiento", $idTratamiento);
                $saldo = $tratamiento["saldo"];
                $estadoPago = $tratamiento["estadoPago"];

                if ($estadoPago == "pendiente" && $saldo > 0) {
                    ModeloTratamiento::mdlActualizarEstadoPago($idTratamiento, "parcial");
                    ModeloTratamiento::mdlActualizarEstado($idTratamiento, "activo");
                }

                if ($saldo <= 0) {
                    ModeloTratamiento::mdlActualizarEstadoPago($idTratamiento, "pagado");
                    ModeloTratamiento::mdlActualizarEstado($idTratamiento, "completado");
                }

                // 🔑 Generar token para el recibo
                $claveSecreta = "TuClaveUltraPrivada2025";
                $token = hash('sha256', $codPlan . $claveSecreta);

                // Mostrar SweetAlert y abrir recibo en nueva pestaña
                echo '<script>
                    swal({
                        type: "success",
                        title: "¡El plan de pago ha sido guardado correctamente!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if(result.value){
                            window.open("vistas/modulos/reciboQR.php?codPlan='.$codPlan.'&token='.$token.'", "_blank");
                            $("#modalAgregarPlanPago").modal("hide");
                            window.location = "tratamiento";
                        }
                    });
                </script>';

            } else {
                echo '<script>
                    swal({
                        type: "error",
                        title: "¡Error al guardar el plan de pago!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
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
                });
            </script>';
        }
    }
}
    /*=============================================
    EDITAR PLAN DE PAGO
    =============================================*/
static public function ctrEditarPlanPago(){
        if(isset($_POST["idPlanPagoEditar"])){

            $tabla = "planpagotratamiento";

            $datos = array(
                "codPlan" => $_POST["idPlanPagoEditar"],
                "descripcion" => $_POST["editarDescripcion"],
                "descuento" => $_POST["editarDescuento"],
                "fecha" => $_POST["editarFecha"],
                "monto" => $_POST["editarMonto"],
                "codTipoPago" => $_POST["editarTipoPago"]
            );

            $respuesta = ModeloPlanPago::mdlEditarPlanPago($tabla, $datos);

            if($respuesta == "ok"){
                echo '<script>
                    swal({
                            type: "success",
                            title: "El pago ha sido editado correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if(result.value){
                                window.location = "planPagoTratamiento";
                            }
                        });
                </script>';
            } else {
                echo '<script>
                    swal({
                        type: "error",
                        title: "¡Los campos no pueden ir vacíos o contener caracteres no válidos!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if(result.value){
                            window.location = "planPagoTratamiento";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    MOSTRAR PLANES DE PAGO
    =============================================*/
    static public function ctrMostrarPlanesPago($item, $valor) {
        $tabla = "planPagoTratamiento";
        $respuesta = ModeloPlanPago::mdlMostrarPlanesPago($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    MOSTRAR TIPOS DE PAGO
    =============================================*/
    public static function ctrMostrarTiposPago() {
        return ModeloPlanPago::mdlMostrarTiposPago();
    }
    public static function ctrMostrarPagosPorTratamiento($idTratamiento) {
  return ModeloPlanPago::mdlMostrarPagosPorTratamiento($idTratamiento);
}
 /*=============================================
    ELIMINAR MEDICAMENTO
    =============================================*/
    static public function ctrEliminarPlanPago() {
        if (isset($_GET["codPlan"])) {

            $tabla = "planpagotratamiento";
            $datos = $_GET["codPlan"];

            $respuesta = ModeloPlanPago::mdlBorrarPlanPagos($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    swal({
                        type: "success",
                        title: "El pago ha sido eliminado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if(result.value){
                            window.location = "planPagoTratamiento";
                        }
                    });
                </script>';
            }
        }
    }
/* =============================
     MOSTRAR TIPOS DE PAGO
  ============================== */
  static public function ctrMostrarTipoPago($item, $valor) {
    $tabla = "tipopago";
    $respuesta = ModeloPlanPago::mdlMostrarTipoPago($tabla, $item, $valor);
    return $respuesta;
  }










// static public function ctrPagosPorPaciente($idPaciente){
//     return ModeloPlanPago::mdlPagosPorPaciente($idPaciente);
// }

static public function ctrPagosPorEstadoTratamiento(){
    return ModeloPlanPago::mdlPagosPorEstadoTratamiento();
}

static public function ctrDescuentosAplicados(){
    return ModeloPlanPago::mdlDescuentosAplicados();
}

static public function ctrPagosPorServicio(){
    return ModeloPlanPago::mdlPagosPorServicio();
}

static public function ctrPagosMensuales(){
    return ModeloPlanPago::mdlPagosMensuales();
}
static public function ctrPagosPorOdontologo($desde, $hasta){
    return ModeloPlanPago::mdlPagosPorOdontologo($desde, $hasta);
}

static public function ctrPagosPorTipo($desde, $hasta){
    return ModeloPlanPago::mdlPagosPorTipo($desde, $hasta);
}

    // 1. Pagos totales entre fechas
    static public function ctrPagosTotales($desde, $hasta) {
        return ModeloPlanPago::mdlPagosTotales($desde, $hasta);
    }

    // 2. Saldos por paciente
    static public function ctrSaldosPorPaciente() {
        return ModeloPlanPago::mdlSaldosPorPaciente();
    }

    // 3. Pagos pendientes
    static public function ctrPagosPendientes() {
        return ModeloPlanPago::mdlPagosPendientes();
    }

    // 4. Pagos por día
    static public function ctrPagosPorDia($desde, $hasta) {
        return ModeloPlanPago::mdlPagosPorDia($desde, $hasta);
    }

    // 5. Servicios más solicitados
    static public function ctrServiciosMasSolicitados($desde, $hasta) {
        return ModeloPlanPago::mdlServiciosMasSolicitados($desde, $hasta);
    }
}
?>