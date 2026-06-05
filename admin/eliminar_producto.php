<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $sql = "DELETE FROM productos WHERE id_producto = $id";

    if (mysqli_query($conexion, $sql)) {
        header("Location: productos.php");
        exit();
    } else {
        echo "Error al eliminar: " . mysqli_error($conexion);
    }

} else {
    header("Location: productos.php");
    exit();
}
?>