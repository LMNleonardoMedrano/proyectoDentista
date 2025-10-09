<?php
include('../modelo/conexion.php');


$id = $_REQUEST['ide'];
$planta_id = $_POST['planta_id'];
$frecuencia_riego = $_POST['frecuencia_riego'];
$tipo_abono = $_POST['tipo_abono'];


$query = "UPDATE Cuidados SET planta_id='$planta_id', frecuencia_riego='$frecuencia_riego', tipo_abono='$tipo_abono' WHERE id='$id'";

$res = $conexion->query($query);

if ($res) {
    header("Location: ../vistas/ListaCuidado.php");
} else {
    echo 'No se actualizaron los datos';
}

$conexion->close();
?>
