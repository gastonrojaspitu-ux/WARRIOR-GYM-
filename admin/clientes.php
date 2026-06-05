<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM clientes";
$resultado = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<title>Clientes - Warrior Gym</title>

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
    padding:10px;
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
    <h1>CLIENTES - WARRIOR GYM</h1>
</div>

<div class="contenido">

    <p>
        <a class="boton" href="dashboard.php">Volver al Panel</a>
        <a class="boton" href="nuevo_cliente.php">+ Nuevo Cliente</a>
    </p>

    <table>

        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Documento</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>

        <?php while($fila = mysqli_fetch_assoc($resultado)) { ?>

        <tr>
            <td><?php echo $fila['id_cliente']; ?></td>
            <td><?php echo $fila['nombre']; ?></td>
            <td><?php echo $fila['apellido']; ?></td>
            <td><?php echo $fila['numero_documento']; ?></td>
            <td><?php echo $fila['telefono']; ?></td>
            <td><?php echo $fila['email']; ?></td>
            <td><?php echo $fila['estado']; ?></td>

            <td>
                <a class="boton" href="editar_cliente.php?id=<?php echo $fila['id_cliente']; ?>">
                    Editar
                </a>

                <a class="boton" href="eliminar_cliente.php?id=<?php echo $fila['id_cliente']; ?>"
                   onclick="return confirm('¿Eliminar cliente?');">
                    Eliminar
                </a>
            </td>
        </tr>

        <?php } ?>

    </table>

</div>

</body>
</html>