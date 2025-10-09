<?php
class ControladorTratamiento2PRUEBA
{
  /*=============================================
mostrar TRATAMIENTO COMPLETO
=============================================*/
  // static public function ctrMostrarTratamientoCompleto($idTratamiento)
  // {
  //   $tratamiento = ModeloTratamiento::mdlObtenerTratamiento("tratamiento", $idTratamiento);
  //   $medicamento = ModeloTratamiento::mdlObtenerMedicamentoPorTratamiento("detalleMedicamento", $idTratamiento);
  //   $odontograma = ModeloTratamiento::mdlObtenerOdontogramaPorTratamiento("odontograma", $idTratamiento);

  //   return [
  //     "tratamiento" => $tratamiento,
  //     "medicamento" => $medicamento,
  //     "odontograma" => $odontograma
  //   ];
  // }

  /*=============================================
crear TRATAMIENTO COMPLETO
=============================================*/
//   static public function ctrCrearTratamientoCompleto()
// {
//     if (!isset($_POST["fechaRegistro"])) return;

//     // Validación simplificada para este ejemplo
//     if (
//         !preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST["fechaRegistro"]) ||
//         !is_numeric($_POST["saldo"]) ||
//         !is_numeric($_POST["totalPago"]) ||
//         !is_numeric($_POST["idPaciente"]) ||
//         !is_numeric($_POST["idUsuarios"]) ||
//         empty($_POST["estado"]) ||
//         empty($_POST["estadoPago"])
//     ) {
//         echo '<script>
//       Swal.fire({
//         icon: "error",
//         title: "Campos inválidos o incompletos",
//         confirmButtonText: "Cerrar"
//       }).then(() => {
//         window.location = "tratamientos";
//       });
//     </script>';
//       return;
//     }

//     // Datos del tratamiento
//     $datosTratamiento = [
//         "fechaRegistro" => $_POST["fechaRegistro"],
//         "saldo"         => $_POST["saldo"],
//         "totalPago"     => $_POST["totalPago"],
//         "estado"        => $_POST["estado"],
//         "estadoPago"    => $_POST["estadoPago"],
//         "idPaciente"    => $_POST["idPaciente"],
//         "idUsuarios"    => $_POST["idUsuarios"]
//     ];

//     // Armar array de medicamentos recorriendo los arrays del formulario
//     $datosMedicamento = [];
//     if (!empty($_POST["codMedicamento"]) && is_array($_POST["codMedicamento"])) {
//         $count = count($_POST["codMedicamento"]);
//         for ($i = 0; $i < $count; $i++) {
//             // Solo agregamos si codMedicamento no está vacío (requerido)
//             if (!empty($_POST["codMedicamento"][$i])) {
//                 $datosMedicamento[] = [
//                     "codMedicamento" => $_POST["codMedicamento"][$i],
//                     "dosis"         => $_POST["dosis"][$i] ?? '',
//                     "fechaInicio"   => $_POST["fechaInicio"][$i] ?? '',
//                     "fechaFinal"    => $_POST["fechaFinal"][$i] ?? '',
//                     "tiempo"        => $_POST["tiempo"][$i] ?? '',
//                     "observacion"   => $_POST["observacion"][$i] ?? ''
//                 ];
//             }
//         }
//     }

//     // Odontograma (puedes dejar igual si quieres)
//     $datosOdontograma = !empty($_POST["odontogramImage"]) ? [
//         "descripcion"   => $_POST["descripcion"] ?? '',
//         "estado"        => $_POST["estadoOdonto"] ?? '',
//         "fechaRegistro" => $_POST["fechaRegistro"],
//         "foto"          => $_POST["odontogramImage"],
//         "posicion"      => $_POST["posicion"] ?? ''
//     ] : [];

//     // Llamar modelo para guardar todo
//     $respuesta = ModeloTratamiento::mdlRegistrarTratamientoCompleto($datosTratamiento, $datosMedicamento, $datosOdontograma);

//      // Verificación clínica
//     if (is_string($respuesta) && str_starts_with($respuesta, "Error")) {
//       echo '<script>
//       Swal.fire({
//         icon: "error",
//         title: "Error al guardar: ' . addslashes($respuesta) . '",
//         confirmButtonText: "Cerrar"
//       }).then(() => {
//         window.location = "tratamientos";
//       });
//     </script>';
//       return;
//     }

//     if (is_numeric($respuesta)) {
//         ModeloTratamiento::mdlEvaluarEstadoPago($respuesta);
//     }

//      // Éxito
//     echo '<script>
//                         swal({
//                             type: "success",
//                             title: "¡El tratamiento ha sido guardado correctamente!",
//                             showConfirmButton: true,
//                             confirmButtonText: "Cerrar"
//                         }).then(function(result){
//                             if(result.value){
//                                 window.location = "tratamiento";
//                             }
//                         });
//                     </script>';
// }


  /*=============================================
EDITAR TRATAMIENTO COMPLETO
=============================================*/
  // static public function ctrEditarTratamientoCompleto()
  // {
  //   if (isset($_POST["idTratamientoEditar"])) {

  //     $datos = array(
  //       "idTratamiento"   => $_POST["idTratamientoEditar"],
  //       "idPaciente"      => $_POST["idPacienteEditar"],
  //       "idUsuarios"      => $_POST["idUsuariosEditar"],
  //       "fechaRegistro"   => $_POST["fechaRegistroEditar"],
  //       "saldo"           => $_POST["saldoEditar"],
  //       "totalPago"       => $_POST["totalPagoEditar"],
  //       "estado"          => $_POST["estadoEditar"],

  //       // Detalle Medicamento
  //       "codMedicamento"  => $_POST["codMedicamentoEditar"],
  //       "dosis"           => $_POST["dosisEditar"],
  //       "fechaInicio"     => $_POST["fechaInicioEditar"],
  //       "fechaFinal"      => $_POST["fechaFinalEditar"],
  //       "tiempo"          => $_POST["tiempoEditar"],
  //       "observacion"     => $_POST["observacionEditar"],

  //       // Odontograma
  //       "posicion"        => $_POST["posicionEditar"] ?? '',
  //       "estadoDiente"    => $_POST["estadoEditar"] ?? '',
  //       "descripcion"     => $_POST["descripcionEditar"] ?? '',
  //       "foto"            => null
  //     );

  //     // Procesar imagen si existe
  //     if (!empty($_FILES["fotoEditar"]["name"])) {
  //       $nombreFoto = $_FILES["fotoEditar"]["name"];
  //       $ruta = "vistas/img/odontograma/" . $nombreFoto;
  //       if (move_uploaded_file($_FILES["fotoEditar"]["tmp_name"], $ruta)) {
  //         $datos["foto"] = $ruta;
  //       }
  //     }

  //     $respuesta = ModeloTratamiento::mdlEditarTratamientoCompleto($datos, $datos, $datos);

  //     if ($respuesta == "ok") {
  //       echo "<script>
  //       Swal.fire({
  //         icon: 'success',
  //         title: 'Tratamiento actualizado correctamente',
  //         confirmButtonText: 'Cerrar'
  //       }).then(() => {
  //         window.location = 'index.php?ruta=tratamientos';
  //       });
  //     </script>";
  //     } else {
  //       echo "<script>
  //       Swal.fire({
  //         icon: 'error',
  //         title: 'Hubo un error al actualizar',
  //         text: '" . $respuesta . "',
  //         confirmButtonText: 'Cerrar'
  //       });
  //     </script>";
  //     }
  //   }
  // }
  // public static function ctrActualizarSaldoTratamiento($idTratamiento, $nuevoSaldo)
  // {
  //   return ModeloTratamiento::mdlActualizarSaldoTratamiento($idTratamiento, $nuevoSaldo);
  // }
  // public static function ctrActualizarEstadoPago($idTratamiento, $estadoPago)
  // {
  //   return ModeloTratamiento::mdlActualizarEstadoPago($idTratamiento, $estadoPago);
  // }
  // /*=============================================
  // MOSTRAR DATOS EN VISTAS
  // =============================================*/
  // static public function ctrMostrarTratamientos()
  // {
  //   return ModeloTratamiento::mdlMostrarTratamientos("tratamiento");
  // }

  // static public function ctrMostrarDetalleMedicamento()
  // {
  //   return ModeloTratamiento::mdlMostrarDetalleMedicamento("detalleMedicamento");
  // }

  // static public function ctrMostrarOdontogramas()
  // {
  //   return ModeloTratamiento::mdlMostrarOdontogramas("odontograma");
  // }
}
