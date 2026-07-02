<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . "/../php/conexion.php");

/* PROTEGER SOLO ADMIN */
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$error = "";

/* CARGAR CLIENTES */
$sqlClientes = "
    SELECT id_cliente, nombre, apellido
    FROM clientes
    WHERE estado = 'Activo'
    ORDER BY apellido, nombre
";

$clientes = mysqli_query($conexion, $sqlClientes);

if (!$clientes) {
    die("Error al cargar clientes: " . mysqli_error($conexion));
}

/* CARGAR APARATOS */
$sqlAparatos = "
    SELECT id_aparato, nombre
    FROM aparatos
    ORDER BY nombre
";

$aparatos = mysqli_query($conexion, $sqlAparatos);

if (!$aparatos) {
    die("Error al cargar aparatos: " . mysqli_error($conexion));
}

/* GUARDAR RESERVA */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_cliente = intval($_POST['id_cliente']);
    $id_aparato = intval($_POST['id_aparato']);
    $fecha = trim($_POST['fecha']);
    $hora_inicio = trim($_POST['hora_inicio']);
    $hora_fin = trim($_POST['hora_fin']);
    $estado_reserva = trim($_POST['estado_reserva']);

    if ($id_cliente <= 0 || $id_aparato <= 0 || empty($fecha) || empty($hora_inicio) || empty($hora_fin) || empty($estado_reserva)) {

        $error = "Completá todos los campos.";

    } elseif ($hora_inicio >= $hora_fin) {

        $error = "La hora de inicio debe ser menor a la hora fin.";

    } elseif ($estado_reserva != "Activa" && $estado_reserva != "Cancelada" && $estado_reserva != "Finalizada") {

        $error = "Estado de reserva no válido.";

    } else {

        /* VALIDAR CLIENTE */
        $sqlCliente = "SELECT id_cliente 
                       FROM clientes 
                       WHERE id_cliente = ?
                       AND estado = 'Activo'
                       LIMIT 1";

        $stmtCliente = mysqli_prepare($conexion, $sqlCliente);

        if (!$stmtCliente) {
            die("Error al preparar cliente: " . mysqli_error($conexion));
        }

        mysqli_stmt_bind_param($stmtCliente, "i", $id_cliente);
        mysqli_stmt_execute($stmtCliente);
        $resCliente = mysqli_stmt_get_result($stmtCliente);

        if (!$resCliente || mysqli_num_rows($resCliente) == 0) {

            $error = "El cliente seleccionado no está activo.";

        } else {

            /* VALIDAR SOLAPAMIENTO SOLO SI LA RESERVA VA A QUEDAR ACTIVA */
            if ($estado_reserva == "Activa") {

                $sqlCheck = "SELECT id_reserva 
                             FROM reservas
                             WHERE id_aparato = ?
                             AND fecha = ?
                             AND estado_reserva = 'Activa'
                             AND hora_inicio < ?
                             AND hora_fin > ?
                             LIMIT 1";

                $stmtCheck = mysqli_prepare($conexion, $sqlCheck);

                if (!$stmtCheck) {
                    die("Error al preparar verificación de horario: " . mysqli_error($conexion));
                }

                mysqli_stmt_bind_param($stmtCheck, "isss", $id_aparato, $fecha, $hora_fin, $hora_inicio);
                mysqli_stmt_execute($stmtCheck);
                $resCheck = mysqli_stmt_get_result($stmtCheck);

                if ($resCheck && mysqli_num_rows($resCheck) > 0) {
                    $error = "El aparato ya está reservado en ese horario.";
                }
            }

            if (empty($error)) {

                $sqlInsert = "INSERT INTO reservas 
                              (id_cliente, id_aparato, fecha, hora_inicio, hora_fin, estado_reserva)
                              VALUES (?, ?, ?, ?, ?, ?)";

                $stmtInsert = mysqli_prepare($conexion, $sqlInsert);

                if (!$stmtInsert) {
                    die("Error al preparar reserva: " . mysqli_error($conexion));
                }

                mysqli_stmt_bind_param(
                    $stmtInsert,
                    "iissss",
                    $id_cliente,
                    $id_aparato,
                    $fecha,
                    $hora_inicio,
                    $hora_fin,
                    $estado_reserva
                );

                if (mysqli_stmt_execute($stmtInsert)) {

                    echo "<script>
                        alert('Reserva guardada correctamente.');
                        window.location='reservas.php';
                    </script>";
                    exit();

                } else {

                    $error = "Error al guardar reserva: " . mysqli_error($conexion);
                }
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

<title>Nueva Reserva - Admin Warrior Gym</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    font-family: Arial, sans-serif;
    background: #111;
    color: white;
}

.header {
    background: #d40000;
    padding: 25px;
    text-align: center;
}

.header h1 {
    margin: 0;
    font-weight: bold;
}

.form-box {
    max-width: 600px;
    margin: 40px auto;
    background: #1c1c1c;
    padding: 25px;
    border-radius: 15px;
    border: 1px solid #333;
    box-shadow: 0 0 20px rgba(220,53,69,0.20);
}

.form-control,
.form-select {
    background: #111;
    color: white;
    border: 1px solid #333;
}

.form-control:focus,
.form-select:focus {
    background: #111;
    color: white;
    border-color: #dc3545;
    box-shadow: none;
}

.btn-warrior {
    background: #d40000;
    color: white;
    border: none;
}

.btn-warrior:hover {
    background: #ff1a1a;
    color: white;
}
</style>
</head>

<body>

<div class="header">
    <h1>📅 Nueva Reserva Admin</h1>
</div>

<div class="container">

    <div class="form-box">

        <div class="mb-3">
            <a href="reservas.php" class="btn btn-outline-light btn-sm">
                ⬅ Volver a Reservas
            </a>

            <a href="dashboard.php" class="btn btn-outline-light btn-sm">
                🏠 Panel
            </a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

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

            <label class="mb-1">Aparato</label>
            <select name="id_aparato" class="form-select mb-3" required>
                <option value="">Seleccionar aparato</option>

                <?php while($a = mysqli_fetch_assoc($aparatos)): ?>
                    <option value="<?= $a['id_aparato'] ?>">
                        <?= htmlspecialchars($a['nombre']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label class="mb-1">Fecha</label>
            <input 
                type="date" 
                name="fecha" 
                class="form-control mb-3"
                value="<?= date('Y-m-d') ?>"
                required>

            <label class="mb-1">Hora inicio</label>
            <select name="hora_inicio" class="form-select mb-3" required>
                <option value="">Seleccionar hora</option>
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
            <select name="hora_fin" class="form-select mb-3" required>
                <option value="">Seleccionar hora</option>
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

            <label class="mb-1">Estado</label>
            <select name="estado_reserva" class="form-select mb-3" required>
                <option value="Activa">Activa</option>
                <option value="Cancelada">Cancelada</option>
                <option value="Finalizada">Finalizada</option>
            </select>

            <button type="submit" class="btn btn-warrior w-100">
                Guardar Reserva
            </button>

        </form>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>