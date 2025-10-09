<?php
include('../modelo/conexion.php');


$id = $_REQUEST['ide'];


$query = "DELETE FROM Cuidados WHERE id='$id'";

$res = $conexion->query($query);

if ($res) {
    header("Location: ../vistas/ListaCuidado.php");
} else {
    echo 'No se pudo eliminar el cuidado';
}

$conexion->close();
?>
