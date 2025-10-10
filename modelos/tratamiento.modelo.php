<?php

require_once "conexion.php";

class ModeloTratamiento
{

   /*=============================================
MOSTRAR TRATAMIENTOS
=============================================*/
static public function mdlMostrarTratamientos($tabla, $item, $valor)
{
    $conexion = Conexion::conectar();

    if ($item != null) {
        $stmt = $conexion->prepare(
            "SELECT t.*, 
                    p.nombre AS nombrePaciente, 
                    p.ci AS ciPaciente, 
                    u.nombre AS nombreUsuario, 
                    u.apellido AS apellidoUsuario
             FROM $tabla t
             INNER JOIN pacientes p ON t.idPaciente = p.idPaciente
             INNER JOIN usuarios u ON t.idUsuarios = u.idUsuarios
             WHERE t.$item = :$item"
        );
        $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    } else {
        $stmt = $conexion->prepare(
            "SELECT t.*, 
                    p.nombre AS nombrePaciente, 
                    p.ci AS ciPaciente, 
                    u.nombre AS nombreUsuario, 
                    u.apellido AS apellidoUsuario
             FROM $tabla t
             INNER JOIN pacientes p ON t.idPaciente = p.idPaciente
             INNER JOIN usuarios u ON t.idUsuarios = u.idUsuarios"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    $stmt = null;
}



    /*=============================================
    REGISTRAR TRATAMIENTO
    =============================================*/
    static public function mdlIngresarTratamiento($tabla, $datos)
    {
        $conexion = Conexion::conectar();

        $stmt = $conexion->prepare(
            "INSERT INTO $tabla(fechaRegistro, saldo, totalPago, estado, estadoPago, idPaciente, idUsuarios)
             VALUES (:fechaRegistro, :saldo, :totalPago, :estado, :estadoPago, :idPaciente, :idUsuarios)"
        );

        $stmt->bindParam(":fechaRegistro", $datos["fechaRegistro"], PDO::PARAM_STR);
        $stmt->bindParam(":saldo", $datos["saldo"], PDO::PARAM_STR);
        $stmt->bindParam(":totalPago", $datos["totalPago"], PDO::PARAM_STR);
        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
        $stmt->bindParam(":estadoPago", $datos["estadoPago"], PDO::PARAM_STR);
        $stmt->bindParam(":idPaciente", $datos["idPaciente"], PDO::PARAM_INT);
        $stmt->bindParam(":idUsuarios", $datos["idUsuarios"], PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $conexion->lastInsertId();
        } else {
            print_r($stmt->errorInfo());
            return false;
        }
        $stmt = null;
    }

    /*=============================================
    GUARDAR DETALLE DE SERVICIOS (ACUMULANDO)
    =============================================*/
    static public function mdlIngresarDetalleServicio($tabla, $datos)
    {
        $conexion = Conexion::conectar();

        $stmt = $conexion->prepare(
            "INSERT INTO $tabla(idTratamiento, idServicio, cantidad, precio) 
             VALUES (:idTratamiento, :idServicio, :cantidad, :precio)"
        );

        $stmt->bindParam(':idTratamiento', $datos['idTratamiento'], PDO::PARAM_INT);
        $stmt->bindParam(':idServicio', $datos['idServicio'], PDO::PARAM_INT);
        $stmt->bindParam(':cantidad', $datos['cantidad'], PDO::PARAM_INT);
        $stmt->bindParam(':precio', $datos['precio'], PDO::PARAM_STR);

        if (!$stmt->execute()) {
            print_r($stmt->errorInfo());
            return false;
        }

        // Recalcular totales exactos desde la tabla de detalles
        self::mdlActualizarTotalesDesdeDetalle($datos['idTratamiento']);
        return true;

        return true;
    }

    /*=============================================
    GUARDAR DETALLE DE MEDICAMENTOS (ACUMULANDO)
    =============================================*/
    static public function mdlIngresarDetalleMedicamento($tabla, $datos)
    {
        $conexion = Conexion::conectar();

        $stmt = $conexion->prepare(
            "INSERT INTO $tabla (idTratamiento, codMedicamento, dosis, fechaFinal, fechaInicio, observacion, tiempo) 
             VALUES (:idTratamiento, :codMedicamento, :dosis, :fechaFinal, :fechaInicio, :observacion, :tiempo)"
        );

        $stmt->bindParam(':idTratamiento', $datos['idTratamiento'], PDO::PARAM_INT);
        $stmt->bindParam(':codMedicamento', $datos['codMedicamento'], PDO::PARAM_INT);
        $stmt->bindParam(':dosis', $datos['dosis'], PDO::PARAM_STR);
        $stmt->bindParam(':fechaFinal', $datos['fechaFinal'], PDO::PARAM_STR);
        $stmt->bindParam(':fechaInicio', $datos['fechaInicio'], PDO::PARAM_STR);
        $stmt->bindParam(':observacion', $datos['observacion'], PDO::PARAM_STR);
        $stmt->bindParam(':tiempo', $datos['tiempo'], PDO::PARAM_STR);

        if (!$stmt->execute()) {
            print_r($stmt->errorInfo());
            return false;
        }

        // Solo recalcular totales desde detalle de servicios (medicamentos no suman al totalPago)
self::mdlActualizarTotalesDesdeDetalle($datos['idTratamiento']);


        return true;
    }

    /*=============================================
    ACTUALIZAR TOTALES ACUMULANDO
    =============================================*/
    static public function mdlActualizarTotalesAcumulando($idTratamiento, $totalNuevo)
    {
        $conexion = Conexion::conectar();

        $stmt = $conexion->prepare("SELECT totalPago, saldo FROM tratamiento WHERE idTratamiento = :idTratamiento");
        $stmt->bindParam(":idTratamiento", $idTratamiento, PDO::PARAM_INT);
        $stmt->execute();
        $totalesActuales = $stmt->fetch(PDO::FETCH_ASSOC);

        $totalPagoActual = $totalesActuales['totalPago'] ?? 0;
        $saldoActual = $totalesActuales['saldo'] ?? 0;

        $totalPagoAcumulado = $totalPagoActual + $totalNuevo;
        $saldoAcumulado = $saldoActual + $totalNuevo;

        $stmtUpdate = $conexion->prepare(
            "UPDATE tratamiento SET totalPago = :totalPago, saldo = :saldo WHERE idTratamiento = :idTratamiento"
        );
        $stmtUpdate->bindParam(":totalPago", $totalPagoAcumulado, PDO::PARAM_STR);
        $stmtUpdate->bindParam(":saldo", $saldoAcumulado, PDO::PARAM_STR);
        $stmtUpdate->bindParam(":idTratamiento", $idTratamiento, PDO::PARAM_INT);

        return $stmtUpdate->execute();
    }

    /*=============================================
    EDITAR TRATAMIENTO
    =============================================*/
    static public function mdlEditarTratamiento($tabla, $datos)
    {
        $stmt = Conexion::conectar()->prepare(
            "UPDATE $tabla 
             SET fechaRegistro = :fechaRegistro, saldo = :saldo, totalPago = :totalPago, estado = :estado, estadoPago = :estadoPago, idPaciente = :idPaciente, idUsuarios = :idUsuarios
             WHERE idTratamiento = :idTratamiento"
        );

        $stmt->bindParam(":idTratamiento", $datos["idTratamiento"], PDO::PARAM_INT);
        $stmt->bindParam(":fechaRegistro", $datos["fechaRegistro"], PDO::PARAM_STR);
        $stmt->bindParam(":saldo", $datos["saldo"], PDO::PARAM_STR);
        $stmt->bindParam(":totalPago", $datos["totalPago"], PDO::PARAM_STR);
        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
        $stmt->bindParam(":estadoPago", $datos["estadoPago"], PDO::PARAM_STR);
        $stmt->bindParam(":idPaciente", $datos["idPaciente"], PDO::PARAM_INT);
        $stmt->bindParam(":idUsuarios", $datos["idUsuarios"], PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
        $stmt = null;
    }

    /*=============================================
BORRAR TRATAMIENTO (con eliminación de detalles)
=============================================*/
    static public function mdlBorrarTratamiento($tabla, $id)
    {
        $conexion = Conexion::conectar();

        try {
            // Iniciar transacción
            $conexion->beginTransaction();

            // 1. Eliminar servicios asociados
            $stmtServicios = $conexion->prepare(
                "DELETE FROM detalleTratamientoServicios WHERE idTratamiento = :idTratamiento"
            );
            $stmtServicios->bindParam(":idTratamiento", $id, PDO::PARAM_INT);
            $stmtServicios->execute();

            // 2. Eliminar medicamentos asociados
            $stmtMedicamentos = $conexion->prepare(
                "DELETE FROM detallemedicamento WHERE idTratamiento = :idTratamiento"
            );
            $stmtMedicamentos->bindParam(":idTratamiento", $id, PDO::PARAM_INT);
            $stmtMedicamentos->execute();

            // 3. Eliminar el tratamiento
            $stmtTratamiento = $conexion->prepare(
                "DELETE FROM $tabla WHERE idTratamiento = :idTratamiento"
            );
            $stmtTratamiento->bindParam(":idTratamiento", $id, PDO::PARAM_INT);
            $stmtTratamiento->execute();

            // Confirmar transacción
            $conexion->commit();
            return "ok";
        } catch (PDOException $e) {
            // Revertir transacción si ocurre error
            $conexion->rollBack();
            print_r($e->getMessage());
            return "error";
        }
    }
 /*=============================================
MOSTRAR DETALLE DE SERVICIOS
=============================================*/
static public function mdlMostrarDetalleServicios($tabla, $item = null, $valor = null)
{
    $conexion = Conexion::conectar();

    if($item && $valor){
        $stmt = $conexion->prepare(
            "SELECT dts.idDetalle, dts.idTratamiento, dts.idServicio, dts.cantidad, dts.precio,
                    s.nombreServicio
             FROM $tabla dts
             LEFT JOIN servicios s ON dts.idServicio = s.idServicio
             WHERE $item = :valor"
        );

        $stmt->bindParam(":valor", $valor, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } else {
        // Si no se pasa filtro, devuelve todo
        $stmt = $conexion->prepare(
            "SELECT dts.idDetalle, dts.idTratamiento, dts.idServicio, dts.cantidad, dts.precio,
                    s.nombreServicio
             FROM $tabla dts
             LEFT JOIN servicios s ON dts.idServicio = s.idServicio"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

    /*=============================================
MOSTRAR DETALLE DE MEDICAMENTOS
=============================================*/
    static public function mdlMostrarDetalleMedicamento($tabla, $item = null, $valor = null) 
{
    $conexion = Conexion::conectar();

    $stmt = $conexion->prepare(
        "SELECT 
            dm.idDetalleMedicamento, 
            dm.idTratamiento, 
            dm.codMedicamento, 
            dm.dosis, 
            dm.fechaInicio, 
            dm.fechaFinal, 
            dm.observacion, 
            dm.tiempo,
            m.nombre AS nombreMedicamento,
            p.nombre AS nombrePaciente
        FROM $tabla dm
        LEFT JOIN medicamento m ON dm.codMedicamento = m.codMedicamento
        LEFT JOIN tratamiento t ON dm.idTratamiento = t.idTratamiento
        LEFT JOIN pacientes p ON t.idPaciente = p.idPaciente"
    );

    $stmt->execute();
    return $stmt->fetchAll();
}

    /* =============================================
     OBTENER DATOS DE TRATAMIENTO POR ID
  ============================================= */
    static public function mdlObtenerTratamiento($tabla, $id)
    {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE idTratamiento = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function mdlActualizarSaldoTratamiento($idTratamiento, $saldo)
    {
        $stmt = Conexion::conectar()->prepare("
    UPDATE tratamiento SET saldo = :saldo WHERE idTratamiento = :id
  ");
        $stmt->bindParam(":saldo", $saldo, PDO::PARAM_STR);
        $stmt->bindParam(":id", $idTratamiento, PDO::PARAM_INT);
        return $stmt->execute();
    }
    static public function mdlActualizarSaldo($idTratamiento, $nuevoSaldo)
    {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("UPDATE tratamiento SET saldo = :saldo WHERE idTratamiento = :idTratamiento");
        $stmt->bindParam(":saldo", $nuevoSaldo, PDO::PARAM_STR);
        $stmt->bindParam(":idTratamiento", $idTratamiento, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /*=============================================
ACTUALIZAR ESTADO DE PAGO
=============================================*/
  static public function mdlActualizarEstadoPago($idTratamiento, $estadoPago) {
    $stmt = Conexion::conectar()->prepare("UPDATE tratamiento SET estadoPago = :estadoPago WHERE idTratamiento = :idTratamiento");
    $stmt->bindParam(":estadoPago", $estadoPago, PDO::PARAM_STR);
    $stmt->bindParam(":idTratamiento", $idTratamiento, PDO::PARAM_INT);
    return $stmt->execute() ? "ok" : "error";
}

static public function mdlActualizarEstado($idTratamiento, $estado) {
    $stmt = Conexion::conectar()->prepare("UPDATE tratamiento SET estado = :estado WHERE idTratamiento = :idTratamiento");
    $stmt->bindParam(":estado", $estado, PDO::PARAM_STR);
    $stmt->bindParam(":idTratamiento", $idTratamiento, PDO::PARAM_INT);
    return $stmt->execute() ? "ok" : "error";
}

    /*=============================================
RECALCULAR TOTALES DESDE DETALLE
=============================================*/
    static public function mdlActualizarTotalesDesdeDetalle($idTratamiento)
    {
        $conexion = Conexion::conectar();

        // Obtener suma exacta de los servicios asociados al tratamiento
        $stmt = $conexion->prepare("
        SELECT SUM(precio * cantidad) AS total
        FROM detalleTratamientoServicios
        WHERE idTratamiento = :idTratamiento
    ");
        $stmt->bindParam(":idTratamiento", $idTratamiento, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        $total = $resultado['total'] ?? 0;

        // Actualizar tratamiento con el total calculado
        $stmtUpdate = $conexion->prepare("
        UPDATE tratamiento
        SET totalPago = :total, saldo = :total
        WHERE idTratamiento = :idTratamiento
    ");
        $stmtUpdate->bindParam(":total", $total, PDO::PARAM_STR);
        $stmtUpdate->bindParam(":idTratamiento", $idTratamiento, PDO::PARAM_INT);

        return $stmtUpdate->execute();
    }

/*=============================================
OBTENER SERVICIOS DE UN TRATAMIENTO
=============================================*/
static public function mdlObtenerServiciosPorTratamiento($idTratamiento) {
    $stmt = Conexion::conectar()->prepare("
        SELECT s.idServicio, s.nombreServicio, s.descripcion, dts.cantidad, dts.precio
        FROM detalleTratamientoServicios dts
        INNER JOIN servicios s ON dts.idServicio = s.idServicio
        WHERE dts.idTratamiento = :idTratamiento
    ");
    $stmt->bindParam(":idTratamiento", $idTratamiento, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

 /*=============================================
    MOSTRAR TRATAMIENTOS PENDIENTES
    =============================================*/
    static public function mdlMostrarTratamientosPendientes($tabla) {

        $stmt = Conexion::conectar()->prepare("
            SELECT * 
            FROM $tabla 
            WHERE estado = 'activo' OR estadoPago IN ('pendiente','parcial')
        ");

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;
    }

    // Eliminamos estas funciones de borrado porque ahora acumulamos en vez de reemplazar
    // static public function mdlEliminarServicios($idTratamiento) {}
    // static public function mdlEliminarMedicamentos($idTratamiento) {}














    // Tratamientos completados
    static public function mdlTratamientosCompletados($desde = null, $hasta = null) {
        $sql = "SELECT t.idTratamiento, p.nombre AS nombre_paciente, s.nombreServicio AS nombre_servicio, t.fechaRegistro AS fecha_fin
                FROM tratamiento t
                INNER JOIN pacientes p ON t.idPaciente = p.idPaciente
                LEFT JOIN detalletratamientoservicios dts ON t.idTratamiento = dts.idTratamiento
                LEFT JOIN servicios s ON dts.idServicio = s.idServicio
                WHERE t.estado = 'completado'";

        if ($desde && $hasta) {
            $sql .= " AND t.fechaRegistro BETWEEN :desde AND :hasta";
        }

        $stmt = Conexion::conectar()->prepare($sql);
        if ($desde && $hasta) {
            $stmt->bindParam(":desde", $desde, PDO::PARAM_STR);
            $stmt->bindParam(":hasta", $hasta, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tratamientos parciales
    static public function mdlTratamientosParciales($desde = null, $hasta = null) {
        $sql = "SELECT t.idTratamiento, p.nombre AS nombre_paciente, 
                       ROUND((t.totalPago - t.saldo)/t.totalPago*100, 0) AS progreso
                FROM tratamiento t
                INNER JOIN pacientes p ON t.idPaciente = p.idPaciente
                WHERE t.estado != 'completado'";

        if ($desde && $hasta) {
            $sql .= " AND t.fechaRegistro BETWEEN :desde AND :hasta";
        }

        $stmt = Conexion::conectar()->prepare($sql);
        if ($desde && $hasta) {
            $stmt->bindParam(":desde", $desde, PDO::PARAM_STR);
            $stmt->bindParam(":hasta", $hasta, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tratamientos activos
    static public function mdlTratamientosActivos($desde = null, $hasta = null) {
        $sql = "SELECT t.idTratamiento, p.nombre AS nombre_paciente, s.nombreServicio AS nombre_servicio
                FROM tratamiento t
                INNER JOIN pacientes p ON t.idPaciente = p.idPaciente
                LEFT JOIN detalletratamientoservicios dts ON t.idTratamiento = dts.idTratamiento
                LEFT JOIN servicios s ON dts.idServicio = s.idServicio
                WHERE t.estado = 'activo'";

        if ($desde && $hasta) {
            $sql .= " AND t.fechaRegistro BETWEEN :desde AND :hasta";
        }

        $stmt = Conexion::conectar()->prepare($sql);
        if ($desde && $hasta) {
            $stmt->bindParam(":desde", $desde, PDO::PARAM_STR);
            $stmt->bindParam(":hasta", $hasta, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tratamientos no cancelados
    static public function mdlTratamientosNoCancelados($desde = null, $hasta = null) {
        $sql = "SELECT t.idTratamiento, p.nombre AS nombre_paciente, t.estado
                FROM tratamiento t
                INNER JOIN pacientes p ON t.idPaciente = p.idPaciente
                WHERE t.estado != 'cancelado'";

        if ($desde && $hasta) {
            $sql .= " AND t.fechaRegistro BETWEEN :desde AND :hasta";
        }

        $stmt = Conexion::conectar()->prepare($sql);
        if ($desde && $hasta) {
            $stmt->bindParam(":desde", $desde, PDO::PARAM_STR);
            $stmt->bindParam(":hasta", $hasta, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tratamientos por odontólogo
    static public function mdlTratamientosPorOdontologo($desde = null, $hasta = null) {
        $sql = "SELECT u.nombre AS nombre_odontologo, COUNT(*) AS cantidad
                FROM tratamiento t
                INNER JOIN usuarios u ON t.idUsuarios = u.idUsuarios
                WHERE u.idRol = 2"; // odontólogo

        if ($desde && $hasta) {
            $sql .= " AND t.fechaRegistro BETWEEN :desde AND :hasta";
        }

        $sql .= " GROUP BY u.idUsuarios";

        $stmt = Conexion::conectar()->prepare($sql);
        if ($desde && $hasta) {
            $stmt->bindParam(":desde", $desde, PDO::PARAM_STR);
            $stmt->bindParam(":hasta", $hasta, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tratamientos por servicio
    static public function mdlTratamientosPorServicio($desde = null, $hasta = null) {
        $sql = "SELECT s.nombreServicio, COUNT(*) AS cantidad
                FROM detalletratamientoservicios dts
                INNER JOIN servicios s ON dts.idServicio = s.idServicio
                INNER JOIN tratamiento t ON dts.idTratamiento = t.idTratamiento
                WHERE 1";

        if ($desde && $hasta) {
            $sql .= " AND t.fechaRegistro BETWEEN :desde AND :hasta";
        }

        $sql .= " GROUP BY s.idServicio";

        $stmt = Conexion::conectar()->prepare($sql);
        if ($desde && $hasta) {
            $stmt->bindParam(":desde", $desde, PDO::PARAM_STR);
            $stmt->bindParam(":hasta", $hasta, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tratamientos por estado
    static public function mdlTratamientosPorEstado($desde = null, $hasta = null) {
        $sql = "SELECT estado, COUNT(*) AS cantidad
                FROM tratamiento
                WHERE 1";

        if ($desde && $hasta) {
            $sql .= " AND fechaRegistro BETWEEN :desde AND :hasta";
        }

        $sql .= " GROUP BY estado";

        $stmt = Conexion::conectar()->prepare($sql);
        if ($desde && $hasta) {
            $stmt->bindParam(":desde", $desde, PDO::PARAM_STR);
            $stmt->bindParam(":hasta", $hasta, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tratamientos mensuales
    static public function mdlTratamientosMensuales() {
        $sql = "SELECT MONTH(fechaRegistro) AS mes, YEAR(fechaRegistro) AS anio, COUNT(*) AS cantidad
                FROM tratamiento
                GROUP BY YEAR(fechaRegistro), MONTH(fechaRegistro)
                ORDER BY YEAR(fechaRegistro), MONTH(fechaRegistro)";

        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
