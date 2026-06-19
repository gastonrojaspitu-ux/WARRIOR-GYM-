<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../php/conexion.php");

if (!isset($_GET['id'])) {
    die("ID no recibido");
}

$id = intval($_GET['id']);

/* 1. Cliente */
$sql = "SELECT * FROM clientes WHERE id_cliente = $id";
$res = mysqli_query($conexion, $sql);

if (!$res || mysqli_num_rows($res) == 0) {
    die("Cliente no encontrado");
}

$cliente = mysqli_fetch_assoc($res);

$nombre = $cliente['nombre'];
$email  = $cliente['email'];

if ($email == '') {
    die("El cliente no tiene email");
}

/* 2. Verificar duplicado */
$check = "SELECT id_usuario FROM usuarios WHERE email = '$email'";
$checkRes = mysqli_query($conexion, $check);

if (mysqli_num_rows($checkRes) > 0) {
    echo "<script>
        alert('⚠️ Este cliente ya tiene acceso al sistema');
        window.location.href='clientes.php';
    </script>";
    exit();
}

/* 3. Insert */
$insert = "INSERT INTO usuarios (nombre, email, password, rol, estado)
           VALUES ('$nombre', '$email', '1234', 'cliente', 'Activo')";

if (mysqli_query($conexion, $insert)) {

    echo "<script>
        alert('✔ Usuario creado correctamente');
        window.location.href='clientes.php';
    </script>";

} else {
    echo "<script>
        alert('❌ Error al crear usuario');
        window.location.href='clientes.php';
    </script>";
}
?>