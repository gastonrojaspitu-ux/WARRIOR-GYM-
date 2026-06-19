<?php
session_start();
include(__DIR__ . "/../php/conexion.php");

$id = $_GET['id'];

/* VENTA */
$sql = "SELECT 
            v.id_venta,
            v.fecha,
            COALESCE(u.nombre,'Sin usuario') AS nombre,
            d.cantidad,
            d.precio_unitario,
            p.nombre AS producto
        FROM ventas v
        JOIN usuarios u ON v.id_usuario = u.id_usuario
        JOIN detalle_ventas d ON v.id_venta = d.id_venta
        JOIN productos p ON d.id_producto = p.id_producto
        WHERE v.id_venta = $id";

$result = mysqli_query($conexion, $sql);

if (!$result) {
    die("ERROR SQL: " . mysqli_error($conexion));
}

$total = 0;
$rowInfo = mysqli_fetch_assoc($result);
mysqli_data_seek($result, 0);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Factura</title>
<style>
body{
    font-family: Arial;
    background: #111;
    color: white;
    margin: 0;
    padding: 20px;
}

h2{
    text-align: center;
    color: #d40000;
}

table{
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: #1c1c1c;
    border-radius: 10px;
    overflow: hidden;
}

th{
    background: #d40000;
    padding: 12px;
    text-transform: uppercase;
}

td{
    padding: 10px;
    border-bottom: 1px solid #333;
    text-align: center;
}

tr:hover{
    background: #2a2a2a;
}

a{
    color: #00ff99;
    text-decoration: none;
    font-weight: bold;
}

a:hover{
    color: #00ffaa;
}
</style>
</head>

<body>

<h2>Factura</h2>

<p><b>Venta ID:</b> <?= $rowInfo['id_venta'] ?></p>
<p><b>Cliente:</b> <?= $rowInfo['nombre'] ?></p>
<p><b>Fecha:</b> <?= $rowInfo['fecha'] ?></p>

<table border="1" width="100%">
<tr>
    <th>Producto</th>
    <th>Cantidad</th>
    <th>Precio</th>
    <th>Subtotal</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) {

$subtotal = $row['cantidad'] * $row['precio_unitario'];
$total += $subtotal;

?>

<tr>
    <td><?= $row['producto'] ?></td>
    <td><?= $row['cantidad'] ?></td>
    <td>$<?= number_format($row['precio_unitario'],2) ?></td>
    <td>$<?= number_format($subtotal,2) ?></td>
</tr>

<?php } ?>

</table>

<h3>Total: $<?= number_format($total,2) ?></h3>

</body>
</html>