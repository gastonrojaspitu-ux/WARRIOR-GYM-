<?php
session_start();

include("../php/conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM membresias";
$resultado = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<title>Membresías - Warrior Gym</title>

<style>

body{
    font-family: Arial, sans-serif;
    background:#111;
    color:white;
    margin:0;
}

.header{
    background:#d40000;
    padding:20px;
    text-align:center;
}

.contenido{
    padding:30px;
}

table{
    width:100%;
    border-collapse:collapse;
    background:#222;
}

th, td{
    border:1px solid #444;
    padding:12px;
    text-align:center;
}

th{
    background:#d40000;
}

.boton{
    background:#d40000;
    color:white;
    padding:10px 15px;
    border-radius:5px;
    text-decoration:none;
}

</style>

</head>

<body>

<div class="header">
    <h1>MEMBRESÍAS - WARRIOR GYM</h1>
</div>

<div class="contenido">

    <p>
        <a class="boton" href="dashboard.php">
            Volver al Panel
        </a>
    </p>

    <table>

        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Descripción</th>
        </tr>

        <?php while($fila = mysqli_fetch_assoc($resultado)) { ?>

        <tr>
            <td><?php echo $fila['id_membresia']; ?></td>
            <td><?php echo $fila['nombre']; ?></td>
            <td>$<?php echo $fila['precio']; ?></td>
            <td><?php echo $fila['descripcion']; ?></td>
        </tr>

        <?php } ?>

    </table>

</div>

</body>
</html>