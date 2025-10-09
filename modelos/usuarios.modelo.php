<?php

require_once "conexion.php";

class ModeloUsuarios
{

   /*=============================================
MOSTRAR USUARIOS
=============================================*/
static public function mdlMostrarUsuarios($tabla, $item = null, $valor = null)
{
    try {
        if ($item != null) {
            // Traer un solo usuario por un campo específico
            $stmt = Conexion::conectar()->prepare(
                "SELECT u.*, r.nombreRol 
                 FROM $tabla u
                 INNER JOIN roles r ON u.idRol = r.idRol
                 WHERE u.$item = :$item"
            );
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC); // Retorna un solo registro
        } else {
            // Traer todos los usuarios
            $stmt = Conexion::conectar()->prepare(
                "SELECT u.*, r.nombreRol 
                 FROM $tabla u
                 INNER JOIN roles r ON u.idRol = r.idRol"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retorna todos los registros
        }
    } catch (PDOException $e) {
        error_log("Error en mdlMostrarUsuarios: " . $e->getMessage());
        return [];
    } finally {
        $stmt = null;
    }
}



    /*=============================================
	REGISTRO DE USUARIO
=============================================*/

    static public function mdlIngresarUsuario($tabla, $datos)
    {

        // Preparar la consulta SQL para insertar los datos
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombre, apellido, ci, correo, domicilio, password, idRol, foto, usuario, estado, fecha) 
                                            VALUES (:nombre, :apellido, :ci, :correo, :domicilio, :password, :idRol, :foto, :usuario, :estado, CURDATE())");

        // Asignar los valores de los parámetros
        $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":apellido", $datos["apellido"], PDO::PARAM_STR);
        $stmt->bindParam(":ci", $datos["ci"], PDO::PARAM_STR);
        $stmt->bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
        $stmt->bindParam(":domicilio", $datos["domicilio"], PDO::PARAM_STR);
        $stmt->bindParam(":password", $datos["password"], PDO::PARAM_STR);
        $stmt->bindParam(":idRol", $datos["idRol"], PDO::PARAM_INT);
        $stmt->bindParam(":foto", $datos["foto"], PDO::PARAM_STR);
        $stmt->bindParam(":usuario", $datos["usuario"], PDO::PARAM_STR);

        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR); // Asignar valor de estado

        // Ejecutar la consulta y verificar si fue exitosa
        if ($stmt->execute()) {

            return "ok";  // Si la inserción es exitosa, retorna "ok"

        } else {

            return "error";  // Si ocurre un error, retorna "error"

        }

        // Cerrar la sentencia y la conexión
        $stmt->close();
        $stmt = null;
    }


 /*=============================================
    ACTUALIZAR USUARIO
=============================================*/

static public function mdlEditarUsuario($tabla, $datos)
{
    // Preparar la consulta SQL para actualizar los datos
    $stmt = Conexion::conectar()->prepare("UPDATE $tabla 
                                            SET nombre = :nombre,
                                                apellido = :apellido, 
                                                ci = :ci, 
                                                correo = :correo, 
                                                domicilio = :domicilio, 
                                                password = :password, 
                                                idRol = :idRol, 
                                                foto = :foto, 
                                                estado = :estado, 
                                                ultimoLogin = :ultimoLogin
                                            WHERE idUsuarios = :idUsuarios");

    // Asignar los parámetros a la consulta SQL
    $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
    $stmt->bindParam(":apellido", $datos["apellido"], PDO::PARAM_STR);
    $stmt->bindParam(":ci", $datos["ci"], PDO::PARAM_STR);
    $stmt->bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
    $stmt->bindParam(":domicilio", $datos["domicilio"], PDO::PARAM_STR);
    $stmt->bindParam(":password", $datos["password"], PDO::PARAM_STR);
    $stmt->bindParam(":idRol", $datos["idRol"], PDO::PARAM_INT);
    $stmt->bindParam(":foto", $datos["foto"], PDO::PARAM_STR);
    $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
    $stmt->bindParam(":ultimoLogin", $datos["ultimoLogin"], PDO::PARAM_STR);
    $stmt->bindParam(":idUsuarios", $datos["idUsuarios"], PDO::PARAM_INT);

    // Ejecutar la consulta y verificar si fue exitosa
    if ($stmt->execute()) {
        return "ok";  // Si la actualización fue exitosa, retorna "ok"
    } else {
        $errorInfo = $stmt->errorInfo();  // Obtener detalles del error
        echo "Error: " . $errorInfo[2];  // Mostrar el mensaje de error
        return "error";  // Si ocurre un error, retorna "error"
    }

    // Cerrar la conexión
    $stmt = null;
}



    /*=============================================
	ACTUALIZAR USUARIO
	=============================================*/

    static public function mdlActualizarUsuario($tabla, $item1, $valor1, $item2, $valor2)
    {

        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item1 = :$item1 WHERE $item2 = :$item2");

        $stmt->bindParam(":" . $item1, $valor1, PDO::PARAM_STR);
        $stmt->bindParam(":" . $item2, $valor2, PDO::PARAM_STR);

        if ($stmt->execute()) {

            return "ok";
        } else {

            return "error";
        }

        $stmt->close();

        $stmt = null;
    }

    /*=============================================
	BORRAR USUARIO
	=============================================*/

    /*=============================================
	ELIMINAR USUARIO
=============================================*/

    static public function mdlEliminarUsuario($tabla, $datos)
    {

        // Prepara la consulta SQL para eliminar el usuario
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE idUsuarios = :id");

        // Vincula el parámetro :id con el valor pasado en $datos
        $stmt->bindParam(":id", $datos, PDO::PARAM_INT);

        // Ejecuta la consulta
        if ($stmt->execute()) {

            return "ok";  // Si la eliminación fue exitosa, retorna "ok"

        } else {

            return "error";  // Si ocurre un error, retorna "error"
        }

        // Cierra la conexión
        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    ACTUALIZAR CONTRASEÑA DEL USUARIO
    =============================================*/
    static public function mdlActualizarPassword($tabla, $datos)
    {

        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET password = :password WHERE usuario = :usuario");

        $stmt->bindParam(":password", $datos["password"], PDO::PARAM_STR);
        $stmt->bindParam(":usuario", $datos["usuario"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success' role='alert'>Actualización exitosa</div>";
            return "ok";
        } else {
            echo "<div class='alert alert-danger' role='alert'>Error en la ejecución de la actualización</div>";
            return "error";
        }


        $stmt->close();
        $stmt = null;
    }
static public function mdlMostrarSoloOdontologos($tabla) {
    try {
        $stmt = Conexion::conectar()->prepare(
            "SELECT u.idUsuarios, u.nombre, u.apellido, u.usuario, u.foto, r.nombreRol 
             FROM $tabla u
             INNER JOIN roles r ON u.idRol = r.idRol
             WHERE r.nombreRol = 'Odontólogo'"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error en mdlMostrarSoloOdontologos: ' . $e->getMessage());
        return [];
    } finally {
        $stmt = null;
    }
}












// Mostrar datos de un usuario por ID
    static public function mdlMostrarPerfil($idUsuario) {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM usuarios WHERE idUsuarios = :id");
        $stmt->bindParam(":id", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

/*=============================================
EDITAR PERFIL
=============================================*/
static public function mdlEditarPerfil($tabla, $datos) {
    $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre=:n, apellido=:a, correo=:c, domicilio=:d, foto=:f WHERE idUsuarios=:id");
    $stmt->bindParam(":n", $datos["nombre"], PDO::PARAM_STR);
    $stmt->bindParam(":a", $datos["apellido"], PDO::PARAM_STR);
    $stmt->bindParam(":c", $datos["correo"], PDO::PARAM_STR);
    $stmt->bindParam(":d", $datos["domicilio"], PDO::PARAM_STR);
    $stmt->bindParam(":f", $datos["foto"], PDO::PARAM_STR);
    $stmt->bindParam(":id", $datos["idUsuarios"], PDO::PARAM_INT);

    return $stmt->execute() ? "ok" : "error";
}

/*=============================================
OBTENER USUARIO POR ID
=============================================*/
static public function mdlMostrarUsuarioPorId($tabla, $id) {
    $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE idUsuarios = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/*=============================================
CAMBIAR CONTRASEÑA
=============================================*/
static public function mdlCambiarPassword($tabla, $id, $hash) {
    $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET password=:p WHERE idUsuarios=:id");
    $stmt->bindParam(":p", $hash, PDO::PARAM_STR);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    return $stmt->execute() ? "ok" : "error";
}

}
