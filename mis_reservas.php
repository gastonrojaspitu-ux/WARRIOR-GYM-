<?php
session_start();
include(__DIR__ . "/php/conexion.php");

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['id_cliente'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'cliente') {
    header("Location: login.php");
    exit();
}

$id_cliente = intval($_SESSION['id_cliente']);

function formatoHora($hora) {
    return date("h:i A", strtotime($hora));
}

function formatoFecha($fecha) {
    return date("d/m/Y", strtotime($fecha));
}

$sql = "SELECT 
            r.id_reserva,
            r.id_cliente,
            r.id_aparato,
            r.fecha,
            r.hora_inicio,
            r.hora_fin,
            r.estado_reserva,
            a.nombre AS aparato
        FROM reservas r
        INNER JOIN aparatos a 
            ON r.id_aparato = a.id_aparato
        WHERE r.id_cliente = ?
        ORDER BY r.fecha DESC, r.hora_inicio DESC";

$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_cliente);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Mis Reservas - Warrior Gym</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

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

.reserva-card {
    background: rgba(15, 15, 15, .96);
    border: 1px solid #222;
    border-radius: 18px;
    transition: .3s;
}

.reserva-card:hover {
    border-color: #dc3545;
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(220, 53, 69, .22);
}

.estado-activa {
    color: #2ecc71;
    font-weight: bold;
}

.estado-pendiente {
    color: #ffc107;
    font-weight: bold;
}

.estado-cancelada {
    color: #ff4d4d;
    font-weight: bold;
}

.estado-finalizada {
    color: #0dcaf0;
    font-weight: bold;
}

.estado-otro {
    color: orange;
    font-weight: bold;
}

.empty-box {
    background: rgba(15, 15, 15, .96);
    border: 1px solid #333;
    border-radius: 18px;
    padding: 35px;
}
</style>
</head>

<body>

<div class="container py-5">

    <div class="text-center mb-5">

        <h2 class="page-title">
            <i class="bi bi-calendar-check-fill"></i> Mis Reservas
        </h2>

        <p class="text-secondary">
            Acá podés ver tus turnos activos, pendientes y reservas anteriores.
        </p>

        <a href="dashboard_usuario.php" class="btn btn-outline-light btn-sm">
            ← Volver al panel
        </a>

    </div>

    <?php if (mysqli_num_rows($res) == 0): ?>

        <div class="empty-box text-center col-md-7 mx-auto">
            <h4 class="text-danger">Todavía no tenés reservas</h4>

            <p class="text-secondary">
                Cuando reserves un aparato, va a aparecer acá.
            </p>

            <a href="clases_usuario.php" class="btn btn-danger">
                Reservar ahora
            </a>
        </div>

    <?php else: ?>

        <div class="row g-4">

            <?php while($r = mysqli_fetch_assoc($res)): ?>

                <div class="col-md-6 col-lg-4">

                    <div class="reserva-card p-4 h-100">

                        <h4 class="text-danger mb-3">
                            <i class="bi bi-lightning-charge-fill"></i>
                            <?= htmlspecialchars($r['aparato']) ?>
                        </h4>

                        <p class="mb-2">
                            <i class="bi bi-calendar-event"></i>
                            <strong>Fecha:</strong>
                            <?= formatoFecha($r['fecha']) ?>
                        </p>

                        <p class="mb-2">
                            <i class="bi bi-clock"></i>
                            <strong>Horario:</strong>
                            <?= formatoHora($r['hora_inicio']) ?> - <?= formatoHora($r['hora_fin']) ?>
                        </p>

                        <p class="mb-3">
                            <i class="bi bi-info-circle"></i>
                            <strong>Estado:</strong>

                            <?php if($r['estado_reserva'] == 'Activa'): ?>

                                <span class="estado-activa">Activa</span>

                            <?php elseif($r['estado_reserva'] == 'Pendiente'): ?>

                                <span class="estado-pendiente">Pendiente</span>

                            <?php elseif($r['estado_reserva'] == 'Cancelada'): ?>

                                <span class="estado-cancelada">Cancelada</span>

                            <?php elseif($r['estado_reserva'] == 'Finalizada'): ?>

                                <span class="estado-finalizada">Finalizada</span>

                            <?php else: ?>

                                <span class="estado-otro">
                                    <?= htmlspecialchars($r['estado_reserva']) ?>
                                </span>

                            <?php endif; ?>
                        </p>

                        <?php if($r['estado_reserva'] == 'Activa'): ?>

                            <a 
                                href="cancelar_reserva.php?id=<?= $r['id_reserva'] ?>" 
                                class="btn btn-danger w-100"
                                onclick="return confirm('¿Seguro que querés cancelar esta reserva?');">
                                <i class="bi bi-x-circle-fill"></i>
                                Cancelar reserva
                            </a>

                        <?php elseif($r['estado_reserva'] == 'Pendiente'): ?>

                            <button class="btn btn-warning w-100" disabled>
                                <i class="bi bi-hourglass-split"></i>
                                Esperando aprobación
                            </button>

                        <?php elseif($r['estado_reserva'] == 'Cancelada'): ?>

                            <button class="btn btn-secondary w-100" disabled>
                                <i class="bi bi-x-circle"></i>
                                Reserva cancelada
                            </button>

                        <?php elseif($r['estado_reserva'] == 'Finalizada'): ?>

                            <button class="btn btn-info w-100" disabled>
                                <i class="bi bi-check-circle"></i>
                                Reserva finalizada
                            </button>

                        <?php else: ?>

                            <button class="btn btn-secondary w-100" disabled>
                                Estado no disponible
                            </button>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>