<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT ra.*,
               c.nombre AS nombre_cliente,
               c.apellido
        FROM rutina_asignada ra
        INNER JOIN clientes c ON ra.id_cliente = c.id_cliente
        ORDER BY ra.id_rutina_asignada DESC";

$resultado = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Rutinas Asignadas</title>

<style>
body{
    background:#111;
    color:white;
    font-family:Arial;
}

table{
    width:90%;
    margin:20px auto;
    border-collapse:collapse;
}

th, td{
    border:1px solid #444;
    padding:10px;
    text-align:center;
}

th{
    background:#d40000;
}

h2{
    text-align:center;
}
</style>
</head>

<body>

<h2>🏋 Rutinas Asignadas</h2>

<table>
<tr>
    <th>Cliente</th>
    <th>Rutina</th>
    <th>Fecha Inicio</th>
    <th>Fecha Fin</th>
</tr>

<?php while($fila = mysqli_fetch_assoc($resultado)) { ?>
<tr>
   <td><?= $fila['nombre_cliente'] . " " . $fila['apellido'] ?></td>
<td><?= $fila['nombre_rutina'] ?></td>
<td><?= $fila['descripcion'] ?></td>
<td><?= $fila['fecha_asignacion'] ?></td>
<td><?= $fila['estado'] ?></td>
</tr>
<?php } ?>

</table>

</body>
</html>