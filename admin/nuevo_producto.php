<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    // 🔍 Verificar si el producto ya existe
    $sqlCheck = "SELECT * FROM productos WHERE nombre = '$nombre'";
    $resultado = mysqli_query($conexion, $sqlCheck);

    if (mysqli_num_rows($resultado) > 0) {

        // 🔼 Si existe → sumar stock
        $sql = "UPDATE productos 
                SET stock = stock + $stock 
                WHERE nombre = '$nombre'";

    } else {

        // ➕ Si no existe → insertar nuevo
        $sql = "INSERT INTO productos (nombre, descripcion, precio, stock)
                VALUES ('$nombre', '$descripcion', '$precio', '$stock')";
    }

    if (mysqli_query($conexion, $sql)) {
        header("Location: productos.php");
        exit();
    } else {
        echo "ERROR: " . mysqli_error($conexion);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nuevo Producto</title>

<style>
body{
    background:#111;
    color:white;
    font-family:Arial;
}

form{
    width:500px;
    margin:40px auto;
}

input, textarea{
    width:100%;
    padding:10px;
    margin-bottom:10px;
}

button{
    width:100%;
    padding:12px;
    background:#d40000;
    color:white;
    border:none;
    cursor:pointer;
}
</style>

</head>

<body>

<h1 align="center">Nuevo Producto</h1>

<form method="POST">

    <input type="text" name="nombre" placeholder="Nombre del producto" required>

    <textarea name="descripcion" placeholder="Descripción"></textarea>

    <input type="number" step="0.01" name="precio" placeholder="Precio" required>

    <input type="number" name="stock" placeholder="Stock" required>

    <button type="submit">Guardar Producto</button>

</form>

</body>
</html>