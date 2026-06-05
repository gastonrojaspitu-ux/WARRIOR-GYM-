<?php
session_start();

include("../php/conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $documento = $_POST['documento'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    $sql = "INSERT INTO clientes
    (
        id_tipo_documento,
        numero_documento,
        nombre,
        apellido,
        telefono,
        email,
        fecha_registro,
        estado,
        usuario,
        contrasena
    )
    VALUES
    (
        1,
        '$documento',
        '$nombre',
        '$apellido',
        '$telefono',
        '$email',
        CURDATE(),
        'Activo',
        '$documento',
        '1234'
    )";

    mysqli_query($conexion, $sql);

    header("Location: clientes.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<title>Nuevo Cliente</title>

<style>

body{
    background:#111;
    color:white;
    font-family:Arial;
}

form{
    width:400px;
    margin:30px auto;
}

input{
    width:100%;
    padding:10px;
    margin-bottom:10px;
}

button{
    width:100%;
    padding:10px;
    background:#d40000;
    color:white;
    border:none;
}

</style>

</head>

<body>

<h1 align="center">Nuevo Cliente</h1>

<form method="POST">

    <input type="text" name="nombre" placeholder="Nombre" required>

    <input type="text" name="apellido" placeholder="Apellido" required>

    <input type="text" name="documento" placeholder="Documento" required>

    <input type="text" name="telefono" placeholder="Teléfono">

    <input type="email" name="email" placeholder="Email">

    <button type="submit">
        Guardar Cliente
    </button>

</form>

</body>
</html>