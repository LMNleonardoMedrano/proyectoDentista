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

// 1. Buscar paciente
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

// 2. Citas
$citas = Conexion::conectar()->prepare("
    SELECT c.*, CONCAT(u.nombre, ' ', u.apellido) AS odontologo
    FROM citas c
    INNER JOIN usuarios u ON u.idUsuarios = c.idUsuarios
    WHERE c.idPaciente = :id
    ORDER BY c.fecha DESC, c.hora DESC
");
$citas->execute(['id' => $idPaciente]);
$citasHistorial = $citas->fetchAll(PDO::FETCH_ASSOC);

// 3. Tratamientos
$tratamientos = Conexion::conectar()->prepare("
    SELECT t.*, CONCAT(u.nombre, ' ', u.apellido) AS odontologo
    FROM tratamiento t
    INNER JOIN usuarios u ON u.idUsuarios = t.idUsuarios
    WHERE t.idPaciente = :id
    ORDER BY t.fechaRegistro DESC
");
$tratamientos->execute(['id' => $idPaciente]);
$tratamientosHistorial = $tratamientos->fetchAll(PDO::FETCH_ASSOC);

// 4. Medicamentos
$medicamentos = Conexion::conectar()->prepare("
    SELECT dm.*, m.nombre AS medicamento
    FROM detallemedicamento dm
    INNER JOIN medicamento m ON m.codMedicamento = dm.codMedicamento
    INNER JOIN tratamiento t ON t.idTratamiento = dm.idTratamiento
    WHERE t.idPaciente = :id
");
$medicamentos->execute(['id' => $idPaciente]);
$medicamentosHistorial = $medicamentos->fetchAll(PDO::FETCH_ASSOC);

// 5. Pagos
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
// DISEÑO HTML DEL HISTORIAL
// ==============================
?>
<style>
.historial-container {
    background: #fff;
    border-radius: 10px;
    padding: 25px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    font-family: 'Segoe UI', sans-serif;
    color: #333;
}
.historial-header {
    text-align: center;
    border-bottom: 3px solid #007bff;
    padding-bottom: 10px;
    margin-bottom: 20px;
}
.historial-header h2 {
    color: #007bff;
    font-weight: bold;
}
.section {
    margin-bottom: 25px;
}
.section h4 {
    background: #007bff;
    color: #fff;
    padding: 8px 12px;
    border-radius: 5px;
    font-size: 16px;
}
.table-historial {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
.table-historial th, .table-historial td {
    border: 1px solid #ddd;
    padding: 8px;
}
.table-historial th {
    background: #f1f1f1;
    color: #333;
    text-align: left;
}
.summary {
    background: #f8f9fa;
    border-left: 5px solid #007bff;
    padding: 15px;
    border-radius: 5px;
}
</style>

<div class="historial-container">
    <div class="historial-header">
        <h2>🩺 Historial Clínico del Paciente</h2>
    </div>

    <div class="section">
        <h4>🧍 Datos del Paciente</h4>
        <p><b>Nombre:</b> <?= $paciente['nombre'] ?></p>
        <p><b>DNI/CI:</b> <?= $paciente['ci'] ?></p>
        <p><b>Fecha de nacimiento:</b> <?= $paciente['fechaNac'] ?></p>
        <p><b>Teléfono:</b> <?= $paciente['telCel'] ?></p>
        <p><b>Dirección:</b> <?= $paciente['domicilio'] ?></p>
        <p><b>Sexo:</b> <?= $paciente['genero'] ?></p>
        <?php if ($paciente['tutor']) : ?>
            <p><b>Tutor:</b> <?= $paciente['tutor'] ?> (<?= $paciente['relacion'] ?>)</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h4>📅 Historial de Citas</h4>
        <?php if (count($citasHistorial) > 0): ?>
            <table class="table-historial">
                <tr><th>Fecha</th><th>Hora</th><th>Motivo</th><th>Odontólogo</th><th>Estado</th></tr>
                <?php foreach ($citasHistorial as $c): ?>
                    <tr>
                        <td><?= $c['fecha'] ?></td>
                        <td><?= $c['hora'] ?></td>
                        <td><?= $c['motivoConsulta'] ?></td>
                        <td><?= $c['odontologo'] ?></td>
                        <td><?= $c['estado'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No hay citas registradas.</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h4>🦷 Tratamientos</h4>
        <?php if (count($tratamientosHistorial) > 0): ?>
            <?php foreach ($tratamientosHistorial as $t): ?>
                <div style="margin-bottom:10px;">
                    <b>Fecha:</b> <?= $t['fechaRegistro'] ?> | <b>Odontólogo:</b> <?= $t['odontologo'] ?> |
                    <b>Total:</b> <?= $t['totalPago'] ?> | <b>Saldo:</b> <?= $t['saldo'] ?> | <b>Estado:</b> <?= $t['estado'] ?><br>
                    <?php
                    $servs = Conexion::conectar()->prepare("
                        SELECT s.nombreServicio
                        FROM detalletratamientoservicios dts
                        INNER JOIN servicios s ON s.idServicio = dts.idServicio
                        WHERE dts.idTratamiento = :idTratamiento
                    ");
                    $servs->execute(['idTratamiento' => $t['idTratamiento']]);
                    $servicios = $servs->fetchAll(PDO::FETCH_ASSOC);
                    $servStr = implode(", ", array_column($servicios,'nombreServicio'));
                    ?>
                    <b>Servicios realizados:</b> <?= $servStr ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay tratamientos registrados.</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h4>💊 Medicamentos Recetados</h4>
        <?php if (count($medicamentosHistorial) > 0): ?>
            <table class="table-historial">
                <tr><th>Medicamento</th><th>Dosis</th><th>Inicio</th><th>Fin</th><th>Observación</th></tr>
                <?php foreach ($medicamentosHistorial as $m): ?>
                    <tr>
                        <td><?= $m['medicamento'] ?></td>
                        <td><?= $m['dosis'] ?></td>
                        <td><?= $m['fechaInicio'] ?></td>
                        <td><?= $m['fechaFinal'] ?></td>
                        <td><?= $m['observacion'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No hay medicamentos recetados.</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h4>💰 Pagos Realizados</h4>
        <?php if (count($pagosHistorial) > 0): ?>
            <table class="table-historial">
                <tr><th>Fecha</th><th>Monto</th><th>Forma de Pago</th><th>Observación</th></tr>
                <?php foreach ($pagosHistorial as $p): ?>
                    <tr>
                        <td><?= $p['fecha'] ?></td>
                        <td><?= $p['monto'] ?></td>
                        <td><?= $p['nombreTipoPago'] ?></td>
                        <td><?= $p['descripcion'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No hay pagos registrados.</p>
        <?php endif; ?>
    </div>

    <div class="section summary">
        <?php
        $totalTratamientos = count($tratamientosHistorial);
        $totalPagado = array_sum(array_column($pagosHistorial,'monto'));
        $saldoPendiente = array_sum(array_column($tratamientosHistorial,'saldo'));
        $ultimaCita = end($citasHistorial)['fecha'] ?? '-';
        $ultimoTrat = end($tratamientosHistorial)['fechaRegistro'] ?? '-';
        ?>
        <h4>🧩 Resumen Final</h4>
        <p><b>Total de tratamientos:</b> <?= $totalTratamientos ?></p>
        <p><b>Total pagado:</b> <?= $totalPagado ?></p>
        <p><b>Saldo pendiente:</b> <?= $saldoPendiente ?></p>
        <p><b>Última cita:</b> <?= $ultimaCita ?></p>
        <p><b>Último tratamiento completado:</b> <?= $ultimoTrat ?></p>
    </div>
</div>
