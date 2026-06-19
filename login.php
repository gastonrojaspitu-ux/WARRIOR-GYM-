<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include(__DIR__ . "/php/conexion.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario = $_POST['usuario'];
    $password = $_POST['contrasena'];

    $sql = "SELECT * FROM usuarios
            WHERE nombre='$usuario'
            AND password='$password'
            AND estado='Activo'";

    $result = mysqli_query($conexion, $sql);

    if (!$result) {
        die("ERROR SQL: " . mysqli_error($conexion));
    }

    if ($row = mysqli_fetch_assoc($result)) {

        $_SESSION['id_usuario'] = $row['id_usuario'];
        $_SESSION['nombre'] = $row['nombre'];
        $_SESSION['rol'] = $row['rol'];

        if ($row['rol'] == 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: tienda.php");
        }

        exit();
    }

    $error = "Usuario o contraseña incorrectos";
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

</head>

<body>

<section class="login-section">

    <div class="login-overlay"></div>

    <div class="container">

        <div class="row justify-content-center align-items-center min-vh-100">

            <div class="col-xl-4 col-lg-5 col-md-7">

                <div class="login-card shadow-lg">

                    <div class="text-center mb-4">

                        <div class="brand-badge mb-3">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>

                        <h1 class="login-title">
                            WARRIOR GYM
                        </h1>

                        <p class="login-subtitle">
                            Controla tu entrenamiento, compras y progreso desde un solo lugar.
                        </p>

                    </div>

                    <?php if(!empty($error)): ?>

                        <div class="alert alert-danger text-center">
                            <?php echo $error; ?>
                        </div>

                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3 login-field">

                            <input
                                type="text"
                                name="usuario"
                                class="form-control"
                                placeholder="Usuario"
                                required>

                        </div>

                        <div class="mb-4 login-field">

                            <input
                                type="password"
                                name="contrasena"
                                class="form-control"
                                placeholder="Contraseña"
                                required>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-danger w-100 login-btn">

                            Ingresar

                        </button>

                    </form>

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