<?php

$conexion = mysqli_connect("localhost", "root", "", "warrior_gym");

if (!$conexion) {
    die("❌ NO está conectado: " . mysqli_connect_error());
}

echo "✔ SÍ está conectado a MySQL<br>";

// prueba real a la base
$resultado = mysqli_query($conexion, "SELECT DATABASE()");
$fila = mysqli_fetch_assoc($resultado);

echo "📌 Base activa: " . $fila['DATABASE()'];

?>