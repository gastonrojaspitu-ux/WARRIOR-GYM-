<?php
session_start();
include("../php/conexion.php");
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
$clientes = mysqli_fetch_row(
    mysqli_query($conexion, "SELECT COUNT(*) FROM clientes")
)[0];

$productos = mysqli_fetch_row(
    mysqli_query($conexion, "SELECT COUNT(*) FROM productos")
)[0];

$membresias = mysqli_fetch_row(
    mysqli_query($conexion, "SELECT COUNT(*) FROM membresias")
)[0];

$ventas = mysqli_fetch_row(
    mysqli_query($conexion, "SELECT COUNT(*) FROM ventas")
)[0];

$reservas = mysqli_fetch_row(
    mysqli_query($conexion, "SELECT COUNT(*) FROM reservas")
)[0];

$personal = mysqli_fetch_row(
    mysqli_query($conexion, "SELECT COUNT(*) FROM personal")
)[0];
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel Admin - Warrior Gym</title>

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

.tarjeta{
    background:#222;
    padding:20px;
    margin-bottom:15px;
    border-radius:10px;
}

a{
    color:white;
    text-decoration:none;
}

.logout{
    background:#d40000;
    padding:10px 15px;
    border-radius:5px;
}

</style>

</head>

<body>

<div class="header">
    <h1>WARRIOR GYM - PANEL ADMINISTRADOR</h1>
</div>

<div class="contenido">

    <h2>Bienvenido <?php echo $_SESSION['usuario']; ?></h2>

  <!-- ===== USUARIOS ===== -->
<h2>👤 Usuarios</h2>

<a href="clientes.php" style="text-decoration:none; color:white;">
    <div class="tarjeta">👥 Clientes: <?php echo $clientes; ?></div>
</a>

<a href="personal.php" style="text-decoration:none; color:white;">
    <div class="tarjeta">👨‍💼 Personal: <?php echo $personal; ?></div>
</a>


<!-- ===== GIMNASIO ===== -->
<h2>🏋 Gimnasio</h2>

<a href="membresias.php" style="text-decoration:none; color:white;">
    <div class="tarjeta">🏋 Planes de Membresía</div>
</a>

<a href="cliente_membresia.php" style="text-decoration:none; color:white;">
    <div class="tarjeta">👥 Membresías de Clientes</div>
</a>

<a href="nueva_cliente_membresia.php" style="text-decoration:none; color:white;">
    <div class="tarjeta">➕ Asignar Membresía</div>
</a>


<!-- ===== TIENDA ===== -->
<h2>📦 Tienda</h2>

<a href="productos.php" style="text-decoration:none; color:white;">
    <div class="tarjeta">📦 Productos: <?php echo $productos; ?></div>
</a>


<!-- ===== NEGOCIO ===== -->
<h2>💰 Negocio</h2>

<a href="ventas.php" style="text-decoration:none; color:white;">
    <div class="tarjeta">💰 Ventas: <?php echo $ventas; ?></div>
</a>

<a href="reservas.php" style="text-decoration:none; color:white;">
    <div class="tarjeta">📅 Reservas: <?php echo $reservas; ?></div>
</a>
</div>

<a class="logout" href="logout.php">
    Cerrar sesión
</a>

</body>
</html>