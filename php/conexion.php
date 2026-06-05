<?php

$host = "localhost";
$usuario = "root";
$password = "";
$basedatos = "warrior_gym";

$conexion = mysqli_connect($host, $usuario, $password, $basedatos);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

?>
