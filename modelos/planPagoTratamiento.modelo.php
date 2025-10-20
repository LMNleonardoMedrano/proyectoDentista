<?php

require_once "conexion.php";

class ModeloPlanPago
{

    /*=============================================
    MOSTRAR PLANES DE PAGO
    =============================================*/
   static public function mdlMostrarPlanesPago($tabla, $item, $valor)
{
    if ($item != null) {
        $stmt = Conexion::conectar()->prepare(
            "SELECT pp.*, tp.nombreTipoPago 
             FROM $tabla pp 
             INNER JOIN tipoPago tp ON pp.codTipoPago = tp.codTipoPago 
             WHERE pp.$item = :$item"
        );
        $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
       $stmt = Conexion::conectar()->prepare(
    "SELECT pp.*, tp.nombreTipoPago 
     FROM $tabla pp 
     INNER JOIN tipoPago tp ON pp.codTipoPago = tp.codTipoPago
     ORDER BY pp.codPlan DESC"
);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $stmt = null;
}
    public static function mdlMostrarTiposPago()
    {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM tipoPago");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
  /*=============================================
REGISTRAR PLAN DE PAGO
=============================================*/
static public function mdlIngresarPlanPago($tabla, $datos)
{
    $conexion = Conexion::conectar();

    $stmt = $conexion->prepare("INSERT INTO $tabla(descripcion, descuento, fecha, monto, idTratamiento, codTipoPago) 
                                VALUES (:descripcion, :descuento, :fecha, :monto, :idTratamiento, :codTipoPago)");

    $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
    $stmt->bindParam(":descuento", $datos["descuento"], PDO::PARAM_STR);
    $stmt->bindParam(":fecha", $datos["fecha"], PDO::PARAM_STR);
    $stmt->bindParam(":monto", $datos["monto"], PDO::PARAM_STR);
    $stmt->bindParam(":idTratamiento", $datos["idTratamiento"], PDO::PARAM_INT);
    $stmt->bindParam(":codTipoPago", $datos["codTipoPago"], PDO::PARAM_INT);

    if($stmt->execute()){
        // Devuelve el codPlan recién insertado
        return $conexion->lastInsertId();
    } else {
        return false;
    }
}
 static public function mdlEditarPlanPago($tabla, $datos){
        $stmt = Conexion::conectar()->prepare(
            "UPDATE $tabla SET 
                descripcion = :descripcion,
                descuento = :descuento,
                fecha = :fecha,
                monto = :monto,
                codTipoPago = :codTipoPago
            WHERE codPlan = :codPlan"
        );

        $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
        $stmt->bindParam(":descuento", $datos["descuento"], PDO::PARAM_STR);
        $stmt->bindParam(":fecha", $datos["fecha"], PDO::PARAM_STR);
        $stmt->bindParam(":monto", $datos["monto"], PDO::PARAM_STR);
        $stmt->bindParam(":codTipoPago", $datos["codTipoPago"], PDO::PARAM_INT);
        $stmt->bindParam(":codPlan", $datos["codPlan"], PDO::PARAM_INT);

        return $stmt->execute() ? "ok" : "error";
        $stmt = null;
    }
    /*=============================================
ACTUALIZAR SALDO DE TRATAMIENTO
=============================================*/
static public function mdlActualizarSaldoTratamiento($idTratamiento, $monto)
{
    $stmt = Conexion::conectar()->prepare("
        UPDATE tratamiento 
        SET saldo = saldo - :monto 
        WHERE idTratamiento = :idTratamiento
    ");

    $stmt->bindParam(":monto", $monto, PDO::PARAM_STR);
    $stmt->bindParam(":idTratamiento", $idTratamiento, PDO::PARAM_INT);

    return $stmt->execute();
}
public static function mdlMostrarPagosPorTratamiento($idTratamiento) {
  $stmt = Conexion::conectar()->prepare("
    SELECT pp.*, tp.nombreTipoPago
    FROM planPagoTratamiento pp
    INNER JOIN tipoPago tp ON pp.codTipoPago = tp.codTipoPago
    WHERE pp.idTratamiento = :id
    ORDER BY pp.fecha ASC
  ");
  $stmt->bindParam(":id", $idTratamiento, PDO::PARAM_INT);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
 /*=============================================
    BORRAR PlanPagos
    =============================================*/
    static public function mdlBorrarPlanPagos($tabla, $id) {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE codPlan = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }
    /* =============================
     MOSTRAR TIPOS DE PAGO
  ============================== */
  static public function mdlMostrarTipoPago($tabla, $item, $valor) {
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
    $stmt->close();
    $stmt = null;
  }







// // 1. Pagos por paciente individual
// static public function mdlPagosPorPaciente($idPaciente) {
//     $stmt = Conexion::conectar()->prepare("
//         SELECT pp.fecha, pp.monto, tp.nombreTipoPago, t.estadoPago
//         FROM planpagotratamiento pp
//         INNER JOIN tratamiento t ON pp.idTratamiento = t.idTratamiento
//         INNER JOIN tipopago tp ON pp.codTipoPago = tp.codTipoPago
//         WHERE t.idPaciente = :idPaciente
//         ORDER BY pp.fecha ASC
//     ");
//     $stmt->bindParam(":idPaciente", $idPaciente, PDO::PARAM_INT);
//     $stmt->execute();
//     return $stmt->fetchAll(PDO::FETCH_ASSOC);
// }

// 2. Pagos por estado de tratamiento
static public function mdlPagosPorEstadoTratamiento() {
    $stmt = Conexion::conectar()->prepare("
        SELECT t.estadoPago, COUNT(*) AS cantidad, SUM(pp.monto) AS total
        FROM tratamiento t
        INNER JOIN planpagotratamiento pp ON pp.idTratamiento = t.idTratamiento
        GROUP BY t.estadoPago
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 3. Descuentos aplicados
static public function mdlDescuentosAplicados() {
    $stmt = Conexion::conectar()->prepare("
        SELECT pp.fecha, pp.monto, pp.descuento, pp.descripcion
        FROM planpagotratamiento pp
        WHERE pp.descuento > 0
        ORDER BY pp.fecha DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 4. Pagos por servicio
static public function mdlPagosPorServicio() {
    $stmt = Conexion::conectar()->prepare("
        SELECT s.nombreServicio, COUNT(*) AS veces, SUM(dts.precio) AS totalRecaudado
        FROM detalleTratamientoServicios dts
        INNER JOIN servicios s ON dts.idServicio = s.idServicio
        GROUP BY s.idServicio
        ORDER BY totalRecaudado DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 5. Pagos mensuales
static public function mdlPagosMensuales() {
    $stmt = Conexion::conectar()->prepare("
        SELECT MONTH(pp.fecha) AS mes, YEAR(pp.fecha) AS anio, SUM(pp.monto) AS total
        FROM planpagotratamiento pp
        GROUP BY YEAR(pp.fecha), MONTH(pp.fecha)
        ORDER BY anio, mes
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
static public function mdlPagosPorOdontologo($desde, $hasta) {
    $stmt = Conexion::conectar()->prepare("
        SELECT u.idUsuarios, u.nombre, u.apellido, SUM(pp.monto) AS totalRecaudado
        FROM planpagotratamiento pp
        INNER JOIN tratamiento t ON pp.idTratamiento = t.idTratamiento
        INNER JOIN usuarios u ON t.idUsuarios = u.idUsuarios
        WHERE pp.fecha BETWEEN :desde AND :hasta
        GROUP BY u.idUsuarios, u.nombre, u.apellido
        ORDER BY totalRecaudado DESC
    ");
    $stmt->bindParam(":desde", $desde, PDO::PARAM_STR);
    $stmt->bindParam(":hasta", $hasta, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

static public function mdlPagosPorTipo($desde, $hasta) {
    $stmt = Conexion::conectar()->prepare("
        SELECT tp.nombreTipoPago, SUM(pp.monto) AS total
        FROM planpagotratamiento pp
        INNER JOIN tipopago tp ON pp.codTipoPago = tp.codTipoPago
        WHERE pp.fecha BETWEEN :desde AND :hasta
        GROUP BY tp.nombreTipoPago
        ORDER BY total DESC
    ");
    $stmt->bindParam(":desde", $desde, PDO::PARAM_STR);
    $stmt->bindParam(":hasta", $hasta, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
  // 1. Pagos totales entre fechas
static public function mdlPagosTotales($desde, $hasta) {
   $stmt = Conexion::conectar()->prepare(
    "SELECT pp.fecha, pp.monto, t.idPaciente, p.nombre AS nombrePaciente
     FROM planpagotratamiento pp
     INNER JOIN tratamiento t ON pp.idTratamiento = t.idTratamiento
     INNER JOIN pacientes p ON t.idPaciente = p.idPaciente
     WHERE pp.fecha BETWEEN :desde AND :hasta"
);
    $stmt->bindParam(":desde", $desde, PDO::PARAM_STR);
    $stmt->bindParam(":hasta", $hasta, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

   static public function mdlSaldosPorPaciente() {
    $stmt = Conexion::conectar()->prepare("
        SELECT 
            p.idPaciente, 
            p.nombre, 
            SUM(t.saldo) AS saldo_total
        FROM pacientes p
        INNER JOIN tratamiento t ON t.idPaciente = p.idPaciente
        GROUP BY p.idPaciente, p.nombre
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // 3. Pagos pendientes
static public function mdlPagosPendientes() {
    $stmt = Conexion::conectar()->prepare(
        "SELECT 
            p.idPaciente, 
            p.nombre, 
            SUM(t.saldo) AS saldo
         FROM pacientes p
         INNER JOIN tratamiento t ON t.idPaciente = p.idPaciente
         WHERE t.saldo > 0
         GROUP BY p.idPaciente, p.nombre"
    );
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // 4. Pagos por día entre fechas
    static public function mdlPagosPorDia($desde, $hasta) {
        $stmt = Conexion::conectar()->prepare(
            "SELECT fecha, SUM(monto) as recaudacion
             FROM planPagoTratamiento
             WHERE fecha BETWEEN :desde AND :hasta
             GROUP BY fecha
             ORDER BY fecha ASC"
        );
        $stmt->bindParam(":desde", $desde, PDO::PARAM_STR);
        $stmt->bindParam(":hasta", $hasta, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 5. Servicios más solicitados
static public function mdlServiciosMasSolicitados($desde, $hasta) {
    $stmt = Conexion::conectar()->prepare(
        "SELECT s.nombreServicio, COUNT(dts.idServicio) AS veces
         FROM detalleTratamientoServicios dts
         INNER JOIN tratamiento t ON dts.idTratamiento = t.idTratamiento
         INNER JOIN servicios s ON dts.idServicio = s.idServicio
         WHERE t.fechaRegistro BETWEEN :desde AND :hasta
         GROUP BY s.nombreServicio
         ORDER BY veces DESC"
    );
    $stmt->bindParam(":desde", $desde, PDO::PARAM_STR);
    $stmt->bindParam(":hasta", $hasta, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
