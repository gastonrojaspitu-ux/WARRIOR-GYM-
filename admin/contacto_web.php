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

$sql = "SELECT * FROM contacto_web ORDER BY fecha_envio DESC";
$resultado = mysqli_query($conexion, $sql);

if (!$resultado) {
    die("Error SQL: " . mysqli_error($conexion));
}

function formatoFecha($fecha) {
    return date("d/m/Y H:i", strtotime($fecha));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Mensajes de Contacto - Warrior Gym</title>

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

.contenido {
    padding: 30px;
}

.table-box {
    background: #1c1c1c;
    padding: 20px;
    border-radius: 15px;
    border: 1px solid #333;
}

.table-dark th {
    background: #d40000;
    color: white;
}
</style>
</head>

<body>

<div class="header">
    <h1>📩 Mensajes de Contacto</h1>
</div>

<div class="contenido">

    <div class="mb-4">
        <a href="dashboard.php" class="btn btn-outline-light">
            ⬅ Volver al Panel
        </a>
    </div>

    <div class="table-box">

        <h3 class="text-danger mb-4">Consultas recibidas</h3>

        <?php if (mysqli_num_rows($resultado) > 0): ?>

            <div class="table-responsive">

                <table class="table table-dark table-hover text-center align-middle">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Asunto</th>
                            <th>Mensaje</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php while($fila = mysqli_fetch_assoc($resultado)): ?>

                            <tr>
                                <td><?= $fila['id_contacto'] ?></td>

                                <td>
                                    <?= formatoFecha($fila['fecha_envio']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($fila['nombre']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($fila['email']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($fila['asunto']) ?>
                                </td>

                                <td style="max-width:350px; text-align:left;">
                                    <?= nl2br(htmlspecialchars($fila['mensaje'])) ?>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <p class="text-secondary text-center">
                No hay mensajes de contacto todavía.
            </p>

        <?php endif; ?>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>