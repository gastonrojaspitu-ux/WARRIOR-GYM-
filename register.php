<?php
session_start();
include(__DIR__ . "/php/conexion.php");

$error = "";

/* VARIABLES PARA NO BORRAR DATOS SI HAY ERROR */
$nombre_usuario = "";
$email = "";
$id_tipo_documento = "";
$numero_documento = "";
$nombre = "";
$apellido = "";
$telefono = "";
$direccion = "";
$fecha_nacimiento = "";

/* CARGAR TIPOS DE DOCUMENTO */
$tipos_documento = mysqli_query($conexion, "SELECT * FROM tipo_documento ORDER BY descripcion");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    $id_tipo_documento = $_POST['id_tipo_documento'] ?? '';
    $numero_documento = trim($_POST['numero_documento'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';

    /* VALIDACIONES */
    if (
        $nombre_usuario == "" ||
        $email == "" ||
        $password == "" ||
        $confirmar == "" ||
        $id_tipo_documento == "" ||
        $numero_documento == "" ||
        $nombre == "" ||
        $apellido == ""
    ) {
        $error = "Completá todos los campos obligatorios.";
    } elseif (!preg_match("/^[a-zA-Z0-9_]{3,30}$/", $nombre_usuario)) {
        $error = "El usuario debe tener entre 3 y 30 caracteres. Solo letras, números o guion bajo.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El email no tiene un formato válido.";
    } elseif (!preg_match("/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/u", $nombre)) {
        $error = "El nombre solo puede contener letras.";
    } elseif (!preg_match("/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/u", $apellido)) {
        $error = "El apellido solo puede contener letras.";
    } elseif (!preg_match("/^[0-9]+$/", $numero_documento)) {
        $error = "El número de documento solo puede contener números.";
    } elseif ($telefono != "" && !preg_match("/^[0-9]+$/", $telefono)) {
        $error = "El teléfono solo puede contener números.";
    } elseif ($fecha_nacimiento != "" && $fecha_nacimiento > date("Y-m-d")) {
        $error = "La fecha de nacimiento no puede ser una fecha futura.";
    } elseif (strlen($password) < 4) {
        $error = "La contraseña debe tener al menos 4 caracteres.";
    } elseif ($password !== $confirmar) {
        $error = "Las contraseñas no coinciden.";
    } else {

        $password_segura = password_hash($password, PASSWORD_DEFAULT);

        if ($fecha_nacimiento == "") {
            $fecha_nacimiento = null;
        }

        mysqli_begin_transaction($conexion);

        try {

            /* VALIDAR USUARIO O EMAIL REPETIDO */
            $sqlCheckUsuario = "SELECT id_usuario FROM usuarios WHERE nombre = ? OR email = ?";
            $stmtCheckUsuario = mysqli_prepare($conexion, $sqlCheckUsuario);

            if (!$stmtCheckUsuario) {
                throw new Exception("Error al validar usuario: " . mysqli_error($conexion));
            }

            mysqli_stmt_bind_param($stmtCheckUsuario, "ss", $nombre_usuario, $email);
            mysqli_stmt_execute($stmtCheckUsuario);
            $resCheckUsuario = mysqli_stmt_get_result($stmtCheckUsuario);

            if (mysqli_num_rows($resCheckUsuario) > 0) {
                throw new Exception("El usuario o email ya está registrado.");
            }

            /*
                VALIDAR DOCUMENTO REPETIDO POR TIPO + NÚMERO.
                Ejemplo:
                DNI 3 y LC 3 pueden ser personas distintas.
                Pero DNI 3 y DNI 3 no.
            */
            $sqlCheckDoc = "SELECT id_cliente 
                            FROM clientes 
                            WHERE id_tipo_documento = ? 
                            AND numero_documento = ?";
            $stmtCheckDoc = mysqli_prepare($conexion, $sqlCheckDoc);

            if (!$stmtCheckDoc) {
                throw new Exception("Error al validar documento: " . mysqli_error($conexion));
            }

            mysqli_stmt_bind_param($stmtCheckDoc, "is", $id_tipo_documento, $numero_documento);
            mysqli_stmt_execute($stmtCheckDoc);
            $resCheckDoc = mysqli_stmt_get_result($stmtCheckDoc);

            if (mysqli_num_rows($resCheckDoc) > 0) {
                throw new Exception("Ya existe un cliente con ese tipo y número de documento.");
            }

            /* CREAR USUARIO */
            $sqlUsuario = "INSERT INTO usuarios 
            (nombre, email, password, rol, estado)
            VALUES (?, ?, ?, 'cliente', 'Activo')";

            $stmtUsuario = mysqli_prepare($conexion, $sqlUsuario);

            if (!$stmtUsuario) {
                throw new Exception("Error al preparar usuario: " . mysqli_error($conexion));
            }

            mysqli_stmt_bind_param($stmtUsuario, "sss", $nombre_usuario, $email, $password_segura);

            if (!mysqli_stmt_execute($stmtUsuario)) {
                throw new Exception("Error al crear usuario: " . mysqli_error($conexion));
            }

            $id_usuario = mysqli_insert_id($conexion);

            /* CREAR CLIENTE VINCULADO */
            $sqlCliente = "INSERT INTO clientes 
            (id_tipo_documento, numero_documento, nombre, apellido, telefono, email, direccion, fecha_nacimiento, fecha_registro, estado, usuario, contrasena, id_usuario)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), 'Activo', ?, ?, ?)";

            $stmtCliente = mysqli_prepare($conexion, $sqlCliente);

            if (!$stmtCliente) {
                throw new Exception("Error al preparar cliente: " . mysqli_error($conexion));
            }

            mysqli_stmt_bind_param(
                $stmtCliente,
                "isssssssssi",
                $id_tipo_documento,
                $numero_documento,
                $nombre,
                $apellido,
                $telefono,
                $email,
                $direccion,
                $fecha_nacimiento,
                $nombre_usuario,
                $password_segura,
                $id_usuario
            );

            if (!mysqli_stmt_execute($stmtCliente)) {
                throw new Exception("Error al crear cliente: " . mysqli_error($conexion));
            }

            mysqli_commit($conexion);

            header("Location: login.php?registro=ok");
            exit();

        } catch (Throwable $e) {
            mysqli_rollback($conexion);
            $error = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Registro - Warrior Gym</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

<style>
body {
    background:
        linear-gradient(rgba(0,0,0,.82), rgba(0,0,0,.9)),
        url('img/gym-bg.jpg');
    background-size: cover;
    background-position: center;
    min-height: 100vh;
}

.register-card {
    background: rgba(10,10,10,.96);
    border: 1px solid #dc3545;
    border-radius: 22px;
    padding: 35px;
}

.register-title {
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

.form-control::placeholder {
    color: #999;
}

.form-label {
    color: #ddd;
    font-size: 14px;
    margin-bottom: 4px;
}

.obligatorio {
    color: #dc3545;
    font-weight: bold;
}

.section-title {
    color: #dc3545;
    font-weight: 700;
    margin-top: 15px;
}

.text-small {
    font-size: 13px;
}
</style>

</head>

<body>

<div class="container py-5">

    <div class="col-lg-7 col-md-9 mx-auto register-card shadow-lg">

        <div class="text-center mb-4">
            <h2 class="register-title">
                <i class="bi bi-person-plus-fill"></i> Crear Cuenta
            </h2>
            <p class="text-secondary">
                Registrate para acceder a tus rutinas, reservas y compras.
            </p>
            <p class="text-secondary text-small">
                Los campos marcados con <span class="obligatorio">*</span> son obligatorios.
            </p>
        </div>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger text-center">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <h6 class="section-title">Datos de acceso</h6>

            <div class="row">

                <div class="col-md-6 mb-2">
                    <label class="form-label">Usuario <span class="obligatorio">*</span></label>
                    <input 
                        class="form-control" 
                        name="nombre_usuario" 
                        placeholder="Ej: gaston123"
                        value="<?= htmlspecialchars($nombre_usuario) ?>"
                        pattern="[A-Za-z0-9_]{3,30}"
                        title="Solo letras, números o guion bajo. Mínimo 3 caracteres."
                        required>
                </div>

                <div class="col-md-6 mb-2">
                    <label class="form-label">Email <span class="obligatorio">*</span></label>
                    <input 
                        class="form-control" 
                        type="email" 
                        name="email" 
                        placeholder="Ej: usuario@gmail.com"
                        value="<?= htmlspecialchars($email) ?>"
                        required>
                </div>

                <div class="col-md-6 mb-2">
                    <label class="form-label">Contraseña <span class="obligatorio">*</span></label>
                    <input 
                        class="form-control" 
                        type="password" 
                        name="password" 
                        placeholder="Mínimo 4 caracteres" 
                        minlength="4"
                        required>
                </div>

                <div class="col-md-6 mb-2">
                    <label class="form-label">Confirmar contraseña <span class="obligatorio">*</span></label>
                    <input 
                        class="form-control" 
                        type="password" 
                        name="confirmar" 
                        placeholder="Repetir contraseña" 
                        minlength="4"
                        required>
                </div>

            </div>

            <h6 class="section-title">Datos personales</h6>

            <div class="row">

                <div class="col-md-6 mb-2">
                    <label class="form-label">Nombre <span class="obligatorio">*</span></label>
                    <input 
                        class="form-control" 
                        name="nombre" 
                        placeholder="Solo letras"
                        value="<?= htmlspecialchars($nombre) ?>"
                        pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                        title="El nombre solo puede contener letras."
                        required>
                </div>

                <div class="col-md-6 mb-2">
                    <label class="form-label">Apellido <span class="obligatorio">*</span></label>
                    <input 
                        class="form-control" 
                        name="apellido" 
                        placeholder="Solo letras"
                        value="<?= htmlspecialchars($apellido) ?>"
                        pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                        title="El apellido solo puede contener letras."
                        required>
                </div>

                <div class="col-md-6 mb-2">
                    <label class="form-label">Tipo de documento <span class="obligatorio">*</span></label>
                    <select class="form-select" name="id_tipo_documento" required>
                        <option value="">Seleccionar tipo</option>

                        <?php while($tipo = mysqli_fetch_assoc($tipos_documento)): ?>
                            <option 
                                value="<?= $tipo['id_tipo_documento'] ?>"
                                <?= ($id_tipo_documento == $tipo['id_tipo_documento']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tipo['descripcion']) ?>
                            </option>
                        <?php endwhile; ?>

                    </select>
                </div>

                <div class="col-md-6 mb-2">
                    <label class="form-label">Número de documento <span class="obligatorio">*</span></label>
                    <input 
                        class="form-control" 
                        name="numero_documento" 
                        placeholder="Solo números"
                        value="<?= htmlspecialchars($numero_documento) ?>"
                        pattern="[0-9]+"
                        title="El documento solo puede contener números."
                        required>
                </div>

                <div class="col-md-6 mb-2">
                    <label class="form-label">Teléfono</label>
                    <input 
                        class="form-control" 
                        name="telefono" 
                        placeholder="Solo números"
                        value="<?= htmlspecialchars($telefono) ?>"
                        pattern="[0-9]*"
                        title="El teléfono solo puede contener números.">
                </div>

                <div class="col-md-6 mb-2">
                    <label class="form-label">Dirección</label>
                    <input 
                        class="form-control" 
                        name="direccion" 
                        placeholder="Dirección"
                        value="<?= htmlspecialchars($direccion) ?>">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Fecha de nacimiento</label>
                    <input 
    class="form-control" 
    type="date" 
    name="fecha_nacimiento"
    max="<?= date('Y-m-d') ?>"
    value="<?= htmlspecialchars($fecha_nacimiento ?? '') ?>">
                </div>

            </div>

            <button class="btn btn-danger w-100 py-2 fw-bold">
                Registrarse
            </button>

        </form>

        <div class="text-center mt-3">
            <a href="login.php" class="text-white text-decoration-none">
                ¿Ya tenés cuenta? Iniciar sesión
            </a>
        </div>

        <div class="text-center mt-2">
            <a href="index.html" class="text-secondary text-decoration-none">
    <i class="bi bi-arrow-left"></i> Volver a la página principal
</a>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>