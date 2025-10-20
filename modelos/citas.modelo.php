<?php

require_once "conexion.php";

class ModeloCita
{

    /*=============================================
    CREAR CITA
    =============================================*/
    static public function mdlIngresarCita($tabla, $datos)
    {
        try {
            $conn = Conexion::conectar();
            $stmt = $conn->prepare(
                "INSERT INTO $tabla (idPaciente, idUsuarios, fecha, hora, horaFin, motivoConsulta, estado)
                 VALUES (:idPaciente, :idUsuarios, :fecha, :hora, :horaFin, :motivoConsulta, :estado)"
            );

            $stmt->bindParam(":idPaciente", $datos["idPaciente"], PDO::PARAM_INT);
            $stmt->bindParam(":idUsuarios", $datos["idUsuarios"], PDO::PARAM_INT);
            $stmt->bindParam(":fecha", $datos["fecha"], PDO::PARAM_STR);
            $stmt->bindParam(":hora", $datos["hora"], PDO::PARAM_STR);
            $stmt->bindParam(":horaFin", $datos["horaFin"], PDO::PARAM_STR);
            $stmt->bindParam(":motivoConsulta", htmlspecialchars($datos["motivoConsulta"]), PDO::PARAM_STR);
            $stmt->bindParam(":estado", htmlspecialchars($datos["estado"]), PDO::PARAM_STR);
            if ($stmt->execute()) {
                return "ok";
            } else {
                return "error";
            }
        } catch (PDOException $e) {
            error_log("Error en mdlIngresarCita: " . $e->getMessage());
            return "error";
        } finally {
            $stmt = null;
        }
    }
    static public function mdlVerificarCita($tabla, $fecha, $hora)
    {
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) FROM $tabla WHERE fecha = :fecha AND hora = :hora");
        $stmt->bindParam(":fecha", $fecha, PDO::PARAM_STR);
        $stmt->bindParam(":hora", $hora, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn() > 0; // Si existe una cita, retorna true
    }
    /*=============================================
    MOSTRAR CITAS
    =============================================*/
    static public function mdlMostrarCitas($tabla)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "SELECT idCita, fecha, hora, horaFin, motivoConsulta, estado, idPaciente, idUsuarios
             FROM $tabla"
            );

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en mdlMostrarCitas: " . $e->getMessage());
            return [];
        } finally {
            $stmt = null;
        }
    }
/*=============================================
MOSTRAR TODAS LAS CITAS CON NOMBRES Y ID USUARIOS
=============================================*/
static public function mdlMostrarCitasCompletas($tabla)
{
    try {
       $stmt = Conexion::conectar()->prepare(
    "SELECT c.idCita, c.fecha, c.hora, c.horaFin, c.motivoConsulta, c.estado,
            p.nombre AS nombrePaciente,
            u.idUsuarios, 
            CONCAT(u.nombre, ' ', u.apellido) AS nombreOdontologo
     FROM $tabla c
     LEFT JOIN pacientes p ON c.idPaciente = p.idPaciente
     LEFT JOIN usuarios u ON c.idUsuarios = u.idUsuarios
     ORDER BY c.idCita DESC"
);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en mdlMostrarCitasCompletas: " . $e->getMessage());
        return [];
    } finally {
        $stmt = null;
    }
}


    /*=============================================
    EDITAR CITA
    =============================================*/
    static public function mdlEditarCita($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "UPDATE $tabla
                 SET idPaciente = :idPaciente, idUsuarios = :idUsuarios,
                     fecha = :fecha, hora = :hora, horaFin = :horaFin,
                     motivoConsulta = :motivoConsulta, estado = :estado
                 WHERE idCita = :idCita"
            );

            $stmt->bindParam(":idCita", $datos["idCita"], PDO::PARAM_INT);
            $stmt->bindParam(":idPaciente", $datos["idPaciente"], PDO::PARAM_INT);
            $stmt->bindParam(":idUsuarios", $datos["idUsuarios"], PDO::PARAM_INT);
            $stmt->bindParam(":fecha", $datos["fecha"], PDO::PARAM_STR);
            $stmt->bindParam(":hora", $datos["hora"], PDO::PARAM_STR);
            $stmt->bindParam(":horaFin", $datos["horaFin"], PDO::PARAM_STR);
            $stmt->bindParam(":motivoConsulta", htmlspecialchars($datos["motivoConsulta"]), PDO::PARAM_STR);
            $stmt->bindParam(":estado", htmlspecialchars($datos["estado"]), PDO::PARAM_STR);
            if ($stmt->execute()) {
                return "ok";
            } else {
                return "error";
            }
        } catch (PDOException $e) {
            error_log("Error en mdlEditarCita: " . $e->getMessage());
            return "error";
        } finally {
            $stmt = null;
        }
    }
    /*=============================================
    ELIMINAR CITA
    =============================================*/
    static public function mdlEliminarCita($tabla, $id)
    {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE idCita = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
        $stmt = null;
    }
    static public function mdlMostrarCitasPorFecha($tabla, $fecha)
    {
        $stmt = Conexion::conectar()->prepare("
        SELECT c.*, 
               p.nombre AS nombrePaciente, 
               CONCAT(u.nombre, ' ', u.apellido) AS nombreUsuarios
        FROM $tabla c
        JOIN pacientes p ON c.idPaciente = p.idPaciente
        JOIN usuarios u ON c.idUsuarios = u.idUsuarios
        WHERE c.fecha = :fecha
    ");
        $stmt->bindParam(":fecha", $fecha, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    static public function mdlMostrarCitasPorOdontologo($tabla, $idUsuarios)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "SELECT c.*, p.nombre AS nombrePaciente
             FROM $tabla c
             JOIN pacientes p ON c.idPaciente = p.idPaciente
             WHERE c.idUsuarios = :idUsuarios"
            );
            $stmt->bindParam(":idUsuarios", $idUsuarios, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en mdlMostrarCitasPorOdontologo: ' . $e->getMessage());
            return [];
        } finally {
            $stmt = null;
        }
    }
    static public function mdlVerificarCitaOdontologo($tabla, $fecha, $hora, $idUsuarios)
    {
        $stmt = Conexion::conectar()->prepare(
            "SELECT COUNT(*) FROM $tabla WHERE fecha = :fecha AND hora = :hora AND idUsuarios = :idUsuarios"
        );
        $stmt->bindParam(":fecha", $fecha, PDO::PARAM_STR);
        $stmt->bindParam(":hora", $hora, PDO::PARAM_STR);
        $stmt->bindParam(":idUsuarios", $idUsuarios, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
   static public function mdlActualizarEstadoCita($tabla, $datos)
{
    $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET estado = :estado WHERE idCita = :idCita");

    $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
    $stmt->bindParam(":idCita", $datos["idCita"], PDO::PARAM_INT);

    $resultado = $stmt->execute();

    $stmt = null; // cierra la conexión

    return $resultado ? "ok" : "error";
}

static public function mdlMostrarCitasFiltradas($tabla, $estado)
{
    try {
        $stmt = Conexion::conectar()->prepare(
            "SELECT c.idCita, c.fecha, c.hora, c.horaFin, c.motivoConsulta, c.estado,
                    p.nombre AS nombrePaciente,
                    u.idUsuarios,
                    CONCAT(u.nombre, ' ', u.apellido) AS nombreOdontologo
             FROM $tabla c
             LEFT JOIN pacientes p ON c.idPaciente = p.idPaciente
             LEFT JOIN usuarios u ON c.idUsuarios = u.idUsuarios
             WHERE c.estado = :estado"
        );
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en mdlMostrarCitasFiltradas: " . $e->getMessage());
        return [];
    } finally {
        $stmt = null;
    }
}










 // Citas Programadas
    public static function mdlCitasProgramadas($desde = null, $hasta = null){
        $sql = "SELECT c.idCita, c.fecha AS fecha_cita, c.hora, p.nombre AS nombre_paciente, 
                       u.nombre AS nombre_odontologo
                FROM citas c
                JOIN pacientes p ON c.idPaciente = p.idPaciente
                JOIN usuarios u ON c.idUsuarios = u.idUsuarios
                WHERE c.estado = 'programada'";
        if($desde && $hasta){
            $sql .= " AND c.fecha BETWEEN :desde AND :hasta";
        }

        $stmt = Conexion::conectar()->prepare($sql);
        if($desde && $hasta){
            $stmt->bindParam(':desde', $desde);
            $stmt->bindParam(':hasta', $hasta);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Citas Confirmadas
    public static function mdlCitasConfirmadas($desde = null, $hasta = null){
        $sql = $sql = "SELECT 
            c.idCita, 
            c.fecha AS fecha_cita, 
            c.hora, 
            p.nombre AS nombre_paciente, 
            CONCAT(u.nombre, ' ', u.apellido) AS nombre_odontologo
        FROM citas c
        JOIN pacientes p ON c.idPaciente = p.idPaciente
        JOIN usuarios u ON c.idUsuarios = u.idUsuarios
        WHERE c.estado = 'confirmada'";

        if($desde && $hasta){
            $sql .= " AND c.fecha BETWEEN :desde AND :hasta";
        }

        $stmt = Conexion::conectar()->prepare($sql);
        if($desde && $hasta){
            $stmt->bindParam(':desde', $desde);
            $stmt->bindParam(':hasta', $hasta);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Pacientes Atendidos
    public static function mdlPacientesAtendidos($desde = null, $hasta = null){
        $sql = "SELECT u.nombre AS nombre_odontologo, COUNT(*) AS total_atendidos
                FROM citas c
                JOIN usuarios u ON c.idUsuarios = u.idUsuarios
                WHERE c.estado = 'atendida'";
        if($desde && $hasta){
            $sql .= " AND c.fecha BETWEEN :desde AND :hasta";
        }
        $sql .= " GROUP BY u.idUsuarios";

        $stmt = Conexion::conectar()->prepare($sql);
        if($desde && $hasta){
            $stmt->bindParam(':desde', $desde);
            $stmt->bindParam(':hasta', $hasta);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Citas Canceladas
    public static function mdlCitasCanceladas($desde = null, $hasta = null){
        $sql = "SELECT c.idCita, c.fecha AS fecha_cita, c.hora, p.nombre AS nombre_paciente, 
                       c.motivoConsulta AS motivo_cancelacion
                FROM citas c
                JOIN pacientes p ON c.idPaciente = p.idPaciente
                WHERE c.estado = 'cancelada'";
        if($desde && $hasta){
            $sql .= " AND c.fecha BETWEEN :desde AND :hasta";
        }

        $stmt = Conexion::conectar()->prepare($sql);
        if($desde && $hasta){
            $stmt->bindParam(':desde', $desde);
            $stmt->bindParam(':hasta', $hasta);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Citas por Día
    public static function mdlCitasPorDia($desde = null, $hasta = null){
        $sql = "SELECT c.fecha AS fecha_cita, COUNT(*) AS cantidad
                FROM citas c
                WHERE 1";
        if($desde && $hasta){
            $sql .= " AND c.fecha BETWEEN :desde AND :hasta";
        }
        $sql .= " GROUP BY c.fecha ORDER BY c.fecha ASC";

        $stmt = Conexion::conectar()->prepare($sql);
        if($desde && $hasta){
            $stmt->bindParam(':desde', $desde);
            $stmt->bindParam(':hasta', $hasta);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Citas por Odontólogo
    public static function mdlCitasPorOdontologo($desde = null, $hasta = null){
        $sql = "SELECT u.nombre AS nombre_odontologo, COUNT(*) AS cantidad
                FROM citas c
                JOIN usuarios u ON c.idUsuarios = u.idUsuarios
                WHERE 1";
        if($desde && $hasta){
            $sql .= " AND c.fecha BETWEEN :desde AND :hasta";
        }
        $sql .= " GROUP BY u.idUsuarios";

        $stmt = Conexion::conectar()->prepare($sql);
        if($desde && $hasta){
            $stmt->bindParam(':desde', $desde);
            $stmt->bindParam(':hasta', $hasta);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Citas por Servicio
    public static function mdlCitasPorServicio($desde = null, $hasta = null){
        $sql = "SELECT s.nombreServicio AS nombre_servicio, COUNT(*) AS cantidad
                FROM citas c
                JOIN detalletratamientoservicios dts ON dts.idTratamiento = c.idCita
                JOIN servicios s ON s.idServicio = dts.idServicio
                WHERE 1";
        if($desde && $hasta){
            $sql .= " AND c.fecha BETWEEN :desde AND :hasta";
        }
        $sql .= " GROUP BY s.idServicio";

        $stmt = Conexion::conectar()->prepare($sql);
        if($desde && $hasta){
            $stmt->bindParam(':desde', $desde);
            $stmt->bindParam(':hasta', $hasta);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Citas Mensuales
    public static function mdlCitasMensuales(){
        $sql = "SELECT MONTH(c.fecha) AS mes, YEAR(c.fecha) AS anio, COUNT(*) AS cantidad
                FROM citas c
                GROUP BY YEAR(c.fecha), MONTH(c.fecha)
                ORDER BY YEAR(c.fecha), MONTH(c.fecha)";

        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public static function mdlMostrarCitasPorFechas($tabla, $fecha)
{
    $stmt = Conexion::conectar()->prepare("
        SELECT 
            c.idCita, 
            p.nombre AS paciente, 
            c.idUsuarios, 
            c.motivoConsulta, 
            c.fecha, 
            c.hora, 
            c.horaFin, 
            c.estado,
            COALESCE(TRIM(CONCAT(u.nombre, ' ', u.apellido)), 'Desconocido') AS odontologo
        FROM $tabla c
        LEFT JOIN pacientes p ON c.idPaciente = p.idPaciente
        LEFT JOIN usuarios u ON c.idUsuarios = u.idUsuarios
        WHERE c.fecha = :fecha
        ORDER BY c.hora ASC
    ");

    $stmt->bindParam(":fecha", $fecha, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public static function mdlMostrarCitasPorFechaYUsuario($tabla, $filtro)
{
    $stmt = Conexion::conectar()->prepare("
        SELECT 
            c.idCita, 
            p.nombre AS paciente, 
            c.idUsuarios, 
            c.motivoConsulta, 
            c.fecha, 
            c.hora, 
            c.horaFin, 
            c.estado,
            COALESCE(TRIM(CONCAT(u.nombre, ' ', u.apellido)), 'Desconocido') AS odontologo
        FROM $tabla c
        LEFT JOIN pacientes p ON c.idPaciente = p.idPaciente
        LEFT JOIN usuarios u ON c.idUsuarios = u.idUsuarios
        WHERE c.fecha = :fecha 
          AND c.idUsuarios = :idUsuarios
        ORDER BY c.hora ASC
    ");

    $stmt->bindParam(":fecha", $filtro['fecha'], PDO::PARAM_STR);
    $stmt->bindParam(":idUsuarios", $filtro['idUsuarios'], PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}




}
