<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'cliente') {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['id_cliente']) || empty($_SESSION['id_cliente'])) {
    echo "<script>
        alert('Tu cuenta no está vinculada a un cliente. Consultá al administrador.');
        window.location='login.php';
    </script>";
    exit();
}

$nombre = $_SESSION['nombre'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard Usuario - Warrior Gym</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        background: #0f0f0f;
    }

    .card-custom {
        background: #141414;
        border: 1px solid #222;
        transition: 0.3s;
        height: 100%;
        border-radius: 15px;
    }

    .card-custom:hover {
        transform: translateY(-5px);
        border-color: #dc3545;
        box-shadow: 0 10px 25px rgba(220, 53, 69, 0.25);
    }

    .title {
        color: #dc3545;
        font-weight: bold;
    }
</style>

</head>

<body class="text-white">

<div class="container py-5">

    <!-- HEADER -->
    <div class="text-center mb-5">

        <h1 class="title">Warrior Gym</h1>

        <h3>Bienvenido, <?= htmlspecialchars($nombre) ?></h3>

        <p class="text-secondary">
            Elegí qué querés hacer dentro del sistema
        </p>

        <a href="/warrior_gym/logout.php" class="btn btn-danger">
            Cerrar sesión
        </a>

    </div>

    <!-- GRID -->
    <div class="row g-4 justify-content-center">

        <!-- RESERVAS -->
        <div class="col-md-4">
            <div class="card card-custom p-4 text-center">

                <h4>🏋 Reservar Turnos</h4>
                <p class="text-secondary">Elegí aparatos y horarios</p>

                <a href="clases_usuario.php" class="btn btn-danger w-100">
                    Ir a Reservas
                </a>

            </div>
        </div>

        <!-- MIS RESERVAS -->
        <div class="col-md-4">
            <div class="card card-custom p-4 text-center">

                <h4>📅 Mis Reservas</h4>
                <p class="text-secondary">Ver tus turnos activos</p>

                <a href="mis_reservas.php" class="btn btn-secondary w-100">
                    Ver Mis Reservas
                </a>

            </div>
        </div>

        <!-- TIENDA -->
        <div class="col-md-4">
            <div class="card card-custom p-4 text-center">

                <h4>🛒 Tienda</h4>
                <p class="text-secondary">Suplementos y productos</p>

                <a href="tienda.php" class="btn btn-warning w-100">
                    Ir a Tienda
                </a>

            </div>
        </div>

        <!-- MI RUTINA -->
        <div class="col-md-6">
            <div class="card card-custom p-4 text-center">

                <h4>💪 Mi Rutina</h4>
                <p class="text-secondary">
                    Ver tu plan de entrenamiento personalizado
                </p>

                <a href="mi_rutina.php" class="btn btn-success w-100">
                    Ver Rutina
                </a>

            </div>
        </div>

        <!-- MIS COMPRAS -->
        <div class="col-md-6">
            <div class="card card-custom p-4 text-center">

                <h4>🛍️ Mis Compras</h4>

                <p class="text-secondary">
                    Historial de compras realizadas en la tienda
                </p>

                <a href="clientes_mis_compras.php" class="btn btn-danger w-100">
                    Ver Mis Compras
                </a>

            </div>
        </div>

        <!-- SOLICITAR RUTINA -->
        <div class="col-md-6">
            <div class="card card-custom p-4 text-center">

                <h4>📋 Solicitar Rutina</h4>

                <p class="text-secondary">
                    Pedí una rutina personalizada al entrenador
                </p>

                <a href="solicitar_rutina.php" class="btn btn-warning w-100">
                    Solicitar
                </a>

            </div>
        </div>

        <!-- MEMBRESÍA -->
        <div class="col-md-6">
            <div class="card card-custom p-4 text-center">

                <h4>💳 Membresía</h4>

                <p class="text-secondary">
                    Solicitá o renová tu plan
                </p>

                <a href="solicitar_membresia.php" class="btn btn-primary w-100">
                    Gestionar
                </a>

                <a href="cliente_membresia.php" class="btn btn-danger w-100 mt-2">
                    Ver mi membresía
                </a>

            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>