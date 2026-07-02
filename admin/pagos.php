<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . "/../php/conexion.php");

/* PROTEGER ADMIN */
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$error = "";

/* CLIENTES */
$clientes = mysqli_query($conexion, "
    SELECT id_cliente, nombre, apellido 
    FROM clientes 
    ORDER BY apellido, nombre
");

/* MEMBRESÍAS */
$membresias = mysqli_query($conexion, "
    SELECT * 
    FROM membresias 
    ORDER BY id_membresia
");

if (!$clientes) {
    die("Error al cargar clientes: " . mysqli_error($conexion));
}

if (!$membresias) {
    die("Error al cargar membresías: " . mysqli_error($conexion));
}

/* GUARDAR PAGO */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_cliente = intval($_POST['id_cliente']);
    $id_membresia = intval($_POST['id_membresia']);
    $monto = floatval($_POST['monto']);
    $fecha_pago = $_POST['fecha_pago'];
    $metodo_pago = trim($_POST['metodo_pago']);
    $concepto = trim($_POST['concepto']);
    $estado = trim($_POST['estado']);

    if ($id_cliente <= 0 || $id_membresia <= 0 || $monto <= 0 || empty($fecha_pago) || empty($metodo_pago) || empty($concepto) || empty($estado)) {

        $error = "Completá todos los campos correctamente.";

    } else {

        mysqli_begin_transaction($conexion);

        try {

            /* 1. GUARDAR PAGO */
            $sqlPago = "INSERT INTO pagos 
                        (id_cliente, monto, fecha_pago, metodo_pago, concepto, estado)
                        VALUES (?, ?, ?, ?, ?, ?)";

            $stmtPago = mysqli_prepare($conexion, $sqlPago);
            mysqli_stmt_bind_param($stmtPago, "idssss", $id_cliente, $monto, $fecha_pago, $metodo_pago, $concepto, $estado);

            if (!mysqli_stmt_execute($stmtPago)) {
                throw new Exception("Error al registrar pago: " . mysqli_error($conexion));
            }

            /* 2. SI EL PAGO ESTÁ PAGADO, ACTIVAR MEMBRESÍA */
            if ($estado == "Pagado") {

                $fecha_inicio = date("Y-m-d");
                $fecha_fin = date("Y-m-d", strtotime("+30 days"));

                $check = "SELECT * 
                          FROM cliente_membresia 
                          WHERE id_cliente = ?
                          LIMIT 1";

                $stmtCheck = mysqli_prepare($conexion, $check);
                mysqli_stmt_bind_param($stmtCheck, "i", $id_cliente);
                mysqli_stmt_execute($stmtCheck);
                $resCheck = mysqli_stmt_get_result($stmtCheck);

                if ($resCheck && mysqli_num_rows($resCheck) > 0) {

                    /* ACTUALIZAR MEMBRESÍA EXISTENTE */
                    $update = "UPDATE cliente_membresia 
                               SET id_membresia = ?,
                                   estado = 'Activa',
                                   fecha_inicio = ?,
                                   fecha_fin = ?
                               WHERE id_cliente = ?";

                    $stmtUpdate = mysqli_prepare($conexion, $update);
                    mysqli_stmt_bind_param($stmtUpdate, "issi", $id_membresia, $fecha_inicio, $fecha_fin, $id_cliente);

                    if (!mysqli_stmt_execute($stmtUpdate)) {
                        throw new Exception("Error al actualizar membresía: " . mysqli_error($conexion));
                    }

                } else {

                    /* CREAR MEMBRESÍA NUEVA */
                    $insert = "INSERT INTO cliente_membresia 
                               (id_cliente, id_membresia, fecha_inicio, fecha_fin, estado)
                               VALUES (?, ?, ?, ?, 'Activa')";

                    $stmtInsert = mysqli_prepare($conexion, $insert);
                    mysqli_stmt_bind_param($stmtInsert, "iiss", $id_cliente, $id_membresia, $fecha_inicio, $fecha_fin);

                    if (!mysqli_stmt_execute($stmtInsert)) {
                        throw new Exception("Error al crear membresía: " . mysqli_error($conexion));
                    }
                }
            }

            mysqli_commit($conexion);

            echo "<script>
                alert('Pago registrado correctamente y membresía actualizada.');
                window.location='pagos.php';
            </script>";
            exit();

        } catch (Exception $e) {

            mysqli_rollback($conexion);
            $error = $e->getMessage();
        }
    }
}

/* LISTAR PAGOS */
$pagos = mysqli_query($conexion, "
    SELECT 
        p.*, 
        c.nombre, 
        c.apellido
    FROM pagos p
    INNER JOIN clientes c ON p.id_cliente = c.id_cliente
    ORDER BY p.id_pago DESC
");

if (!$pagos) {
    die("Error al listar pagos: " . mysqli_error($conexion));
}

function formatoPrecio($precio) {
    return "$" . number_format($precio, 2, ",", ".");
}

function formatoFecha($fecha) {
    return date("d/m/Y", strtotime($fecha));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Pagos - Admin Warrior Gym</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #111;
    color: white;
    font-family: Arial, sans-serif;
}

.page-title {
    color: #dc3545;
    font-weight: bold;
}

.form-box {
    max-width: 650px;
    margin: 20px auto;
    background: #1c1c1c;
    padding: 25px;
    border-radius: 15px;
    border: 1px solid #333;
    box-shadow: 0 0 20px rgba(220,53,69,0.20);
}

.form-control,
.form-select {
    background: #111;
    border: 1px solid #333;
    color: white;
}

.form-control:focus,
.form-select:focus {
    background: #111;
    color: white;
    border-color: #dc3545;
    box-shadow: none;
}

.table-box {
    width: 95%;
    margin: 30px auto;
    background: #1c1c1c;
    padding: 20px;
    border-radius: 15px;
    border: 1px solid #333;
}

.table-dark th {
    background: #dc3545;
    color: white;
}

.estado-pagado {
    color: #00ff88;
    font-weight: bold;
}

.estado-pendiente {
    color: #ffcc00;
    font-weight: bold;
}
</style>
</head>

<body>

<div class="container py-4">

    <div class="text-center mb-4">
        <h2 class="page-title">💰 Sistema de Pagos + Membresías</h2>

        <a href="dashboard.php" class="btn btn-outline-light btn-sm mt-2">
            ← Volver al dashboard
        </a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="form-box">

        <form method="POST">

            <label class="mb-1">Cliente</label>
            <select name="id_cliente" class="form-select mb-3" required>
                <option value="">Seleccionar cliente</option>

                <?php while($c = mysqli_fetch_assoc($clientes)): ?>
                    <option value="<?= $c['id_cliente'] ?>">
                        <?= htmlspecialchars($c['apellido'] . " " . $c['nombre']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label class="mb-1">Membresía / Plan</label>
            <select name="id_membresia" class="form-select mb-3" required>
                <option value="">Seleccionar membresía</option>

                <?php while($m = mysqli_fetch_assoc($membresias)): ?>

                    <?php
                    $nombrePlan = "";

                    if (isset($m['nombre'])) {
                        $nombrePlan = $m['nombre'];
                    } elseif (isset($m['nombre_membresia'])) {
                        $nombrePlan = $m['nombre_membresia'];
                    } elseif (isset($m['plan'])) {
                        $nombrePlan = $m['plan'];
                    } elseif (isset($m['tipo'])) {
                        $nombrePlan = $m['tipo'];
                    } else {
                        $nombrePlan = "Membresía #" . $m['id_membresia'];
                    }

                    $precioPlan = "";

                    if (isset($m['precio'])) {
                        $precioPlan = " - $" . number_format($m['precio'], 2, ",", ".");
                    } elseif (isset($m['monto'])) {
                        $precioPlan = " - $" . number_format($m['monto'], 2, ",", ".");
                    }
                    ?>

                    <option value="<?= $m['id_membresia'] ?>">
                        <?= htmlspecialchars($nombrePlan . $precioPlan) ?>
                    </option>

                <?php endwhile; ?>
            </select>

            <label class="mb-1">Monto</label>
            <input 
                type="number" 
                step="0.01" 
                name="monto" 
                class="form-control mb-3" 
                placeholder="Monto" 
                required>

            <label class="mb-1">Fecha de pago</label>
            <input 
                type="date" 
                name="fecha_pago" 
                class="form-control mb-3" 
                value="<?= date('Y-m-d') ?>"
                required>

            <label class="mb-1">Método de pago</label>
            <select name="metodo_pago" class="form-select mb-3" required>
                <option value="Efectivo">Efectivo</option>
                <option value="Tarjeta">Tarjeta</option>
                <option value="Transferencia">Transferencia</option>
                <option value="Mercado Pago">Mercado Pago</option>
            </select>

            <label class="mb-1">Concepto</label>
            <input 
                type="text" 
                name="concepto" 
                class="form-control mb-3" 
                value="Membresía mensual"
                required>

            <label class="mb-1">Estado</label>
            <select name="estado" class="form-select mb-3" required>
                <option value="Pagado">Pagado</option>
                <option value="Pendiente">Pendiente</option>
            </select>

            <button type="submit" class="btn btn-success w-100">
                Registrar Pago
            </button>

        </form>

    </div>

</div>

<div class="table-box">

    <h2 class="text-center text-danger mb-4">📋 Historial de Pagos</h2>

    <?php if (mysqli_num_rows($pagos) > 0): ?>

        <div class="table-responsive">

            <table class="table table-dark table-hover text-center align-middle">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                        <th>Método</th>
                        <th>Concepto</th>
                        <th>Estado</th>
                    </tr>
                </thead>

                <tbody>

                    <?php while($p = mysqli_fetch_assoc($pagos)): ?>

                        <tr>
                            <td><?= $p['id_pago'] ?></td>

                            <td>
                                <?= htmlspecialchars($p['apellido'] . " " . $p['nombre']) ?>
                            </td>

                            <td>
                                <?= formatoPrecio($p['monto']) ?>
                            </td>

                            <td>
                                <?= formatoFecha($p['fecha_pago']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($p['metodo_pago']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($p['concepto']) ?>
                            </td>

                            <td class="<?= $p['estado'] == 'Pagado' ? 'estado-pagado' : 'estado-pendiente' ?>">
                                <?= htmlspecialchars($p['estado']) ?>
                            </td>
                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <p class="text-center text-secondary">
            No hay pagos registrados todavía.
        </p>

    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>