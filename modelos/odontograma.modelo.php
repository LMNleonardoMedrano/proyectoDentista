<?php

require_once "conexion.php";

class ModeloOdontograma {

    /*=============================================
    MOSTRAR ODONTOGRAMA
    =============================================*/
   static public function mdlMostrarOdontograma($tabla, $item, $valor) {
    $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :valor");
    $stmt->bindParam(":valor", $valor, PDO::PARAM_INT); // Cambié :$item por :valor
    $stmt->execute();
    return $stmt->fetch();
}

    /*=============================================
    REGISTRAR ODONTOGRAMA
    =============================================*/
    static public function mdlIngresarOdontograma($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(descripcion, estado, fechaRegistro, idTratamiento) 
                                               VALUES (:descripcion, :estado, NOW(), :idTratamiento)");

        $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
        $stmt->bindParam(":idTratamiento", $datos["idTratamiento"], PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }

    /*=============================================
    EDITAR ODONTOGRAMA
    =============================================*/
    static public function mdlEditarOdontograma($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET descripcion = :descripcion, estado = :estado 
                                               WHERE idOdontograma = :idOdontograma");

        $stmt->bindParam(":idOdontograma", $datos["idOdontograma"], PDO::PARAM_INT);
        $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }

    /*=============================================
    BORRAR ODONTOGRAMA
    =============================================*/
    static public function mdlBorrarOdontograma($tabla, $id) {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE idOdontograma = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }
}