<?php

include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $plan = $_POST['plan'];

    $sql = "INSERT INTO solicitudes_membresia
    (nombre, apellido, email, telefono, plan_solicitado, fecha_solicitud)
    VALUES
    ('$nombre', '$apellido', '$email', '$telefono', '$plan', CURDATE())";

    if (mysqli_query($conexion, $sql)) {

        echo "<script>
            alert('Solicitud enviada correctamente');
            window.location='../membresias.html';
        </script>";

    } else {

        echo 'Error: ' . mysqli_error($conexion);

    }
}

?>