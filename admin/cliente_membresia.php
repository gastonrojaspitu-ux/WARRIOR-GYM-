<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT cm.*, c.nombre, c.apellido, m.nombre AS membresia
        FROM cliente_membresia cm
        INNER JOIN clientes c ON cm.id_cliente = c.id_cliente
        INNER JOIN membresias m ON cm.id_membresia = m.id_membresia";

$resultado = mysqli_query($conexion, $sql);

// 🔥 Si falla la consulta, muestra error real
if (!$resultado) {
    die("Error SQL: " . mysqli_error($conexion));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Clientes con Membresía</title>

<style>
body{
    font-family: Arial;
    background:#111;
    color:white;
    margin:0;
    padding:20px;
}

h2{
    margin-bottom:15px;
}

.boton{
    background:#d40000;
    padding:10px 15px;
    border-radius:5px;
    color:white;
    text-decoration:none;
    display:inline-block;
    margin-bottom:15px;
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

tr:hover{
    background:#2a2a2a;
}
.boton-volver{
    display:inline-block;
    margin:15px;
    background:#444;
    color:white;
    padding:10px 15px;
    border-radius:5px;
    text-decoration:none;
}

.boton-volver:hover{
    background:#d40000;
}
</style>
</head>

<body>

<h2>🏋 Clientes con Membresía</h2>
<a href="dashboard.php" class="boton-volver">
    ⬅ Volver al Panel
</a>
<a class="boton" href="nueva_cliente_membresia.php">+ Asignar Membresía</a>

<table>

<tr>
    <th>Cliente</th>
    <th>Membresía</th>
    <th>Inicio</th>
    <th>Fin</th>
    <th>Estado</th>
</tr>

<?php if (mysqli_num_rows($resultado) > 0) { ?>

    <?php while($fila = mysqli_fetch_assoc($resultado)) { ?>

    <tr>
        <td><?php echo $fila['nombre'] . " " . $fila['apellido']; ?></td>
        <td><?php echo $fila['membresia']; ?></td>
        <td><?php echo $fila['fecha_inicio']; ?></td>
        <td><?php echo $fila['fecha_fin']; ?></td>
        <td><?php echo $fila['estado']; ?></td>
    </tr>

    <?php } ?>

<?php } else { ?>

<tr>
    <td colspan="5">No hay membresías asignadas</td>
</tr>

<?php } ?>

</table>

</body>
</html>