<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// TRAER PRODUCTO
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT * FROM productos WHERE id_producto = $id";
    $resultado = mysqli_query($conexion, $sql);
    $producto = mysqli_fetch_assoc($resultado);
}

// GUARDAR CAMBIOS
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    $sql = "UPDATE productos SET 
            nombre = '$nombre',
            descripcion = '$descripcion',
            precio = '$precio',
            stock = '$stock'
            WHERE id_producto = $id";

    if (mysqli_query($conexion, $sql)) {
        header("Location: productos.php");
        exit();
    } else {
        echo "Error al actualizar: " . mysqli_error($conexion);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Producto</title>

<style>
body{
    background:#111;
    color:white;
    font-family:Arial;
}

.container{
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
    background:#f0ad4e;
    color:black;
    border:none;
    cursor:pointer;
}
</style>

</head>

<body>

<div class="container">

<h1 align="center">Editar Producto</h1>

<form method="POST">

    <input type="hidden" name="id" value="<?php echo $producto['id_producto']; ?>">

    <input type="text" name="nombre" value="<?php echo $producto['nombre']; ?>" required>

    <textarea name="descripcion"><?php echo $producto['descripcion']; ?></textarea>

    <input type="number" step="0.01" name="precio" value="<?php echo $producto['precio']; ?>" required>

    <input type="number" name="stock" value="<?php echo $producto['stock']; ?>" required>

    <button type="submit">Guardar Cambios</button>

</form>

</div>

</body>
</html>