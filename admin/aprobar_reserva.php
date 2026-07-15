<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . "/../php/conexion.php");
date_default_timezone_set("America/Argentina/Buenos_Aires");

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

/* TRAER RESERVA */
$sqlReserva = "SELECT * FROM reservas WHERE id_reserva = ? LIMIT 1";

$stmtReserva = mysqli_prepare($conexion, $sqlReserva);
mysqli_stmt_bind_param($stmtReserva, "i", $id_reserva);
mysqli_stmt_execute($stmtReserva);

$resReserva = mysqli_stmt_get_result($stmtReserva);

if (!$resReserva || mysqli_num_rows($resReserva) == 0) {
    echo "<script>
        alert('Reserva no encontrada.');
        window.location='reservas.php';
    </script>";
    exit();
}

$reserva = mysqli_fetch_assoc($resReserva);
/* VALIDAR QUE LA RESERVA ESTÉ PENDIENTE */
if ($reserva['estado_reserva'] != 'Pendiente') {
    echo "<script>
        alert('Solo se pueden aprobar reservas pendientes.');
        window.location='reservas.php';
    </script>";
    exit();
}

/* NO APROBAR RESERVAS DE FECHA U HORARIO PASADO */
if ($reserva['fecha'] < date("Y-m-d")) {
    echo "<script>
        alert('No se puede aprobar una reserva con fecha pasada.');
        window.location='reservas.php';
    </script>";
    exit();
}

if ($reserva['fecha'] == date("Y-m-d") && $reserva['hora_inicio'] <= date("H:i")) {
    echo "<script>
        alert('No se puede aprobar una reserva con horario pasado.');
        window.location='reservas.php';
    </script>";
    exit();
}

/* VERIFICAR SOLAPAMIENTO CON OTRA RESERVA ACTIVA */
$sqlCheck = "SELECT id_reserva 
             FROM reservas
             WHERE id_aparato = ?
             AND fecha = ?
             AND estado_reserva = 'Activa'
             AND id_reserva <> ?
             AND hora_inicio < ?
             AND hora_fin > ?
             LIMIT 1";

$stmtCheck = mysqli_prepare($conexion, $sqlCheck);

mysqli_stmt_bind_param(
    $stmtCheck,
    "isiss",
    $reserva['id_aparato'],
    $reserva['fecha'],
    $id_reserva,
    $reserva['hora_fin'],
    $reserva['hora_inicio']
);

mysqli_stmt_execute($stmtCheck);
$resCheck = mysqli_stmt_get_result($stmtCheck);

if ($resCheck && mysqli_num_rows($resCheck) > 0) {
    echo "<script>
        alert('No se puede aprobar. Ya existe una reserva activa en ese horario.');
        window.location='reservas.php';
    </script>";
    exit();
}

/* APROBAR RESERVA */
$sqlUpdate = $sqlUpdate = "UPDATE reservas 
              SET estado_reserva = 'Activa'
              WHERE id_reserva = ?
              AND estado_reserva = 'Pendiente'";
$stmtUpdate = mysqli_prepare($conexion, $sqlUpdate);
mysqli_stmt_bind_param($stmtUpdate, "i", $id_reserva);

if (mysqli_stmt_execute($stmtUpdate)) {
    echo "<script>
        alert('Reserva aprobada correctamente.');
        window.location='reservas.php';
    </script>";
    exit();
} else {
    echo "<script>
        alert('Error al aprobar reserva.');
        window.location='reservas.php';
    </script>";
    exit();
}
?>