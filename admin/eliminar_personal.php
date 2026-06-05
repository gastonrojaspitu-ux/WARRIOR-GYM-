<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: personal.php");
    exit();
}

$id = $_GET['id'];

/* 1. Eliminar relación con cargo primero */
mysqli_query($conexion, "DELETE FROM personal_cargo WHERE id_personal = '$id'");

/* 2. Eliminar personal */
mysqli_query($conexion, "DELETE FROM personal WHERE id_personal = '$id'");

header("Location: personal.php");
exit();
?>