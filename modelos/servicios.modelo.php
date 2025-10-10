<?php

require_once "conexion.php";

class ModeloServicios {

    /*=============================================
    MOSTRAR SERVICIOS
    =============================================*/
    static public function mdlMostrarServicios($tabla, $item, $valor) {
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
    REGISTRAR SERVICIO
    =============================================*/
    static public function mdlIngresarServicio($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombreServicio, descripcion, precio) VALUES (:nombreServicio, :descripcion, :precio)");

        $stmt->bindParam(":nombreServicio", $datos["nombreServicio"], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
        $stmt->bindParam(":precio", $datos["precio"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }

    /*=============================================
    EDITAR SERVICIO
    =============================================*/
    static public function mdlEditarServicio($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombreServicio = :nombreServicio, descripcion = :descripcion, precio = :precio WHERE idServicio = :idServicio");

        $stmt->bindParam(":idServicio", $datos["idServicio"], PDO::PARAM_INT);
        $stmt->bindParam(":nombreServicio", $datos["nombreServicio"], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
        $stmt->bindParam(":precio", $datos["precio"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }

    /*=============================================
    BORRAR SERVICIO
    =============================================*/
    static public function mdlBorrarServicio($tabla, $id) {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE idServicio = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }

}
