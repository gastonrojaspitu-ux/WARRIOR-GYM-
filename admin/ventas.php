<?php
session_start();
include(__DIR__ . "/../php/conexion.php");

/* PROTEGER ADMIN */
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

function formatoPrecio($precio) {
    return "$" . number_format($precio, 2, ",", ".");
}

function formatoFecha($fecha) {
    return date("d/m/Y H:i", strtotime($fecha));
}

/* TRAER VENTAS */
$sql = "
SELECT 
    v.id_venta,
    v.fecha,
    COALESCE(
        NULLIF(TRIM(CONCAT(COALESCE(c.nombre, ''), ' ', COALESCE(c.apellido, ''))), ''),
        u.nombre,
        'Usuario eliminado'
    ) AS cliente,
    COALESCE(dt.total_detalle, v.total, 0) AS total
FROM ventas v
LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario
LEFT JOIN clientes c ON c.id_usuario = u.id_usuario
LEFT JOIN (
    SELECT 
        id_venta,
        SUM(cantidad * precio_unitario) AS total_detalle
    FROM detalle_ventas
    GROUP BY id_venta
) dt ON dt.id_venta = v.id_venta
ORDER BY v.id_venta DESC
";

$resultado = mysqli_query($conexion, $sql);

if (!$resultado) {
    die("ERROR SQL: " . mysqli_error($conexion));
}

$ventas = [];
$total_general = 0;

while ($fila = mysqli_fetch_assoc($resultado)) {
    $ventas[] = $fila;
    $total_general += $fila['total'];
}

$cantidad_ventas = count($ventas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Ventas - Admin Warrior Gym</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

<style>
body {
    background: #0f0f0f;
    color: white;
}

.page-title {
    color: #dc3545;
    font-weight: 800;
}

.stat-card {
    background: #181818;
    border: 1px solid #252525;
    border-radius: 18px;
    padding: 22px;
}

.stat-card h4 {
    color: #dc3545;
    font-weight: bold;
}

.table-box {
    background: #181818;
    border: 1px solid #252525;
    border-radius: 18px;
    padding: 25px;
    box-shadow: 0 0 20px rgba(220,53,69,.18);
}

.table-dark th {
    background: #dc3545;
    color: white;
}

.btn-factura {
    font-weight: 600;
}
</style>
</head>

<body>

<div class="container py-5">

    <div class="text-center mb-4">

        <h2 class="page-title">
            <i class="bi bi-receipt-cutoff"></i> Ventas
        </h2>

        <p class="text-secondary">
            Historial de compras realizadas en la tienda Warrior Gym.
        </p>

        <a href="dashboard.php" class="btn btn-outline-light btn-sm">
            ← Volver al dashboard
        </a>

    </div>

    <div class="row g-4 mb-4">

        <div class="col-md-6">
            <div class="stat-card text-center">
                <p class="text-secondary mb-1">Cantidad de ventas</p>
                <h4><?= $cantidad_ventas ?></h4>
            </div>
        </div>

        <div class="col-md-6">
            <div class="stat-card text-center">
                <p class="text-secondary mb-1">Total vendido</p>
                <h4><?= formatoPrecio($total_general) ?></h4>
            </div>
        </div>

    </div>

    <div class="table-box">

        <?php if ($cantidad_ventas > 0): ?>

            <div class="table-responsive">

                <table class="table table-dark table-hover text-center align-middle mb-0">

                    <thead>
                        <tr>
                            <th>ID Venta</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($ventas as $fila): ?>

                            <tr>
                                <td><?= $fila['id_venta'] ?></td>

                                <td><?= htmlspecialchars($fila['cliente']) ?></td>

                                <td><?= formatoFecha($fila['fecha']) ?></td>

                                <td class="text-success fw-bold">
                                    <?= formatoPrecio($fila['total']) ?>
                                </td>

                                <td>
                                    <a href="factura.php?id=<?= $fila['id_venta'] ?>" class="btn btn-danger btn-sm btn-factura">
                                        <i class="bi bi-file-earmark-text"></i>
                                        Ver factura
                                    </a>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="text-center py-4">
                <h4 class="text-danger">No hay ventas registradas</h4>
                <p class="text-secondary">
                    Cuando un cliente realice una compra, va a aparecer acá.
                </p>
            </div>

        <?php endif; ?>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>