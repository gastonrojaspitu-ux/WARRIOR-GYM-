<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}

$id = (int) $_GET['id'];

$sql = mysqli_query($conexion, "
    SELECT * FROM solicitudes_rutina 
    WHERE id_solicitud='$id'
    LIMIT 1
");

$data = mysqli_fetch_assoc($sql);

if (!$data) {
    die("Error: solicitud no encontrada.");
}

$id_cliente = (int) $data['id_cliente'];

$insert = mysqli_query($conexion, "
    INSERT INTO rutina_asignada
    (id_cliente, nombre_rutina, descripcion, fecha_asignacion, estado)
    VALUES
    ('$id_cliente', 'Rutina personalizada', 
    '" . mysqli_real_escape_string($conexion, $data['descripcion']) . "', 
    CURDATE(), 'Activa')
");

if (!$insert) {
    die("Error al insertar rutina: " . mysqli_error($conexion));
}

$update = mysqli_query($conexion, "
    UPDATE solicitudes_rutina
    SET estado='Aprobada'
    WHERE id_solicitud='$id'
");

if (!$update) {
    die("Error al actualizar estado: " . mysqli_error($conexion));
}

header("Location: solicitudes_rutina.php");
exit();
?>