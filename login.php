<?php
session_start();
include(__DIR__ . "/php/conexion.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario = trim($_POST['usuario']);
    $contrasena = $_POST['contrasena'];

    $sql = "SELECT 
                u.id_usuario,
                u.nombre,
                u.email,
                u.password,
                u.rol,
                u.estado,
                c.id_cliente
            FROM usuarios u
            LEFT JOIN clientes c ON u.id_usuario = c.id_usuario
            WHERE (u.nombre = ? OR u.email = ?)
            AND u.estado = 'Activo'
            LIMIT 1";

    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $usuario, $usuario);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultado)) {

        $password_bd = $row['password'];

        $password_ok = password_verify($contrasena, $password_bd) || $contrasena === $password_bd;

        if ($password_ok) {

            if ($row['rol'] == 'cliente' && empty($row['id_cliente'])) {
                $error = "Tu cuenta no está vinculada a un cliente. Consultá al administrador.";
            } else {

                $_SESSION['id_usuario'] = $row['id_usuario'];
                $_SESSION['nombre'] = $row['nombre'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['rol'] = $row['rol'];
                $_SESSION['id_cliente'] = $row['id_cliente'];

                if ($row['rol'] == 'admin') {
                    header("Location: admin/dashboard.php");
                    exit();
                }

                if ($row['rol'] == 'cliente') {
                    header("Location: dashboard_usuario.php");
                    exit();
                }

                $error = "Rol de usuario no válido.";
            }

        } else {
            $error = "Usuario o contraseña incorrectos.";
        }

    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login - Warrior Gym</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="css/style.css">

<style>
body {
    background: #080808;
}

.login-section {
    min-height: 100vh;
    background:
        linear-gradient(rgba(0,0,0,.75), rgba(0,0,0,.85)),
        url('img/gym-bg.jpg');
    background-size: cover;
    background-position: center;
}

.login-card {
    background: rgba(10,10,10,.95);
    border: 1px solid #dc3545;
    border-radius: 22px;
    padding: 35px;
}

.brand-badge {
    width: 70px;
    height: 70px;
    margin: auto;
    border-radius: 50%;
    background: #dc3545;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: white;
}

.login-title {
    color: #dc3545;
    font-weight: 800;
    letter-spacing: 2px;
}

.login-subtitle {
    color: #bbb;
    font-size: 15px;
}

.form-control {
    background: #111;
    border: 1px solid #333;
    color: white;
    padding: 13px;
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

.login-btn {
    padding: 12px;
    font-weight: 700;
}
</style>

</head>

<body>

<section class="login-section">

    <div class="container">

        <div class="row justify-content-center align-items-center min-vh-100">

            <div class="col-xl-4 col-lg-5 col-md-7">

                <div class="login-card shadow-lg">

                    <div class="text-center mb-4">

                        <div class="brand-badge mb-3">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>

                        <h1 class="login-title">WARRIOR GYM</h1>

                        <p class="login-subtitle">
                            Ingresá con tu usuario o email.
                        </p>

                    </div>

                    <?php if(!empty($error)): ?>
                        <div class="alert alert-danger text-center">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <input
                                type="text"
                                name="usuario"
                                class="form-control"
                                placeholder="Usuario o email"
                                required>
                        </div>

                        <div class="mb-4">
                            <input
                                type="password"
                                name="contrasena"
                                class="form-control"
                                placeholder="Contraseña"
                                required>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 login-btn">
                            Ingresar
                        </button>

                    </form>

                    <div class="text-center mt-3">
                        <a href="register.php" class="text-white text-decoration-none">
                            ¿No tenés cuenta? Registrarse
                        </a>
                    </div>

                    <div class="text-center mt-2">
                        <a href="recuperar_password.php" class="text-warning text-decoration-none">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>

                    <div class="text-center mt-4">
                        <a href="index.html" class="text-white text-decoration-none">
                            ← Volver a Warrior Gym
                        </a>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>