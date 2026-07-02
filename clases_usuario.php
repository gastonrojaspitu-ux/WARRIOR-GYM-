<?php
session_start();
include(__DIR__ . "/php/conexion.php");

/* PROTEGER SOLO CLIENTE */
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'cliente') {
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

/* TRAER APARATOS */
$sql = "SELECT * FROM aparatos ORDER BY nombre";
$aparatos = mysqli_query($conexion, $sql);

if (!$aparatos) {
    die("Error al cargar aparatos: " . mysqli_error($conexion));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Aparatos Disponibles - Warrior Gym</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background:
        linear-gradient(rgba(0,0,0,.88), rgba(0,0,0,.95)),
        url('img/gym-bg.jpg');
    background-size: cover;
    background-position: center;
    min-height: 100vh;
    color: white;
}

.page-title {
    color: #dc3545;
    font-weight: 800;
}

.card-aparato {
    background: rgba(15, 15, 15, .96);
    border: 1px solid #252525;
    border-radius: 18px;
    transition: .3s;
    height: 100%;
}

.card-aparato:hover {
    border-color: #dc3545;
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(220,53,69,.22);
}
</style>
</head>

<body>

<div class="container py-5">

    <div class="text-center mb-5">

        <h2 class="page-title">
            🏋 Aparatos disponibles
        </h2>

        <p class="text-secondary">
            Elegí un aparato y reservá el día y horario que querés usarlo.
        </p>

        <a href="dashboard_usuario.php" class="btn btn-outline-light btn-sm">
            ← Volver al panel
        </a>

    </div>

    <?php if (mysqli_num_rows($aparatos) == 0): ?>

        <div class="text-center bg-black p-4 rounded">
            <h4 class="text-danger">No hay aparatos cargados</h4>
            <p class="text-secondary mb-0">
                Cuando el administrador cargue aparatos, van a aparecer acá.
            </p>
        </div>

    <?php else: ?>

        <div class="row g-4">

            <?php while($a = mysqli_fetch_assoc($aparatos)): ?>

                <div class="col-md-4">

                    <div class="card-aparato p-4 text-center">

                        <h4 class="text-danger">
                            <?= htmlspecialchars($a['nombre']) ?>
                        </h4>

                        <p class="text-secondary">
                            Disponible para reservar
                        </p>

                        <a 
                            href="reserva_usuario.php?id_aparato=<?= intval($a['id_aparato']) ?>"
                            class="btn btn-danger w-100">
                            Reservar
                        </a>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>