<?php
require_once "conexion.php";

class ModeloPaciente {

 /*=============================================
  Mostrar Paciente(s)
=============================================*/
static public function mdlMostrarPaciente($tabla, $item, $valor) {
  $db = Conexion::conectar();

  if ($item != null) {
    $stmt = $db->prepare("SELECT * FROM $tabla WHERE $item = :$item");
    $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); // ✅ Solo un registro
  } else {
    $stmt = $db->prepare("SELECT * FROM $tabla ORDER BY idPaciente DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // ✅ Lista completa
  }

  $stmt = null;
}
public static function mdlMostrarPacientesSinCitas() {
        $pdo = Conexion::conectar();

        $stmt = $pdo->prepare("
            SELECT p.*
            FROM pacientes p
            LEFT JOIN citas c ON p.idPaciente = c.idPaciente
            WHERE c.idCita IS NULL
        ");
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
  /*=============================================
  Mostrar Tutor por paciente (si existe)
  =============================================*/
  static public function mdlMostrarTutorPorPaciente($tabla, $idPaciente) {
    $db = Conexion::conectar();
    $stmt = $db->prepare("SELECT * FROM $tabla WHERE idPaciente = :idPaciente LIMIT 1");
    $stmt->bindParam(":idPaciente", $idPaciente, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  /*=============================================
  Crear Paciente y Tutor con transacción
  =============================================*/
  static public function mdlCrearPacienteCompleto($tablaPaciente, $tablaTutor, $datosPaciente, $datosTutor) {
    $db = Conexion::conectar();

    try {
      $db->beginTransaction();

      // Insert paciente
      $stmtPaciente = $db->prepare("INSERT INTO $tablaPaciente (ci, domicilio, fechaNac, nombre, genero, telCel, fechaRegistro) VALUES (:ci, :domicilio, :fechaNac, :nombre, :genero, :telCel, :fechaRegistro)");

      $stmtPaciente->bindParam(":ci", $datosPaciente["ci"], PDO::PARAM_STR);
      $stmtPaciente->bindParam(":domicilio", $datosPaciente["domicilio"], PDO::PARAM_STR);
      $stmtPaciente->bindParam(":fechaNac", $datosPaciente["fechaNac"], PDO::PARAM_STR);
      $stmtPaciente->bindParam(":nombre", $datosPaciente["nombre"], PDO::PARAM_STR);
      $stmtPaciente->bindParam(":genero", $datosPaciente["genero"], PDO::PARAM_STR);
      $stmtPaciente->bindParam(":telCel", $datosPaciente["telCel"], PDO::PARAM_STR);
      $stmtPaciente->bindParam(":fechaRegistro", $datosPaciente["fechaRegistro"], PDO::PARAM_STR);

      if (!$stmtPaciente->execute()) {
        throw new Exception("Error al insertar paciente.");
      }

      $idPaciente = $db->lastInsertId();

      // Si hay datos de tutor, insertar
      if (!empty($datosTutor)) {
        $stmtTutor = $db->prepare("INSERT INTO $tablaTutor (idPaciente, nombrePT, generoPT, ocupacion, relacion) VALUES (:idPaciente, :nombrePT, :generoPT, :ocupacion, :relacion)");
        $stmtTutor->bindParam(":idPaciente", $idPaciente, PDO::PARAM_INT);
        $stmtTutor->bindParam(":nombrePT", $datosTutor["nombrePT"], PDO::PARAM_STR);
        $stmtTutor->bindParam(":generoPT", $datosTutor["generoPT"], PDO::PARAM_STR);
        $stmtTutor->bindParam(":ocupacion", $datosTutor["ocupacion"], PDO::PARAM_STR);
        $stmtTutor->bindParam(":relacion", $datosTutor["relacion"], PDO::PARAM_STR);

        if (!$stmtTutor->execute()) {
          throw new Exception("Error al insertar tutor.");
        }
      }

      $db->commit();
      return "ok";
    } catch (Exception $e) {
      $db->rollBack();
      return $e->getMessage();
    }
  }

  /*=============================================
  Editar Paciente y Tutor con transacción
  =============================================*/
  static public function mdlEditarPacienteCompleto($tablaPaciente, $tablaTutor, $datosPaciente, $datosTutor, $esMenor) {
    $db = Conexion::conectar();

    try {
      $db->beginTransaction();

      // Actualizar paciente
      $stmtPaciente = $db->prepare("UPDATE $tablaPaciente SET ci = :ci, domicilio = :domicilio, fechaNac = :fechaNac, nombre = :nombre, genero = :genero, telCel = :telCel, fechaRegistro = :fechaRegistro WHERE idPaciente = :idPaciente");

      $stmtPaciente->bindParam(":ci", $datosPaciente["ci"], PDO::PARAM_STR);
      $stmtPaciente->bindParam(":domicilio", $datosPaciente["domicilio"], PDO::PARAM_STR);
      $stmtPaciente->bindParam(":fechaNac", $datosPaciente["fechaNac"], PDO::PARAM_STR);
      $stmtPaciente->bindParam(":nombre", $datosPaciente["nombre"], PDO::PARAM_STR);
      $stmtPaciente->bindParam(":genero", $datosPaciente["genero"], PDO::PARAM_STR);
      $stmtPaciente->bindParam(":telCel", $datosPaciente["telCel"], PDO::PARAM_STR);
      $stmtPaciente->bindParam(":idPaciente", $datosPaciente["idPaciente"], PDO::PARAM_INT);
      $stmtPaciente->bindParam(":fechaRegistro", $datosPaciente["fechaRegistro"], PDO::PARAM_STR);

      if (!$stmtPaciente->execute()) {
        throw new Exception("Error al actualizar paciente.");
      }

      // Si es menor, actualizar o insertar tutor
      if ($esMenor) {
        // Verificar si tutor existe
        $stmtCheckTutor = $db->prepare("SELECT COUNT(*) FROM $tablaTutor WHERE idPaciente = :idPaciente");
        $stmtCheckTutor->bindParam(":idPaciente", $datosPaciente["idPaciente"], PDO::PARAM_INT);
        $stmtCheckTutor->execute();
        $existeTutor = $stmtCheckTutor->fetchColumn() > 0;

        if ($existeTutor) {
          $stmtTutor = $db->prepare("UPDATE $tablaTutor SET nombrePT = :nombrePT, generoPT = :generoPT, ocupacion = :ocupacion, relacion = :relacion WHERE idPaciente = :idPaciente");
        } else {
          $stmtTutor = $db->prepare("INSERT INTO $tablaTutor (idPaciente, nombrePT, generoPT, ocupacion, relacion) VALUES (:idPaciente, :nombrePT, :generoPT, :ocupacion, :relacion)");
        }

        $stmtTutor->bindParam(":idPaciente", $datosPaciente["idPaciente"], PDO::PARAM_INT);
        $stmtTutor->bindParam(":nombrePT", $datosTutor["nombrePT"], PDO::PARAM_STR);
        $stmtTutor->bindParam(":generoPT", $datosTutor["generoPT"], PDO::PARAM_STR);
        $stmtTutor->bindParam(":ocupacion", $datosTutor["ocupacion"], PDO::PARAM_STR);
        $stmtTutor->bindParam(":relacion", $datosTutor["relacion"], PDO::PARAM_STR);

        if (!$stmtTutor->execute()) {
          throw new Exception("Error al actualizar o insertar tutor.");
        }
      } else {
        // Si no es menor, borrar tutor si existía
        $stmtBorrarTutor = $db->prepare("DELETE FROM $tablaTutor WHERE idPaciente = :idPaciente");
        $stmtBorrarTutor->bindParam(":idPaciente", $datosPaciente["idPaciente"], PDO::PARAM_INT);
        $stmtBorrarTutor->execute();
      }

      $db->commit();
      return "ok";
    } catch (Exception $e) {
      $db->rollBack();
      return $e->getMessage();
    }
  }

  /*=============================================
  Eliminar Paciente y Tutor con transacción
  =============================================*/
static public function mdlEliminarPacienteCompleto($tablaPaciente, $tablaTutor, $idPaciente) {
    $db = Conexion::conectar();

    try {
        $db->beginTransaction();

        // Borrar tutor si existe
        $stmtTutor = $db->prepare("DELETE FROM $tablaTutor WHERE idPaciente = :idPaciente");
        $stmtTutor->bindParam(":idPaciente", $idPaciente, PDO::PARAM_INT);
        $stmtTutor->execute();

        // Borrar paciente
        $stmtPaciente = $db->prepare("DELETE FROM $tablaPaciente WHERE idPaciente = :idPaciente");
        $stmtPaciente->bindParam(":idPaciente", $idPaciente, PDO::PARAM_INT);
        $stmtPaciente->execute();

        $db->commit();
        return "ok";

    } catch (PDOException $e) {
        $db->rollBack();

        // Detectar error FK 1451 (restricción de integridad)
        if ($e->getCode() === '23000') {
            // Puedes devolver un mensaje o un código para controlarlo en el controlador
            return "error_tratamiento";
        }

        // Para otros errores
        return "error_general";
    }
}


}
