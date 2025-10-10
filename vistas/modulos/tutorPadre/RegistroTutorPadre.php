<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de Cuidado</title>
</head>
<body>
    <?php 
    include("enlaces.php");
    include("menu.php");
    ?>
     
    
    <div class="container mt-5">
        <h1>Registro de Cuidado</h1>
        <form action="../controlador/crtCuidado.php" method="POST">
            <div class="form-group">
                <label for="planta_id">ID de Planta:</label>
                <select class="form-control" id="planta_id" name="planta_id" required>
                    <?php
                    include('../modelo/conexion.php');
                    $sql = "SELECT id, nombre FROM Plantas";
                    $result = $conexion->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row["id"] . '">' . $row["nombre"] . '</option>';
                    }
                    $conexion->close();
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="frecuencia_riego">Frecuencia de Riego:</label>
                <input type="text" class="form-control" id="frecuencia_riego" name="frecuencia_riego" required>
            </div>
            <div class="form-group">
                <label for="tipo_abono">Tipo de Abono:</label>
                <input type="text" class="form-control" id="tipo_abono" name="tipo_abono" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="../vistas/ListaCuidado.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script src="../path/to/jquery.min.js"></script>
    <script src="../path/to/bootstrap.bundle.min.js"></script>
</body>
</html>
