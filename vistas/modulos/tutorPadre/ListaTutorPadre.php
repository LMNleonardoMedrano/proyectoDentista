<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Lista de Cuidado</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php 
    include("enlaces.php");
    include("menu.php");
    ?>
    <div class="container mt-5">
        <h1>Lista de Cuidados</h1>
        <a href="../vistas/RegistroCuidado.php" class="btn btn-primary mb-3">Registrar Nuevo Cuidado</a>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">ID de Planta</th>
                    <th scope="col">Frecuencia de Riego</th>
                    <th scope="col">Tipo de Abono</th>
                    <th scope="col">Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include('../modelo/conexion.php');
                $query = "SELECT * FROM Cuidados";
                $result = $conexion->query($query);
                
                while ($row = $result->fetch_assoc()) {
                    $planta_id = $row['planta_id'];
                    
                   
                    $planta_query = "SELECT nombre FROM Plantas WHERE id='$planta_id'";
                    $planta_result = $conexion->query($planta_query);
                    $planta_row = $planta_result->fetch_assoc();
                    $planta_nombre = $planta_row['nombre'];
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $planta_nombre;  ?></td>
                    <td><?php echo $row['frecuencia_riego']; ?></td>
                    <td><?php echo $row['tipo_abono']; ?></td>
                    <td>
                        <a href="../vistas/ActualizarCuidado.php?ide=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm" data-toggle="tooltip" title="Modificar">
                            <span class="fas fa-pencil-alt"></span> Modificar
                        </a>
                        <a href="../controlador/EliminarCuidado.php?ide=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Eliminar">
                            <span class="fas fa-trash"></span> Eliminar
                        </a>
                    </td>
                </tr>
                <?php
                }
                $conexion->close();
                ?>
            </tbody>
        </table>
    </div>

    <!-- Scripts de jQuery y Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.min.js"></script>
</body>
</html>
