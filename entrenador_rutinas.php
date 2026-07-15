<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . "/php/conexion.php");

if (!isset($_SESSION['id_personal']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'entrenador') {
    header("Location: login.php");
    exit();
}

$id_personal = intval($_SESSION['id_personal']);
$nombre_entrenador = $_SESSION['nombre'] ?? 'Entrenador';

$mensaje = "";
$error = "";

function limpiar($texto) {
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

/* CREAR RUTINA DEL ENTRENADOR */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre_rutina = trim($_POST['nombre_rutina'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if ($nombre_rutina == "" || $descripcion == "") {

        $error = "Completá el nombre y la descripción de la rutina.";

    } else {

        $sql = "INSERT INTO rutinas 
                (id_personal, nombre_rutina, descripcion, fecha_creacion)
                VALUES (?, ?, ?, CURDATE())";

        $stmt = mysqli_prepare($conexion, $sql);

        if (!$stmt) {
            $error = "Error al preparar la rutina: " . mysqli_error($conexion);
        } else {

            mysqli_stmt_bind_param($stmt, "iss", $id_personal, $nombre_rutina, $descripcion);

            if (mysqli_stmt_execute($stmt)) {
                $mensaje = "Rutina cargada correctamente.";
            } else {
                $error = "Error al cargar rutina: " . mysqli_error($conexion);
            }
        }
    }
}

/* LISTAR RUTINAS DEL ENTRENADOR */
$rutinas = [];

$sqlRutinas = "SELECT id_rutina, nombre_rutina, descripcion, fecha_creacion
               FROM rutinas
               WHERE id_personal = ?
               ORDER BY id_rutina DESC";

$stmtRutinas = mysqli_prepare($conexion, $sqlRutinas);
mysqli_stmt_bind_param($stmtRutinas, "i", $id_personal);
mysqli_stmt_execute($stmtRutinas);
$resRutinas = mysqli_stmt_get_result($stmtRutinas);

if ($resRutinas) {
    while ($r = mysqli_fetch_assoc($resRutinas)) {
        $rutinas[] = $r;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Rutinas Entrenador - Warrior Gym</title>

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

.box {
    background: #1c1c1c;
    border: 1px solid #333;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 25px;
}

.form-control {
    background: #111;
    border: 1px solid #333;
    color: white;
}

.form-control:focus {
    background: #111;
    color: white;
    border-color: #dc3545;
    box-shadow: none;
}

.table-dark th {
    background: #d40000;
    color: white;
}
</style>
</head>

<body>

<div class="header">
    <h1>🏋 Rutinas del Entrenador</h1>
</div>

<div class="container py-5">

    <div class="mb-3">
        <a href="dashboard_entrenador.php" class="btn btn-outline-light btn-sm">
            ⬅ Volver al panel
        </a>
    </div>

    <h3 class="text-danger">
        Entrenador: <?= limpiar($nombre_entrenador) ?>
    </h3>

    <p class="text-secondary">
        Cargá rutinas que quedarán vinculadas a tu usuario de entrenador.
    </p>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-success text-center">
            <?= limpiar($mensaje) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center">
            <?= limpiar($error) ?>
        </div>
    <?php endif; ?>

    <div class="box">

        <h4 class="text-danger mb-3">
            Crear nueva rutina
        </h4>

        <form method="POST">

            <label class="mb-1">Nombre de rutina *</label>
            <input 
                type="text" 
                name="nombre_rutina" 
                class="form-control mb-3" 
                placeholder="Ej: Rutina fuerza intermedia"
                required>

            <label class="mb-1">Descripción *</label>
            <textarea 
                name="descripcion" 
                class="form-control mb-3" 
                rows="7"
                placeholder="Detalle de ejercicios, series, repeticiones y objetivo"
                required></textarea>

            <button class="btn btn-danger w-100">
                Guardar Rutina
            </button>

        </form>

    </div>

    <div class="box">

        <h4 class="text-danger mb-3">
            Mis rutinas cargadas
        </h4>

        <?php if (count($rutinas) == 0): ?>

            <p class="text-secondary text-center mb-0">
                Todavía no cargaste rutinas.
            </p>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table table-dark table-hover align-middle text-center">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Rutina</th>
                            <th>Descripción</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($rutinas as $r): ?>

                            <tr>
                                <td><?= intval($r['id_rutina']) ?></td>
                                <td><?= limpiar($r['nombre_rutina']) ?></td>
                                <td class="text-start"><?= nl2br(limpiar($r['descripcion'])) ?></td>
                                <td>
                                    <?= !empty($r['fecha_creacion']) && $r['fecha_creacion'] != '0000-00-00'
                                        ? date("d/m/Y", strtotime($r['fecha_creacion']))
                                        : '-' ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>