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

$id_usuario = intval($_SESSION['id_usuario']);

function formatoPrecio($precio) {
    return "$" . number_format($precio, 2, ",", ".");
}

function formatoFecha($fecha) {
    return date("d/m/Y H:i", strtotime($fecha));
}

/* TRAER COMPRAS DEL USUARIO */
$sqlVentas = "SELECT id_venta, fecha, total
              FROM ventas
              WHERE id_usuario = ?
              ORDER BY id_venta DESC";

$stmtVentas = mysqli_prepare($conexion, $sqlVentas);
mysqli_stmt_bind_param($stmtVentas, "i", $id_usuario);
mysqli_stmt_execute($stmtVentas);
$resultadoVentas = mysqli_stmt_get_result($stmtVentas);
?>

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Mis Compras - Warrior Gym</title>

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

.compra-card {
    background: rgba(20, 20, 20, .96);
    border: 1px solid #252525;
    border-radius: 18px;
    padding: 25px;
    margin-bottom: 25px;
    transition: .3s;
}

.compra-card:hover {
    border-color: #dc3545;
    box-shadow: 0 10px 25px rgba(220, 53, 69, .22);
    transform: translateY(-3px);
}

.page-title {
    color: #dc3545;
    font-weight: 800;
}

.total {
    color: #dc3545;
    font-size: 1.3rem;
    font-weight: bold;
}

.empty-box {
    background: rgba(20, 20, 20, .96);
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
            <i class="bi bi-bag-check-fill"></i> Mis Compras
        </h2>

        <p class="text-secondary">
            Historial de compras realizadas en la tienda Warrior Gym.
        </p>

        <a href="dashboard_usuario.php" class="btn btn-outline-light btn-sm">
            ← Volver al panel
        </a>

    </div>

    <?php if ($resultadoVentas && mysqli_num_rows($resultadoVentas) > 0): ?>

        <?php while ($venta = mysqli_fetch_assoc($resultadoVentas)): ?>

            <div class="compra-card">

                <div class="d-flex justify-content-between flex-wrap mb-3">

                    <div>
                        <h4 class="text-danger mb-1">
                            Compra #<?= $venta['id_venta'] ?>
                        </h4>

                        <p class="text-secondary mb-0">
                            <i class="bi bi-calendar-event"></i>
                            <?= formatoFecha($venta['fecha']) ?>
                        </p>
                    </div>

                    <div class="text-end">
                        <p class="mb-1 text-secondary">Total</p>
                        <div class="total">
                            <?= formatoPrecio($venta['total']) ?>
                        </div>
                    </div>

                </div>

                <?php
                $id_venta = intval($venta['id_venta']);

                $sqlDetalle = "SELECT 
                                    d.cantidad,
                                    d.precio_unitario,
                                    p.nombre AS producto
                               FROM detalle_ventas d
                               INNER JOIN productos p ON d.id_producto = p.id_producto
                               WHERE d.id_venta = ?";

                $stmtDetalle = mysqli_prepare($conexion, $sqlDetalle);
                mysqli_stmt_bind_param($stmtDetalle, "i", $id_venta);
                mysqli_stmt_execute($stmtDetalle);
                $resultadoDetalle = mysqli_stmt_get_result($stmtDetalle);
                ?>

                <?php if ($resultadoDetalle && mysqli_num_rows($resultadoDetalle) > 0): ?>

                    <div class="table-responsive">

                        <table class="table table-dark table-hover align-middle text-center mb-0">

                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio unitario</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php while ($detalle = mysqli_fetch_assoc($resultadoDetalle)): ?>

                                    <?php
                                    $subtotal = $detalle['cantidad'] * $detalle['precio_unitario'];
                                    ?>

                                    <tr>
                                        <td><?= htmlspecialchars($detalle['producto']) ?></td>
                                        <td><?= $detalle['cantidad'] ?></td>
                                        <td><?= formatoPrecio($detalle['precio_unitario']) ?></td>
                                        <td><?= formatoPrecio($subtotal) ?></td>
                                    </tr>

                                <?php endwhile; ?>

                            </tbody>

                        </table>

                    </div>

                <?php else: ?>

                    <div class="alert alert-warning mb-0">
                        Esta compra no tiene detalle cargado.
                    </div>

                <?php endif; ?>

            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <div class="empty-box text-center col-md-7 mx-auto">

            <h4 class="text-danger">
                Todavía no tenés compras
            </h4>

            <p class="text-secondary">
                Cuando compres productos en la tienda, van a aparecer acá.
            </p>

            <a href="tienda.php" class="btn btn-danger">
                Ir a la tienda
            </a>

        </div>

    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>