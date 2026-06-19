<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: /warrior_gym/admin/login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['accion'])) {

    $id = $_GET['id'];
    $accion = $_GET['accion'];

    $sql = "SELECT stock FROM productos WHERE id_producto = $id";
    $res = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($res);

    $stock = $fila['stock'];

    if ($accion == "sumar") {
        $stock++;
    }

    if ($accion == "restar") {
        $stock--;

        if ($stock < 0) {
            $stock = 0;
        }
    }

    if ($accion == "sumar5") {
        $stock += 5;
    }

    if ($accion == "restar5") {
        $stock -= 5;

        if ($stock < 0) {
            $stock = 0;
        }
    }

    $sqlUpdate = "UPDATE productos SET stock = $stock WHERE id_producto = $id";
    mysqli_query($conexion, $sqlUpdate);
}

header("Location: productos.php");
exit();
?>