<?php

require_once "conexion.php";

class ModeloRoles {

    /*=============================================
    MOSTRAR ROLES
    =============================================*/
    static public function mdlMostrarRoles($tabla, $item, $valor) {
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
    MOSTRAR PERMISOS
    =============================================*/
    static public function mdlMostrarPermisos($tabla) {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");
        $stmt->execute();
        return $stmt->fetchAll();
    }
// Mostrar permisos de un rol
    static public function mdlMostrarPermisosPorRol($idRol) {
        $stmt = Conexion::conectar()->prepare("
            SELECT p.idPermiso, p.nombrePermiso, p.modulo,
                   CASE WHEN rp.idRol IS NULL THEN 0 ELSE 1 END AS tieneAcceso
            FROM permisos p
            LEFT JOIN rolespermisos rp ON p.idPermiso = rp.idPermiso AND rp.idRol = :idRol
            ORDER BY p.modulo, p.idPermiso
        ");
        $stmt->bindParam(":idRol", $idRol, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Mostrar permisos por módulo
    static public function mdlMostrarPermisosPorModulo($modulo) {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM permisos WHERE modulo = :modulo");
        $stmt->bindParam(":modulo", $modulo, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     // Asignar permiso
    static public function mdlAsignarPermiso($idRol, $idPermiso) {
        $stmt = Conexion::conectar()->prepare("INSERT IGNORE INTO rolespermisos (idRol, idPermiso) VALUES (:idRol, :idPermiso)");
        $stmt->bindParam(":idRol", $idRol, PDO::PARAM_INT);
        $stmt->bindParam(":idPermiso", $idPermiso, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Quitar permiso
    static public function mdlQuitarPermiso($idRol, $idPermiso) {
        $stmt = Conexion::conectar()->prepare("DELETE FROM rolespermisos WHERE idRol = :idRol AND idPermiso = :idPermiso");
        $stmt->bindParam(":idRol", $idRol, PDO::PARAM_INT);
        $stmt->bindParam(":idPermiso", $idPermiso, PDO::PARAM_INT);
        $stmt->execute();
    }
static public function mdlMostrarFormulariosPorModulo($idRol, $modulo) {
    $stmt = Conexion::conectar()->prepare("
        SELECT p.idPermiso, p.nombrePermiso,
        IF(rp.idRol IS NOT NULL, 1, 0) AS tieneAcceso
        FROM permisos p
        LEFT JOIN rolespermisos rp ON p.idPermiso = rp.idPermiso AND rp.idRol = :idRol
        WHERE p.modulo = :modulo
    ");
    $stmt->bindParam(":idRol", $idRol, PDO::PARAM_INT);
    $stmt->bindParam(":modulo", $modulo, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll();
}



    
    /*=============================================
    MOSTRAR PERMISOS DE UN ROL
    =============================================*/
    static public function mdlMostrarPermisosRol($idRol) {
        $stmt = Conexion::conectar()->prepare("
            SELECT idPermiso FROM rolespermisos WHERE idRol = :idRol
        ");
        $stmt->bindParam(":idRol", $idRol, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /*=============================================
    ASIGNAR PERMISOS A UN ROL
    =============================================*/
    static public function mdlAsignarPermisos($idRol, $permisos) {
        $conexion = Conexion::conectar();

        // Eliminar permisos anteriores
        $conexion->prepare("DELETE FROM rolespermisos WHERE idRol = :idRol")
                 ->execute([":idRol" => $idRol]);

        // Insertar nuevos permisos
        $stmt = $conexion->prepare("INSERT INTO rolespermisos (idRol, idPermiso) VALUES (:idRol, :idPermiso)");

        foreach ($permisos as $idPermiso) {
            $stmt->execute([
                ":idRol" => $idRol,
                ":idPermiso" => $idPermiso
            ]);
        }

        return "ok";
    }
    /*=============================================
    REGISTRAR ROL
    =============================================*/
    static public function mdlIngresarRoles($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombreRol) VALUES (:nombreRol)");

        $stmt->bindParam(":nombreRol", $datos["nombreRol"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }

    /*=============================================
    EDITAR ROLES
    =============================================*/
    static public function mdlEditarRoles($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombreRol = :nombreRol WHERE idRol = :idRol");

        $stmt->bindParam(":idRol", $datos["idRol"], PDO::PARAM_INT);
        $stmt->bindParam(":nombreRol", $datos["nombreRol"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }

    /*=============================================
    BORRAR ROL
    =============================================*/
    static public function mdlBorrarRoles($tabla, $id) {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE idRol = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }
}
