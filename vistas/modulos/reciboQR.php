<?php
date_default_timezone_set('America/La_Paz');
include_once '../../modelos/conexion.php';
$conexion = Conexion::conectar();

$tokenRecibido = $_GET['token'] ?? '';
$claveSecreta = "TuClaveUltraPrivada2025";
$tokenEsperado = hash('sha256', $_GET['codPlan'] . $claveSecreta);

if ($tokenRecibido !== $tokenEsperado) {
    die("<h2 style='color:red;'>❌ Acceso no autorizado. Token inválido.</h2>");
}

if (isset($_GET['codPlan'])) {
    $codPlan = $_GET['codPlan'];

    $stmt = $conexion->prepare("
        SELECT pp.*, tp.nombreTipoPago, t.idPaciente, t.totalPago, t.idUsuarios,
               CONCAT(p.nombre, ' (CI: ', p.ci, ')') AS pacienteNombre,
               CONCAT(u.nombre, ' ', u.apellido) AS nombreDoctor
        FROM planPagoTratamiento pp
        INNER JOIN tipoPago tp ON pp.codTipoPago = tp.codTipoPago
        INNER JOIN tratamiento t ON pp.idTratamiento = t.idTratamiento
        INNER JOIN pacientes p ON t.idPaciente = p.idPaciente
        INNER JOIN usuarios u ON t.idUsuarios = u.idUsuarios
        WHERE pp.codPlan = ?
    ");
    $stmt->bindParam(1, $codPlan, PDO::PARAM_INT);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        $stmtPagos = $conexion->prepare("SELECT SUM(monto) FROM planPagoTratamiento WHERE idTratamiento = ?");
        $stmtPagos->bindParam(1, $resultado["idTratamiento"], PDO::PARAM_INT);
        $stmtPagos->execute();
        $totalAbonado = floatval($stmtPagos->fetchColumn());

        $totalTratamiento = floatval($resultado["totalPago"]);

        if ($totalAbonado == 0) {
            $estadoPago = "PENDIENTE";
        } elseif ($totalAbonado >= $totalTratamiento) {
            $estadoPago = "PAGADO";
        } else {
            $estadoPago = "PARCIAL";
        }

        $colorEstado = $estadoPago === "PAGADO" ? "#28a745" : ($estadoPago === "PARCIAL" ? "#ffc107" : "#dc3545");

        ob_start();
        include_once '../modulos/phpqrcode/qrlib.php';
        $token = hash('sha256', $codPlan . $claveSecreta);
        $linkQR = "http://192.168.100.83/dentista/vistas/modulos/reciboQR.php?codPlan=$codPlan&token=$token";

        QRcode::png($linkQR, null, QR_ECLEVEL_L, 6); // QR más grande
        $qrImage = base64_encode(ob_get_clean());

        echo '
        <!DOCTYPE html>
        <html lang="es">
        <head>
        <meta charset="UTF-8">
        <title>Recibo de Tratamiento</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #fff;
                width: 21.6cm;
                height: 15cm;
                margin: 0 auto;
                padding: 25px 40px;
                color: #000;
            }
            .recibo {
                border: 3.5px solid #2dce89;
                padding: 50px 25px;
                height: 100%;
                position: relative;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }
            .header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid #000;
                padding-bottom: 5px;
                margin-bottom: 10px;
            }
            .logo {
                flex: 0 0 auto;
                display: flex;
                align-items: center;
            }
            .logo img {
                width: 190px; /* Cambia este valor para agrandar o achicar */
                height: auto;
            }
            .titulo {
                text-align: center;
                flex: 1;
                font-size: 20px;
                font-weight: bold;
            }
            .datos-recibo {
                text-align: right;
                font-size: 12px;
                flex: 0 0 auto;
            }
            .campo {
                margin: 10px 0;
                font-size: 14px;
            }
            .linea {
                display: inline-block;
                border-bottom: 1px solid #000;
                width: 80%;
                margin-left: 8px;
            }
            .linea-corta {
                display: inline-block;
                border-bottom: 1px solid #000;
                width: 25%;
                margin-left: 8px;
            }
            .estado {
                color: ' . $colorEstado . ';
                font-weight: bold;
            }
            .firmas-qr {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 40px;
            }
            .firmas-qr .firma {
                text-align: center;
                width: 30%;
                border-top: 1px solid #000;
                padding-top: 4px;
                font-size: 13px;
            }
            .firmas-qr .qr {
                width: 20%;
                text-align: center;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .firmas-qr .qr img {
                width: 120px;
                height: 120px;
            }
            .firmas-qr .qr p {
                font-size: 10px;
                margin: 5px 0 0 0;
                width: 100%;
                text-align: center;
                word-wrap: break-word;
            }
            .entregado {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-20deg);
                font-size: 40px;
                font-weight: bold;
                color: rgba(0, 0, 255, 0.2);
                padding: 10px 30px;
            }
            @media print {
                .no-print { display: none; }
            }
        </style>
        </head>
        <body>

        <div class="no-print" style="text-align:center;margin-bottom:10px;">
            <button onclick="window.print()" style="padding:8px 15px;background:#007bff;color:#fff;border:none;border-radius:4px;">Imprimir</button>
        </div>

        <div class="recibo">
            <div class="header">
                <div class="logo">
                    <img src="/dentista/vistas/src/img/logo7.5.png" alt="Logo Clínica">
                </div>
                <div class="titulo">CLÍNICA DENTAL DENTANI</div>
                <div class="datos-recibo">
                    <div><strong>N°:</strong> ' . str_pad($codPlan, 5, "0", STR_PAD_LEFT) . '</div>
                    <div><strong>Fecha:</strong> ' . date("d/m/Y") . '</div>
                </div>
            </div>

            <div style="text-align:center;font-weight:bold;margin-bottom:10px;">RECIBO DE TRATAMIENTO</div>

            <div class="campo"><strong>Recibí de:</strong> <span class="linea">' . $resultado["pacienteNombre"] . '</span></div>
            <div class="campo"><strong>La cantidad de:</strong> <span class="linea">' . number_format($resultado["monto"], 2) . ' Bs.</span></div>
            <div class="campo">
                <strong>En concepto de:</strong> 
                <span class="linea" style="white-space: normal; word-wrap: break-word; word-break: break-word;">
                    ' . htmlspecialchars($resultado["descripcion"]) . '
                </span>
            </div>
            <div class="campo"><strong>Saldo pendiente:</strong> <span class="linea-corta">' . number_format($totalTratamiento - $totalAbonado, 2) . ' Bs.</span></div>
            <div class="campo"><strong>Saldo total:</strong> <span class="linea-corta">' . number_format($totalTratamiento, 2) . ' Bs.</span></div>
            <div class="campo"><strong>Doctor:</strong> <span class="linea">' . $resultado["nombreDoctor"] . '</span></div>
            <div class="campo"><strong>Estado:</strong> <span class="estado">' . $estadoPago . '</span></div>
            <div class="campo"><strong>Tipo de pago:</strong> <span class="linea">' . $resultado["nombreTipoPago"] . '</span></div>

            ' . ($estadoPago === "PAGADO" ? '<div class="entregado">ENTREGADO</div>' : '') . '

            <div class="firmas-qr">
                <div class="firma">Recibí Conforme</div>
                <div class="qr">
                    <img src="data:image/png;base64,' . $qrImage . '" alt="Código QR">
                    <p>Escanéa para ver el recibo</p>
                </div>
                <div class="firma">Entregué Conforme</div>
            </div>
        </div>

        </body>
        </html>';
    } else {
        echo "<p style='color:red;'>Recibo no encontrado para el código proporcionado.</p>";
    }
} else {
    echo "<p style='color:red;'>Parámetro <code>codPlan</code> no proporcionado.</p>";
}
?>
