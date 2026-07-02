<?php
session_start();

include(__DIR__ . "/../php/conexion.php");

/* PROTEGER ADMIN */
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: solicitudes_membresia.php");
    exit();
}

$id_solicitud = intval($_GET['id']);

if ($id_solicitud <= 0) {
    header("Location: solicitudes_membresia.php");
    exit();
}

$sql = "UPDATE solicitudes_membresia 
        SET estado = 'Aprobada'
        WHERE id_solicitud = $id_solicitud";

if (mysqli_query($conexion, $sql)) {
    echo "<script>
        alert('Solicitud aprobada correctamente.');
        window.location='solicitudes_membresia.php';
    </script>";
    exit();
} else {
    echo "Error SQL: " . mysqli_error($conexion);
}
?>