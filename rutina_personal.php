<?php
session_start();
include("php/conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['id_cliente'])) {
    die("Error: tu cuenta no está vinculada a un cliente.");
}

$id_cliente = $_SESSION['id_cliente'];

$sql = "SELECT *
        FROM rutina_asignada
        WHERE id_cliente='$id_cliente'
        ORDER BY fecha_asignacion DESC";

$resultado = mysqli_query($conexion, $sql);

if (!$resultado) {
    die(mysqli_error($conexion));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Rutinas</title>

<style>
body{
    background:#111;
    color:white;
    font-family:Arial;
}

h2{
    text-align:center;
}

table{
    width:90%;
    margin:30px auto;
    border-collapse:collapse;
}

th,td{
    border:1px solid #444;
    padding:12px;
    text-align:center;
}

th{
    background:#d40000;
}
</style>
</head>

<body>

<h2>🏋 Mis Rutinas</h2>

<table>

<tr>
    <th>Rutina</th>
    <th>Descripción</th>
    <th>Fecha</th>
    <th>Estado</th>
</tr>

<?php if(mysqli_num_rows($resultado)>0){ ?>

    <?php while($row=mysqli_fetch_assoc($resultado)){ ?>

    <tr>
        <td><?= $row['nombre_rutina'] ?></td>
        <td><?= $row['descripcion'] ?></td>
        <td><?= $row['fecha_asignacion'] ?></td>
        <td><?= $row['estado'] ?></td>
    </tr>

    <?php } ?>

<?php } else { ?>

<tr>
    <td colspan="4">No tenés rutinas asignadas.</td>
</tr>

<?php } ?>

</table>

</body>
</html>