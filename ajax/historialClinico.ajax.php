<?php
require_once "../modelos/conexion.php"; // Conexión a la DB

// ==============================
// AUTOCOMPLETADO
// ==============================
if (isset($_GET['autocompletar'])) {
    $query = trim($_GET['autocompletar']);
    if ($query === '') {
        echo json_encode([]);
        exit;
    }

    $stmt = Conexion::conectar()->prepare("
        SELECT idPaciente, nombre, ci 
        FROM pacientes 
        WHERE nombre LIKE :query OR ci LIKE :query 
        ORDER BY nombre ASC 
        LIMIT 10
    ");
    $like = "%$query%";
    $stmt->execute(['query' => $like]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
    exit;
}

// ==============================
// BÚSQUEDA COMPLETA DE HISTORIAL
// ==============================
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

if ($busqueda === '') {
    echo "<div class='alert alert-info'>Ingrese un nombre o CI para buscar.</div>";
    exit;
}

// 1. Buscar paciente por nombre o CI
$stmtPaciente = Conexion::conectar()->prepare("
    SELECT p.*, pm.nombrePT AS tutor, pm.relacion
    FROM pacientes p
    LEFT JOIN pacientemenor pm ON pm.idPaciente = p.idPaciente
    WHERE p.nombre LIKE :busqueda OR p.ci LIKE :busqueda
    LIMIT 1
");
$stmtPaciente->execute(['busqueda' => "%$busqueda%"]);
$paciente = $stmtPaciente->fetch(PDO::FETCH_ASSOC);

if (!$paciente) {
    echo "<div class='alert alert-warning'>No se encontró ningún paciente con '$busqueda'.</div>";
    exit;
}

$idPaciente = $paciente['idPaciente'];

// 2. Historial de citas
$citas = Conexion::conectar()->prepare("
    SELECT c.*, u.nombre AS odontologo
    FROM citas c
    INNER JOIN usuarios u ON u.idUsuarios = c.idUsuarios
    WHERE c.idPaciente = :id
    ORDER BY c.fecha DESC, c.hora DESC
");
$citas->execute(['id' => $idPaciente]);
$citasHistorial = $citas->fetchAll(PDO::FETCH_ASSOC);

// 3. Tratamientos y servicios
$tratamientos = Conexion::conectar()->prepare("
    SELECT t.*, u.nombre AS odontologo
    FROM tratamiento t
    INNER JOIN usuarios u ON u.idUsuarios = t.idUsuarios
    WHERE t.idPaciente = :id
    ORDER BY t.fechaRegistro DESC
");
$tratamientos->execute(['id' => $idPaciente]);
$tratamientosHistorial = $tratamientos->fetchAll(PDO::FETCH_ASSOC);

// 4. Medicamentos por tratamiento
$medicamentos = Conexion::conectar()->prepare("
    SELECT dm.*, m.nombre AS medicamento
    FROM detallemedicamento dm
    INNER JOIN medicamento m ON m.codMedicamento = dm.codMedicamento
    INNER JOIN tratamiento t ON t.idTratamiento = dm.idTratamiento
    WHERE t.idPaciente = :id
");
$medicamentos->execute(['id' => $idPaciente]);
$medicamentosHistorial = $medicamentos->fetchAll(PDO::FETCH_ASSOC);

// 5. Pagos realizados
$pagos = Conexion::conectar()->prepare("
    SELECT pp.*, tp.nombreTipoPago
    FROM planpagotratamiento pp
    INNER JOIN tipopago tp ON tp.codTipoPago = pp.codTipoPago
    INNER JOIN tratamiento t ON t.idTratamiento = pp.idTratamiento
    WHERE t.idPaciente = :id
");
$pagos->execute(['id' => $idPaciente]);
$pagosHistorial = $pagos->fetchAll(PDO::FETCH_ASSOC);

// ==============================
// GENERAR HTML DEL HISTORIAL
// ==============================
echo "<div class='historial-row p-3 bg-white rounded shadow-sm mb-3'>";

// Datos del paciente
echo "<h4>🧍 Datos del Paciente</h4>";
echo "<p><b>Nombre:</b> {$paciente['nombre']}</p>";
echo "<p><b>DNI/CI:</b> {$paciente['ci']}</p>";
echo "<p><b>Fecha de nacimiento:</b> {$paciente['fechaNac']}</p>";
echo "<p><b>Teléfono:</b> {$paciente['telCel']}</p>";
echo "<p><b>Dirección:</b> {$paciente['domicilio']}</p>";
echo "<p><b>Sexo:</b> {$paciente['genero']}</p>";
if($paciente['tutor']){
    echo "<p><b>Tutor:</b> {$paciente['tutor']} ({$paciente['relacion']})</p>";
}

// Historial de citas
echo "<h4>📅 Historial de Citas</h4>";
if(count($citasHistorial) > 0){
    echo "<ul>";
    foreach($citasHistorial as $cita){
        echo "<li>{$cita['fecha']} - {$cita['hora']} | {$cita['motivoConsulta']} | Odontólogo: {$cita['odontologo']} | Estado: {$cita['estado']}</li>";
    }
    echo "</ul>";
}else{
    echo "<p>No hay citas registradas.</p>";
}

// Tratamientos y servicios
echo "<h4>🦷 Tratamientos</h4>";
if(count($tratamientosHistorial) > 0){
    foreach($tratamientosHistorial as $t){
        echo "<p><b>Fecha:</b> {$t['fechaRegistro']} | Odontólogo: {$t['odontologo']} | Total: {$t['totalPago']} | Saldo: {$t['saldo']} | Estado: {$t['estado']}</p>";

        // Servicios por tratamiento
        $servs = Conexion::conectar()->prepare("
            SELECT s.nombreServicio
            FROM detalletratamientoservicios dts
            INNER JOIN servicios s ON s.idServicio = dts.idServicio
            WHERE dts.idTratamiento = :idTratamiento
        ");
        $servs->execute(['idTratamiento'=>$t['idTratamiento']]);
        $servicios = $servs->fetchAll(PDO::FETCH_ASSOC);
        $servStr = implode(", ", array_column($servicios,'nombreServicio'));
        echo "<p><b>Servicios realizados:</b> $servStr</p>";
    }
}else{
    echo "<p>No hay tratamientos registrados.</p>";
}

// Medicamentos
echo "<h4>💊 Medicamentos Recetados</h4>";
if(count($medicamentosHistorial) > 0){
    foreach($medicamentosHistorial as $m){
        echo "<p>{$m['medicamento']} | Dosis: {$m['dosis']} | Inicio: {$m['fechaInicio']} | Fin: {$m['fechaFinal']} | Obs: {$m['observacion']}</p>";
    }
}else{
    echo "<p>No hay medicamentos recetados.</p>";
}

// Pagos
echo "<h4>💰 Pagos Realizados</h4>";
if(count($pagosHistorial) > 0){
    foreach($pagosHistorial as $p){
        echo "<p>{$p['fecha']} | Monto: {$p['monto']} | Forma: {$p['nombreTipoPago']} | Obs: {$p['descripcion']}</p>";
    }
}else{
    echo "<p>No hay pagos registrados.</p>";
}

// Resumen final
$totalTratamientos = count($tratamientosHistorial);
$totalPagado = array_sum(array_column($pagosHistorial,'monto'));
$saldoPendiente = array_sum(array_column($tratamientosHistorial,'saldo'));
$ultimaCita = end($citasHistorial)['fecha'] ?? '-';
$ultimoTrat = end($tratamientosHistorial)['fechaRegistro'] ?? '-';

echo "<h4>🧩 Resumen Final</h4>";
echo "<p>Total de tratamientos: $totalTratamientos</p>";
echo "<p>Total pagado: $totalPagado</p>";
echo "<p>Saldo pendiente: $saldoPendiente</p>";
echo "<p>Última cita: $ultimaCita</p>";
echo "<p>Último tratamiento completado: $ultimoTrat</p>";

echo "</div>";
?>
