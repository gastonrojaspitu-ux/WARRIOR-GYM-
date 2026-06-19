<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT rc.*, 
               c.nombre AS nombre_cliente, c.apellido,
               r.nombre_rutina
        FROM rutinas_cliente rc
        INNER JOIN clientes c ON rc.id_cliente = c.id_cliente
        INNER JOIN rutinas r ON rc.id_rutina = r.id_rutina
        ORDER BY rc.id_rutina_cliente DESC";

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
    <td><?= $fila['fecha_inicio'] ?></td>
    <td><?= $fila['fecha_fin'] ?></td>
</tr>
<?php } ?>

</table>

</body>
</html>