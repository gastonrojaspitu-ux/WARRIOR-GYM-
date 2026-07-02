<?php
session_start();
include(__DIR__ . "/php/conexion.php");

/* PROTECCIÓN */
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

/* Si entra admin, vuelve al panel admin */
if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin') {
    header("Location: admin/dashboard.php");
    exit();
}

if (!isset($_SESSION['id_cliente']) || empty($_SESSION['id_cliente'])) {
    echo "<script>
        alert('Tu cuenta no está vinculada a un cliente. Consultá al administrador.');
        window.location='dashboard_usuario.php';
    </script>";
    exit();
}

$id_cliente = intval($_SESSION['id_cliente']);

/* DATOS DEL CLIENTE */
$sqlCliente = "SELECT nombre, apellido, email 
               FROM clientes 
               WHERE id_cliente = $id_cliente
               LIMIT 1";

$resCliente = mysqli_query($conexion, $sqlCliente);

if (!$resCliente || mysqli_num_rows($resCliente) == 0) {
    die("Error: cliente no encontrado.");
}

$cliente = mysqli_fetch_assoc($resCliente);

$nombreCompleto = $cliente['nombre'] . " " . $cliente['apellido'];
$email = $cliente['email'];

/* BUSCAR MEMBRESÍA ACTIVA DEL CLIENTE */
$sqlMembresia = "
SELECT 
    cm.*,
    m.*
FROM cliente_membresia cm
INNER JOIN membresias m ON cm.id_membresia = m.id_membresia
WHERE cm.id_cliente = $id_cliente
ORDER BY cm.fecha_inicio DESC
LIMIT 1
";

$resMembresia = mysqli_query($conexion, $sqlMembresia);

if (!$resMembresia) {
    die("Error SQL membresía: " . mysqli_error($conexion));
}

$membresia = mysqli_fetch_assoc($resMembresia);

/* BUSCAR ÚLTIMA SOLICITUD POR EMAIL */
$sqlSolicitud = "SELECT * 
                 FROM solicitudes_membresia
                 WHERE email = '$email'
                 ORDER BY id_solicitud DESC
                 LIMIT 1";

$resSolicitud = mysqli_query($conexion, $sqlSolicitud);

if (!$resSolicitud) {
    die("Error SQL solicitud: " . mysqli_error($conexion));
}

$solicitud = mysqli_fetch_assoc($resSolicitud);

/* FUNCIÓN PARA MOSTRAR NOMBRE DE PLAN */
function obtenerNombrePlan($fila) {
    if (isset($fila['nombre']) && $fila['nombre'] != "") {
        return $fila['nombre'];
    }

    if (isset($fila['nombre_membresia']) && $fila['nombre_membresia'] != "") {
        return $fila['nombre_membresia'];
    }

    if (isset($fila['plan']) && $fila['plan'] != "") {
        return $fila['plan'];
    }

    if (isset($fila['tipo']) && $fila['tipo'] != "") {
        return $fila['tipo'];
    }

    return "Membresía";
}

function formatoFecha($fecha) {
    if (empty($fecha)) {
        return "-";
    }

    return date("d/m/Y", strtotime($fecha));
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Mi Membresía - Warrior Gym</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #0f0f0f;
    color: white;
}

.card-gym {
    background: #1c1c1c;
    max-width: 550px;
    margin: 70px auto;
    padding: 30px;
    border-radius: 18px;
    text-align: center;
    box-shadow: 0 0 20px rgba(220,53,69,0.3);
    border: 1px solid #dc3545;
}

.title {
    color: #dc3545;
    margin-bottom: 20px;
    font-weight: bold;
}

.estado-box {
    background: #111;
    border-radius: 12px;
    padding: 18px;
    margin-top: 20px;
    border: 1px solid #333;
}
</style>

</head>

<body>

<div class="card-gym">

    <h2 class="title">💪 Mi Membresía</h2>

    <h5>Bienvenido, <?= htmlspecialchars($nombreCompleto) ?></h5>

    <p class="text-secondary">
        <?= htmlspecialchars($email) ?>
    </p>

    <hr>

    <?php if ($membresia): ?>

        <div class="estado-box">

            <h4 class="text-success">
                <?= htmlspecialchars(obtenerNombrePlan($membresia)) ?>
            </h4>

            <p>
                <strong>Estado:</strong>
                <?php if ($membresia['estado'] == 'Activa'): ?>
                    <span class="badge bg-success">Activa</span>
                <?php else: ?>
                    <span class="badge bg-secondary">
                        <?= htmlspecialchars($membresia['estado']) ?>
                    </span>
                <?php endif; ?>
            </p>

            <p>
                <strong>Fecha inicio:</strong>
                <?= formatoFecha($membresia['fecha_inicio'] ?? '') ?>
            </p>

            <p>
                <strong>Fecha fin:</strong>
                <?= formatoFecha($membresia['fecha_fin'] ?? '') ?>
            </p>

            <p class="text-success mb-0">
                Tu membresía está registrada en el sistema.
            </p>

        </div>

    <?php elseif ($solicitud): ?>

        <div class="estado-box">

            <h4 class="text-warning">
                Solicitud: <?= htmlspecialchars($solicitud['plan_solicitado']) ?>
            </h4>

            <p>
                <strong>Fecha solicitud:</strong>
                <?= formatoFecha($solicitud['fecha_solicitud']) ?>
            </p>

            <p>
                <strong>Estado:</strong>

                <?php if ($solicitud['estado'] == 'Pendiente'): ?>
                    <span class="badge bg-warning text-dark">Pendiente</span>
                <?php elseif ($solicitud['estado'] == 'Aprobada'): ?>
                    <span class="badge bg-success">Aprobada</span>
                <?php else: ?>
                    <span class="badge bg-danger">Rechazada</span>
                <?php endif; ?>
            </p>

            <p class="text-secondary mb-0">
                Todavía no tenés una membresía activa registrada por pago.
            </p>

        </div>

    <?php else: ?>

        <p class="text-secondary">
            No tenés membresía ni solicitud registrada todavía.
        </p>

        <a href="solicitar_membresia.php" class="btn btn-warning w-100 mt-2">
            Solicitar Membresía
        </a>

    <?php endif; ?>

    <a href="dashboard_usuario.php" class="btn btn-danger mt-3 w-100">
        Volver al panel
    </a>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>