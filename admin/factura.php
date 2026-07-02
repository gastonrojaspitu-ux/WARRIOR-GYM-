<?php
session_start();
include(__DIR__ . "/../php/conexion.php");

/* PROTEGER ADMIN */
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ventas.php");
    exit();
}

$id_venta = intval($_GET['id']);

function formatoPrecio($precio) {
    return "$" . number_format($precio, 2, ",", ".");
}

function formatoFecha($fecha) {
    return date("d/m/Y H:i", strtotime($fecha));
}

/* DATOS DE LA VENTA */
$sqlVenta = "
SELECT 
    v.id_venta,
    v.fecha,
    v.total,
    COALESCE(
        NULLIF(TRIM(CONCAT(COALESCE(c.nombre, ''), ' ', COALESCE(c.apellido, ''))), ''),
        u.nombre,
        'Sin usuario'
    ) AS cliente,
    COALESCE(c.email, u.email, 'Sin email') AS email
FROM ventas v
LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario
LEFT JOIN clientes c ON c.id_usuario = u.id_usuario
WHERE v.id_venta = ?
LIMIT 1
";

$stmtVenta = mysqli_prepare($conexion, $sqlVenta);
mysqli_stmt_bind_param($stmtVenta, "i", $id_venta);
mysqli_stmt_execute($stmtVenta);
$resultVenta = mysqli_stmt_get_result($stmtVenta);

if (!$resultVenta || mysqli_num_rows($resultVenta) == 0) {
    echo "<script>
        alert('Factura no encontrada.');
        window.location='ventas.php';
    </script>";
    exit();
}

$venta = mysqli_fetch_assoc($resultVenta);

/* DETALLE DE LA VENTA */
$sqlDetalle = "
SELECT 
    d.cantidad,
    d.precio_unitario,
    p.nombre AS producto
FROM detalle_ventas d
INNER JOIN productos p ON d.id_producto = p.id_producto
WHERE d.id_venta = ?
";

$stmtDetalle = mysqli_prepare($conexion, $sqlDetalle);
mysqli_stmt_bind_param($stmtDetalle, "i", $id_venta);
mysqli_stmt_execute($stmtDetalle);
$resultDetalle = mysqli_stmt_get_result($stmtDetalle);

$total_calculado = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Factura #<?= $venta['id_venta'] ?> - Warrior Gym</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

<style>
body {
    background: #0f0f0f;
    color: white;
}

.factura-box {
    max-width: 950px;
    margin: 40px auto;
    background: #181818;
    border: 1px solid #252525;
    border-radius: 18px;
    padding: 30px;
    box-shadow: 0 0 25px rgba(220, 53, 69, .20);
}

.factura-title {
    color: #dc3545;
    font-weight: 800;
}

.info-box {
    background: #111;
    border: 1px solid #333;
    border-radius: 14px;
    padding: 18px;
}

.table-dark th {
    background: #dc3545;
    color: white;
}

.total-box {
    text-align: right;
    font-size: 1.5rem;
    font-weight: bold;
    color: #dc3545;
}

@media print {
    body {
        background: white;
        color: black;
    }

    .factura-box {
        box-shadow: none;
        border: none;
        margin: 0;
        max-width: 100%;
        color: black;
    }

    .no-print {
        display: none;
    }

    .table {
        color: black;
    }
}
</style>
</head>

<body>

<div class="factura-box">

    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">

        <div>
            <h2 class="factura-title mb-1">
                <i class="bi bi-receipt-cutoff"></i> Factura
            </h2>
            <p class="text-secondary mb-0">
                Warrior Gym - Tienda oficial
            </p>
        </div>

        <div class="text-end">
            <h4 class="text-danger mb-1">
                #<?= $venta['id_venta'] ?>
            </h4>
            <p class="text-secondary mb-0">
                <?= formatoFecha($venta['fecha']) ?>
            </p>
        </div>

    </div>

    <div class="row g-3 mb-4">

        <div class="col-md-6">
            <div class="info-box">
                <p class="text-secondary mb-1">Cliente</p>
                <h5><?= htmlspecialchars($venta['cliente']) ?></h5>
                <p class="mb-0"><?= htmlspecialchars($venta['email']) ?></p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="info-box">
                <p class="text-secondary mb-1">Datos de venta</p>
                <p class="mb-1"><strong>ID Venta:</strong> <?= $venta['id_venta'] ?></p>
                <p class="mb-0"><strong>Fecha:</strong> <?= formatoFecha($venta['fecha']) ?></p>
            </div>
        </div>

    </div>

    <div class="table-responsive">

        <table class="table table-dark table-hover text-center align-middle">

            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>

            <tbody>

            <?php if ($resultDetalle && mysqli_num_rows($resultDetalle) > 0): ?>

                <?php while($row = mysqli_fetch_assoc($resultDetalle)): ?>

                    <?php
                    $subtotal = $row['cantidad'] * $row['precio_unitario'];
                    $total_calculado += $subtotal;
                    ?>

                    <tr>
                        <td><?= htmlspecialchars($row['producto']) ?></td>
                        <td><?= $row['cantidad'] ?></td>
                        <td><?= formatoPrecio($row['precio_unitario']) ?></td>
                        <td><?= formatoPrecio($subtotal) ?></td>
                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="4" class="text-warning">
                        Esta venta no tiene productos cargados en el detalle.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

    <?php
    $total_final = $total_calculado > 0 ? $total_calculado : $venta['total'];
    ?>

    <div class="total-box mt-4">
        Total: <?= formatoPrecio($total_final) ?>
    </div>

    <div class="d-flex gap-2 justify-content-end mt-4 no-print">

        <a href="ventas.php" class="btn btn-outline-light">
            ← Volver a ventas
        </a>

        <button onclick="window.print()" class="btn btn-danger">
            <i class="bi bi-printer-fill"></i> Imprimir
        </button>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>