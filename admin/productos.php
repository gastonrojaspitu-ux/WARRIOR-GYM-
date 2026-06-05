<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM productos";
$resultado = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Productos - Warrior Gym</title>

<style>
body{
    font-family: Arial;
    background:#111;
    color:white;
    margin:0;
}

.header{
    background:#d40000;
    padding:20px;
    text-align:center;
}

.container{
    padding:20px;
}

a{
    color:white;
    text-decoration:none;
}

.btn{
    background:#d40000;
    padding:10px 15px;
    border-radius:5px;
    display:inline-block;
    margin-bottom:15px;
}

table{
    width:100%;
    border-collapse:collapse;
    background:#222;
}

th, td{
    padding:12px;
    border:1px solid #444;
    text-align:center;
}

th{
    background:#d40000;
}

.actions a{
    padding:5px 10px;
    border-radius:4px;
    margin:0 3px;
}

.edit{
    background:#f0ad4e;
}

.delete{
    background:#d9534f;
}
</style>

</head>

<body>

<div class="header">
    <h1>PRODUCTOS - WARRIOR GYM</h1>
</div>

<div class="container">

<a class="btn" href="nuevo_producto.php">+ Nuevo Producto</a>
<a class="btn" href="dashboard.php">⬅ Volver al Panel</a>
<table>

<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Descripción</th>
    <th>Precio</th>
    <th>Stock</th>
    <th>Acciones</th>
</tr>

<?php while($fila = mysqli_fetch_assoc($resultado)) { ?>

<tr>
    <td><?php echo $fila['id_producto']; ?></td>
    <td><?php echo $fila['nombre']; ?></td>
    <td><?php echo $fila['descripcion']; ?></td>
    <td>$<?php echo $fila['precio']; ?></td>
    <td><?php echo $fila['stock']; ?></td>

    <td class="actions">
        <a class="edit" href="editar_producto.php?id=<?php echo $fila['id_producto']; ?>">
Editar
</a>
        <a class="delete" href="eliminar_producto.php?id=<?php echo $fila['id_producto']; ?>"
   onclick="return confirm('¿Seguro que querés eliminar este producto?');">
Eliminar
</a>
<a href="stock.php?id=<?php echo $fila['id_producto']; ?>&accion=sumar"
   style="background:green;padding:5px;color:white;border-radius:4px;">
+ 
</a>

<a href="stock.php?id=<?php echo $fila['id_producto']; ?>&accion=restar"
   style="background:orange;padding:5px;color:white;border-radius:4px;">
- 
</a>

    </td>
</tr>

<?php } ?>

</table>

</div>

</body>
</html>