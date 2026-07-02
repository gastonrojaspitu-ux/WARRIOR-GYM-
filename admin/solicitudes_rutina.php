<?php
session_start();
include("../php/conexion.php");

/* 🔒 PROTECCIÓN ADMIN */
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

/* 📌 CONSULTA CORRECTA CON RELACIÓN REAL */
$sql = "SELECT 
            sr.id_solicitud,
            sr.descripcion,
            sr.estado,
            sr.fecha_solicitud,
            c.id_cliente,
            c.nombre,
            c.apellido
        FROM solicitudes_rutina sr
        INNER JOIN clientes c 
        ON sr.id_cliente = c.id_cliente
        ORDER BY sr.id_solicitud DESC";

$resultado = mysqli_query($conexion, $sql);

if (!$resultado) {
    die("Error SQL: " . mysqli_error($conexion));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Admin - Solicitudes de Rutina</title>

<style>
body{
    background:#111;
    color:white;
    font-family:Arial;
}

.container{
    width:90%;
    margin:auto;
}

.card{
    background:#1c1c1c;
    padding:15px;
    margin:10px 0;
    border-radius:10px;
}

.btn{
    padding:8px 12px;
    margin-right:5px;
    border:none;
    cursor:pointer;
}

.aprobar{background:green;color:white;}
.rechazar{background:red;color:white;}
</style>
</head>

<body>

<h1 align="center">📋 Solicitudes de Rutina</h1>

<div class="container">

<?php while($row = mysqli_fetch_assoc($resultado)) { ?>

    <div class="card">

        <!-- 👤 CLIENTE CORRECTO -->
        <h3>
            👤 <?= $row['nombre'] . " " . $row['apellido'] ?>
        </h3>

        <!-- 📌 SOLICITUD -->
        <p><?= nl2br($row['descripcion']) ?></p>

        <p>📅 <?= $row['fecha_solicitud'] ?></p>

        <p>
            Estado: 
            <b><?= $row['estado'] ?></b>
        </p>

        <!-- 🔘 BOTONES -->
        <a class="btn aprobar" href="aprobar_rutina.php?id=<?= $row['id_solicitud'] ?>">
            Aprobar
        </a>

        <a class="btn rechazar" href="rechazar_rutina.php?id=<?= $row['id_solicitud'] ?>">
            Rechazar
        </a>

    </div>

<?php } ?>

</div>

</body>
</html>