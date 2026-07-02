<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}

$id = (int) $_GET['id'];

// Obtener id_cliente de la solicitud
$sol = mysqli_fetch_assoc(mysqli_query($conexion, "
    SELECT id_cliente FROM solicitudes_rutina 
    WHERE id_solicitud='$id' LIMIT 1
"));

// Cambiar estado a Rechazada
mysqli_query($conexion, "
    UPDATE solicitudes_rutina
    SET estado='Rechazada'
    WHERE id_solicitud='$id'
");

// ✅ Eliminar rutina asignada si existía
if ($sol) {
    mysqli_query($conexion, "
        DELETE FROM rutina_asignada
        WHERE id_cliente='" . $sol['id_cliente'] . "'
    ");
}

header("Location: solicitudes_rutina.php");
exit();
?>