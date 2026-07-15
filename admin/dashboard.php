<?php
session_start();

include(__DIR__ . "/../php/conexion.php");

/* PROTEGER PANEL ADMIN */
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

/* FUNCIÓN PARA CONTAR SIN ROMPER */
function contar($conexion, $tabla) {
    $tabla = mysqli_real_escape_string($conexion, $tabla);

    $verificar = mysqli_query($conexion, "SHOW TABLES LIKE '$tabla'");

    if (!$verificar || mysqli_num_rows($verificar) == 0) {
        return 0;
    }

    $resultado = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM `$tabla`");

    if (!$resultado) {
        return 0;
    }

    $fila = mysqli_fetch_assoc($resultado);

    return $fila['total'] ?? 0;
}

/* CONTADORES */
$clientes = contar($conexion, "clientes");
$productos = contar($conexion, "productos");
$membresias = contar($conexion, "membresias");
$ventas = contar($conexion, "ventas");
$reservas = contar($conexion, "reservas");
$personal = contar($conexion, "personal");

$nombre_admin = $_SESSION['nombre'] ?? 'Administrador';
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Panel Admin - Warrior Gym</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    font-family: Arial, sans-serif;
    background: #111;
    color: white;
    margin: 0;
}

.header {
    background: #d40000;
    padding: 25px;
    text-align: center;
}

.header h1 {
    margin: 0;
    font-weight: bold;
}

.contenido {
    padding: 30px;
}

.bienvenida {
    margin-bottom: 30px;
}

.seccion {
    margin-top: 30px;
    margin-bottom: 15px;
    color: #dc3545;
    font-weight: bold;
}

.tarjeta {
    background: #222;
    padding: 20px;
    margin-bottom: 15px;
    border-radius: 12px;
    border: 1px solid #333;
    transition: 0.3s;
}

.tarjeta:hover {
    background: #2b2b2b;
    transform: translateY(-3px);
    border-color: #dc3545;
}

a {
    color: white;
    text-decoration: none;
}

a:hover {
    color: white;
}

.logout-box {
    padding: 30px;
    text-align: center;
}
</style>

</head>

<body>

<div class="header">
    <h1>WARRIOR GYM - PANEL ADMINISTRADOR</h1>
</div>

<div class="contenido">

    <div class="bienvenida">
        <h2>Bienvenido <?= htmlspecialchars($nombre_admin) ?></h2>
        <p class="text-secondary mb-0">
            Administrá clientes, membresías, rutinas, tienda, ventas y reservas.
        </p>
    </div>

    <!-- USUARIOS -->
    <h2 class="seccion">👤 Usuarios</h2>

    <a href="clientes.php">
        <div class="tarjeta">
            👥 Clientes: <?= $clientes ?>
        </div>
    </a>

    <a href="personal.php">
        <div class="tarjeta">
            👨‍💼 Personal: <?= $personal ?>
        </div>
    </a>

    <!-- GIMNASIO -->
    <h2 class="seccion">🏋 Gimnasio</h2>

    <a href="membresias.php">
        <div class="tarjeta">
            🏋 Planes de Membresía: <?= $membresias ?>
        </div>
    </a>

    <a href="solicitudes_membresia.php">
        <div class="tarjeta">
            📩 Solicitudes de Membresía
        </div>
    </a>

    <a href="pagos.php">
        <div class="tarjeta">
            💳 Membresías de Clientes / Pagos
        </div>
    </a>

    <a href="rutinas.php">
        <div class="tarjeta">
            🏋 Gestión de Rutinas y Solicitudes
        </div>
    </a>

    <!-- TIENDA -->
    <h2 class="seccion">📦 Tienda</h2>

    <a href="productos.php">
        <div class="tarjeta">
            📦 Productos: <?= $productos ?>
        </div>
    </a>

    <!-- NEGOCIO -->
    <h2 class="seccion">💰 Negocio</h2>

    <a href="ventas.php">
        <div class="tarjeta">
            💰 Ventas: <?= $ventas ?>
        </div>
    </a>

    <a href="reservas.php">
        <div class="tarjeta">
            📅 Reservas: <?= $reservas ?>
        </div>
    </a>
    
    <a href="contacto_web.php">
    <div class="tarjeta">
        📩 Mensajes de Contacto
    </div>

    <!-- SISTEMA -->
  <h2 class="seccion">🗄 Sistema</h2>

   <a href="base_datos.php">
    <div class="tarjeta">
        🗄 Base de Datos / Tablas del Sistema
    </div>
</a>

</div>

<div class="logout-box">
    <a href="/warrior_gym/logout.php" class="btn btn-danger">
        🚪 Cerrar sesión
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>