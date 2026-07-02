<?php
session_start();
include(__DIR__ . "/php/conexion.php");

/* PROTEGER SOLO CLIENTE */
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['id_cliente'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'cliente') {
    header("Location: login.php");
    exit();
}

$id_cliente = intval($_SESSION['id_cliente']);

/* FUNCIONES */
function limpiar($texto) {
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

function formatoFecha($fecha) {
    if (empty($fecha)) {
        return "Sin fecha";
    }

    return date("d/m/Y", strtotime($fecha));
}

function obtenerColumnas($conexion, $tabla) {
    $columnas = [];

    if (!preg_match('/^[a-zA-Z0-9_]+$/', $tabla)) {
        return $columnas;
    }

    $res = mysqli_query($conexion, "SHOW COLUMNS FROM `$tabla`");

    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $columnas[$row['Field']] = $row;
        }
    }

    return $columnas;
}

function columnaExiste($columnas, $columna) {
    return isset($columnas[$columna]);
}

function obtenerPrimaryKey($columnas) {
    foreach ($columnas as $nombre => $info) {
        if (isset($info['Key']) && $info['Key'] == 'PRI') {
            return $nombre;
        }
    }

    return null;
}

/* VERIFICAR COLUMNAS DE rutina_asignada */
$columnasRutinaAsignada = obtenerColumnas($conexion, "rutina_asignada");

$tieneEstado = columnaExiste($columnasRutinaAsignada, "estado");
$tieneFecha = columnaExiste($columnasRutinaAsignada, "fecha_asignacion");
$pkRutinaAsignada = obtenerPrimaryKey($columnasRutinaAsignada);

/*
    CONSULTA:
    - Si existe columna estado, busca solo Activa.
    - Si existe fecha_asignacion, ordena por la más nueva.
    - Si existe clave primaria, también ordena por ID descendente.
    - Muestra solo una rutina.
*/

$whereEstado = "";
$orderBy = [];

if ($tieneEstado) {
    $whereEstado = " AND LOWER(TRIM(estado)) = 'activa' ";
}

if ($tieneFecha) {
    $orderBy[] = "fecha_asignacion DESC";
}

if (!empty($pkRutinaAsignada)) {
    $orderBy[] = "`$pkRutinaAsignada` DESC";
}

$orderSql = "";

if (count($orderBy) > 0) {
    $orderSql = " ORDER BY " . implode(", ", $orderBy);
}

$sql = "SELECT * 
        FROM rutina_asignada 
        WHERE id_cliente = ?
        $whereEstado
        $orderSql
        LIMIT 1";

$stmt = mysqli_prepare($conexion, $sql);

if (!$stmt) {
    die("Error al preparar SQL: " . mysqli_error($conexion));
}

mysqli_stmt_bind_param($stmt, "i", $id_cliente);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);

if (!$resultado) {
    die("Error en SQL: " . mysqli_error($conexion));
}

$rutina = mysqli_fetch_assoc($resultado);
?>

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Mi Rutina - Warrior Gym</title>

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

.card-rutina {
    background: rgba(15, 15, 15, .96);
    border: 1px solid #252525;
    border-radius: 18px;
    padding: 28px;
    box-shadow: 0 0 20px rgba(220,53,69,0.18);
    transition: .3s;
}

.card-rutina:hover {
    border-color: #dc3545;
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(220,53,69,0.25);
}

.titulo {
    color: #dc3545;
    font-weight: bold;
}

.seccion {
    border-bottom: 1px solid #333;
    padding: 12px 0;
}

.seccion:last-child {
    border-bottom: none;
}

.badge-activa {
    background: #198754;
}

.badge-inactiva {
    background: #6c757d;
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

        <h1 class="page-title">
            <i class="bi bi-activity"></i> Mi Rutina Personalizada
        </h1>

        <p class="text-secondary">
            Acá podés ver tu rutina activa asignada por el gimnasio.
        </p>

        <a href="dashboard_usuario.php" class="btn btn-outline-light btn-sm">
            ← Volver al panel
        </a>

    </div>

    <?php if (!$rutina): ?>

        <div class="empty-box text-center col-md-7 mx-auto">

            <h4 class="text-danger">
                No tenés una rutina activa todavía
            </h4>

            <p class="text-secondary">
                El entrenador aún no te asignó una rutina activa.
            </p>

            <a href="solicitar_rutina.php" class="btn btn-danger">
                Solicitar rutina
            </a>

        </div>

    <?php else: ?>

        <div class="card-rutina mb-4">

            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">

                <h3 class="titulo mb-0">
                    🏋 <?= limpiar($rutina['nombre_rutina'] ?? 'Rutina personalizada') ?>
                </h3>

                <?php if (isset($rutina['estado'])): ?>

                    <?php if ($rutina['estado'] == 'Activa'): ?>

                        <span class="badge badge-activa">
                            Activa
                        </span>

                    <?php else: ?>

                        <span class="badge badge-inactiva">
                            <?= limpiar($rutina['estado']) ?>
                        </span>

                    <?php endif; ?>

                <?php else: ?>

                    <span class="badge badge-activa">
                        Asignada
                    </span>

                <?php endif; ?>

            </div>

            <?php if (isset($rutina['fecha_asignacion'])): ?>

                <div class="seccion">
                    <strong>📅 Fecha de asignación:</strong>
                    <?= formatoFecha($rutina['fecha_asignacion']) ?>
                </div>

            <?php endif; ?>

            <div class="seccion">
                <strong>📌 Descripción:</strong><br>
                <?= nl2br(limpiar($rutina['descripcion'] ?? 'Sin descripción cargada.')) ?>
            </div>

            <?php if (!empty($rutina['objetivo'])): ?>

                <div class="seccion">
                    <strong>🎯 Objetivo:</strong>
                    <?= limpiar($rutina['objetivo']) ?>
                </div>

            <?php endif; ?>

            <?php if (!empty($rutina['nivel'])): ?>

                <div class="seccion">
                    <strong>💪 Nivel:</strong>
                    <?= limpiar($rutina['nivel']) ?>
                </div>

            <?php endif; ?>

            <?php if (!empty($rutina['dias'])): ?>

                <div class="seccion">
                    <strong>📅 Días:</strong>
                    <?= limpiar($rutina['dias']) ?>
                </div>

            <?php endif; ?>

        </div>

    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>