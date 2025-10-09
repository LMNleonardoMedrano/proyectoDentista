<?php
include('../modelo/conexion.php');


$planta_id = $_POST['planta_id'];
$frecuencia_riego = $_POST['frecuencia_riego'];
$tipo_abono = $_POST['tipo_abono'];


$query = "INSERT INTO Cuidados (planta_id, frecuencia_riego, tipo_abono) VALUES ('$planta_id', '$frecuencia_riego', '$tipo_abono')";

$res = $conexion->query($query);

if ($res) {
    header("Location: ../vistas/ListaCuidado.php");
} else {
    echo '<script language="javascript">alert("No se guardaron los datos");</script>';
}

$conexion->close();
?>
