<?php
require_once "conexion.php";

class ModeloInicio {

    static public function mdlObtenerEstadisticas()
    {
        // Contar pacientes totales
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) AS total FROM pacientes");
        $stmt->execute();
        $pacientes_total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Contar pacientes nuevos del mes
        $stmt = Conexion::conectar()->prepare("
            SELECT COUNT(*) AS nuevos 
            FROM pacientes 
            WHERE MONTH(fechaRegistro) = MONTH(CURDATE()) 
              AND YEAR(fechaRegistro) = YEAR(CURDATE())");
        $stmt->execute();
        $pacientes_mes = $stmt->fetch(PDO::FETCH_ASSOC)['nuevos'];

        // Contar citas confirmadas
        $stmt = Conexion::conectar()->prepare("
            SELECT COUNT(*) AS confirmada 
            FROM citas 
            WHERE estado = 'confirmada'");
        $stmt->execute();
        $citas_confirmadas = $stmt->fetch(PDO::FETCH_ASSOC)['confirmada'];

        // Contar tratamientos activos
        $stmt = Conexion::conectar()->prepare("
            SELECT COUNT(*) AS activo 
            FROM tratamiento 
            WHERE estado = 'activo'");
        $stmt->execute();
        $tratamientos_activos = $stmt->fetch(PDO::FETCH_ASSOC)['activo'];

        // Contar tratamientos completados
        $stmt = Conexion::conectar()->prepare("
            SELECT COUNT(*) AS completado 
            FROM tratamiento 
            WHERE estado = 'completado'");
        $stmt->execute();
        $tratamientos_completados = $stmt->fetch(PDO::FETCH_ASSOC)['completado'];

        // Sumar los ingresos por tratamientos completados
        $stmt = Conexion::conectar()->prepare("
            SELECT SUM(totalPago) AS totalPago 
            FROM tratamiento 
            WHERE estado = 'completado'");
        $stmt->execute();
        $tratamientos_ingresos = $stmt->fetch(PDO::FETCH_ASSOC)['totalPago'];
        $tratamientos_ingresos = $tratamientos_ingresos ?? 0; // Si es null, poner 0

        // Contar citas programadas para hoy
        $stmt = Conexion::conectar()->prepare("
            SELECT COUNT(*) AS citas_hoy 
            FROM citas 
            WHERE DATE(fecha) = CURDATE()");
        $stmt->execute();
        $citas_hoy = $stmt->fetch(PDO::FETCH_ASSOC)['citas_hoy'];

        return [
            'pacientes' => [
                'total' => $pacientes_total, 
                'nuevos' => $pacientes_mes
            ],
            'citas' => [
                'confirmada' => $citas_confirmadas,
                'hoy' => $citas_hoy
            ],
            'tratamiento' => [
                'activo' => $tratamientos_activos, 
                'completado' => $tratamientos_completados, 
                'ingresos' => $tratamientos_ingresos
            ]
        ];
    }

    // Obtener las citas de hoy con detalles del paciente y doctor
    static public function mdlCitasHoy()
    {
        $query = "
            SELECT c.*, p.nombre AS paciente_nombre, p.ci AS paciente_ci, 
                   u.nombre AS doctor_nombre, u.apellido AS doctor_apellido
            FROM citas c
            LEFT JOIN pacientes p ON c.idPaciente = p.idPaciente
            LEFT JOIN usuarios u ON c.idUsuarios = u.idUsuarios
            WHERE DATE(c.fecha) = CURDATE()
            ORDER BY c.hora ASC
        ";

        $stmt = Conexion::conectar()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener citas por estado (confirmada, pendiente, etc.)
    static public function mdlCitasByStatus($estado)
    {
        $query = "
            SELECT c.*, p.nombre AS paciente_nombre, p.ci AS paciente_ci, 
                   u.nombre AS doctor_nombre, u.apellido AS doctor_apellido
            FROM citas c
            LEFT JOIN pacientes p ON c.idPaciente = p.idPaciente
            LEFT JOIN usuarios u ON c.idUsuarios = u.idUsuarios
            WHERE c.estado = :estado
            ORDER BY c.fecha ASC, c.hora ASC
        ";

        $stmt = Conexion::conectar()->prepare($query);
        $stmt->bindParam(':estado', $estado);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
