<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$id_venta = $_GET['id'];

/* CABECERA */
$sqlVenta = "SELECT v.*, c.nombre, c.apellido
             FROM ventas v
             INNER JOIN clientes c ON v.id_cliente = c.id_cliente
             WHERE v.id_venta = '$id_venta'";

$resVenta = mysqli_query($conexion, $sqlVenta);
$venta = mysqli_fetch_assoc($resVenta);

/* DETALLE */
$sqlDetalle = "SELECT d.*, p.nombre
               FROM detalle_ventas d
               INNER JOIN productos p ON d.id_producto = p.id_producto
               WHERE d.id_venta = '$id_venta'";

$resDetalle = mysqli_query($conexion, $sqlDetalle);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Factura</title>

<style>
body{
    font-family: Arial;
    background:#111;
    color:white;
}

.factura{
    width:600px;
    margin:30px auto;
    background:#222;
    padding:20px;
    border-radius:10px;
}

h2{
    text-align:center;
    color:#d40000;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
}

th, td{
    border:1px solid #444;
    padding:10px;
    text-align:center;
}

th{
    background:#d40000;
}

.boton{
    display:inline-block;
    background:#444;
    color:white;
    padding:10px 15px;
    text-decoration:none;
    border-radius:5px;
    margin-bottom:10px;
}

.total{
    text-align:right;
    margin-top:10px;
    font-size:18px;
}
</style>
</head>

<body>

<div class="factura">

<a class="boton" href="ventas.php">⬅ Volver</a>
<a class="boton" href="dashboard.php">🏠 Panel</a>

<h2>🧾 FACTURA DE VENTA</h2>

<p><b>Cliente:</b> <?php echo $venta['nombre']." ".$venta['apellido']; ?></p>
<p><b>Fecha:</b> <?php echo $venta['fecha']; ?></p>
<p><b>ID Venta:</b> #<?php echo $venta['id_venta']; ?></p>

<table>

<tr>
    <th>Producto</th>
    <th>Cantidad</th>
    <th>Precio Unitario</th>
    <th>Subtotal</th>
</tr>

<?php 
$total = 0;

while($d = mysqli_fetch_assoc($resDetalle)) { 

    /* 🔥 CORRECCIÓN CLAVE */
    $precio = $d['precio_unitario'];

    $subtotal = $d['cantidad'] * $precio;
    $total += $subtotal;
?>

<tr>
    <td><?php echo $d['nombre']; ?></td>
    <td><?php echo $d['cantidad']; ?></td>
    <td>$<?php echo $precio; ?></td>
    <td>$<?php echo $subtotal; ?></td>
</tr>

<?php } ?>

</table>

<div class="total">
    <b>Total: $<?php echo $total; ?></b>
</div>

</div>

</body>
</html>