<?php
session_start();
include("../php/conexion.php");

/* 🔐 seguridad */
if (!isset($_SESSION['id_usuario'])) {
    header("Location: /warrior_gym/admin/login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

/* 🔥 ventas del usuario */
$sql = "
SELECT 
    v.id_venta,
    v.fecha,
    (
        SELECT SUM(d.cantidad * d.precio)
        FROM detalle_ventas d
        WHERE d.id_venta = v.id_venta
    ) AS total
FROM ventas v
WHERE v.id_usuario = '$id_usuario'
ORDER BY v.id_venta DESC
";

$resultado = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Compras</title>

<style>
body{
    font-family: Arial;
    background:#111;
    color:white;
    padding:20px;
}

.container{
    max-width:800px;
    margin:auto;
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
    margin-top:20px;
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
    padding:8px 12px;
    background:#444;
    color:white;
    text-decoration:none;
    border-radius:5px;
}

.boton:hover{
    background:#d40000;
}
</style>
</head>

<body>

<div class="container">

<h2>🛒 Mis Compras</h2>

<table>

<tr>
    <th>ID Venta</th>
    <th>Fecha</th>
    <th>Total</th>
    <th>Acción</th>
</tr>

<?php while($row = mysqli_fetch_assoc($resultado)) { ?>

<tr>
    <td><?php echo $row['id_venta']; ?></td>
    <td><?php echo $row['fecha']; ?></td>
    <td>$<?php echo number_format($row['total'], 2); ?></td>

    <td>
        <a class="boton" href="factura.php?id=<?php echo $row['id_venta']; ?>">
            Ver Factura
        </a>
    </td>
</tr>

<?php } ?>

</table>

</div>

</body>
</html>