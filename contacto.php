<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . "/php/conexion.php");

$mensaje_ok = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $asunto = trim($_POST['asunto']);
    $mensaje = trim($_POST['mensaje']);

    if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje)) {

        $error = "Completá todos los campos.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Ingresá un email válido.";

    } else {

        $sql = "INSERT INTO contacto_web 
                (nombre, email, asunto, mensaje)
                VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $nombre, $email, $asunto, $mensaje);

        if (mysqli_stmt_execute($stmt)) {
            $mensaje_ok = "Mensaje enviado correctamente. Nos comunicaremos pronto.";
        } else {
            $error = "Error al enviar mensaje: " . mysqli_error($conexion);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Contacto - Warrior Gym</title>

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

.header h1 {
    margin: 0;
    font-weight: bold;
}

.form-box {
    max-width: 650px;
    margin: 40px auto;
    background: #1c1c1c;
    padding: 25px;
    border-radius: 15px;
    border: 1px solid #333;
    box-shadow: 0 0 20px rgba(220,53,69,0.20);
}

.form-control {
    background: #111;
    color: white;
    border: 1px solid #333;
}

.form-control:focus {
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
    <h1>📩 Contacto - Warrior Gym</h1>
</div>

<div class="container">

    <div class="form-box">

        <div class="mb-3">
            <a href="index.php" class="btn btn-outline-light btn-sm">
                ⬅ Volver al inicio
            </a>
        </div>

        <?php if (!empty($mensaje_ok)): ?>
            <div class="alert alert-success text-center">
                <?= htmlspecialchars($mensaje_ok) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label class="mb-1">Nombre</label>
            <input 
                type="text" 
                name="nombre" 
                class="form-control mb-3" 
                placeholder="Tu nombre"
                required>

            <label class="mb-1">Email</label>
            <input 
                type="email" 
                name="email" 
                class="form-control mb-3" 
                placeholder="tuemail@gmail.com"
                required>

            <label class="mb-1">Asunto</label>
            <input 
                type="text" 
                name="asunto" 
                class="form-control mb-3" 
                placeholder="Ej: Consulta por membresía"
                required>

            <label class="mb-1">Mensaje</label>
            <textarea 
                name="mensaje" 
                class="form-control mb-3" 
                rows="5"
                placeholder="Escribí tu consulta..."
                required></textarea>

            <button type="submit" class="btn btn-warrior w-100">
                Enviar mensaje
            </button>

        </form>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>