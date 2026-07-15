<?php
session_start();
include(__DIR__ . "/../php/conexion.php");

/* PROTEGER ADMIN */
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$mensaje = "";
$error = "";
$id_personal = 0;

/* TRAER PERSONAL / ENTRENADORES */
$entrenadores = [];

$sqlEntrenadores = "SELECT id_personal, nombre, apellido
                    FROM personal
                    ORDER BY apellido, nombre";

$resEntrenadores = mysqli_query($conexion, $sqlEntrenadores);

if ($resEntrenadores) {
    while ($e = mysqli_fetch_assoc($resEntrenadores)) {
        $entrenadores[] = $e;
    }
}

/* FUNCIONES */
function limpiar($texto) {
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

function formatoFecha($fecha) {
    if (empty($fecha)) {
        return "-";
    }
    return date("d/m/Y", strtotime($fecha));
}

/* CREAR RUTINA */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] == "crear_rutina") {

    $id_personal = intval($_POST['id_personal'] ?? 0);
    $nombre_rutina = trim($_POST['nombre_rutina'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if ($id_personal <= 0 || $nombre_rutina == "" || $descripcion == "") {

        $error = "Seleccioná un entrenador y completá el nombre y la descripción de la rutina.";

    } else {

        /* VALIDAR QUE EL ENTRENADOR EXISTA */
        $sqlPersonal = "SELECT id_personal 
                        FROM personal 
                        WHERE id_personal = ?
                        LIMIT 1";

        $stmtPersonal = mysqli_prepare($conexion, $sqlPersonal);

        if (!$stmtPersonal) {

            $error = "Error al preparar entrenador: " . mysqli_error($conexion);

        } else {

            mysqli_stmt_bind_param($stmtPersonal, "i", $id_personal);
            mysqli_stmt_execute($stmtPersonal);
            $resPersonal = mysqli_stmt_get_result($stmtPersonal);

            if (!$resPersonal || mysqli_num_rows($resPersonal) == 0) {

                $error = "El entrenador seleccionado no existe.";

            } else {

                $sql =$sql = "INSERT INTO rutinas (id_personal, nombre_rutina, descripcion, fecha_creacion) 
        VALUES (?, ?, ?, CURDATE())";
                $stmt = mysqli_prepare($conexion, $sql);

                if (!$stmt) {

                    $error = "Error al preparar la rutina: " . mysqli_error($conexion);

                } else {

                    mysqli_stmt_bind_param($stmt, "iss", $id_personal, $nombre_rutina, $descripcion);

                    if (mysqli_stmt_execute($stmt)) {
                        $mensaje = "Rutina creada correctamente.";
                    } else {
                        $error = "Error al crear rutina: " . mysqli_error($conexion);
                    }
                }
            }
        }
    }
}

/* ASIGNAR RUTINA */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] == "asignar_rutina") {

    $id_cliente = intval($_POST['id_cliente'] ?? 0);
    $id_rutina = intval($_POST['id_rutina'] ?? 0);
    $id_solicitud = intval($_POST['id_solicitud'] ?? 0);

    if ($id_cliente <= 0 || $id_rutina <= 0) {

        $error = "Seleccioná un cliente y una rutina válida.";

    } else {

        /* BUSCAR LA RUTINA EN LA TABLA rutinas */
        $sqlRutina = $sqlRutina = "SELECT id_rutina, id_personal, nombre_rutina, descripcion 
              FROM rutinas 
              WHERE id_rutina = ?
              LIMIT 1";

        $stmtRutina = mysqli_prepare($conexion, $sqlRutina);

        if (!$stmtRutina) {

            $error = "Error al preparar búsqueda de rutina: " . mysqli_error($conexion);

        } else {

            mysqli_stmt_bind_param($stmtRutina, "i", $id_rutina);
            mysqli_stmt_execute($stmtRutina);
            $resRutina = mysqli_stmt_get_result($stmtRutina);

            if (!$resRutina || mysqli_num_rows($resRutina) == 0) {

                $error = "La rutina seleccionada no existe.";

            } else {

                $rutina = mysqli_fetch_assoc($resRutina);

                $nombre_rutina = $rutina['nombre_rutina'];
                $descripcion = $rutina['descripcion'];
                $id_personal = intval($rutina['id_personal']);

                /*
                    DESACTIVAR RUTINAS ANTERIORES DEL CLIENTE
                    Tu tabla rutina_asignada sí tiene estado.
                */
                $sqlDesactivar = "UPDATE rutina_asignada
                                  SET estado = 'Inactiva'
                                  WHERE id_cliente = ?
                                  AND estado = 'Activa'";

                $stmtDesactivar = mysqli_prepare($conexion, $sqlDesactivar);

                if ($stmtDesactivar) {
                    mysqli_stmt_bind_param($stmtDesactivar, "i", $id_cliente);
                    mysqli_stmt_execute($stmtDesactivar);
                }

                /*
                    INSERTAR RUTINA ASIGNADA
                    IMPORTANTE:
                    Tu tabla rutina_asignada NO tiene id_rutina.
                    Por eso solo guardamos:
                    id_cliente, nombre_rutina, descripcion, fecha_asignacion, estado
                */
                $sqlAsignar = $sqlAsignar = "INSERT INTO rutina_asignada
               (id_cliente, id_personal, nombre_rutina, descripcion, fecha_asignacion, estado)
               VALUES
               (?, ?, ?, ?, CURDATE(), 'Activa')";
                $stmtAsignar = mysqli_prepare($conexion, $sqlAsignar);

                if (!$stmtAsignar) {

                    $error = "Error al preparar asignación: " . mysqli_error($conexion);

                } else {

                   mysqli_stmt_bind_param(
    $stmtAsignar,
    "iiss",
    $id_cliente,
    $id_personal,
    $nombre_rutina,
    $descripcion
);

                    if (mysqli_stmt_execute($stmtAsignar)) {

                        /* SI VIENE DE UNA SOLICITUD, MARCAR COMO APROBADA */
                        if ($id_solicitud > 0) {

                            $sqlUpdate = "UPDATE solicitudes_rutina
                                          SET estado = 'Aprobada'
                                          WHERE id_solicitud = ?";

                            $stmtUpdate = mysqli_prepare($conexion, $sqlUpdate);

                            if ($stmtUpdate) {
                                mysqli_stmt_bind_param($stmtUpdate, "i", $id_solicitud);
                                mysqli_stmt_execute($stmtUpdate);
                            }
                        }

                        $mensaje = "Rutina asignada correctamente.";

                    } else {

                        $error = "Error al asignar rutina: " . mysqli_error($conexion);
                    }
                }
            }
        }
    }
}

/* RECHAZAR SOLICITUD */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] == "rechazar_solicitud") {

    $id_solicitud = intval($_POST['id_solicitud'] ?? 0);

    if ($id_solicitud <= 0) {

        $error = "Solicitud no válida.";

    } else {

        $sqlRechazar = "UPDATE solicitudes_rutina
                        SET estado = 'Rechazada'
                        WHERE id_solicitud = ?
                        AND estado = 'Pendiente'";

        $stmtRechazar = mysqli_prepare($conexion, $sqlRechazar);

        if (!$stmtRechazar) {

            $error = "Error al preparar rechazo: " . mysqli_error($conexion);

        } else {

            mysqli_stmt_bind_param($stmtRechazar, "i", $id_solicitud);
            mysqli_stmt_execute($stmtRechazar);

            if (mysqli_stmt_affected_rows($stmtRechazar) > 0) {
                $mensaje = "Solicitud rechazada correctamente.";
            } else {
                $error = "No se pudo rechazar la solicitud o ya estaba gestionada.";
            }
        }
    }
}

/* TRAER CLIENTES */
$clientes = [];

$sqlClientes = "SELECT id_cliente, nombre, apellido, email
                FROM clientes
                WHERE estado = 'Activo'
                AND id_usuario IS NOT NULL
                ORDER BY apellido, nombre";

$resClientes = mysqli_query($conexion, $sqlClientes);

if ($resClientes) {
    while ($c = mysqli_fetch_assoc($resClientes)) {
        $clientes[] = $c;
    }
}

/* TRAER RUTINAS */
$rutinas = [];

$sqlRutinas = "SELECT id_rutina, nombre_rutina, descripcion
               FROM rutinas
               ORDER BY id_rutina DESC";

$resRutinas = mysqli_query($conexion, $sqlRutinas);

if ($resRutinas) {
    while ($r = mysqli_fetch_assoc($resRutinas)) {
        $rutinas[] = $r;
    }
}

/* TRAER SOLICITUDES */
$solicitudes = [];

$sqlSolicitudes = "SELECT 
                        s.id_solicitud,
                        s.id_cliente,
                        s.descripcion,
                        s.estado,
                        s.fecha_solicitud,
                        c.nombre AS cliente_nombre,
                        c.apellido AS cliente_apellido,
                        c.email AS cliente_email
                   FROM solicitudes_rutina s
                   INNER JOIN clientes c 
                        ON s.id_cliente = c.id_cliente
                   ORDER BY 
                        CASE 
                            WHEN s.estado = 'Pendiente' THEN 0
                            ELSE 1
                        END,
                        s.fecha_solicitud DESC";

$resSolicitudes = mysqli_query($conexion, $sqlSolicitudes);

if ($resSolicitudes) {
    while ($s = mysqli_fetch_assoc($resSolicitudes)) {
        $solicitudes[] = $s;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Rutinas - Admin Warrior Gym</title>

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

.box {
    background: #181818;
    border: 1px solid #252525;
    border-radius: 18px;
    padding: 25px;
    box-shadow: 0 0 20px rgba(220,53,69,.15);
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

.table-dark th {
    background: #dc3545;
    color: white;
}

.badge-pendiente {
    background: #ffc107;
    color: #000;
}

.badge-aprobada {
    background: #198754;
}

.badge-rechazada {
    background: #dc3545;
}

.badge-otro {
    background: #6c757d;
}
</style>
</head>

<body>

<div class="container py-5">

    <div class="text-center mb-4">

        <h2 class="page-title">
            <i class="bi bi-activity"></i> Gestión de Rutinas
        </h2>

        <p class="text-secondary">
            Crear rutinas, asignarlas a clientes y revisar solicitudes.
        </p>

        <a href="dashboard.php" class="btn btn-outline-light btn-sm">
            ← Volver al dashboard
        </a>

    </div>

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

    <div class="row g-4 mb-4">

        <!-- CREAR RUTINA -->
        <div class="col-lg-6">

            <div class="box h-100">

                <h4 class="text-danger mb-3">
                    <i class="bi bi-plus-circle-fill"></i> Crear Rutina
                </h4>

                <form method="POST">

                    <input type="hidden" name="accion" value="crear_rutina">
                    <label class="mb-1">Entrenador / Personal responsable</label>
<select name="id_personal" class="form-select mb-3" required>
    <option value="">Seleccionar entrenador</option>

    <?php foreach ($entrenadores as $e): ?>
        <option value="<?= intval($e['id_personal']) ?>" <?= ($id_personal == $e['id_personal']) ? "selected" : "" ?>>
            <?= limpiar($e['apellido'] . " " . $e['nombre']) ?>
        </option>
    <?php endforeach; ?>

</select>

                    <label class="mb-1">Nombre de rutina</label>
                    <input 
                        type="text" 
                        name="nombre_rutina" 
                        class="form-control mb-3" 
                        placeholder="Ej: Hipertrofia inicial"
                        required>

                    <label class="mb-1">Descripción</label>
                    <textarea 
                        name="descripcion" 
                        class="form-control mb-3" 
                        rows="6"
                        placeholder="Ej: Día 1: Pecho y tríceps..."
                        required></textarea>

                    <button class="btn btn-danger w-100">
                        Guardar Rutina
                    </button>

                </form>

            </div>

        </div>

        <!-- ASIGNAR RUTINA -->
        <div class="col-lg-6">

            <div class="box h-100">

                <h4 class="text-danger mb-3">
                    <i class="bi bi-person-check-fill"></i> Asignar Rutina
                </h4>

                <form method="POST">

                    <input type="hidden" name="accion" value="asignar_rutina">

                    <label class="mb-1">Cliente</label>
                    <select name="id_cliente" class="form-select mb-3" required>
                        <option value="">Seleccionar cliente</option>

                        <?php foreach ($clientes as $c): ?>
                            <option value="<?= intval($c['id_cliente']) ?>">
                                <?= limpiar($c['apellido'] . " " . $c['nombre']) ?> - <?= limpiar($c['email']) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>

                    <label class="mb-1">Rutina</label>
                    <select name="id_rutina" class="form-select mb-3" required>
                        <option value="">Seleccionar rutina</option>

                        <?php foreach ($rutinas as $r): ?>
                            <option value="<?= intval($r['id_rutina']) ?>">
                                <?= limpiar($r['nombre_rutina']) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>

                    <button class="btn btn-success w-100">
                        Asignar Rutina
                    </button>

                </form>

            </div>

        </div>

    </div>

    <!-- RUTINAS CREADAS -->
    <div class="box mb-4">

        <h4 class="text-danger mb-3">
            <i class="bi bi-list-check"></i> Rutinas Creadas
        </h4>

        <?php if (count($rutinas) > 0): ?>

            <div class="table-responsive">

                <table class="table table-dark table-hover align-middle text-center mb-0">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Rutina</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($rutinas as $r): ?>

                            <tr>
                                <td><?= intval($r['id_rutina']) ?></td>
                                <td><?= limpiar($r['nombre_rutina']) ?></td>
                                <td class="text-start"><?= nl2br(limpiar($r['descripcion'])) ?></td>
                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <p class="text-secondary text-center mb-0">
                Todavía no hay rutinas creadas.
            </p>

        <?php endif; ?>

    </div>

    <!-- SOLICITUDES -->
    <div class="box">

        <h4 class="text-danger mb-3">
            <i class="bi bi-inbox-fill"></i> Solicitudes de Rutina
        </h4>

        <?php if (count($solicitudes) > 0): ?>

            <div class="table-responsive">

                <table class="table table-dark table-hover align-middle text-center mb-0">

                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Solicitud</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($solicitudes as $s): ?>

                            <?php
                            $estado = $s['estado'] ?? 'Pendiente';
                            $fechaSolicitud = $s['fecha_solicitud'] ?? null;
                            ?>

                            <tr>
                                <td>
                                    <?= limpiar($s['cliente_apellido'] . " " . $s['cliente_nombre']) ?><br>
                                    <small class="text-secondary"><?= limpiar($s['cliente_email']) ?></small>
                                </td>

                                <td class="text-start">
                                    <?= nl2br(limpiar($s['descripcion'])) ?>
                                </td>

                                <td>
                                    <?= formatoFecha($fechaSolicitud) ?>
                                </td>

                                <td>
                                    <?php if ($estado == "Pendiente"): ?>
                                        <span class="badge badge-pendiente">Pendiente</span>
                                    <?php elseif ($estado == "Aprobada"): ?>
                                        <span class="badge badge-aprobada">Aprobada</span>
                                    <?php elseif ($estado == "Rechazada"): ?>
                                        <span class="badge badge-rechazada">Rechazada</span>
                                    <?php else: ?>
                                        <span class="badge badge-otro"><?= limpiar($estado) ?></span>
                                    <?php endif; ?>
                                </td>

                                <td>

                                    <?php if ($estado == "Pendiente"): ?>

                                        <div class="d-flex gap-2 justify-content-center flex-wrap">

                                            <form method="POST" class="d-flex gap-2">

                                                <input type="hidden" name="accion" value="asignar_rutina">
                                                <input type="hidden" name="id_cliente" value="<?= intval($s['id_cliente']) ?>">
                                                <input type="hidden" name="id_solicitud" value="<?= intval($s['id_solicitud']) ?>">

                                                <select name="id_rutina" class="form-select form-select-sm" required>
                                                    <option value="">Rutina</option>

                                                    <?php foreach ($rutinas as $r): ?>
                                                        <option value="<?= intval($r['id_rutina']) ?>">
                                                            <?= limpiar($r['nombre_rutina']) ?>
                                                        </option>
                                                    <?php endforeach; ?>

                                                </select>

                                                <button class="btn btn-success btn-sm">
                                                    Asignar
                                                </button>

                                            </form>

                                            <form method="POST">

                                                <input type="hidden" name="accion" value="rechazar_solicitud">
                                                <input type="hidden" name="id_solicitud" value="<?= intval($s['id_solicitud']) ?>">

                                                <button 
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('¿Seguro que querés rechazar esta solicitud?');">
                                                    Rechazar
                                                </button>

                                            </form>

                                        </div>

                                    <?php else: ?>

                                        <span class="text-secondary">Ya gestionada</span>

                                    <?php endif; ?>

                                </td>
                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <p class="text-secondary text-center mb-0">
                No hay solicitudes de rutina todavía.
            </p>

        <?php endif; ?>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>