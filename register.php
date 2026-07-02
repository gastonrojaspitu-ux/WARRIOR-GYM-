<?php
session_start();
include(__DIR__ . "/php/conexion.php");

$error = "";

/* CARGAR TIPOS DE DOCUMENTO */
$tipos_documento = mysqli_query($conexion, "SELECT * FROM tipo_documento ORDER BY descripcion");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre_usuario = trim($_POST['nombre_usuario']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmar = $_POST['confirmar'];

    $id_tipo_documento = $_POST['id_tipo_documento'];
    $numero_documento = trim($_POST['numero_documento']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];

    if ($password !== $confirmar) {
        $error = "Las contraseñas no coinciden.";
    } else {

        $password_segura = password_hash($password, PASSWORD_DEFAULT);

        mysqli_begin_transaction($conexion);

        try {

            /* VALIDAR USUARIO O EMAIL REPETIDO */
            $sqlCheckUsuario = "SELECT id_usuario FROM usuarios WHERE nombre = ? OR email = ?";
            $stmtCheckUsuario = mysqli_prepare($conexion, $sqlCheckUsuario);
            mysqli_stmt_bind_param($stmtCheckUsuario, "ss", $nombre_usuario, $email);
            mysqli_stmt_execute($stmtCheckUsuario);
            $resCheckUsuario = mysqli_stmt_get_result($stmtCheckUsuario);

            if (mysqli_num_rows($resCheckUsuario) > 0) {
                throw new Exception("El usuario o email ya está registrado.");
            }

            /* VALIDAR DOCUMENTO REPETIDO */
            $sqlCheckDoc = "SELECT id_cliente FROM clientes WHERE numero_documento = ?";
            $stmtCheckDoc = mysqli_prepare($conexion, $sqlCheckDoc);
            mysqli_stmt_bind_param($stmtCheckDoc, "s", $numero_documento);
            mysqli_stmt_execute($stmtCheckDoc);
            $resCheckDoc = mysqli_stmt_get_result($stmtCheckDoc);

            if (mysqli_num_rows($resCheckDoc) > 0) {
                throw new Exception("El documento ya está registrado.");
            }

            /* CREAR USUARIO */
            $sqlUsuario = "INSERT INTO usuarios 
            (nombre, email, password, rol, estado)
            VALUES (?, ?, ?, 'cliente', 'Activo')";

            $stmtUsuario = mysqli_prepare($conexion, $sqlUsuario);
            mysqli_stmt_bind_param($stmtUsuario, "sss", $nombre_usuario, $email, $password_segura);
            mysqli_stmt_execute($stmtUsuario);

            $id_usuario = mysqli_insert_id($conexion);

            /* CREAR CLIENTE VINCULADO */
            $sqlCliente = "INSERT INTO clientes 
            (id_tipo_documento, numero_documento, nombre, apellido, telefono, email, direccion, fecha_nacimiento, fecha_registro, estado, usuario, contrasena, id_usuario)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), 'Activo', ?, ?, ?)";

            $stmtCliente = mysqli_prepare($conexion, $sqlCliente);

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

            mysqli_stmt_execute($stmtCliente);

            mysqli_commit($conexion);

            echo "<script>
                alert('Cuenta creada correctamente. Ahora podés iniciar sesión.');
                window.location='login.php';
            </script>";
            exit();

        } catch (Exception $e) {
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

.section-title {
    color: #dc3545;
    font-weight: 700;
    margin-top: 15px;
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
        </div>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger text-center">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <h6 class="section-title">Datos de acceso</h6>

            <div class="row">

                <div class="col-md-6 mb-2">
                    <input class="form-control" name="nombre_usuario" placeholder="Usuario" required>
                </div>

                <div class="col-md-6 mb-2">
                    <input class="form-control" type="email" name="email" placeholder="Email" required>
                </div>

                <div class="col-md-6 mb-2">
                    <input class="form-control" type="password" name="password" placeholder="Contraseña" required>
                </div>

                <div class="col-md-6 mb-2">
                    <input class="form-control" type="password" name="confirmar" placeholder="Confirmar contraseña" required>
                </div>

            </div>

            <h6 class="section-title">Datos personales</h6>

            <div class="row">

                <div class="col-md-6 mb-2">
                    <input class="form-control" name="nombre" placeholder="Nombre" required>
                </div>

                <div class="col-md-6 mb-2">
                    <input class="form-control" name="apellido" placeholder="Apellido" required>
                </div>

                <div class="col-md-6 mb-2">
                    <select class="form-select" name="id_tipo_documento" required>
                        <option value="">Tipo de documento</option>

                        <?php while($tipo = mysqli_fetch_assoc($tipos_documento)): ?>
                            <option value="<?= $tipo['id_tipo_documento'] ?>">
                                <?= $tipo['descripcion'] ?>
                            </option>
                        <?php endwhile; ?>

                    </select>
                </div>

                <div class="col-md-6 mb-2">
                    <input class="form-control" name="numero_documento" placeholder="Número de documento" required>
                </div>

                <div class="col-md-6 mb-2">
                    <input class="form-control" name="telefono" placeholder="Teléfono">
                </div>

                <div class="col-md-6 mb-2">
                    <input class="form-control" name="direccion" placeholder="Dirección">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="text-secondary mb-1">Fecha de nacimiento</label>
                    <input class="form-control" type="date" name="fecha_nacimiento">
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

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>