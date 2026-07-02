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
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $objetivo = trim($_POST['objetivo'] ?? '');
    $nivel = trim($_POST['nivel'] ?? '');
    $dias = trim($_POST['dias'] ?? '');
    $duracion = trim($_POST['duracion'] ?? '');
    $preferencias = trim($_POST['preferencias'] ?? '');
    $lesiones = trim($_POST['lesiones'] ?? '');
    $extra = trim($_POST['extra'] ?? '');

    if (
        empty($objetivo) ||
        empty($nivel) ||
        empty($dias) ||
        empty($duracion) ||
        empty($preferencias)
    ) {

        $error = "Completá los campos obligatorios.";

    } else {

        /*
            EVITAR SOLICITUD PENDIENTE DUPLICADA
            Si el cliente ya tiene una solicitud pendiente,
            no se le permite mandar otra hasta que el admin la gestione.
        */
        $sqlCheck = "SELECT COUNT(*) AS total
                     FROM solicitudes_rutina
                     WHERE id_cliente = ?
                     AND LOWER(TRIM(estado)) = 'pendiente'";

        $stmtCheck = mysqli_prepare($conexion, $sqlCheck);

        if (!$stmtCheck) {
            die("Error al preparar verificación: " . mysqli_error($conexion));
        }

        mysqli_stmt_bind_param($stmtCheck, "i", $id_cliente);
        mysqli_stmt_execute($stmtCheck);

        $resCheck = mysqli_stmt_get_result($stmtCheck);
        $rowCheck = mysqli_fetch_assoc($resCheck);

        if ($rowCheck['total'] > 0) {

            $error = "Ya tenés una solicitud de rutina pendiente. Esperá a que el administrador la revise.";

        } else {

            $descripcion =
                "Objetivo: " . $objetivo . "\n" .
                "Nivel: " . $nivel . "\n" .
                "Días de entrenamiento: " . $dias . "\n" .
                "Duración por sesión: " . $duracion . "\n" .
                "Preferencias: " . $preferencias . "\n" .
                "Lesiones: " . ($lesiones ?: "Ninguna") . "\n" .
                "Comentarios extra: " . ($extra ?: "Sin comentarios");

            $sql = "INSERT INTO solicitudes_rutina
                    (id_cliente, descripcion, estado, fecha_solicitud)
                    VALUES (?, ?, 'Pendiente', CURDATE())";

            $stmt = mysqli_prepare($conexion, $sql);

            if (!$stmt) {
                die("Error al preparar solicitud: " . mysqli_error($conexion));
            }

            mysqli_stmt_bind_param($stmt, "is", $id_cliente, $descripcion);

            if (mysqli_stmt_execute($stmt)) {

                echo "<script>
                    alert('Solicitud enviada correctamente. Quedará pendiente hasta que el administrador la revise.');
                    window.location.href='dashboard_usuario.php';
                </script>";
                exit();

            } else {

                $error = "Error al enviar la solicitud: " . mysqli_error($conexion);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Solicitar Rutina - Warrior Gym</title>

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

.rutina-card {
    background: rgba(15, 15, 15, .96);
    border: 1px solid #ffc107;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 0 25px rgba(255,193,7,.18);
}

.page-title {
    color: #ffc107;
    font-weight: 800;
}

.form-control,
.form-select {
    background: #111;
    border: 1px solid #333;
    color: white;
    padding: 12px;
}

.form-control:focus,
.form-select:focus {
    background: #111;
    color: white;
    border-color: #ffc107;
    box-shadow: none;
}

.form-control::placeholder {
    color: #999;
}

label {
    margin-bottom: 5px;
    font-weight: 600;
}
</style>

</head>

<body>

<div class="container py-5">

    <div class="text-center mb-4">

        <h2 class="page-title">
            <i class="bi bi-clipboard2-pulse-fill"></i> Solicitud de Rutina
        </h2>

        <p class="text-secondary">
            Completá tus datos para que el entrenador pueda prepararte una rutina personalizada.
        </p>

        <a href="dashboard_usuario.php" class="btn btn-outline-light btn-sm">
            ← Volver al panel
        </a>

    </div>

    <div class="row justify-content-center">

        <div class="col-lg-7 col-md-9">

            <div class="rutina-card">

                <?php if(!empty($error)): ?>
                    <div class="alert alert-danger text-center">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">

                    <label>🎯 Objetivo</label>
                    <select name="objetivo" class="form-select mb-3" required>
                        <option value="">Seleccionar objetivo</option>
                        <option>Ganar masa muscular</option>
                        <option>Bajar de peso</option>
                        <option>Definición</option>
                        <option>Resistencia</option>
                        <option>Fuerza</option>
                    </select>

                    <label>💪 Nivel</label>
                    <select name="nivel" class="form-select mb-3" required>
                        <option value="">Seleccionar nivel</option>
                        <option>Principiante</option>
                        <option>Intermedio</option>
                        <option>Avanzado</option>
                    </select>

                    <label>📅 Días de entrenamiento</label>
                    <select name="dias" class="form-select mb-3" required>
                        <option value="">Seleccionar días</option>
                        <option>2 días</option>
                        <option>3 días</option>
                        <option>4 días</option>
                        <option>5 días</option>
                        <option>6 días</option>
                    </select>

                    <label>⏱ Duración por sesión</label>
                    <select name="duracion" class="form-select mb-3" required>
                        <option value="">Seleccionar duración</option>
                        <option>45 minutos</option>
                        <option>60 minutos</option>
                        <option>90 minutos</option>
                    </select>

                    <label>🏋 Preferencias</label>
                    <input 
                        type="text" 
                        name="preferencias" 
                        class="form-control mb-3" 
                        placeholder="Ej: pesas, cardio, mixto" 
                        required>

                    <label>⚠ Lesiones o molestias</label>
                    <input 
                        type="text" 
                        name="lesiones" 
                        class="form-control mb-3" 
                        placeholder="Ej: rodilla, espalda, hombro">

                    <label>📝 Comentarios extra</label>
                    <textarea 
                        name="extra" 
                        class="form-control mb-4" 
                        rows="3" 
                        placeholder="Agregá cualquier detalle importante"></textarea>

                    <button class="btn btn-warning w-100 py-2 fw-bold">
                        <i class="bi bi-send-fill"></i> Enviar solicitud
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>