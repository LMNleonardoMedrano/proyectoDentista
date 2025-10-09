<?php

require_once "conexion.php";

class ModeloMedicamento {

    /*=============================================
    MOSTRAR MEDICAMENTOS
    =============================================*/
    static public function mdlMostrarMedicamentos($tabla, $item, $valor) {
        if ($item != null) {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");
            $stmt->execute();
            return $stmt->fetchAll();
        }

        $stmt = null;
    }

    /*=============================================
    REGISTRAR MEDICAMENTO
    =============================================*/
    static public function mdlIngresarMedicamento($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombre, tipo, medida, tiempo) VALUES (:nombre, :tipo, :medida, :tiempo)");

        $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
        $stmt->bindParam(":medida", $datos["medida"], PDO::PARAM_STR);
        $stmt->bindParam(":tiempo", $datos["tiempo"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }

    /*=============================================
    EDITAR MEDICAMENTO
    =============================================*/
    static public function mdlEditarMedicamento($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre = :nombre, tipo = :tipo, medida = :medida, tiempo = :tiempo WHERE codMedicamento = :codMedicamento");

        $stmt->bindParam(":codMedicamento", $datos["codMedicamento"], PDO::PARAM_INT);
        $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
        $stmt->bindParam(":medida", $datos["medida"], PDO::PARAM_STR);
        $stmt->bindParam(":tiempo", $datos["tiempo"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }

    /*=============================================
    BORRAR MEDICAMENTO
    =============================================*/
    static public function mdlBorrarMedicamento($tabla, $id) {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE codMedicamento = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }
}
