<?php
session_start();

if (!isset($_SESSION['id_personal']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'entrenador') {
    header("Location: login.php");
    exit();
}

$nombre = $_SESSION['nombre'] ?? 'Entrenador';
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Panel Entrenador - Warrior Gym</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #111;
    color: white;
    font-family: Arial, sans-serif;
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
</style>
</head>

<body>

<div class="header">
    <h1>🏋 WARRIOR GYM - PANEL ENTRENADOR</h1>
</div>

<div class="contenido">

    <h2>Bienvenido <?= htmlspecialchars($nombre) ?></h2>

    <p class="text-secondary">
        Desde este panel podés cargar rutinas del gimnasio.
    </p>

    <a href="entrenador_rutinas.php">
        <div class="tarjeta">
            🏋 Cargar mis rutinas
        </div>
    </a>

</div>

<div class="text-center mb-4">
    <a href="logout.php" class="btn btn-danger">
        🚪 Cerrar sesión
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>