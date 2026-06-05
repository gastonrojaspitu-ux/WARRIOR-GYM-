<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $sql = "DELETE FROM clientes WHERE id_cliente = $id";
    mysqli_query($conexion, $sql);
}

header("Location: clientes.php");
exit();
?>