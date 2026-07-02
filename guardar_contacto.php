<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . "/php/conexion.php");

/* SI ENTRAN SIN ENVIAR FORMULARIO */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /warrior_gym/contacto.html");
    exit();
}

/* RECIBIR DATOS */
$nombre = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$asunto = trim($_POST['asunto'] ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');

/* VALIDAR CAMPOS */
if (
    $nombre === "" ||
    $apellido === "" ||
    $email === "" ||
    $telefono === "" ||
    $asunto === "" ||
    $mensaje === ""
) {
    echo "<script>
        alert('Completá todos los campos.');
        window.history.back();
    </script>";
    exit();
}

/* VALIDAR EMAIL */
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>
        alert('Ingresá un email válido.');
        window.history.back();
    </script>";
    exit();
}

/* ADAPTAR DATOS A TU TABLA */
$nombre_completo = $nombre . " " . $apellido;

$mensaje_final = "Teléfono: " . $telefono . "\n\nMensaje:\n" . $mensaje;

/* INSERTAR EN contacto_web */
$sql = "INSERT INTO contacto_web 
        (nombre, email, asunto, mensaje)
        VALUES (?, ?, ?, ?)";

$stmt = mysqli_prepare($conexion, $sql);

if (!$stmt) {
    die("Error en prepare: " . mysqli_error($conexion));
}

mysqli_stmt_bind_param($stmt, "ssss", $nombre_completo, $email, $asunto, $mensaje_final);

if (mysqli_stmt_execute($stmt)) {
    header("Location: /warrior_gym/contacto.html?enviado=1");
    exit();
} else {
    die("Error al guardar el mensaje: " . mysqli_error($conexion));
}
?>