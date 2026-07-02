<?php
session_start();
include(__DIR__ . "/php/conexion.php");

date_default_timezone_set("America/Argentina/Buenos_Aires");

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['id_cliente'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'cliente') {
    header("Location: login.php");
    exit();
}

$id_cliente = intval($_SESSION['id_cliente']);
$id_aparato = isset($_GET['id_aparato']) ? intval($_GET['id_aparato']) : 0;

if ($id_aparato <= 0) {
    echo "<script>
        alert('No se seleccionó un aparato válido.');
        window.location='clases_usuario.php';
    </script>";
    exit();
}

$error = "";

/* TRAER NOMBRE DEL APARATO */
$sqlAparato = "SELECT nombre FROM aparatos WHERE id_aparato = ? LIMIT 1";
$stmtAparato = mysqli_prepare($conexion, $sqlAparato);
mysqli_stmt_bind_param($stmtAparato, "i", $id_aparato);
mysqli_stmt_execute($stmtAparato);
$resAparato = mysqli_stmt_get_result($stmtAparato);

if (!$resAparato || mysqli_num_rows($resAparato) == 0) {
    echo "<script>
        alert('El aparato seleccionado no existe.');
        window.location='clases_usuario.php';
    </script>";
    exit();
}

$aparato = mysqli_fetch_assoc($resAparato);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fecha = $_POST['fecha'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];

    if (empty($fecha) || empty($hora_inicio) || empty($hora_fin)) {

        $error = "Completá todos los campos.";

    } elseif ($fecha < date("Y-m-d")) {

        $error = "No podés reservar una fecha pasada.";

    } elseif ($hora_fin <= $hora_inicio) {

        $error = "La hora de fin debe ser mayor a la hora de inicio.";

    } else {

        /*
            Verifica si ya existe una reserva Activa o Pendiente
            en ese mismo aparato y horario.
        */
        $sqlCheck = "SELECT COUNT(*) AS total 
                     FROM reservas
                     WHERE id_aparato = ?
                     AND fecha = ?
                     AND estado_reserva IN ('Activa', 'Pendiente')
                     AND hora_inicio < ?
                     AND hora_fin > ?";

        $stmtCheck = mysqli_prepare($conexion, $sqlCheck);
        mysqli_stmt_bind_param($stmtCheck, "isss", $id_aparato, $fecha, $hora_fin, $hora_inicio);
        mysqli_stmt_execute($stmtCheck);

        $resultadoCheck = mysqli_stmt_get_result($stmtCheck);
        $rowCheck = mysqli_fetch_assoc($resultadoCheck);

        if ($rowCheck['total'] > 0) {

            $error = "Ese aparato ya tiene una reserva o solicitud pendiente en ese horario. Elegí otro horario.";

        } else {

            /*
                IMPORTANTE:
                La reserva del cliente ahora queda Pendiente.
                El admin después la aprueba o la cancela.
            */
            $sqlReserva = "INSERT INTO reservas
            (id_cliente, id_aparato, fecha, hora_inicio, hora_fin, estado_reserva)
            VALUES (?, ?, ?, ?, ?, 'Pendiente')";

            $stmtReserva = mysqli_prepare($conexion, $sqlReserva);
            mysqli_stmt_bind_param($stmtReserva, "iisss", $id_cliente, $id_aparato, $fecha, $hora_inicio, $hora_fin);

            if (mysqli_stmt_execute($stmtReserva)) {
                echo "<script>
                    alert('Solicitud de reserva enviada correctamente. Quedará pendiente hasta que el administrador la apruebe.');
                    window.location='mis_reservas.php';
                </script>";
                exit();
            } else {
                $error = "Error al guardar la solicitud de reserva: " . mysqli_error($conexion);
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

<title>Reservar Turno - Warrior Gym</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

<style>
body {
    background:
        linear-gradient(rgba(0,0,0,.85), rgba(0,0,0,.92)),
        url('img/gym-bg.jpg');
    background-size: cover;
    background-position: center;
    min-height: 100vh;
}

.reserva-card {
    background: rgba(15, 15, 15, .96);
    border: 1px solid #dc3545;
    border-radius: 22px;
    padding: 35px;
}

.reserva-title {
    color: #dc3545;
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
    border-color: #dc3545;
    box-shadow: none;
}

.info-box {
    background: #111;
    border: 1px solid #222;
    border-radius: 15px;
    padding: 15px;
}
</style>

</head>

<body class="text-white">

<div class="container py-5">

    <div class="col-lg-6 col-md-8 mx-auto reserva-card shadow-lg">

        <div class="text-center mb-4">

            <h2 class="reserva-title">
                <i class="bi bi-calendar-check-fill"></i> Solicitar Reserva
            </h2>

            <p class="text-secondary">
                Seleccioná la fecha y el horario. La reserva quedará pendiente hasta que el administrador la apruebe.
            </p>

        </div>

        <div class="info-box mb-4 text-center">
            <p class="mb-1 text-secondary">Aparato seleccionado</p>
            <h5 class="mb-0 text-danger">
                <?= htmlspecialchars($aparato['nombre']) ?>
            </h5>
        </div>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger text-center">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label class="mb-1">Fecha</label>
            <input 
                type="date" 
                name="fecha" 
                class="form-control mb-3" 
                min="<?= date('Y-m-d') ?>"
                required>

            <label class="mb-1">Hora inicio</label>
            <select name="hora_inicio" class="form-select mb-3" required>
                <option value="">Seleccionar horario</option>
                <option value="06:00">06:00 AM</option>
                <option value="07:00">07:00 AM</option>
                <option value="08:00">08:00 AM</option>
                <option value="09:00">09:00 AM</option>
                <option value="10:00">10:00 AM</option>
                <option value="11:00">11:00 AM</option>
                <option value="12:00">12:00 PM</option>
                <option value="13:00">01:00 PM</option>
                <option value="14:00">02:00 PM</option>
                <option value="15:00">03:00 PM</option>
                <option value="16:00">04:00 PM</option>
                <option value="17:00">05:00 PM</option>
                <option value="18:00">06:00 PM</option>
                <option value="19:00">07:00 PM</option>
                <option value="20:00">08:00 PM</option>
                <option value="21:00">09:00 PM</option>
                <option value="22:00">10:00 PM</option>
            </select>

            <label class="mb-1">Hora fin</label>
            <select name="hora_fin" class="form-select mb-4" required>
                <option value="">Seleccionar horario</option>
                <option value="07:00">07:00 AM</option>
                <option value="08:00">08:00 AM</option>
                <option value="09:00">09:00 AM</option>
                <option value="10:00">10:00 AM</option>
                <option value="11:00">11:00 AM</option>
                <option value="12:00">12:00 PM</option>
                <option value="13:00">01:00 PM</option>
                <option value="14:00">02:00 PM</option>
                <option value="15:00">03:00 PM</option>
                <option value="16:00">04:00 PM</option>
                <option value="17:00">05:00 PM</option>
                <option value="18:00">06:00 PM</option>
                <option value="19:00">07:00 PM</option>
                <option value="20:00">08:00 PM</option>
                <option value="21:00">09:00 PM</option>
                <option value="22:00">10:00 PM</option>
                <option value="23:00">11:00 PM</option>
            </select>

            <button class="btn btn-danger w-100 py-2 fw-bold">
                <i class="bi bi-send-fill"></i> Enviar solicitud de reserva
            </button>

        </form>

        <div class="text-center mt-3">
            <a href="clases_usuario.php" class="text-white text-decoration-none">
                ← Volver a aparatos
            </a>
        </div>

        <div class="text-center mt-2">
            <a href="dashboard_usuario.php" class="text-secondary text-decoration-none">
                Volver al panel
            </a>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>