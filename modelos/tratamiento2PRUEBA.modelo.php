<?php

require_once "conexion.php";

class ModeloTratamiento2PRUEBA
{

    /*=============================================
  MOSTRAR DATOS
  =============================================*/

    // static public function mdlMostrarTratamientos($tabla)
    // {
    //     $stmt = Conexion::conectar()->prepare("
    //   SELECT t.*, 
    //          p.nombre AS nombrePaciente, 
    //          u.nombre AS nombreUsuario
    //   FROM $tabla t
    //   INNER JOIN pacientes p ON t.idPaciente = p.idPaciente
    //   INNER JOIN usuarios u ON t.idUsuarios = u.idUsuarios
    //   ORDER BY t.idTratamiento DESC
    // ");
    //     $stmt->execute();
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }

    // static public function mdlMostrarDetalleMedicamento($tabla)
    // {
    //     $stmt = Conexion::conectar()->prepare("
    //   SELECT dm.*, 
    //          m.nombre AS nombreMedicamento, 
    //          t.idPaciente, 
    //          t.idUsuarios,
    //          p.nombre AS nombrePaciente,
    //          u.nombre AS nombreUsuario
    //   FROM $tabla dm
    //   INNER JOIN medicamento m ON dm.codMedicamento = m.codMedicamento
    //   INNER JOIN tratamiento t ON dm.idTratamiento = t.idTratamiento
    //   INNER JOIN pacientes p ON t.idPaciente = p.idPaciente
    //   INNER JOIN usuarios u ON t.idUsuarios = u.idUsuarios
    //   ORDER BY dm.idDetalleMedicamento DESC
    // ");
    //     $stmt->execute();
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }

    // static public function mdlMostrarOdontogramas($tabla)
    // {
    //     $stmt = Conexion::conectar()->prepare("
    //   SELECT o.*, 
    //          t.idPaciente, 
    //          t.idUsuarios
    //   FROM $tabla o
    //   INNER JOIN tratamiento t ON o.idTratamiento = t.idTratamiento
    //   ORDER BY o.idOdontograma DESC
    // ");
    //     $stmt->execute();
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }

    /*=============================================
  REGISTRAR TRATAMIENTO COMPLETO CON TRANSACCIÓN
  =============================================*/
// static public function mdlRegistrarTratamientoCompleto($datosTratamiento, $datosMedicamento, $datosOdontograma)
// {
//     try {
//         $db = Conexion::conectar();
//         $db->beginTransaction();

//         $stmt = $db->prepare("
//             INSERT INTO tratamiento (fechaRegistro, saldo, totalPago, estado, idPaciente, idUsuarios)
//             VALUES (:fechaRegistro, :saldo, :totalPago, :estado, :idPaciente, :idUsuarios)
//         ");
//         $stmt->execute([
//             ":fechaRegistro" => $datosTratamiento["fechaRegistro"],
//             ":saldo" => $datosTratamiento["saldo"],
//             ":totalPago" => $datosTratamiento["totalPago"],
//             ":estado" => $datosTratamiento["estado"],
//             ":idPaciente" => $datosTratamiento["idPaciente"],
//             ":idUsuarios" => $datosTratamiento["idUsuarios"]
//         ]);

//         $idTratamiento = $db->lastInsertId();

//         // Insertar todos los medicamentos
//         if (!empty($datosMedicamento)) {
//             $stmtMed = $db->prepare("
//                 INSERT INTO detalleMedicamento (idTratamiento, codMedicamento, dosis, fechaInicio, fechaFinal, observacion, tiempo)
//                 VALUES (:idTratamiento, :codMedicamento, :dosis, :fechaInicio, :fechaFinal, :observacion, :tiempo)
//             ");

//             foreach ($datosMedicamento as $med) {
//                 $stmtMed->execute([
//                     ":idTratamiento" => $idTratamiento,
//                     ":codMedicamento" => $med["codMedicamento"],
//                     ":dosis" => $med["dosis"],
//                     ":fechaInicio" => $med["fechaInicio"],
//                     ":fechaFinal" => $med["fechaFinal"],
//                     ":observacion" => $med["observacion"],
//                     ":tiempo" => $med["tiempo"]
//                 ]);
//             }
//         }

//         // Insertar odontograma si existe (igual que antes)
//         if (!empty($datosOdontograma)) {
//             $stmtOdonto = $db->prepare("
//                 INSERT INTO odontograma (descripcion, estado, fechaRegistro, foto, posicion, idTratamiento)
//                 VALUES (:descripcion, :estado, :fechaRegistro, :foto, :posicion, :idTratamiento)
//             ");
//             $stmtOdonto->execute([
//                 ":descripcion" => $datosOdontograma["descripcion"],
//                 ":estado" => $datosOdontograma["estado"],
//                 ":fechaRegistro" => $datosOdontograma["fechaRegistro"],
//                 ":foto" => $datosOdontograma["foto"],
//                 ":posicion" => $datosOdontograma["posicion"],
//                 ":idTratamiento" => $idTratamiento
//             ]);
//         }

//         $db->commit();
//         return $idTratamiento;
//     } catch (Exception $e) {
//         $db->rollBack();
//         return "Error: " . $e->getMessage();
//     }
// }

    /*=============================================
  editar TRATAMIENTO COMPLETO CON TRANSACCIÓN
  =============================================*/
    // static public function mdlEditarTratamientoCompleto($datosTratamiento, $datosDetalle, $datosOdontograma)
    // {
    //     try {
    //         $pdo = Conexion::conectar();
    //         $pdo->beginTransaction();

    //         // Editar tratamiento
    //         $stmt1 = $pdo->prepare("
    //   UPDATE tratamiento 
    //   SET idPaciente = :idPaciente,
    //       idUsuarios = :idUsuarios,
    //       fechaRegistro = :fechaRegistro,
    //       saldo = :saldo,
    //       totalPago = :totalPago,
    //       estado = :estado
    //   WHERE idTratamiento = :idTratamiento
    // ");
    //         $stmt1->execute($datosTratamiento);

    //         // Editar detalle medicamento
    //         $stmt2 = $pdo->prepare("
    //   UPDATE detallemedicamento 
    //   SET codMedicamento = :codMedicamento,
    //       dosis = :dosis,
    //       fechaInicio = :fechaInicio,
    //       fechaFinal = :fechaFinal,
    //       tiempo = :tiempo,
    //       observacion = :observacion
    //   WHERE idTratamiento = :idTratamiento
    // ");
    //         $stmt2->execute($datosDetalle);

    //         // Editar odontograma
    //         $stmt3 = $pdo->prepare("
    //   UPDATE detalleodontograma 
    //   SET posicion = :posicion,
    //       estado = :estado,
    //       descripcion = :descripcion,
    //       foto = :foto
    //   WHERE idTratamiento = :idTratamiento
    // ");
    //         $stmt3->execute($datosOdontograma);

    //         $pdo->commit();
    //         return true;
    //     } catch (Exception $e) {
    //         $pdo->rollBack();
    //         error_log("Error al editar tratamiento completo: " . $e->getMessage());
    //         return false;
    //     }
    // }


   
  /* =============================================
     OBTENER DATOS DE TRATAMIENTO POR ID
  ============================================= */
  // static public function mdlObtenerTratamiento($tabla, $id) {
  //   $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE idTratamiento = :id");
  //   $stmt->bindParam(":id", $id, PDO::PARAM_INT);
  //   $stmt->execute();

  //   return $stmt->fetch(PDO::FETCH_ASSOC);
  // }

  /* =============================================
     OBTENER DETALLES DE MEDICAMENTO POR TRATAMIENTO
  ============================================= */
  // static public function mdlObtenerMedicamentoPorTratamiento($tabla, $idTratamiento) {
  //   $stmt = Conexion::conectar()->prepare("
  //     SELECT dm.*, m.nombre AS nombreMedicamento
  //     FROM $tabla dm
  //     INNER JOIN medicamento m ON dm.idMedicamento = m.idMedicamento
  //     WHERE dm.idTratamiento = :id
  //   ");
  //   $stmt->bindParam(":id", $idTratamiento, PDO::PARAM_INT);
  //   $stmt->execute();

  //   return $stmt->fetchAll(PDO::FETCH_ASSOC);
  // }

  /* =============================================
     OBTENER DATOS DEL ODONTOGRAMA POR TRATAMIENTO
  ============================================= */
  // static public function mdlObtenerOdontogramaPorTratamiento($tabla, $idTratamiento) {
  //   $stmt = Conexion::conectar()->prepare("
  //     SELECT * FROM $tabla WHERE idTratamiento = :id
  //   ");
  //   $stmt->bindParam(":id", $idTratamiento, PDO::PARAM_INT);
  //   $stmt->execute();

  //   return $stmt->fetchAll(PDO::FETCH_ASSOC);
  // }
  /*=============================================
EVALUAR ESTADO DE PAGO DE TRATAMIENTO
=============================================*/
// static public function mdlEvaluarEstadoPago($idTratamiento) {
//     $pdo = Conexion::conectar();

//     // Consulta actual de saldo y totalPago
//     $stmt = $pdo->prepare("SELECT saldo, totalPago FROM tratamiento WHERE idTratamiento = :id");
//     $stmt->bindParam(":id", $idTratamiento, PDO::PARAM_INT);
//     $stmt->execute();
//     $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

//     if (!$resultado) return false;

//     $saldo = (float) $resultado["saldo"];
//     $total = (float) $resultado["totalPago"];

//     // Evaluación del estado
//     if ($saldo == 0) {
//         $estadoPago = "pagado";
//     } elseif ($saldo < $total) {
//         $estadoPago = "parcial";
//     } else {
//         $estadoPago = "pendiente";
//     }

//     // Actualizar estadoPago en tratamiento
//     $update = $pdo->prepare("UPDATE tratamiento SET estadoPago = :estadoPago WHERE idTratamiento = :id");
//     $update->bindParam(":estadoPago", $estadoPago, PDO::PARAM_STR);
//     $update->bindParam(":id", $idTratamiento, PDO::PARAM_INT);
//     return $update->execute();
// }
// public static function mdlActualizarEstadoPago($idTratamiento, $estadoPago) {
//   $stmt = Conexion::conectar()->prepare("
//     UPDATE tratamiento SET estadoPago = :estado WHERE idTratamiento = :id
//   ");
//   $stmt->bindParam(":estado", $estadoPago, PDO::PARAM_STR);
//   $stmt->bindParam(":id", $idTratamiento, PDO::PARAM_INT);
//   return $stmt->execute();
// }
// public static function mdlActualizarSaldoTratamiento($idTratamiento, $saldo) {
//   $stmt = Conexion::conectar()->prepare("
//     UPDATE tratamiento SET saldo = :saldo WHERE idTratamiento = :id
//   ");
//   $stmt->bindParam(":saldo", $saldo, PDO::PARAM_STR);
//   $stmt->bindParam(":id", $idTratamiento, PDO::PARAM_INT);
//   return $stmt->execute();
// }

}
