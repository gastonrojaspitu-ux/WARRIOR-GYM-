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

/* VALIDAR ID */
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: reservas.php");
    exit();
}

$id_reserva = intval($_GET['id']);

if ($id_reserva <= 0) {
    echo "<script>
        alert('Reserva no válida.');
        window.location='reservas.php';
    </script>";
    exit();
}

/* CANCELAR SOLO RESERVAS PENDIENTES O ACTIVAS */
$sql = "UPDATE reservas 
        SET estado_reserva = 'Cancelada'
        WHERE id_reserva = ?
        AND estado_reserva IN ('Pendiente', 'Activa')";

$stmt = mysqli_prepare($conexion, $sql);

if (!$stmt) {
    die("Error prepare: " . mysqli_error($conexion));
}

mysqli_stmt_bind_param($stmt, "i", $id_reserva);

if (!mysqli_stmt_execute($stmt)) {
    die("Error al cancelar reserva: " . mysqli_error($conexion));
}

if (mysqli_stmt_affected_rows($stmt) > 0) {
    echo "<script>
        alert('Reserva cancelada correctamente.');
        window.location='reservas.php';
    </script>";
    exit();
} else {
    echo "<script>
        alert('No se pudo cancelar la reserva. Puede que ya esté cancelada o finalizada.');
        window.location='reservas.php';
    </script>";
    exit();
}
?>