<?php
session_start();
include(__DIR__ . "/php/conexion.php");

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['id_cliente'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'cliente') {
    header("Location: login.php");
    exit();
}

$id_cliente = intval($_SESSION['id_cliente']);
$id_reserva = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_reserva <= 0) {
    echo "<script>
        alert('Reserva no válida.');
        window.location='mis_reservas.php';
    </script>";
    exit();
}

/* Cancelar solo si la reserva pertenece al cliente logueado */
$sql = "UPDATE reservas 
        SET estado_reserva = 'Cancelada'
        WHERE id_reserva = ?
        AND id_cliente = ?
        AND estado_reserva = 'Activa'";

$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "ii", $id_reserva, $id_cliente);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
    echo "<script>
        alert('Reserva cancelada correctamente.');
        window.location='mis_reservas.php';
    </script>";
    exit();
} else {
    echo "<script>
        alert('No se pudo cancelar la reserva o ya estaba cancelada.');
        window.location='mis_reservas.php';
    </script>";
    exit();
}
?>