<?php
session_start();

include(__DIR__ . "/php/conexion.php");

/* PROTEGER SOLO CLIENTE */
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'cliente') {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['id_cliente']) || empty($_SESSION['id_cliente'])) {
    echo "<script>
        alert('Tu cuenta no está vinculada a un cliente. Consultá al administrador.');
        window.location='login.php';
    </script>";
    exit();
}

$id_cliente = intval($_SESSION['id_cliente']);

$mensaje = "";
$error = "";

/* FUNCIÓN PARA VER SI EXISTE UNA COLUMNA */
function existeColumna($conexion, $tabla, $columna) {
    $tabla = mysqli_real_escape_string($conexion, $tabla);
    $columna = mysqli_real_escape_string($conexion, $columna);

    $sql = "SHOW COLUMNS FROM `$tabla` LIKE '$columna'";
    $res = mysqli_query($conexion, $sql);

    return ($res && mysqli_num_rows($res) > 0);
}

/* VERIFICAR SI solicitudes_membresia TIENE id_cliente */
$tiene_id_cliente = existeColumna($conexion, "solicitudes_membresia", "id_cliente");

/* TRAER DATOS REALES DEL CLIENTE LOGUEADO */
$sqlCliente = "SELECT nombre, apellido, email, telefono 
               FROM clientes 
               WHERE id_cliente = ?
               LIMIT 1";

$stmtCliente = mysqli_prepare($conexion, $sqlCliente);

if (!$stmtCliente) {
    die("Error al preparar cliente: " . mysqli_error($conexion));
}

mysqli_stmt_bind_param($stmtCliente, "i", $id_cliente);
mysqli_stmt_execute($stmtCliente);

$resCliente = mysqli_stmt_get_result($stmtCliente);

if (!$resCliente || mysqli_num_rows($resCliente) == 0) {
    echo "<script>
        alert('No se encontraron tus datos de cliente.');
        window.location='dashboard_usuario.php';
    </script>";
    exit();
}

$cliente = mysqli_fetch_assoc($resCliente);

$nombre = $cliente['nombre'];
$apellido = $cliente['apellido'];
$email = $cliente['email'];
$telefono = $cliente['telefono'];

/* ENVIAR SOLICITUD */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $plan = trim($_POST['plan'] ?? '');

    $planes_validos = ["Basico", "Premium", "Elite"];

    if ($plan == "") {

        $error = "Seleccioná un plan.";

    } elseif (!in_array($plan, $planes_validos)) {

        $error = "El plan seleccionado no es válido.";

    } else {

        /*
            EVITAR SOLICITUD PENDIENTE DUPLICADA
            Si la tabla tiene id_cliente, controla por id_cliente.
            Si no tiene id_cliente, controla por email.
        */
        if ($tiene_id_cliente) {

            $sqlCheck = "SELECT COUNT(*) AS total 
                         FROM solicitudes_membresia
                         WHERE id_cliente = ?
                         AND LOWER(TRIM(estado)) = 'pendiente'";

            $stmtCheck = mysqli_prepare($conexion, $sqlCheck);

            if (!$stmtCheck) {
                die("Error al preparar verificación: " . mysqli_error($conexion));
            }

            mysqli_stmt_bind_param($stmtCheck, "i", $id_cliente);

        } else {

            $sqlCheck = "SELECT COUNT(*) AS total 
                         FROM solicitudes_membresia
                         WHERE LOWER(TRIM(email)) = LOWER(TRIM(?))
                         AND LOWER(TRIM(estado)) = 'pendiente'";

            $stmtCheck = mysqli_prepare($conexion, $sqlCheck);

            if (!$stmtCheck) {
                die("Error al preparar verificación: " . mysqli_error($conexion));
            }

            mysqli_stmt_bind_param($stmtCheck, "s", $email);
        }

        mysqli_stmt_execute($stmtCheck);
        $resCheck = mysqli_stmt_get_result($stmtCheck);
        $rowCheck = mysqli_fetch_assoc($resCheck);

        if ($rowCheck['total'] > 0) {

            $error = "Ya tenés una solicitud de membresía pendiente. Esperá la respuesta del administrador.";

        } else {

            /*
                INSERTAR SOLICITUD
                Se adapta a tu tabla:
                - Si tiene id_cliente, lo guarda.
                - Si no tiene id_cliente, guarda solo nombre/email/teléfono.
            */
            if ($tiene_id_cliente) {

                $sqlInsert = "INSERT INTO solicitudes_membresia
                              (id_cliente, nombre, apellido, email, telefono, plan_solicitado, fecha_solicitud, estado)
                              VALUES
                              (?, ?, ?, ?, ?, ?, CURDATE(), 'Pendiente')";

                $stmtInsert = mysqli_prepare($conexion, $sqlInsert);

                if (!$stmtInsert) {
                    die("Error al preparar solicitud: " . mysqli_error($conexion));
                }

                mysqli_stmt_bind_param(
                    $stmtInsert,
                    "isssss",
                    $id_cliente,
                    $nombre,
                    $apellido,
                    $email,
                    $telefono,
                    $plan
                );

            } else {

                $sqlInsert = "INSERT INTO solicitudes_membresia
                              (nombre, apellido, email, telefono, plan_solicitado, fecha_solicitud, estado)
                              VALUES
                              (?, ?, ?, ?, ?, CURDATE(), 'Pendiente')";

                $stmtInsert = mysqli_prepare($conexion, $sqlInsert);

                if (!$stmtInsert) {
                    die("Error al preparar solicitud: " . mysqli_error($conexion));
                }

                mysqli_stmt_bind_param(
                    $stmtInsert,
                    "sssss",
                    $nombre,
                    $apellido,
                    $email,
                    $telefono,
                    $plan
                );
            }

            if (mysqli_stmt_execute($stmtInsert)) {

                $mensaje = "Solicitud enviada correctamente. Quedará pendiente hasta que el administrador la revise.";

            } else {

                $error = "No se pudo guardar la solicitud: " . mysqli_error($conexion);
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

<title>Solicitar Membresía - Warrior Gym</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #0f0f0f;
    color: white;
}

.card-box {
    background: #1c1c1c;
    padding: 30px;
    border-radius: 15px;
    border: 1px solid #dc3545;
    box-shadow: 0 0 20px rgba(220,53,69,0.25);
}

.plan {
    background: #222;
    padding: 15px;
    border-radius: 10px;
    cursor: pointer;
    transition: 0.3s;
    border: 1px solid #333;
    height: 100%;
}

.plan:hover {
    transform: scale(1.03);
    border-color: #dc3545;
}

.plan h5 {
    color: #dc3545;
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

.form-control::placeholder {
    color: #999;
}
</style>

</head>

<body>

<div class="container py-5">

    <h2 class="text-center text-danger mb-3">💳 Solicitar Membresía</h2>

    <p class="text-center text-secondary mb-4">
        Hola, <?= htmlspecialchars($_SESSION['nombre']) ?>. Elegí el plan que querés solicitar.
    </p>

    <?php if ($mensaje != ""): ?>
        <div class="alert alert-success text-center">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <?php if ($error != ""): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="card-box mx-auto" style="max-width:850px;">

        <form method="POST">

            <div class="row">

                <div class="col-md-6 mb-3">
                    <input 
                        type="text" 
                        class="form-control" 
                        value="<?= htmlspecialchars($nombre) ?>"
                        readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <input 
                        type="text" 
                        class="form-control" 
                        value="<?= htmlspecialchars($apellido) ?>"
                        readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <input 
                        type="email" 
                        class="form-control" 
                        value="<?= htmlspecialchars($email) ?>"
                        readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <input 
                        type="text" 
                        class="form-control" 
                        value="<?= htmlspecialchars($telefono) ?>"
                        readonly>
                </div>

            </div>

            <h5 class="mt-3 mb-3 text-danger">Elegí tu plan</h5>

            <div class="row g-3">

                <div class="col-md-4">
                    <label class="plan w-100 text-center">
                        <input type="radio" name="plan" value="Basico" required>
                        <h5>Básico</h5>
                        <p class="text-secondary">Acceso general</p>
                        <p>$18.000</p>
                    </label>
                </div>

                <div class="col-md-4">
                    <label class="plan w-100 text-center">
                        <input type="radio" name="plan" value="Premium">
                        <h5>Premium</h5>
                        <p class="text-secondary">Gimnasio + clases</p>
                        <p>$28.000</p>
                    </label>
                </div>

                <div class="col-md-4">
                    <label class="plan w-100 text-center">
                        <input type="radio" name="plan" value="Elite">
                        <h5>Elite</h5>
                        <p class="text-secondary">Plan completo</p>
                        <p>$45.990</p>
                    </label>
                </div>

            </div>

            <button class="btn btn-danger w-100 mt-4">
                Enviar Solicitud
            </button>

        </form>

        <a href="cliente_membresia.php" class="btn btn-outline-light w-100 mt-3">
            Ver mi membresía
        </a>

        <a href="dashboard_usuario.php" class="btn btn-secondary w-100 mt-3">
            Volver al panel
        </a>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>