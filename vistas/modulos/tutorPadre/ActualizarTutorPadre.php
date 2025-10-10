<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Actualizar Cuidado</title>
</head>
<body>
    <?php 
    include("enlaces.php");
    include("menu.php");
    ?>
    <div class="container mt-5">
        <h1>Actualizar Cuidado</h1>
        <?php
        $id = $_REQUEST['ide'];
        include('../modelo/conexion.php');
        $query = "SELECT * FROM Cuidados WHERE id='$id'";
        $result = $conexion->query($query);
        $row = $result->fetch_assoc();
        ?>
        <form method="POST" action="../controlador/crtModificarCuidado.php?ide=<?php echo $row['id']; ?>">
            <div class="form-group">
                <label for="planta_id">ID de Planta:</label>
                <select class="form-control" id="planta_id" name="planta_id" required>
                    <?php
                    $sql = "SELECT id, nombre FROM Plantas";
                    $plantas = $conexion->query($sql);
                    while ($planta = $plantas->fetch_assoc()) {
                        $selected = ($planta["id"] == $row["planta_id"]) ? 'selected' : '';
                        echo '<option value="' . $planta["id"] . '" ' . $selected . '>' . $planta["nombre"] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="frecuencia_riego">Frecuencia de Riego:</label>
                <input type="text" class="form-control" id="frecuencia_riego" name="frecuencia_riego" value="<?php echo $row['frecuencia_riego']; ?>" required>
            </div>
            <div class="form-group">
                <label for="tipo_abono">Tipo de Abono:</label>
                <input type="text" class="form-control" id="tipo_abono" name="tipo_abono" value="<?php echo $row['tipo_abono']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="../vistas/ListaCuidado.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script src="../path/to/jquery.min.js"></script>
    <script src="../path/to/bootstrap.bundle.min.js"></script>
</body>
</html>
