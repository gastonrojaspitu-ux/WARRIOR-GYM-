<?php
session_start();
include(__DIR__ . "/../php/conexion.php");

/* PROTEGER */
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

/* VENTAS */
$sql = "
SELECT 
    v.id_venta,
    v.fecha,
    COALESCE(u.nombre, 'Sin usuario') AS nombre,
    (
        SELECT COALESCE(SUM(d.cantidad * d.precio_unitario),0)
        FROM detalle_ventas d
        WHERE d.id_venta = v.id_venta
    ) AS total
FROM ventas v
LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario
ORDER BY v.id_venta DESC
";

$resultado = mysqli_query($conexion, $sql);

if (!$resultado) {
    die("ERROR SQL: " . mysqli_error($conexion));
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Ventas</title>
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

<h2>Ventas</h2>

<table border="1" width="100%">
<tr>
    <th>ID</th>
    <th>Cliente/Usuario</th>
    <th>Fecha</th>
    <th>Total</th>
    <th>Acciones</th>
</tr>

<?php while($fila = mysqli_fetch_assoc($resultado)) { ?>

<tr>
    <td><?= $fila['id_venta'] ?></td>
    <td><?= $fila['nombre'] ?></td>
    <td><?= $fila['fecha'] ?></td>
    <td>$<?= number_format($fila['total'],2) ?></td>
    <td>
        <a href="factura.php?id=<?= $fila['id_venta'] ?>">Ver Factura</a>
    </td>
</tr>

<?php } ?>

</table>

</body>
</html>