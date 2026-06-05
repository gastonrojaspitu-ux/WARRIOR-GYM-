<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

/* VENTAS CON TOTAL CALCULADO DESDE DETALLE */
$sql = "SELECT v.id_venta, v.fecha, c.nombre, c.apellido,
               SUM(d.cantidad * d.precio_unitario) AS total
        FROM ventas v
        INNER JOIN clientes c ON v.id_cliente = c.id_cliente
        INNER JOIN detalle_ventas d ON v.id_venta = d.id_venta
        GROUP BY v.id_venta, v.fecha, c.nombre, c.apellido
        ORDER BY v.id_venta DESC";

$resultado = mysqli_query($conexion, $sql);

if (!$resultado) {
    die("Error SQL: " . mysqli_error($conexion));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ventas - Warrior Gym</title>

<style>
body{
    font-family: Arial;
    background:#111;
    color:white;
    margin:0;
}

h2{
    text-align:center;
    margin-top:20px;
}

table{
    width:100%;
    border-collapse:collapse;
    background:#222;
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
    margin:10px 5px;
}

.boton:hover{
    background:#d40000;
}
</style>
</head>

<body>

<h2>💰 Ventas</h2>

<div style="text-align:center;">
    <a class="boton" href="nueva_venta.php">➕ Nueva Venta</a>
    <a class="boton" href="dashboard.php">⬅ Volver al Panel</a>
</div>

<table>

<tr>
    <th>ID</th>
    <th>Cliente</th>
    <th>Fecha</th>
    <th>Total</th>
    <th>Acciones</th>
</tr>

<?php while($fila = mysqli_fetch_assoc($resultado)) { ?>

<tr>
    <td><?php echo $fila['id_venta']; ?></td>
    <td><?php echo $fila['nombre']." ".$fila['apellido']; ?></td>
    <td><?php echo $fila['fecha']; ?></td>

    <!-- TOTAL CORREGIDO -->
    <td>$<?php echo $fila['total'] ?? 0; ?></td>

    <td>
        <a class="boton" href="factura.php?id=<?php echo $fila['id_venta']; ?>">
            Ver Factura
        </a>
    </td>
</tr>

<?php } ?>

</table>

</body>
</html>