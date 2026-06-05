<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// TRAER CLIENTE
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: clientes.php");
    exit();
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM clientes WHERE id_cliente = $id";
$resultado = mysqli_query($conexion, $sql);

if (!$resultado || mysqli_num_rows($resultado) == 0) {
    header("Location: clientes.php");
    exit();
}

$cliente = mysqli_fetch_assoc($resultado);
// GUARDAR CAMBIOS
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $documento = $_POST['documento'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $estado = $_POST['estado'];

    $sql = "UPDATE clientes SET
            nombre = '$nombre',
            apellido = '$apellido',
            numero_documento = '$documento',
            telefono = '$telefono',
            email = '$email',
            estado = '$estado'
            WHERE id_cliente = $id";

    mysqli_query($conexion, $sql);

    header("Location: clientes.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Cliente</title>

<style>
body{
    background:#111;
    color:white;
    font-family:Arial;
}

form{
    width:400px;
    margin:30px auto;
}

input, select{
    width:100%;
    padding:10px;
    margin-bottom:10px;
}

button{
    width:100%;
    padding:10px;
    background:#f0ad4e;
    border:none;
    cursor:pointer;
}
</style>

</head>

<body>

<h1 align="center">Editar Cliente</h1>

<form method="POST">

    <input type="hidden" name="id" value="<?php echo $cliente['id_cliente']; ?>">

    <input type="text" name="nombre" value="<?php echo $cliente['nombre']; ?>" required>

    <input type="text" name="apellido" value="<?php echo $cliente['apellido']; ?>" required>

    <input type="text" name="documento" value="<?php echo $cliente['numero_documento']; ?>" required>

    <input type="text" name="telefono" value="<?php echo $cliente['telefono']; ?>">

    <input type="email" name="email" value="<?php echo $cliente['email']; ?>">

    <select name="estado">
        <option value="Activo" <?php if($cliente['estado']=="Activo") echo "selected"; ?>>Activo</option>
        <option value="Inactivo" <?php if($cliente['estado']=="Inactivo") echo "selected"; ?>>Inactivo</option>
    </select>

    <button type="submit">Guardar Cambios</button>

</form>

</body>
</html>